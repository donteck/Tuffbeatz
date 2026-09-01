<?php
if(!defined('ABSPATH'))exit;
/** TUFF BEATZ Studio OS V16.3 — Production Readiness Gate (read-only verdict) */
function tuff_beatz_v163_readiness_gate($id){
    if(!$id||get_post_type($id)!=='tb_request')return array('verdict'=>'blocked','reason'=>'Invalid project','critical'=>1,'high'=>0,'attention'=>0);
    $report=function_exists('tuff_beatz_v16_verification_checks')?tuff_beatz_v16_verification_checks($id):array('failed'=>1);
    $anomalies=function_exists('tuff_beatz_v162_anomalies')?tuff_beatz_v162_anomalies($id):array(array('severity'=>'critical','label'=>'V16.2 anomaly engine unavailable'));
    $runtime=function_exists('tuff_beatz_runtime_health')?tuff_beatz_runtime_health($id):array('critical'=>1,'warnings'=>0);
    $integrity=function_exists('tuff_beatz_integrity_report')?tuff_beatz_integrity_report($id):array('issues'=>array(array('severity'=>'critical')));
    $rc=function_exists('tuff_beatz_rc_checks')?tuff_beatz_rc_checks($id,'state'):array('status'=>'blocked');
    $critical=0;$high=0;
    foreach($anomalies as $a){if(($a['severity']??'')==='critical')$critical++;elseif(($a['severity']??'')==='high')$high++;}
    foreach((array)($integrity['issues']??array()) as $issue){if(($issue['severity']??'')==='critical')$critical++;elseif(($issue['severity']??'')==='high')$high++;}
    $runtime_critical=(int)($runtime['critical']??0);$critical+=$runtime_critical;
    $check_fail=(int)($report['failed']??0);$attention=$check_fail+(int)($runtime['warnings']??0);
    $rc_blocked=(($rc['status']??'blocked')==='blocked');
    if($rc_blocked)$critical++;
    $verdict=$critical>0?'blocked':(($high>0||$attention>0)?'attention':'ready');
    $reason=$verdict==='ready'?'No blocking evidence detected':($verdict==='blocked'?'Critical production-readiness condition detected':'Non-blocking conditions require producer review');
    return array('version'=>'16.3','verdict'=>$verdict,'reason'=>$reason,'critical'=>$critical,'high'=>$high,'attention'=>$attention,'rc_status'=>$rc['status']??'unknown','checked_at'=>current_time('mysql'));
}
function tuff_beatz_v163_workspace_gate(){
    if(!is_page('project-dashboard')||!is_user_logged_in())return;$id=(int)($_GET['project']??0);if(!$id)return;
    $producer=function_exists('tuff_beatz_auth_is_producer')?tuff_beatz_auth_is_producer($id):current_user_can('edit_post',$id);if(!$producer)return;
    $gate=tuff_beatz_v163_readiness_gate($id);?><script>document.addEventListener('DOMContentLoaded',function(){var panel=document.querySelector('.tb-v16-verification');if(!panel)return;var gate=document.createElement('div');gate.className='tb-v163-gate is-<?php echo esc_attr($gate['verdict']);?>';gate.innerHTML=<?php ob_start();?><div><small>PRODUCTION READINESS GATE</small><strong><?php echo esc_html(strtoupper($gate['verdict']));?></strong><span><?php echo esc_html($gate['reason']);?></span></div><div class="tb-v163-metrics"><b><?php echo esc_html((string)$gate['critical']);?><i>CRITICAL</i></b><b><?php echo esc_html((string)$gate['high']);?><i>HIGH</i></b><b><?php echo esc_html((string)$gate['attention']);?><i>ATTENTION</i></b></div><?php $html=ob_get_clean();echo wp_json_encode($html);?>;panel.insertBefore(gate,panel.firstChild);});</script><?php
}
add_action('wp_footer','tuff_beatz_v163_workspace_gate',7);
function tuff_beatz_v163_assets(){if(!is_page('project-dashboard'))return;wp_add_inline_style('tuff-beatz-project-portal','.tb-v163-gate{display:flex;justify-content:space-between;gap:18px;align-items:center;margin:-2px -2px 14px;padding:13px 14px;border:1px solid rgba(216,180,90,.18);border-radius:10px;background:rgba(255,255,255,.018)}.tb-v163-gate small{display:block;color:#82765c;font-size:.49rem;letter-spacing:.14em}.tb-v163-gate strong{display:block;margin-top:2px;color:#dbc06f;font-family:Cinzel,serif;font-size:1rem;letter-spacing:.08em}.tb-v163-gate span{display:block;margin-top:2px;color:#777066;font-size:.54rem}.tb-v163-metrics{display:flex;gap:16px}.tb-v163-metrics b{color:#d5b65d;font-size:.9rem;text-align:center}.tb-v163-metrics i{display:block;color:#6f685e;font-style:normal;font-size:.42rem;letter-spacing:.1em}.tb-v163-gate.is-blocked{border-left:3px solid #b38f48}.tb-v163-gate.is-ready strong{color:#e3ca7c}@media(max-width:620px){.tb-v163-gate{align-items:flex-start;flex-direction:column}.tb-v163-metrics{width:100%;justify-content:space-between}}');}
add_action('wp_enqueue_scripts','tuff_beatz_v163_assets',105);
