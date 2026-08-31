<footer class="site-footer" id="contact">
  <div class="container footer-grid">
    <div class="footer-brand">
      <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/tuff-beatz-logo.png'); ?>" alt="TUFF BEATZ logo">
      <div><strong>TUFF BEATZ</strong><small>BY EMMANUEL TUFFET</small></div>
    </div>
    <div class="footer-contact">
      <p>Ready to build the sound?</p>
      <a class="btn btn-gold" href="<?php echo esc_url(home_url('/start-a-project/')); ?>">Start a Project</a>
    </div>
    <div class="social-links">
      <a href="<?php echo esc_url(tuff_beatz_get('instagram_url', '#contact')); ?>" target="_blank" rel="noopener">Instagram</a>
      <a href="<?php echo esc_url(tuff_beatz_get('youtube_url', '#music')); ?>" target="_blank" rel="noopener">YouTube</a>
      <a href="<?php echo esc_url(tuff_beatz_get('facebook_url', '#contact')); ?>" target="_blank" rel="noopener">Facebook</a>
      <a href="<?php echo esc_url(tuff_beatz_get('tiktok_url', '#contact')); ?>" target="_blank" rel="noopener">TikTok</a>
    </div>
  </div>
  <div class="container footer-bottom">
    <span>BUILD THE SOUND. FEEL THE POWER. LEAVE THE LEGACY.</span>
    <span>© <?php echo esc_html(date('Y')); ?> TUFF BEATZ. ALL RIGHTS RESERVED.</span>
  </div>
</footer>

<button class="tb34-launcher" id="tb34Launcher" type="button" aria-label="Open music player"><span id="tb34LauncherIcon">▶</span></button>

<section class="tb34-now" id="tb34Now" aria-hidden="true">
  <button class="tb34-now-close" id="tb34NowClose" type="button" aria-label="Close Now Playing">×</button>
  <div class="tb34-now-inner">
    <div class="tb34-art-large"><img id="tb34NowCover" alt="" hidden><span id="tb34NowFallback">TB</span></div>
    <div class="tb34-now-copy">
      <div class="tb34-eyebrow">NOW PLAYING · TUFF BEATZ</div>
      <h2 id="tb34NowTitle">TUFF BEATZ</h2>
      <div class="tb34-now-artist" id="tb34NowArtist">SOUND. PURPOSE. LEGACY.</div>
      <div class="tb34-badges"><span id="tb34Genre">PRODUCTION</span><span id="tb34Bpm">— BPM</span><span id="tb34Key">— KEY</span><span>PRODUCED BY TUFF BEATZ</span></div>
      <div class="tb34-now-wave" aria-hidden="true"><?php for($i=0;$i<70;$i++): ?><i></i><?php endfor; ?></div>
      <div class="tb34-now-buttons"><button class="tb34-btn gold" id="tb34NowPlay" type="button">▶ PLAY</button><button class="tb34-btn outline" id="tb34OpenQueueFromNow" type="button">OPEN QUEUE</button><a class="tb34-btn outline" id="tb34NowStream" href="#" target="_blank" rel="noopener" hidden>LISTEN</a></div>
    </div>
  </div>
</section>

<aside class="tb34-queue" id="tb34Queue" aria-hidden="true">
  <div class="tb34-queue-head"><div><span class="tb34-eyebrow">NOW PLAYING / TUFF BEATZ</span><strong>Music Player</strong></div><div class="tb34-queue-tagline">SOUND. PURPOSE. LEGACY.</div><button id="tb34QueueClose" type="button" aria-label="Close queue">×</button></div>
  <div class="tb34-queue-brand">
    <div class="tb34-queue-logo"><img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/tuff-beatz-logo.png'); ?>" alt="TUFF BEATZ logo"></div>
    <div class="tb34-queue-copy"><div class="tb34-queue-kicker">THE PRODUCER IDENTITY OF EMMANUEL TUFFET</div><h3>TUFF <span>BEATZ</span></h3><p>IMMERSIVE RELEASES · FULL PLAYLIST · PREMIUM PRODUCER EXPERIENCE</p></div>
  </div>
  <div class="tb34-queue-list" id="tb34QueueList"></div>
</aside>

<div class="tb34-player" id="tb34Player" aria-hidden="true">
  <div class="tb34-player-inner">
    <button class="tb34-cover" id="tb34Cover" type="button" aria-label="Open Now Playing"><img id="tb34CoverImg" alt="" hidden><span id="tb34CoverFallback">TB</span></button>
    <div class="tb34-meta"><strong id="tb34Title">TUFF BEATZ</strong><span id="tb34Artist">Select a track</span></div>
    <div class="tb34-controls"><button id="tb34Prev" type="button">⏮</button><button class="tb34-main-play" id="tb34Play" type="button">▶</button><button id="tb34Next" type="button">⏭</button></div>
    <div class="tb34-timeline"><span class="tb34-time" id="tb34Current">0:00</span><div class="tb34-waveform" id="tb34Waveform"><?php for($i=0;$i<78;$i++): ?><i></i><?php endfor; ?><div class="tb34-wave-progress" id="tb34WaveProgress"><div class="tb34-wave-clone"><?php for($i=0;$i<78;$i++): ?><i></i><?php endfor; ?></div></div></div><span class="tb34-time" id="tb34Duration">0:00</span></div>
    <div class="tb34-actions"><span>VOL</span><input id="tb34Volume" type="range" min="0" max="1" step="0.01" value="0.85"></div>
    <div class="tb34-player-right"><a id="tb34Stream" href="#" target="_blank" rel="noopener" hidden>LISTEN</a><button class="tb34-queue-btn" id="tb34QueueBtn" type="button">QUEUE</button><button class="tb34-close" id="tb34Close" type="button">×</button></div>
  </div>
</div>
<audio id="tb34Audio" preload="metadata"></audio>

<?php wp_footer(); ?>
</body>
</html>
