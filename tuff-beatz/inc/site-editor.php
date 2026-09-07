<?php
/**
 * TUFF BEATZ Site Editor — Phase 1.2
 * Safe, module-driven editor foundation with live visual preview.
 */
if (!defined('ABSPATH')) exit;

function tuff_beatz_editor_defaults(){
    return array(
        'hero_eyebrow'=>'THE PRODUCER IDENTITY OF',
        'hero_name'=>'EMMANUEL TUFFET',
        'hero_title'=>'SOUND. PURPOSE. LEGACY.',
        'hero_lead'=>'TUFF BEATZ is where music, technology and creativity come together to create timeless records that move people, inspire generations and leave a legacy.',
        'hero_primary_label'=>'Play Showreel',
        'hero_secondary_label'=>'Work With Me',
        'hero_secondary_url'=>'#contact',
        'show_about'=>1,'show_services'=>1,'show_featured'=>1,'show_music'=>1,'show_platforms'=>1,
        'about_eyebrow'=>'ABOUT','about_title'=>'MORE THAN BEATS. I BUILD EMOTIONS.',
        'services_eyebrow'=>'WHAT I DO','services_title'=>'PROFESSIONAL MUSIC PRODUCTION',
        'music_eyebrow'=>'MUSIC THAT SPEAKS','music_title'=>'RECENT WORK',
        'footer_note'=>'SOUND. PURPOSE. LEGACY.'
    );
}
function tuff_beatz_editor_settings(){return wp_parse_args((array)get_option('tuff_beatz_site_editor',array()),tuff_beatz_editor_defaults());}
function tuff_beatz_editor_get($key,$fallback=''){$s=tuff_beatz_editor_settings();return array_key_exists($key,$s)?$s[$key]:$fallback;}
function tuff_beatz_editor_enabled($key){return !empty(tuff_beatz_editor_get($key,1));}

function tuff_beatz_site_editor_menu(){
    add_menu_page('TUFF BEATZ Site Editor','TUFF BEATZ Editor','manage_options','tuff-beatz-site-editor','tuff_beatz_site_editor_page','dashicons-admin-customizer',3);
}
add_action('admin_menu','tuff_beatz_site_editor_menu');

function tuff_beatz_site_editor_sanitize($raw){
    $d=tuff_beatz_editor_defaults();$out=array();
    foreach($d as $k=>$default){
        if(strpos($k,'show_')===0){$out[$k]=empty($raw[$k])?0:1;continue;}
        if(substr($k,-4)==='_url'){$out[$k]=isset($raw[$k])?esc_url_raw($raw[$k]):$default;continue;}
        $out[$k]=isset($raw[$k])?sanitize_textarea_field($raw[$k]):$default;
    }
    return $out;
}
function tuff_beatz_site_editor_save(){
    if(!current_user_can('manage_options'))wp_die('Unauthorized');
    check_admin_referer('tuff_beatz_site_editor_save');
    if(isset($_POST['tb_reset'])){delete_option('tuff_beatz_site_editor');wp_safe_redirect(admin_url('admin.php?page=tuff-beatz-site-editor&reset=1'));exit;}
    $raw=isset($_POST['tb_editor'])?(array)wp_unslash($_POST['tb_editor']):array();
    update_option('tuff_beatz_site_editor',tuff_beatz_site_editor_sanitize($raw),false);
    wp_safe_redirect(admin_url('admin.php?page=tuff-beatz-site-editor&saved=1'));exit;
}
add_action('admin_post_tuff_beatz_site_editor_save','tuff_beatz_site_editor_save');

