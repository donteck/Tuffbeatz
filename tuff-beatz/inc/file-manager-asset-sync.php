<?php
if(!defined('ABSPATH'))exit;
/** TUFF BEATZ Studio OS V13.7.2 — safe File Manager canonical asset synchronization */
function tuff_beatz_v1372_file_manager_asset_type($studio){
    $map=array('source'=>'source','stems'=>'stem','mixes'=>'mix','masters'=>'master','final'=>'final');
    return $map[sanitize_key($studio)]??'source';
}
function tuff_beatz_v1372_sync_file_manager_asset_type(){
    if(($_SERVER['REQUEST_METHOD']??'')!=='POST')return;
    if(sanitize_key($_POST['action']??'')!=='tb_file_manager_update')return;
    $id=(int)($_POST['request_id']??0);
    $fid=sanitize_text_field(wp_unslash($_POST['file_id']??''));
    $studio=sanitize_key(wp_unslash($_POST['studio_category']??'source'));
    if(!$id||!$fid||!is_user_logged_in()||!current_user_can('edit_post',$id))return;
    if(!isset($_POST['tb_fm_manage_nonce'])||!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['tb_fm_manage_nonce'])),'tb_fm_manage_'.$id.'_'.$fid))return;
    $allowed=array('source','stems','mixes','masters','final');
    if(!in_array($studio,$allowed,true))$studio='source';
    if(!function_exists('tuff_beatz_vault_files'))return;
    $all=tuff_beatz_vault_files($id);$changed=false;
    foreach($all as &$f){
        if(($f['id']??'')!==$fid)continue;
        $canonical=tuff_beatz_v1372_file_manager_asset_type($studio);
        if(($f['asset_type']??'')!==$canonical){$f['asset_type']=$canonical;$changed=true;}
        break;
    }
    unset($f);
    if($changed)update_post_meta($id,'_tb_vault_files',$all);
}
add_action('admin_init','tuff_beatz_v1372_sync_file_manager_asset_type',2);
