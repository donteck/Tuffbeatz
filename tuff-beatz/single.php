<?php get_header(); ?>
<main class="section" style="padding-top:140px">
  <div class="container single-project">
    <?php while (have_posts()): the_post(); ?>
      <p class="eyebrow">TUFF BEATZ PROJECT</p><h1><?php the_title(); ?></h1>
      <?php if (has_post_thumbnail()) the_post_thumbnail('large', array('class'=>'single-cover')); ?>
      <div class="entry-content"><?php the_content(); ?></div>
      <a class="btn btn-gold" href="#contact">Work With TUFF BEATZ</a>
    <?php endwhile; ?>
  </div>
</main>
<?php get_footer(); ?>
