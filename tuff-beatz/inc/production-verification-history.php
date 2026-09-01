<?php
if(!defined('ABSPATH'))exit;
/** TUFF BEATZ Studio OS V16.5 — Producer verification history & audit timeline (read-only) */
function tuff_beatz_v165_history_rows($id){
    $history=function_exists('tuff_beatz_v164_snapshot_history')?tuff_beatz_v164_snapshot_history($id):array();
    $rows=array();$previous='';
    foreach($history as $n=>$s){
        $verdict=sanitize_key($s['verdict']??'attention');$change=$previous&&$previous!==$verdict?strtoupper($previous).' → '.strtoupper($verdict):($previous?'UNCHANGED':'BASELINE');
        $rows[]=array('sequence'=>$n+1,'snapshot_id'=>sanitize_text_field($s['snapshot_id']??''),'captured_at'=>sanitize_text_field($s['captured_at']??''),'project_status'=>sanitize_key($s['project_status']??''),'verdict'=>$verdict,'change'=>$change,'failed'=>(int)($s['verification']['failed']??0),'anomalies'=>count((array)($s['anomalies']??array())),'captured_by'=>(int)($s['captured_by']??0));$previous=$verdict;
    }
    return array_reverse($rows);
}
function tuff_beatz_v165_workspace(){
    if(!is_page('project-dashboard')||!is_user_logged_in())return;$id=(int)($_GET['project']??0);if(!$id||get_post_type($id)!=='tb_request'||!current_user_can('edit_post',$id))return;
    $rows=tuff_beatz_v165_history_rows($id);if(!$rows)return;?><script>document.addEventListener('DOMContentLoaded',function(){var host=document.querySelector('.tb-v164-snapshot');if(!host)return;var box=document.createElement('div');box.className='tb-v165-history';box.innerHTML=<?php ob_start();?><div class="tb-v165-title"><small>V16.5 • AUDIT TIMELINE</small><strong>Verification History</strong><span><?php echo esc_html((string)count($rows));?> append-preserving snapshot<?php echo count($rows)===1?'':'s';?></span></div><div class="tb-v165-list"><?php foreach(array_slice($rows,0,8) as $r):?><div><i class="is-<?php echo esc_attr($r['verdict']);?>"></i><p><strong><?php echo esc_html(strtoupper($r['verdict']));?> <em><?php echo esc_html($r['change']);?></em></strong><span><?php echo esc_html($r['captured_at']);?> • <?php echo esc_html(strtoupper($r['project_status']));?> • <?php echo esc_html((string)$r['failed']);?> failed • <?php echo esc_html((string)$r['anomalies']);?> anomalies</span><small><?php echo esc_html($r['snapshot_id']);?></small></p></div><?php endforeach;?></div><?php $html=ob_get_clean();echo wp_json_encode($html);?>;host.insertAdjacentElement('afterend',box);});</script><?php
}
add_action('wp_footer','tuff_beatz_v165_workspace',10);
function tuff_beatz_v165_assets(){if(!is_page('project-dashboard'))return;wp_add_inline_style('tuff-beatz-project-portal','.tb-v165-history{margin-top:10px;padding:12px;border-top:1px solid rgba(216,180,90,.12)}.tb-v165-title{display:flex;align-items:baseline;gap:9px;flex-wrap:wrap}.tb-v165-title small{color:#8d7b50;font-size:.48rem;letter-spacing:.13em}.tb-v165-title strong{color:#d8d0bf;font-size:.65rem}.tb-v165-title span{color:#716a60;font-size:.5rem}.tb-v165-list{display:grid;gap:5px;margin-top:9px}.tb-v165-list>div{display:flex;gap:9px;align-items:flex-start;padding:7px 8px;background:rgba(255,255,255,.014);border-radius:7px}.tb-v165-list i{width:7px;height:7px;border-radius:50%;margin-top:4px;background:#81796b}.tb-v165-list i.is-ready{background:#c7a553}.tb-v165-list i.is-blocked{box-shadow:0 0 0 2px rgba(199,165,83,.18)}.tb-v165-list p{margin:0}.tb-v165-list strong,.tb-v165-list span,.tb-v165-list small{display:block}.tb-v165-list strong{color:#cfc5b2;font-size:.55rem}.tb-v165-list strong em{font-style:normal;color:#8d7b50;font-size:.46rem;margin-left:5px}.tb-v165-list span{color:#716a60;font-size:.49rem;margin-top:2px}.tb-v165-list small{color:#575249;font-size:.43rem;margin-top:2px;word-break:break-all}');}
add_action('wp_enqueue_scripts','tuff_beatz_v165_assets',107);
