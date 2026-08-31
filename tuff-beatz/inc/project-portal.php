<?php
if (!defined('ABSPATH')) exit;

/** TUFF BEATZ Production Portal V3 */
function tuff_beatz_portal_roles(){
    if(!get_role('tb_client')) add_role('tb_client',__('TUFF BEATZ Client','tuff-beatz'),array('read'=>true));
    if(!get_role('tb_artist')) add_role('tb_artist',__('TUFF BEATZ Artist','tuff-beatz'),array('read'=>true));
}
add_action('init','tuff_beatz_portal_roles',5);

function tuff_beatz_request_cpt(){
    register_post_type('tb_request',array(
        'labels'=>array('name'=>__('Project Requests','tuff-beatz'),'singular_name'=>__('Project Request','tuff-beatz'),'menu_name'=>__('Project Requests','tuff-beatz'),'edit_item'=>__('Review Project Request','tuff-beatz')),
        'public'=>false,'show_ui'=>true,'show_in_menu'=>true,'show_in_rest'=>false,'menu_icon'=>'dashicons-clipboard','supports'=>array('title','author'),'capability_type'=>'post','map_meta_cap'=>true
    ));
}
add_action('init','tuff_beatz_request_cpt',8);

function tuff_beatz_ensure_portal_page(){
    if(get_option('tuff_beatz_portal_page_ready')) return;
    $page=get_page_by_path('start-a-project');
    if(!$page){
        $id=wp_insert_post(array('post_title'=>'Start a Project','post_name'=>'start-a-project','post_status'=>'publish','post_type'=>'page'));
        if(!is_wp_error($id)&&$id) update_post_meta($id,'_wp_page_template','page-start-project.php');
    }else update_post_meta($page->ID,'_wp_page_template','page-start-project.php');
    update_option('tuff_beatz_portal_page_ready',1,false);
}
add_action('init','tuff_beatz_ensure_portal_page',30);

function tuff_beatz_portal_assets(){
    if(is_page(array('start-a-project','project-dashboard'))){
        $p=get_template_directory().'/assets/css/project-portal.css';
        wp_enqueue_style('tuff-beatz-project-portal',get_template_directory_uri().'/assets/css/project-portal.css',array('tuff-beatz-main'),file_exists($p)?filemtime($p):wp_get_theme()->get('Version'));
    }
}
add_action('wp_enqueue_scripts','tuff_beatz_portal_assets',40);

function tuff_beatz_industry_types(){return array(
    'artist'=>'Artist / Recording Artist','group'=>'Group / Duo','band'=>'Band','singer'=>'Singer / Vocalist','songwriter'=>'Songwriter','producer'=>'Producer','composer'=>'Composer','musician'=>'Musician','label'=>'Record Label / A&R','manager'=>'Artist Manager / Representative','publisher'=>'Publisher','media'=>'Film / TV / Media Company','choir'=>'Church / Choir','creator'=>'Content Creator','other'=>'Other Industry Professional'
);}
function tuff_beatz_services(){return array(
    'full-production'=>'Full Song Production','beat-production'=>'Beat / Instrumental Production','mixing'=>'Mixing','mastering'=>'Mastering','mix-master'=>'Mixing + Mastering','vocal-production'=>'Vocal Production','songwriting'=>'Songwriting','arrangement'=>'Arrangement','musician-recording'=>'Musician / Instrument Recording','band-production'=>'Band Production','artist-development'=>'Artist Development','producer-collab'=>'Producer Collaboration','media-music'=>'Film / TV / Media Music','consultation'=>'Production Consultation','custom'=>'Other / Custom Project'
);}

function tuff_beatz_register_portal_account(){
    $redirect=home_url('/start-a-project/');
    if(!isset($_POST['tb_register_nonce'])||!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['tb_register_nonce'])),'tb_register_account')){wp_safe_redirect(add_query_arg('portal_error','security',$redirect));exit;}
    if(!empty($_POST['company_website'])){wp_safe_redirect($redirect);exit;}
    $name=sanitize_text_field(wp_unslash($_POST['display_name']??''));
    $email=sanitize_email(wp_unslash($_POST['email']??''));
    $password=(string)wp_unslash($_POST['password']??'');
    $industry=sanitize_key(wp_unslash($_POST['industry_type']??'artist'));
    if(!$name||!is_email($email)||strlen($password)<10){wp_safe_redirect(add_query_arg('portal_error','registration_fields',$redirect));exit;}
    if(email_exists($email)){wp_safe_redirect(add_query_arg('portal_error','email_exists',$redirect));exit;}
    if(!array_key_exists($industry,tuff_beatz_industry_types()))$industry='other';
    $base=sanitize_user(strtolower(strtok($email,'@')),true)?:'client';$username=$base;$i=1;while(username_exists($username))$username=$base.$i++;
    $uid=wp_insert_user(array('user_login'=>$username,'user_email'=>$email,'user_pass'=>$password,'display_name'=>$name,'role'=>'tb_client'));
    if(is_wp_error($uid)){wp_safe_redirect(add_query_arg('portal_error','registration_failed',$redirect));exit;}
    update_user_meta($uid,'_tb_industry_type',$industry);wp_set_current_user($uid);wp_set_auth_cookie($uid,true);wp_safe_redirect(add_query_arg('welcome','1',$redirect));exit;
}
add_action('admin_post_nopriv_tb_register_portal_account','tuff_beatz_register_portal_account');

