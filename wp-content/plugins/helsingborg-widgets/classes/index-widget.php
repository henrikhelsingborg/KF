<?php
if (!class_exists('Index_Widget')) {
  class Index_Widget
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
      register_widget( 'Index_Widget_Box' );
    }
  }
}

if (!class_exists('Index_Widget_Box')) {
  class Index_Widget_Box extends WP_Widget {

    /** constructor */
    function Index_Widget_Box() {
      parent::WP_Widget(false, '* Index', array('description' => 'Lägg till de index som du vill visa.'));
    }

    public function widget( $args, $instance ) {
      extract($args);

      // Get all the data saved
      $amount = empty($instance['amount']) ? 1 : $instance['amount'];
      $page_list = isset($instance['page_list']) ? $instance['page_list'] : false;
      $list_class = $page_list !== 'on' ? ' ' : ' page-list ';

      for ($i = 1; $i <= $amount; $i++) {
        $items[$i-1] = $instance['item'.$i];
        $item_ids[$i-1] = $instance['item_id'.$i];
      }
      ?>

      <ul class="block-list page-block-list<?php echo $list_class; ?>large-block-grid-3 medium-block-grid-3 small-block-grid-2">
        <?php // Go through all list items and present as a list
        foreach ($items as $num => $item) :
            $item_id = $item_ids[$num];
            $page = get_page($item_id, OBJECT, 'display');
            if ($page->post_status !== 'publish') continue;

            $link = get_permalink($page->ID);

            // Get the content, see if <!--more--> is inserted
            $the_content = get_extended($page->post_content);
            $main = $the_content['main'];
            $content = $the_content['extended'];

            $image = false;
            if (has_post_thumbnail( $page->ID ) ) :
              $image_id = get_post_thumbnail_id( $page->ID );
              $image = wp_get_attachment_image_src( $image_id, 'single-post-thumbnail' );
              $alt_text = get_post_meta($image_id, '_wp_attachment_image_alt', true);
            endif;

            $title = $page->post_title;
            if (isset($instance['headline' . ($num+1)]) && strlen($instance['headline' . ($num+1)]) > 0) {
              $title = $instance['headline' . ($num+1)];
            }
          ?>
          <li>
            <a href="<?php echo $link ?>" desc="link-desc">
              <?php if($image) : ?><img src="<?php echo $image[0]; ?>" alt="<?php echo $alt_text; ?>"><?php endif; ?>
              <h2 class="list-title"><?php echo $title ?></h2>
              <div class="list-content">
                <?php echo wpautop($main, true); ?>
              </div>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
      <?php
    }

    public function update( $new_instance, $old_instance) {

      // Save the data
      $amount = $new_instance['amount'];
      $new_item = empty($new_instance['new_item']) ? false : strip_tags($new_instance['new_item']);

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

      if($order){
        foreach ($order as $i => $item_num) {
          $instance['item'.($i+1)] = empty($new_instance['item'.$item_num]) ? '' : strip_tags($new_instance['item'.$item_num]);
          $instance['item_id'.($i+1)] = empty($new_instance['item_id'.$item_num]) ? '' : strip_tags($new_instance['item_id'.$item_num]);
          $instance['headline'.($i+1)] = empty($new_instance['headline'.$item_num]) ? '' : strip_tags($new_instance['headline'.$item_num]);
        }
      }

      $instance['amount'] = $amount;
      $instance['page_list'] = empty($new_instance['page_list']) ? '' : strip_tags($new_instance['page_list']);

      return $instance;
    }

    public function form ( $instance ) {
      $amount = empty($instance['amount']) ? 1 : $instance['amount'];
      $page_list = empty($instance['page_list']) ? '' : $instance['page_list'];

      for ($i = 1; $i <= $amount; $i++) {
        $items[$i] = empty($instance['item'.$i]) ? '' : $instance['item'.$i];
        $item_ids[$i] = empty($instance['item_id'.$i]) ? '' : $instance['item_id'.$i];
      } ?>

      <ul class="hbgllw-instructions">
        <li><?php echo __("Lägg till de sidor som ni vill ska visas i listan."); ?></li>
      </ul>

      <div class="helsingborg-link-list">
      <?php
      foreach ($items as $num => $item) :
        $item = esc_attr($item);
        $item_id = esc_attr($item_ids[$num]);
        $h5 = esc_attr($item);
        if (!empty($item_id)) {
          $h5 = get_post($item_id, OBJECT, 'display')->post_title;
        }
      ?>

        <div id="<?php echo $this->get_field_id($num); ?>" class="list-item">
          <h5 class="moving-handle"><span class="number"><?php echo $num; ?></span>. <span class="item-title"><?php echo $h5 . ' (ID: ' . $item_id . ')'; ?></span><a class="hbgllw-action hide-if-no-js"></a></h5>
          <div class="hbgllw-edit-item">
            <p>
              <label for="<?php echo $this->get_field_id('item_headline'.$num); ?>"><?php echo __("Indexrubrik (lämna tomt för att använda sidan titel):"); ?></label><br>
              <input id="input_<?php echo $this->get_field_id('item_headline'.$num); ?>" type="text" class="input-text" name="<?php echo $this->get_field_name('headline'.$num); ?>" value="<?php echo $instance['headline'.$num]; ?>" />
            </p>
            <p>
              <label for="<?php echo $this->get_field_id('item_id'.$num); ?>"><?php echo __("Sida att söka efter: "); ?></label><br>
              <input id="input_<?php echo $this->get_field_id('item_id'.$num); ?>" type="text" class="input-text" />
              <button id="button_<?php echo $this->get_field_id('item_id'.$num); ?>" name="<?php echo $this->get_field_name('item_id'.$num); ?>" type="button" class="button-secondary" onclick="load_page_containing(this.id, this.name)"><?php echo __("SÖK"); ?></button>
            </p>

            <div id="select_<?php echo $this->get_field_id('item_id'.$num); ?>" style="display: none;">
              <select id="<?php echo $this->get_field_id('item_id'.$num); ?>" name="<?php echo $this->get_field_name('item_id'.$num); ?>">
                <option value="<?php echo $item_id; ?>"><?php echo $h5; ?></option>
              </select>
            </div>

            <a class="hbgllw-delete hide-if-no-js"><img src="<?php echo plugins_url('../images/delete.png', __FILE__ ); ?>" /> <?php echo __("Remove"); ?></a>
            <br>
          </div>
        </div>
      <?php endforeach; ?>

      <?php if ( isset($_GET['editwidget']) && $_GET['editwidget'] ) : ?>
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
        <a class="hbgllw-add button-secondary"><img src="<?php echo plugins_url('../images/add.png', __FILE__ )?>" /> <?php echo __("Lägg till indexobjekt"); ?></a>
      </div>

      <input type="hidden" id="<?php echo $this->get_field_id('amount'); ?>" class="amount" name="<?php echo $this->get_field_name('amount'); ?>" value="<?php echo $amount ?>" />
      <input type="hidden" id="<?php echo $this->get_field_id('order'); ?>" class="order" name="<?php echo $this->get_field_name('order'); ?>" value="<?php echo implode(',',range(1,$amount)); ?>" />

      <div class="hbgllw-row">
        <input type="checkbox" name="<?php echo $this->get_field_name('page_list'); ?>" id="<?php echo $this->get_field_id('page_list'); ?>" <?php checked($page_list, 'on'); ?> /> <label for="<?php echo $this->get_field_id('page_list'); ?>"><?php echo __("Visa som lista? "); ?></label>
      </div>
<?php
    }
  }
}
