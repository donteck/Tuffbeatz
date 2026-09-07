<?php get_header(); ?>
<main>
<section class="hero" id="home">
  <div class="hero-overlay"></div>
  <div class="container hero-grid">
    <div class="hero-logo-wrap">
      <img class="hero-logo" src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/tuff-beatz-logo.png'); ?>" alt="TUFF BEATZ emblem">
    </div>
    <div class="hero-person-wrap">
      <img class="hero-person" src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/emmanuel-tuffet.jpg'); ?>" alt="Emmanuel Tuffet">
    </div>
    <div class="hero-copy">
      <p class="eyebrow"><?php echo esc_html(tuff_beatz_editor_get('hero_eyebrow','THE PRODUCER IDENTITY OF')); ?></p>
      <h2 class="producer-name"><?php echo esc_html(tuff_beatz_editor_get('hero_name','EMMANUEL TUFFET')); ?></h2>
      <div class="wave-line">⌁⌁⌁⌁⌁</div>
      <?php $tb_hero_title=tuff_beatz_editor_get('hero_title','SOUND. PURPOSE. LEGACY.'); $tb_hero_parts=preg_split('/\s+/',trim($tb_hero_title)); $tb_hero_last=array_pop($tb_hero_parts); ?>
      <h1><?php echo esc_html(implode(' ',$tb_hero_parts)); ?><br><span><?php echo esc_html($tb_hero_last); ?></span></h1>
      <p class="lead"><?php echo esc_html(tuff_beatz_editor_get('hero_lead')); ?></p>
      <div class="hero-actions">
        <a class="btn btn-gold" href="<?php echo esc_url(tuff_beatz_get('showreel_url', '#music')); ?>">▶ <?php echo esc_html(tuff_beatz_editor_get('hero_primary_label','Play Showreel')); ?></a>
        <a class="btn btn-outline" href="<?php echo esc_url(tuff_beatz_editor_get('hero_secondary_url','#contact')); ?>"><?php echo esc_html(tuff_beatz_editor_get('hero_secondary_label','Work With Me')); ?></a>
      </div>
    </div>
  </div>
  <div class="genres">
    <span>AFROBEATS</span><b>•</b><span>KOMPA</span><b>•</b><span>ZOUK</span><b>•</b><span>HIP HOP</span><b>•</b><span>R&amp;B</span><b>•</b><span>AMAPIANO</span><b>•</b><span>DANCEHALL</span><b>•</b><span>AFRO FUSION</span>
  </div>
</section>

<?php if(tuff_beatz_editor_enabled('show_about')): ?>
<section class="section about" id="about">
  <div class="container two-col">
    <div>
      <p class="eyebrow"><?php echo esc_html(tuff_beatz_editor_get('about_eyebrow','ABOUT')); ?></p>
      <h2><?php echo esc_html(tuff_beatz_editor_get('about_title','MORE THAN BEATS. I BUILD EMOTIONS.')); ?></h2>
      <p>Multi-instrumentalist, producer, composer and creative technologist with a passion for sound that connects culture, people and purpose. From the studio to the stage, my mission is simple: to create authentic music with excellence and impact.</p>
      <div class="icon-grid">
        <div><span>♫</span><strong>MULTI-<br>INSTRUMENTALIST</strong></div>
        <div><span>⌁</span><strong>PRODUCER<br>&amp; COMPOSER</strong></div>
        <div><span>✦</span><strong>CREATIVE<br>TECHNOLOGIST</strong></div>
        <div><span>◎</span><strong>PURPOSE<br>DRIVEN</strong></div>
      </div>
      <div class="signature">Emmanuel Tuffet</div>
    </div>
    <div class="about-visual" id="studio">
      <div class="studio-card"><img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/tuff-beatz-logo.png'); ?>" alt="TUFF BEATZ logo"></div>
      <blockquote>“I don’t just make beats.<br>I craft the soundtrack of your story.<br>I build the sound. You leave the legacy.”<cite>— Tuff Beatz</cite></blockquote>
    </div>
  </div>
</section>
<?php endif; ?>

<?php if(tuff_beatz_editor_enabled('show_services')): ?>
<section class="section services" id="services">
  <div class="container">
    <p class="eyebrow centered"><?php echo esc_html(tuff_beatz_editor_get('services_eyebrow','WHAT I DO')); ?></p>
    <h2 class="centered"><?php echo esc_html(tuff_beatz_editor_get('services_title','PROFESSIONAL MUSIC PRODUCTION')); ?></h2>
    <div class="service-grid">
      <article><span>🎧</span><h3>Beat Production</h3><p>Custom beats for artists and creators.</p><a href="#contact">Book Production →</a></article>
      <article><span>🎙</span><h3>Recording</h3><p>Professional recording with industry gear.</p><a href="#contact">Book Recording →</a></article>
      <article><span>⌁</span><h3>Mixing &amp; Mastering</h3><p>Radio-ready sound with industry standard.</p><a href="#contact">Book Mix →</a></article>
      <article><span>🎹</span><h3>Composition &amp; Arrangement</h3><p>Melodies, harmonies and arrangements that elevate your record.</p><a href="#contact">Start Composition →</a></article>
      <article><span>🎸</span><h3>Live Instruments</h3><p>Guitars, bass, keys, percussions and more for authentic vibes.</p><a href="#contact">Add Musicians →</a></article>
      <article><span>〽</span><h3>Sound Design</h3><p>Unique textures and sounds to make your record stand out.</p><a href="#contact">Design My Sound →</a></article>
    </div>
  </div>
