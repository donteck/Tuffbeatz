<?php
/**
 * TUFF BEATZ Site Editor — Phase 2.0 Visual Click-to-Edit
 * Admin-preview navigation only. Does not alter public content or protected Studio OS logic.
 */
if (!defined('ABSPATH')) exit;

function tuff_beatz_editor_click_edit_panel(){
    if(empty($_GET['page']) || $_GET['page']!=='tuff-beatz-site-editor' || !current_user_can('manage_options')) return;
    ?>
    <style>
      .tbse-clickedit-tip{position:fixed;z-index:2147483646;pointer-events:none;background:#111;color:#fff;border:1px solid #c9a45b;border-radius:8px;padding:6px 9px;font:700 10px/1.2 -apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;letter-spacing:.08em;box-shadow:0 8px 24px rgba(0,0,0,.28);display:none}.tbse-clickedit-focus{outline:2px solid #c9a45b!important;outline-offset:4px;transition:outline-color .25s ease}.tbse-clickedit-flash{animation:tbseClickFlash 1.2s ease}@keyframes tbseClickFlash{0%,100%{box-shadow:none}35%{box-shadow:0 0 0 4px rgba(201,164,91,.28)}}
    </style>
    <script>
    document.addEventListener('DOMContentLoaded',function(){
      var frame=document.getElementById('tbse-preview'),form=document.getElementById('tbse-form');
      if(!frame||!form)return;
      var tip=document.createElement('div');tip.className='tbse-clickedit-tip';tip.textContent='CLICK TO EDIT';document.body.appendChild(tip);
      var map=[
        {selectors:['.hero','#hero'],field:'tb_editor[hero_title]',label:'Hero'},
        {selectors:['#about','.about'],field:'tb_editor[about_title]',label:'About'},
        {selectors:['#services','.services'],field:'tb_editor[services_title]',label:'Services'},
        {selectors:['#music','.music'],field:'tb_editor[music_title]',label:'Recent Work'},
        {selectors:['header','.site-header'],field:'tb_editor[footer_note]',label:'Header / Global'},
        {selectors:['footer','.site-footer'],field:'tb_editor[footer_note]',label:'Footer / Global'}
      ];
      function findTarget(doc,item){for(var i=0;i<item.selectors.length;i++){var el=doc.querySelector(item.selectors[i]);if(el)return el}return null}
      function controlFor(item){return form.querySelector('[name="'+item.field+'"]')||document.querySelector('[name="'+item.field+'"]')}
      function jump(item){
        var control=controlFor(item);if(!control)return;
        var card=control.closest('.tbse-card')||control.closest('section')||control.parentElement;
        if(card){card.scrollIntoView({behavior:'smooth',block:'center'});card.classList.remove('tbse-clickedit-flash');void card.offsetWidth;card.classList.add('tbse-clickedit-flash')}
        setTimeout(function(){try{control.focus({preventScroll:true});control.select&&control.select()}catch(e){}},450);
      }
      function wire(){
        var doc;try{doc=frame.contentDocument||frame.contentWindow.document}catch(e){return}if(!doc)return;
        map.forEach(function(item){
          var el=findTarget(doc,item);if(!el||el.dataset.tbseClickEdit)return;el.dataset.tbseClickEdit='1';el.style.cursor='pointer';
          el.addEventListener('mouseenter',function(e){el.classList.add('tbse-clickedit-focus');tip.textContent='EDIT '+item.label.toUpperCase();tip.style.display='block';tip.style.left=(frame.getBoundingClientRect().left+e.clientX+12)+'px';tip.style.top=(frame.getBoundingClientRect().top+e.clientY+12)+'px'});
          el.addEventListener('mousemove',function(e){tip.style.left=(frame.getBoundingClientRect().left+e.clientX+12)+'px';tip.style.top=(frame.getBoundingClientRect().top+e.clientY+12)+'px'});
          el.addEventListener('mouseleave',function(){el.classList.remove('tbse-clickedit-focus');tip.style.display='none'});
          el.addEventListener('click',function(e){e.preventDefault();e.stopPropagation();tip.style.display='none';jump(item)},true);
        });
        Array.from(doc.querySelectorAll('a,button')).forEach(function(el){if(!el.dataset.tbseNavBlock){el.dataset.tbseNavBlock='1';el.addEventListener('click',function(e){e.preventDefault()},true)}});
      }
      frame.addEventListener('load',function(){setTimeout(wire,250)});setTimeout(wire,500);
      var toolbar=document.querySelector('.tbse-preview-toolbar');if(toolbar&&!document.getElementById('tbse-clickedit-status')){var badge=document.createElement('span');badge.id='tbse-clickedit-status';badge.textContent='CLICK-TO-EDIT ON';badge.style.cssText='border:1px solid #8a7040;color:#d8b66c;border-radius:999px;padding:6px 9px;font-size:9px;font-weight:800;letter-spacing:.08em;white-space:nowrap';toolbar.insertBefore(badge,toolbar.lastElementChild)}
    });
    </script>
    <?php
}
add_action('admin_footer','tuff_beatz_editor_click_edit_panel',120);
