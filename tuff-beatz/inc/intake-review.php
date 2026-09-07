<?php
if(!defined('ABSPATH'))exit;
/** TUFF BEATZ — Intake Review V1.1
 * Producer-only qualification bridge: canonical tb_request -> CRM opportunity -> proposal workflow.
 * Client submission itself never auto-qualifies, sends a proposal, creates a contract, invoice, charge, or active project.
 */
function tuff_beatz_intake_review_is_producer(){return is_user_logged_in()&&function_exists('tuff_beatz_is_producer_user')&&tuff_beatz_is_producer_user();}
function tuff_beatz_intake_opportunity_id($request_id){return (int)get_post_meta((int)$request_id,'_tb_intake_opportunity_id',true);}
function tuff_beatz_intake_budget_value($raw){
    $raw=(string)$raw;
    if(preg_match('/\$?([0-9,]+)\s*[–-]\s*\$?([0-9,]+)/u',$raw,$m))return ((float)str_replace(',','',$m[1])+(float)str_replace(',','',$m[2]))/2;
    if(preg_match('/\$?([0-9,]+)\+/', $raw,$m))return (float)str_replace(',','',$m[1]);
    if(preg_match('/\$?([0-9,]+)/',$raw,$m))return (float)str_replace(',','',$m[1]);
    return 0.0;
}
function tuff_beatz_intake_create_opportunity($request_id){
    $request_id=(int)$request_id;if(!$request_id||get_post_type($request_id)!=='tb_request')return new WP_Error('invalid_request','Invalid intake request.');
    $existing=tuff_beatz_intake_opportunity_id($request_id);if($existing&&get_post_type($existing)==='tb_opportunity')return $existing;
    $artist=(string)get_post_meta($request_id,'_tb_request_artist',true);$email=(string)get_post_meta($request_id,'_tb_request_email',true);$service=(string)get_post_meta($request_id,'_tb_request_service',true);$budget=(string)get_post_meta($request_id,'_tb_request_budget',true);
    $opp_id=wp_insert_post(array('post_type'=>'tb_opportunity','post_status'=>'publish','post_title'=>'Opportunity — '.($artist?:get_the_title($request_id))));
    if(is_wp_error($opp_id)||!$opp_id)return is_wp_error($opp_id)?$opp_id:new WP_Error('create_failed','Opportunity could not be created.');
    update_post_meta($opp_id,'_tb_v14_contact_name',$artist);
    update_post_meta($opp_id,'_tb_v14_contact_email',$email);
    update_post_meta($opp_id,'_tb_v14_service',$service);
    update_post_meta($opp_id,'_tb_v14_source','TUFF BEATZ Start a Project');
    update_post_meta($opp_id,'_tb_v14_stage','qualified');
    update_post_meta($opp_id,'_tb_v14_probability',25);
    update_post_meta($opp_id,'_tb_v14_estimated_value',tuff_beatz_intake_budget_value($budget));
    update_post_meta($opp_id,'_tb_v14_next_action','Prepare discovery / proposal for qualified intake.');
    update_post_meta($opp_id,'_tb_v14_linked_request_id',$request_id);
    update_post_meta($request_id,'_tb_intake_opportunity_id',$opp_id);
    update_post_meta($request_id,'_tb_request_status','reviewing');
    update_post_meta($request_id,'_tb_intake_qualified_at',current_time('mysql'));
    update_post_meta($request_id,'_tb_intake_qualified_by',get_current_user_id());
    if(function_exists('tuff_beatz_v14_add_opportunity_activity'))tuff_beatz_v14_add_opportunity_activity($opp_id,'created','Qualified from Start a Project intake #'.$request_id);
    return $opp_id;
}
function tuff_beatz_handle_qualify_intake(){
    if(!tuff_beatz_intake_review_is_producer())wp_die('Unauthorized.',403);
    $request_id=absint($_POST['request_id']??0);
    if(!$request_id||!isset($_POST['tb_intake_nonce'])||!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['tb_intake_nonce'])),'tb_qualify_intake_'.$request_id))wp_die('Invalid request.',400);
    $opp=tuff_beatz_intake_create_opportunity($request_id);
    if(is_wp_error($opp)){wp_safe_redirect(add_query_arg('intake_error',rawurlencode($opp->get_error_message()),home_url('/producer-crm/')));exit;}
    wp_safe_redirect(add_query_arg(array('intake_qualified'=>1,'request'=>$request_id),get_edit_post_link((int)$opp,'url')));exit;
}
add_action('admin_post_tb_qualify_intake','tuff_beatz_handle_qualify_intake');

