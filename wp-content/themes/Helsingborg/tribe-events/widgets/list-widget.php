<?php
/**
 * Events List Widget Template
 * This file overrides the original /views/widgets/list-widget.php.
 *
 * Thus printing the proper containers.
 * @return string
 *
 * @package TribeEventsCalendar
 *
 */
if ( !defined('ABSPATH') ) { die('-1'); }
if ( $posts ) { ?>

<div class="calendar-event-widget widget large-12 medium-6 columns">
    <div class="widget-content">

        <h2 class="widget-title">Evenemang i Helsingborg</h2>

        <div class="divider">
            <div class="upper-divider"></div>
            <div class="lower-divider"></div>
        </div>

        <ul class="calendar-list">

        <?php  $today = strtotime(date('Y-m-d'));

        foreach( $posts as $post ) :
            setup_postdata( $post );

            // Parse the dates presented in the event
            $datetime_start = strtotime(tribe_get_start_date($post));
            $datetime_end = strtotime(tribe_get_end_date($post));

            // Save the events date and time
            $date = date('Y-m-d', $datetime_start);
            $time = date('H:i', $datetime_start); ?>

          <li>
            <?php // Present 'Idag HH:ii' och 'YYYY-mm-dd'
            if ($today > $datetime_start && $today < $datetime_end) { ?>
              <span class="date">Idag <?php echo $time; ?></span>
            <?php } else { ?>
              <span class="date"><?php echo $date; ?></span>
            <?php } ?>

            <a href="<?php echo tribe_get_event_link(); ?>"><?php the_title(); ?></a>
          </li>
        <?php endforeach; ?>

        </ul><!-- .calendar-list -->

      <a href="<?php echo tribe_get_events_link(); ?>" class="read-more">Fler evenemang</a>

  </div><!-- /.widget-content -->
</div><!-- /.widget -->

<?php } ?>
