<?php
if(!defined('ABSPATH'))exit;
/**
 * TUFF BEATZ — Proposal Workflow V1.0
 * Producer-only bridge from a qualified CRM opportunity into a structured proposal draft.
 * Does not send, accept, contract, invoice, charge, or create a production project.
 */
function tuff_beatz_proposal_workflow_is_producer(){
    return is_user_logged_in()&&function_exists('tuff_beatz_is_producer_user')&&tuff_beatz_is_producer_user();
}
function tuff_beatz_proposal_workflow_default_scope($opportunity_id){
    $o=function_exists('tuff_beatz_v14_opportunity_data')?tuff_beatz_v14_opportunity_data($opportunity_id):array();
    $request_id=(int)($o['linked_request_id']??0);
    $genre=$request_id?(string)get_post_meta($request_id,'_tb_request_genre',true):'';
    $timeline=$request_id?(string)get_post_meta($request_id,'_tb_request_target_date',true):'';
    if(!$timeline&&$request_id)$timeline=(string)get_post_meta($request_id,'_tb_request_timeline',true);
    $details=$request_id?(string)get_post_meta($request_id,'_tb_request_details',true):'';
    $service=(string)($o['service']??'Production service');
    $lines=array(
        'Service: '.$service,
        $genre?'Creative direction / genre: '.$genre:'',
        $timeline?'Target timeline: '.$timeline:'',
        '',
        'Deliverables and revision limits should be confirmed by the producer before this proposal is sent.',
        $details?'Client brief: '.$details:''
    );
    return trim(implode("\n",array_filter($lines,function($v){return $v!=='';})));
}
function tuff_beatz_proposal_workflow_prepare($opportunity_id){
    $opportunity_id=(int)$opportunity_id;
    if(!$opportunity_id||get_post_type($opportunity_id)!=='tb_opportunity')return new WP_Error('invalid_opportunity','Invalid opportunity.');
    if(!function_exists('tuff_beatz_v14_proposal_data'))return new WP_Error('proposal_engine_missing','Proposal engine is unavailable.');
    $existing=tuff_beatz_v14_proposal_data($opportunity_id);
    if($existing)return $existing;
    $o=tuff_beatz_v14_opportunity_data($opportunity_id);
    $summary='TUFF BEATZ proposes a focused '.(($o['service']??'production')?:'production').' engagement for '.(($o['contact_name']??'this client')?:'this client').'. This draft converts the qualified project intake into a clear commercial scope for producer review before delivery to the client.';
    $subtotal=max(0,(float)($o['estimated_value']??0));
    $p=array(
        'number'=>'TBP-'.str_pad($opportunity_id,5,'0',STR_PAD_LEFT),
        'summary'=>$summary,
        'scope'=>tuff_beatz_proposal_workflow_default_scope($opportunity_id),
        'subtotal'=>$subtotal,
        'discount'=>0,
        'tax'=>0,
        'deposit_percent'=>50,
        'expires'=>wp_date('Y-m-d',current_time('timestamp')+(14*DAY_IN_SECONDS)),
        'updated_at'=>current_time('mysql'),
        'revision'=>1,
        'status'=>'draft'
    );
    $p['version']=hash('sha256',wp_json_encode(array($p['summary'],$p['scope'],$p['subtotal'],$p['discount'],$p['tax'],$p['deposit_percent'],$p['expires'])));
    update_post_meta($opportunity_id,'_tb_v14_proposal',$p);
    update_post_meta($opportunity_id,'_tb_v14_next_action','Review proposal scope and pricing, then send to client.');
    update_post_meta($opportunity_id,'_tb_proposal_draft_prepared_at',current_time('mysql'));
    update_post_meta($opportunity_id,'_tb_proposal_draft_prepared_by',get_current_user_id());
    if(function_exists('tuff_beatz_v14_add_opportunity_activity'))tuff_beatz_v14_add_opportunity_activity($opportunity_id,'proposal','Proposal draft prepared from qualified opportunity');
    return $p;
}
function tuff_beatz_handle_prepare_proposal(){
    if(!tuff_beatz_proposal_workflow_is_producer())wp_die('Unauthorized.',403);
    $id=absint($_POST['opportunity_id']??0);
    if(!$id||!isset($_POST['tb_prepare_proposal_nonce'])||!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['tb_prepare_proposal_nonce'])),'tb_prepare_proposal_'.$id))wp_die('Invalid request.',400);
    $result=tuff_beatz_proposal_workflow_prepare($id);
    $url=get_edit_post_link($id,'url');
    if(is_wp_error($result))$url=add_query_arg('proposal_error',rawurlencode($result->get_error_message()),$url);
    else $url=add_query_arg('proposal_prepared','1',$url);
    wp_safe_redirect($url);exit;
}
add_action('admin_post_tb_prepare_proposal','tuff_beatz_handle_prepare_proposal');
function tuff_beatz_proposal_workflow_box($post){
    if(!tuff_beatz_proposal_workflow_is_producer())return;
    $o=function_exists('tuff_beatz_v14_opportunity_data')?tuff_beatz_v14_opportunity_data($post->ID):array();
    $p=function_exists('tuff_beatz_v14_proposal_data')?tuff_beatz_v14_proposal_data($post->ID):array();
    echo '<div style="display:grid;gap:10px">';
    echo '<p><strong>Commercial stage:</strong> '.esc_html(ucwords(str_replace('-',' ',(string)($o['stage']??'new-lead')))).'</p>';
    if(!$p){
        echo '<p>Create a structured draft from the qualified opportunity. You can edit scope, price, deposit, expiration and terms before sending.</p>';
        echo '<form method="post" action="'.esc_url(admin_url('admin-post.php')).'"><input type="hidden" name="action" value="tb_prepare_proposal"><input type="hidden" name="opportunity_id" value="'.esc_attr($post->ID).'">';
        wp_nonce_field('tb_prepare_proposal_'.$post->ID,'tb_prepare_proposal_nonce');
        echo '<button class="button button-primary" type="submit">Prepare Proposal Draft</button></form>';
    }else{
        $t=function_exists('tuff_beatz_v14_proposal_totals')?tuff_beatz_v14_proposal_totals($p):array('total'=>0,'deposit'=>0);
        echo '<p><strong>Proposal:</strong> '.esc_html($p['number']??'Draft').' • '.esc_html(strtoupper($p['status']??'draft')).'</p>';
        echo '<p><strong>Total:</strong> '.esc_html(function_exists('tuff_beatz_money')?tuff_beatz_money($t['total']):'$'.number_format((float)$t['total'],2)).'<br><strong>Deposit:</strong> '.esc_html(function_exists('tuff_beatz_money')?tuff_beatz_money($t['deposit']):'$'.number_format((float)$t['deposit'],2)).'</p>';
        if(function_exists('tuff_beatz_v14_proposal_url'))echo '<p><a class="button" target="_blank" rel="noopener" href="'.esc_url(tuff_beatz_v14_proposal_url($post->ID)).'">Preview Client Proposal</a></p>';
        echo '<p style="font-size:12px;color:#646970">Sending and client acceptance remain separate controlled actions. Proposal acceptance advances to contract stage only.</p>';
    }
    echo '</div>';
}
function tuff_beatz_proposal_workflow_metabox(){add_meta_box('tb_proposal_workflow','TUFF BEATZ — Proposal Workflow','tuff_beatz_proposal_workflow_box','tb_opportunity','side','high');}
add_action('add_meta_boxes_tb_opportunity','tuff_beatz_proposal_workflow_metabox');
