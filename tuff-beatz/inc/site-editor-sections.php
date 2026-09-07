<?php
/**
 * TUFF BEATZ Site Editor — Phase 1.4 Section Manager
 * Presentation-only homepage section ordering. Protected Studio OS logic is untouched.
 */
if (!defined('ABSPATH')) exit;

function tuff_beatz_editor_section_defaults(){
    return array('about','services','featured','music','platforms');
}
function tuff_beatz_editor_section_order(){
    $allowed=tuff_beatz_editor_section_defaults();
    $saved=(array)get_option('tuff_beatz_site_editor_sections',array());
    $clean=array_values(array_intersect($saved,$allowed));
    foreach($allowed as $key) if(!in_array($key,$clean,true)) $clean[]=$key;
    return $clean;
}
function tuff_beatz_editor_sections_save(){
    if(!current_user_can('manage_options')) wp_send_json_error(array('message'=>'Unauthorized'),403);
    check_ajax_referer('tuff_beatz_editor_sections','nonce');
    $allowed=tuff_beatz_editor_section_defaults();
    $raw=isset($_POST['order'])?(array)wp_unslash($_POST['order']):array();
    $order=array_values(array_intersect(array_map('sanitize_key',$raw),$allowed));
    foreach($allowed as $key) if(!in_array($key,$order,true)) $order[]=$key;
    update_option('tuff_beatz_site_editor_sections',$order,false);
    wp_send_json_success(array('order'=>$order));
}
add_action('wp_ajax_tuff_beatz_editor_sections_save','tuff_beatz_editor_sections_save');

function tuff_beatz_editor_sections_reset(){
    if(!current_user_can('manage_options')) wp_send_json_error(array('message'=>'Unauthorized'),403);
    check_ajax_referer('tuff_beatz_editor_sections','nonce');
    delete_option('tuff_beatz_site_editor_sections');
    wp_send_json_success(array('order'=>tuff_beatz_editor_section_defaults()));
}
add_action('wp_ajax_tuff_beatz_editor_sections_reset','tuff_beatz_editor_sections_reset');

function tuff_beatz_editor_sections_frontend(){
    if(is_admin()) return;
    $order=tuff_beatz_editor_section_order();
    ?>
    <script id="tbse-section-order-runtime">
    document.addEventListener('DOMContentLoaded',function(){
      var order=<?php echo wp_json_encode($order); ?>;
      var selectors={about:'#about',services:'#services',featured:'#featured',music:'#music',platforms:'#platforms'};
      var nodes=order.map(function(k){return document.querySelector(selectors[k])}).filter(Boolean);
      if(nodes.length<2)return;
      var parent=nodes[0].parentNode;
      if(!parent)return;
      nodes.forEach(function(node){parent.appendChild(node)});
    });
    </script>
    <?php
}
add_action('wp_footer','tuff_beatz_editor_sections_frontend',4);

