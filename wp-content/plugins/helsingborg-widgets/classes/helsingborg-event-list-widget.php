<?php
if (!class_exists('EventList')) {
  class EventList
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
      register_widget( 'EventListWidget' );
    }
  }
}

if (!class_exists('EventListWidget')) {
  class EventListWidget extends WP_Widget {

    /** constructor */
    function EventListWidget() {
      parent::WP_Widget(false, '* Evenemangskalender', array('description' => 'Listar de senaste evenemangen.'));
    }

    public function widget( $args, $instance ) {
      extract($args);

      $title                = empty($instance['title'])                ? __('Kommande evenemang') : $instance['title'];
      $link_text            = empty($instance['link_text'])            ? __('Fler evenemang')     : $instance['link_text'];
      $link                 = empty($instance['link'])                 ? '#'                      : $instance['link'];
      $amount               = empty($instance['amount'])               ? 5                        : $instance['amount'];
      $administration_units = empty($instance['administration_units']) ? 'helsingborgsstad'       : $instance['administration_units'];

      $ids = array();
      foreach(explode(',',$administration_units) as $value) {
        $id = HelsingborgEventModel::get_administration_id_from_name(trim($value));
        array_push($ids, $id->AdministrationUnitID);
      }
      $administration_ids = implode(',',$ids);

      $reference = $link . "?q=" . $administration_ids;
      echo $before_widget; ?>

      <h2 class="widget-title"><?php echo $title; ?></h2>

      <div class="divider">
          <div class="upper-divider"></div>
          <div class="lower-divider"></div>
      </div>

      <ul class="calendar-list" style="min-height: 30px;">
        <?php // To be filled from AJAX, triggered when page is loaded ?>
        <div class="event-list-loader" id="loading-event" style="margin-top: -5px;"></div>
      </ul><!-- .calendar-list -->

      <a href="<?php echo $reference; ?>" class="read-more"><?php echo $link_text; ?></a>


      <div id="eventModal" class="reveal-modal" data-reveal>
          <img class="modal-image"/>

          <div class="row">
            <div class="modal-event-info large-12 columns">
                <h2 class="modal-title"></h2>
                <p class="modal-description"></p>
                <p class="modal-link"></p>
                <!--<p class="modal-date"></p>-->
            </div>
          </div>
          <!-- IF arrangör exist -->
          <div class="row">
            <div class="large-6 columns" id="event-times">
              <h2 class="section-title">Datum, tid och plats</h2>
              <div class="divider fade">
                <div class="upper-divider"></div>
                <div class="lower-divider"></div>
              </div>

              <ul class="modal-list" id="time-modal"></ul>
            </div><!-- /.modal-column -->
            <div class="large-6 columns" id="event-organizers">
              <h2 class="section-title">Arrangör</h2>
              <div class="divider fade">
                <div class="upper-divider"></div>
                <div class="lower-divider"></div>
              </div>

              <ul class="modal-list" id="organizer-modal"></ul>
            </div><!-- /.modal-column -->
          </div><!-- /.row -->
          <a class="close-reveal-modal">&#215;</a>
      </div>

      <script type="text/javascript">
        var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
      </script>

      <script>
        var events = {};
        jQuery(document).ready(function() {
          var data = { action: 'update_event_calendar', amount: '<?php echo $amount; ?>', ids: '<?php echo $administration_ids; ?>' };
          jQuery.post(ajaxurl, data, function(response) {
            var obj = JSON.parse(response);
            events = obj.events;
            jQuery('.calendar-list').html(obj.list);
          });

          jQuery(document).on('click', '.modalLink', function(event){
              event.preventDefault();
              var image = $('.modal-image');
              var title = $('.modal-title');
              var link = $('.modal-link');
              var date = $('.modal-date');
              var description = $('.modal-description');
              var time_list = $('#time-modal');
              var organizer_list = $('#organizer-modal');
              document.getElementById('event-times').style.display = 'none';
              document.getElementById('event-times').className = 'large-6 columns';
              document.getElementById('event-organizers').style.display = 'none';

              var result;
              for (var i = 0; i < events.length; i++) {
                if (events[i].EventID === this.id) {
                  result = events[i];
                  break;
                }
              }

              var dates_data = { action: 'load_event_dates', id: this.id, location: result.Location };
              jQuery.post(ajaxurl, dates_data, function(response) {
                html = "<li>";
                var dates = JSON.parse(response);
                for (var i=0;i<dates.length;i++) {

                  html += '<span>' + dates[i].Date + '</span>';
                  html += '<span>' + dates[i].Time + '</span>';
                  html += '<span>' + dates_data.location + '</span>';
                }
                html += '</li>';
                jQuery('#time-modal').html(html);
                if (dates.length > 0) {
                  document.getElementById('event-times').style.display = 'block';
                }
              });

              var organizers_data = { action: 'load_event_organizers', id: this.id };
              jQuery.post(ajaxurl, organizers_data, function(response) {
                var organizers = JSON.parse(response); html = '';
                for (var i=0;i<organizers.length;i++) {
                  html += '<li><span>' + organizers[i].Name + '</span></li>';
                }
                jQuery('#organizer-modal').html(html);
                if (organizers.length > 0) {
                  document.getElementById('event-organizers').style.display = 'block';
                } else {
                  document.getElementById('event-times').className = 'large-12 columns';
                }
              });

              jQuery(image).attr("src", result.ImagePath);
              jQuery(title).html(result.Name);
              if (result.Link) {
                jQuery(link).html('<a href="' + result.Link + '" target="blank">' + result.Link + '</a>').show();
              } else {
                jQuery(link).hide();
              }
              jQuery(date).html(result.Date);
              jQuery(description).html(result.Description);
          });
        });
      </script>

      <?php
      echo $after_widget;
    }

    public function update( $new_instance, $old_instance) {
      $instance['title']                = strip_tags($new_instance['title']);
      $instance['link']                 = strip_tags($new_instance['link']);
      $instance['link_text']            = strip_tags($new_instance['link_text']);
      $instance['amount']               = $new_instance['amount'];
      $instance['administration_units'] = strip_tags($new_instance['administration_units']);
      return $instance;
    }

    public function form( $instance ) {
      $instance             = wp_parse_args( (array) $instance, array( 'title' => '', 'text' => '', 'link' => '' ) );
      $title                = strip_tags($instance['title']);
      $link                 = strip_tags($instance['link']);
      $link_text            = strip_tags($instance['link_text']);
      $amount               = empty($instance['amount']) ? 1 : $instance['amount'];
      $administration_units = strip_tags($instance['administration_units']);
  ?>

      <ul class="hbgllw-instructions">
        <li><?php echo __("<b>OBS!</b> Denna widget bör endast användas i <b>Höger area</b> !"); ?></li>
      </ul>

      <ul class="hbgllw-instructions">
        <li><?php echo __("<b>Titel</b> är det som visas i widgetens header."); ?></li>
        <li><?php echo __("<b>Evenemangslänk</b> är länken till sidan som listar alla evenemang."); ?></li>
        <li><?php echo __("<b>Länktext</b> är texten på länken som går till alla evenemang."); ?></li>
        <li><?php echo __("<b>Antal evenemang</b> är hur många widgeten ska visa"); ?></li>
      </ul>

      <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Titel:'); ?></label>
      <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>

      <p><label for="<?php echo $this->get_field_id('link'); ?>"><?php _e('Evenemangslänk:'); ?></label>
      <input class="widefat" id="<?php echo $this->get_field_id('link'); ?>" name="<?php echo $this->get_field_name('link'); ?>" type="text" value="<?php echo esc_attr($link); ?>" /></p>

      <p><label for="<?php echo $this->get_field_id('link_text'); ?>"><?php _e('Länktext:'); ?></label>
      <input class="widefat" id="<?php echo $this->get_field_id('link_text'); ?>" name="<?php echo $this->get_field_name('link_text'); ?>" type="text" value="<?php echo esc_attr($link_text); ?>" /></p>

      <p><label for="<?php echo $this->get_field_id('amount'); ?>"><?php _e('Antal evenemang:'); ?></label>
      <input class="widefat" id="<?php echo $this->get_field_id('amount'); ?>" name="<?php echo $this->get_field_name('amount'); ?>" type="number" value="<?php echo esc_attr($amount); ?>" /></p>

      <p><label for="<?php echo $this->get_field_id('administration_units'); ?>"><?php _e('Förvaltningsenheter:'); ?></label>
      <input class="widefat" id="<?php echo $this->get_field_id('administration_units'); ?>" name="<?php echo $this->get_field_name('administration_units'); ?>" type="text" value="<?php echo esc_attr($administration_units); ?>" /></p>
<?php
    }
  }
}
