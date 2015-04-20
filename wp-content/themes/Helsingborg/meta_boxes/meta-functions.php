<?php

define('helsingborg_WORDPRESS_FOLDER',$_SERVER['DOCUMENT_ROOT']);
define('helsingborg_THEME_FOLDER',str_replace("\\",'/',dirname(__FILE__)));
define('helsingborg_THEME_PATH','/' . substr(helsingborg_THEME_FOLDER,stripos(helsingborg_THEME_FOLDER,'wp-content')));

add_action('admin_init','helsingborg_meta_init');

function helsingborg_meta_init()
{
    // review the function reference for parameter details
    // http://codex.wordpress.org/Function_Reference/wp_enqueue_script
    // http://codex.wordpress.org/Function_Reference/wp_enqueue_style

    //wp_enqueue_script('helsingborg_meta_js', helsingborg_THEME_PATH . '/custom/meta.js', array('jquery'));
    //wp_enqueue_style('helsingborg_meta_css', helsingborg_THEME_PATH . '/meta_boxes/UI/meta.css');

    // review the function reference for parameter details
    // http://codex.wordpress.org/Function_Reference/add_meta_box
    $post_id = '';
    if (isset($_GET['post'])) {
      $post_id = $_GET['post'];
    } else if (isset($_GET['post_ID'])) {
      $post_id = $_POST['post_ID'];
    }
    $template_file = get_post_meta($post_id,'_wp_page_template',TRUE);

    // add a meta box for each of the wordpress page types: posts and pages
    foreach (array('post','page') as $type)
    {

      // LIST PAGE
      if ($template_file == 'templates/list-page.php') {
        add_meta_box('helsingborg_all_meta', "Listpresentation", 'helsingborg_meta_ListPage', $type, 'normal', 'high');
      }
      // RSS PAGE
      else if ($template_file == 'templates/rss-page.php') {
        remove_post_type_support('page', 'editor');
        remove_post_type_support('page', 'comments');
        remove_post_type_support('page', 'custom-fields');
        add_meta_box('helsingborg_all_meta', "RSS nod", 'helsingborg_meta_RSS', $type, 'normal', 'high');
      }
      // Default page.php
      else {
        add_meta_box('helsingborg_all_meta', "Artikelsida", 'helsingborg_meta_ArticlePage', $type, 'normal', 'high');
      }
    }

    // add a callback function to save any data a user enters in
    add_action('save_post','helsingborg_meta_save');
}

function helsingborg_meta_ArticlePage() {
  global $post;

  // Get this page meta
  $meta = get_post_meta($post->ID,'_helsingborg_meta',TRUE);

  // Used if parent is list and metaboxes should be shown, same list will be used
  include(helsingborg_THEME_FOLDER . '/UI/list-array.php');

  // See if this page has any parents
  if ($post->ancestors) {
    // Get the current parent
    $parent = $post->ancestors;
    // Check if the parent actually exists
    if ($parent) {
      // Make sure the first ancestor is retrieved -> parent
      $post_parent = $parent[0];

      // Get the parent meta
      $parent_meta = get_post_meta($post_parent,'_helsingborg_meta',TRUE);

      // Check if parent want some data
      if (!empty($parent_meta['list_options'])) {

        // Take all keys from parent
        $parent_keys = explode( ',', $parent_meta['list_options']);

        // Go through all items in list-array
        foreach ($list as $key => $value) :

          // If the item is present among parent keys, then setup so this value can be used
          if (in_array($key, $parent_keys)) :

            // Retrieve the value if it has been set
            if (!is_array($meta) || !array_key_exists('article_options_' . $key, $meta))
              $set_value = '';
            else
              $set_value = $meta['article_options_' . $key];

            // Print the form form for this item and make sure it can be saved as meta
          ?>
          <p>
            <label style="font-weight:bold;" for="_helsingborg_meta[article_options_<?php echo $key ?>]"><?php echo $value ?></label>
            <input type="text" name="_helsingborg_meta[article_options_<?php echo $key ?>]" id="_helsingborg_meta[article_options_<?php echo $key ?>]" style="width:100%;" value="<?php echo $set_value ?>" />
          </p>
        <?php
          endif;
        endforeach;
      }
    }
  }

  // create a custom nonce for submit verification later
  echo '<input type="hidden" name="helsingborg_meta_noncename" value="' . wp_create_nonce(__FILE__) . '" />';
}

