<?php
if (!defined('ABSPATH')) exit;

/** TUFF BEATZ Production Dashboard V2 */
function tuff_beatz_dashboard_page_setup(){
    $page=get_page_by_path('project-dashboard');
    if(!$page){$id=wp_insert_post(array('post_title'=>'Project Dashboard','post_name'=>'project-dashboard','post_status'=>'publish','post_type'=>'page','post_content'=>''));if(!is_wp_error($id)&&$id)update_post_meta($id,'_wp_page_template','page-project-dashboard.php');}
    else update_post_meta($page->ID,'_wp_page_template','page-project-dashboard.php');
}
add_action('init','tuff_beatz_dashboard_page_setup',31);

function tuff_beatz_dashboard_assets(){if(is_page('project-dashboard')){$p=get_template_directory().'/assets/css/project-portal.css';wp_enqueue_style('tuff-beatz-project-portal',get_template_directory_uri().'/assets/css/project-portal.css',array('tuff-beatz-main'),file_exists($p)?filemtime($p):wp_get_theme()->get('Version'));}}
add_action('wp_enqueue_scripts','tuff_beatz_dashboard_assets',41);

function tuff_beatz_user_can_view_request($request_id,$user_id=0){
    $request_id=(int)$request_id;$user_id=$user_id?:get_current_user_id();if(!$request_id||!$user_id)return false;
    if(current_user_can('edit_post',$request_id))return true;$post=get_post($request_id);
    return $post&&$post->post_type==='tb_request'&&(int)$post->post_author===(int)$user_id;
}
function tuff_beatz_project_dashboard_url($request_id){return add_query_arg('project',(int)$request_id,home_url('/project-dashboard/'));}
function tuff_beatz_dashboard_messages($request_id){$m=get_post_meta((int)$request_id,'_tb_project_messages',true);return is_array($m)?$m:array();}

function tuff_beatz_submit_project_message(){
    $request_id=(int)($_POST['request_id']??0);$redirect=tuff_beatz_project_dashboard_url($request_id);
    if(!is_user_logged_in()||!tuff_beatz_user_can_view_request($request_id)){wp_safe_redirect(home_url('/start-a-project/'));exit;}
    if(!isset($_POST['tb_message_nonce'])||!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['tb_message_nonce'])),'tb_project_message_'.$request_id)){wp_safe_redirect(add_query_arg('dash_error','security',$redirect));exit;}
    $body=sanitize_textarea_field(wp_unslash($_POST['message']??''));if(!$body){wp_safe_redirect(add_query_arg('dash_error','message',$redirect));exit;}
    $u=wp_get_current_user();$messages=tuff_beatz_dashboard_messages($request_id);$messages[]=array('user_id'=>$u->ID,'name'=>$u->display_name,'role'=>current_user_can('edit_post',$request_id)?'TUFF BEATZ':'Client','body'=>$body,'time'=>current_time('mysql'));update_post_meta($request_id,'_tb_project_messages',$messages);
    wp_safe_redirect(add_query_arg('message_sent','1',$redirect).'#messages');exit;
}
add_action('admin_post_tb_submit_project_message','tuff_beatz_submit_project_message');

function tuff_beatz_upload_project_dashboard_files(){
    $request_id=(int)($_POST['request_id']??0);$redirect=tuff_beatz_project_dashboard_url($request_id);
    if(!is_user_logged_in()||!tuff_beatz_user_can_view_request($request_id)){wp_safe_redirect(home_url('/start-a-project/'));exit;}
    if(!isset($_POST['tb_files_nonce'])||!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['tb_files_nonce'])),'tb_project_files_'.$request_id)){wp_safe_redirect(add_query_arg('dash_error','security',$redirect));exit;}
    $version=sanitize_text_field(wp_unslash($_POST['upload_version']??'Client Upload'));
    $new=tuff_beatz_vault_save_files($request_id,'project_files','source',$version?:'Client Upload');
    wp_safe_redirect(add_query_arg('files_added',(string)count($new),$redirect).'#files');exit;
}
add_action('admin_post_tb_upload_project_dashboard_files','tuff_beatz_upload_project_dashboard_files');

function tuff_beatz_admin_upload_delivery_files(){
    $request_id=(int)($_POST['request_id']??0);$redirect=admin_url('post.php?post='.$request_id.'&action=edit');
    if(!$request_id||!current_user_can('edit_post',$request_id)){wp_safe_redirect(admin_url());exit;}
    if(!isset($_POST['tb_delivery_upload_nonce'])||!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['tb_delivery_upload_nonce'])),'tb_delivery_upload_'.$request_id)){wp_safe_redirect($redirect);exit;}
    $version=sanitize_text_field(wp_unslash($_POST['delivery_version']??'Final Delivery'));
    $new=tuff_beatz_vault_save_files($request_id,'delivery_files','delivery',$version?:'Final Delivery');
    if($new)update_post_meta($request_id,'_tb_request_status','delivery');
    wp_safe_redirect(add_query_arg('vault_delivery_added',(string)count($new),$redirect));exit;
}
add_action('admin_post_tb_admin_upload_delivery_files','tuff_beatz_admin_upload_delivery_files');

