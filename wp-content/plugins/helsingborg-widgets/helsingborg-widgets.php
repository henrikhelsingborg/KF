<?php
/*
 * Plugin Name: Helsingborg Widgets
 * Plugin URI: -
 * Description: Skapar en samling med Widgets anpassade för Helsingborg stad
 * Version: 1.0
 * Author: Henric Lind
 * Author URI: -
 *
 * Copyright (C) 2014 Helsingborg stad
 */

// Include the widget files
include_once('classes/link-list-widget.php');
include_once('classes/news-list-widget.php');
include_once('classes/index-widget.php');
include_once('classes/index-large-widget.php');
include_once('classes/image-list-widget.php');
include_once('classes/helsingborg-event-list-widget.php');
include_once('classes/helsingborg-guides.php');
include_once('classes/helsingborg-unfiltered-html.php');
include_once('classes/helsingborg-text-widget.php');
include_once('classes/helsingborg-booking-widget.php');
include_once('classes/helsingborg-timeline-widget.php');
include_once('helsingborg-settings.php');

// Setup event handling
require_once('models/event_model.php');
// require_once ('classes/helsingborg-post-thumbnail.php');

if (!class_exists( 'post_author' ) ) {
    include_once ('classes/post_author.php');
    $post_author_filter = new post_author_filter();
}

// Initiate widgets
$SimpleLinkList     = new SimpleLinkList();
$News_List_Widget   = new News_List_Widget();
$Index_Widget       = new Index_Widget();
$Index_Large_Widget = new Index_Large_Widget();
$Image_List         = new Image_List();
$EventList          = new EventList();
$HbgTextWidget      = new HbgTextWidget();
$HbgBookingWidget   = new HbgBookingWidget();
$HbgTimelineWidget   = new HbgTimelineWidget();

function scripts_and_styles_link_list_widget(){
	// Add resources used by link-list-widget
	wp_enqueue_style( 'helsingborg-widgets-css', plugin_dir_url(__FILE__) .'css/helsingborg-widgets.css');
	//wp_enqueue_script( 'jquery', get_template_directory_uri() . '/js/jquery/dist/jquery.min.js');
	wp_enqueue_script( 'helsingborg-list-sort-js', plugin_dir_url(__FILE__) .'js/helsingborg-list-sort.js');
	wp_enqueue_script( 'helsingborg-media-selector-original-js', plugin_dir_url(__FILE__) .'js/helsingborg-media-selector-original.js');
	wp_enqueue_script( 'steps-js', plugin_dir_url(__FILE__) . 'js/steps.js');
}
add_action( 'admin_enqueue_scripts', 'scripts_and_styles_link_list_widget' );


// Function to purge the cache of a specific page_id/post_id when page widgets is updated
if (!function_exists("hbg_purge_page")) {
    function hbg_purge_page($args) {
        $post_id = $args['post_id'];
        if (function_exists('w3tc_pgcache_flush_post')){
            w3tc_pgcache_flush_post($post_id);
            //print '<!-- Post with id ' . $post_id . ' purged -->';
        }
    }
    add_filter('hbg_page_widget_save', 'hbg_purge_page');
}