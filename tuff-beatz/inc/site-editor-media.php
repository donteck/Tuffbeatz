<?php
/**
 * TUFF BEATZ Site Editor — Phase 1.3 Media & Layout
 * Isolated presentation controls. Does not alter protected Studio OS logic.
 */
if (!defined('ABSPATH')) exit;

function tuff_beatz_editor_media_defaults(){
    return array(
        'hero_background'=>'',
        'hero_portrait'=>'',
        'hero_logo'=>'',
        'hero_align'=>'left',
        'hero_min_height'=>860,
        'hero_overlay'=>55,
    );
}
function tuff_beatz_editor_media_settings(){return wp_parse_args((array)get_option('tuff_beatz_site_editor_media',array()),tuff_beatz_editor_media_defaults());}

function tuff_beatz_editor_media_admin_assets($hook){
    if(empty($_GET['page']) || $_GET['page']!=='tuff-beatz-site-editor') return;
    wp_enqueue_media();
}
add_action('admin_enqueue_scripts','tuff_beatz_editor_media_admin_assets');

function tuff_beatz_editor_media_save(){
    if(!current_user_can('manage_options')) wp_send_json_error(array('message'=>'Unauthorized'),403);
    check_ajax_referer('tuff_beatz_editor_media','nonce');
    $raw=isset($_POST['settings'])?(array)wp_unslash($_POST['settings']):array();
    $d=tuff_beatz_editor_media_defaults();
    $out=array();
    $out['hero_background']=isset($raw['hero_background'])?esc_url_raw($raw['hero_background']):'';
    $out['hero_portrait']=isset($raw['hero_portrait'])?esc_url_raw($raw['hero_portrait']):'';
    $out['hero_logo']=isset($raw['hero_logo'])?esc_url_raw($raw['hero_logo']):'';
    $align=isset($raw['hero_align'])?sanitize_key($raw['hero_align']):'left';
    $out['hero_align']=in_array($align,array('left','center','right'),true)?$align:'left';
    $out['hero_min_height']=max(620,min(1200,absint($raw['hero_min_height']??$d['hero_min_height'])));
    $out['hero_overlay']=max(0,min(90,absint($raw['hero_overlay']??$d['hero_overlay'])));
    update_option('tuff_beatz_site_editor_media',$out,false);
    wp_send_json_success(array('settings'=>$out,'message'=>'Media & layout settings saved.'));
}
add_action('wp_ajax_tuff_beatz_editor_media_save','tuff_beatz_editor_media_save');

function tuff_beatz_editor_media_reset(){
    if(!current_user_can('manage_options')) wp_send_json_error(array('message'=>'Unauthorized'),403);
    check_ajax_referer('tuff_beatz_editor_media','nonce');
    delete_option('tuff_beatz_site_editor_media');
    wp_send_json_success(array('settings'=>tuff_beatz_editor_media_defaults(),'message'=>'Media & layout restored to V3.4 defaults.'));
}
add_action('wp_ajax_tuff_beatz_editor_media_reset','tuff_beatz_editor_media_reset');

function tuff_beatz_editor_media_frontend(){
    if(is_admin()) return;
    $s=tuff_beatz_editor_media_settings();
    $bg=$s['hero_background'];$portrait=$s['hero_portrait'];$logo=$s['hero_logo'];
    ?>
    <style id="tbse-media-runtime">
      .hero{min-height:<?php echo (int)$s['hero_min_height']; ?>px}
      .hero-overlay{opacity:<?php echo esc_attr(number_format(((int)$s['hero_overlay'])/100,2,'.','')); ?>}
      <?php if($bg):?>.hero{background-image:url('<?php echo esc_url($bg); ?>')!important;background-size:cover!important;background-position:center!important}<?php endif;?>
      <?php if($s['hero_align']==='center'):?>.hero-copy{text-align:center}.hero-actions{justify-content:center}<?php elseif($s['hero_align']==='right'):?>.hero-copy{text-align:right}.hero-actions{justify-content:flex-end}<?php else:?>.hero-copy{text-align:left}<?php endif;?>
    </style>
    <?php if($portrait||$logo):?>
    <script id="tbse-media-runtime-js">document.addEventListener('DOMContentLoaded',function(){<?php if($portrait):?>var p=document.querySelector('.hero-person');if(p)p.src=<?php echo wp_json_encode($portrait);?>;<?php endif;?><?php if($logo):?>var l=document.querySelector('.hero-logo');if(l)l.src=<?php echo wp_json_encode($logo);?>;<?php endif;?>});</script>
    <?php endif;
}
add_action('wp_head','tuff_beatz_editor_media_frontend',90);

