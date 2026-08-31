<?php
if(!defined('ABSPATH'))exit;
/** TUFF BEATZ V8.5+ — Producer + Client Portal Hubs / Luxury Studio OS */
function tuff_beatz_is_producer_user($uid=0){$u=$uid?get_user_by('id',$uid):wp_get_current_user();return $u&&$u->exists()&&(user_can($u,'edit_posts')||user_can($u,'manage_options'));}
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
}
add_action('wp_enqueue_scripts','tuff_beatz_portal_hub_assets',80);
function tuff_beatz_studio_os_body_class($classes){if(tuff_beatz_is_studio_os_page())$classes[]='tb-studio-os';return $classes;}add_filter('body_class','tuff_beatz_studio_os_body_class');
function tuff_beatz_studio_os_admin_bar($show){if(!is_admin()&&tuff_beatz_is_studio_os_page())return false;return $show;}add_filter('show_admin_bar','tuff_beatz_studio_os_admin_bar',99);
function tuff_beatz_client_projects($uid=0){$uid=$uid?:get_current_user_id();if(!$uid)return array();$q=new WP_Query(array('post_type'=>'tb_request','post_status'=>'publish','posts_per_page'=>100,'orderby'=>'date','order'=>'DESC'));$out=array();foreach($q->posts as $p){if((int)$p->post_author===(int)$uid||(function_exists('tuff_beatz_user_project_permission')&&tuff_beatz_user_project_permission($p->ID,'view',$uid)))$out[]=$p;}return $out;}
function tuff_beatz_project_progress_percent($id){if(function_exists('tuff_beatz_milestones')){$m=tuff_beatz_milestones($id);if($m){$done=0;foreach($m as $x)if(($x['status']??'')==='completed')$done++;return (int)round(($done/count($m))*100);}}$s=get_post_meta($id,'_tb_request_status',true);$map=array('new'=>10,'reviewing'=>20,'approved'=>30,'in-progress'=>50,'revision'=>65,'mastering'=>80,'delivery'=>90,'completed'=>100,'declined'=>0);return $map[$s]??10;}
function tuff_beatz_portal_login_redirect($redirect_to,$requested,$user){if(is_wp_error($user)||!$user)return $redirect_to;if($requested&&strpos($requested,'wp-admin')!==false)return $redirect_to;return tuff_beatz_is_producer_user($user->ID)?home_url('/producer-portal/'):home_url('/client-portal/');}
add_filter('login_redirect','tuff_beatz_portal_login_redirect',20,3);
function tuff_beatz_portal_account_url(){return tuff_beatz_is_producer_user()?home_url('/producer-portal/'):home_url('/client-portal/');}
