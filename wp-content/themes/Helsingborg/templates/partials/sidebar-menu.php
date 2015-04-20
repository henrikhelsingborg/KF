<?php /* Generates the page tree, skips drafted pages */
require_once(get_template_directory() . '/library/helsingborg-walker.php');
$walker_page = new Helsingborg_Walker(); ?>
<nav class="main-nav large-12 columns show-for-medium-up">
  <ul class="main-nav-list">
    <li class="nav-home"><a href="<?php echo get_site_url();?>">Startsida</a></li>
    <?php
    $menu = wp_cache_get('menu_' . $post->ID);
    if ( false === $menu ) {
      $menu = wp_list_pages( array('title_li' => '',
                                   'echo' => 0,
                                   'walker' => $walker_page,
                                   'include' => get_included_pages($post) ));
      wp_cache_set('menu_' . $post->ID , $menu);
    }
    echo $menu; ?>
  </ul>
</nav>
