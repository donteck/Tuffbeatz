<?php
/**
 * TUFF BEATZ Site Editor — Phase 1.8 Draft / Publish Workflow
 * Presentation settings only. Protected Studio OS/business/security logic is excluded.
 */
if (!defined('ABSPATH')) exit;

function tuff_beatz_editor_workflow_state(){
    $state=get_option('tuff_beatz_site_editor_workflow',array());
    return wp_parse_args(is_array($state)?$state:array(),array('status'=>'published','draft_saved_at'=>'','published_at'=>'','draft'=>array()));
}
function tuff_beatz_editor_workflow_collect_post(){
    $out=array();
    foreach($_POST as $key=>$value){
        if(strpos((string)$key,'tbse_')!==0) continue;
        $key=sanitize_key($key);
        if(is_array($value)) $out[$key]=array_map('sanitize_text_field',wp_unslash($value));
        else $out[$key]=sanitize_textarea_field(wp_unslash($value));
    }
    return $out;
}
function tuff_beatz_editor_workflow_save_draft(){
    if(!current_user_can('manage_options')) wp_send_json_error(array('message'=>'Unauthorized'),403);
    check_ajax_referer('tuff_beatz_editor_workflow','nonce');
    $state=tuff_beatz_editor_workflow_state();
    $state['status']='draft';
    $state['draft_saved_at']=current_time('mysql');
    $state['draft']=tuff_beatz_editor_workflow_collect_post();
    update_option('tuff_beatz_site_editor_workflow',$state,false);
    wp_send_json_success(array('message'=>'Draft saved','saved_at'=>$state['draft_saved_at']));
}
add_action('wp_ajax_tuff_beatz_editor_workflow_save_draft','tuff_beatz_editor_workflow_save_draft');

function tuff_beatz_editor_workflow_mark_published(){
    if(!current_user_can('manage_options')) wp_send_json_error(array('message'=>'Unauthorized'),403);
    check_ajax_referer('tuff_beatz_editor_workflow','nonce');
    $state=tuff_beatz_editor_workflow_state();
    $state['status']='published';
    $state['published_at']=current_time('mysql');
    $state['draft']=array();
    update_option('tuff_beatz_site_editor_workflow',$state,false);
    wp_send_json_success(array('message'=>'Editor state marked published','published_at'=>$state['published_at']));
}
add_action('wp_ajax_tuff_beatz_editor_workflow_mark_published','tuff_beatz_editor_workflow_mark_published');

function tuff_beatz_editor_workflow_panel(){
    if(empty($_GET['page'])||$_GET['page']!=='tuff-beatz-site-editor'||!current_user_can('manage_options')) return;
    $state=tuff_beatz_editor_workflow_state();
    $nonce=wp_create_nonce('tuff_beatz_editor_workflow');
    ?>
    <style>
      .tbse-workflow{background:#0f0f0f;color:#eee;border:1px solid #332b1e;border-radius:14px;padding:18px 20px;grid-column:1/-1;display:flex;align-items:center;gap:14px;position:sticky;bottom:12px;z-index:50;box-shadow:0 14px 35px rgba(0,0,0,.25)}.tbse-workflow-copy{margin-right:auto}.tbse-workflow-copy strong{display:block;color:#fff}.tbse-workflow-copy small{color:#aaa}.tbse-workflow-status{border:1px solid #8a7040;color:#d8b66c;border-radius:999px;padding:6px 10px;font-size:10px;font-weight:800;letter-spacing:.08em}.tbse-workflow .button{min-height:36px}.tbse-workflow .button-primary{background:#b08b42;border-color:#b08b42}@media(max-width:760px){.tbse-workflow{flex-wrap:wrap}.tbse-workflow-copy{width:100%}}
    </style>
    <script>
    document.addEventListener('DOMContentLoaded',function(){
      var grid=document.querySelector('.tbse-grid');if(!grid)return;
      var state=<?php echo wp_json_encode($state);?>,nonce=<?php echo wp_json_encode($nonce);?>;
      var bar=document.createElement('section');bar.className='tbse-workflow';
      bar.innerHTML='<div class="tbse-workflow-copy"><strong>Draft & Publish Workflow</strong><small id="tbse-workflow-note">'+(state.status==='draft'?'Unpublished editor draft saved '+(state.draft_saved_at||''):'Public editor state is published')+'</small></div><span class="tbse-workflow-status" id="tbse-workflow-status">'+String(state.status||'published').toUpperCase()+'</span><button type="button" class="button" id="tbse-save-draft">Save Draft</button><button type="button" class="button button-primary" id="tbse-mark-published">Mark Published</button>';
      grid.appendChild(bar);
      function send(action){var d=new URLSearchParams();d.append('action',action);d.append('nonce',nonce);document.querySelectorAll('.tbse-wrap input,.tbse-wrap textarea,.tbse-wrap select').forEach(function(el){if(!el.name||el.disabled)return;var name='tbse_'+el.name.replace(/[^a-zA-Z0-9_\-]/g,'_');if(el.type==='checkbox'){d.append(name,el.checked?'1':'0')}else{d.append(name,el.value||'')}});return fetch(ajaxurl,{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded; charset=UTF-8'},body:d.toString()}).then(function(r){return r.json()})}
      document.getElementById('tbse-save-draft').addEventListener('click',function(){var b=this;b.disabled=true;b.textContent='Saving…';send('tuff_beatz_editor_workflow_save_draft').then(function(j){if(!j.success)throw new Error();document.getElementById('tbse-workflow-status').textContent='DRAFT';document.getElementById('tbse-workflow-note').textContent='Unpublished editor draft saved '+j.data.saved_at;b.textContent='Draft Saved'}).catch(function(){b.textContent='Save Draft';alert('Could not save draft.')}).finally(function(){b.disabled=false})});
      document.getElementById('tbse-mark-published').addEventListener('click',function(){var b=this;b.disabled=true;b.textContent='Publishing…';send('tuff_beatz_editor_workflow_mark_published').then(function(j){if(!j.success)throw new Error();document.getElementById('tbse-workflow-status').textContent='PUBLISHED';document.getElementById('tbse-workflow-note').textContent='Public editor state marked published '+j.data.published_at;b.textContent='Published'}).catch(function(){b.textContent='Mark Published';alert('Could not update publish state.')}).finally(function(){b.disabled=false})});
    });
    </script>
    <?php
}
add_action('admin_footer','tuff_beatz_editor_workflow_panel',100);
