<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
    <?php $tb_v34_restore = get_template_directory() . '/assets/css/v34-frontend-restore.css'; ?>
    <link rel="stylesheet" href="<?php echo esc_url(get_template_directory_uri() . '/assets/css/v34-frontend-restore.css?v=' . (file_exists($tb_v34_restore) ? filemtime($tb_v34_restore) : '3.4')); ?>">
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
      <?php $tb_home_prefix = is_front_page() ? '' : home_url('/'); ?>
      <a href="<?php echo esc_url($tb_home_prefix . '#home'); ?>">Home</a>
      <a href="<?php echo esc_url($tb_home_prefix . '#about'); ?>">About</a>
      <a href="<?php echo esc_url($tb_home_prefix . '#music'); ?>">Music</a>
      <a href="<?php echo esc_url($tb_home_prefix . '#services'); ?>">Services</a>
      <a href="<?php echo esc_url($tb_home_prefix . '#credits'); ?>">Credits</a>
      <a href="<?php echo esc_url($tb_home_prefix . '#studio'); ?>">Studio</a>
      <a href="<?php echo esc_url($tb_home_prefix . '#contact'); ?>">Contact</a>
    </nav>
    <a class="btn btn-outline header-cta" href="<?php echo esc_url(home_url('/start-a-project/')); ?>">Work With Me</a>
  </div>
</header>
