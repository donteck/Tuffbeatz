<?php
if(!defined('ABSPATH'))exit;
/** TUFF BEATZ Studio OS V16.4 — Producer verification snapshots (explicit, append-preserving, no workflow mutation) */
function tuff_beatz_v164_snapshot_history($id){$h=get_post_meta((int)$id,'_tb_verification_snapshot_v164',false);return is_array($h)?array_values(array_filter($h,'is_array')):array();}
function tuff_beatz_v164_snapshot_payload($id){
    $gate=function_exists('tuff_beatz_v163_readiness_gate')?tuff_beatz_v163_readiness_gate($id):array('verdict'=>'attention','reasons'=>array('V16.3 readiness gate unavailable.'));
    $verification=function_exists('tuff_beatz_v16_verification_checks')?tuff_beatz_v16_verification_checks($id):array();
    $scenarios=function_exists('tuff_beatz_v161_lifecycle_scenarios')?tuff_beatz_v161_lifecycle_scenarios($id):array();
    $anomalies=function_exists('tuff_beatz_v162_anomalies')?tuff_beatz_v162_anomalies($id):array();
    $status=(string)(get_post_meta($id,'_tb_request_status',true)?:'new');
    return array('snapshot_id'=>wp_generate_uuid4(),'version'=>'16.4','project_id'=>(int)$id,'project_status'=>$status,'verdict'=>sanitize_key($gate['verdict']??'attention'),'reasons'=>array_values(array_map('sanitize_text_field',(array)($gate['reasons']??array()))),'verification'=>array('passed'=>(int)($verification['passed']??0),'failed'=>(int)($verification['failed']??0),'status'=>sanitize_key($verification['status']??'unknown')),'lifecycle'=>array_values(array_map(function($s){return array('key'=>sanitize_key($s['key']??''),'label'=>sanitize_text_field($s['label']??''),'state'=>sanitize_key($s['state']??''),'detail'=>sanitize_text_field($s['detail']??''));},(array)$scenarios)),'anomalies'=>array_values(array_map(function($a){return array('key'=>sanitize_key($a['key']??''),'severity'=>sanitize_key($a['severity']??''),'label'=>sanitize_text_field($a['label']??''),'detail'=>sanitize_text_field($a['detail']??''));},(array)$anomalies)),'captured_by'=>get_current_user_id(),'captured_at'=>current_time('mysql'));
}
function tuff_beatz_v164_capture_snapshot(){
    $id=(int)($_POST['request_id']??0);if(!$id||get_post_type($id)!=='tb_request')wp_die('Invalid project.');
    if(!is_user_logged_in()||!current_user_can('edit_post',$id))wp_die('Not authorized.');
    if(!isset($_POST['tb_v164_nonce'])||!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['tb_v164_nonce'])),'tb_v164_snapshot_'.$id))wp_die('Invalid request.');
    $snapshot=tuff_beatz_v164_snapshot_payload($id);add_post_meta($id,'_tb_verification_snapshot_v164',$snapshot,false);
    wp_safe_redirect(add_query_arg(array('project'=>$id,'verification_snapshot'=>'captured'),home_url('/project-dashboard/')));exit;
}
add_action('admin_post_tb_capture_verification_snapshot','tuff_beatz_v164_capture_snapshot');
function tuff_beatz_v164_workspace(){
    if(!is_page('project-dashboard')||!is_user_logged_in())return;$id=(int)($_GET['project']??0);if(!$id||get_post_type($id)!=='tb_request')return;
    if(!current_user_can('edit_post',$id))return;$history=tuff_beatz_v164_snapshot_history($id);$latest=$history?end($history):array();$action=admin_url('admin-post.php');?><script>document.addEventListener('DOMContentLoaded',function(){var host=document.querySelector('.tb-v163-readiness')||document.querySelector('.tb-v16-verification');if(!host)return;var box=document.createElement('div');box.className='tb-v164-snapshot';box.innerHTML=<?php ob_start();?><div><small>V16.4 • VERIFICATION RECORD</small><strong><?php echo $latest?'Latest snapshot: '.esc_html(strtoupper($latest['verdict']??'unknown')):'No verification snapshot captured';?></strong><?php if($latest):?><span><?php echo esc_html($latest['captured_at']??'');?> • <?php echo esc_html($latest['snapshot_id']??'');?></span><?php else:?><span>Capture a timestamped producer verification record without changing workflow state.</span><?php endif;?></div><form method="post" action="<?php echo esc_url($action);?>"><input type="hidden" name="action" value="tb_capture_verification_snapshot"><input type="hidden" name="request_id" value="<?php echo esc_attr((string)$id);?>"><?php wp_nonce_field('tb_v164_snapshot_'.$id,'tb_v164_nonce');?><button type="submit">CAPTURE SNAPSHOT</button></form><?php $html=ob_get_clean();echo wp_json_encode($html);?>;host.appendChild(box);});</script><?php
}
add_action('wp_footer','tuff_beatz_v164_workspace',9);
function tuff_beatz_v164_assets(){if(!is_page('project-dashboard'))return;wp_add_inline_style('tuff-beatz-project-portal','.tb-v164-snapshot{display:flex;align-items:center;justify-content:space-between;gap:16px;margin-top:12px;padding:11px 12px;border-top:1px solid rgba(216,180,90,.12)}.tb-v164-snapshot small,.tb-v164-snapshot strong,.tb-v164-snapshot span{display:block}.tb-v164-snapshot small{color:#8d7b50;font-size:.48rem;letter-spacing:.13em}.tb-v164-snapshot strong{margin-top:3px;color:#d8d0bf;font-size:.62rem}.tb-v164-snapshot span{margin-top:2px;color:#716a60;font-size:.5rem}.tb-v164-snapshot button{border:1px solid rgba(216,180,90,.34);border-radius:7px;background:rgba(216,180,90,.07);color:#d7b95e;padding:8px 10px;font-size:.5rem;letter-spacing:.1em;cursor:pointer}@media(max-width:680px){.tb-v164-snapshot{align-items:flex-start;flex-direction:column}}');}
add_action('wp_enqueue_scripts','tuff_beatz_v164_assets',106);