function tuff_beatz_submit_project_request(){
    $redirect=home_url('/start-a-project/');
    if(!is_user_logged_in()){wp_safe_redirect(wp_login_url($redirect));exit;}
    if(!isset($_POST['tb_project_nonce'])||!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['tb_project_nonce'])),'tb_submit_project')){wp_safe_redirect(add_query_arg('portal_error','security',$redirect));exit;}
    if(empty($_POST['rights_confirm'])){wp_safe_redirect(add_query_arg('portal_error','consent',$redirect));exit;}

    $u=wp_get_current_user();
    $client=sanitize_text_field(wp_unslash($_POST['artist_name']??''));
    $title=sanitize_text_field(wp_unslash($_POST['project_title']??''));
    $service=sanitize_key(wp_unslash($_POST['service']??''));
    $industry=sanitize_key(wp_unslash($_POST['industry_type']??''));
    $details=sanitize_textarea_field(wp_unslash($_POST['details']??''));
    if(!$client||!$title||!array_key_exists($service,tuff_beatz_services())||!$details){wp_safe_redirect(add_query_arg('portal_error','required_fields',$redirect));exit;}
    if(!array_key_exists($industry,tuff_beatz_industry_types()))$industry='other';

    $id=wp_insert_post(array('post_type'=>'tb_request','post_status'=>'publish','post_title'=>sprintf('%s — %s',$client,$title),'post_author'=>$u->ID));
    if(is_wp_error($id)||!$id){wp_safe_redirect(add_query_arg('portal_error','submit_failed',$redirect));exit;}

    $fields=array(
        '_tb_request_artist'=>$client,'_tb_request_industry_type'=>$industry,'_tb_request_service'=>$service,
        '_tb_request_project_type'=>sanitize_text_field(wp_unslash($_POST['project_type']??'')),
        '_tb_request_genre'=>sanitize_text_field(wp_unslash($_POST['genre']??'')),
        '_tb_request_bpm'=>sanitize_text_field(wp_unslash($_POST['bpm']??'')),
        '_tb_request_key'=>sanitize_text_field(wp_unslash($_POST['song_key']??'')),
        '_tb_request_track_count'=>sanitize_text_field(wp_unslash($_POST['track_count']??'')),
        '_tb_request_budget'=>sanitize_text_field(wp_unslash($_POST['budget']??'')),
        '_tb_request_target_date'=>sanitize_text_field(wp_unslash($_POST['target_date']??'')),
        '_tb_request_reference_url'=>esc_url_raw(wp_unslash($_POST['reference_url']??'')),
        '_tb_request_transfer_url'=>esc_url_raw(wp_unslash($_POST['transfer_url']??'')),
        '_tb_request_mix_notes'=>sanitize_textarea_field(wp_unslash($_POST['mix_notes']??'')),
        '_tb_request_details'=>$details,'_tb_request_status'=>'new','_tb_request_email'=>$u->user_email,
        '_tb_request_rights_confirmed'=>'yes','_tb_request_submitted_at'=>current_time('mysql')
    );
    foreach($fields as $k=>$v) update_post_meta($id,$k,$v);

    $uploads=function_exists('tuff_beatz_vault_save_files')?tuff_beatz_vault_save_files($id,'project_files','source','Initial Upload'):array();
    $admin=tuff_beatz_get('contact_email',get_option('admin_email'));$label=tuff_beatz_services()[$service];
    wp_mail($admin,sprintf('[TUFF BEATZ] New %s Request — %s',$label,$title),"New TUFF BEATZ project request.\n\nClient: {$client}\nEmail: {$u->user_email}\nService: {$label}\nProject: {$title}\nPrivate vault files: ".count($uploads)."\n\nReview in WordPress → Project Requests.");
    wp_safe_redirect(add_query_arg(array('submitted'=>'1','request'=>$id),$redirect));exit;
}
add_action('admin_post_tb_submit_project_request','tuff_beatz_submit_project_request');

