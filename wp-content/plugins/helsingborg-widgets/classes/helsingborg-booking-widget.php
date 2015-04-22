<?php
/*
Plugin Name: HbgBookingWidget
Custom Booking Widget
*/
// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');	
/**
 * Adds HbgBookingWidget widget.
 */
class HbgBookingWidget extends WP_Widget {
	function __construct() {
		// Register the widget on widgets_init
        add_action('widgets_init', array($this, 'registerWidget'));     
        // Enqueue datepicker script & css for the widget on sidebar_admin_setup
        add_action( 'widgets_init', array( $this, 'load_datepicker_sidebar_admin_setup' ) );	
		parent::__construct(
			'hbgbookingwidget', // Base ID
			__('* Bokning', 'helsingborg'), // Name
			array( 'description' => __( 'Bookingswidget description', 'helsingborg' ), ) // Args
		);
	}	
    // Register widget
    public function registerWidget() {
        register_widget('hbgbookingwidget');
    }	
    // Enqueue scripts
    
	public function load_datepicker_sidebar_admin_setup() {
		wp_enqueue_script('jquery-ui-datepicker');
		wp_enqueue_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
	}
	
	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		
		 // Get all the data saved
		$show_placement = empty($instance['show_placement']) ? 'show_in_sidebar' : $instance['show_placement'];
						
		if ( ! empty( $instance['title'] ) ) {
			$rubrik = $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
		}
		if ( ! empty( $instance['datum'] ) ) {
			$widgetContent.= '<li class="datumBooking">'.apply_filters( 'sanitize_text_field', $instance['datum'] ).'</li>';
		}
		if ( ! empty( $instance['tid'] ) ) {
			$widgetContent.= '<li class="tidBooking">'.apply_filters( 'sanitize_text_field', $instance['tid'] ).'</li>';
		}
		if ( ! empty( $instance['pris'] ) ) {
			$widgetContent.= '<li class="prisBooking">'.apply_filters( 'sanitize_text_field', $instance['pris'] ).'</li>';
		}
		if ( ! empty( $instance['alternativpris'] ) ) {
			$widgetContent.= '<li class="alternativprisBooking">'.apply_filters( 'sanitize_text_field', $instance['alternativpris'] ).'</li>';
		}
		if ( ! empty( $instance['rubrik_kopknapp'] ) && ! empty( $instance['lank_till_webbshop'] ) ) {
			$buttonBooking = '<button id="searchsubmit" class="button" type="submit"><a style="color:#fff; text-decoration:none;" href="'.apply_filters( 'sanitize_text_field', $instance['lank_till_webbshop'] ).'">'.apply_filters( 'sanitize_text_field', $instance['rubrik_kopknapp'] ).'</a></button>';
		}
		
