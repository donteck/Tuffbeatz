<?php
/**
 * TUFF BEATZ Site Editor — Phase 1.9 True Staging Workflow
 * Draft → Preview → Publish for public presentation settings only.
 * Protected Studio OS/business/security logic is excluded.
 */
if (!defined('ABSPATH')) exit;

function tuff_beatz_editor_workflow_state(){
    $state=get_option('tuff_beatz_site_editor_workflow',array());
    return wp_parse_args(is_array($state)?$state:array(),array(
        'status'=>'published','draft_saved_at'=>'','published_at'=>'','draft'=>array()
    ));
}

function tuff_beatz_editor_staging_allowed_options(){
    return array(
        'core'=>'tuff_beatz_site_editor',
        'media'=>'tuff_beatz_site_editor_media',
        'sections'=>'tuff_beatz_site_editor_sections',
        'global'=>'tuff_beatz_site_editor_global',
    );
}

function tuff_beatz_editor_staging_sanitize($payload){
    $payload=is_array($payload)?$payload:array();$out=array();
    $out['core']=function_exists('tuff_beatz_site_editor_sanitize')?tuff_beatz_site_editor_sanitize((array)($payload['core']??array())):(array)($payload['core']??array());

    $md=function_exists('tuff_beatz_editor_media_defaults')?tuff_beatz_editor_media_defaults():array('hero_background'=>'','hero_portrait'=>'','hero_logo'=>'','hero_align'=>'left','hero_min_height'=>860,'hero_overlay'=>55);
    $m=(array)($payload['media']??array());
    $out['media']=array(
        'hero_background'=>esc_url_raw($m['hero_background']??''),
        'hero_portrait'=>esc_url_raw($m['hero_portrait']??''),
        'hero_logo'=>esc_url_raw($m['hero_logo']??''),
        'hero_align'=>in_array(($a=sanitize_key($m['hero_align']??'left')),array('left','center','right'),true)?$a:'left',
        'hero_min_height'=>max(620,min(1200,absint($m['hero_min_height']??$md['hero_min_height']))),
        'hero_overlay'=>max(0,min(90,absint($m['hero_overlay']??$md['hero_overlay']))),
    );

    $allowed=function_exists('tuff_beatz_editor_section_defaults')?tuff_beatz_editor_section_defaults():array('about','services','featured','music','platforms');
    $sections=array_values(array_intersect(array_map('sanitize_key',(array)($payload['sections']??array())),$allowed));
    foreach($allowed as $k)if(!in_array($k,$sections,true))$sections[]=$k;
    $out['sections']=$sections;

    $gd=function_exists('tuff_beatz_editor_global_defaults')?tuff_beatz_editor_global_defaults():array();
    $g=(array)($payload['global']??array());$go=array();
    $go['public_logo']=esc_url_raw($g['public_logo']??'');
    foreach(array('brand_subtitle','header_cta_label','footer_prompt','footer_cta_label','footer_slogan','footer_copyright') as $k)$go[$k]=sanitize_text_field($g[$k]??($gd[$k]??''));
    foreach(array('header_cta_url','footer_cta_url') as $k){$v=trim((string)($g[$k]??($gd[$k]??'')));$go[$k]=(strpos($v,'/')===0&&strpos($v,'//')!==0)?sanitize_text_field($v):esc_url_raw($v);}
    $out['global']=$go;
    return $out;
}

function tuff_beatz_editor_staging_save_draft(){
    if(!current_user_can('manage_options'))wp_send_json_error(array('message'=>'Unauthorized'),403);
    check_ajax_referer('tuff_beatz_editor_staging','nonce');
    $raw=isset($_POST['payload'])?json_decode(wp_unslash($_POST['payload']),true):array();
    if(!is_array($raw))wp_send_json_error(array('message'=>'Invalid draft payload'),400);
    $state=tuff_beatz_editor_workflow_state();
    $state['status']='draft';$state['draft_saved_at']=current_time('mysql');$state['draft']=tuff_beatz_editor_staging_sanitize($raw);
    update_option('tuff_beatz_site_editor_workflow',$state,false);
    wp_send_json_success(array('message'=>'Draft saved','saved_at'=>$state['draft_saved_at']));
}
add_action('wp_ajax_tuff_beatz_editor_staging_save_draft','tuff_beatz_editor_staging_save_draft');

