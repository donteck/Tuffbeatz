<?php
if(!defined('ABSPATH'))exit;
/** TUFF BEATZ V8.6.1 — Producer + Client Portal Hubs / Luxury Studio OS */
function tuff_beatz_producer_session_action($uid){return 'tb_producer_session_'.(int)$uid;}
function tuff_beatz_producer_session_token($uid=0){$uid=$uid?:get_current_user_id();return $uid?wp_create_nonce(tuff_beatz_producer_session_action($uid)):'';}
function tuff_beatz_has_producer_session_token($uid=0){$uid=$uid?:get_current_user_id();if(!$uid||$uid!==get_current_user_id())return false;$token=sanitize_text_field(wp_unslash($_GET['tb_producer_session']??''));return $token&&wp_verify_nonce($token,tuff_beatz_producer_session_action($uid));}
function tuff_beatz_is_producer_user($uid=0){$u=$uid?get_user_by('id',$uid):wp_get_current_user();if(!$u||!$u->exists())return false;$legacy=user_can($u,'edit_posts')||user_can($u,'manage_options');if($legacy)return true;return (int)$u->ID===get_current_user_id()&&tuff_beatz_has_producer_session_token((int)$u->ID);}
function tuff_beatz_producer_workspace_url($id,$anchor=''){$url=add_query_arg(array('project'=>(int)$id,'tb_producer_session'=>tuff_beatz_producer_session_token()),home_url('/project-dashboard/'));if($anchor)$url.='#'.ltrim($anchor,'#');return $url;}
function tuff_beatz_project_dashboard_access_gate(){
 if(!is_page('project-dashboard'))return;
 $id=(int)($_GET['project']??0);
 if(!is_user_logged_in()){
  $target=$id?add_query_arg('project',$id,home_url('/project-dashboard/')):home_url('/project-dashboard/');
  wp_safe_redirect(wp_login_url($target));exit;
 }
 if(!$id){wp_safe_redirect(home_url('/start-a-project/'));exit;}
 $allowed=function_exists('tuff_beatz_auth_can')?tuff_beatz_auth_can($id,'view'): (function_exists('tuff_beatz_user_can_view_request')&&tuff_beatz_user_can_view_request($id));
 if(!$allowed){wp_safe_redirect(home_url('/start-a-project/'));exit;}
 if(isset($_GET['tb_producer_session'])&&!tuff_beatz_has_producer_session_token(get_current_user_id())){
  $clean=remove_query_arg('tb_producer_session');
  wp_safe_redirect($clean);exit;
 }
}
add_action('template_redirect','tuff_beatz_project_dashboard_access_gate',0);
function tuff_beatz_portal_hub_setup(){
 $pages=array('producer-portal'=>array('Producer Portal','page-producer-portal.php'),'client-portal'=>array('Client Portal','page-client-portal.php'));
 foreach($pages as $slug=>$data){$p=get_page_by_path($slug);if(!$p){$id=wp_insert_post(array('post_title'=>$data[0],'post_name'=>$slug,'post_status'=>'publish','post_type'=>'page','post_content'=>''));if(!is_wp_error($id)&&$id)update_post_meta($id,'_wp_page_template',$data[1]);}else update_post_meta($p->ID,'_wp_page_template',$data[1]);}
}
add_action('init','tuff_beatz_portal_hub_setup',34);
function tuff_beatz_is_studio_os_page(){return is_page(array('producer-portal','client-portal','producer-crm','project-dashboard','start-a-project'));}
function tuff_beatz_portal_hub_assets(){
 if(is_page(array('producer-portal','client-portal','producer-crm'))){$p=get_template_directory().'/assets/css/portal-hubs.css';wp_enqueue_style('tuff-beatz-portal-hubs',get_template_directory_uri().'/assets/css/portal-hubs.css',array('tuff-beatz-main'),file_exists($p)?filemtime($p):wp_get_theme()->get('Version'));}
 if(tuff_beatz_is_studio_os_page()){$p=get_template_directory().'/assets/css/luxury-studio-os.css';$deps=is_page(array('producer-portal','client-portal','producer-crm'))?array('tuff-beatz-portal-hubs'):array('tuff-beatz-main');wp_enqueue_style('tuff-beatz-luxury-studio-os',get_template_directory_uri().'/assets/css/luxury-studio-os.css',$deps,file_exists($p)?filemtime($p):wp_get_theme()->get('Version'));}
 if(is_page('producer-portal')){$p=get_template_directory().'/assets/css/producer-command-v2.css';wp_enqueue_style('tuff-beatz-producer-command-v2',get_template_directory_uri().'/assets/css/producer-command-v2.css',array('tuff-beatz-luxury-studio-os'),file_exists($p)?filemtime($p):wp_get_theme()->get('Version'));}
 if(is_page('client-portal')){$p=get_template_directory().'/assets/css/client-portal-v2.css';wp_enqueue_style('tuff-beatz-client-portal-v2',get_template_directory_uri().'/assets/css/client-portal-v2.css',array('tuff-beatz-luxury-studio-os'),file_exists($p)?filemtime($p):wp_get_theme()->get('Version'));}
 if(is_page('project-dashboard')&&is_user_logged_in()&&!tuff_beatz_is_producer_user()){
   $css=get_template_directory().'/assets/css/client-workspace-v2.css';
   $js=get_template_directory().'/assets/js/client-workspace-v2.js';
   $fm=get_template_directory().'/assets/css/studio-file-manager.css';
   $fmjs=get_template_directory().'/assets/js/studio-file-manager.js';
   $fd=get_template_directory().'/assets/css/final-delivery.css';
   wp_enqueue_style('tuff-beatz-client-workspace-v2',get_template_directory_uri().'/assets/css/client-workspace-v2.css',array('tuff-beatz-luxury-studio-os'),file_exists($css)?filemtime($css):wp_get_theme()->get('Version'));
   if(file_exists($fm))wp_enqueue_style('tuff-beatz-studio-file-manager',get_template_directory_uri().'/assets/css/studio-file-manager.css',array('tuff-beatz-client-workspace-v2'),filemtime($fm));
   if(file_exists($fd))wp_enqueue_style('tuff-beatz-final-delivery',get_template_directory_uri().'/assets/css/final-delivery.css',array('tuff-beatz-client-workspace-v2'),filemtime($fd));
   wp_enqueue_script('tuff-beatz-client-workspace-v2',get_template_directory_uri().'/assets/js/client-workspace-v2.js',array(),file_exists($js)?filemtime($js):wp_get_theme()->get('Version'),true);
   if(file_exists($fmjs))wp_enqueue_script('tuff-beatz-studio-file-manager',get_template_directory_uri().'/assets/js/studio-file-manager.js',array('tuff-beatz-client-workspace-v2'),filemtime($fmjs),true);
 }
}
add_action('wp_enqueue_scripts','tuff_beatz_portal_hub_assets',80);
function tuff_beatz_studio_os_body_class($classes){if(tuff_beatz_is_studio_os_page())$classes[]='tb-studio-os';if(is_page('project-dashboard')&&is_user_logged_in()&&!tuff_beatz_is_producer_user())$classes[]='tb-client-workspace';return $classes;}add_filter('body_class','tuff_beatz_studio_os_body_class');
function tuff_beatz_studio_os_admin_bar($show){if(!is_admin()&&tuff_beatz_is_studio_os_page())return false;return $show;}add_filter('show_admin_bar','tuff_beatz_studio_os_admin_bar',99);
function tuff_beatz_client_projects($uid=0){$uid=$uid?:get_current_user_id();if(!$uid)return array();$q=new WP_Query(array('post_type'=>'tb_request','post_status'=>'publish','posts_per_page'=>100,'orderby'=>'date','order'=>'DESC'));$out=array();foreach($q->posts as $p){if((int)$p->post_author===(int)$uid||(function_exists('tuff_beatz_user_project_permission')&&tuff_beatz_user_project_permission($p->ID,'view',$uid)))$out[]=$p;}return $out;}
function tuff_beatz_project_progress_percent($id){if(function_exists('tuff_beatz_milestones')){$m=tuff_beatz_milestones($id);if($m){$done=0;foreach($m as $x)if(($x['status']??'')==='completed')$done++;return (int)round(($done/count($m))*100);}}$s=get_post_meta($id,'_tb_request_status',true);$map=array('new'=>10,'reviewing'=>20,'approved'=>30,'in-progress'=>50,'revision'=>65,'mastering'=>80,'delivery'=>90,'completed'=>100,'declined'=>0);return $map[$s]??10;}
function tuff_beatz_portal_login_redirect($redirect_to,$requested,$user){if(is_wp_error($user)||!$user)return $redirect_to;if($requested&&strpos($requested,'wp-admin')!==false)return $redirect_to;return tuff_beatz_is_producer_user($user->ID)?home_url('/producer-portal/'):home_url('/client-portal/');}
add_filter('login_redirect','tuff_beatz_portal_login_redirect',20,3);
function tuff_beatz_portal_account_url(){return tuff_beatz_is_producer_user()?home_url('/producer-portal/'):home_url('/client-portal/');}
