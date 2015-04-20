<?php
if (!class_exists('Image_List')) {
  class Image_List
  {
    /**
     * Constructor
     */
    public function __construct()
    {
      add_action( 'widgets_init', array( $this, 'add_widgets' ) );
    }

    /**
     * Add widget
     */
    public function add_widgets()
    {
      register_widget( 'Image_List_Widget' );
    }
  }
}

if (!class_exists('Image_List_Widget')) {
  class Image_List_Widget extends WP_Widget {

    // Constructor
    function Image_List_Widget() {
      // Register the widget
      parent::WP_Widget(false, '* Bildlistor', array('description' => 'Lägg till de bilder du vill rendera ut.'));
    }

    // This is what to render on the actual page
    public function widget( $args, $instance ) {
      extract($args);

      // Get all the data saved
      $title          = apply_filters('widget_title', empty($instance['title']) ? __('List') : $instance['title']);
      $rss_link       = empty($instance['rss_link'])       ? '#' : $instance['rss_link']; // TODO: Proper default ?
      $show_rss       = empty($instance['show_rss'])       ? 'rss_no' : $instance['show_rss'];
      $show_placement = empty($instance['show_placement']) ? 'show_in_sidebar' : $instance['show_placement'];
      $show_dates     = isset($instance['show_dates'])     ? $instance['show_dates'] : false;
      $amount         = empty($instance['amount'])         ? 1 : $instance['amount'];

      // Retrieved all links
      for ($i = 1; $i <= $amount; $i++) {
        $items[$i-1]                    = $instance['item'.$i];
        $item_links[$i-1]               = $instance['item_link'.$i];
        $item_targets[$i-1]             = isset($instance['item_target'.$i]) ? $instance['item_target'.$i] : false;
        $item_ids[$i-1]                 = $instance['item_id'.$i];
        $item_attachement_id[$i-1]      = $instance['attachment_id'.$i];
        $item_imageurl[$i-1]            = $instance['imageurl'.$i];
        $item_alts[$i-1]                = $instance['alt'.$i];
        $item_texts[$i-1]               = $instance['item_text'.$i];
        $item_force_widths[$i-1]        = $instance['item_force_width'.$i];
        $item_force_margins[$i-1]       = $instance['item_force_margin'.$i];
        $item_force_margin_values[$i-1] = $instance['item_force_margin_value'.$i];
      }

      // Important to define which area these images will be rendered in!
      if ($show_placement == 'show_in_sidebar') :
        // Show in sidebar
        echo('<div class="push-links-widget widget large-12 columns">');
          echo('<ul class="push-links-list">');
          foreach ($items as $num => $item) :
            echo('<li>');
              echo('<a href="' . $item_links[$num] . '"><img src="' . $item_imageurl[$num] . '" alt="' . $item_alts[$num] . '" /></a>');
            echo('</li>');
          endforeach;
          echo('</ul>');
        echo('</div><!-- /.widget -->');
      elseif ($show_placement == 'show_in_slider') :
        // Show in slider
        // Make sure to skip bunch of stuff if there is only a single image
        $data_options = (count($items) == 1) ? 'data-options="navigation_arrows:false;slide_number:false;timer:false;"' : '';
        echo('<div class="large-12 columns slider-container">');
          echo('<ul class="helsingborg-orbit" data-orbit ' . $data_options . '>');
          foreach ($items as $num => $item) :
            $force_width  = (!empty($item_force_widths[$num])) ? 'width:100%;' : '';
            $force_margin = (!empty($item_force_margins[$num]) && !empty($item_force_margin_values[$num])) ? ' margin-top:-' . $item_force_margin_values[$num] . 'px;' : '';
            echo('<li>');
              if (!empty($item_links[$num])) { echo('<a href="' . $item_links[$num] . '">'); }
              echo('<img class="img-slide" src="' . $item_imageurl[$num] . '" alt="' . $item_alts[$num] . '" style="' . $force_width . $force_margin .'" />');
              if (!empty($item_links[$num])) { echo( '</a>'); }
              if (!empty($item_texts[$num])) :
                echo('<div class="orbit-caption show-for-medium-up">');
                  echo $item_texts[$num];
                echo('</div>');
              endif;
            echo('</li>');
          endforeach;
          echo('</ul>');
        echo('</div><!-- /.widget -->');
      else :
        // Show under content
        // Make sure to display proper layout if there is more than 2 items
        $grid_size = (count($items) >= 3) ? "3" : "2";
        echo('<section class="large-8 columns">');
          echo('<ul class="block-list news-block large-block-grid-'.$grid_size.' medium-block-grid-'.$grid_size.' small-block-grid-2">');
          foreach ($items as $num => $item) :
            echo('<li>');
              echo('<a href="' . $item_links[$num] . '"><img src="' . $item_imageurl[$num] . '" alt="' . $item_alts[$num] . '" /></a>');
            echo('</li>');
          endforeach;
          echo('</ul>');
        echo('</section>');
      endif;
    }

    // This is where we end up upon "Save" button being used. Make sure to save all fields here!
    public function update( $new_instance, $old_instance) {
      // Save the data
      $instance['title']    = strip_tags($new_instance['title']);
      $instance['rss_link'] = strip_tags($new_instance['rss_link']);
      $amount               = $new_instance['amount'];
      $new_item             = empty($new_instance['new_item']) ? false : strip_tags($new_instance['new_item']);

      // Make sure to pick up each new item created
      if ( isset($new_instance['position1'])) {
        for($i=1; $i<= $new_instance['amount']; $i++){
          if($new_instance['position'.$i] != -1){
            $position[$i] = $new_instance['position'.$i];
          }else{
            $amount--;
          }
        }
        if($position){
          asort($position);
          $order = array_keys($position);
          if(strip_tags($new_instance['new_item'])){
            $amount++;
            array_push($order, $amount);
          }
        }

      }else{
        $order = explode(',',$new_instance['order']);
        foreach($order as $key => $order_str){
          $num = strrpos($order_str,'-');
          if($num !== false){
            $order[$key] = substr($order_str,$num+1);
          }
        }
      }

      // Go through each item created
      if($order){
        foreach ($order as $i => $item_num) {
          $instance['item'.($i+1)]                     = empty($new_instance['item'.$item_num])                    ? '' : strip_tags($new_instance['item'.$item_num]);
          $instance['item_link'.($i+1)]                = empty($new_instance['item_link'.$item_num])               ? '' : strip_tags($new_instance['item_link'.$item_num]);
          $instance['item_target'.($i+1)]              = empty($new_instance['item_target'.$item_num])             ? '' : strip_tags($new_instance['item_target'.$item_num]);
          $instance['item_class'.($i+1)]               = empty($new_instance['item_class'.$item_num])              ? '' : strip_tags($new_instance['item_class'.$item_num]);
          $instance['item_id'.($i+1)]                  = empty($new_instance['item_id'.$item_num])                 ? '' : strip_tags($new_instance['item_id'.$item_num]);
          $instance['attachment_id'.($i+1)]            = empty($new_instance['attachment_id'.$item_num])           ? '' : strip_tags($new_instance['attachment_id'.$item_num]);
          $instance['title'.($i+1)]                    = empty($new_instance['title'.$item_num])                   ? '' : strip_tags($new_instance['title'.$item_num]);
          $instance['imageurl'.($i+1)]                 = empty($new_instance['imageurl'.$item_num])                ? '' : strip_tags($new_instance['imageurl'.$item_num]);
          $instance['alt'.($i+1)]                      = empty($new_instance['alt'.$item_num])                     ? '' : strip_tags($new_instance['alt'.$item_num]);
          $instance['item_text'.($i+1)]                = empty($new_instance['item_text'.$item_num])               ? '' : strip_tags($new_instance['item_text'.$item_num]);
          $instance['item_force_width'.($i+1)]         = empty($new_instance['item_force_width'.$item_num])        ? '' : strip_tags($new_instance['item_force_width'.$item_num]);
          $instance['item_force_margin'.($i+1)]        = empty($new_instance['item_force_margin'.$item_num])       ? '' : strip_tags($new_instance['item_force_margin'.$item_num]);
          $instance['item_force_margin_value'.($i+1)]  = empty($new_instance['item_force_margin_value'.$item_num]) ? '' : strip_tags($new_instance['item_force_margin_value'.$item_num]);
        }
      }

      $instance['amount']         = $amount;
      $instance['show_placement'] = strip_tags($new_instance['show_placement']);

      return $instance;
    }

    // This is what to render in admin area
    public function form( $instance ) {

      // First retrieve all saved data from before, if any
      $instance       = wp_parse_args( (array) $instance, array( 'title' => '', 'text' => '', 'title_link' => '' ) );
      $show_placement = empty($instance['show_placement']) ? 'show_in_sidebar' : $instance['show_placement'];
      $amount         = empty($instance['amount']) ? 1 : $instance['amount'];

      for ($i = 1; $i <= $amount; $i++) {
        $items[$i]                    = empty($instance['item'.$i])                    ? '' : $instance['item'.$i];
        $item_links[$i]               = empty($instance['item_link'.$i])               ? '' : $instance['item_link'.$i];
        $item_targets[$i]             = empty($instance['item_target'.$i])             ? '' : $instance['item_target'.$i];
        $item_ids[$i]                 = empty($instance['item_id'.$i])                 ? '' : $instance['item_id'.$i];
        $item_titles[$i]              = empty($instance['title'.$i])                   ? '' : $instance['title'.$i];
        $item_imageurl[$i]            = empty($instance['imageurl'.$i])                ? '' : $instance['imageurl'.$i];
        $item_attachement_id[$i]      = empty($instance['attachment_id'.$i])           ? '' : $instance['attachment_id'.$i];
        $item_alts[$i]                = empty($instance['alt'.$i])                     ? '' : $instance['alt'.$i];
        $item_texts[$i]               = empty($instance['item_text'.$i])               ? '' : $instance['item_text'.$i];
        $item_force_widths[$i]        = empty($instance['item_force_width'.$i])        ? '' : $instance['item_force_width'.$i];
        $item_force_margins[$i]       = empty($instance['item_force_margin'.$i])       ? '' : $instance['item_force_margin'.$i];
        $item_force_margin_values[$i] = empty($instance['item_force_margin_value'.$i]) ? '' : $instance['item_force_margin_value'.$i];
      } ?>

      <div class="hbgllw-row">
        <label><b>OBS! Vart ska denna visas?  </b></label><br>
        <label for="<?php echo $this->get_field_id('show_in_content'); ?>"><input type="radio" name="<?php echo $this->get_field_name('show_placement'); ?>" value="show_in_content" id="<?php echo $this->get_field_id('show_in_content'); ?>" <?php checked($show_placement, "show_in_content"); ?> />  <?php echo __("Under innehållet"); ?></label>
        <label for="<?php echo $this->get_field_id('show_in_sidebar'); ?>"><input type="radio" name="<?php echo $this->get_field_name('show_placement'); ?>" value="show_in_sidebar" id="<?php echo $this->get_field_id('show_in_sidebar'); ?>" <?php checked($show_placement, "show_in_sidebar"); ?> /> <?php echo __("I sidokolumn"); ?></label>
        <label for="<?php echo $this->get_field_id('show_in_slider'); ?>"><input type="radio" name="<?php echo $this->get_field_name('show_placement'); ?>" value="show_in_slider" id="<?php echo $this->get_field_id('show_in_slider'); ?>" <?php checked($show_placement, "show_in_slider"); ?> /> <?php echo __("I bildspel"); ?></label>
      </div>

      <div class="hbgllw-instructions">
        <?php echo __("<b>Bildmått: 1024 x 400 pixlar.</b>"); ?>
      </div>

      <ul class="hbgllw-instructions">
        <li style="word-break: break-all;"><?php echo __("Notera att <b>minst</b> två bilder måste användas i denna <br> widget om den ska befinna sig under innehållet!"); ?></li>
      </ul>

      <div class="helsingborg-link-list">
      <?php
      // Now render each item
      foreach ($items as $num => $item) :
        $item               = esc_attr($item);
        $item_link          = esc_attr($item_links[$num]);
        $item_id            = esc_attr($item_ids[$num]);
        $image_title        = esc_attr($item_titles[$num]);
        $image_url          = esc_attr($item_imageurl[$num]);
        $attachement_id     = esc_attr($item_attachement_id[$num]);
        $item_text          = esc_attr($item_texts[$num]);
        $force_margin_value = esc_attr($item_force_margin_values[$num]);
        $force_width        = checked($item_force_widths[$num],  'on', false);
        $force_margin       = checked($item_force_margins[$num], 'on', false);
        $checked            = checked($item_targets[$num],       'on', false);
        $button_click       = "helsingborgMediaSelector.create('" . $this->get_field_id($num) . "', '" . $this->get_field_id('') . "', '" . $num . "' ); return false;";
        ?>

        <div id="<?php echo $this->get_field_id($num); ?>" class="list-item">
          <h5 class="moving-handle"><span class="number"><?php echo $num; ?></span>. <span class="item-title"><?php echo $image_title; ?></span><a class="hbgllw-action hide-if-no-js"></a></h5>
          <div class="hbgllw-edit-item" style="display: table;margin: auto;">

            <div class="uploader" style="display: table;margin: auto;">
              <br>
              <div class="widefat" id="<?php echo $this->get_field_id('preview'.$num); ?>">
                <img src="<?php echo $image_url; ?>" style="max-width: 80%;display: table;margin:auto;"/>
              </div>
              <br>
              <input type="submit" class="button" style="display: table; margin: auto;" name="<?php echo $this->get_field_name('uploader_button'.$num); ?>" id="<?php echo $this->get_field_id('uploader_button'.$num); ?>" value="Välj bild" onclick="<?php echo $button_click; ?>" />
              <input type="hidden" id="<?php echo $this->get_field_id('title'.$num); ?>" name="<?php echo $this->get_field_name('title'.$num); ?>" value="<?php echo $instance['title'.$num]; ?>" />
              <input type="hidden" id="<?php echo $this->get_field_id('imageurl'.$num); ?>" name="<?php echo $this->get_field_name('imageurl'.$num); ?>" value="<?php echo $instance['imageurl'.$num]; ?>" />
              <input type="hidden" id="<?php echo $this->get_field_id('alt'.$num); ?>" name="<?php echo $this->get_field_name('alt'.$num); ?>" value="<?php echo esc_attr(strip_tags($instance['alt'])); ?>" />

            </div>
            <br clear="all" />

            <label for="<?php echo $this->get_field_id('item_link'.$num); ?>"><?php echo __("Länk:"); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('item_link'.$num); ?>" name="<?php echo $this->get_field_name('item_link'.$num); ?>" type="text" value="<?php echo $item_link; ?>" />

            <label for="<?php echo $this->get_field_id('item_search'.$num); ?>"><b><?php echo __("Sök efter sida: "); ?></b></label><br>
            <input style="width: 70%;" id="<?php echo $this->get_field_id('item_search'.$num); ?>" type="text" class="input-text" />
            <button style="width: 25%;" id="<?php echo $this->get_field_id('item_search_button'.$num); ?>" name="<?php echo $this->get_field_name('item_search'.$num); ?>" type="button" class="button-secondary" onclick="load_pages_with_update('<?php echo $this->get_field_id('item'); ?>', '<?php echo $num; ?>', 'update_list_item_cells')"><?php echo __("Sök"); ?></button>

            <p>
              <div id="<?php echo $this->get_field_id('item_select'.$num); ?>" style="display: none;">
              </div>
            </p>

            <label for="<?php echo $this->get_field_id('item_text'.$num); ?>"><?php echo __("Bildspelstext:"); ?></label>
            <textarea rows="4" cols="30" id="<?php echo $this->get_field_id('item_text'.$num); ?>" name="<?php echo $this->get_field_name('item_text'.$num); ?>" type="text" style="width:100%;"><?php echo $item_text; ?></textarea>

            <ul class="hbgllw-instructions">
              <li><?php echo __("<b>Bildinställningar</b>"); ?></li>
            </ul>

            <input type="checkbox" name="<?php echo $this->get_field_name('item_force_width'.$num); ?>" id="<?php echo $this->get_field_id('item_force_width'.$num); ?>" value="on" data-clear="false" <?php echo $force_width; ?> /> <label for="<?php echo $this->get_field_id('item_force_width'.$num); ?>"><?php echo __("Tvinga bilden att anpassa i bredd (endast bildspel)"); ?></label>
            <br>
            <input type="checkbox" data-clear="false" value="on" name="<?php echo $this->get_field_name('item_force_margin'.$num); ?>" id="<?php echo $this->get_field_id('item_force_margin'.$num); ?>" <?php echo $force_margin; ?> /> <label for="<?php echo $this->get_field_id('item_force_margin'.$num); ?>"><?php echo __("Tvinga förskjutning i Y-led med "); ?></label>
            <input maxlength="4" size="4" id="<?php echo $this->get_field_id('item_force_margin_value'.$num); ?>" name="<?php echo $this->get_field_name('item_force_margin_value'.$num); ?>" type="text" value="<?php echo $force_margin_value; ?>" /> <label for="<?php echo $this->get_field_id('item_force_margin_value'.$num); ?>"><?php echo __(" pixlar. (endast bildspel)"); ?></label>
            <br>
            <input type="checkbox" name="<?php echo $this->get_field_name('item_target'.$num); ?>" id="<?php echo $this->get_field_id('item_target'.$num); ?>" <?php echo $checked; ?> /> <label for="<?php echo $this->get_field_id('item_target'.$num); ?>"><?php echo __("Öppna i nytt fönster"); ?></label>
            <a class="hbgllw-delete hide-if-no-js"><img src="<?php echo plugins_url('../images/delete.png', __FILE__ ); ?>" /> <?php echo __("Remove"); ?></a>
          </div>
        </div>

      <?php endforeach;

      if ( isset($_GET['editwidget']) && $_GET['editwidget'] ) : ?>
        <table class='widefat'>
          <thead><tr><th><?php echo __("Item"); ?></th><th><?php echo __("Position/Action"); ?></th></tr></thead>
          <tbody>
            <?php foreach ($items as $num => $item) : ?>
            <tr>
              <td><?php echo esc_attr($item); ?></td>
              <td>
                <select id="<?php echo $this->get_field_id('position'.$num); ?>" name="<?php echo $this->get_field_name('position'.$num); ?>">
                  <option><?php echo __('&mdash; Select &mdash;'); ?></option>
                  <?php for($i=1; $i<=count($items); $i++) {
                    if($i==$num){
                      echo "<option value='$i' selected>$i</option>";
                    }else{
                      echo "<option value='$i'>$i</option>";
                    }
                  } ?>
                  <option value="-1"><?php echo __("Delete"); ?></option>
                </select>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>

        <div class="hbgllw-row">
          <input type="checkbox" name="<?php echo $this->get_field_name('new_item'); ?>" id="<?php echo $this->get_field_id('new_item'); ?>" /> <label for="<?php echo $this->get_field_id('new_item'); ?>"><?php echo __("Add New Item"); ?></label>
        </div>
      <?php endif; ?>

      </div>
      <div class="hbgllw-row hide-if-no-js">
        <a class="hbgllw-add button-secondary"><img src="<?php echo plugins_url('../images/add.png', __FILE__ )?>" /> <?php echo __("Lägg till bild"); ?></a>
      </div>

      <input type="hidden" id="<?php echo $this->get_field_id('amount'); ?>" class="amount" name="<?php echo $this->get_field_name('amount'); ?>" value="<?php echo $amount ?>" />
      <input type="hidden" id="<?php echo $this->get_field_id('order'); ?>" class="order" name="<?php echo $this->get_field_name('order'); ?>" value="<?php echo implode(',',range(1,$amount)); ?>" />

<?php
    }
  }
}
