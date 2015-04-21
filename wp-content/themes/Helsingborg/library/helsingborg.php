<?php
/* Function for displaying proper IDs depending on current location,
 * used by wp_include_pages for the menus.
*/
function get_included_pages($post) {
  $includes = array();
  $args = array(
    'post_type' => 'page',
    'post_status' => 'publish',
    'post_parent' => get_option('page_on_front'),
  );

  $base_pages = get_children( $args );
  foreach($base_pages as $page) {
    array_push($includes, $page->ID);
  }

  if ($post) {
    $ancestors = get_post_ancestors($post);
    array_push($ancestors, strval($post->ID));
    foreach ($ancestors as $ancestor) {
      $args = array(
        'post_type' => 'page',
        'post_status' => 'publish',
        'post_parent' => $ancestor,
      );

      $childs = get_children( $args );
      foreach ($childs as $child) {
        array_push($includes, $child->ID);
      }
      array_push($includes, $ancestor);
    }
  }

  return implode(',', $includes);
}

/* Flush cache when page is updated */
function cache_flush_on_page_update( $post_id ) {
  // Remove the cached menu
  wp_cache_delete('menu_' . $post_id);

  // Remove the W3TC for this specific page
  if(function_exists('w3tc_pgcache_flush_post')){
      w3tc_pgcache_flush_post($post_id);

      // If page parent is list page, then flush that cache as well
      $parent = wp_get_post_parent_id($post_id);
      if ($parent) {
        $template_file = get_post_meta($parent,'_wp_page_template',TRUE);
        if ($template_file == 'templates/list-page.php') {
          w3tc_pgcache_flush_post($parent);
        }
      }
  }
}
add_filter( 'save_post', 'cache_flush_on_page_update', 10, 1 );

// Add scheduled work for CBIS events
require_once('scheduled_cbis.php');
 /* Setup the scheduled task */
add_action( 'wp', 'setup_scheduled_cbis' );
function setup_scheduled_cbis() {
  if ( ! wp_next_scheduled( 'scheduled_cbis' ) ) {
    // Set scheduled task to occur at 22.30 each day
    wp_schedule_event( strtotime(date("Y-m-d", time()) . '22:30'), 'daily', 'scheduled_cbis');
  }
}

// Add scheduled work for XCap events
require_once('scheduled_xcap.php');
/* Setup the scheduled task */
add_action( 'wp', 'setup_scheduled_xcap' );
function setup_scheduled_xcap() {
  if ( ! wp_next_scheduled( 'scheduled_xcap' ) ) {
    // Set scheduled task to occur at 22.30 each day
    wp_schedule_event( strtotime(date("Y-m-d", time()) . '22:30'), 'daily', 'scheduled_xcap');
  }
}

/* Update the default maximum number of redirects to 250 */
add_filter( 'srm_max_redirects', 'dbx_srm_max_redirects' );
function dbx_srm_max_redirects() {
    return 250;
}

/*
 * We need to insert empty spans in content.
 * Make sure html content isnt altered with when switching between Visual and Text.
 * This is due to our "listen" icon after documents, sent to readspeaker docreader.
 */
function override_mce_options($initArray) {
  $opts = '*[*]';
  $initArray['valid_elements'] = $opts;
  $initArray['extended_valid_elements'] = $opts;
  return $initArray;
}
add_filter('tiny_mce_before_init', 'override_mce_options');

/* Add new Cron interval used by our schedule events */
add_filter( 'cron_schedules', 'cron_add_3min' );
function cron_add_3min( $schedules ) {
   $schedules['3min'] = array(
       'interval' => 3*60,
       'display' => __( 'Once every three minutes' )
   );
   return $schedules;
}

