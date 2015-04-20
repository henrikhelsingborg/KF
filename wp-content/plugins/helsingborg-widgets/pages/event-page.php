<?php

// Get the EventID
$event_id = $_GET['id'];

// Get Event with retrieved ID
$event                = HelsingborgEventModel::load_event_with_event_id($event_id);
$image                = HelsingborgEventModel::get_image_with_event_id($event_id);
$times                = HelsingborgEventModel::load_event_times_with_event_id($event_id);

// Get previous selections
$selected_units       = HelsingborgEventModel::get_units_with_event_id($event_id);
$selected_event_types = HelsingborgEventModel::get_event_types_with_event_id($event_id);
$selected_organizer   = HelsingborgEventModel::get_organizers_with_event_id($event_id);

// Get all values for our selects
$administration_units = HelsingborgEventModel::load_administration_units();
$organizers           = HelsingborgEventModel::load_organizers();
$event_types          = HelsingborgEventModel::load_event_types();

// Setup times that was selected
$number_of_dates      = count($times);
$checked_days         = array();
if ($number_of_dates > 1) {
  foreach($times as $time) {
    $day = date('N', strtotime($time->Date));
    if(!in_array($day, $checked_days))
      $checked_days[] = $day;
  }
}

$start_date = $times[0];
$end_date   = $number_of_dates > 1 ? $times[$number_of_dates - 1] : null;

?>

<script src="<?php echo get_stylesheet_directory_uri() ; ?>/js/jquery/dist/jquery-ui.min.js"></script>
<script src="<?php echo get_stylesheet_directory_uri() ; ?>/js/foundation-multiselect/zmultiselect/zurb5-multiselect.js"></script>
<link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri() ; ?>/bower_components/foundation-multiselect/zmultiselect/zurb5-multiselect.css">
<link rel="stylesheet" href="<?php echo plugins_url(); ?>/helsingborg-widgets/css/helsingborg-admin.css">

<div class="wrap"><div id="icon-options-general" class="icon32"><br></div>
<h2>Hantering av evenemang</h2>

<br>
<input type="submit" class="button" onclick="location.href='<?php echo site_url(); ?>/wp-admin/admin.php?page=helsingborg-eventhandling'" value="Tillbaks till listan">
<br>

<form><fieldset>

<label for="e_name">Namn</label><br>
<textarea id="e_name" class="large-text" maxlength="255" rows="1"><?php echo htmlentities($event->Name); ?></textarea>

<br><br>

<label for="e_description">Beskrivning</label>
<textarea id="e_description" class="large-text" maxlength="5000" rows="5"><?php echo htmlentities($event->Description); ?></textarea><br><br>

<label for="e_link">Länk</label>
<input type="text" id="e_link" value="<?php echo htmlentities($event->Link); ?>"><br><br>

<table width="100%">
  <tr>
    <th>Datum från</th>
    <th>Datum till</th>
    <th>Tid</th>
  </tr>
  <tr>
    <td><input id="e_start_date" type="Date" value="<?php if($start_date) {echo $start_date->Date;} ?>"></input></td>
    <td><input id="e_end_date" type="Date" value="<?php if($end_date) {echo $end_date->Date;} ?>"></input></td>
    <td><input id="e_time" type="Time" value="<?php echo $times[0]->Time; ?>"></input></td>
  </tr>
</table>
<br>
<table width="100%">
  <tr>
    <td><input type="checkbox" id="e_cb1" <?php if(in_array('1', $checked_days)){echo 'checked';} ?> value="1" name="days[]">Måndag</input></td>
    <td><input type="checkbox" id="e_cb2" <?php if(in_array('2', $checked_days)){echo 'checked';} ?> value="2" name="days[]">Tisdag</input></td>
    <td><input type="checkbox" id="e_cb3" <?php if(in_array('3', $checked_days)){echo 'checked';} ?> value="3" name="days[]">Onsdag</input></td>
    <td><input type="checkbox" id="e_cb4" <?php if(in_array('4', $checked_days)){echo 'checked';} ?> value="4" name="days[]">Torsdag</input></td>
    <td><input type="checkbox" id="e_cb5" <?php if(in_array('5', $checked_days)){echo 'checked';} ?> value="5" name="days[]">Fredag</input></td>
    <td><input type="checkbox" id="e_cb6" <?php if(in_array('6', $checked_days)){echo 'checked';} ?> value="6" name="days[]">Lördag</input></td>
    <td><input type="checkbox" id="e_cb7" <?php if(in_array('7', $checked_days)){echo 'checked';} ?> value="7" name="days[]">Söndag</input></td>
  </tr>
</table>

<br>

<table width="100%">
  <tr>
    <th width="50%">Enhet</th>
    <th width="25%">Typ av evenemang</th>
    <th width="25%">Arrangör</th>
  </tr>
  <tr>
    <td>
      <select id="e_units">
        <?php
        foreach($administration_units as $administration_unit) {
          $selected = '';
          foreach($selected_units as $unit){
            if ($administration_unit->Name == $unit->Name) {
              $selected = 'data-selected selected';
              break;
            }
          }
          echo('<option value="' . $administration_unit->Name . '" ' . $selected . '>'. $administration_unit->Name . '</option>');
        }
        ?>
      </select>
    </td>
    <td>
      <select id="e_types">
        <?php
        foreach($event_types as $event_type) {
          $selected = '';
          foreach($selected_event_types as $selected_event){
            if ($event_type->EventTypesName == $selected_event->EventTypesName) {
              $selected = 'data-selected';
              break;
            }
          }
          echo('<option value="' . $event_type->EventTypesName . '" ' . $selected . '>'. $event_type->EventTypesName . '</option>');
        }
        ?>
      </select>
    </td>
    <td>
      <select id="e_organizer">
        <?php
        foreach($organizers as $organizer) {
          echo('<option value="' . $organizer->Name . '">'. $organizer->Name . '</option>');
        }
        ?>
      </select>
    </td>
  </tr>
