<?php

$id          = $_POST['id'];
$type        = $_POST['type'];
$name        = $_POST['name'];
$description = $_POST['description'];
$link        = $_POST['link'];
$single_date = $_POST['singleDate'];
$start_date  = $_POST['startDate'];
$end_date    = $_POST['endDate'];
$time        = $_POST['time'];
$units       = $_POST['units'];
$types       = $_POST['types'];
$organizer   = $_POST['organizer'];
$location    = $_POST['location'];
$imageUrl    = $_POST['imageUrl'];
$author      = $_POST['author'];


// Create event
$event = array (
  'EventID'         => $id,
  'Name'            => $name,
  'Description'     => $description,
  'Approved'        => $approved,
  'OrganizerID'     => $organizer,
  'Location'        => $location,
  'ExternalEventID' => $external_id
);

// Event types
$event_types  = explode(',', $types);

// Administration units
$administrations = explode(',', $units);
foreach($administrations as $unit) {
  $administration_units[] = HelsingborgEventModel::get_administration_id_from_name($unit)->AdministrationUnitID;
}

// Image
if ($imageUrl)
  $image = array( 'ImagePath' => $imageUrl, 'Author' => $author);

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

// HelsingborgEventModel::update_event($event, $event_types, $administration_units, $image, $times)

echo $type;
?>