/* Add supported mime-types for upload */
function add_mime_types($mimes) {
  $mimes = array(
    'dwg' => 'application/dwg',
    'tfw' => 'application/tfw',

    // Image formats
    'jpg|jpeg|jpe'                 => 'image/jpeg',
    'gif'                          => 'image/gif',
    'png'                          => 'image/png',
    'bmp'                          => 'image/bmp',
    'tif|tiff'                     => 'image/tiff',
    'ico'                          => 'image/x-icon',

    // Video formats
    'asf|asx'                      => 'video/x-ms-asf',
    'wmv'                          => 'video/x-ms-wmv',
    'wmx'                          => 'video/x-ms-wmx',
    'wm'                           => 'video/x-ms-wm',
    'avi'                          => 'video/avi',
    'divx'                         => 'video/divx',
    'flv'                          => 'video/x-flv',
    'mov|qt'                       => 'video/quicktime',
    'mpeg|mpg|mpe'                 => 'video/mpeg',
    'mp4|m4v'                      => 'video/mp4',
    'ogv'                          => 'video/ogg',
    'webm'                         => 'video/webm',
    'mkv'                          => 'video/x-matroska',

    // Text formats
    'txt|asc|c|cc|h'               => 'text/plain',
    'csv'                          => 'text/csv',
    'tsv'                          => 'text/tab-separated-values',
    'ics'                          => 'text/calendar',
    'rtx'                          => 'text/richtext',
    'css'                          => 'text/css',
    'htm|html'                     => 'text/html',

    // Audio formats
    'mp3|m4a|m4b'                  => 'audio/mpeg',
    'ra|ram'                       => 'audio/x-realaudio',
    'wav'                          => 'audio/wav',
    'ogg|oga'                      => 'audio/ogg',
    'mid|midi'                     => 'audio/midi',
    'wma'                          => 'audio/x-ms-wma',
    'wax'                          => 'audio/x-ms-wax',
    'mka'                          => 'audio/x-matroska',

    // Misc application formats
    'rtf'                          => 'application/rtf',
    'js'                           => 'application/javascript',
    'pdf'                          => 'application/pdf',
    'swf'                          => 'application/x-shockwave-flash',
    'class'                        => 'application/java',
    'tar'                          => 'application/x-tar',
    'zip'                          => 'application/zip',
    'gz|gzip'                      => 'application/x-gzip',
    'rar'                          => 'application/rar',
    '7z'                           => 'application/x-7z-compressed',
    'exe'                          => 'application/x-msdownload',
    'swf'                          => 'application/x-shockwave-flash',

    // MS Office formats
    'doc'                          => 'application/msword',
    'pot|pps|ppt'                  => 'application/vnd.ms-powerpoint',
    'wri'                          => 'application/vnd.ms-write',
    'xla|xls|xlt|xlw'              => 'application/vnd.ms-excel',
    'mdb'                          => 'application/vnd.ms-access',
    'mpp'                          => 'application/vnd.ms-project',
    'docx'                         => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'docm'                         => 'application/vnd.ms-word.document.macroEnabled.12',
    'dotx'                         => 'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
    'dotm'                         => 'application/vnd.ms-word.template.macroEnabled.12',
    'xlsx'                         => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    'xlsm'                         => 'application/vnd.ms-excel.sheet.macroEnabled.12',
    'xlsb'                         => 'application/vnd.ms-excel.sheet.binary.macroEnabled.12',
    'xltx'                         => 'application/vnd.openxmlformats-officedocument.spreadsheetml.template',
    'xltm'                         => 'application/vnd.ms-excel.template.macroEnabled.12',
    'xlam'                         => 'application/vnd.ms-excel.addin.macroEnabled.12',
    'pptx'                         => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
    'pptm'                         => 'application/vnd.ms-powerpoint.presentation.macroEnabled.12',
    'ppsx'                         => 'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
    'ppsm'                         => 'application/vnd.ms-powerpoint.slideshow.macroEnabled.12',
    'potx'                         => 'application/vnd.openxmlformats-officedocument.presentationml.template',
    'potm'                         => 'application/vnd.ms-powerpoint.template.macroEnabled.12',
    'ppam'                         => 'application/vnd.ms-powerpoint.addin.macroEnabled.12',
    'sldx'                         => 'application/vnd.openxmlformats-officedocument.presentationml.slide',
    'sldm'                         => 'application/vnd.ms-powerpoint.slide.macroEnabled.12',
    'onetoc|onetoc2|onetmp|onepkg' => 'application/onenote',

    // OpenOffice formats
    'odt'                          => 'application/vnd.oasis.opendocument.text',
    'odp'                          => 'application/vnd.oasis.opendocument.presentation',
    'ods'                          => 'application/vnd.oasis.opendocument.spreadsheet',
    'o dg'                          => 'application/vnd.oasis.opendocument.graphics',
    'odc'                          => 'application/vnd.oasis.opendocument.chart',
    'odb'                          => 'application/vnd.oasis.opendocument.database',
    'odf'                          => 'application/vnd.oasis.opendocument.formula',

    // WordPerfect formats
    'wp|wpd'                       => 'application/wordperfect',

    // iWork formats
    'key'                          => 'application/vnd.apple.keynote',
    'numbers'                      => 'application/vnd.apple.numbers',
    'pages'                        => 'application/vnd.apple.pages',
  );
  return $mimes;
}
add_filter('upload_mimes','add_mime_types');

