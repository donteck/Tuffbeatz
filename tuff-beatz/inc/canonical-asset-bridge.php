<?php
if(!defined('ABSPATH'))exit;
/** TUFF BEATZ Studio OS V13.7 — canonical asset propagation bridge for legacy upload callers. */
function tuff_beatz_v137_upload_asset_type($category,$version=''){
    $action=sanitize_key($_POST['action']??'');
    if($action==='tb_file_manager_upload'){
        $studio=sanitize_key(wp_unslash($_POST['studio_category']??'source'));
        $map=array('source'=>'source','stems'=>'stem','mixes'=>'mix','masters'=>'master','final'=>'final');
        return $map[$studio]??'source';
    }
    if($action==='tb_producer_console_upload'){
        $type=sanitize_key(wp_unslash($_POST['audio_type']??'mix'));
        return in_array($type,array('source','mix','master','final'),true)?$type:'mix';
    }
    if($action==='tb_admin_upload_delivery_files'){
        $intent=sanitize_key(wp_unslash($_POST['delivery_intent']??'review'));
        return array('review'=>'mix','master'=>'master','final'=>'final')[$intent]??'mix';
    }
    if($action==='tb_upload_project_dashboard_files')return 'source';
    $category=sanitize_key($category);
    if($category==='message')return 'attachment';
    if($category==='source'){
        $label=strtolower((string)$version);
        return preg_match('/\bstems?\b/',$label)?'stem':'source';
    }
    if($category==='delivery'){
        $label=strtolower((string)$version);
        if(preg_match('/\bfinal\b/',$label))return 'final';
        if(preg_match('/\bmaster(ed)?\b/',$label))return 'master';
        return 'mix';
    }
    return '';
}
function tuff_beatz_v137_propagate_asset_identity($request_id,$added,$category,$version,$asset_type=''){
    if(!$added||!function_exists('tuff_beatz_vault_files'))return;
    $canonical=tuff_beatz_v137_upload_asset_type($category,$version);
    if(!$canonical||!in_array($canonical,array('source','stem','mix','master','final','attachment','document'),true))return;
    $ids=array();foreach((array)$added as $f)if(!empty($f['id']))$ids[(string)$f['id']]=1;
    if(!$ids)return;
    $all=tuff_beatz_vault_files($request_id);$changed=false;
    foreach($all as &$f){$fid=(string)($f['id']??'');if(!isset($ids[$fid]))continue;if(($f['asset_type']??'')!==$canonical){$f['asset_type']=$canonical;$changed=true;}}
    unset($f);
    if($changed)update_post_meta((int)$request_id,'_tb_vault_files',$all);
}
add_action('tuff_beatz_vault_files_added','tuff_beatz_v137_propagate_asset_identity',5,5);
