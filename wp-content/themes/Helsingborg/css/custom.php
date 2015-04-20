<?php

header("Content-type: text/css");
$color = get_field('color_code');

?>

.container {
  background: <?php echo $color; ?>;
}
