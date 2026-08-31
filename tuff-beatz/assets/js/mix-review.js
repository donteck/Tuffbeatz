document.addEventListener('DOMContentLoaded',function(){
  function fmt(sec){sec=Math.max(0,Math.floor(sec||0));return Math.floor(sec/60)+':'+String(sec%60).padStart(2,'0');}
  document.querySelectorAll('[data-tb-review]').forEach(function(box,index){
    var audio=box.querySelector('audio'),time=box.querySelector('[data-review-time]'),field=box.querySelector('input[name="timestamp"]'),mark=box.querySelector('[data-mark-time]');
    if(!audio)return;
    box.classList.add('tb-review-console');
    var nativeWrap=document.createElement('div');nativeWrap.className='tb-review-transport';
    var play=document.createElement('button');play.type='button';play.className='tb-review-play';play.setAttribute('aria-label','Play or pause mix');play.innerHTML='▶';
    var rail=document.createElement('button');rail.type='button';rail.className='tb-review-wave';rail.setAttribute('aria-label','Seek through mix');
    var bars='';for(var i=0;i<72;i++){var h=20+((i*17)%43);bars+='<i style="height:'+h+'%"></i>';}rail.innerHTML='<span class="tb-review-wave-bars">'+bars+'</span><span class="tb-review-wave-progress"></span>';
    var clock=document.createElement('div');clock.className='tb-review-transport-time';clock.innerHTML='<strong>0:00</strong><span> / --:--</span>';
    nativeWrap.appendChild(play);nativeWrap.appendChild(rail);nativeWrap.appendChild(clock);audio.insertAdjacentElement('afterend',nativeWrap);audio.classList.add('tb-review-native-audio');
    var progress=rail.querySelector('.tb-review-wave-progress');
    function sync(){var cur=audio.currentTime||0,dur=isFinite(audio.duration)?audio.duration:0,p=dur?cur/dur*100:0;progress.style.width=p+'%';clock.innerHTML='<strong>'+fmt(cur)+'</strong><span> / '+(dur?fmt(dur):'--:--')+'</span>';if(time)time.textContent=fmt(cur);play.innerHTML=audio.paused?'▶':'Ⅱ';}
    audio.addEventListener('timeupdate',sync);audio.addEventListener('loadedmetadata',sync);audio.addEventListener('play',sync);audio.addEventListener('pause',sync);audio.addEventListener('ended',sync);
    play.addEventListener('click',function(){if(audio.paused)audio.play();else audio.pause();});
    rail.addEventListener('click',function(e){if(!isFinite(audio.duration)||!audio.duration)return;var r=rail.getBoundingClientRect();audio.currentTime=Math.max(0,Math.min(audio.duration,(e.clientX-r.left)/r.width*audio.duration));sync();});
    if(mark)mark.addEventListener('click',function(){if(field)field.value=Math.max(0,Math.floor(audio.currentTime||0));if(time)time.textContent=fmt(audio.currentTime||0);box.classList.add('tb-review-marked');setTimeout(function(){box.classList.remove('tb-review-marked');},700);});
    sync();
  });
  document.querySelectorAll('[data-jump-time]').forEach(function(btn){btn.addEventListener('click',function(){var id=btn.getAttribute('data-player'),audio=document.getElementById(id);if(!audio)return;audio.currentTime=parseInt(btn.getAttribute('data-jump-time')||'0',10);audio.play();});});
});