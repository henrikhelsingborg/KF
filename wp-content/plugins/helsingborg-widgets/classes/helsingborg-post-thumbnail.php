<?php
/**
 * helsingborgPostThumbnail
 *
 * @package Helsingborg Widgets
 * @author Henric Lind
 *
 * @access internal
 */
class helsingborgPostThumbnail {

  /**
   * PHP4 compatibility layer for calling the PHP5 constructor.
   *
   */
  function helsingborgPostThumbnail() {
    return $this->__construct();
  }

  /**
   * Main constructor - Add filter and action hooks
   *
   */
  function __construct() {
    add_filter( 'admin_post_thumbnail_html', array( $this, 'admin_post_thumbnail'), 10, 2 );
    return;
  }

  /**
   * Filter for the post meta box.
   *
   * @param string $content
   * @return string html output
   */
  function admin_post_thumbnail( $content, $post_id = null )
  {
    if ($post_id == null)
    {
      global $post;

      if ( !is_object($post) )
         return $content;

      $post_id = $post->ID;
    }

    $thumbnail_id = get_post_meta($post_id, '_thumbnail_id', true);
    return $this->_wp_post_thumbnail_html( $thumbnail_id );
  }

  /**
   * Output HTML for the post thumbnail meta-box.
   *
   * @see wp-admin\includes\post.php
   * @param int $thumbnail_id ID of the image used for thumbnail
   * @return string html output
   */
  function _wp_post_thumbnail_html( $thumbnail_id = NULL ) {

    global $_wp_additional_image_sizes, $post_ID;

    $set_thumbnail_link = '<p class="hide-if-no-js"><a title="' . esc_attr__( 'Set featured image' ) . '" id="set-post-thumbnail" class="thickbox">%s</a></p>';
    $upload_iframe_src = esc_url( get_upload_iframe_src('image', $post_ID ) );

    if (empty($thumbnail_id)) {
      $ajax_nonce = wp_create_nonce( "set_post_thumbnail-$post_ID" );
      $button_click = "helsingborgMediaSelector.create( ".$post_ID.", featured_img, 0, '" . $ajax_nonce . "'); return false;";
      $content = '<p>Används för nyhetslistning och indexpuffar. Bredd 300 px.</p><p style="width:100%;text-align:center;" class="button" name="featured_img" id="featured_img" onclick="' . $button_click . '">Välj utvald bild</p>';
    } else {
      $img_src = wp_get_attachment_image( $thumbnail_id, 'post-thumbnail' );
      $ajax_nonce = wp_create_nonce( "set_post_thumbnail-$post_ID" );
      $button_click = "helsingborgMediaSelector.remove( ".$post_ID.", '" . $ajax_nonce . "'); return false;";
      $content = sprintf($set_thumbnail_link, $img_src);
      $content .= '<p class="hide-if-no-js"><a href="#" id="remove-post-thumbnail" onclick="' . $button_click . '">' . esc_html__( 'Remove featured image' ) . '</a></p>';
    }

    return $content;
  }
}

$helsingborgPostThumbnail = new helsingborgPostThumbnail();
