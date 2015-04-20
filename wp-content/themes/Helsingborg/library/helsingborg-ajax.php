<?php
/******************/
/* AJAX FUNCTIONS */
/******************/


/* Manually start fetch of XCap */
add_action( 'wp_ajax_start_manual_xcap', 'start_manual_xcap_callback');
function start_manual_xcap_callback() {
  xcap_event();
}

/* Manually start fetch of CBIS */
add_action( 'wp_ajax_start_manual_cbis', 'start_manual_cbis_callback');
function start_manual_cbis_callback() {
  cbis_event();
}

/* Loads the list of event, to be presented inside a widget */
add_action( 'wp_ajax_nopriv_update_event_calendar', 'update_event_calendar_callback');
add_action( 'wp_ajax_update_event_calendar', 'update_event_calendar_callback');
function update_event_calendar_callback() {
  $amount = $_POST['amount'];
  $ids    = $_POST['ids'];

  // Get the events
  $events = HelsingborgEventModel::load_events_simple($amount, $ids);

  $today = date('Y-m-d');
  $list = '';
  foreach( $events as $event ) :
    $list .= '<li>';

    // Present 'Idag HH:ii' och 'YYYY-mm-dd'
    if ($today == $event->Date) {
      $list .= '<span class="date">Idag ' . $event->Time . '</span>';
    } else {
      $list .= '<span class="date">' . $event->Date . '</span>';
    }

    $list .= '<a href="#" class="modalLink" id="'.$event->EventID.'" data-reveal-id="eventModal">'.$event->Name.'</a>';
    $list .= '</li>';
  endforeach;

  $result = array('events' => $events, 'list' => $list);
  echo json_encode($result);
  die();
}

/* Loads the big notifications i.e. warning/information and prints the alert messages */
/* The IDs being fetched are set from Helsingborg settings */
add_action( 'wp_ajax_nopriv_big_notification', 'big_notification_callback');
add_action( 'wp_ajax_big_notification', 'big_notification_callback');
function big_notification_callback() {
  global $wpdb;
  $disturbances = array();
  $informations = array();

  // Get the parent IDs from where the notifications are being fetched
  $disturbance_root_id = get_option('helsingborg_big_disturbance_root');
  $information_root_id = get_option('helsingborg_big_information_root');

  // Get the child pages for these IDs
  $wp_query     = new WP_Query();
  $all_wp_pages = $wp_query->query(array('post_type' => 'page'));

  // Get the children
  if ($disturbance_root_id != '')
    $disturbances = get_page_children($disturbance_root_id, $all_wp_pages);
  if ($information_root_id != '')
    $informations = get_page_children($information_root_id, $all_wp_pages);

  // Merge the notifications and sort the new array by date
  $notifications = array_merge($disturbances, $informations);

  // No notifications to show, just die
  if (!$notifications) {die();}

  // Sort all notifications by date
  usort( $notifications, create_function('$a,$b', 'return strcmp($b->post_date, $a->post_date);'));

  // Return
  die();
}