function tuff_beatz_site_editor_page(){
    if(!current_user_can('manage_options'))return;$s=tuff_beatz_editor_settings();
    $checks=array('show_about'=>'About','show_services'=>'Services','show_featured'=>'Featured Experience','show_music'=>'Recent Work','show_platforms'=>'Platforms');
    ?>
    <div class="wrap tbse-wrap">
      <div class="tbse-head"><div><p>TUFF BEATZ • SITE SYSTEM</p><h1>Site Editor <span>Phase 1.2</span></h1><small>Live visual editing for the public experience. Production, permissions, vault, payments and verification remain protected.</small></div><a class="button button-secondary" target="_blank" href="<?php echo esc_url(home_url('/')); ?>">Open Live Site ↗</a></div>
      <?php if(isset($_GET['saved'])):?><div class="notice notice-success is-dismissible"><p>Site Editor settings saved.</p></div><?php endif;?>
      <?php if(isset($_GET['reset'])):?><div class="notice notice-warning is-dismissible"><p>Public editor settings restored to the V3.4 baseline values.</p></div><?php endif;?>
      <div class="tbse-workspace">
        <form class="tbse-controls" id="tbse-form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
          <input type="hidden" name="action" value="tuff_beatz_site_editor_save"><?php wp_nonce_field('tuff_beatz_site_editor_save'); ?>
          <div class="tbse-grid">
            <section class="tbse-card"><p class="tbse-kicker">GLOBAL</p><h2>Brand Foundation</h2><label>Footer / Brand Note<input name="tb_editor[footer_note]" value="<?php echo esc_attr($s['footer_note']); ?>"></label><p class="description">Phase 1.2 keeps the approved V3.4 colors, typography and core CSS protected.</p></section>
            <section class="tbse-card"><p class="tbse-kicker">SECTIONS</p><h2>Visibility</h2><?php foreach($checks as $k=>$label):?><label class="tbse-toggle"><input type="checkbox" name="tb_editor[<?php echo esc_attr($k); ?>]" value="1" <?php checked(!empty($s[$k]));?>><span><?php echo esc_html($label); ?></span></label><?php endforeach;?><p class="description">Preview hide/show instantly. Content and project data are never deleted.</p></section>
            <section class="tbse-card tbse-wide"><p class="tbse-kicker">HOMEPAGE</p><h2>Hero</h2><div class="tbse-fields"><label>Eyebrow<input name="tb_editor[hero_eyebrow]" value="<?php echo esc_attr($s['hero_eyebrow']); ?>"></label><label>Producer Name<input name="tb_editor[hero_name]" value="<?php echo esc_attr($s['hero_name']); ?>"></label><label class="wide">Headline<input name="tb_editor[hero_title]" value="<?php echo esc_attr($s['hero_title']); ?>"></label><label class="wide">Lead<textarea name="tb_editor[hero_lead]" rows="4"><?php echo esc_textarea($s['hero_lead']); ?></textarea></label><label>Primary Button<input name="tb_editor[hero_primary_label]" value="<?php echo esc_attr($s['hero_primary_label']); ?>"></label><label>Secondary Button<input name="tb_editor[hero_secondary_label]" value="<?php echo esc_attr($s['hero_secondary_label']); ?>"></label><label class="wide">Secondary Button URL<input name="tb_editor[hero_secondary_url]" value="<?php echo esc_attr($s['hero_secondary_url']); ?>"></label></div></section>
            <section class="tbse-card tbse-wide"><p class="tbse-kicker">CONTENT</p><h2>Section Headlines</h2><div class="tbse-fields"><label>About Eyebrow<input name="tb_editor[about_eyebrow]" value="<?php echo esc_attr($s['about_eyebrow']); ?>"></label><label>About Headline<input name="tb_editor[about_title]" value="<?php echo esc_attr($s['about_title']); ?>"></label><label>Services Eyebrow<input name="tb_editor[services_eyebrow]" value="<?php echo esc_attr($s['services_eyebrow']); ?>"></label><label>Services Headline<input name="tb_editor[services_title]" value="<?php echo esc_attr($s['services_title']); ?>"></label><label>Music Eyebrow<input name="tb_editor[music_eyebrow]" value="<?php echo esc_attr($s['music_eyebrow']); ?>"></label><label>Music Headline<input name="tb_editor[music_title]" value="<?php echo esc_attr($s['music_title']); ?>"></label></div></section>
            <section class="tbse-card tbse-wide"><p class="tbse-kicker">SAFETY</p><h2>Protected Architecture</h2><div class="tbse-protect"><span>✓ Private File Vault</span><span>✓ Producer / Client Permissions</span><span>✓ Payments & Delivery Guards</span><span>✓ V16 Verification</span><span>✓ V3.4 main.css baseline</span></div><p class="description">Live preview changes the iframe only until you save. System logic remains code-controlled.</p></section>
          </div>
          <div class="tbse-actions"><span id="tbse-dirty" class="tbse-dirty">All changes saved</span><button class="button button-primary button-hero" type="submit">Save Site Settings</button><button class="button" type="submit" name="tb_reset" value="1" onclick="return confirm('Restore Phase 1 editor values to the approved V3.4 baseline?');">Reset to V3.4 Baseline</button></div>
        </form>

        <aside class="tbse-preview-panel">
          <div class="tbse-preview-toolbar">
            <div><strong>LIVE PREVIEW</strong><small id="tbse-preview-state">Connected</small></div>
            <div class="tbse-devices" role="group" aria-label="Preview device">
              <button type="button" class="is-active" data-width="100%">Desktop</button>
              <button type="button" data-width="768px">Tablet</button>
              <button type="button" data-width="390px">Mobile</button>
            </div>
            <button type="button" class="tbse-refresh" id="tbse-refresh">↻</button>
          </div>
          <div class="tbse-frame-stage"><iframe id="tbse-preview" title="TUFF BEATZ live preview" src="<?php echo esc_url(add_query_arg('tbse_preview','1',home_url('/'))); ?>"></iframe></div>
          <p class="tbse-preview-note">Preview is temporary until you click <strong>Save Site Settings</strong>.</p>
        </aside>
      </div>
    </div>
    <style>
      .tbse-wrap{max-width:none;margin-top:28px;padding-right:20px}.tbse-head{display:flex;justify-content:space-between;gap:24px;align-items:center;padding:26px 28px;background:#111;border:1px solid #34302a;border-radius:16px;color:#eee;margin-bottom:18px}.tbse-head p,.tbse-kicker{color:#c9a45b;font-weight:800;letter-spacing:.14em;font-size:11px;margin:0 0 7px}.tbse-head h1{color:#fff;margin:0 0 7px;font-size:30px}.tbse-head h1 span{color:#c9a45b;font-size:14px}.tbse-head small{color:#aaa}.tbse-workspace{display:grid;grid-template-columns:minmax(440px,620px) minmax(520px,1fr);gap:18px;align-items:start}.tbse-controls{min-width:0}.tbse-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px}.tbse-card{background:#fff;border:1px solid #ddd;border-radius:14px;padding:22px}.tbse-wide{grid-column:1/-1}.tbse-card h2{margin-top:0}.tbse-card label{display:block;font-weight:700;margin:0 0 14px}.tbse-card input[type=text],.tbse-card input:not([type]),.tbse-card textarea{display:block;width:100%;max-width:none;margin-top:6px}.tbse-fields{display:grid;grid-template-columns:1fr 1fr;gap:0 16px}.tbse-fields .wide{grid-column:1/-1}.tbse-toggle{display:flex!important;align-items:center;gap:9px;padding:10px 0;border-bottom:1px solid #eee}.tbse-toggle input{margin:0}.tbse-protect{display:flex;flex-wrap:wrap;gap:9px}.tbse-protect span{background:#f5f1e8;border:1px solid #e2d3b2;padding:8px 11px;border-radius:999px;font-weight:700}.tbse-actions{position:sticky;bottom:0;background:rgba(240,240,241,.97);padding:16px 0;display:flex;gap:10px;align-items:center;z-index:5}.tbse-dirty{margin-right:auto;color:#646970;font-size:12px}.tbse-dirty.is-dirty{color:#996800;font-weight:700}.tbse-preview-panel{position:sticky;top:46px;background:#111;border:1px solid #302b24;border-radius:16px;overflow:hidden;box-shadow:0 18px 48px rgba(0,0,0,.18)}.tbse-preview-toolbar{min-height:58px;padding:10px 14px;display:flex;align-items:center;gap:14px;color:#fff;border-bottom:1px solid #2f2a23}.tbse-preview-toolbar>div:first-child{margin-right:auto}.tbse-preview-toolbar strong{display:block;color:#c9a45b;font-size:11px;letter-spacing:.14em}.tbse-preview-toolbar small{display:block;color:#8f8f8f;margin-top:3px}.tbse-devices{display:flex;background:#1b1b1b;border:1px solid #34302a;border-radius:9px;overflow:hidden}.tbse-devices button,.tbse-refresh{appearance:none;border:0;background:transparent;color:#aaa;padding:8px 10px;cursor:pointer}.tbse-devices button+button{border-left:1px solid #34302a}.tbse-devices button.is-active{background:#c9a45b;color:#111;font-weight:800}.tbse-refresh{border:1px solid #34302a;border-radius:9px;font-size:18px}.tbse-frame-stage{height:calc(100vh - 210px);min-height:620px;background:#252525;display:flex;justify-content:center;align-items:flex-start;overflow:auto;padding:12px}.tbse-frame-stage iframe{display:block;width:100%;height:100%;min-height:596px;border:0;background:#000;transition:width .2s ease;box-shadow:0 0 0 1px rgba(255,255,255,.06)}.tbse-preview-note{margin:0;padding:9px 14px;color:#999;font-size:11px;border-top:1px solid #2f2a23}.tbse-preview-note strong{color:#d2b16f}@media(max-width:1400px){.tbse-workspace{grid-template-columns:minmax(420px,520px) 1fr}}@media(max-width:1100px){.tbse-workspace{grid-template-columns:1fr}.tbse-preview-panel{position:relative;top:auto}.tbse-frame-stage{height:760px}.tbse-grid,.tbse-fields{grid-template-columns:1fr}.tbse-wide,.tbse-fields .wide{grid-column:auto}}@media(max-width:782px){.tbse-wrap{padding-right:10px}.tbse-head{align-items:flex-start;flex-direction:column}.tbse-preview-toolbar{flex-wrap:wrap}.tbse-devices{order:3;width:100%}.tbse-devices button{flex:1}}
    </style>
    <script>
    (function(){
      const form=document.getElementById('tbse-form'),frame=document.getElementById('tbse-preview'),dirty=document.getElementById('tbse-dirty'),state=document.getElementById('tbse-preview-state');
      if(!form||!frame)return;
      const q=(doc,sel)=>doc.querySelector(sel);
      const val=name=>{const el=form.querySelector('[name="tb_editor['+name+']"]');return el?el.value:''};
      const checked=name=>{const el=form.querySelector('[name="tb_editor['+name+']"]');return !!(el&&el.checked)};
      const text=(doc,sel,value)=>{const el=q(doc,sel);if(el)el.textContent=value};
      function setVisible(doc,sel,on){const el=q(doc,sel);if(el)el.style.display=on?'':'none'}
      function headline(doc){const el=q(doc,'.hero-copy h1');if(!el)return;const words=val('hero_title').trim().split(/\s+/).filter(Boolean);const last=words.pop()||'';el.innerHTML='';el.append(document.createTextNode(words.join(' ')));el.append(document.createElement('br'));const span=document.createElement('span');span.textContent=last;el.append(span)}
      function apply(){
        let doc;try{doc=frame.contentDocument||frame.contentWindow.document}catch(e){state.textContent='Preview unavailable';return}
        if(!doc||!doc.body)return;
        text(doc,'.hero-copy .eyebrow',val('hero_eyebrow'));text(doc,'.hero-copy .producer-name',val('hero_name'));headline(doc);text(doc,'.hero-copy .lead',val('hero_lead'));
        const heroBtns=doc.querySelectorAll('.hero-actions .btn');if(heroBtns[0])heroBtns[0].textContent='▶ '+val('hero_primary_label');if(heroBtns[1]){heroBtns[1].textContent=val('hero_secondary_label');heroBtns[1].setAttribute('href',val('hero_secondary_url')||'#contact')}
        text(doc,'#about .eyebrow',val('about_eyebrow'));text(doc,'#about h2',val('about_title'));text(doc,'#services .eyebrow',val('services_eyebrow'));text(doc,'#services h2',val('services_title'));text(doc,'#music .music-heading .eyebrow',val('music_eyebrow'));text(doc,'#music .music-heading h2',val('music_title'));
        setVisible(doc,'#about',checked('show_about'));setVisible(doc,'#services',checked('show_services'));setVisible(doc,'#featured-release',checked('show_featured'));setVisible(doc,'#music',checked('show_music'));setVisible(doc,'#credits',checked('show_platforms'));
        state.textContent='Live preview connected';
      }
      function markDirty(){dirty.textContent='Unsaved preview changes';dirty.classList.add('is-dirty');apply()}
      form.addEventListener('input',markDirty);form.addEventListener('change',markDirty);
      frame.addEventListener('load',apply);
      document.querySelectorAll('.tbse-devices button').forEach(btn=>btn.addEventListener('click',()=>{document.querySelectorAll('.tbse-devices button').forEach(b=>b.classList.remove('is-active'));btn.classList.add('is-active');frame.style.width=btn.dataset.width||'100%'}));
      document.getElementById('tbse-refresh').addEventListener('click',()=>{state.textContent='Refreshing…';frame.contentWindow.location.reload()});
    })();
    </script>
    <?php
}