function tuff_beatz_handle_qualify_prepare_proposal(){
    if(!tuff_beatz_intake_review_is_producer())wp_die('Unauthorized.',403);
    $request_id=absint($_POST['request_id']??0);
    if(!$request_id||!isset($_POST['tb_intake_proposal_nonce'])||!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['tb_intake_proposal_nonce'])),'tb_qualify_prepare_'.$request_id))wp_die('Invalid request.',400);
    $opp=tuff_beatz_intake_create_opportunity($request_id);
    if(is_wp_error($opp)){wp_safe_redirect(add_query_arg('intake_error',rawurlencode($opp->get_error_message()),home_url('/producer-crm/')));exit;}
    if(!function_exists('tuff_beatz_proposal_workflow_prepare')){wp_safe_redirect(add_query_arg('proposal_error','proposal_workflow_unavailable',get_edit_post_link((int)$opp,'url')));exit;}
    $proposal=tuff_beatz_proposal_workflow_prepare((int)$opp);
    if(is_wp_error($proposal)){wp_safe_redirect(add_query_arg('proposal_error',rawurlencode($proposal->get_error_message()),get_edit_post_link((int)$opp,'url')));exit;}
    update_post_meta($request_id,'_tb_intake_proposal_prepared_at',current_time('mysql'));
    update_post_meta($request_id,'_tb_intake_proposal_prepared_by',get_current_user_id());
    wp_safe_redirect(add_query_arg(array('intake_qualified'=>1,'proposal_prepared'=>1,'request'=>$request_id),get_edit_post_link((int)$opp,'url')));exit;
}
add_action('admin_post_tb_qualify_prepare_proposal','tuff_beatz_handle_qualify_prepare_proposal');

function tuff_beatz_intake_review_admin_box($post){
    if(!tuff_beatz_intake_review_is_producer())return;
    $opp=tuff_beatz_intake_opportunity_id($post->ID);echo '<div style="display:grid;gap:10px">';
    if($opp&&get_post_type($opp)==='tb_opportunity'){
        echo '<p><strong>Qualified Opportunity:</strong> <a href="'.esc_url(get_edit_post_link($opp,'url')).'">Open Opportunity #'.esc_html($opp).'</a></p>';
        $proposal=function_exists('tuff_beatz_v14_proposal_data')?tuff_beatz_v14_proposal_data($opp):array();
        if($proposal){echo '<p><strong>Proposal:</strong> '.esc_html($proposal['number']??'Draft').' • '.esc_html(strtoupper($proposal['status']??'draft')).'</p>';if(function_exists('tuff_beatz_v14_proposal_url'))echo '<p><a class="button" target="_blank" rel="noopener" href="'.esc_url(tuff_beatz_v14_proposal_url($opp)).'">Preview Client Proposal</a></p>';}
        elseif(function_exists('tuff_beatz_proposal_workflow_prepare')){echo '<form method="post" action="'.esc_url(admin_url('admin-post.php')).'"><input type="hidden" name="action" value="tb_qualify_prepare_proposal"><input type="hidden" name="request_id" value="'.esc_attr($post->ID).'">';wp_nonce_field('tb_qualify_prepare_'.$post->ID,'tb_intake_proposal_nonce');echo '<button class="button button-primary" type="submit">Prepare Proposal Draft</button></form>';}
    }else{
        echo '<p>Review this intake, then choose the controlled next step. Qualification creates a linked CRM opportunity. Preparing a proposal creates a draft only; it does not send, accept, contract, invoice, charge, or activate production.</p>';
        echo '<form method="post" action="'.esc_url(admin_url('admin-post.php')).'"><input type="hidden" name="action" value="tb_qualify_intake"><input type="hidden" name="request_id" value="'.esc_attr($post->ID).'">';wp_nonce_field('tb_qualify_intake_'.$post->ID,'tb_intake_nonce');echo '<button class="button" type="submit">Qualify → Create Opportunity</button></form>';
        echo '<form method="post" action="'.esc_url(admin_url('admin-post.php')).'"><input type="hidden" name="action" value="tb_qualify_prepare_proposal"><input type="hidden" name="request_id" value="'.esc_attr($post->ID).'">';wp_nonce_field('tb_qualify_prepare_'.$post->ID,'tb_intake_proposal_nonce');echo '<button class="button button-primary" type="submit">Qualify + Prepare Proposal</button></form>';
    }
    echo '</div>';
}
function tuff_beatz_intake_review_metabox(){add_meta_box('tb_intake_qualification','TUFF BEATZ — Intake Qualification','tuff_beatz_intake_review_admin_box','tb_request','side','high');}
add_action('add_meta_boxes_tb_request','tuff_beatz_intake_review_metabox');
