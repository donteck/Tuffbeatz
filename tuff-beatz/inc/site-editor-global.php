<?php
/**
 * TUFF BEATZ Site Editor — Phase 1.5 Global Header & Footer
 * Presentation-only controls. Studio OS navigation and protected logic are not editable here.
 */
if (!defined('ABSPATH')) exit;

function tuff_beatz_editor_global_defaults(){
    return array(
        'public_logo'=>'',
        'brand_subtitle'=>'BY EMMANUEL TUFFET',
        'header_cta_label'=>'Work With Me',
        'header_cta_url'=>'/start-a-project/',
        'footer_prompt'=>'Ready to build the sound?',
        'footer_cta_label'=>'Start a Project',
        'footer_cta_url'=>'/start-a-project/',
        'footer_slogan'=>'BUILD THE SOUND. FEEL THE POWER. LEAVE THE LEGACY.',
        'footer_copyright'=>'TUFF BEATZ. ALL RIGHTS RESERVED.'
    );
}
function tuff_beatz_editor_global_settings(){return wp_parse_args((array)get_option('tuff_beatz_site_editor_global',array()),tuff_beatz_editor_global_defaults());}
function tuff_beatz_editor_global_url($value){
    $value=trim((string)$value);
    if($value==='') return '';
    if(strpos($value,'/')===0 && strpos($value,'//')!==0) return home_url($value);
    return $value;
}
function tuff_beatz_editor_global_save(){
    if(!current_user_can('manage_options')) wp_send_json_error(array('message'=>'Unauthorized'),403);
    check_ajax_referer('tuff_beatz_editor_global','nonce');
    $raw=isset($_POST['settings'])?(array)wp_unslash($_POST['settings']):array();
    $d=tuff_beatz_editor_global_defaults();
    $out=array();
    $out['public_logo']=isset($raw['public_logo'])?esc_url_raw($raw['public_logo']):'';
    foreach(array('brand_subtitle','header_cta_label','footer_prompt','footer_cta_label','footer_slogan','footer_copyright') as $k){$out[$k]=isset($raw[$k])?sanitize_text_field($raw[$k]):$d[$k];}
    foreach(array('header_cta_url','footer_cta_url') as $k){$v=isset($raw[$k])?trim((string)$raw[$k]):$d[$k];$out[$k]=strpos($v,'/')===0&&strpos($v,'//')!==0?sanitize_text_field($v):esc_url_raw($v);}
    update_option('tuff_beatz_site_editor_global',$out,false);
    wp_send_json_success(array('settings'=>$out,'message'=>'Global header & footer settings saved.'));
}
add_action('wp_ajax_tuff_beatz_editor_global_save','tuff_beatz_editor_global_save');
function tuff_beatz_editor_global_reset(){
    if(!current_user_can('manage_options')) wp_send_json_error(array('message'=>'Unauthorized'),403);
    check_ajax_referer('tuff_beatz_editor_global','nonce');
    delete_option('tuff_beatz_site_editor_global');
    wp_send_json_success(array('settings'=>tuff_beatz_editor_global_defaults()));
}
add_action('wp_ajax_tuff_beatz_editor_global_reset','tuff_beatz_editor_global_reset');

function tuff_beatz_editor_global_frontend(){
    if(is_admin()) return;$s=tuff_beatz_editor_global_settings();
    $header_url=tuff_beatz_editor_global_url($s['header_cta_url']);$footer_url=tuff_beatz_editor_global_url($s['footer_cta_url']);
    ?>
    <script id="tbse-global-runtime">
    document.addEventListener('DOMContentLoaded',function(){
      var header=document.querySelector('.site-header:not(.tb-os-header)');
      if(header){
        var logo=header.querySelector('.brand img');if(logo&&<?php echo wp_json_encode((string)$s['public_logo']);?>)logo.src=<?php echo wp_json_encode((string)$s['public_logo']);?>;
        var sub=header.querySelector('.brand small');if(sub)sub.textContent=<?php echo wp_json_encode((string)$s['brand_subtitle']);?>;
        var cta=header.querySelector('.header-cta');if(cta&&cta.textContent.trim()!=='My Portal'){cta.textContent=<?php echo wp_json_encode((string)$s['header_cta_label']);?>;cta.href=<?php echo wp_json_encode($header_url);?>;}
      }
      var footer=document.querySelector('.site-footer');if(footer){
        var flogo=footer.querySelector('.footer-brand img');if(flogo&&<?php echo wp_json_encode((string)$s['public_logo']);?>)flogo.src=<?php echo wp_json_encode((string)$s['public_logo']);?>;
        var fsub=footer.querySelector('.footer-brand small');if(fsub)fsub.textContent=<?php echo wp_json_encode((string)$s['brand_subtitle']);?>;
        var prompt=footer.querySelector('.footer-contact p');if(prompt)prompt.textContent=<?php echo wp_json_encode((string)$s['footer_prompt']);?>;
        var fcta=footer.querySelector('.footer-contact .btn');if(fcta){fcta.textContent=<?php echo wp_json_encode((string)$s['footer_cta_label']);?>;fcta.href=<?php echo wp_json_encode($footer_url);?>;}
        var bottoms=footer.querySelectorAll('.footer-bottom span');if(bottoms[0])bottoms[0].textContent=<?php echo wp_json_encode((string)$s['footer_slogan']);?>;if(bottoms[1])bottoms[1].textContent='© '+new Date().getFullYear()+' '+<?php echo wp_json_encode((string)$s['footer_copyright']);?>;
      }
    });
    </script>
    <?php
}
add_action('wp_footer','tuff_beatz_editor_global_frontend',3);