function tuff_beatz_editor_staging_publish(){
    if(!current_user_can('manage_options'))wp_send_json_error(array('message'=>'Unauthorized'),403);
    check_ajax_referer('tuff_beatz_editor_staging','nonce');
    $state=tuff_beatz_editor_workflow_state();
    if(empty($state['draft'])||!is_array($state['draft']))wp_send_json_error(array('message'=>'No draft to publish'),409);
    if(function_exists('tuff_beatz_editor_revision_snapshot'))tuff_beatz_editor_revision_snapshot('before_publish');
    foreach(tuff_beatz_editor_staging_allowed_options() as $key=>$option)if(array_key_exists($key,$state['draft']))update_option($option,$state['draft'][$key],false);
    $state['status']='published';$state['published_at']=current_time('mysql');$state['draft']=array();
    update_option('tuff_beatz_site_editor_workflow',$state,false);
    wp_send_json_success(array('message'=>'Draft published','published_at'=>$state['published_at']));
}
add_action('wp_ajax_tuff_beatz_editor_staging_publish','tuff_beatz_editor_staging_publish');

function tuff_beatz_editor_staging_discard(){
    if(!current_user_can('manage_options'))wp_send_json_error(array('message'=>'Unauthorized'),403);
    check_ajax_referer('tuff_beatz_editor_staging','nonce');
    $state=tuff_beatz_editor_workflow_state();$state['status']='published';$state['draft']=array();$state['draft_saved_at']='';
    update_option('tuff_beatz_site_editor_workflow',$state,false);
    wp_send_json_success(array('message'=>'Draft discarded'));
}
add_action('wp_ajax_tuff_beatz_editor_staging_discard','tuff_beatz_editor_staging_discard');

/** Draft preview is admin-only and never changes public option values. */
function tuff_beatz_editor_staging_preview_boot(){
    if(is_admin()||empty($_GET['tbse_preview'])||$_GET['tbse_preview']!=='1'||!is_user_logged_in()||!current_user_can('manage_options'))return;
    $state=tuff_beatz_editor_workflow_state();if(empty($state['draft'])||!is_array($state['draft']))return;
    foreach(tuff_beatz_editor_staging_allowed_options() as $key=>$option){
        if(!array_key_exists($key,$state['draft']))continue;$value=$state['draft'][$key];
        add_filter('pre_option_'.$option,function($pre)use($value){return $value;});
    }
}
add_action('wp', 'tuff_beatz_editor_staging_preview_boot',1);

