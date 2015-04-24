<?php
// används i page-samling.php
function checkforbookingwidget($sidebars_widgetsUnserialized){
	foreach ($sidebars_widgetsUnserialized as $dd) {
		if($dd != null){
			foreach ($dd as $d) {										
				$string = 'hbgbookingwidget';
				if (stripos($d,$string) !== false) {
				    $data = $d;    
					$widget_id = substr($data, strpos($data, "-") + 1);    
				    return $widget_id;
				} 	
			}
		}
	}
}

// används i page-samling.php
function shorten_Post_Content($string, $link) {
	if (strlen($string) > 200) {
	    $stringCut = substr($string, 0, 200);
	    $string = '<a class="clickable_excerpt_text" href="'.$link.'">'.substr($stringCut, 0, strrpos($stringCut, ' ')).'...</a> <a href="'.$link.'">Läs mer</a>'; 
	}else {
		$string = '<a class="clickable_excerpt_text" href="'.$link.'">'.$string.'...</a><a href="'.$link.'">Läs mer</a>';
	}
	return $string;
}

/*
Handles month/year increment calculations in a safe way,
avoiding the pitfall of 'fuzzy' month units.

Returns a DateTime object with incremented month values, and a date value == 1.
*/
function incrementDate($startDate, $monthIncrement = 0) {

    $startingTimeStamp = $startDate->getTimestamp();
    // Get the month value of the given date:
    $monthString = date('Y-m', $startingTimeStamp);
    // Create a date string corresponding to the 1st of the give month,
    // making it safe for monthly calculations:
    $safeDateString = "first day of $monthString";
    // Increment date by given month increments:
    $incrementedDateString = "$safeDateString $monthIncrement month";
    $newTimeStamp = strtotime($incrementedDateString);
    $newDate = DateTime::createFromFormat('U', $newTimeStamp);
    return $newDate;
}

function appendLeadingZero($num) {
	$num_padded = sprintf("%02d", $num);
	return $num_padded;
}

?>