		// placement
		if ($show_placement == 'show_in_sidebar') { //show in sidebar
			echo '<!--before_widget_start-->'.$args['before_widget'].'<!--before-widget-end-->'.$rubrik;
			echo '<ul class="push-booking-list">'.$widgetContent.'</ul>'.$buttonBooking;
		}else {
			// Show under content
			echo '<section class="small-12 medium-12 large-12 columns bookingwidget widget-content">'.$rubrik.'<div class="divider fade"><div class="upper-divider"></div><div class="lower-divider"></div></div><ul class="">'.$widgetContent.'</ul>'.$buttonBooking.'</section>';
		}
		echo '<!--after_widget_start-->'.$args['after_widget'].'<!--after-widget-end-->';
		
	} //function widget

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		global $post;
		
    ?>
        <script type="text/javascript">
            jQuery('.datepicker_booking_widget').on('click', function() {
                jQuery(this).datepicker({ dateFormat: "yy-mm-dd" });
                jQuery(this).datepicker('show');
            });                        
        </script>
    <?php
		if ( isset( $instance[ 'show_placement' ] ) ) {
			$show_placement = $instance[ 'show_placement' ];
		}
		else {
			$show_placement = __( 'show_in_sidebar', 'helsingborg' );
		}		
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( '', 'helsingborg' );
		}
		if ( isset( $instance[ 'datum' ] ) ) {
			$datum = $instance[ 'datum' ];
		}
		else {
			$datum = __( '', 'helsingborg' );
		}
		if ( isset( $instance[ 'tid' ] ) ) {
			$tid = $instance[ 'tid' ];
		}
		else {
			$tid = __( '', 'helsingborg' );
		}
		if ( isset( $instance[ 'pris' ] ) ) {
			$pris = $instance[ 'pris' ];
		}
		else {
			$pris = __( '', 'helsingborg' );
		}
		if ( isset( $instance[ 'alternativpris' ] ) ) {
			$alternativpris = $instance[ 'alternativpris' ];
		}
		else {
			$alternativpris = __( '', 'helsingborg' );
		}
		if ( isset( $instance[ 'rubrik_kopknapp' ] ) ) {
			$rubrik_kopknapp = $instance[ 'rubrik_kopknapp' ];
		}
		else {
			$rubrik_kopknapp = __( '', 'Helsingborg' );
		}
		if ( isset( $instance[ 'lank_till_webbshop' ] ) ) {
			$lank_till_webbshop = $instance[ 'lank_till_webbshop' ];
		}
		else {
			$lank_till_webbshop = __( '', 'helsingborg' );
		}
		
		
		if ( isset( $instance[ 'post_id' ] ) ) {
			$post_id = $instance[ 'post_id' ];
		}
		else {
			$post_id = $post->ID;
		}
		
		?>
		
		<div class="hbgllw-row">
	        <label><b>OBS! Vart ska denna visas?  </b></label><br>
	        <label for="<?php echo $this->get_field_id('show_in_content'); ?>"><input type="radio" name="<?php echo $this->get_field_name('show_placement'); ?>" value="show_in_content" id="<?php echo $this->get_field_id('show_in_content'); ?>" <?php checked($show_placement, "show_in_content"); ?> />  <?php echo __("Under innehållet"); ?></label>
	        <label for="<?php echo $this->get_field_id('show_in_sidebar'); ?>"><input type="radio" name="<?php echo $this->get_field_name('show_placement'); ?>" value="show_in_sidebar" id="<?php echo $this->get_field_id('show_in_sidebar'); ?>" <?php checked($show_placement, "show_in_sidebar"); ?> /> <?php echo __("I sidokolumn"); ?></label>
	        <p>Vid flera bokningswidgets på samma artikel kommer endast den översta att visas på samlingssidor.</p>
	    </div>
		
		
		
		
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Rubrik:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
			
			<label for="<?php echo $this->get_field_id( 'datum' ); ?>"><?php _e( 'Datum:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'datum' ); ?>" name="<?php echo $this->get_field_name( 'datum' ); ?>" type="text" value="<?php echo esc_attr( $datum ); ?>">
			
			<label for="<?php echo $this->get_field_id( 'tid' ); ?>"><?php _e( 'Tid:' ); ?></label> 
			<input class="widefat datepicker_booking_widget" id="<?php echo $this->get_field_id( 'tid' ); ?>" name="<?php echo $this->get_field_name( 'tid' ); ?>" type="text" value="<?php echo esc_attr( $tid ); ?>">
			
			<label for="<?php echo $this->get_field_id( 'pris' ); ?>"><?php _e( 'Pris:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'tid' ); ?>" name="<?php echo $this->get_field_name( 'pris' ); ?>" type="text" value="<?php echo esc_attr( $pris ); ?>">
			
			<label for="<?php echo $this->get_field_id( 'alternativpris' ); ?>"><?php _e( 'Alternativpris:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'alternativpris' ); ?>" name="<?php echo $this->get_field_name( 'alternativpris' ); ?>" type="text" value="<?php echo esc_attr( $alternativpris ); ?>">
			
			<label for="<?php echo $this->get_field_id( 'rubrik_kopknapp' ); ?>"><?php _e( 'Rubrik köpknapp:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'rubrik_kopknapp' ); ?>" name="<?php echo $this->get_field_name( 'rubrik_kopknapp' ); ?>" type="text" value="<?php echo esc_attr( $rubrik_kopknapp ); ?>">
			
			<label for="<?php echo $this->get_field_id( 'lank_till_webbshop' ); ?>"><?php _e( 'Länk till webbshop:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'lank_till_webbshop' ); ?>" name="<?php echo $this->get_field_name( 'lank_till_webbshop' ); ?>" type="text" value="<?php echo esc_attr( $lank_till_webbshop ); ?>">
		</p>
				
		
		<input id="<?php echo $this->get_field_id( 'post_id' ); ?>" name="<?php echo $this->get_field_name( 'post_id' ); ?>" type="hidden" value="<?php echo $post_id; ?>">
		
		
		<?php 
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['datum'] = ( ! empty( $new_instance['datum'] ) ) ? strip_tags( $new_instance['datum'] ) : '';
		$instance['tid'] = ( ! empty( $new_instance['tid'] ) ) ? strip_tags( $new_instance['tid'] ) : '';
		$instance['pris'] = ( ! empty( $new_instance['pris'] ) ) ? strip_tags( $new_instance['pris'] ) : '';
		$instance['alternativpris'] = ( ! empty( $new_instance['alternativpris'] ) ) ? strip_tags( $new_instance['alternativpris'] ) : '';
		$instance['rubrik_kopknapp'] = ( ! empty( $new_instance['rubrik_kopknapp'] ) ) ? strip_tags( $new_instance['rubrik_kopknapp'] ) : '';
		$instance['lank_till_webbshop'] = ( ! empty( $new_instance['lank_till_webbshop'] ) ) ? strip_tags( $new_instance['lank_till_webbshop'] ) : '';
		
		$instance['post_id'] = $new_instance['post_id'];
		
		$instance['show_placement'] = strip_tags($new_instance['show_placement']);

		return $instance;
	}

} // class HbgBookingWidget