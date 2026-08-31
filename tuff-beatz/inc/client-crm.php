<?php
if(!defined('ABSPATH'))exit;
/** TUFF BEATZ V10 — Client CRM & Relationship Directory */
function tuff_beatz_crm_setup(){
 $p=get_page_by_path('producer-crm');
 if(!$p){$id=wp_insert_post(array('post_title'=>'Client CRM','post_name'=>'producer-crm','post_status'=>'publish','post_type'=>'page','post_content'=>''));if(!is_wp_error($id)&&$id)update_post_meta($id,'_wp_page_template','page-producer-crm.php');}
 else update_post_meta($p->ID,'_wp_page_template','page-producer-crm.php');
}
add_action('init','tuff_beatz_crm_setup',35);
function tuff_beatz_crm_assets(){if(is_page('producer-crm')){$p=get_template_directory().'/assets/css/client-crm.css';wp_enqueue_style('tuff-beatz-client-crm',get_template_directory_uri().'/assets/css/client-crm.css',array('tuff-beatz-portal-hubs'),file_exists($p)?filemtime($p):wp_get_theme()->get('Version'));}}
add_action('wp_enqueue_scripts','tuff_beatz_crm_assets',55);
function tuff_beatz_crm_client_projects($uid){$q=new WP_Query(array('post_type'=>'tb_request','post_status'=>'publish','author'=>(int)$uid,'posts_per_page'=>200,'orderby'=>'date','order'=>'DESC'));return $q->posts;}
function tuff_beatz_crm_project_value($id){if(function_exists('tuff_beatz_quote_data')&&function_exists('tuff_beatz_quote_totals')){$q=tuff_beatz_quote_data($id);if($q){$t=tuff_beatz_quote_totals($q);return (float)($t['total']??0);}}return 0;}
function tuff_beatz_crm_project_paid($id){if(function_exists('tuff_beatz_payment_totals')){$t=tuff_beatz_payment_totals($id);return (float)($t['paid']??0);}return 0;}
function tuff_beatz_crm_client_summary($uid){$u=get_userdata($uid);if(!$u)return array();$projects=tuff_beatz_crm_client_projects($uid);$value=0;$paid=0;$active=0;$completed=0;$files=0;$last='';foreach($projects as $p){$id=$p->ID;$value+=tuff_beatz_crm_project_value($id);$paid+=tuff_beatz_crm_project_paid($id);$s=get_post_meta($id,'_tb_request_status',true)?:'new';if($s==='completed')$completed++;elseif($s!=='declined')$active++;if(function_exists('tuff_beatz_vault_files'))$files+=count(tuff_beatz_vault_files($id));if(!$last||strtotime($p->post_date)>strtotime($last))$last=$p->post_date;}return array('user'=>$u,'projects'=>$projects,'project_count'=>count($projects),'active'=>$active,'completed'=>$completed,'value'=>$value,'paid'=>$paid,'balance'=>max(0,$value-$paid),'files'=>$files,'last'=>$last,'industry'=>get_user_meta($uid,'_tb_industry_type',true));}
function tuff_beatz_crm_clients(){
 $users=get_users(array('role__in'=>array('tb_client','tb_artist'),'orderby'=>'display_name','order'=>'ASC'));
 $out=array();foreach($users as $u)$out[] = tuff_beatz_crm_client_summary($u->ID);return $out;
}