/* Include JavaScript WordPress editor functions, used for guides */
if( file_exists( get_template_directory() . '/includes/js-wp-editor.php' ) ) {
  require_once( get_template_directory() . '/includes/js-wp-editor.php' );
}

/* Remove Medium image size, only need full and thumbnail */
function remove_medium_image_size() {
  remove_image_size('medium');
}
add_action('init', 'remove_medium_image_size');

/* Fix to rename Default Template text to 'Artikel', since this page is default */
function change_default_template_to_artikel( $translation, $text, $domain ) {
  if ( $text == 'Default Template' ) {
    return _('Artikel');
  }
  return $translation;
}
add_filter( 'gettext', 'change_default_template_to_artikel', 10, 3 );

/* Add ID field for each user, which is used for listing events */
add_action( 'show_user_profile', 'helsingborg_happy_user_id_field' );
add_action( 'edit_user_profile', 'helsingborg_happy_user_id_field' );
function helsingborg_happy_user_id_field( $user ) { ?>
  <h3><?php _e("Evenemangshantering", "blank"); ?></h3>
  <table class="form-table">
    <tr>
      <th><label for="happy_user_id"><?php _e("Användarens ID"); ?></label></th>
      <td>
        <input type="text" name="happy_user_id" id="happy_user_id" class="regular-text"
            value="<?php echo esc_attr( get_the_author_meta( 'happy_user_id', $user->ID ) ); ?>" /><br />
        <span class="description"><?php _e("Skriv in id som används för autentiering för evenemangshantering."); ?></span>
    </td>
    </tr>
  </table>
<?php
}

/* When the users ID is saved */
add_action( 'personal_options_update', 'helsingborg_save_happy_user_id_field' );
add_action( 'edit_user_profile_update', 'helsingborg_save_happy_user_id_field' );
function helsingborg_save_happy_user_id_field( $user_id ) {
  $saved = false;
  if ( current_user_can( 'edit_user', $user_id ) ) {
    update_user_meta( $user_id, 'happy_user_id', $_POST['happy_user_id'] );
    $saved = true;
  }
  return true;
}

/* Adds the event to the database, then deletes the entry */
add_action('gform_after_submission', 'event_add_entry_to_db', 10, 2);
function event_add_entry_to_db($entry, $form) {

  // Make sure to only hijack the event form -> set in settings
  if (strcmp($entry['form_id'], get_option('helsingborg_event_form_id')) === 0) {
    // Event
    $name         = $entry[1];
    $description  = $entry[15];
    $approved     = 0;
    $organizer    = $entry[11];
    $location     = $entry[7];
    $external_id  = null;

    // Event time
    $type_of_date  = $entry[4];
    $single_date   = $entry[5];
    $time          = $entry[6];
    $start_date    = $entry[8];
    $end_date      = $entry[9];

    // Selected days
    for($e=1;$e<=7;$e++) {
      if (strlen($entry["10.$e"])>0) {
        $days_array[] = $e;
      }
    }

    // Create event
    $event        = 	array ( 'Name'            => $name,
                              'Description'     => $description,
                              'Approved'        => $approved,
                              'OrganizerID'     => $organizer,
                              'Location'        => $location,
                              'ExternalEventID' => $external_id );

    // Administration units
    $administrations = explode(',', $entry[3]);
    foreach($administrations as $unit) {
      $administration_units[] = HelsingborgEventModel::get_administration_id_from_name($unit)->AdministrationUnitID;
    }

    // Event types
    $event_types  = explode(',', $entry[2]);

    // Create time/times
    $event_times = array();
    if ($single_date) { // Single occurence
      $event_time = array('Date'  => $single_date,
                          'Time'  => $time,
                          'Price' => 0);
      array_push($event_times, $event_time);
    } else { // Must be start and end then
      $dates_array = create_date_range_array($start_date, $end_date);
      $filtered_days = filter_date_array_by_days($dates_array, $days_array);

      foreach($filtered_days as $date) {
        $event_time = array('Date'  => $date,
                            'Time'  => $time,
                            'Price' => 0);
        array_push($event_times, $event_time);
      }
    }

    // Image
    if ($entry[16])
      $image = handle_gf_image($entry[16], $entry[13]);

    // Now create the Event in DB
    HelsingborgEventModel::create_event($event, $event_types, $administration_units, $image, $event_times);

    // Now remove the entry from GF !
    delete_form_entry($entry);
  }
}

