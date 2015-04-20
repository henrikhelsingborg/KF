<?php
/*
 * NOTE: This is just the Unfiltered MU plugins code added here!
 * Can be found at: http://wordpress.org/extend/plugins/unfiltered-mu/.
 * This is to keep down the amount of plugins used on current site/sites.
 */
function um_kses_init() {
  if ( current_user_can( 'unfiltered_html' ) )
  kses_remove_filters();
}

add_action( 'init', 'um_kses_init', 11 );
add_action( 'set_current_user', 'um_kses_init', 11 );


/*
If you install this plugin in wp-content/plugins, the following code
will add the cap on plugin activation, and remove it on deactivation.
It will be a per-blog setting (the plugin will need to be activated on
each blog you want the unfiltered_html cap).
*/

function um_unfilter_roles() {
  // Makes sure $wp_roles is initialized
  get_role( 'administrator' );

  global $wp_roles;
  // Dont use get_role() wrapper, it doesn't work as a one off.
  // (get_role does not properly return as reference)
  $wp_roles->role_objects['administrator']->add_cap( 'unfiltered_html' );
  $wp_roles->role_objects['editor']->add_cap( 'unfiltered_html' );
}

function um_refilter_roles() {
  get_role( 'administrator' );
  global $wp_roles;
  // Could use the get_role() wrapper here since this function is never
  // called as a one off.  It is always called to alter the role as
  // stored in the DB.
  $wp_roles->role_objects['administrator']->remove_cap( 'unfiltered_html' );
  $wp_roles->role_objects['editor']->remove_cap( 'unfiltered_html' );
}

register_activation_hook( __FILE__, 'um_unfilter_roles' );   // Add on activate
register_deactivation_hook( __FILE__, 'um_refilter_roles' ); // Remove on deactivate

/*
If you install this plugin in wp-content/mu-plugins, the following code
will add give all admins and all editors on every blog the
unfiltered_html cap.  Deleting this plugin will remove the cap.
*/

function um_unfilter_roles_one_time() {
  get_role( 'administrator' );

  global $wp_roles, $current_user;

  $use_db = $wp_roles->use_db;
  $wp_roles->use_db = false; // Don't store in db.  Just do a one off mod to the role.
  um_unfilter_roles(); // Add caps for this page load only: - ^^^^^^^
  $wp_roles->use_db = $use_db;

  if ( is_user_logged_in() ) // Re-prime the current user's caps
  $current_user->_init_caps();
}

if ( false !== strpos( __FILE__, MUPLUGINDIR ) )
add_action( 'init', 'um_unfilter_roles_one_time', 1 );

// Add the unfiltered_html capability back in to WordPress 3.0 multisite.
function um_unfilter_multisite( $caps, $cap, $user_id, $args ) {
  if ( $cap == 'unfiltered_html' ) {
    unset( $caps );
    $caps[] = $cap;
  }
  return $caps;
}
add_filter( 'map_meta_cap', 'um_unfilter_multisite', 10, 4 );
?>
