<footer class="site-footer" id="contact">
  <div class="container footer-grid">
    <div class="footer-brand">
      <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/tuff-beatz-logo.jpg'); ?>" alt="TUFF BEATZ logo">
      <div><strong>TUFF BEATZ</strong><small>BY EMMANUEL TUFFET</small></div>
    </div>
    <div class="footer-contact">
      <p>Ready to build the sound?</p>
      <a class="btn btn-gold" href="mailto:<?php echo esc_attr(tuff_beatz_get('contact_email', get_option('admin_email'))); ?>">Start a Project</a>
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

<div class="tb-player" id="tbPlayer" aria-label="TUFF BEATZ music player">
  <audio id="tbAudio" preload="metadata"></audio>
  <div class="tb-player__progress-wrap" id="tbProgressWrap" aria-label="Seek">
    <div class="tb-player__wave" aria-hidden="true">
      <i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i>
      <i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i>
      <i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i>
      <i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i>
    </div>
    <div class="tb-player__progress" id="tbProgress"></div>
  </div>
  <div class="tb-player__inner">
    <button class="tb-player__cover-btn" id="tbPlaylistToggle" aria-label="Open playlist">
      <img id="tbPlayerCover" src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/tuff-beatz-logo.jpg'); ?>" alt="">
      <span class="tb-player__cover-overlay">☰</span>
    </button>
    <div class="tb-player__meta"><strong id="tbPlayerTitle">TUFF BEATZ</strong><span id="tbPlayerArtist">Select a track</span></div>
    <div class="tb-player__transport"><button id="tbPrev" aria-label="Previous track">◀◀</button><button class="tb-player__mainplay" id="tbPlay" aria-label="Play">▶</button><button id="tbNext" aria-label="Next track">▶▶</button></div>
    <div class="tb-player__time"><span id="tbCurrent">0:00</span><b>/</b><span id="tbDuration">0:00</span></div>
    <div class="tb-player__volume"><span>◖</span><input id="tbVolume" type="range" min="0" max="1" step="0.01" value="0.85" aria-label="Volume"></div>
    <a class="tb-player__link" id="tbStream" href="#music">LISTEN ↗</a>
    <button class="tb-player__queue" id="tbPlaylistToggle2" aria-label="Open queue">QUEUE <span id="tbQueueCount">0</span></button>
  </div>
</div>

<div class="tb-playlist-drawer" id="tbPlaylistDrawer" aria-hidden="true">
  <div class="tb-playlist-drawer__backdrop" id="tbPlaylistBackdrop"></div>
  <section class="tb-playlist-drawer__panel" aria-label="TUFF BEATZ playlist">
    <header><div><p>NOW PLAYING / TUFF BEATZ</p><h2>MUSIC PLAYER</h2></div><button id="tbPlaylistClose" aria-label="Close playlist">×</button></header>
    <div class="tb-playlist-drawer__hero">
      <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/tuff-beatz-logo.jpg'); ?>" alt="">
      <div><span>THE PRODUCER IDENTITY OF</span><strong>EMMANUEL TUFFET</strong><small>SOUND. PURPOSE. LEGACY.</small></div>
    </div>
    <div class="tb-playlist-list" id="tbPlaylistList"></div>
  </section>
</div>

<?php wp_footer(); ?>
</body>
</html>