function tuff_beatz_editor_sections_panel(){
    if(empty($_GET['page']) || $_GET['page']!=='tuff-beatz-site-editor' || !current_user_can('manage_options')) return;
    $order=tuff_beatz_editor_section_order();
    $labels=array('about'=>'About','services'=>'Services','featured'=>'Featured Experience','music'=>'Recent Work','platforms'=>'Platforms');
    $nonce=wp_create_nonce('tuff_beatz_editor_sections');
    ?>
    <style>
      .tbse-section-manager{background:#fff;border:1px solid #ddd;border-radius:14px;padding:22px;grid-column:1/-1}.tbse-section-list{display:grid;gap:9px;margin-top:14px}.tbse-section-row{display:grid;grid-template-columns:42px 1fr auto;align-items:center;gap:12px;padding:12px 13px;border:1px solid #e1e1e1;border-radius:11px;background:#fafafa}.tbse-section-handle{font-size:19px;color:#8a7040;text-align:center;cursor:grab}.tbse-section-row strong{display:block}.tbse-section-row small{color:#777}.tbse-section-buttons{display:flex;gap:5px}.tbse-section-buttons button{min-width:34px}.tbse-section-savebar{display:flex;align-items:center;gap:9px;margin-top:14px;padding-top:14px;border-top:1px solid #eee}.tbse-section-status{margin-right:auto;color:#646970}.tbse-section-status.is-dirty{color:#996800;font-weight:700}
    </style>
    <script>
    document.addEventListener('DOMContentLoaded',function(){
      var grid=document.querySelector('.tbse-grid');if(!grid)return;
      var labels=<?php echo wp_json_encode($labels);?>,order=<?php echo wp_json_encode($order);?>;
      var card=document.createElement('section');card.className='tbse-section-manager';
      card.innerHTML='<p class="tbse-kicker">PHASE 1.4</p><h2>Homepage Section Manager</h2><p class="description">Reorder the public homepage safely. Visibility remains controlled by the existing switches above.</p><div class="tbse-section-list" id="tbse-section-list"></div><div class="tbse-section-savebar"><span class="tbse-section-status" id="tbse-section-status">Section order saved</span><button type="button" class="button" id="tbse-section-reset">Reset Order</button><button type="button" class="button button-primary" id="tbse-section-save">Save Section Order</button></div>';
      var visibility=Array.from(grid.querySelectorAll('.tbse-card')).find(function(el){var k=el.querySelector('.tbse-kicker');return k&&k.textContent.trim()==='SECTIONS'});
      if(visibility)visibility.insertAdjacentElement('afterend',card);else grid.prepend(card);
      var list=document.getElementById('tbse-section-list'),status=document.getElementById('tbse-section-status');
      function render(){list.innerHTML='';order.forEach(function(key,i){var row=document.createElement('div');row.className='tbse-section-row';row.dataset.key=key;row.innerHTML='<span class="tbse-section-handle">☰</span><div><strong>'+labels[key]+'</strong><small>Homepage section '+(i+1)+'</small></div><div class="tbse-section-buttons"><button type="button" class="button tbse-up" aria-label="Move up">↑</button><button type="button" class="button tbse-down" aria-label="Move down">↓</button></div>';list.appendChild(row)});preview()}
      function dirty(){status.textContent='Unsaved section order';status.classList.add('is-dirty')}
      function preview(){var f=document.getElementById('tbse-preview'),d;try{d=f&&(f.contentDocument||f.contentWindow.document)}catch(e){return}if(!d)return;var sels={about:'#about',services:'#services',featured:'#featured',music:'#music',platforms:'#platforms'},nodes=order.map(function(k){return d.querySelector(sels[k])}).filter(Boolean);if(nodes.length<2)return;var parent=nodes[0].parentNode;if(!parent)return;nodes.forEach(function(n){parent.appendChild(n)})}
      list.addEventListener('click',function(e){var row=e.target.closest('.tbse-section-row');if(!row)return;var i=order.indexOf(row.dataset.key);if(e.target.classList.contains('tbse-up')&&i>0){var t=order[i-1];order[i-1]=order[i];order[i]=t;dirty();render()}if(e.target.classList.contains('tbse-down')&&i<order.length-1){var t2=order[i+1];order[i+1]=order[i];order[i]=t2;dirty();render()}});
      document.getElementById('tbse-section-save').addEventListener('click',function(){var btn=this;btn.disabled=true;status.textContent='Saving…';var data=new URLSearchParams();data.append('action','tuff_beatz_editor_sections_save');data.append('nonce',<?php echo wp_json_encode($nonce);?>);order.forEach(function(k){data.append('order[]',k)});fetch(ajaxurl,{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded; charset=UTF-8'},body:data.toString()}).then(function(r){return r.json()}).then(function(j){if(!j.success)throw new Error();status.textContent='Section order saved';status.classList.remove('is-dirty')}).catch(function(){status.textContent='Could not save section order';status.classList.add('is-dirty')}).finally(function(){btn.disabled=false})});
      document.getElementById('tbse-section-reset').addEventListener('click',function(){if(!confirm('Restore the approved V3.4 homepage section order?'))return;var data=new URLSearchParams();data.append('action','tuff_beatz_editor_sections_reset');data.append('nonce',<?php echo wp_json_encode($nonce);?>);fetch(ajaxurl,{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded; charset=UTF-8'},body:data.toString()}).then(function(r){return r.json()}).then(function(j){if(j.success)location.reload()})});
      var frame=document.getElementById('tbse-preview');if(frame)frame.addEventListener('load',preview);render();
    });
    </script>
    <?php
}
add_action('admin_footer','tuff_beatz_editor_sections_panel',55);