</table>

<label for="e_location">Plats</label><br>
<textarea id="e_location" class="large-text" maxlength="255" rows="1"><?php echo htmlentities($event->Location); ?></textarea>

<br>

<table width="100%">
  <tr>
    <th width="75%">Bild</th>
    <th width="25%">Upphovsrätt/Copyright</th>
  </tr>
  <tr>
    <td>
      <?php if (!$image) : ?>
        Ingen bild vald<br>
      <?php else : ?>
        <img id="e_image" src="<?php echo $image->ImagePath; ?>"><br>
      <?php endif; ?>
      <input type="text" name="imageUrl" value="<?php echo $image->ImagePath; ?>">
    </td>
    <td><input id="e_autor" type="Text" value="<?php echo $image->Author; ?>" name="author"></td>
  </tr>
</table>

</fieldset></form>
</div>

<input type="text" id="e_selected_units" name="units" style="display: none;"/>
<input type="text" id="e_selected_types" name="types" style="display: none;"/>

<br>
<br>

<ul class="button-group round even-3">
  <li><input type="submit" class="button success" onclick="approveEvent()" value="Godkänn"></li>
  <li><input type="submit" class="button button"  onclick="saveEvent()" value="Spara"></li>
  <li><input type="submit" class="button alert"   onclick="denyEvent()" value="Neka"></li>
</ul>

<script>
function approveEvent() {

    var approveDone = false;
    var saveDone = false;

    // Save
    var dataSave = {
      action: 'save_event',
      id: <?php echo $event_id; ?>,
      name: jQuery("#e_name").val(),
      description: jQuery("#e_description").val(),
      link: jQuery("#e_link").val(),
      startDate: jQuery("#e_start_date").val(),
      endDate: jQuery("#e_end_date").val(),
      time: jQuery("#e_time").val(),
      days: getCheckedDays(),
      units: jQuery("#e_selected_units").val(),
      types: jQuery("#e_selected_types").val(),
      organizer: jQuery("#e_organizer").val(),
      location: jQuery("#e_location").val(),
      imageUrl: jQuery("[name=imageUrl]").val(),
      author: jQuery("#e_autor").val(),
    };

    if (confirm('Är du säker på du vill godkänna?')){
      jQuery.post(ajaxurl, dataSave, function(response) {
        saveDone = true;

        // Approve
        var data = {
          action: 'approve_event',
          id: <?php echo $event_id; ?>
        };

        jQuery.post(ajaxurl, data, function(response) {
          window.location.replace("<?php echo site_url(); ?>/wp-admin/admin.php?page=helsingborg-eventhandling");
        });
      });
    }
}

function denyEvent(){
  var data = {
    action: 'deny_event',
    id: <?php echo $event_id; ?>
  };

  if (confirm('Är du säker på du vill neka detta event?')){
    jQuery.post(ajaxurl, data, function(response) {
      window.location.replace("<?php echo site_url(); ?>/wp-admin/admin.php?page=helsingborg-eventhandling");
    });
  }
}

function saveEvent() {
  var data = {
    action: 'save_event',
    id: <?php echo $event_id; ?>,
    name: jQuery("#e_name").val(),
    description: jQuery("#e_description").val(),
    link: jQuery("#e_link").val(),
    startDate: jQuery("#e_start_date").val(),
    endDate: jQuery("#e_end_date").val(),
    time: jQuery("#e_time").val(),
    days: getCheckedDays(),
    units: jQuery("#e_selected_units").val(),
    types: jQuery("#e_selected_types").val(),
    organizer: jQuery("#e_organizer").val(),
    location: jQuery("#e_location").val(),
    imageUrl: jQuery("[name=imageUrl]").val(),
    author: jQuery("#e_autor").val(),
  };

  if (confirm('Är du säker på du vill spara?')){
    jQuery.post(ajaxurl, data, function(response) {
      if (redirect !== undefined) window.location.replace("<?php echo site_url(); ?>/wp-admin/admin.php?page=helsingborg-eventhandling");
    });
  }
}

function getCheckedDays() {
  var values = [];
  for(var i=1; i<=7; i++) {
    jQuery('#e_cb' + i).is(":checked") ? values.push(i.toString()) : '';
  }
  return values.join();
}
</script>

<script>
jQuery(document).ready(function() {
  jQuery("select#e_units").zmultiselect({
    live: "#e_selected_units",
    filter: true,
    filterPlaceholder: 'Filtrera...',
    filterResult: true,
    filterResultText: "Visar",
    selectedText: ['Valt','av'],
    selectAll: true,
    selectAllText: ['Markera alla','Avmarkera alla']
  });
  jQuery("select#e_types").zmultiselect({
    live: "#e_selected_types",
    filter: true,
    filterPlaceholder: 'Filtrera...',
    filterResult: true,
    filterResultText: "Visar",
    selectedText: ['Valt','av'],
    selectAll: true,
    selectAllText: ['Markera alla','Avmarkera alla']
  });
});
</script>

<?php
?>
