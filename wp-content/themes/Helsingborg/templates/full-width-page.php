<?php
/*
Template Name: Fullbredd
*/
get_header();

// Get the content, see if <!--more--> is inserted
$the_content = get_extended($post->post_content);
$main = $the_content['main'];
$content = $the_content['extended']; // If content is empty, no <!--more--> tag was used -> content is in $main
?>

<div class="full-width-page-layout row">
    <!-- main-page-layout -->
        <div class="main-area large-12 columns">

        <div class="main-content row">

            <div class="large-12 medium-12 columns">

              <div class="alert row"></div>
              <?php get_template_part('templates/partials/header','image'); ?>

                <div class="row no-image">
                </div><!-- /.row -->

                <?php the_breadcrumb(); ?>

                <?php /* Start loop */ ?>
                <?php while (have_posts()) : the_post(); ?>
                  <article class="article" id="article">
                    <header>
                      <?php get_template_part('templates/partials/accessability','menu'); ?>

                      <h1 class="article-title"><?php the_title(); ?></h1>
                    </header>
                    <?php if (!empty($content)) : ?>
                      <div class="ingress">
                        <?php echo apply_filters('the_content', $main); ?>
                      </div><!-- /.ingress -->
                    <?php endif; ?>
                    <div class="article-body">
                      <?php if(!empty($content)){
                        echo apply_filters('the_content', $content);
                      } else {
                        echo apply_filters('the_content', $main);
                      } ?>
                    </div>
                    <footer>
                      <ul class="socialmedia-list">
                          <li class="fbook"><a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(get_the_permalink()); ?>">Facebook</a></li>
                          <li class="twitter"><a href="http://twitter.com/share?text=<?php echo strip_tags(get_the_excerpt()); ?>&amp;url=<?php echo urlencode(wp_get_shortlink()); ?>">Twitter</a></li>
                      </ul>
                    </footer>
                  </article>
                <?php endwhile; // End the loop ?>

                <?php if ( (is_active_sidebar('content-area') == TRUE) ) : ?>
                  <?php dynamic_sidebar("content-area"); ?>
                <?php endif; ?>

            <!-- END LIST + BLOCK puffs :-) -->
        </div><!-- /.columns -->
    </div><!-- /.main-content -->
    </div>  <!-- /.main-area -->
</div><!-- /.article-page-layout -->
</div><!-- /.main-site-container -->


<?php get_footer(); ?>
