<?php
/*
 * CBIS scheduled event. Loads events from CBIS api and adds to DB
 * This should occur each day at 22.30, so new events are added in our
 * database as external events. This file is included from functions.php
 */

/* Function to execute as event, from setup above */
add_action( 'scheduled_cbis', 'cbis_event' );
function cbis_event() {
  global $wpdb;

  // Get the keys from Helsingborg->Settings
  $cbis_api_key     = get_option('helsingborg_cbis_api_key');
  $cbis_hbg_id      = get_option('helsingborg_cbis_hbg_id');
  $cbis_category_id = get_option('helsingborg_cbis_category_id');

  if (!isset($cbis_api_key) || !isset($cbis_hbg_id) || !isset($cbis_category_id)) {
    return; // Escape if no keys has been set !
  }

  // TODO: Go through params, are these correct?!
  $requestParams = array(
      'apiKey' => $cbis_api_key,
      'languageId' => 1,
      'categoryId' => $cbis_category_id,
      'templateId' => 0,
      'pageOffset' => 0,
      'itemsPerPage' => 100,
      'filter' => array(
        'GeoNodeIds' => array( $cbis_hbg_id ),
        'StartDate' => time(),
        'Highlights' => 0,
        'OrderBy' => 'None',
        'SortOrder' => 'None',
        'MaxLatitude' => null,
        'MinLatitude' => null,
        'MaxLongitude' => null,
        'MinLongitude' => null,
        'SubCategoryId' => 0,
        'ProductType' => null,
        'WithOccasionsOnly' => false,
        'ExcludeProductsWithoutOccasions' => false,
        'ExcludeProductsNotInCurrentLanguage' => false,
        'IncludeArchivedProducts' => true,
        'IncludeInactiveProducts' => true,
        'BookableProductsFirst' => false,
        'RandomSortSeed' => 0,
        'ExcludeProductsWhereNameNotInCurrentLanguage' => false,
        'IncludePendingPublish' => true
      )
  );

  // Step 1: Delete all citybreak events
  $delete_query = "DELETE FROM happy_external_event WHERE ImageID LIKE '%citybreak%' OR ImageId = ''";
  $result = $wpdb->get_results($delete_query);

  /* Step 2: Load new events from SOAP api */
  // Setup SOAP and retrieve data
  $client = new SoapClient('http://api.cbis.citybreak.com/Products.asmx?WSDL');
  $response = $client->ListAll($requestParams);
  $description = $introduction = $url = $type = '';

  // Loop through the fetched items and save to DB
  foreach($response->ListAllResult->Items->Product as $key => $product) {
    $title        = $product->Name;

    // Get values if present
    $status       = $product->Status       ?: 'Övrigt';
    $introduction = $product->Introduction ?: '';
    $description  = $product->Description  ?: '';
    $imageid      = $product->Image->Url   ?: '';

    // Get the EventType for event
    foreach ($product->Categories as $attribute) {
      $type = $attribute->Name; // TODO: Why always the last? (from original code)
    }

    // Go through all times for this event
    foreach ($product->Occasions as $occasion) {

      // Make sure the occasion has a startdate !
      if (isset($occasion->StartDate)) {
        $id = $occasion->Id;
        $location = $occasion->ArenaName;

        // Create proper DateTime obj. from string (yyyy-mm-ddThh:mm:ss)
        $date = DateTime::CreateFromFormat('Y-m-d\TH:i:s', $occasion->StartDate);
        $time = DateTime::CreateFromFormat('Y-m-d\TH:i:s', $occasion->StartTime);

        // Now save it to the DB
        $wpdb->insert('happy_external_event',
                       array(
                         'ID' => $id,
                         'Name' => $title,
                         'Status' => $status,
                         'Description' => $product->Description,
                         'EventType' => $type,
                         'Date' => $date->format('Y-m-d'),
                         'Time' => $time->format('H:i'),
                         'Location' => $location,
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
      } // Endif
    } // End Occasions
  } // End Items

  // Now trigger the Store Procedure!
  // TODO: Not the prettiest solution, change when WP support calling SP
  $mysqli    = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
  $procedure = "CALL spInsertIntoHappyEvent();";
  $mysqli->real_query($procedure);
}
?>
