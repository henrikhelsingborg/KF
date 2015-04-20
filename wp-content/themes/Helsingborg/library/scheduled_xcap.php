<?php
/*
 * XCAP scheduled event. Loads events from mittkulturkort.se and adds to DB
 * This should occur each day at 22.30, so new events are added in our
 * database as external events. This file is included from functions.php
 */

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
    $address     = $event->{'x-xcap-address'};
    $imageid     = $event->{'x-xcap-imageid'};

    if (strpos($categories,'pyssel')) { $type = 'Pyssel'; }
    if (strpos($categories,'kultur')) { $type = 'Kultur'; }
    if (strpos($time, '24')) { $time = '00'; }

    // Format the date string corretly
    $dateParts = explode("T", $event->dtstart);
    $dateString = substr($dateParts[0], 0, 4) . '-' . substr($dateParts[0], 4, 2) . '-' . substr($dateParts[0], 6, 2);
    $timeString = substr($dateParts[1], 0, 4);
    $timeString = substr($timeString, 0, 2) . ':' . substr($timeString, 2, 2);
    $dateString = $dateString . ' ' . $timeString;

    // Create UTC date object
    $date = new DateTime(date('Y-m-d H:i', strtotime($dateString)));
    $timeZone = new DateTimeZone('Europe/Stockholm');
    $date->setTimezone($timeZone); // Set timezone to stockholm

    // Insert event to our DB as external event
    $wpdb->insert('happy_external_event',
                   array(
                     'ID' => $id,
                     'Name' => $name,
                     'Status' => $status,
                     'Description' => $description,
                     'EventType' => $type,
                     'Date' => $date->format('Y-m-d'),
                     'Time' => $date->format('H:i'),
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
