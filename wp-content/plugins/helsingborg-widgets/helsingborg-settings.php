<?php

function helsingborg_panel(){
  add_menu_page('Helsingborg',                  // Page title
                'Helsingborg',                  // Menu title
                'read_private_pages',           // Capability
                'helsingborg',                  // Slug
                'helsingborg_panel_func');      // Function

  add_submenu_page( 'helsingborg',                           // Parent slug
                    'Nya evenemang',                         // Page title
                    'Nya evenemang',                         // Menu title
                    'read_private_pages',                    // Capability
                    'helsingborg-eventhandling',             // Slug
                    'helsingborg_panel_func_eventhandling'); // Function

  add_submenu_page( 'helsingborg',                           // Parent slug
                    'Sök evenemang',                 // Page title
                    'Sök evenemang',                 // Menu title
                    'read_private_pages',                    // Capability
                    'helsingborg-eventsearch',               // Slug
                    'helsingborg_panel_func_eventsearch'); // Function

  add_submenu_page( 'helsingborg',
                    'Inställningar',
                    'Inställningar',
                    'read_private_pages',
                    'helsingborg-settings',
                    'helsingborg_panel_func_settings');

  // Create the page that loads the details for an event, dynamically built inside
  add_submenu_page( null,  // null makes the page accessible, but hidden in menu
                    'Ändra evenemang',
                    'Ändra evenemang',
                    'read_private_pages',
                    'helsingborg-event-details',
                    'helsingborg_panel_func_event_details');
}
add_action('admin_menu', 'helsingborg_panel');

function helsingborg_panel_func(){
  echo '<div class="wrap"><div id="icon-options-general" class="icon32"><br></div>
  <h2>Helsingborg</h2></div>';
}
function helsingborg_panel_func_eventhandling(){
  include('classes/helsingborg-event-list-table.php');
  include('includes/panel_eventhandling.php');
}
function helsingborg_panel_func_eventsearch(){
  include('classes/helsingborg-event-search-table.php');
  include('includes/panel_eventsearch.php');
}
function helsingborg_panel_func_settings(){
  include('pages/settings-page.php');
}
function helsingborg_panel_func_event_details(){
  include('pages/event-page.php');
}
?>
