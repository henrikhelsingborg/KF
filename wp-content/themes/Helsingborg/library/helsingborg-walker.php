<?php
class Helsingborg_Walker extends Walker {
  public $tree_type = 'page';
  public $db_fields = array ('parent' => 'post_parent', 'id' => 'ID');

  function walk( $elements, $max_depth ) {
    global $post;
    $args = array_slice( func_get_args(), 2 );
    $output = '';

    /* invalid parameter */
    if ( $max_depth < -1 ) {
      return $output;
    }

    // Don't print the whole tree on search results page
    if (is_search()) {
      $max_depth = 1;
    }

    /* Nothing to walk */
    if ( empty( $elements ) ) {
      return $output;
    }

    /* Set up variables. */
    $top_level_elements = array();
    $children_elements  = array();
    $parent_field = $this->db_fields['parent'];
    $child_of = intval(get_option('page_on_front'));

    /* Loop elements */
    foreach ( (array) $elements as $e ) {
      $parent_id = $e->$parent_field;
      if ( isset( $parent_id ) ) {

        /* Skip showing childs of list page */
        if (get_post_meta($parent_id,'_wp_page_template',TRUE) == 'templates/list-page.php') {
          continue;
        }

        /* Top level pages. */
        if( $child_of === $parent_id ) {
          $top_level_elements[] = $e;
        }

        /* Only display children of the current hierarchy. */
        else if (
        ( isset( $post->ID ) && $parent_id == $post->ID ) ||
        ( isset( $post->post_parent ) && $parent_id == $post->post_parent ) ||
        ( isset( $post->ancestors ) && in_array( $parent_id, (array) $post->ancestors ) )
        ) {
          $children_elements[ $e->$parent_field ][] = $e;
        }
      }
    }

    foreach ( $top_level_elements as $e ) {
      $this->display_element( $e, $children_elements, $max_depth, 0, $args, $output );
    }
    return $output;
  }

  public function start_lvl( &$output, $depth = 0, $args = array() ) {
    $indent = str_repeat("\t", $depth);
    $output .= "\n$indent<ul class='sub-menu'>\n";
  }

  public function end_lvl( &$output, $depth = 0, $args = array() ) {
    $indent = str_repeat("\t", $depth);
    $output .= "$indent</ul>\n";
  }

  public function start_el( &$output, $page, $depth = 0, $args = array(), $current_page = 0 ) {
    if ( $depth ) {
      $indent = str_repeat( "\t", $depth );
    } else {
      $indent = '';
    }

    if ( ! empty( $current_page ) ) {
      $_current_page = get_post( $current_page );
      $css_class = '';
      $arr_css_classes = array();

      if ( in_array( $page->ID, $_current_page->ancestors ) && $page->post_parent == get_option('page_on_front') ) {
        $arr_css_classes[] = 'current-node';
      }
      if ( in_array( $page->ID, $_current_page->ancestors ) ) {
        $arr_css_classes[] = 'current-ancestor';
      }
      if ( $page->ID == $current_page ) {
        $arr_css_classes[] = 'current';
      }
      $args = array(
        'post_type' => 'page',
        'post_status' => 'publish',
        'post_parent' => $page->ID,
      );

      $children = get_children($args);
      $has_children = !empty($children);

      // Check if page got childrens or not, if it does, add has-child class
      if ( !in_array( $page->ID, $_current_page->ancestors ) && $has_children && ($page->post_parent != get_option('page_on_front')) && get_post_meta($page->ID,'_wp_page_template',TRUE) != 'templates/list-page.php' ) {
        $arr_css_classes[] = 'has-childs';
      }

      /* If article page parent is list page, then mark the parent as current -> since childs are hidden */
      if ( in_array( $page->ID, $_current_page->ancestors) && get_post_meta($page->ID,'_wp_page_template',TRUE) == 'templates/list-page.php') {
        $arr_css_classes[] = 'current';
      }


      /*
      If the current items parent is set as PRIVATE(and should not be visible in menus),
      the private parent should be set as current instead.

      Example with ancestors:
          25    5220         5776            5781          5785
        (root) (node)  (set to current)   (private)   (actual current)
      http://localhost/startsida/omsorg-och-stod/frivilligt-arbete-och-foreningar/info/las-mer-om-socialt-arbete-med-ersattning/
      */
      if ( get_post_status($_current_page->post_parent) == 'private' && in_array( $page->ID, $_current_page->ancestors)) {
        $_current_page_ansectors = $_current_page->ancestors;
        $last_element = count($_current_page_ansectors) - 1; // We want last index

        // -2 for seletion of grandparent
        $selector = $last_element > 1 ? $_current_page_ansectors[$last_element-2] : 0;
        if ($selector && $page->ID == $selector) {
          $css_class = 'class="current"';
          $arr_css_classes[] = 'current';
        }
      }
    }

    if (count($arr_css_classes) > 0)
    {
        $css_class = 'class="' . implode(' ', $arr_css_classes) . '"';
    } else {
        $css_class = '';
    }

    /* Now let's build the item */
    $output .= $indent . sprintf(
    '<li %s><a href="%s">%s</a>',
      $css_class,
      get_permalink( $page->ID ),
      apply_filters( 'the_title', $page->post_title, $page->ID )
      );

    }

    public function end_el( &$output, $page, $depth = 0, $args = array() ) {
      $output .= "</li>\n";
    }
  }

?>