function tuff_beatz_request_admin_box($post){
    wp_nonce_field('tb_request_admin_save','tb_request_admin_nonce');
    $services=tuff_beatz_services();$industries=tuff_beatz_industry_types();$service=get_post_meta($post->ID,'_tb_request_service',true);$industry=get_post_meta($post->ID,'_tb_request_industry_type',true);
    $fields=array('Client / Artist'=>get_post_meta($post->ID,'_tb_request_artist',true),'Industry Role'=>$industries[$industry]??$industry,'Email'=>get_post_meta($post->ID,'_tb_request_email',true),'Service'=>$services[$service]??$service,'Project Type'=>get_post_meta($post->ID,'_tb_request_project_type',true),'Genre'=>get_post_meta($post->ID,'_tb_request_genre',true),'BPM'=>get_post_meta($post->ID,'_tb_request_bpm',true),'Key'=>get_post_meta($post->ID,'_tb_request_key',true),'Track Count'=>get_post_meta($post->ID,'_tb_request_track_count',true),'Budget'=>get_post_meta($post->ID,'_tb_request_budget',true),'Target Date'=>get_post_meta($post->ID,'_tb_request_target_date',true));
    echo '<div style="display:grid;gap:10px">';foreach($fields as $l=>$v)echo '<div><strong>'.esc_html($l).':</strong> '.esc_html($v?:'—').'</div>';
    foreach(array('Reference'=>'_tb_request_reference_url','Large-file transfer'=>'_tb_request_transfer_url') as $l=>$k){$url=get_post_meta($post->ID,$k,true);if($url)echo '<div><strong>'.esc_html($l).':</strong> <a href="'.esc_url($url).'" target="_blank" rel="noopener">Open link</a></div>';}

    $vault=function_exists('tuff_beatz_vault_files')?tuff_beatz_vault_files($post->ID):array();
    if($vault){echo '<div><strong>Private Vault:</strong><ul>';foreach($vault as $file){$url=tuff_beatz_vault_download_url($post->ID,$file['id']);echo '<li><a href="'.esc_url($url).'">'.esc_html($file['name']).'</a> <small>('.esc_html($file['category']??'file').')</small></li>';}echo '</ul></div>';}
    $legacy=get_post_meta($post->ID,'_tb_request_attachments',true);if(is_array($legacy)&&$legacy){echo '<div><strong>Legacy public uploads:</strong> '.count($legacy).'</div>';}

    echo '<div><strong>Mix / Master notes:</strong><p style="white-space:pre-wrap">'.esc_html(get_post_meta($post->ID,'_tb_request_mix_notes',true)).'</p></div><div><strong>Project details:</strong><p style="white-space:pre-wrap">'.esc_html(get_post_meta($post->ID,'_tb_request_details',true)).'</p></div>';
    $current=get_post_meta($post->ID,'_tb_request_status',true)?:'new';echo '<label><strong>Status</strong><br><select name="tb_request_status">';foreach(array('new'=>'New','reviewing'=>'Reviewing','approved'=>'Approved','in-progress'=>'In Production','revision'=>'Revision','mastering'=>'Mastering','delivery'=>'Final Delivery','completed'=>'Completed','declined'=>'Declined') as $v=>$l)echo '<option value="'.esc_attr($v).'" '.selected($current,$v,false).'>'.esc_html($l).'</option>';echo '</select></label></div>';
}
function tuff_beatz_request_admin_metaboxes(){add_meta_box('tb_request_details',__('Project Request Details','tuff-beatz'),'tuff_beatz_request_admin_box','tb_request','normal','high');}
add_action('add_meta_boxes_tb_request','tuff_beatz_request_admin_metaboxes');

function tuff_beatz_save_request_admin($id){
    if(!isset($_POST['tb_request_admin_nonce'])||!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['tb_request_admin_nonce'])),'tb_request_admin_save')||!current_user_can('edit_post',$id))return;
    $s=sanitize_key(wp_unslash($_POST['tb_request_status']??''));$allowed=array('new','reviewing','approved','in-progress','revision','mastering','delivery','completed','declined');if(in_array($s,$allowed,true))update_post_meta($id,'_tb_request_status',$s);
}
add_action('save_post_tb_request','tuff_beatz_save_request_admin');

function tuff_beatz_request_status_label($s){$l=array('new'=>'New','reviewing'=>'Reviewing','approved'=>'Approved','in-progress'=>'In Production','revision'=>'Revision','mastering'=>'Mastering','delivery'=>'Final Delivery','completed'=>'Completed','declined'=>'Declined');return $l[$s]??'New';}
