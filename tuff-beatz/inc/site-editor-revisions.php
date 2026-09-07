<?php
/**
 * TUFF BEATZ Site Editor — Phase 1.7 Revision History
 * Presentation-only snapshots for editor settings. Protected Studio OS logic is excluded.
 */
if (!defined('ABSPATH')) exit;

function tuff_beatz_editor_revision_options(){
    return array(
        'tuff_beatz_site_editor',
        'tuff_beatz_site_editor_media',
        'tuff_beatz_site_editor_sections',
        'tuff_beatz_site_editor_global',
    );
}
function tuff_beatz_editor_revision_snapshot($reason='update'){
    static $busy=false;
    if($busy) return;
    $busy=true;
    $snapshot=array(
        'id'=>wp_generate_uuid4(),
        'time'=>current_time('mysql'),
        'user'=>get_current_user_id(),
        'reason'=>sanitize_key($reason),
        'settings'=>array(),
    );
    foreach(tuff_beatz_editor_revision_options() as $option){
        $snapshot['settings'][$option]=get_option($option,array());
    }
    $history=(array)get_option('tuff_beatz_site_editor_revision_history',array());
    array_unshift($history,$snapshot);
    $history=array_slice($history,0,20);
    update_option('tuff_beatz_site_editor_revision_history',$history,false);
    $busy=false;
}
function tuff_beatz_editor_revision_capture_updated($option,$old_value,$value){
    if(!in_array($option,tuff_beatz_editor_revision_options(),true)) return;
    tuff_beatz_editor_revision_snapshot($option);
}
function tuff_beatz_editor_revision_capture_added($option,$value){
    if(!in_array($option,tuff_beatz_editor_revision_options(),true)) return;
    tuff_beatz_editor_revision_snapshot($option);
}
add_action('updated_option','tuff_beatz_editor_revision_capture_updated',20,3);
add_action('added_option','tuff_beatz_editor_revision_capture_added',20,2);

function tuff_beatz_editor_revision_restore(){
    if(!current_user_can('manage_options')) wp_send_json_error(array('message'=>'Unauthorized'),403);
    check_ajax_referer('tuff_beatz_editor_revisions','nonce');
    $id=isset($_POST['revision_id'])?sanitize_text_field(wp_unslash($_POST['revision_id'])):'';
    $history=(array)get_option('tuff_beatz_site_editor_revision_history',array());
    $match=null;
    foreach($history as $revision){if(isset($revision['id']) && hash_equals((string)$revision['id'],$id)){$match=$revision;break;}}
    if(!$match || empty($match['settings']) || !is_array($match['settings'])) wp_send_json_error(array('message'=>'Revision not found'),404);
    tuff_beatz_editor_revision_snapshot('before_restore');
    foreach(tuff_beatz_editor_revision_options() as $option){
        $value=array_key_exists($option,$match['settings'])?$match['settings'][$option]:array();
        update_option($option,$value,false);
    }
    wp_send_json_success(array('message'=>'Revision restored.','time'=>$match['time']??''));
}
add_action('wp_ajax_tuff_beatz_editor_revision_restore','tuff_beatz_editor_revision_restore');

function tuff_beatz_editor_revision_panel(){
    if(empty($_GET['page']) || $_GET['page']!=='tuff-beatz-site-editor' || !current_user_can('manage_options')) return;
    $history=(array)get_option('tuff_beatz_site_editor_revision_history',array());
    $nonce=wp_create_nonce('tuff_beatz_editor_revisions');
    ?>
    <style>
      .tbse-revisions{background:#fff;border:1px solid #ddd;border-radius:14px;padding:22px;grid-column:1/-1}.tbse-revision-list{display:grid;gap:8px;margin-top:14px}.tbse-revision-row{display:grid;grid-template-columns:1fr auto;gap:12px;align-items:center;border:1px solid #e6e6e6;border-radius:10px;padding:12px 13px;background:#fafafa}.tbse-revision-row strong{display:block}.tbse-revision-row small{color:#777}.tbse-revision-empty{padding:14px;border:1px dashed #ccc;border-radius:10px;color:#777;margin-top:12px}
    </style>
    <script>
    document.addEventListener('DOMContentLoaded',function(){
      var grid=document.querySelector('.tbse-grid');if(!grid)return;
      var history=<?php echo wp_json_encode($history);?>;
      var card=document.createElement('section');card.className='tbse-revisions';
      var html='<p class="tbse-kicker">PHASE 1.7 · SAFETY</p><h2>Revision History</h2><p class="description">Automatic snapshots of public Site Editor settings. Restore an earlier presentation state without touching Studio OS security or business logic.</p>';
      if(!history.length){html+='<div class="tbse-revision-empty">No revisions yet. A snapshot is created when Site Editor settings change.</div>'}else{html+='<div class="tbse-revision-list">';history.slice(0,10).forEach(function(r,i){var when=r.time||'Unknown time',reason=(r.reason||'update').replace(/_/g,' ');html+='<div class="tbse-revision-row"><div><strong>Revision '+(i+1)+'</strong><small>'+when+' · '+reason+'</small></div><button type="button" class="button tbse-restore-revision" data-id="'+r.id+'">Restore</button></div>'});html+='</div>'}
      card.innerHTML=html;grid.appendChild(card);
      card.addEventListener('click',function(e){if(!e.target.classList.contains('tbse-restore-revision'))return;var btn=e.target,id=btn.dataset.id;if(!confirm('Restore this Site Editor revision? Current public editor settings will be snapshotted first.'))return;btn.disabled=true;btn.textContent='Restoring…';var data=new URLSearchParams();data.append('action','tuff_beatz_editor_revision_restore');data.append('nonce',<?php echo wp_json_encode($nonce);?>);data.append('revision_id',id);fetch(ajaxurl,{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded; charset=UTF-8'},body:data.toString()}).then(function(r){return r.json()}).then(function(j){if(!j.success)throw new Error();location.reload()}).catch(function(){btn.disabled=false;btn.textContent='Restore';alert('Could not restore this revision.')})});
    });
    </script>
    <?php
}
add_action('admin_footer','tuff_beatz_editor_revision_panel',95);
