<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<header class="site-header" id="top">
  <div class="container nav-wrap">
    <a class="brand" href="<?php echo esc_url(home_url('/')); ?>" aria-label="TUFF BEATZ home">
      <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/tuff-beatz-logo.png'); ?>" alt="TUFF BEATZ logo">
      <span><strong>TUFF BEATZ</strong><small>BY EMMANUEL TUFFET</small></span>
    </a>
    <button class="menu-toggle" aria-label="Toggle navigation" aria-expanded="false">☰</button>
    <nav class="main-nav" aria-label="Main navigation">
      <a href="<?php echo esc_url(home_url('/#home')); ?>">Home</a>
      <a href="<?php echo esc_url(home_url('/#about')); ?>">About</a>
      <a href="<?php echo esc_url(home_url('/#music')); ?>">Music</a>
      <a href="<?php echo esc_url(home_url('/#services')); ?>">Services</a>
      <a href="<?php echo esc_url(home_url('/#credits')); ?>">Credits</a>
      <a href="<?php echo esc_url(home_url('/#studio')); ?>">Studio</a>
      <a href="<?php echo esc_url(home_url('/#contact')); ?>">Contact</a>
    </nav>
    <a class="btn btn-outline header-cta" href="<?php echo esc_url(home_url('/start-a-project/')); ?>">Work With Me</a>
  </div>
</header>
