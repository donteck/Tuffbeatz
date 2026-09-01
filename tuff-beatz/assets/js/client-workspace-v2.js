(function(){
  function initClientWorkspace(){
    var body=document.body;
    if(!body||!body.classList.contains('tb-client-workspace')) return;
    var nav=document.querySelector('.tb-dashboard-nav');
    var main=document.querySelector('.tb-dashboard-main');
    if(!nav||!main) return;
    var panels=Array.prototype.slice.call(main.querySelectorAll('.tb-workspace-section'));
    if(!panels.length) return;
    body.classList.add('tb-workspace-tabs-ready');

    function normalize(id){
      if(id==='payments' && document.getElementById('commerce')) return 'commerce';
      return id;
    }
    function activate(id,scroll){
      id=normalize(id||'overview');
      var target=document.getElementById(id);
      if(!target||!target.classList.contains('tb-workspace-section')){id='overview';target=document.getElementById(id);}
      panels=Array.prototype.slice.call(main.querySelectorAll('.tb-workspace-section'));
      panels.forEach(function(p){p.classList.toggle('is-active-panel',p.id===id);});
      Array.prototype.slice.call(nav.querySelectorAll('a[href^="#"]')).forEach(function(a){
        var href=normalize((a.getAttribute('href')||'').replace('#',''));
        a.classList.toggle('is-active',href===id);
      });
      if(history.replaceState) history.replaceState(null,'','#'+id);
      if(scroll&&target){var y=target.getBoundingClientRect().top+window.pageYOffset-145;window.scrollTo({top:y,behavior:'smooth'});}
    }

    nav.addEventListener('click',function(e){var a=e.target.closest('a[href^="#"]');if(!a)return;var id=(a.getAttribute('href')||'').slice(1);if(!id)return;e.preventDefault();activate(id,true);});
    document.addEventListener('click',function(e){var a=e.target.closest('a[href^="#"]');if(!a||nav.contains(a))return;var id=(a.getAttribute('href')||'').slice(1);var el=document.getElementById(normalize(id));if(el&&el.classList.contains('tb-workspace-section')){e.preventDefault();activate(id,true);}});

    /* Turn the six-stage production journey into a useful Studio OS control. */
    var journey=document.querySelector('.tb-stage-track, .tb-project-stages, .tb-production-journey');
    if(!journey){
      var candidates=Array.prototype.slice.call(document.querySelectorAll('.tb-client-workspace a, .tb-client-workspace button, .tb-client-workspace div'));
      journey=candidates.find(function(el){return /01\s*Intake/i.test(el.textContent||'')&&/06\s*Delivery/i.test(el.textContent||'');});
    }
    if(journey){
      var stageMap=[
        {re:/Intake/i,tab:'overview'},
        {re:/Approved/i,tab:'timeline'},
        {re:/Production/i,tab:'files'},
        {re:/Revision/i,tab:'review'},
        {re:/Mastering/i,tab:'review'},
        {re:/Delivery/i,tab:'delivery'}
      ];
      var nodes=Array.prototype.slice.call(journey.children).filter(function(n){return (n.textContent||'').trim();});
      if(nodes.length<6) nodes=Array.prototype.slice.call(journey.querySelectorAll('a,button,span,div')).filter(function(n){return /^\s*0[1-6]\b/.test((n.textContent||'').trim());});
      nodes.forEach(function(node){
        var txt=node.textContent||'';var m=stageMap.find(function(x){return x.re.test(txt);});if(!m)return;
        node.setAttribute('role','button');node.setAttribute('tabindex','0');node.classList.add('tb-journey-action');
        var go=function(){activate(m.tab,true);};
        node.addEventListener('click',go);node.addEventListener('keydown',function(e){if(e.key==='Enter'||e.key===' '){e.preventDefault();go();}});
      });
    }

    /* Files UX: explain what belongs in each vault category and when it should be uploaded. */
    function enhanceFiles(){
      var files=document.getElementById('files');
      if(!files||files.dataset.uploadGuideReady==='1')return false;
      var upload=files.querySelector('.tb-fm-upload');
      var select=upload&&upload.querySelector('select[name="studio_category"]');
      if(!upload||!select)return false;
      files.dataset.uploadGuideReady='1';
      var guide=document.createElement('section');
      guide.className='tb-upload-guide';
      guide.innerHTML='<div class="tb-upload-guide-head"><div><small>PRODUCTION FILE FLOW</small><strong>What to upload — and when</strong><span>Keep every asset in the right production stage. Select a stage below to prepare the upload form.</span></div><b>PRIVATE VAULT</b></div><div class="tb-upload-steps"><button type="button" data-upload-cat="source"><i>01</i><strong>Source Files</strong><span>Project start</span><em>Sessions, references, roughs, lyrics, MIDI.</em></button><button type="button" data-upload-cat="stems"><i>02</i><strong>Stems</strong><span>Before mixing</span><em>Consolidated vocals, drums, music and instrument stems.</em></button><button type="button" data-upload-cat="mixes"><i>03</i><strong>Mix Versions</strong><span>During review</span><em>Mix V1, V2 and revisions for client review.</em></button><button type="button" data-upload-cat="masters"><i>04</i><strong>Masters</strong><span>After mix approval</span><em>Mastering versions before official release delivery.</em></button><button type="button" data-upload-cat="final"><i>05</i><strong>Final Delivery</strong><span>Release only</span><em>Approved canonical Final assets ready for delivery.</em></button></div><div class="tb-upload-guide-note"><b>RULE</b><span>Do not upload a work-in-progress file as Final Delivery. Final is reserved for the approved release asset.</span></div>';
      upload.parentNode.insertBefore(guide,upload);
      var style=document.createElement('style');
      style.textContent='.tb-upload-guide{margin:22px 0 0;padding:20px;border:1px solid rgba(216,180,90,.16);border-radius:18px;background:linear-gradient(145deg,rgba(216,180,90,.055),rgba(8,8,8,.96) 34%)}.tb-upload-guide-head{display:flex;justify-content:space-between;gap:20px;align-items:flex-start;margin-bottom:16px}.tb-upload-guide-head small{display:block;color:#8d784a;font-size:.52rem;letter-spacing:.16em}.tb-upload-guide-head strong{display:block;margin:5px 0;color:#ead9b4;font:700 1.05rem Cinzel,serif}.tb-upload-guide-head span{display:block;color:#81786b;font-size:.66rem;max-width:620px}.tb-upload-guide-head>b{padding:7px 10px;border:1px solid rgba(216,180,90,.18);border-radius:999px;color:#c6a653;font-size:.5rem;letter-spacing:.12em;white-space:nowrap}.tb-upload-steps{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:8px}.tb-upload-steps button{text-align:left;min-height:128px;padding:13px;border:1px solid rgba(255,255,255,.055);border-radius:13px;background:#0a0908;color:inherit;cursor:pointer;transition:.2s ease}.tb-upload-steps button:hover,.tb-upload-steps button.is-selected{transform:translateY(-2px);border-color:rgba(216,180,90,.42);background:rgba(216,180,90,.065)}.tb-upload-steps i{display:grid;place-items:center;width:28px;height:28px;border-radius:50%;background:rgba(216,180,90,.08);color:#d6b65d;font-style:normal;font-size:.55rem}.tb-upload-steps strong,.tb-upload-steps span,.tb-upload-steps em{display:block}.tb-upload-steps strong{margin-top:10px;color:#ded2bf;font-size:.68rem}.tb-upload-steps span{margin-top:3px;color:#d0ad53;font-size:.53rem;text-transform:uppercase;letter-spacing:.08em}.tb-upload-steps em{margin-top:8px;color:#746d63;font-size:.57rem;line-height:1.45;font-style:normal}.tb-upload-guide-note{display:flex;gap:10px;align-items:center;margin-top:10px;padding:10px 12px;border-left:2px solid #b99645;background:rgba(216,180,90,.035)}.tb-upload-guide-note b{color:#d3af55;font-size:.5rem;letter-spacing:.12em}.tb-upload-guide-note span{color:#81786b;font-size:.6rem}.tb-fm-upload{margin-top:10px!important}.tb-fm-upload:before{content:"UPLOAD TO SELECTED STAGE";grid-column:1/-1;color:#8d784a;font-size:.5rem;letter-spacing:.15em}@media(max-width:900px){.tb-upload-steps{grid-template-columns:repeat(2,minmax(0,1fr))}}@media(max-width:560px){.tb-upload-guide-head{display:block}.tb-upload-guide-head>b{display:inline-block;margin-top:10px}.tb-upload-steps{grid-template-columns:1fr}.tb-upload-steps button{min-height:auto}}';
      document.head.appendChild(style);
      var buttons=Array.prototype.slice.call(guide.querySelectorAll('[data-upload-cat]'));
      function sync(){buttons.forEach(function(b){b.classList.toggle('is-selected',b.dataset.uploadCat===select.value);});}
      buttons.forEach(function(b){b.addEventListener('click',function(){select.value=b.dataset.uploadCat;select.dispatchEvent(new Event('change',{bubbles:true}));sync();upload.scrollIntoView({behavior:'smooth',block:'center'});});});
      select.addEventListener('change',sync);sync();
      return true;
    }
    if(!enhanceFiles()){
      var tries=0,watch=setInterval(function(){tries++;if(enhanceFiles()||tries>20)clearInterval(watch);},150);
    }

    var initial=(location.hash||'#overview').slice(1);
    setTimeout(function(){activate(initial,false);},60);
  }
  if(document.readyState==='loading') document.addEventListener('DOMContentLoaded',function(){setTimeout(initClientWorkspace,180);});
  else setTimeout(initClientWorkspace,180);
})();