/* Copies the image to the new location. Returns an array with the new image path and author */
function handle_gf_image($path, $author) {
  $image_path   = $path;
  $file_name    = basename($image_path);
  $uploads      = wp_upload_dir();
  $new_path     = $uploads['basedir'].'/eventimages/'.$file_name;
  $save_path     = $uploads['baseurl'].'/eventimages/'.$file_name;

  if (!file_exists($uploads['basedir'].'/eventimages/')) {
    mkdir($uploads['basedir'].'/eventimages/', 0777, true);
  }

  if (!copy($image_path, $new_path))
    return false;

  return array( 'ImagePath' => $save_path, 'Author' => $auther);
}

/* Completely removes all trace of an entry */
function delete_form_entry( $entry ) {
  $delete = GFAPI::delete_entry( $entry['id'] );
  $result = ( $delete ) ? "entry {$entry['id']} successfully deleted." : $delete;
  GFCommon::log_debug( "GFAPI::delete_entry() - form #{$form['id']}: " . print_r( $result, true ) );
}

/* Filter the days so only those in $days_array is returned. */
function filter_date_array_by_days($dates_array, $days_array) {
  $return_array=array();
  foreach($dates_array as $date_string) {
    if (in_array(date('N', strtotime($date_string)), $days_array)) {
      array_push($return_array, $date_string);
    }
  }
  return $return_array;
}

/* Creates an Array with strings with all dates between the from and to dates inserted. */
function create_date_range_array($strDateFrom,$strDateTo)
{
    $aryRange=array();
    $iDateFrom=mktime(1,0,0,substr($strDateFrom,5,2),substr($strDateFrom,8,2),substr($strDateFrom,0,4));
    $iDateTo=mktime(1,0,0,substr($strDateTo,5,2),substr($strDateTo,8,2),substr($strDateTo,0,4));
    if ($iDateTo>=$iDateFrom)
    {
        array_push($aryRange,date('Y-m-d',$iDateFrom)); // first entry
        while ($iDateFrom<$iDateTo)
        {
            $iDateFrom+=86400; // add 24 hours
            array_push($aryRange,date('Y-m-d',$iDateFrom));
        }
    }
    return $aryRange;
}

/**
 * trims text to a space then adds ellipses if desired
 * @param string $input text to trim
 * @param int $length in characters to trim to
 * @param bool $ellipses if ellipses (...) are to be added
 * @param bool $strip_html if html tags are to be stripped
 * @param bool $strip_style if css style are to be stripped
 * @return string
 */
function trim_text($input, $length, $ellipses = true, $strip_tag = true,$strip_style = true) {
    //strip tags, if desired
    if ($strip_tag) { $input = strip_tags($input); }

    //strip tags, if desired
    if ($strip_style) { $input = preg_replace('/(<[^>]+) style=".*?"/i', '$1',$input); }

    if($length=='full') { $trimmed_text=$input; }
    else
    {
        //no need to trim, already shorter than trim length
        if (strlen($input) <= $length) { return $input; }

        //find last space within length
        $last_space = strrpos(substr($input, 0, $length), ' ');
        $trimmed_text = substr($input, 0, $last_space);

        //add ellipses (...)
        if ($ellipses) { $trimmed_text .= '...'; }
    }
    return $trimmed_text;
}

