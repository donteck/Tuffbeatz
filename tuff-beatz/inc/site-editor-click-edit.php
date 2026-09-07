<?php
/**
 * TUFF BEATZ Site Editor — Phase 2.0.1 Visual Click-to-Edit
 * Preview-only selection. Keeps public site and protected Studio OS untouched.
 */
if (!defined('ABSPATH')) exit;

function tuff_beatz_editor_click_edit_panel(){
    if(empty($_GET['page']) || $_GET['page']!=='tuff-beatz-site-editor' || !current_user_can('manage_options')) return;
    ?>
    <style>
      .tbse-clickedit-flash{animation:tbseClickFlash 1.2s ease}@keyframes tbseClickFlash{0%,100%{box-shadow:none}35%{box-shadow:0 0 0 4px rgba(201,164,91,.28)}}
    </style>
    <script>
    document.addEventListener('DOMContentLoaded',function(){
      var frame=document.getElementById('tbse-preview'),form=document.getElementById('tbse-form');
      if(!frame||!form)return;
      var map=[
        {selectors:['.hero','#hero'],field:'tb_editor[hero_title]',label:'Hero'},
        {selectors:['#about','.about'],field:'tb_editor[about_title]',label:'About'},
        {selectors:['#services','.services'],field:'tb_editor[services_title]',label:'Services'},
        {selectors:['#music','.music'],field:'tb_editor[music_title]',label:'Recent Work'},
        {selectors:['header','.site-header'],field:'tb_editor[footer_note]',label:'Header / Global'},
        {selectors:['footer','.site-footer'],field:'tb_editor[footer_note]',label:'Footer / Global'}
      ];
      function controlFor(item){return form.querySelector('[name="'+item.field+'"]')||document.querySelector('[name="'+item.field+'"]')}
      function jump(item){var control=controlFor(item);if(!control)return;var card=control.closest('.tbse-card')||control.closest('section')||control.parentElement;if(card){card.scrollIntoView({behavior:'smooth',block:'center'});card.classList.remove('tbse-clickedit-flash');void card.offsetWidth;card.classList.add('tbse-clickedit-flash')}setTimeout(function(){try{control.focus({preventScroll:true});if(control.select)control.select()}catch(e){}},400)}
      function itemForTarget(target){for(var i=0;i<map.length;i++){for(var j=0;j<map[i].selectors.length;j++){try{if(target.closest(map[i].selectors[j]))return map[i]}catch(e){}}}return null}
      function install(){
        var doc;try{doc=frame.contentDocument||frame.contentWindow.document}catch(e){return}if(!doc||!doc.body||doc.documentElement.dataset.tbseClickDelegated)return;
        doc.documentElement.dataset.tbseClickDelegated='1';
        var style=doc.createElement('style');style.textContent='[data-tbse-hover="1"]{outline:2px solid #c9a45b!important;outline-offset:-2px!important;cursor:pointer!important;position:relative!important} #tbse-preview-label{position:fixed;z-index:2147483647;pointer-events:none;background:#111;color:#fff;border:1px solid #c9a45b;border-radius:7px;padding:6px 9px;font:700 10px/1.2 sans-serif;letter-spacing:.08em;display:none}';doc.head.appendChild(style);
        var label=doc.createElement('div');label.id='tbse-preview-label';doc.body.appendChild(label);var hover=null;
        doc.addEventListener('mousemove',function(e){var item=itemForTarget(e.target);var next=null;if(item){for(var s=0;s<item.selectors.length;s++){try{next=e.target.closest(item.selectors[s]);if(next)break}catch(x){}}}if(hover&&hover!==next)hover.removeAttribute('data-tbse-hover');hover=next;if(hover){hover.setAttribute('data-tbse-hover','1');label.textContent='EDIT '+item.label.toUpperCase();label.style.display='block';label.style.left=(e.clientX+14)+'px';label.style.top=(e.clientY+14)+'px'}else label.style.display='none'},true);
        doc.addEventListener('click',function(e){var item=itemForTarget(e.target);if(!item)return;e.preventDefault();e.stopImmediatePropagation();label.style.display='none';jump(item)},true);
        doc.addEventListener('click',function(e){if(e.target.closest('a,button'))e.preventDefault()},false);
      }
      frame.addEventListener('load',function(){setTimeout(install,300)});setTimeout(install,650);
      var toolbar=document.querySelector('.tbse-preview-toolbar');if(toolbar&&!document.getElementById('tbse-clickedit-status')){var badge=document.createElement('span');badge.id='tbse-clickedit-status';badge.textContent='CLICK-TO-EDIT ON';badge.style.cssText='border:1px solid #8a7040;color:#d8b66c;border-radius:999px;padding:6px 9px;font-size:9px;font-weight:800;letter-spacing:.08em;white-space:nowrap';toolbar.insertBefore(badge,toolbar.lastElementChild)}
    });
    </script>
    <?php
}
add_action('admin_footer','tuff_beatz_editor_click_edit_panel',120);
