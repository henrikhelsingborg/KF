<?php
/*
 * XCAP scheduled event. Loads events from mittkulturkort.se and adds to DB
 * This should occur each day at 22.30, so new events are added in our
 * database as external events. This file is included from functions.php
 */

/* Setup the scheduled task */
add_action( 'wp', 'setup_scheduled_xcap' );
function setup_scheduled_xcap() {
  if ( ! wp_next_scheduled( 'scheduled_xcap' ) ) {
    // Set scheduled task to occur at 22.30 each day
    // wp_schedule_event( strtotime(date("Y-m-d", time()) . '22:30'), 'daily', 'scheduled_xcap');

    // Temporary for test purpose
    wp_schedule_event( time(), '3min', 'scheduled_xcap');
  }
}

/* Function to execute as event, from setup above */
add_action( 'scheduled_xcap', 'xcap_event' );
function xcap_event() {
  global $wpdb;

  // Load the external data
  $url = "http://mittkulturkort.se/calendar/listEvents.action?month=&date=&categoryPermaLink=&q=&p=&feedType=ICAL_XML";
  $xml = simplexml_load_file($url);

  /* Step 1: Delete all from external events coming from mittkulturkort */
  $delete_query = "DELETE FROM happy_external_event WHERE ImageID LIKE '%mittkulturkort%'";
  $result = $wpdb->get_results($delete_query);

  /* Step 2: Go through new events and add do database */
  // Loop through each event
  foreach($xml->iCal->vevent as $event) {
    $id          = intval(substr($event->uid, strripos($event->uid, '-') + 1));
    $status      = 'Active';
    $type        = 'Övrigt';
    $name        = $event->summary;
    $description = $event->description;
    $categories  = $event->categories;
    $time        = substr($event->dtstart, 9, 2);
    $address     = $event->{'x-xcap-address'};
    $imageid     = $event->{'x-xcap-imageid'};

    if (strpos($categories,'pyssel')) { $type = 'Pyssel'; }
    if (strpos($categories,'kultur')) { $type = 'Kultur'; }
    if (strpos($time, '24')) { $time = '00'; }

    $time  = $time . ':' . substr($event->dtstart, 11, 2);
    $start = substr($event->dtstart, 0, 4) . '-' . substr($event->dtstart, 4, 2) . '-' . substr($event->dtstart, 6, 2);

    // Insert event to our DB as external event
    $wpdb->insert('happy_external_event',
                   array(
                     'ID' => $id,
                     'Name' => $name,
                     'Status' => $status,
                     'Description' => $description,
                     'EventType' => $type,
                     'Date' => $start,
                     'Time' => $time,
                     'Location' => $address,
                     'ImageID' => $imageid
                   ),
                   array(
                     '%d',
                     '%s',
                     '%s',
                     '%s',
                     '%s',
                     '%s',
                     '%s',
                     '%s',
                     '%s'
                   )
                 );
  }
}
?>
