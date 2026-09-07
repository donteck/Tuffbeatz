<?php
if(!defined('ABSPATH'))exit;
/**
 * TUFF BEATZ — Contract Workflow V1.0
 * Producer-controlled bridge from an accepted proposal to a versioned agreement draft.
 * Does not send, accept, invoice, charge, or create an active production project.
 */
function tuff_beatz_contract_workflow_is_producer(){
    return is_user_logged_in()&&function_exists('tuff_beatz_is_producer_user')&&tuff_beatz_is_producer_user();
}
function tuff_beatz_contract_workflow_default_terms($opportunity_id){
    $o=function_exists('tuff_beatz_v14_opportunity_data')?tuff_beatz_v14_opportunity_data($opportunity_id):array();
    $p=function_exists('tuff_beatz_v14_proposal_data')?tuff_beatz_v14_proposal_data($opportunity_id):array();
    $t=function_exists('tuff_beatz_v14_proposal_totals')?tuff_beatz_v14_proposal_totals($p):array('total'=>0,'deposit'=>0,'deposit_pct'=>0);
    $service=(string)($o['service']??'Production services');
    $scope=trim((string)($p['scope']??''));
    $money=function($value){return function_exists('tuff_beatz_money')?tuff_beatz_money($value):'$'.number_format((float)$value,2);};
    $terms=array(
        'TUFF BEATZ PRODUCTION AGREEMENT — DRAFT FOR PRODUCER REVIEW',
        '',
        '1. PROJECT & SCOPE',
        'Service: '.$service,
        $scope!==''?$scope:'The project scope and deliverables are defined by the accepted proposal attached to this opportunity.',
        '',
        '2. COMMERCIAL TERMS',
        'Approved project total: '.$money($t['total']??0).'.',
        'Initial deposit: '.$money($t['deposit']??0).' ('.(float)($t['deposit_pct']??0).'%).',
        'Production is not automatically activated by platform acceptance of this agreement. Deposit/payment status and producer approval remain separate requirements.',
        '',
        '3. REVISIONS & DELIVERABLES',
        'Revision limits, delivery formats, credits, ownership/licensing terms, and any third-party costs should be confirmed by the producer before the agreement is sent.',
        '',
        '4. CLIENT MATERIALS',
        'The client represents that they own, control, or have permission to provide materials submitted to TUFF BEATZ for the project.',
        '',
        '5. PLATFORM ACCEPTANCE',
        'This website records acceptance of the exact agreement version displayed to the client. This workflow is not represented as a certified third-party electronic-signature service.',
        '',
        'Producer note: Review and customize these draft terms before sending to the client.'
    );
    return implode("\n",$terms);
}
function tuff_beatz_contract_workflow_prepare($opportunity_id){
    $opportunity_id=(int)$opportunity_id;
    if(!$opportunity_id||get_post_type($opportunity_id)!=='tb_opportunity')return new WP_Error('invalid_opportunity','Invalid opportunity.');
    if(!function_exists('tuff_beatz_v14_proposal_acceptance')||!function_exists('tuff_beatz_v145_contract_data'))return new WP_Error('contract_engine_missing','Contract engine is unavailable.');
    $proposal_a=tuff_beatz_v14_proposal_acceptance($opportunity_id);
    if(($proposal_a['decision']??'')!=='accepted')return new WP_Error('proposal_required','The proposal must be accepted before a contract draft can be prepared.');
    $existing=tuff_beatz_v145_contract_data($opportunity_id);
    if($existing)return $existing;
    $proposal=tuff_beatz_v14_proposal_data($opportunity_id);
    $c=array(
        'number'=>'TBC-'.str_pad($opportunity_id,5,'0',STR_PAD_LEFT),
        'terms'=>tuff_beatz_contract_workflow_default_terms($opportunity_id),
        'start_notes'=>'Production start is subject to contract acceptance, required deposit/payment confirmation, and producer approval.',
        'expires'=>wp_date('Y-m-d',current_time('timestamp')+(14*DAY_IN_SECONDS)),
        'updated_at'=>current_time('mysql'),
        'proposal_version'=>$proposal['version']??'',
        'revision'=>1,
        'status'=>'draft'
    );
    $c['version']=hash('sha256',wp_json_encode(array($c['terms'],$c['start_notes'],$c['expires'],$c['proposal_version'])));
    update_post_meta($opportunity_id,'_tb_v145_contract',$c);
    update_post_meta($opportunity_id,'_tb_v14_stage','contract');
    update_post_meta($opportunity_id,'_tb_v14_probability',75);
    update_post_meta($opportunity_id,'_tb_v14_next_action','Review agreement terms, then send contract to client.');
    update_post_meta($opportunity_id,'_tb_contract_draft_prepared_at',current_time('mysql'));
    update_post_meta($opportunity_id,'_tb_contract_draft_prepared_by',get_current_user_id());
    if(function_exists('tuff_beatz_v14_add_opportunity_activity'))tuff_beatz_v14_add_opportunity_activity($opportunity_id,'contract','Contract draft prepared from accepted proposal');
    return $c;
}
function tuff_beatz_handle_prepare_contract(){
    if(!tuff_beatz_contract_workflow_is_producer())wp_die('Unauthorized.',403);
    $id=absint($_POST['opportunity_id']??0);
    if(!$id||!isset($_POST['tb_prepare_contract_nonce'])||!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['tb_prepare_contract_nonce'])),'tb_prepare_contract_'.$id))wp_die('Invalid request.',400);
    $result=tuff_beatz_contract_workflow_prepare($id);
    $url=get_edit_post_link($id,'url');
    if(is_wp_error($result))$url=add_query_arg('contract_error',rawurlencode($result->get_error_message()),$url);else $url=add_query_arg('contract_prepared','1',$url);
    wp_safe_redirect($url);exit;
}
add_action('admin_post_tb_prepare_contract','tuff_beatz_handle_prepare_contract');
function tuff_beatz_contract_workflow_box($post){
    if(!tuff_beatz_contract_workflow_is_producer())return;
    $proposal_a=function_exists('tuff_beatz_v14_proposal_acceptance')?tuff_beatz_v14_proposal_acceptance($post->ID):array();
    $contract=function_exists('tuff_beatz_v145_contract_data')?tuff_beatz_v145_contract_data($post->ID):array();
    echo '<div style="display:grid;gap:10px">';
    echo '<p><strong>Proposal:</strong> '.esc_html(strtoupper($proposal_a['decision']??'not accepted')).'</p>';
    if(($proposal_a['decision']??'')!=='accepted'){
        echo '<p>An accepted proposal is required before a contract draft can be prepared.</p>';
    }elseif(!$contract){
        echo '<p>Create a versioned agreement draft from the accepted proposal. Review and customize all legal/commercial terms before sending.</p>';
        echo '<form method="post" action="'.esc_url(admin_url('admin-post.php')).'"><input type="hidden" name="action" value="tb_prepare_contract"><input type="hidden" name="opportunity_id" value="'.esc_attr($post->ID).'">';
        wp_nonce_field('tb_prepare_contract_'.$post->ID,'tb_prepare_contract_nonce');
        echo '<button class="button button-primary" type="submit">Prepare Contract Draft</button></form>';
    }else{
        echo '<p><strong>Agreement:</strong> '.esc_html($contract['number']??'Draft').' • '.esc_html(strtoupper($contract['status']??'draft')).'</p>';
        if(function_exists('tuff_beatz_v145_contract_url'))echo '<p><a class="button" target="_blank" rel="noopener" href="'.esc_url(tuff_beatz_v145_contract_url($post->ID)).'">Preview Client Agreement</a></p>';
        echo '<p style="font-size:12px;color:#646970">Sending, client acceptance, deposit/payment, and project activation remain separate controlled stages.</p>';
    }
    echo '</div>';
}
function tuff_beatz_contract_workflow_metabox(){add_meta_box('tb_contract_workflow','TUFF BEATZ — Contract Workflow','tuff_beatz_contract_workflow_box','tb_opportunity','side','high');}
add_action('add_meta_boxes_tb_opportunity','tuff_beatz_contract_workflow_metabox');

require_once get_template_directory().'/inc/deposit-payment-bridge.php';
