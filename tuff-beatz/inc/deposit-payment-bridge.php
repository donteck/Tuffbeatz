<?php
if(!defined('ABSPATH'))exit;
/**
 * TUFF BEATZ — Deposit / Payment Bridge V1.0
 * Connects an accepted opportunity contract + verified deposit to an EXISTING
 * canonical tb_request created by the live Start a Project portal.
 *
 * Important boundaries:
 * - does not create a duplicate production request when one already exists;
 * - does not verify a deposit automatically;
 * - does not call an external gateway;
 * - does not bypass contract acceptance;
 * - activation requires producer action after deposit verification.
 */
function tuff_beatz_deposit_bridge_is_producer(){
    return is_user_logged_in()&&function_exists('tuff_beatz_is_producer_user')&&tuff_beatz_is_producer_user();
}
function tuff_beatz_deposit_bridge_request_id($opportunity_id){
    $o=function_exists('tuff_beatz_v14_opportunity_data')?tuff_beatz_v14_opportunity_data((int)$opportunity_id):array();
    $request_id=(int)($o['linked_request_id']??0);
    return $request_id&&get_post_type($request_id)==='tb_request'?$request_id:0;
}
function tuff_beatz_deposit_bridge_ready($opportunity_id){
    $contract=function_exists('tuff_beatz_v145_contract_acceptance')?tuff_beatz_v145_contract_acceptance((int)$opportunity_id):array();
    $deposit=function_exists('tuff_beatz_v146_deposit_data')?tuff_beatz_v146_deposit_data((int)$opportunity_id):array();
    $expected=function_exists('tuff_beatz_v146_expected_deposit')?(float)tuff_beatz_v146_expected_deposit((int)$opportunity_id):0.0;
    $received=(float)($deposit['amount']??0);
    return ($contract['decision']??'')==='accepted'&&($deposit['status']??'')==='verified'&&$received>0&&($expected<=0||$received+0.00001>=$expected);
}
function tuff_beatz_deposit_bridge_sync_commercial_record($opportunity_id,$request_id){
    $p=function_exists('tuff_beatz_v14_proposal_data')?tuff_beatz_v14_proposal_data($opportunity_id):array();
    $contract=function_exists('tuff_beatz_v145_contract_data')?tuff_beatz_v145_contract_data($opportunity_id):array();
    $contract_acceptance=function_exists('tuff_beatz_v145_contract_acceptance')?tuff_beatz_v145_contract_acceptance($opportunity_id):array();
    $deposit=function_exists('tuff_beatz_v146_deposit_data')?tuff_beatz_v146_deposit_data($opportunity_id):array();
    if($p){
        $quote=array(
            'number'=>$p['number']??('TBP-'.str_pad($opportunity_id,5,'0',STR_PAD_LEFT)),
            'scope'=>$p['scope']??'',
            'subtotal'=>(float)($p['subtotal']??0),
            'discount'=>(float)($p['discount']??0),
            'tax'=>(float)($p['tax']??0),
            'deposit_percent'=>(float)($p['deposit_percent']??50),
            'expires'=>$p['expires']??'',
            'payment_url'=>$deposit['payment_url']??'',
            'status'=>'accepted',
            'updated_at'=>current_time('mysql'),
            'version'=>$p['version']??hash('sha256',wp_json_encode($p)),
        );
        update_post_meta($request_id,'_tb_quote',$quote);
        update_post_meta($request_id,'_tb_v14_proposal_version',$quote['version']);
    }
    if($contract){
        update_post_meta($request_id,'_tb_contract',array(
            'number'=>$contract['number']??'',
            'terms'=>$contract['terms']??'',
            'start_notes'=>$contract['start_notes']??'',
            'version'=>$contract['version']??'',
            'updated_at'=>current_time('mysql'),
        ));
        update_post_meta($request_id,'_tb_v145_contract_version',$contract['version']??'');
    }
    if($contract_acceptance){
        $accept=array(
            'user_id'=>(int)get_post_field('post_author',$request_id),
            'name'=>$contract_acceptance['name']??'',
            'email'=>$contract_acceptance['email']??'',
            'accepted_at'=>$contract_acceptance['accepted_at']??current_time('mysql'),
            'quote_version'=>$p['version']??'',
            'contract_version'=>$contract['version']??'',
            'source'=>'Studio Business OS Deposit Bridge V1.0',
        );
        update_post_meta($request_id,'_tb_quote_acceptance',$accept);
        update_post_meta($request_id,'_tb_contract_acceptance',$accept);
    }
}
function tuff_beatz_deposit_bridge_activate($opportunity_id){
    $opportunity_id=(int)$opportunity_id;
    $request_id=tuff_beatz_deposit_bridge_request_id($opportunity_id);
    if(!$request_id)return new WP_Error('request_missing','No linked Start a Project request exists.');
    if(!tuff_beatz_deposit_bridge_ready($opportunity_id))return new WP_Error('deposit_not_ready','Accepted contract and a fully verified deposit are required.');
    $already=(string)get_post_meta($request_id,'_tb_deposit_bridge_activated_at',true);
    if($already)return $request_id;

    $deposit=tuff_beatz_v146_deposit_data($opportunity_id);
    $amount=(float)($deposit['amount']??0);
    $method=sanitize_text_field($deposit['method']??'Deposit');
    $reference=sanitize_text_field($deposit['reference']??'');
    $transaction_id=$reference?:('opportunity-'.$opportunity_id.'-deposit');

    tuff_beatz_deposit_bridge_sync_commercial_record($opportunity_id,$request_id);

    $recorded=false;
    if(function_exists('tuff_beatz_v7_add_payment')){
        $recorded=tuff_beatz_v7_add_payment($request_id,$amount,$method,$reference,'studio-business-os',$transaction_id);
        if($recorded===false){
            $payments=function_exists('tuff_beatz_v7_payment_records')?tuff_beatz_v7_payment_records($request_id):array();
            foreach($payments as $payment){
                if(($payment['provider']??'')==='studio-business-os'&&($payment['transaction_id']??'')===$transaction_id){$recorded=true;break;}
            }
        }
    }
    if(!$recorded){
        $payments=get_post_meta($request_id,'_tb_payments_v6',true);if(!is_array($payments))$payments=array();
        $payments[]=array('amount'=>$amount,'method'=>$method,'reference'=>$reference,'provider'=>'studio-business-os','transaction_id'=>$transaction_id,'status'=>'paid','date'=>$deposit['verified_at']??current_time('mysql'),'recorded_by'=>(int)($deposit['verified_by']??get_current_user_id()));
        update_post_meta($request_id,'_tb_payments_v6',$payments);
        if(function_exists('tuff_beatz_sync_payment_status'))tuff_beatz_sync_payment_status($request_id);
    }

    update_post_meta($request_id,'_tb_request_status','approved');
    update_post_meta($request_id,'_tb_request_payment_status','Deposit paid');
    update_post_meta($request_id,'_tb_v14_source_opportunity_id',$opportunity_id);
    update_post_meta($request_id,'_tb_deposit_bridge_activated_at',current_time('mysql'));
    update_post_meta($request_id,'_tb_deposit_bridge_activated_by',get_current_user_id());

    update_post_meta($opportunity_id,'_tb_v14_stage','won');
    update_post_meta($opportunity_id,'_tb_v14_probability',100);
    update_post_meta($opportunity_id,'_tb_v146_converted_at',current_time('mysql'));
    update_post_meta($opportunity_id,'_tb_v146_converted_by',get_current_user_id());

    if(function_exists('tuff_beatz_v14_add_opportunity_activity'))tuff_beatz_v14_add_opportunity_activity($opportunity_id,'conversion','Verified deposit activated existing production request #'.$request_id.' without duplication');
    if(function_exists('tuff_beatz_project_activity')){
        $activity=get_post_meta($request_id,'_tb_project_activity',true);if(!is_array($activity))$activity=array();
        $activity[]=array('type'=>'payment','message'=>'Deposit verified and project financially cleared for production','time'=>current_time('mysql'));
        update_post_meta($request_id,'_tb_project_activity',$activity);
    }
    return $request_id;
}
function tuff_beatz_deposit_bridge_action(){
    if(!tuff_beatz_deposit_bridge_is_producer())wp_die('Unauthorized.',403);
    $id=absint($_POST['opportunity_id']??0);
    if(!$id||!isset($_POST['tb_deposit_bridge_nonce'])||!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['tb_deposit_bridge_nonce'])),'tb_deposit_bridge_'.$id))wp_die('Invalid request.',400);
    $result=tuff_beatz_deposit_bridge_activate($id);
    if(is_wp_error($result)){wp_safe_redirect(add_query_arg('deposit_bridge_error',rawurlencode($result->get_error_code()),get_edit_post_link($id,'url')));exit;}
    wp_safe_redirect(function_exists('tuff_beatz_producer_workspace_url')?tuff_beatz_producer_workspace_url($result,'payments'):tuff_beatz_project_dashboard_url($result));exit;
}
add_action('admin_post_tb_deposit_bridge_activate','tuff_beatz_deposit_bridge_action');
function tuff_beatz_deposit_bridge_box($post){
    if(!tuff_beatz_deposit_bridge_is_producer())return;
    $request_id=tuff_beatz_deposit_bridge_request_id($post->ID);if(!$request_id)return;
    $contract=function_exists('tuff_beatz_v145_contract_acceptance')?tuff_beatz_v145_contract_acceptance($post->ID):array();
    $deposit=function_exists('tuff_beatz_v146_deposit_data')?tuff_beatz_v146_deposit_data($post->ID):array();
    $expected=function_exists('tuff_beatz_v146_expected_deposit')?(float)tuff_beatz_v146_expected_deposit($post->ID):0.0;
    $activated=(string)get_post_meta($request_id,'_tb_deposit_bridge_activated_at',true);
    echo '<p><strong>Existing intake project:</strong> #'.esc_html($request_id).'</p>';
    echo '<p><strong>Contract:</strong> '.esc_html(($contract['decision']??'')==='accepted'?'Accepted':'Not accepted').'<br><strong>Deposit:</strong> '.esc_html(strtoupper($deposit['status']??'due')).'<br><strong>Expected:</strong> '.esc_html(function_exists('tuff_beatz_money')?tuff_beatz_money($expected):'$'.number_format($expected,2)).'<br><strong>Recorded:</strong> '.esc_html(function_exists('tuff_beatz_money')?tuff_beatz_money((float)($deposit['amount']??0)):'$'.number_format((float)($deposit['amount']??0),2)).'</p>';
    if($activated){echo '<p><strong>Financially cleared.</strong><br><small>'.esc_html($activated).'</small></p><p><a class="button button-primary" href="'.esc_url(function_exists('tuff_beatz_producer_workspace_url')?tuff_beatz_producer_workspace_url($request_id,'payments'):tuff_beatz_project_dashboard_url($request_id)).'">Open Project Payments</a></p>';return;}
    if(!tuff_beatz_deposit_bridge_ready($post->ID)){echo '<p>Activation stays locked until the contract is accepted and the full required deposit is marked verified.</p>';return;}
    echo '<p>This will record the verified deposit against the existing intake project and move it to Approved. It will not create a duplicate project.</p><form method="post" action="'.esc_url(admin_url('admin-post.php')).'"><input type="hidden" name="action" value="tb_deposit_bridge_activate"><input type="hidden" name="opportunity_id" value="'.esc_attr($post->ID).'">';wp_nonce_field('tb_deposit_bridge_'.$post->ID,'tb_deposit_bridge_nonce');echo '<button class="button button-primary" type="submit">Activate Existing Project</button></form>';
}
function tuff_beatz_deposit_bridge_metabox(){add_meta_box('tb_deposit_bridge','TUFF BEATZ — Existing Project Activation','tuff_beatz_deposit_bridge_box','tb_opportunity','side','high');}
add_action('add_meta_boxes_tb_opportunity','tuff_beatz_deposit_bridge_metabox');