/* Prints the breadcrumb */
function the_breadcrumb() {
    global $post;
    $title = get_the_title();
    $output = '';
    
    if( get_option( 'show_on_front' ) == 'page' ) {
	    $blogPageURL = get_permalink( get_option('page_for_posts' ) );
	    $blogPageTITLE = get_page( get_option('page_for_posts' ) )->post_title;
	}   
	else {
		$blogPageURL = get_bloginfo('url');
		$blogPageTITLE = get_bloginfo('name');
	}
    echo '<ul class="breadcrumbs">';
    if (!is_front_page()) {
        if (is_category() || is_tag() || is_author() || is_single()) {
            echo '<li><a href="'.$blogPageURL.'">'.$blogPageTITLE.'</a></li>';
            if (is_category()) {
            	echo '<li>'; the_category(' <li> ');
            }
            if (is_tag()) {
				echo '<li>'; single_tag_title(); echo '</li>';
            }
            if (is_author()) {
				echo '<li>'.get_the_author().'</li>';
            }
            if (is_single()) {
                echo '<li>'.get_the_title().'</li>';
            }
        } elseif (is_page()) {
            if($post->post_parent){
                $anc = get_post_ancestors( $post->ID );
                $title = get_the_title();
                foreach ( $anc as $ancestor ) {
                  if (get_post_status($ancestor) != 'private') {
                    $output = '<li><a href="'.get_permalink($ancestor).'" title="'.get_the_title($ancestor).'">'.get_the_title($ancestor).'</a></li></li>' . $output;
                  }
                }
                echo $output;
                echo '<strong title="'.$title.'"> '.$title.'</strong>';
            } else {
                echo '<li><strong> '.get_the_title().'</strong></li>';
            }
        }
    }
    echo '</ul>';
}



// Allow script & iframe tag within posts
function allow_post_tags( $allowedposttags ){
  $allowedposttags['script'] = array(
    'type' => true,
    'src' => true,
    'height' => true,
    'width' => true,
  );
  $allowedposttags['iframe'] = array(
    'src' => true,
    'width' => true,
    'height' => true,
    'class' => true,
    'frameborder' => true,
    'webkitAllowFullScreen' => true,
    'mozallowfullscreen' => true,
    'allowFullScreen' => true
  );
  return $allowedposttags;
}
add_filter('wp_kses_allowed_html','allow_post_tags', 1);


/**
 * Override of Page Attribute meta box
 * This is because the load time of wp_dropdown_pages(), which is removed in our version.
 * (original: page_attributes_meta_box() in wp-admin/includes/meta-boxes.php)
 */

// Remove the original meta box
add_action( 'admin_menu', 'helsingborg_remove_meta_box');
function helsingborg_remove_meta_box(){
  remove_meta_box('pageparentdiv', 'page', 'side');
}

// Add our own meta box instead
add_action( 'add_meta_boxes', 'helsingborg_add_meta_box');
function helsingborg_add_meta_box() {
  add_meta_box('pageparentdiv', __('Page Attributes') , 'helsingborg_page_attributes_meta_box', 'page', 'side');
}

// Use custom page attributes meta box, no need to load dropdown with pages!
function helsingborg_page_attributes_meta_box($post) {
  if ( 'page' == $post->post_type && 0 != count( get_page_templates( $post ) ) ) {
    $template = !empty($post->page_template) ? $post->page_template : false;
    ?>
    <p><strong><?php _e('Template') ?></strong></p>
    <label class="screen-reader-text" for="page_template"><?php _e('Page Template') ?></label><select name="page_template" id="page_template">
      <option value='default'><?php _e('Default Template'); ?></option>
      <?php page_template_dropdown($template); ?>
    </select>
    <?php
  } ?>
  <p><strong><?php _e('Order') ?></strong></p>
  <p><label class="screen-reader-text" for="menu_order"><?php _e('Order') ?></label><input name="menu_order" type="text" size="4" id="menu_order" value="<?php echo esc_attr($post->menu_order) ?>" /></p>
  <p><?php if ( 'page' == $post->post_type ) _e( 'Need help? Use the Help tab in the upper right of your screen.' ); ?></p>
  <?php
}
?>