function tuff_beatz_editor_staging_panel(){
    if(empty($_GET['page'])||$_GET['page']!=='tuff-beatz-site-editor'||!current_user_can('manage_options'))return;
    $state=tuff_beatz_editor_workflow_state();$nonce=wp_create_nonce('tuff_beatz_editor_staging');
    ?>
    <style>
      .tbse-staging{background:#0d0d0d;color:#eee;border:1px solid #3c3121;border-radius:14px;padding:18px 20px;grid-column:1/-1;display:flex;align-items:center;gap:10px;position:sticky;bottom:12px;z-index:70;box-shadow:0 16px 38px rgba(0,0,0,.28)}.tbse-staging-copy{margin-right:auto}.tbse-staging-copy strong{display:block;color:#fff}.tbse-staging-copy small{color:#aaa}.tbse-staging-state{border:1px solid #8a7040;color:#d8b66c;border-radius:999px;padding:6px 10px;font-size:10px;font-weight:800;letter-spacing:.08em}.tbse-staging .button{min-height:36px}.tbse-staging .tbse-publish{background:#b08b42!important;border-color:#b08b42!important;color:#111!important;font-weight:800}.tbse-staging-note{color:#8fd18f!important}@media(max-width:900px){.tbse-staging{flex-wrap:wrap}.tbse-staging-copy{width:100%}}
    </style>
    <script>
    document.addEventListener('DOMContentLoaded',function(){
      var grid=document.querySelector('.tbse-grid');if(!grid)return;var nonce=<?php echo wp_json_encode($nonce);?>,state=<?php echo wp_json_encode($state);?>;
      var old=document.querySelector('.tbse-workflow');if(old)old.remove();
      var bar=document.createElement('section');bar.className='tbse-staging';bar.innerHTML='<div class="tbse-staging-copy"><strong>TRUE STAGING · Draft → Preview → Publish</strong><small id="tbse-stage-note">'+(state.status==='draft'?'Draft saved '+(state.draft_saved_at||'')+' — public site unchanged':'Public site is on the published state')+'</small></div><span class="tbse-staging-state" id="tbse-stage-state">'+String(state.status||'published').toUpperCase()+'</span><button type="button" class="button" id="tbse-stage-save">Save Draft</button><button type="button" class="button" id="tbse-stage-preview">Reload Draft Preview</button><button type="button" class="button" id="tbse-stage-discard">Discard Draft</button><button type="button" class="button button-primary tbse-publish" id="tbse-stage-publish">PUBLISH</button>';grid.appendChild(bar);
      function v(id){var e=document.getElementById(id);return e?e.value:''}
      function collect(){
        var core={};document.querySelectorAll('#tbse-form [name^="tb_editor["]').forEach(function(el){var m=el.name.match(/^tb_editor\[([^\]]+)\]$/);if(!m)return;core[m[1]]=el.type==='checkbox'?(el.checked?1:0):el.value});
        var media={hero_background:v('tbse-hero_background'),hero_portrait:v('tbse-hero_portrait'),hero_logo:v('tbse-hero_logo'),hero_align:v('tbse-hero-align'),hero_min_height:v('tbse-hero-height'),hero_overlay:v('tbse-hero-overlay')};
        var sections=Array.from(document.querySelectorAll('#tbse-section-list .tbse-section-row')).map(function(r){return r.dataset.key}).filter(Boolean);
        var global={};['public_logo','brand_subtitle','header_cta_label','header_cta_url','footer_prompt','footer_cta_label','footer_cta_url','footer_slogan','footer_copyright'].forEach(function(k){global[k]=v('tbse-global-'+k)});
        return {core:core,media:media,sections:sections,global:global};
      }
      function send(action,payload){var d=new URLSearchParams();d.append('action',action);d.append('nonce',nonce);if(payload)d.append('payload',JSON.stringify(payload));return fetch(ajaxurl,{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded; charset=UTF-8'},body:d.toString()}).then(function(r){return r.json()})}
      function draftUI(saved){document.getElementById('tbse-stage-state').textContent='DRAFT';document.getElementById('tbse-stage-note').textContent='Draft saved '+saved+' — public site unchanged';document.getElementById('tbse-stage-note').classList.add('tbse-staging-note')}
      document.getElementById('tbse-stage-save').addEventListener('click',function(){var b=this;b.disabled=true;b.textContent='Saving…';send('tuff_beatz_editor_staging_save_draft',collect()).then(function(j){if(!j.success)throw new Error();draftUI(j.data.saved_at);b.textContent='Draft Saved'}).catch(function(){b.textContent='Save Draft';alert('Could not save draft.')}).finally(function(){b.disabled=false})});
      document.getElementById('tbse-stage-preview').addEventListener('click',function(){var b=this;b.disabled=true;send('tuff_beatz_editor_staging_save_draft',collect()).then(function(j){if(!j.success)throw new Error();draftUI(j.data.saved_at);var f=document.getElementById('tbse-preview');if(f){var u=new URL(f.src,window.location.href);u.searchParams.set('tbse_preview','1');u.searchParams.set('_tbse',Date.now());f.src=u.toString()}}).catch(function(){alert('Could not refresh draft preview.')}).finally(function(){b.disabled=false})});
      document.getElementById('tbse-stage-publish').addEventListener('click',function(){if(!confirm('Publish this draft to the public TUFF BEATZ website?'))return;var b=this;b.disabled=true;b.textContent='PUBLISHING…';send('tuff_beatz_editor_staging_save_draft',collect()).then(function(j){if(!j.success)throw new Error();return send('tuff_beatz_editor_staging_publish')}).then(function(j){if(!j.success)throw new Error();document.getElementById('tbse-stage-state').textContent='PUBLISHED';document.getElementById('tbse-stage-note').textContent='Published '+j.data.published_at;setTimeout(function(){location.reload()},650)}).catch(function(){b.disabled=false;b.textContent='PUBLISH';alert('Publish failed. Public site was not intentionally changed by this staging action.')})});
      document.getElementById('tbse-stage-discard').addEventListener('click',function(){if(!confirm('Discard the current unpublished draft?'))return;send('tuff_beatz_editor_staging_discard').then(function(j){if(j.success)location.reload()})});

      /* Convert legacy editor save controls into draft actions so public settings cannot be changed accidentally from this screen. */
      document.addEventListener('click',function(e){var t=e.target;if(!t)return;var legacy=t.closest('#tbse-media-save,#tbse-global-save,#tbse-section-save');if(!legacy)return;e.preventDefault();e.stopImmediatePropagation();document.getElementById('tbse-stage-save').click()},true);
      var form=document.getElementById('tbse-form');if(form)form.addEventListener('submit',function(e){if(e.submitter&&e.submitter.name==='tb_reset')return;e.preventDefault();e.stopImmediatePropagation();document.getElementById('tbse-stage-save').click()},true);
    });
    </script>
    <?php
}
add_action('admin_footer','tuff_beatz_editor_staging_panel',120);
