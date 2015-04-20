<?php

/**
 * Register Menus
 * http://codex.wordpress.org/Function_Reference/register_nav_menus#Examples
 */
register_nav_menus(array(
    'sidebar-menu' => 'Huvudmeny',
    'support-menu' => 'Hjälpmeny',
    'mobile-menu' => 'Mobilmeny'
));


if ( ! function_exists( 'Helsingborg_sidebar_menu' ) ) {
  function Helsingborg_sidebar_menu() {
    if (has_nav_menu('sidebar-menu')){
      wp_nav_menu(array(
          'theme_location'  => 'sidebar-menu' ,
          'container'       => 'nav',
          'container_class' => 'main-nav large-12 columns show-for-medium-up',
          'container_id'    => '',
          'menu_class'      => 'main-nav-list',
          'menu_id'         => '',
          'echo'            => true,
          'fallback_cb'     => 'wp_page_menu',
          'before'          => '',
          'after'           => '',
          'link_before'     => '',
          'link_after'      => '',
          'items_wrap'      => '<ul class="main-nav-list">%3$s</ul>',
          'depth'           => 5,
          'walker'          => new sidebar_menu_walker()
        ));
    }
  }
}

if ( ! function_exists( 'Helsingborg_support_menu' ) ) {
  function Helsingborg_support_menu($mobile_menu = false) {
    if (has_nav_menu('support-menu')){
      $args = array(
        'theme_location'  => 'support-menu' ,
        'container'       => 'div',
        'container_id'    => '',
        'depth'           => 0,
        'echo'            => true,
        'walker'          => new support_menu_walker()
      );

      if ($mobile_menu) {
        $args['container_class'] =  'support-nav-mobile';
        $args['menu_id'] = 'support-nav-mobile';
        $args['menu_class'] = 'support-nav-mobile-list';
      } else {
        add_filter( 'wp_nav_menu_items', 'Helsingborg_support_menu_add_translate' );
        $args['container_class'] =  'support-nav large-8 medium-8 columns';
        $args['menu_id'] = 'support-nav';
        $args['menu_class'] = 'support-nav-list inline-list';
      }
      wp_nav_menu($args);
    }
  }

  // Adds the "translate" menu item to the support menu
  function Helsingborg_support_menu_add_translate($items) {
    $items .=   '<li>
                    <a href="#" class="google-translate-toggle">Translate</a>
                    <div id="google-translate-element"></div>
                </li>';
    return $items;
  }
}

if ( ! function_exists( 'Helsingborg_mobile_menu' ) ) {
  function Helsingborg_mobile_menu() {
    if (has_nav_menu('mobile-menu')){
      wp_nav_menu(array(
          'theme_location'  => 'mobile-menu' ,
          'container'       => false,
          'container_id'    => '',
          'menu_class'      => 'mobile-nav-list',
          'menu_id'         => 'mobile-nav',
          'depth'           => 0,
          'echo'            => true,
          'walker'          => new sidebar_menu_walker()
        ));
    }
  }
}

/**
 * Left top bar
 * http://codex.wordpress.org/Function_Reference/wp_nav_menu
 */
if ( ! function_exists( 'Helsingborg_top_bar_l' ) ) {
  function Helsingborg_top_bar_l() {
      wp_nav_menu(array(
          'container' => false,                           // remove nav container
          'container_class' => '',                        // class of container
          'menu' => '',                                   // menu name
          'menu_class' => 'top-bar-menu left',            // adding custom nav class
          'theme_location' => 'top-bar-l',                // where it's located in the theme
          'before' => '',                                 // before each link <a>
          'after' => '',                                  // after each link </a>
          'link_before' => '',                            // before each link text
          'link_after' => '',                             // after each link text
          'depth' => 5,                                   // limit the depth of the nav
          'fallback_cb' => false,                         // fallback function (see below)
          'walker' => new top_bar_walker()
      ));
  }
}

/**
 * Right top bar
 */
if ( ! function_exists( 'Helsingborg_top_bar_r' ) ) {
  function Helsingborg_top_bar_r() {
      wp_nav_menu(array(
          'container' => false,                           // remove nav container
          'container_class' => '',                        // class of container
          'menu' => '',                                   // menu name
          'menu_class' => 'top-bar-menu right',           // adding custom nav class
          'theme_location' => 'top-bar-r',                // where it's located in the theme
          'before' => '',                                 // before each link <a>
          'after' => '',                                  // after each link </a>
          'link_before' => '',                            // before each link text
          'link_after' => '',                             // after each link text
          'depth' => 5,                                   // limit the depth of the nav
          'fallback_cb' => false,                         // fallback function (see below)
          'walker' => new top_bar_walker()
      ));
  }
}

/**
 * Mobile off-canvas
 */
if ( ! function_exists( 'Helsingborg_mobile_off_canvas' ) ) {
  function Helsingborg_mobile_off_canvas() {
      wp_nav_menu(array(
          'container' => false,                           // remove nav container
          'container_class' => '',                        // class of container
          'menu' => '',                                   // menu name
          'menu_class' => 'off-canvas-list',              // adding custom nav class
          'theme_location' => 'mobile-off-canvas',        // where it's located in the theme
          'before' => '',                                 // before each link <a>
          'after' => '',                                  // after each link </a>
          'link_before' => '',                            // before each link text
          'link_after' => '',                             // after each link text
          'depth' => 5,                                   // limit the depth of the nav
          'fallback_cb' => false,                         // fallback function (see below)
          'walker' => new top_bar_walker()
      ));
  }
}

/**
 * Add support for buttons in the top-bar menu:
 * 1) In WordPress admin, go to Apperance -> Menus.
 * 2) Click 'Screen Options' from the top panel and enable 'CSS CLasses' and 'Link Relationship (XFN)'
 * 3) On your menu item, type 'has-form' in the CSS-classes field. Type 'button' in the XFN field
 * 4) Save Menu. Your menu item will now appear as a button in your top-menu
*/
if ( ! function_exists( 'add_menuclass') ) {
  function add_menuclass($ulclass) {
      $find = array('/<a rel="button"/', '/<a title=".*?" rel="button"/');
      $replace = array('<a rel="button" class="button"', '<a rel="button" class="button"');

      return preg_replace($find, $replace, $ulclass, 1);
  }
  add_filter('wp_nav_menu','add_menuclass');
}

?>
