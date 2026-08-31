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
<?php $tb_is_os = function_exists('tuff_beatz_is_studio_os_page') && tuff_beatz_is_studio_os_page(); ?>
<header class="site-header<?php echo $tb_is_os?' tb-os-header':''; ?>" id="top">
  <div class="container nav-wrap">
    <a class="brand" href="<?php echo esc_url($tb_is_os && is_user_logged_in() && function_exists('tuff_beatz_portal_account_url') ? tuff_beatz_portal_account_url() : home_url('/')); ?>" aria-label="TUFF BEATZ home">
      <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/tuff-beatz-logo.png'); ?>" alt="TUFF BEATZ logo">
      <span><strong>TUFF BEATZ</strong><small><?php echo $tb_is_os?'PRIVATE STUDIO OS':'BY EMMANUEL TUFFET'; ?></small></span>
    </a>
    <button class="menu-toggle" aria-label="Toggle navigation" aria-expanded="false">☰</button>
    <?php if($tb_is_os && is_user_logged_in()): $tb_producer=function_exists('tuff_beatz_is_producer_user')&&tuff_beatz_is_producer_user(); ?>
      <nav class="main-nav tb-os-main-nav" aria-label="Studio OS navigation">
        <?php if($tb_producer): ?>
          <a href="<?php echo esc_url(home_url('/producer-portal/')); ?>">Command</a>
          <a href="<?php echo esc_url(home_url('/producer-crm/')); ?>">Clients</a>
          <a href="<?php echo esc_url(home_url('/start-a-project/')); ?>">New Intake</a>
          <?php if(is_page('project-dashboard')): ?><a href="#overview">Workspace</a><?php endif; ?>
        <?php else: ?>
          <a href="<?php echo esc_url(home_url('/client-portal/')); ?>">My Projects</a>
          <a href="<?php echo esc_url(home_url('/start-a-project/')); ?>">Start Project</a>
          <?php if(is_page('project-dashboard')): ?><a href="#overview">Workspace</a><?php endif; ?>
        <?php endif; ?>
      </nav>
      <div class="tb-os-account"><span class="tb-os-live-dot"></span><small>SECURE SESSION</small><a href="<?php echo esc_url(wp_logout_url(home_url('/'))); ?>">Sign Out</a></div>
    <?php else: ?>
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
      <?php if(is_user_logged_in() && function_exists('tuff_beatz_portal_account_url')): ?>
        <a class="btn btn-outline header-cta" href="<?php echo esc_url(tuff_beatz_portal_account_url()); ?>">My Portal</a>
      <?php else: ?>
        <a class="btn btn-outline header-cta" href="<?php echo esc_url(home_url('/start-a-project/')); ?>">Work With Me</a>
      <?php endif; ?>
    <?php endif; ?>
  </div>
</header>
