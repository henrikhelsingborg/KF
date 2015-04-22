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

?>	