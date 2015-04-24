<?php
/*
Plugin Name: HbgTimelineWidget
Custom Timeline Widget
*/
// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');	
/**
 * Adds HbgBookingWidget widget.
 */
class HbgTimelineWidget extends WP_Widget {
	function __construct() {
		// Register the widget on widgets_init
        add_action('widgets_init', array($this, 'registerWidget'));     
		parent::__construct(
			'hbgtimelinewidget', // Base ID
			__('* Timeline', 'helsingborg'), // Name
			array( 'description' => __( 'Timelinewidget description', 'helsingborg' ), ) // Args
		);
	}	
    // Register widget
    public function registerWidget() {
        register_widget('hbgtimelinewidget');
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
		wp_enqueue_script( 'flexslider', get_template_directory_uri() . '/js/flexslider/jquery.flexslider-min.js', array('jquery'), '1.0.0', false );
		global $post;
		// Get all the data saved
		$show_layout = empty($instance['show_layout']) ? 'show_thin_design' : $instance['show_layout'];
		
		$item_id = empty($instance[ 'item_id' ]) ? '' : $instance[ 'item_id' ];
		
		// val av layout/design
		if ($show_layout == 'show_thin_design') { //show thin design
			echo '<section class="small-12 medium-12 large-12 columns clearfix timelinewidget show_thin_design">';
			echo '<p>show_thin_design</p>';
		}else { //show big design
			echo '<section class="small-12 medium-12 large-12 columns timelinewidget show_big_design">';
			echo '<p>show_big_design</p>';
		}		
		// Get the child pages
		$pages = get_pages(array(
		  'sort_order' => 'DESC',
		  'sort_column' => 'post_modified',
		  'child_of' => $instance[ 'item_id' ],
		  'post_type' => 'page',
		  'post_status' => 'publish')
		);
		// Go through all childs 
		for ($i = 0; $i < count($pages); $i++) {
			//dump_r($pages[$i]);			
			$child_post = $pages[$i];
			$child_post_id = $pages[$i]->ID; 
			$link = get_permalink($child_post_id);			
			// Get some meta data from child
		    $visa_ingress_i_samling = get_post_meta($child_post_id, 'visa_ingress_i_samling');
		    global $wpdb;	
			//tabellen "_customize_sidebars" anger om artikeln har custom eller generella sidebars
			$customize_sidebars = $wpdb->get_results("SELECT meta_value FROM $wpdb->postmeta WHERE meta_key = '_customize_sidebars' AND post_id ='$child_post_id'",ARRAY_A);	
			if( $customize_sidebars[0]['meta_value'] == 'yes' ) {			
				$sidebars_widgets = $wpdb->get_results("SELECT * FROM $wpdb->postmeta WHERE meta_key = '_sidebars_widgets' AND post_id ='$child_post_id'",ARRAY_A);	
				$sidebars_widgetsUnserialized = unserialize($sidebars_widgets[0]['meta_value']);								
				$widget_id = checkforbookingwidget($sidebars_widgetsUnserialized);
				
				if($widget_id != null ){			
					$bookingWidgetsOnThisPostInDatabase = 'widget_'.$child_post_id.'_hbgbookingwidget';			
					$result = $wpdb->get_results("SELECT * FROM $wpdb->options WHERE option_name = '$bookingWidgetsOnThisPostInDatabase'",ARRAY_A);			
					$result_ = unserialize($result[0]['option_value']);			
					$datum = $result_[$widget_id]['datum'];
					$tid = $result_[$widget_id]['tid'];
					$rubrik_kopknapp = $result_[$widget_id]['rubrik_kopknapp'];
					$lank_till_webbshop = $result_[$widget_id]['lank_till_webbshop'];
					
					// Try to get the thumbnail for the page	
				    if (has_post_thumbnail( $child_post_id ) ) {
				      $image_id = get_post_thumbnail_id( $child_post_id );
				      $image = wp_get_attachment_image_src( $image_id, 'thumbnail' );
				      $alt_text = get_post_meta($image_id, '_wp_attachment_image_alt', true);
				      //echo '<a href="'.$link.'"><img src="'.$image[0].'" alt="'.$alt_text.'"></a>';
					}					
					//echo '<p><a href="'.$link.'">'.$child_post->post_title.'</a></p>';
					//echo '<p>'.$tid.'</p>';	
										
					$timelineArray[$i] = [
					    'tid' => $tid,
					    'title' => $child_post->post_title,
					    'link' => $link,
					    'image' => $image[0],
					    'alt_text' => $alt_text, 
					];
						
				} // if bookingwidget exists
			} // if custom sidebars 	
		} //for loop ?>
<div class="timeline flexslider clearfix">
    <ul class="slides">
	<?php
	$timezone = new DateTimeZone('Europe/Stockholm');
	$currentDate = new DateTime();
	$currentDate->setTimezone($timezone);
	for ($k = 0; $k < 12; $k++) {
		//antal dagar i timeline/månad
		$daysInMonth = incrementDate($currentDate, $k)->format('t');
		$yearForThisMonth = incrementDate($currentDate, $k)->format('Y');
		$thisMonthNumerical = incrementDate($currentDate, $k)->format('m');
		$thisMonthText = incrementDate($currentDate, $k)->format('F');	?>
		<li>
    		<div class="month-year-nav"><?php echo __($thisMonthText,'helsingborg').' '.$yearForThisMonth; ?></div>
			<div class="timeline-cal clearfix">
				<ul>
				<?php
				for ($l = 1; $l <= $daysInMonth; $l++) {						
					$thisDayText = jddayofweek ( cal_to_jd(CAL_GREGORIAN, $thisMonthNumerical,$l, $yearForThisMonth) , 1 );
					if( $thisDayText == 'Saturday' || $thisDayText == 'Sunday') { $weekday = 'redday'; } else{$weekday = 'weekday';} ?>
					<li class="date">
					<?php
					$dayNum = appendLeadingZero($l);
					$currentDateInLoop = $yearForThisMonth.'-'.$thisMonthNumerical.'-'.$dayNum;
					$matchDate=false;
					$matchItem=null;
					for ($c = 0; $c < count($timelineArray); $c++) {	
						if($timelineArray[$c]['tid'] == $currentDateInLoop) { 
							//echo $currentDateInLoop.'<br>';
							$matchItem[] .= $c;
							$matchDate=true;
						}	
					}
					if($matchItem[0]!='') { ?>							
						<a class="hasevents <?php echo $weekday; ?> popover" href="#"><?php echo $l; ?></a>
						<div class="eventlist">
					        <ul class="popup">
								<?php
								foreach($matchItem as $Mitem ){							
									echo '<li><a href="'.$timelineArray[$Mitem]['link'].'" >'.$timelineArray[$Mitem]['title'].'</a></li>';																	
								} ?>																	                       
                            </ul>
						</div>								                        
					</li>							
					<?php	
					}else{ ?>						               
						<a class="<?php echo $weekday; ?>" style="display:inline-block" href="#"><?php echo $l; ?></a>
					</li>
					<?php		
					}
				} ?>
				</ul>
			</div>
		</li>
	<?php
	} ?>
	</ul>
</div>		
</section>
	
<script type="text/javascript">
/* TimeLIne flexslider */
$(window).load(function() {
    jQuery('.flexslider').flexslider({
        slideshow: false,
        controlNav: false
    });
});
	
//popup	
jQuery('.no-touch .hasevents').on("mouseenter", function () {
	jQuery(this).next('.eventlist').fadeIn( 400 );        
}); 
jQuery('.no-touch .hasevents').on("mouseleave", function () { 
	jQuery(this).next('.eventlist').delay(1000).fadeOut( 400 );
}); 
jQuery('.touch .hasevents').click(function() {
  jQuery(this).next('.eventlist').fadeToggle( "slow", "linear" );
});
                     
</script>

<?php
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
		
    	if ( isset( $instance[ 'show_layout' ] ) ) {
			$show_placement = $instance[ 'show_layout' ];
		}
		else {
			$show_placement = __( 'show_thin_design', 'helsingborg' );
		}					
		
		if ( isset( $instance[ 'post_id' ] ) ) {
			$post_id = $instance[ 'post_id' ];
		}
		else {
			$post_id = $post->ID;
		}
		
		if ( isset( $instance[ 'item_id' ] ) ) {
			$item_id = $instance[ 'item_id' ];
		}
		else {
			$item_id = '';
		}
		
		?>
		
		<div class="hbgllw-row">
	        <label><b>Design?  </b></label><br>
	        <label for="<?php echo $this->get_field_id('show_big_design'); ?>"><input type="radio" name="<?php echo $this->get_field_name('show_layout'); ?>" value="show_big_design" id="<?php echo $this->get_field_id('show_big_design'); ?>" <?php checked($show_placement, "show_big_design"); ?> />  <?php echo __("Stor design"); ?></label>
	        <label for="<?php echo $this->get_field_id('show_thin_design'); ?>"><input type="radio" name="<?php echo $this->get_field_name('show_layout'); ?>" value="show_thin_design" id="<?php echo $this->get_field_id('show_thin_design'); ?>" <?php checked($show_placement, "show_thin_design"); ?> /> <?php echo __("Smal design"); ?></label>
	        <p>Vid flera bokningswidgets på samma artikel kommer endast den översta att visas i timeline.</p>
	    </div>
		
		<input id="<?php echo $this->get_field_id( 'post_id' ); ?>" name="<?php echo $this->get_field_name( 'post_id' ); ?>" type="hidden" value="<?php echo $post_id; ?>">
		
		
		
		
		<ul class="hbgllw-instructions">
        	<li><?php echo __('Lägg till den sida med sidmallen "Samling" som ni vill ska visas i Timeline-widget.'); ?></li>
      	</ul>
		<div class="helsingborg-link-list">
		 <?php
			 if($item_id){
			 	$item_title = get_post($item_id)->post_title; 
			 	echo '<p>Vald sida är: '.$item_title.'</p>';
			 }
		 ?>
		
		 	<p>
				<label for="<?php echo $this->get_field_id('item_id'); ?>"><?php echo __("Sida att söka efter: "); ?></label><br>
				<input id="input_<?php echo $this->get_field_id('item_id'); ?>" type="text" class="input-text" />
				<button id="button_<?php echo $this->get_field_id('item_id'); ?>" name="<?php echo $this->get_field_name('item_id'); ?>" type="button" class="button-secondary" onclick="load_page_containing(this.id, this.name)"><?php echo __("SÖK"); ?></button>
            </p>
            
            <div id="select_<?php echo $this->get_field_id('item_id'); ?>" style="display: none;">
              	<select id="<?php echo $this->get_field_id('item_id'); ?>" name="<?php echo $this->get_field_name('item_id'); ?>">
                <option value="<?php echo $item_id; ?>"><?php echo $item_title; ?></option>
              </select>
            </div>
		</div>
		
		
			<script type="text/javascript">
				
			function load_page_containing(from, name) {
				
				var id = from.replace('button_', '');
				
				document.getElementById('select_' + id).style.display = "block";
				document.getElementById('select_' + id).innerHTML = "";
				
				var data = {
				action: 'load_pages',
				id: id,
				name: name,
				title: document.getElementById('input_' + id).value
				};
				
				jQuery.post(ajaxurl, data, function(response) {
				document.getElementById('select_' + id).innerHTML = response;
				console.log(response);
				});
				
			};
			</script>
		
		
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
		$instance['show_layout'] = strip_tags($new_instance['show_layout']);		
		$instance['post_id'] = $new_instance['post_id'];
		
		$instance[ 'item_id' ] = $new_instance['item_id'];
		
		
		return $instance;
	}
} // class HbgTimelineWidget