function tuff_beatz_editor_media_panel(){
    if(empty($_GET['page']) || $_GET['page']!=='tuff-beatz-site-editor' || !current_user_can('manage_options')) return;
    $s=tuff_beatz_editor_media_settings();$nonce=wp_create_nonce('tuff_beatz_editor_media');
    ?>
    <style>
    .tbse-media-card{background:#fff;border:1px solid #ddd;border-radius:14px;padding:22px;grid-column:1/-1;position:relative}.tbse-media-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:14px}.tbse-media-field{border:1px solid #e2e2e2;border-radius:12px;padding:14px;background:#fafafa}.tbse-media-field strong{display:block;margin-bottom:9px}.tbse-media-thumb{height:115px;border-radius:9px;background:#161616 center/cover no-repeat;display:flex;align-items:center;justify-content:center;color:#777;margin-bottom:9px;overflow:hidden}.tbse-media-thumb img{max-width:100%;max-height:100%;object-fit:contain}.tbse-media-actions{display:flex;gap:7px}.tbse-media-actions .button{flex:1;text-align:center}.tbse-layout-grid{display:grid;grid-template-columns:1.2fr 1fr 1fr;gap:14px;margin-top:14px}.tbse-layout-grid label{font-weight:700}.tbse-layout-grid select,.tbse-layout-grid input[type=range]{width:100%;margin-top:7px}.tbse-range-value{font-size:12px;color:#7b642e}.tbse-media-savebar{display:flex;align-items:center;gap:10px;margin-top:16px;padding-top:16px;border-top:1px solid #eee}.tbse-media-status{margin-right:auto;color:#646970}.tbse-media-status.is-dirty{color:#996800;font-weight:700}@media(max-width:900px){.tbse-media-grid,.tbse-layout-grid{grid-template-columns:1fr}}
    </style>
    <script>
    document.addEventListener('DOMContentLoaded',function(){
      var grid=document.querySelector('.tbse-grid');if(!grid)return;
      var card=document.createElement('section');card.className='tbse-media-card';card.innerHTML=`
        <p class="tbse-kicker">PHASE 1.3</p><h2>Hero Media & Layout</h2><p class="description">Choose WordPress Media Library assets and tune the hero without modifying the protected V3.4 stylesheet.</p>
        <div class="tbse-media-grid">
          ${mediaField('hero_background','Hero Background','<?php echo esc_js($s['hero_background']); ?>','Background')}
          ${mediaField('hero_portrait','Producer Portrait','<?php echo esc_js($s['hero_portrait']); ?>','Portrait')}
          ${mediaField('hero_logo','Hero Logo','<?php echo esc_js($s['hero_logo']); ?>','Logo')}
        </div>
        <div class="tbse-layout-grid">
          <label>Content Alignment<select id="tbse-hero-align"><option value="left">Left</option><option value="center">Center</option><option value="right">Right</option></select></label>
          <label>Hero Height <span class="tbse-range-value" id="tbse-height-value"></span><input id="tbse-hero-height" type="range" min="620" max="1200" step="10" value="<?php echo (int)$s['hero_min_height'];?>"></label>
          <label>Overlay Darkness <span class="tbse-range-value" id="tbse-overlay-value"></span><input id="tbse-hero-overlay" type="range" min="0" max="90" step="1" value="<?php echo (int)$s['hero_overlay'];?>"></label>
        </div>
        <div class="tbse-media-savebar"><span class="tbse-media-status" id="tbse-media-status">Media settings saved</span><button type="button" class="button" id="tbse-media-reset">Reset Media</button><button type="button" class="button button-primary" id="tbse-media-save">Save Media & Layout</button></div>`;
      var hero=Array.from(grid.querySelectorAll('.tbse-card')).find(function(el){return el.querySelector('.tbse-kicker')&&el.querySelector('.tbse-kicker').textContent.trim()==='HOMEPAGE'});
      if(hero)hero.insertAdjacentElement('afterend',card);else grid.appendChild(card);
      document.getElementById('tbse-hero-align').value=<?php echo wp_json_encode($s['hero_align']);?>;
      var height=document.getElementById('tbse-hero-height'),overlay=document.getElementById('tbse-hero-overlay'),status=document.getElementById('tbse-media-status');
      function mediaField(key,label,url,kind){return '<div class="tbse-media-field" data-key="'+key+'"><strong>'+label+'</strong><div class="tbse-media-thumb" style="'+(url?'background-image:url(&quot;'+url.replace(/"/g,'&quot;')+'&quot;)':'')+'">'+(url?'':'No '+kind+' selected')+'</div><input type="hidden" id="tbse-'+key+'" value="'+url.replace(/"/g,'&quot;')+'"><div class="tbse-media-actions"><button type="button" class="button tbse-choose">Choose</button><button type="button" class="button tbse-clear">Clear</button></div></div>'}
      function mark(){status.textContent='Unsaved media changes';status.classList.add('is-dirty');preview()}
      function setValues(){document.getElementById('tbse-height-value').textContent=height.value+'px';document.getElementById('tbse-overlay-value').textContent=overlay.value+'%'}
      function frameDoc(){var f=document.getElementById('tbse-preview');try{return f&&(f.contentDocument||f.contentWindow.document)}catch(e){return null}}
      function preview(){var d=frameDoc();if(!d)return;var h=d.querySelector('.hero'),o=d.querySelector('.hero-overlay'),copy=d.querySelector('.hero-copy'),acts=d.querySelector('.hero-actions'),p=d.querySelector('.hero-person'),l=d.querySelector('.hero-logo');if(h){h.style.minHeight=height.value+'px';var bg=document.getElementById('tbse-hero_background').value;h.style.backgroundImage=bg?'url("'+bg.replace(/"/g,'\\"')+'")':'';h.style.backgroundSize=bg?'cover':'';h.style.backgroundPosition=bg?'center':''}if(o)o.style.opacity=(parseInt(overlay.value,10)/100).toString();var a=document.getElementById('tbse-hero-align').value;if(copy)copy.style.textAlign=a;if(acts)acts.style.justifyContent=a==='center'?'center':(a==='right'?'flex-end':'');var pv=document.getElementById('tbse-hero_portrait').value,lv=document.getElementById('tbse-hero_logo').value;if(p&&pv)p.src=pv;if(l&&lv)l.src=lv}
      card.addEventListener('click',function(e){var field=e.target.closest('.tbse-media-field');if(!field)return;var key=field.dataset.key,input=document.getElementById('tbse-'+key),thumb=field.querySelector('.tbse-media-thumb');if(e.target.classList.contains('tbse-choose')){var frame=wp.media({title:'Choose TUFF BEATZ media',button:{text:'Use this media'},multiple:false,library:{type:'image'}});frame.on('select',function(){var a=frame.state().get('selection').first().toJSON();input.value=a.url;thumb.style.backgroundImage='url("'+a.url.replace(/"/g,'\\"')+'")';thumb.textContent='';mark()});frame.open()}if(e.target.classList.contains('tbse-clear')){input.value='';thumb.style.backgroundImage='';thumb.textContent='No media selected';mark()}});
      ['input','change'].forEach(function(ev){card.addEventListener(ev,function(e){if(e.target.matches('#tbse-hero-align,#tbse-hero-height,#tbse-hero-overlay')){setValues();mark()}})});setValues();
      document.getElementById('tbse-media-save').addEventListener('click',function(){var btn=this;btn.disabled=true;status.textContent='Saving…';var data=new URLSearchParams();data.append('action','tuff_beatz_editor_media_save');data.append('nonce',<?php echo wp_json_encode($nonce);?>);['hero_background','hero_portrait','hero_logo'].forEach(function(k){data.append('settings['+k+']',document.getElementById('tbse-'+k).value)});data.append('settings[hero_align]',document.getElementById('tbse-hero-align').value);data.append('settings[hero_min_height]',height.value);data.append('settings[hero_overlay]',overlay.value);fetch(ajaxurl,{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded; charset=UTF-8'},body:data.toString()}).then(function(r){return r.json()}).then(function(j){if(!j.success)throw new Error('Save failed');status.textContent='Media settings saved';status.classList.remove('is-dirty')}).catch(function(){status.textContent='Could not save media settings';status.classList.add('is-dirty')}).finally(function(){btn.disabled=false})});
      document.getElementById('tbse-media-reset').addEventListener('click',function(){if(!confirm('Restore hero media and layout to the V3.4 defaults?'))return;var data=new URLSearchParams();data.append('action','tuff_beatz_editor_media_reset');data.append('nonce',<?php echo wp_json_encode($nonce);?>);fetch(ajaxurl,{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded; charset=UTF-8'},body:data.toString()}).then(function(r){return r.json()}).then(function(j){if(j.success)location.reload()})});
      var previewFrame=document.getElementById('tbse-preview');if(previewFrame)previewFrame.addEventListener('load',preview);
    });
    </script>
    <?php
}
add_action('admin_footer','tuff_beatz_editor_media_panel',50);
