<?php /* Generates the page tree, skips drafted pages */
require_once(get_template_directory() . '/library/helsingborg-walker.php');
$walker_page = new Helsingborg_Walker(); ?>
<aside class="left-off-canvas-menu">
  <ul class="mobile-nav-list" role="navigation">
    <li class="nav-home"><a href="<?php echo get_site_url();?>">Startsida</a></li>
    <?php
      $id = is_404() ? get_option('page_on_front') : $post->ID;
      $menu = wp_cache_get('menu_' . $id);
      if ( false === $menu ) {
        $menu = wp_list_pages( array('title_li' => '',
                                     'echo' => 0,
                                     'walker' => $walker_page,
                                     'include' => get_included_pages($post) ));
        wp_cache_set('menu_' . $id , $menu);
      }
      echo $menu;
      ?>
  </ul>
  <div class="mobile-menu-top-bar">
    <?php Helsingborg_support_menu(true); ?>
  </div>
</aside>
