<?php
/*
Custom class for getting and setting events
 */

class HelsingborgEventModel {

	/**
	 * Loads event without event types
	 * @param  integer $amount                 Number of events to load
	 * @param  string  $administation_unit_ids Administration units
	 * @return object                          Object with selected data
	 */
	public static function load_events_simple($amount = 5, $administation_unit_ids = 0) {
		global $wpdb;

		$events = $wpdb->get_results('SELECT DISTINCT
                                            e.EventID,
                                            e.Name,
                                            e.Description,
                                            e.Link,
                                            e.Location,
                                            et.Date,
                                            et.Time,
                                            i.ImagePath
                                        FROM happy_event e
                                            INNER JOIN happy_event_times et ON e.EventID = et.EventID
                                            INNER JOIN happy_event_administration_unit eau ON e.EventID = eau.EventID
                                            INNER JOIN happy_administration_unit au ON eau.AdministrationUnitID = au.AdministrationUnitID
                                            LEFT JOIN happy_images i ON e.EventID = i.EventID
                                        WHERE
                                            e.Approved = 1
                                            AND et.Date >= CURDATE()
                                            AND eau.AdministrationUnitID IN (' . $administation_unit_ids . ')
                                    ORDER BY et.Date, et.Time ASC LIMIT ' . $amount, OBJECT);

		return $events;
	}

	/**
	 * Loads events with types
	 * @param  string $administation_unit_ids Administartion units to select
	 * @return object                         Object with selected data
	 */
	public static function load_events($administation_unit_ids) {
		global $wpdb;

		// Query events
		$events = $wpdb->get_results('SELECT DISTINCT
                                    		e.EventID,
                                    		e.Name,
                                    		e.Description,
                                            e.Link,
                                    		e.Location,
                                    		et.Date,
                                            et.Time,
                                    		i.ImagePath
                                		FROM happy_event e
                                    		INNER JOIN happy_event_times et ON e.EventID = et.EventID
                                    		INNER JOIN happy_event_administration_unit eau ON e.EventID = eau.EventID
                                    		INNER JOIN happy_administration_unit au ON eau.AdministrationUnitID = au.AdministrationUnitID
                                    		LEFT JOIN happy_images i ON e.EventID = i.EventID
                                		WHERE
                                    		e.Approved = 1
                                    		AND et.Date >= CURDATE()
                                            AND eau.AdministrationUnitID IN (' . $administation_unit_ids . ')
                                		ORDER BY et.Date', OBJECT);

		/*
		$events = $wpdb->get_results('SELECT DISTINCT hE.EventID,
		hE.Name,
		hE.Description,
		hETI.Date,
		hIM.ImagePath,
		hE.Location
		FROM happy_event hE,
		happy_event_times hETI,
		happy_event_administration_unit hEFE,
		happy_administration_unit hFE,
		happy_images hIM
		WHERE
		hE.Approved = 1
		AND hE.EventID = hETI.EventID
		AND hETI.Date >= CURDATE()
		AND hE.EventID = hEFE.EventID
		AND hE.EventID = hIM.EventID
		AND hEFE.AdministrationUnitID = hFE.AdministrationUnitID
		AND hEFE.AdministrationUnitID IN (' . $administation_unit_ids . ')
		ORDER BY hETI.Date', OBJECT);
		 */

		// Loop events to get their event types then add to events object
		foreach ($events as $event) {
			$rows = $wpdb->get_results('SELECT DISTINCT hETG.EventTypesName
                                        FROM happy_event_types_group hETG
                                        WHERE hETG.EventID = ' . $event->EventID, ARRAY_A);

			$event_types = array();
			foreach ($rows as $row) {
				foreach ($row as $key => $value) {
					array_push($event_types, $value);
				}
			}

			$event_types_string = implode(',', $event_types);
			$event->EventTypesName = $event_types_string;
		}

		return $events;
	}

	/**
	 * Loads events by name
	 * @param  string $name Name of the event to load
	 * @return Array       Array of the selected data
	 */
	public static function load_events_with_name($name) {
		global $wpdb;

		$query = 'SELECT DISTINCT
                        he.EventID,
                        he.Name,
                        MIN(het.Date) AS Date
                    FROM
                        happy_event he,
                        happy_event_times het,
                        happy_event_administration_unit hefe
                    WHERE
                        het.Date >= CURDATE()
                        AND he.Approved = 1
                        AND he.EventID = het.EventID
                        AND hefe.EventID= he.EventID
                        AND LOWER(he.Name) LIKE "%' . strtolower($name) . '%"
                    Group by he.EventID, he.Name
                    ORDER BY Date, he.EventID';

		$events = $wpdb->get_results($query, ARRAY_A);

		if (!$events || empty($events)) {
			$events = array();
		}

		return $events;
	}

	/**
	 * Gets administartion units by id
	 * @param  integer $happy_user_id ID to load
	 * @return array                  The loaded data
	 */
	public static function get_administration_units_by_id($happy_user_id) {
		global $wpdb;

		$administration_units = array();
		$units = $wpdb->get_results('SELECT DISTINCT AdministrationUnitID FROM happy_user_administration_unit WHERE UserID IN(' . $happy_user_id . ') ORDER BY AdministrationUnitID', ARRAY_A);

		foreach ($units as $unit) {
			$administration_units[] = $unit['AdministrationUnitID'];
		}

		$administration_units = implode(',', $administration_units);
		return $administration_units;
	}

	/**
	 * Get all EventID:s with given administration unit
	 * @param  string $administration_units Administartion units ids to look for
	 * @return array                        Selected data
	 */
	public static function get_event_ids_from_administration_unit_id($administration_units) {
		global $wpdb;
		$event_ids = $wpdb->get_results('SELECT EventID FROM happy_event_administration_unit WHERE AdministrationUnitID IN (' . $administration_units . ')', ARRAY_A);
		return $event_ids;
	}

	/**
	 * Loads all unpublished events
	 * @param  integer $happy_user_id Happy user id
	 * @return array                  The selected data
	 */
	public static function load_unpublished_events($happy_user_id) {
		global $wpdb;

		// If no happy_user_id is given -> escape
		if ($happy_user_id == -1) {
			return; // Escape
		}

		// Get administration units by happy_user_id
		$administration_units = self::get_administration_units_by_id($happy_user_id);

		if (strpos($administration_units, '0') !== false) {
			$and_units = '';
		} else {
			$and_units = 'AND hefe.AdministrationUnitID IN (' . $administration_units . ')';
		}

		// Query
		$query = 'SELECT DISTINCT
                        he.EventID, he.Name,
                        MIN(het.Date) AS Date
                    FROM
                        happy_event he,
                        happy_event_times het,
                        happy_event_administration_unit hefe
                    WHERE
                        het.Date >= CURDATE()
                        AND he.Approved = 0
                        AND he.EventID = het.EventID
                        AND hefe.EventID= he.EventID
                        ' . $and_units . '
                    Group by he.EventID, he.Name
                    ORDER BY Date, he.EventID';

		$events = $wpdb->get_results($query, ARRAY_A);

		// Return empty array if no events where found in db
		if (!$events || empty($events)) {
			$events = array();
		}

		return $events;
	}

	/**
	 * Loads an event with a specific EventID
	 * @param  integer $event_id The EventID of the event
	 * @return object            The event
	 */
	public static function load_event_with_event_id($event_id) {
		global $wpdb;

		$events = 'SELECT
                        hE.Name,
                        hE.Description,
                        hE.Link,
                        hETI.Date,
                        hETI.Time,
                        hE.Location
                    FROM
                        happy_event_times hETI,
                        happy_event hE
                    WHERE
                        hE.EventID = hETI.EventID
                        AND hE.EventID = ' . $event_id;

		return $wpdb->get_results($events, OBJECT)[0];
	}

	/**
	 * Loads the administration units of an event with specific EventID
	 * @param  integer $event_id The EventID
	 * @return object            The selected data
	 */
	public static function get_units_with_event_id($event_id) {
		global $wpdb;

		$units = 'SELECT
                        hFE.Name,
                        hFE.AdministrationUnitID
                    FROM
                        happy_event hE,
                        happy_event_administration_unit hEFE,
                        happy_administration_unit hFE
                    WHERE
                        hE.EventID = hEFE.EventID
                        AND hEFE.AdministrationUnitID = hFE.AdministrationUnitID
                        AND hE.EventID = ' . $event_id;

		return $wpdb->get_results($units, OBJECT);
	}

	/**
	 * Load event types from event with speicfic EventID
	 * @param  integer $event_id The EventID
	 * @return object            The selected data
	 */
	public static function get_event_types_with_event_id($event_id) {
		global $wpdb;

		$event_types = 'SELECT
                            hETG.EventTypesName
                        FROM
                            happy_event_types_group hETG,
                            happy_event hE
                        WHERE
                            hE.EventID = hETG.EventID
                            AND hE.EventID = ' . $event_id;

		return $wpdb->get_results($event_types, OBJECT);
	}

	/**
	 * Loads organizers from event with specific EventID
	 * @param  integer $event_id The EventID
	 * @return array             The selected data
	 */
	public static function get_organizers_with_event_id($event_id) {
		global $wpdb;

		$organizer = 'SELECT
                            hArr.Name,
                            hArr.OrganizerID
                        FROM
                            happy_organizers hArr,
                            happy_event hE
                        WHERE
                            hE.OrganizerID = hArr.OrganizerID
                            AND hE.EventID = ' . $event_id;

		return $wpdb->get_results($organizer, ARRAY_A);
	}

	/**
	 * Loads the image of an event with specific EventID
	 * @param  integer $event_id The EventID
	 * @return object            The image object
	 */
	public static function get_image_with_event_id($event_id) {
		global $wpdb;

		$image = 'SELECT
                        hB.Author,
                        hB.ImageID,
                        hb.ImagePath
                    FROM
                        happy_images hB,
                        happy_event hE
                    WHERE
                        hE.EventID = hB.EventID
                        AND hE.EventID = ' . $event_id . '
                    ORDER BY hB.ImageID DESC
                    LIMIT 1';

		return $wpdb->get_results($image, OBJECT)[0];
	}

	/**
	 * Gets the url of an image with a specific EventID
	 * @param  integer $event_id The EventID
	 * @return object            The image path
	 */
	public static function get_event_image_url($event_id = -1) {
		$image_path = $wpdb->get_results('SELECT ImagePath
                                            FROM happy_images
                                            WHERE EventID = ' . $event_id, OBJECT);
		return $image_path;
	}

	/**
	 * Loads event types
	 * @return object The event types
	 */
	public static function load_event_types() {
		global $wpdb;

		$result_event_types = $wpdb->get_results('SELECT Name EventTypesName
                                                    FROM happy_event_types
                                                    ORDER BY Name', OBJECT);

		foreach ($result_event_types as $key => $value) {
			$result_event_types[$key]->ID = $key;
		}

		return $result_event_types;
	}

	/**
	 * Loads administartion units
	 * @return object The administartion units
	 */
	public static function load_administration_units() {
		global $wpdb;

		$happy_administration_units = $wpdb->get_results('SELECT Name
                                                            FROM happy_administration_unit
                                                            ORDER BY Name ASC', OBJECT);

		return $happy_administration_units;
	}

	/**
	 * Loads organizers
	 * @return object The organizers
	 */
	public static function load_organizers() {
		global $wpdb;

		$result_event_types = $wpdb->get_results('SELECT *
                                                    FROM happy_organizers
                                                    ORDER BY Name', OBJECT);

		return $result_event_types;
	}

	/**
	 * Loads organizer values
	 * @param  integer $organizerId The OrganizerID
	 * @return object               The selected data
	 */
	public static function load_organizer_values($organizerId = -1) {
		global $wpdb;

		// Escape
		if ($organizer == -1) {
			return;
		}

		$result_event_types = $wpdb->get_results('SELECT heO.Phone, heO.Email, heO.WebAddress
                                                    FROM happy_organizers AS heO
                                                    WHERE heO.OrganizerID = ' . $organizerId, OBJECT);

		return $result_event_types;
	}

	/**
	 * Gets the event times
	 * @param  integer $event_id The EventID
	 * @return object           The event times
	 */
	public static function load_event_times_with_event_id($event_id) {
		global $wpdb;

		$result_times = $wpdb->get_results('SELECT *
                                            FROM happy_event_times
                                            WHERE EventID = ' . $event_id, OBJECT);

		return $result_times;
	}

	/**
	 * Gets administarion id from a administration unit name
	 * @param  string $name Unit name
	 * @return object       Unit id
	 */
	public static function get_administration_id_from_name($name) {
		global $wpdb;

		$result_id = $wpdb->get_results("SELECT *
                                        FROM happy_administration_unit
                                        WHERE Name = '" . $name . "'", OBJECT);
		return $result_id[0];
	}

	/**
	 * Stores a new event to the db
	 * @param  array $event                 The event data
	 * @param  string $event_types          Event types
	 * @param  string $administration_units Administkartion units
	 * @param  string $image                The image
	 * @param  array $times                 Time
	 * @return void
	 */
	public static function create_event($event, $event_types, $administration_units, $image, $times) {
		global $wpdb;

		// Insert Event
		$wpdb->insert('happy_event', array(
            'Name'            => $event['Name'],
            'Description'     => $event['Description'],
            'Link'            => $event['Link'],
            'Approved'        => $event['Approved'],
            'OrganizerID'     => $event['Organizer_id'],
            'Location'        => $event['Location'],
            'ExternalEventID' => $event['External_id'])
		);

		// Get the AUTO_INCREMENTED value and set to our event.
		$event['EventID'] = $wpdb->insert_id;

		// Add Event Types
		if ($event_types) {
			foreach ($event_types as $event_type) {
				$wpdb->insert('happy_event_types_group', array(
                    'EventTypesName' => $event_type,
                    'EventID'        => $event['EventID'])
				);
			}
		}

		// Add Administration Unit
		if ($administration_units) {
			foreach ($administration_units as $administration_unit) {
				$wpdb->insert('happy_event_administration_unit', array(
                    'AdministrationUnitID' => $administration_unit,
                    'EventID'              => $event['EventID'])
				);
			}
		}

		// Add Image
		if ($image) {
			$wpdb->insert('happy_images', array(
                'EventID'   => $event['EventID'],
                'ImagePath' => $image['ImagePath'],
                'Author'    => $image['Author'])
			);
		}

		// Add time for event
		if ($times) {
			foreach ($times as $time) {
				$wpdb->insert('happy_event_times', array(
                    'Date'    => $time['Date'],
                    'Time'    => $time['Time'],
                    'Price'   => $time['Price'],
                    'EventID' => $event['EventID'])
				);
			}
		}
	}

	/**
	 * Approves a specific event
	 * @param integer $event_id  The EventID to approve
	 * @return bool The query result
	 */
	public static function approve_event($event_id) {
		global $wpdb;

		// Cannot proceed if no id was inserted
		if (!$event_id || $event_id == -1) {
			return FALSE;
		}

		$result = $wpdb->update('happy_event', // Table
            array('Approved' => 1), // Update row
            array('EventID'  => $event_id) // Where
		);

		return $result;
	}

	/**
	 * Denies a specific event
	 * @param  integer $event_id The EventID to deny
	 * @return bool              The query result
	 */
	public static function deny_event($event_id) {
		global $wpdb;

		// Cannot proceed if no id was inserted
		if (!$event_id || $event_id == -1) {
			return FALSE;
		}

		$result_update = $wpdb->update('happy_event', // Table
            array('Approved' => 0), // Update row
            array('EventID'  => $event_id)
        ); // Where

		// Delete current set of times for this event
		$wpdb->delete('happy_event_administration_unit', array('EventID' => $event_id));

		// Add the new time with
		$result_insert = $wpdb->insert('happy_event_times', array(
            'Date'    => '1968-10-24',
            'Time'    => '00:00',
            'Price'   => 0,
            'EventID' => $event_id)
		);

		return ($result_update && $result_insert);
	}

	/**
	 * Updates an event in the database
	 * @param  array &$event                The event
	 * @param  string $event_types          The event types
	 * @param  string $administration_units The administration units
	 * @param  string $image                The image
	 * @param  array $times                 The time
	 * @return viod                       The result from db
	 */
	public static function update_event(&$event, $event_types, $administration_units, $image, $times) {
		global $wpdb;

		if (!$event || !$event['EventID']) {
			return;
		}

		$wpdb->update("happy_event", array(
            'Name'            => $event['Name'],
            'Description'     => $event['Description'],
            'Link'            => $event['Link'],
            'Approved'        => $event['Approved'],
            'OrganizerID'     => $event['Organizer_id'],
            'Location'        => $event['Location'],
            'ExternalEventID' => $event['External_id'],
		), array(
            'EventID' => $event['EventID'],
		)
		);

		// Add Event Types
		if ($event_types && !empty($event_types)) {
			// Delete the current set of types for this event
			$wpdb->delete('happy_event_types_group', array('EventID' => $event['EventID']));

			// Now add the new ones
			foreach ($event_types as $event_type) {
				$wpdb->insert('happy_event_types_group', array(
                    'EventTypesName' => $event_type['Name'],
                    'EventID'        => $event['EventID'])
				);
			}
		}

		// Add Administration Unit
		if ($administration_units && count($administration_units) > 0) {
			// Delete current set of units for this event
			$wpdb->delete('happy_event_administration_unit', array('EventID' => $event['EventID']));

			// Add the new ones
			foreach ($administration_units as $administration_unit) {
				$wpdb->insert('happy_event_administration_unit', array(
                    'AdministrationUnitID' => $administration_unit['ID'],
                    'EventID'              => $event['EventID'])
				);
			}
		}

		// Add Image
		if ($image) {
			$wpdb->update('happy_images', array(
                'ImageID'   => date('Y-m-d H:i:s'),
                'ImagePath' => $image['ImagePath'],
                'Author'    => $image['Author'],
			), array('EventID' => $event['EventID'])
			);
		}

		// Add time for event
		if ($times) {
			// Delete current set of times for this event
			$wpdb->delete('happy_event_times', array('EventID' => $event['EventID']));

			// Add the new times
			foreach ($times as $time) {
				$wpdb->insert('happy_event_times', array(
                    'Date'    => $time['Date'],
                    'Time'    => $time['Time'],
                    'Price'   => $time['Price'],
                    'EventID' => $event['EventID'],
				));
			}
		}
	}
}
?>
