<?php
// Get basic values
$header_image_title                   = get_option('helsingborg_header_image_title');
$header_image_imageurl                = get_option('helsingborg_header_image_imageurl');

// No need to check further if no image is set
if ((isset($header_image_imageurl)) && ($header_image_imageurl != '')) {
  $header_image_alt                     = get_option('helsingborg_header_image_alt');
  $header_image_item_force_width        = get_option('helsingborg_header_image_item_force_width');
  $header_image_item_force_margin       = get_option('helsingborg_header_image_item_force_margin');
  $header_image_item_force_margin_value = get_option('helsingborg_header_image_item_force_margin_value');

  // See how image should behave
  $fw = $header_image_item_force_width  == 'on' ? ' width:100%;' : '';
  $fm = $header_image_item_force_margin == 'on' ? ' margin-top:-' . $item_force_margin_value . 'px;' : '';

  // Now print the image !
  echo('<div class="row"><div class="large-12 columns header-image">');
    echo('<img class="img-slide" src="' . $header_image_imageurl .
                              '" alt="' . $header_image_alt .
                              '" style="' . $fw . $fm .'" />');
  echo('</div></div>');
}
?>