function tuff_beatz_editor_global_admin_assets(){if(!empty($_GET['page'])&&$_GET['page']==='tuff-beatz-site-editor')wp_enqueue_media();}
add_action('admin_enqueue_scripts','tuff_beatz_editor_global_admin_assets');

function tuff_beatz_editor_global_panel(){
    if(empty($_GET['page'])||$_GET['page']!=='tuff-beatz-site-editor'||!current_user_can('manage_options'))return;
    $s=tuff_beatz_editor_global_settings();$nonce=wp_create_nonce('tuff_beatz_editor_global');
    ?>
    <style>
      .tbse-global-card{background:#fff;border:1px solid #ddd;border-radius:14px;padding:22px;grid-column:1/-1}.tbse-global-columns{display:grid;grid-template-columns:1fr 1fr;gap:16px}.tbse-global-box{border:1px solid #e3e3e3;border-radius:12px;padding:16px;background:#fafafa}.tbse-global-box h3{margin-top:0}.tbse-global-box label{display:block;font-weight:700;margin-bottom:12px}.tbse-global-box input{display:block;width:100%;margin-top:6px}.tbse-global-logo{display:grid;grid-template-columns:120px 1fr;gap:12px;align-items:center;margin-bottom:14px}.tbse-global-logo-preview{height:90px;background:#151515 center/contain no-repeat;border-radius:10px;display:flex;align-items:center;justify-content:center;color:#777}.tbse-global-logo-actions{display:flex;gap:7px}.tbse-global-savebar{display:flex;align-items:center;gap:9px;margin-top:14px;padding-top:14px;border-top:1px solid #eee}.tbse-global-status{margin-right:auto;color:#646970}.tbse-global-status.is-dirty{color:#996800;font-weight:700}@media(max-width:900px){.tbse-global-columns{grid-template-columns:1fr}.tbse-global-logo{grid-template-columns:1fr}}
    </style>
    <script>
    document.addEventListener('DOMContentLoaded',function(){
      var grid=document.querySelector('.tbse-grid');if(!grid)return;var s=<?php echo wp_json_encode($s);?>;
      var card=document.createElement('section');card.className='tbse-global-card';card.innerHTML=`
        <p class="tbse-kicker">PHASE 1.5</p><h2>Global Header & Footer</h2><p class="description">Edit public branding and calls-to-action. Private Studio OS navigation, account links and security controls are intentionally excluded.</p>
        <div class="tbse-global-logo"><div class="tbse-global-logo-preview" id="tbse-global-logo-preview">${s.public_logo?'':'Using theme logo'}</div><div><strong>Public Logo</strong><input type="hidden" id="tbse-global-public_logo" value="${esc(s.public_logo)}"><div class="tbse-global-logo-actions"><button type="button" class="button" id="tbse-global-logo-choose">Choose Logo</button><button type="button" class="button" id="tbse-global-logo-clear">Use Theme Logo</button></div></div></div>
        <div class="tbse-global-columns"><div class="tbse-global-box"><h3>Header</h3><label>Brand Subtitle<input id="tbse-global-brand_subtitle" value="${esc(s.brand_subtitle)}"></label><label>Guest CTA Label<input id="tbse-global-header_cta_label" value="${esc(s.header_cta_label)}"></label><label>Guest CTA URL<input id="tbse-global-header_cta_url" value="${esc(s.header_cta_url)}"></label><p class="description">Logged-in “My Portal” remains protected and is never overwritten.</p></div><div class="tbse-global-box"><h3>Footer</h3><label>Prompt<input id="tbse-global-footer_prompt" value="${esc(s.footer_prompt)}"></label><label>CTA Label<input id="tbse-global-footer_cta_label" value="${esc(s.footer_cta_label)}"></label><label>CTA URL<input id="tbse-global-footer_cta_url" value="${esc(s.footer_cta_url)}"></label><label>Slogan<input id="tbse-global-footer_slogan" value="${esc(s.footer_slogan)}"></label><label>Copyright Text<input id="tbse-global-footer_copyright" value="${esc(s.footer_copyright)}"></label></div></div>
        <div class="tbse-global-savebar"><span class="tbse-global-status" id="tbse-global-status">Global settings saved</span><button type="button" class="button" id="tbse-global-reset">Reset Global</button><button type="button" class="button button-primary" id="tbse-global-save">Save Header & Footer</button></div>`;
      var first=grid.querySelector('.tbse-card');if(first)first.insertAdjacentElement('afterend',card);else grid.prepend(card);
      var preview=document.getElementById('tbse-global-logo-preview');if(s.public_logo)preview.style.backgroundImage='url("'+s.public_logo.replace(/"/g,'\\"')+'")';var status=document.getElementById('tbse-global-status');
      function esc(v){return String(v||'').replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/</g,'&lt;').replace(/>/g,'&gt;')}
      function v(k){var e=document.getElementById('tbse-global-'+k);return e?e.value:''}
      function fullUrl(url){if(!url)return '';if(url.charAt(0)==='/'&&url.charAt(1)!=='/')return <?php echo wp_json_encode(home_url('/'));?>.replace(/\/$/,'')+url;return url}
      function frameDoc(){var f=document.getElementById('tbse-preview');try{return f&&(f.contentDocument||f.contentWindow.document)}catch(e){return null}}
      function apply(){var d=frameDoc();if(!d)return;var h=d.querySelector('.site-header:not(.tb-os-header)');if(h){var logo=h.querySelector('.brand img');if(logo&&v('public_logo'))logo.src=v('public_logo');var sub=h.querySelector('.brand small');if(sub)sub.textContent=v('brand_subtitle');var cta=h.querySelector('.header-cta');if(cta&&cta.textContent.trim()!=='My Portal'){cta.textContent=v('header_cta_label');cta.href=fullUrl(v('header_cta_url'))}}var f=d.querySelector('.site-footer');if(f){var fl=f.querySelector('.footer-brand img');if(fl&&v('public_logo'))fl.src=v('public_logo');var fs=f.querySelector('.footer-brand small');if(fs)fs.textContent=v('brand_subtitle');var p=f.querySelector('.footer-contact p');if(p)p.textContent=v('footer_prompt');var c=f.querySelector('.footer-contact .btn');if(c){c.textContent=v('footer_cta_label');c.href=fullUrl(v('footer_cta_url'))}var b=f.querySelectorAll('.footer-bottom span');if(b[0])b[0].textContent=v('footer_slogan');if(b[1])b[1].textContent='© '+new Date().getFullYear()+' '+v('footer_copyright')}}
      function dirty(){status.textContent='Unsaved global changes';status.classList.add('is-dirty');apply()}
      card.addEventListener('input',function(e){if(e.target.matches('input'))dirty()});
      document.getElementById('tbse-global-logo-choose').addEventListener('click',function(){var frame=wp.media({title:'Choose TUFF BEATZ public logo',button:{text:'Use this logo'},multiple:false,library:{type:'image'}});frame.on('select',function(){var a=frame.state().get('selection').first().toJSON();document.getElementById('tbse-global-public_logo').value=a.url;preview.textContent='';preview.style.backgroundImage='url("'+a.url.replace(/"/g,'\\"')+'")';dirty()});frame.open()});
      document.getElementById('tbse-global-logo-clear').addEventListener('click',function(){document.getElementById('tbse-global-public_logo').value='';preview.style.backgroundImage='';preview.textContent='Using theme logo';dirty()});
      document.getElementById('tbse-global-save').addEventListener('click',function(){var btn=this;btn.disabled=true;status.textContent='Saving…';var keys=['public_logo','brand_subtitle','header_cta_label','header_cta_url','footer_prompt','footer_cta_label','footer_cta_url','footer_slogan','footer_copyright'],data=new URLSearchParams();data.append('action','tuff_beatz_editor_global_save');data.append('nonce',<?php echo wp_json_encode($nonce);?>);keys.forEach(function(k){data.append('settings['+k+']',v(k))});fetch(ajaxurl,{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded; charset=UTF-8'},body:data.toString()}).then(function(r){return r.json()}).then(function(j){if(!j.success)throw new Error();status.textContent='Global settings saved';status.classList.remove('is-dirty')}).catch(function(){status.textContent='Could not save global settings';status.classList.add('is-dirty')}).finally(function(){btn.disabled=false})});
      document.getElementById('tbse-global-reset').addEventListener('click',function(){if(!confirm('Restore public header and footer controls to the V3.4 defaults?'))return;var data=new URLSearchParams();data.append('action','tuff_beatz_editor_global_reset');data.append('nonce',<?php echo wp_json_encode($nonce);?>);fetch(ajaxurl,{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded; charset=UTF-8'},body:data.toString()}).then(function(r){return r.json()}).then(function(j){if(j.success)location.reload()})});
      var pf=document.getElementById('tbse-preview');if(pf)pf.addEventListener('load',apply);apply();
    });
    </script>
    <?php
}
add_action('admin_footer','tuff_beatz_editor_global_panel',58);
