<?php
if (!class_exists('Index_Large_Widget')) {
  class Index_Large_Widget
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
      register_widget( 'Index_Large_Widget_Box' );
    }
  }
}

if (!class_exists('Index_Large_Widget_Box')) {
  class Index_Large_Widget_Box extends WP_Widget {

    /** constructor */
    function Index_Large_Widget_Box() {
      parent::WP_Widget(false, '* Nyhetslista', array('description' => 'Lägg till de nyheter som du vill visa.'));
    }

    public function widget( $args, $instance ) {
      extract($args);

      // Get all the data saved
      $amount = empty($instance['amount']) ? 1 : $instance['amount'];

      for ($i = 1; $i <= $amount; $i++) {
        $items[$i-1] = $instance['item'.$i];
        $item_ids[$i-1] = $instance['item_id'.$i];
      } ?>

      <section class="news-section">
        <ul class="news-list-large row">
        <?php // Go through all list items and present as a list
        foreach ($items as $num => $item) :
            $item_id = $item_ids[$num];
            $page = get_page($item_id, OBJECT, 'display');
            if ($page->post_status !== 'publish') continue;

            // Get the content, see if <!--more--> is inserted
            $the_content = get_extended(strip_shortcodes($page->post_content));
            $main = $the_content['main'];
            $content = $the_content['extended']; // If content is empty, no <!--more--> tag was used -> content is in $main

            $link = get_permalink($page->ID); ?>
          <li class="news-item large-12 columns">
            <div class="row">
              <?php if (has_post_thumbnail( $page->ID ) ) : ?>
              <div class="large-5 medium-4 small-12 columns news-image">
                <?php $image_id = get_post_thumbnail_id( $page->ID );
                $image = wp_get_attachment_image_src( $image_id, 'single-post-thumbnail' );
                $alt_text = get_post_meta($image_id, '_wp_attachment_image_alt', true); ?>
                <a href="<?php echo $link; ?>"><img src="<?php echo $image[0]; ?>" alt="<?php echo $alt_text; ?>"></a>
              </div>
              <?php endif; ?>
              <div class="large-7 medium-8 small-12 columns news-content">
                <a href="<?php echo $link; ?>">
                  <h2 class="news-title"><?php echo $page->post_title ?></h2>
                  <span class="news-date>"></span>
                  <?php echo wpautop($main, true); ?>
                  <span class="read-more">Läs mer</span>
                </a>
              </div>
            </div>
          </li>
        <?php endforeach; ?>
        </ul>
      </section>

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
        }
      }

      $instance['amount'] = $amount;

      return $instance;
    }

    public function form ( $instance ) {
      $amount = empty($instance['amount']) ? 1 : $instance['amount'];

      for ($i = 1; $i <= $amount; $i++) {
        $items[$i] = empty($instance['item'.$i]) ? '' : $instance['item'.$i];
        $item_ids[$i] = empty($instance['item_id'.$i]) ? '' : $instance['item_id'.$i];
      } ?>

      <ul class="hbgllw-instructions">
        <li><?php echo __("Lägg till de sidor som ni vill ska visas i listan."); ?></li>
      </ul>

      <div class="helsingborg-link-list">
      <?php foreach ($items as $num => $item) :
        $item = esc_attr($item);
        $item_id = esc_attr($item_ids[$num]);
        $h5 = esc_attr($item);
        if (!empty($item_id)) {
          $h5 = get_post($item_id, OBJECT, 'display')->post_title;
        }
      ?>

        <div id="<?php echo $this->get_field_id($num); ?>" class="list-item">
          <h5 class="moving-handle"><span class="number"><?php echo $num; ?></span>. <span class="item-title"><?php echo $h5; ?></span><a class="hbgllw-action hide-if-no-js"></a></h5>
          <div class="hbgllw-edit-item">
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
        <a class="hbgllw-add button-secondary"><img src="<?php echo plugins_url('../images/add.png', __FILE__ )?>" /> <?php echo __("Lägg till indexobjekt"); ?></a>
      </div>

      <input type="hidden" id="<?php echo $this->get_field_id('amount'); ?>" class="amount" name="<?php echo $this->get_field_name('amount'); ?>" value="<?php echo $amount ?>" />
      <input type="hidden" id="<?php echo $this->get_field_id('order'); ?>" class="order" name="<?php echo $this->get_field_name('order'); ?>" value="<?php echo implode(',',range(1,$amount)); ?>" />

<?php
    }
  }
}
