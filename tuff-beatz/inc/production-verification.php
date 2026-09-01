<?php
if(!defined('ABSPATH'))exit;
/** TUFF BEATZ Studio OS V16.0 — Production Verification & Test Harness (read-only) */
function tuff_beatz_v16_verification_checks($id=0){
    $checks=array();
    $add=function($key,$label,$ok,$detail='')use(&$checks){$checks[]=array('key'=>$key,'label'=>$label,'status'=>$ok?'pass':'fail','detail'=>$detail?:($ok?'PASS':'FAIL'));};
    $required=array(
        'vault'=>'tuff_beatz_vault_files',
        'authorization'=>'tuff_beatz_auth_can',
        'runtime'=>'tuff_beatz_runtime_health',
        'integrity'=>'tuff_beatz_integrity_report',
        'release-readiness'=>'tuff_beatz_rc_checks',
        'delivery-manifest'=>'tuff_beatz_delivery_manifest',
        'delivery-history'=>'tuff_beatz_delivery_manifest_history',
        'acceptance-history'=>'tuff_beatz_delivery_acceptance_history',
        'final-evidence'=>'tuff_beatz_has_final_delivery_evidence'
    );
    foreach($required as $key=>$fn)$add('module-'.$key,ucwords(str_replace('-',' ',$key)).' module',function_exists($fn),function_exists($fn)?'Loaded':'Missing '.$fn);
    $theme=get_template_directory();
    foreach(array('inc/private-file-vault.php','inc/canonical-asset-bridge.php','inc/file-manager-asset-sync.php','inc/final-delivery-guard.php','inc/delivery-engine.php','inc/permission-hardening.php','inc/data-integrity.php','inc/runtime-health.php','inc/release-candidate.php') as $file)$add('file-'.$file,'Required file: '.$file,is_readable($theme.'/'.$file),is_readable($theme.'/'.$file)?'Readable':'Missing or unreadable');
    if($id&&get_post_type($id)==='tb_request'){
        $can_view=function_exists('tuff_beatz_user_can_view_request')&&tuff_beatz_user_can_view_request($id);
        $add('project-view','Current user project access',$can_view,$can_view?'Authorized':'Not authorized for this project');
        if($can_view){
            $runtime=tuff_beatz_runtime_health($id);$add('runtime-state','Runtime Health has no critical failures',empty($runtime['critical']),'Critical: '.(int)($runtime['critical']??0).' / Warnings: '.(int)($runtime['warnings']??0));
            $integrity=tuff_beatz_integrity_report($id);$blocking=0;foreach((array)($integrity['issues']??array()) as $issue)if(in_array($issue['severity']??'',array('critical','high'),true))$blocking++;
            $add('integrity-state','No high/critical integrity conflicts',$blocking===0,$blocking.' blocking integrity issue(s)');
            $rc=tuff_beatz_rc_checks($id,'state');$add('release-state','Release readiness is not blocked',($rc['status']??'blocked')!=='blocked','RC status: '.($rc['status']??'unknown'));
            $status=(string)(get_post_meta($id,'_tb_request_status',true)?:'new');
            if(in_array($status,array('delivery','completed'),true)){
                $manifest=tuff_beatz_delivery_manifest($id);$history=tuff_beatz_delivery_manifest_history($id);$latest=$history?end($history):array();
                $manifest_ok=!empty($manifest['manifest_id'])&&!empty($latest['manifest_id'])&&hash_equals((string)$manifest['manifest_id'],(string)$latest['manifest_id'])&&(int)($manifest['release_version']??0)===(int)($latest['release_version']??0);
                $add('manifest-history','Current manifest matches latest release-history entry',$manifest_ok,$manifest_ok?'Exact manifest/version match':'Manifest/history mismatch');
                $final=tuff_beatz_has_final_delivery_evidence($id);$add('final-evidence','Active canonical Final evidence',$final,$final?'Present':'Missing');
            }
            if($status==='completed'){
                $accept=function_exists('tuff_beatz_delivery_acceptance_record')?tuff_beatz_delivery_acceptance_record($id):array();$manifest=tuff_beatz_delivery_manifest($id);
                $accept_ok=!empty($accept['acceptance_id'])&&!empty($accept['manifest_id'])&&!empty($manifest['manifest_id'])&&hash_equals((string)$accept['manifest_id'],(string)$manifest['manifest_id'])&&(int)($accept['release_version']??0)===(int)($manifest['release_version']??0);
                $add('acceptance-binding','Completed project acceptance matches exact release',$accept_ok,$accept_ok?'Acceptance ID and release binding verified':'Acceptance/release binding incomplete');
            }
        }
    }
    $failed=0;foreach($checks as $check)if($check['status']==='fail')$failed++;
    return array('version'=>'16.0','mode'=>'read-only','status'=>$failed?'attention':'verified','failed'=>$failed,'passed'=>count($checks)-$failed,'checks'=>$checks,'checked_at'=>current_time('mysql'));
}
function tuff_beatz_v16_verification_workspace(){
    if(!is_page('project-dashboard')||!is_user_logged_in())return;
    $id=(int)($_GET['project']??0);if(!$id)return;
    $producer=function_exists('tuff_beatz_auth_is_producer')?tuff_beatz_auth_is_producer($id):current_user_can('edit_post',$id);if(!$producer)return;
    $report=tuff_beatz_v16_verification_checks($id);?><script>document.addEventListener('DOMContentLoaded',function(){var root=document.querySelector('.tb-project-dashboard')||document.querySelector('main');if(!root)return;var panel=document.createElement('section');panel.className='tb-v16-verification';panel.innerHTML=<?php ob_start();?><div class="tb-v16-head"><div><small>STUDIO OS V16.0 • READ-ONLY</small><strong>Production Verification</strong></div><span><?php echo esc_html(strtoupper($report['status']));?></span></div><div class="tb-v16-counts"><b><?php echo esc_html((string)$report['passed']);?></b><span>PASS</span><b><?php echo esc_html((string)$report['failed']);?></b><span>ATTENTION</span></div><?php if($report['failed']):?><div class="tb-v16-failures"><?php foreach($report['checks'] as $check)if($check['status']==='fail'):?><div><b>CHECK</b><p><strong><?php echo esc_html($check['label']);?></strong><span><?php echo esc_html($check['detail']);?></span></p></div><?php endif;?></div><?php endif;?><p class="tb-v16-note">Verification is observational. It does not mutate project state or claim browser-level end-to-end certification.</p><?php $html=ob_get_clean();echo wp_json_encode($html);?>;root.insertBefore(panel,root.firstChild);});</script><?php
}
add_action('wp_footer','tuff_beatz_v16_verification_workspace',6);
function tuff_beatz_v16_verification_assets(){if(!is_page('project-dashboard'))return;wp_add_inline_style('tuff-beatz-project-portal','.tb-v16-verification{max-width:1180px;margin:12px auto 20px;padding:16px;border:1px solid rgba(216,180,90,.24);border-radius:14px;background:rgba(8,8,8,.88)}.tb-v16-head{display:flex;justify-content:space-between;align-items:center}.tb-v16-head small{display:block;color:#8d7b50;font-size:.54rem;letter-spacing:.14em}.tb-v16-head strong{color:#eee2c7;font-family:Cinzel,serif}.tb-v16-head>span{font-size:.58rem;letter-spacing:.12em;color:#d8bb65}.tb-v16-counts{display:flex;align-items:baseline;gap:7px;margin-top:12px}.tb-v16-counts b{color:#e4c466}.tb-v16-counts span{margin-right:12px;color:#716a5e;font-size:.52rem;letter-spacing:.1em}.tb-v16-failures{display:grid;gap:6px;margin-top:12px}.tb-v16-failures>div{display:flex;gap:10px;padding:8px 10px;border-left:2px solid #a98a47;background:rgba(255,255,255,.018)}.tb-v16-failures b{min-width:50px;color:#c9a853;font-size:.5rem}.tb-v16-failures p{margin:0}.tb-v16-failures p strong,.tb-v16-failures p span{display:block}.tb-v16-failures p strong{color:#d8d0bf;font-size:.64rem}.tb-v16-failures p span{color:#817a6e;font-size:.56rem;margin-top:2px}.tb-v16-note{margin:12px 0 0;color:#746e64;font-size:.58rem}');}
add_action('wp_enqueue_scripts','tuff_beatz_v16_verification_assets',104);
