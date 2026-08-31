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
      if(id==='review') return 'review';
      return id;
    }
    function activate(id,scroll){
      id=normalize(id||'overview');
      var target=document.getElementById(id);
      if(!target||!target.classList.contains('tb-workspace-section')){
        id='overview';target=document.getElementById(id);
      }
      panels=Array.prototype.slice.call(main.querySelectorAll('.tb-workspace-section'));
      panels.forEach(function(p){p.classList.toggle('is-active-panel',p.id===id);});
      Array.prototype.slice.call(nav.querySelectorAll('a[href^="#"]')).forEach(function(a){
        var href=normalize((a.getAttribute('href')||'').replace('#',''));
        a.classList.toggle('is-active',href===id);
      });
      if(history.replaceState) history.replaceState(null,'','#'+id);
      if(scroll&&target){var y=target.getBoundingClientRect().top+window.pageYOffset-145;window.scrollTo({top:y,behavior:'smooth'});}
    }

    nav.addEventListener('click',function(e){
      var a=e.target.closest('a[href^="#"]');
      if(!a) return;
      var id=(a.getAttribute('href')||'').slice(1);
      if(!id) return;
      e.preventDefault();activate(id,true);
    });

    document.addEventListener('click',function(e){
      var a=e.target.closest('a[href^="#"]');
      if(!a||nav.contains(a)) return;
      var id=(a.getAttribute('href')||'').slice(1);
      if(document.getElementById(normalize(id)) && document.getElementById(normalize(id)).classList.contains('tb-workspace-section')){
        e.preventDefault();activate(id,true);
      }
    });

    var initial=(location.hash||'#overview').slice(1);
    setTimeout(function(){activate(initial,false);},60);
  }
  if(document.readyState==='loading') document.addEventListener('DOMContentLoaded',function(){setTimeout(initClientWorkspace,180);});
  else setTimeout(initClientWorkspace,180);
})();