</section>
<?php endif; ?>

<?php if(tuff_beatz_editor_enabled('show_featured')): ?>
<section class="tb-featured section" id="featured-release">
  <div class="container tb-featured__grid">
    <div class="tb-featured__visual">
      <div class="tb-featured__ring"></div>
      <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/tuff-beatz-logo.png'); ?>" alt="TUFF BEATZ">
      <span>NEW / FEATURED</span>
    </div>
    <div class="tb-featured__copy">
      <p class="eyebrow">VISIONARY SOUND / SIGNATURE PRODUCTION</p>
      <h2>THE MUSIC DOESN’T JUST PLAY.<br><span>IT MOVES.</span></h2>
      <p>A more immersive way to experience TUFF BEATZ: release artwork, instant playback, persistent listening while browsing, and a full-screen queue built directly into the site.</p>
      <div class="tb-featured__stats">
        <div><b>01</b><span>PLAY ANYWHERE</span></div><div><b>02</b><span>STICKY PLAYER</span></div><div><b>03</b><span>FULL QUEUE</span></div>
      </div>
      <a href="#music" class="btn btn-gold">Explore the Music</a>
    </div>
  </div>
</section>
<?php endif; ?>

<?php if(tuff_beatz_editor_enabled('show_music')): ?>
<section class="section music" id="music">
  <div class="container">
    <div class="music-heading">
      <div><p class="eyebrow"><?php echo esc_html(tuff_beatz_editor_get('music_eyebrow','MUSIC THAT SPEAKS')); ?></p><h2><?php echo esc_html(tuff_beatz_editor_get('music_title','RECENT WORK')); ?></h2></div>
      <a class="btn btn-gold" href="<?php echo esc_url(tuff_beatz_get('spotify_url', '#music')); ?>" target="_blank" rel="noopener">Listen on All Platforms</a>
    </div>
    <div class="project-grid">
    <?php $q = new WP_Query(array('post_type' => 'tb_project','posts_per_page' => 6));
      if ($q->have_posts()): while ($q->have_posts()): $q->the_post();
        $tb_audio = get_post_meta(get_the_ID(), '_tb_audio_url', true);
        $tb_artist = get_post_meta(get_the_ID(), '_tb_artist_name', true) ?: 'TUFF BEATZ'; ?>
          <article class="project-card tb-release-card">
            <button class="tb-card-play <?php echo $tb_audio ? 'js-tb-play' : 'js-tb-noaudio'; ?>" data-track-id="<?php echo esc_attr(get_the_ID()); ?>" aria-label="Play <?php the_title_attribute(); ?>">
              <span class="tb-release-card__art">
                <?php if (has_post_thumbnail()): the_post_thumbnail('medium_large'); else: ?><span class="project-placeholder"><?php echo esc_html(mb_substr(get_the_title(),0,1)); ?></span><?php endif; ?>
                <span class="tb-release-card__shade"></span><span class="play">▶</span><span class="tb-release-card__tag">PLAY</span>
              </span>
            </button>
            <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3><small><?php echo esc_html($tb_artist); ?></small>
          </article>
        <?php endwhile; wp_reset_postdata(); else:
        $defaults = array('POU LANMOU','DOOM DA DE','MOVE','KITE’M ALE','LOVE AGAIN','ENERGY'); foreach ($defaults as $title): ?>
          <article class="project-card tb-release-card"><a href="#contact" class="tb-release-card__art"><div class="project-placeholder"><?php echo esc_html(mb_substr($title,0,1)); ?></div><span class="tb-release-card__shade"></span><span class="play">▶</span><span class="tb-release-card__tag">ADD AUDIO</span></a><h3><?php echo esc_html($title); ?></h3><small>Sean Davz</small></article>
        <?php endforeach; endif; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php if(tuff_beatz_editor_enabled('show_platforms')): ?>
<section class="platforms" id="credits">
  <div class="container"><p>TRUSTED BY ARTISTS. STREAMED WORLDWIDE.</p><div class="platform-links">
    <a href="<?php echo esc_url(tuff_beatz_get('spotify_url', '#music')); ?>" target="_blank" rel="noopener">Spotify</a>
    <a href="<?php echo esc_url(tuff_beatz_get('apple_music_url', '#music')); ?>" target="_blank" rel="noopener"> MUSIC</a>
    <a href="<?php echo esc_url(tuff_beatz_get('youtube_url', '#music')); ?>" target="_blank" rel="noopener">YouTube</a>
    <a href="<?php echo esc_url(tuff_beatz_get('tidal_url', '#music')); ?>" target="_blank" rel="noopener">TIDAL</a>
    <a href="<?php echo esc_url(tuff_beatz_get('deezer_url', '#music')); ?>" target="_blank" rel="noopener">deezer</a>
    <a href="<?php echo esc_url(tuff_beatz_get('audiomack_url', '#music')); ?>" target="_blank" rel="noopener">audiomack</a>
  </div></div>
</section>
<?php endif; ?>
</main>
<?php get_footer(); ?>