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
      parent::WP_Widget(false, '* Nyhetspuffar', array('description' => 'Lägg till de nyhetspuffar som du vill visa.'));
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
            $link = get_permalink($page->ID); ?>
          <li class="news-item large-12 columns">
            <div class="row">
              <div class="large-5 medium-4 small-4 columns news-image">
              <?php if (has_post_thumbnail( $page->ID ) ) :
                $image_id = get_post_thumbnail_id( $page->ID );
                $image = wp_get_attachment_image_src( $image_id, 'single-post-thumbnail' );
                $alt_text = get_post_meta($image_id, '_wp_attachment_image_alt', true); ?>
                <img src="<?php echo $image[0]; ?>" alt="<?php echo $alt_text; ?>">
              <?php endif; ?>
              </div>
              <div class="large-7 medium-8 small-8 columns news-content">
                <h2 class="news-title"><?php echo $page->post_title ?></h2>
                <span class="news-date>"></span>
                <?php echo $this->fr_excerpt_by_id($page); ?>
                <a href="<?php echo $link ?>" class="read-more">Läs mer</a>
              </div>
            </div>
          </li>
        <?php endforeach; ?>
        </ul>
      </section>

      <?php
    }

    // Function for retrieving the excerpt from page OR part of content if no excerpt was found
    function fr_excerpt_by_id($the_post, $excerpt_length = 35, $line_breaks = TRUE){
      $the_excerpt = $the_post->post_excerpt ? $the_post->post_excerpt : $the_post->post_content; //Gets post_excerpt or post_content to be used as a basis for the excerpt
      $the_excerpt = apply_filters('the_excerpt', $the_excerpt);
      $the_excerpt = $line_breaks ? strip_tags(strip_shortcodes($the_excerpt), '<p><br>') : strip_tags(strip_shortcodes($the_excerpt)); //Strips tags and images
      $words = explode(' ', $the_excerpt, $excerpt_length + 1);
      if(count($words) > $excerpt_length) :
        array_pop($words);
        array_push($words, '…');
        $the_excerpt = implode(' ', $words);
        $the_excerpt = $line_breaks ? $the_excerpt . '</p>' : $the_excerpt;
      endif;
      $the_excerpt = trim($the_excerpt);
      return $the_excerpt;
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
      }
  ?>

      <ul class="sllw-instructions">
        <li><?php echo __("Lägg till de sidor som ni vill ska visas i listan."); ?></li>
      </ul>

      <div class="simple-link-list">
      <?php foreach ($items as $num => $item) :
        $item = esc_attr($item);
        $item_id = esc_attr($item_ids[$num]);
        $h5 = esc_attr($item);
        if (!empty($item_id)) {
          $h5 = get_post($item_id, OBJECT, 'display')->post_title;
        }
      ?>

        <div id="<?php echo $this->get_field_id($num); ?>" class="list-item">
          <h5 class="moving-handle"><span class="number"><?php echo $num; ?></span>. <span class="item-title"><?php echo $h5; ?></span><a class="sllw-action hide-if-no-js"></a></h5>
          <div class="sllw-edit-item">
            <p>
              <label for="<?php echo $this->get_field_id('item_id'.$num); ?>"><?php echo __("Sida att hämta: "); ?></label><br>
              <?php wp_dropdown_pages(array(
                      'show_option_none' => 'Ingen sida vald',
                      'selected' => $item_ids[$num],
                      'id' => $this->get_field_id('item_id'.$num),
                      'name' => $this->get_field_name('item_id'.$num)
                    )); ?>
            </p>
            <a class="sllw-delete hide-if-no-js"><img src="<?php echo plugins_url('../images/delete.png', __FILE__ ); ?>" /> <?php echo __("Remove"); ?></a>
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

        <div class="sllw-row">
          <input type="checkbox" name="<?php echo $this->get_field_name('new_item'); ?>" id="<?php echo $this->get_field_id('new_item'); ?>" /> <label for="<?php echo $this->get_field_id('new_item'); ?>"><?php echo __("Add New Item"); ?></label>
        </div>
      <?php endif; ?>

      </div>
      <div class="sllw-row hide-if-no-js">
        <a class="sllw-add button-secondary"><img src="<?php echo plugins_url('../images/add.png', __FILE__ )?>" /> <?php echo __("Lägg till indexobjekt"); ?></a>
      </div>

      <input type="hidden" id="<?php echo $this->get_field_id('amount'); ?>" class="amount" name="<?php echo $this->get_field_name('amount'); ?>" value="<?php echo $amount ?>" />
      <input type="hidden" id="<?php echo $this->get_field_id('order'); ?>" class="order" name="<?php echo $this->get_field_name('order'); ?>" value="<?php echo implode(',',range(1,$amount)); ?>" />

<?php
    }
  }
}