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

    var initial=(location.hash||'#overview').slice(1);
    setTimeout(function(){activate(initial,false);},60);
  }
  if(document.readyState==='loading') document.addEventListener('DOMContentLoaded',function(){setTimeout(initClientWorkspace,180);});
  else setTimeout(initClientWorkspace,180);
})();
