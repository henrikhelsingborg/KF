<?php
/* Fire our meta box setup function on the post editor screen. */
add_action( 'load-post.php', 'samling_page_meta_boxes_setup' );
add_action( 'load-post-new.php', 'samling_page_meta_boxes_setup' );

/* Meta box setup function. */
function samling_page_meta_boxes_setup() {

  /* Add meta boxes on the 'add_meta_boxes' hook. */
  add_action( 'add_meta_boxes', 'samling_add_page_meta_boxes' );

  /* Save post meta on the 'save_post' hook. */
  add_action( 'save_post', 'samling_save_page_node_meta', 10, 2 );
}

/* Create one or more meta boxes to be displayed on the post editor screen. */
function samling_add_page_meta_boxes() {

  add_meta_box(
    'samling-page-node-select',      // Unique ID
    esc_html__( 'Välj föräldernod', 'helsingborg' ),    // Title
    'samling_page_node_select_meta_box',   // Callback function
    'page',         // Admin page (or post type)
    'normal',         // Context
    'high'         // Priority
  );
}

/* Display the post meta box. */
function samling_page_node_select_meta_box( $object, $box ) { 

	global $post;
    $meta = get_post_meta($object->ID,'_helsingborg_meta',TRUE);

    // Set previous selection
    if (is_array($meta)){
      $selected_id   = $meta['rss_select_id'];
      $selected_name = $meta['rss_select_name'];
    } else {
      $selected_id   = '';
      $selected_name = '-- Ingen sida vald --';
    }

    // Fetch the HTML
    include(helsingborg_THEME_FOLDER . '/UI/meta-ui-rss.php');
 }

/* Save the meta box's post metadata. */
function samling_save_page_node_meta( $post_id, $post ) {
	
  /* Get the post type object. */
  $post_type = get_post_type_object( $post->post_type );

  /* Check if the current user has permission to edit the post. */
  if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
    return $post_id;

  /* Get the posted data and sanitize it for use. */
  $selected_name = ( isset( $_POST['_helsingborg_meta']['rss_select_name'] ) ? sanitize_html_class( $_POST['_helsingborg_meta']['rss_select_name'] ) : '' );
  
  $selected_id = ( isset( $_POST['_helsingborg_meta']['rss_select_id'] ) ? sanitize_html_class( $_POST['_helsingborg_meta']['rss_select_id'] ) : '' );

  /* Get the meta key. */
  $meta_key = '_helsingborg_meta';

  /* Get the meta value of the custom field key. */
  $meta_value = get_post_meta( $post_id, $meta_key, false );

  	/* If a new meta value was added and there was no previous value, add it. */
	if ( $selected_id && '' == $meta_value['rss_select_id'] ) {
		add_post_meta( $post_id, $meta_key, $selected_id, false );
		add_post_meta( $post_id, $meta_key, $selected_name, false );
	}
	/* If the new meta value does not match the old value, update it. */
	elseif ( $selected_id && $selected_id != $meta_value['rss_select_id'] ) {
		update_post_meta( $post_id, $meta_key, $selected_id );
		update_post_meta( $post_id, $meta_key, $selected_name );
	}
	/* If there is no new meta value but an old value exists, delete it. */
	elseif ( '' == $selected_id && $meta_value['rss_select_id'] ) {
		delete_post_meta( $post_id, $meta_key, $meta_value['rss_select_id'] );
		delete_post_meta( $post_id, $meta_key, $meta_value['rss_select_name'] );
	}
}
?>