function tuff_beatz_dashboard_admin_box($post){
    wp_nonce_field('tb_dashboard_admin_save','tb_dashboard_admin_nonce');
    $credits=get_post_meta($post->ID,'_tb_request_credits',true);$payment=get_post_meta($post->ID,'_tb_request_payment_status',true)?:'Not invoiced';$delivery=get_post_meta($post->ID,'_tb_request_delivery_notes',true);$final=get_post_meta($post->ID,'_tb_request_final_links',true);$messages=tuff_beatz_dashboard_messages($post->ID);
    echo '<p><strong>Client dashboard:</strong> <a target="_blank" rel="noopener" href="'.esc_url(tuff_beatz_project_dashboard_url($post->ID)).'">Open Project Dashboard</a></p>';
    echo '<p><label><strong>Credits / Personnel</strong></label><br><textarea style="width:100%;min-height:90px" name="tb_request_credits">'.esc_textarea($credits).'</textarea></p>';
    echo '<p><label><strong>Payment Status</strong></label><br><select name="tb_request_payment_status">';foreach(array('Not invoiced','Quote sent','Deposit due','Deposit paid','Balance due','Paid in full') as $v)echo '<option '.selected($payment,$v,false).'>'.esc_html($v).'</option>';echo '</select></p>';
    echo '<p><label><strong>Delivery Notes</strong></label><br><textarea style="width:100%;min-height:90px" name="tb_request_delivery_notes">'.esc_textarea($delivery).'</textarea></p>';
    echo '<p><label><strong>External Final Delivery Links</strong></label><br><textarea style="width:100%;min-height:80px" name="tb_request_final_links" placeholder="Optional secure external link per line">'.esc_textarea($final).'</textarea></p>';
    echo '<hr><p><strong>Private Vault:</strong> '.count(tuff_beatz_vault_files($post->ID)).' file(s)</p><p><strong>Project Messages:</strong> '.count($messages).'</p>';
}
function tuff_beatz_dashboard_admin_metabox(){add_meta_box('tb_dashboard_details',__('Client Dashboard & Delivery','tuff-beatz'),'tuff_beatz_dashboard_admin_box','tb_request','side','default');}
add_action('add_meta_boxes_tb_request','tuff_beatz_dashboard_admin_metabox');

function tuff_beatz_delivery_upload_box($post){
    $files=array_values(array_filter(tuff_beatz_vault_files($post->ID),function($f){return ($f['category']??'')==='delivery';}));
    echo '<p>Upload Mix V1, Mix V2, Master V1, Instrumental, Acapella, TV Mix or final release files. These files stay outside public_html.</p>';
    if($files){echo '<ul>';foreach($files as $f){echo '<li><strong>'.esc_html($f['version']?:'Delivery').'</strong> — <a href="'.esc_url(tuff_beatz_vault_download_url($post->ID,$f['id'])).'">'.esc_html($f['name']).'</a></li>';}echo '</ul>';}
    echo '<form method="post" enctype="multipart/form-data" action="'.esc_url(admin_url('admin-post.php')).'">';
    echo '<input type="hidden" name="action" value="tb_admin_upload_delivery_files"><input type="hidden" name="request_id" value="'.esc_attr($post->ID).'">';wp_nonce_field('tb_delivery_upload_'.$post->ID,'tb_delivery_upload_nonce');
    echo '<p><label><strong>Version / Delivery Label</strong></label><br><input style="width:100%" name="delivery_version" value="Mix V1" placeholder="Mix V1, Master V1, Final Master..."></p>';
    echo '<p><input type="file" name="delivery_files[]" multiple accept=".wav,.aiff,.aif,.mp3,.m4a,.zip,.pdf,.txt,.mid,.midi"></p><p><button class="button button-primary" type="submit">Upload Private Delivery Files</button></p></form>';
}
function tuff_beatz_delivery_upload_metabox(){add_meta_box('tb_private_delivery',__('Private File Vault — Versions & Delivery','tuff-beatz'),'tuff_beatz_delivery_upload_box','tb_request','normal','default');}
add_action('add_meta_boxes_tb_request','tuff_beatz_delivery_upload_metabox');

function tuff_beatz_save_dashboard_admin($id){
    if(!isset($_POST['tb_dashboard_admin_nonce'])||!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['tb_dashboard_admin_nonce'])),'tb_dashboard_admin_save')||!current_user_can('edit_post',$id))return;
    if(isset($_POST['tb_request_credits']))update_post_meta($id,'_tb_request_credits',sanitize_textarea_field(wp_unslash($_POST['tb_request_credits'])));
    if(isset($_POST['tb_request_payment_status']))update_post_meta($id,'_tb_request_payment_status',sanitize_text_field(wp_unslash($_POST['tb_request_payment_status'])));
    if(isset($_POST['tb_request_delivery_notes']))update_post_meta($id,'_tb_request_delivery_notes',sanitize_textarea_field(wp_unslash($_POST['tb_request_delivery_notes'])));
    if(isset($_POST['tb_request_final_links']))update_post_meta($id,'_tb_request_final_links',sanitize_textarea_field(wp_unslash($_POST['tb_request_final_links'])));
}
add_action('save_post_tb_request','tuff_beatz_save_dashboard_admin',20);