/* Uses Google Custom Search to get JSON with search results */
/* Show these search results instead of WP original */
add_action( 'wp_ajax_nopriv_search', 'search_callback');
add_action( 'wp_ajax_search', 'search_callback');
function search_callback() {
  $key       = 'AIzaSyCMGfdDaSfjqv5zYoS0mTJnOT3e9MURWkU';
  $cx        = '016534817360440217175:ndsqkc_wtzg';
  $index     = $_POST['index'];
  $keyword   = $_POST['keyword'];

  $url = 'https://www.googleapis.com/customsearch/v1?key='.$key.'&cx='.$cx.'&q='.urlencode($keyword).'&siteSearchFilter=i&alt=json&start='.$index;

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
  curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
  curl_setopt($ch, CURLOPT_REFERER, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

  $result = curl_exec($ch);
  curl_close($ch);
  echo $result;
  die();
};

/* Fixes issue with url being wrong inside of widgets, replace all occerrences here */
add_action( 'wp_ajax_fix_widget_data', 'fix_widget_data_callback');
function fix_widget_data_callback() {
  global $wpdb;
  $from = $_POST['from'];
  $to   = $_POST['to'];

  if (count($from) < 1 || count($to) < 1) {
    echo ('Värde saknas!'); die();
  }

  for ($i=1;$i<=125;$i++) {
    $wp_table = $i == 1 ? 'wp_options' : 'wp_' . $i . '_options';

    // Fetch list with those containing the searched value
    $option_ids_query = "SELECT option_id FROM $wp_table WHERE option_name LIKE '%widget%' AND option_value LIKE '%" . $from . "%'";
    $option_ids = $wpdb->get_results($option_ids_query, ARRAY_A);

    // Iterate through all option_ids and go through its data
    foreach ($option_ids as $option_id) {

      // Get the data
      $option_value_query = "SELECT option_value FROM $wp_table WHERE option_id = " . $option_id['option_id'];
      $option_value = $wpdb->get_results($option_value_query, OBJECT)[0]->option_value;

      // Separate values
      $value_array = explode(';', $option_value);

      // Go through each complete value
      foreach($value_array as $key => $value) {
        if (strpos($value, $from) !== false) {
          // Get the proper values
          preg_match('/s:(\d+):"(.*?)"/', $value, $matches);

          // Update url with new parameters
          $new_url = str_replace($from, $to, $matches[2]);

          // Now update complete string
          $value_array[$key] = update_url_and_value($value, $new_url);
        }
      }

      // Now pack it together and save in DB
      $option_value = implode(';', $value_array);
      $result = $wpdb->update($wp_table,
                              array('option_value' => $option_value),
                              array('option_id' => $option_id['option_id']));
    }

    if ($result) { echo 'Uppdaterade - ' . $wp_table . '<br>'; } else { echo 'Ingen uppdatering skedde för ' . $wp_table . '<br>'; }
  }

  die();
}

/* Updates the string value and it's counter, main usage for stored widget data */
function update_url_and_value($obj, $new_value) {
  $updated_value = preg_replace('/s:(\d+):\\"(.*?)\\"/', 's:'. strlen($new_value) . ':"' . $new_value . '"', $obj );
  return $updated_value;
}

/* Loads pages where post_title has keyword $title */
add_action( 'wp_ajax_load_page_with_id', 'load_page_with_id_callback');
function load_page_with_id_callback() {
  global $wpdb;
  $id        = $_POST['id'];

  $sql = "SELECT ID, post_title
          FROM $wpdb->posts
          WHERE ID = " . $id;

  $pages = $wpdb->get_results($sql);

  if ($pages) {$page = $pages[0];} else {die();}

  echo $page->post_title . '|' . get_permalink($page->ID);

  die();
}

/* Loads pages where post_title has keyword $title */
add_action( 'wp_ajax_load_pages_with_update', 'load_pages_with_update_callback');
function load_pages_with_update_callback() {
  global $wpdb;
  $title     = $_POST['title'];
  $id        = $_POST['id'];
  $num       = $_POST['num'];
  $update    = $_POST['update'];

  if (is_numeric($title)) {
    $sql = "SELECT ID, post_title
            FROM $wpdb->posts
            WHERE ID = " . $title;
  } else {
    $sql = "SELECT ID, post_title
            FROM $wpdb->posts
            WHERE post_type = 'page'
            AND post_title LIKE '%" . $title . "%'";
  }

  $pages = $wpdb->get_results($sql);

  $onchange = '';
  if ($update) {
    $onchange = 'onchange="'.$update.'(\''.$id.'\', \''.$num.'\')"';
  }

  $list = '<select '.$onchange.' id=\'select_' . $id . $num . '\'">';
  $list .= '<option value="-1">' . __(" -- Välj sida i listan -- ") . '</option>';
  foreach ($pages as $page) {
    $list .= '<option value="' . $page->ID . '">';
    $list .= $page->post_title . ' (' . $page->ID . ')';
    $list .= '</option>';
  }
  $list .= '</select>';

  echo $list;
  die();
}

/* Loads pages where post_title has keyword $title */
add_action( 'wp_ajax_load_pages', 'load_pages_callback');
function load_pages_callback() {
  global $wpdb;

  $title     = $_POST['title'];
  $id        = $_POST['id'];
  $name      = $_POST['name'];

  $pages = $wpdb->get_results(
    "SELECT ID, post_title
    FROM $wpdb->posts
    WHERE post_type = 'page'
    AND post_title LIKE '%" . $title . "%'");

  $list = '<select id="' . $id . '" name="' . $name . '">';
  foreach ($pages as $page) {
    $list .= '<option value="' . $page->ID . '">';
    $list .= $page->post_title . ' (' . $page->ID . ')';
    $list .= '</option>';
  }
  $list .= '</select>';

  echo $list;
  die();
}

/* Loads pages where post_title has keyword $title */
add_action( 'wp_ajax_load_pages_rss', 'load_pages_rss_callback');
function load_pages_rss_callback() {
  global $wpdb;
  $title     = $_POST['title'];

  $pages = $wpdb->get_results("SELECT ID, post_title
                               FROM $wpdb->posts
                               WHERE post_type = 'page'
                               AND post_title LIKE '%" . $title . "%'");

  $list = '<select onchange="updateValues();" id="rss_select" name="rss_select">';
  $list .= '<option value="-1">' . __(" -- Välj sida i listan -- ") . '</option>';
  foreach ($pages as $page) {
    $list .= '<option value="' . $page->ID . '">';
    $list .= $page->post_title . ' (' . $page->ID . ')';
    $list .= '</option>';
  }
  $list .= '</select>';

  echo $list;
  die();
}


/* Load all organizers with event ID */
add_action( 'wp_ajax_nopriv_load_event_organizers', 'load_event_organizers_callback' );
add_action( 'wp_ajax_load_event_organizers', 'load_event_organizers_callback' );
function load_event_organizers_callback() {
  $id     = $_POST['id'];
  $result = HelsingborgEventModel::get_organizers_with_event_id($id);
  echo json_encode($result);
  die();
}

/* Load all event times for a certain event ID */
add_action( 'wp_ajax_nopriv_load_event_dates', 'load_event_dates_callback' );
add_action( 'wp_ajax_load_event_dates', 'load_event_dates_callback' );
function load_event_dates_callback() {
  $id     = $_POST['id'];
  $result = HelsingborgEventModel::load_event_times_with_event_id($id);
  echo json_encode($result);
  die();
}

/* Load event types */
add_action( 'wp_ajax_nopriv_load_event_types', 'load_event_types_callback' );
add_action( 'wp_ajax_load_event_types', 'load_event_types_callback' );
function load_event_types_callback() {
  $result = HelsingborgEventModel::load_event_types();
  echo json_encode($result);
  die();
}

/* Load events */
add_action( 'wp_ajax_nopriv_load_events', 'load_events_callback' );
add_action( 'wp_ajax_load_events', 'load_events_callback' );
function load_events_callback() {
  $ids     = $_POST['ids'];
  $result = HelsingborgEventModel::load_events($ids);
  echo json_encode($result);
  die();
}

/* Add AJAX functions for admin. So Event may be changed by users
Note: wp_ajax_nopriv_X is not used, since events cannot be changed by other than logged in users */
/* Function for approving events, returns true if success. */
add_action( 'wp_ajax_approve_event', 'approve_event_callback' );
function approve_event_callback() {
  global $wpdb;
  $id     = $_POST['id'];
  $result = HelsingborgEventModel::approve_event($id);
  die();
}

/* Function for denying events, returns true if success. */
add_action( 'wp_ajax_deny_event',    'deny_event_callback' );
function deny_event_callback() {
  global $wpdb;
  $id     = $_POST['id'];
  $result = HelsingborgEventModel::deny_event($id);

  die();
}

/* Function for saving events, returns true if success. */
add_action( 'wp_ajax_save_event',    'save_event_callback' );
function save_event_callback() {
  global $wpdb;

  $id          = $_POST['id'];
  $type        = $_POST['type'];
  $name        = $_POST['name'];
  $description = $_POST['description'];
  $days        = $_POST['days'];
  $start_date  = $_POST['startDate'];
  $end_date    = $_POST['endDate'];
  $time        = $_POST['time'];
  $units       = $_POST['units'];
  $types       = $_POST['types'];
  $organizer   = $_POST['organizer'];
  $location    = $_POST['location'];
  $imageUrl    = $_POST['imageUrl'];
  $author      = $_POST['author'];
  $days_array  = $_POST['days'];
  $days_array  = explode(',', $days_array);

  // Create event
  $event = array (
    'EventID'         => $id,
    'Name'            => $name,
    'Description'     => $description,
    'Approved'        => $approved,
    'OrganizerID'     => $organizer,
    'Location'        => $location,
    'ExternalEventID' => $external_id,
  );

  // Event types
  $event_types_x  = explode(',', $types);
  $event_types = array();
  foreach ($event_types_x as $type) {
    $new_type = array('Name' => $type);
    array_push($event_types, $new_type);
  }

  // Administration units
  if ($units && !empty($units)){
    $administrations = explode(',', $units);
    foreach($administrations as $unit) {
      $administration_units[] = HelsingborgEventModel::get_administration_id_from_name($unit)->AdministrationUnitID;
    }
  }

  // Image
  $image = null;
  if ($imageUrl) {
    $image = array( 'ImagePath' => $imageUrl, 'Author' => $author);
  }

  // Create time/times
  $event_times = array();
  if (!$end_date) { // Single occurence
    $event_time = array(
      'Date'  => $start_date,
      'Time'  => $time,
      'Price' => 0
    );
    array_push($event_times, $event_time);
  } else { // Must be start and end then
    $dates_array = create_date_range_array($start_date, $end_date);
    $filtered_days = filter_date_array_by_days($dates_array, $days_array);

    foreach($filtered_days as $date) {
      $event_time = array(
        'Date'  => $date,
        'Time'  => $time,
        'Price' => 0
      );
      array_push($event_times, $event_time);
      echo $date;
    }
  }

  HelsingborgEventModel::update_event($event, $event_types, $administration_units, $image, $event_times);

  die();
}
?>