function helsingborg_meta_ListPage() {
  global $post;
  $meta = get_post_meta($post->ID,'_helsingborg_meta',TRUE);

  // Check if meta is available
  if (is_array($meta)){
    $selected = $meta['list_options'];
  } else {
    $selected = '';
  }

  // Only need one list to change
  include(helsingborg_THEME_FOLDER . '/UI/list-array.php');

  // Include the form for UI
  include(helsingborg_THEME_FOLDER . '/UI/meta-ui-listselection.php');

  // create a custom nonce for submit verification later
  echo '<input type="hidden" name="helsingborg_meta_noncename" value="' . wp_create_nonce(__FILE__) . '" />';
}

function helsingborg_meta_RSS()
{
    global $post;
    $meta = get_post_meta($post->ID,'_helsingborg_meta',TRUE);

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

    // create a custom nonce for submit verification later
    echo '<input type="hidden" name="helsingborg_meta_noncename" value="' . wp_create_nonce(__FILE__) . '" />';
}

function helsingborg_meta_List()
{
    global $post;

    // using an underscore, prevents the meta variable
    // from showing up in the custom fields section
    $meta = get_post_meta($post->ID,'_helsingborg_meta',TRUE);

    // Get all pages available
    $args = array(
      'sort_order' => 'ASC',
      'sort_column' => 'post_title',
      'post_type' => 'page',
      'child_of' => 0,
      'parent' => -1,
      'post_status' => 'publish'
    );

    $pages = get_pages($args);

    // Set previous selection
    $selected = $meta['list_select'];

    // Fetch the HTML
    include(helsingborg_THEME_FOLDER . '/UI/meta-ui-list.php');

    // create a custom nonce for submit verification later
    echo '<input type="hidden" name="helsingborg_meta_noncename" value="' . wp_create_nonce(__FILE__) . '" />';
}

function helsingborg_meta_save($post_id)
{
    // authentication checks

    // make sure data came from our meta box
    if (!wp_verify_nonce($_POST['helsingborg_meta_noncename'],__FILE__)) return $post_id;

    // check user permissions
    if ($_POST['post_type'] == 'page')
    {
        if (!current_user_can('edit_page', $post_id)) return $post_id;
    }
    else
    {
        if (!current_user_can('edit_post', $post_id)) return $post_id;
    }

    // authentication passed, save data

    // var types
    // single: _helsingborg_meta[var]
    // array: _helsingborg_meta[var][]
    // grouped array: _helsingborg_meta[var_group][0][var_1], _helsingborg_meta[var_group][0][var_2]

    $current_data = get_post_meta($post_id, '_helsingborg_meta', TRUE);
    $new_data = $_POST['_helsingborg_meta'];

    helsingborg_meta_clean($new_data);

    if ($current_data)
    {
        if (is_null($new_data)) delete_post_meta($post_id,'_helsingborg_meta');
        else update_post_meta($post_id,'_helsingborg_meta',$new_data);
    }
    elseif (!is_null($new_data))
    {
        add_post_meta($post_id,'_helsingborg_meta',$new_data,TRUE);
    }

    return $post_id;
}

function helsingborg_meta_clean(&$arr)
{
    if (is_array($arr))
    {
        foreach ($arr as $i => $v)
        {
            if (is_array($arr[$i]))
            {
                helsingborg_meta_clean($arr[$i]);

                if (!count($arr[$i]))
                {
                    unset($arr[$i]);
                }
            }
            else
            {
                if (trim($arr[$i]) == '')
                {
                    unset($arr[$i]);
                }
            }
        }

        if (!count($arr))
        {
            $arr = NULL;
        }
    }
}

?>
