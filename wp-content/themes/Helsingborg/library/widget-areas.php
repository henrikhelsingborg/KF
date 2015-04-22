<?php

function Helsingborg_sidebar_widgets() {

  register_sidebar(array(
      'id' => 'footer-area',
      'name' => __('Footerarea', 'helsingborg'),
      'description' => __('Arean längst ner', 'helsingborg'),
      'before_widget' => '<div class="large-3 medium-12 columns"><div class="footer-content">',
      'after_widget' => '</div></div>',
      'before_title' => '<h2 class="footer-title">',
      'after_title' => '</h2>'
  ));

  register_sidebar(array(
      'id' => 'slider-area',
      'name' => __('Bildarea', 'helsingborg'),
      'description' => __('Lägg till de sliders som ska visas på sidan.', 'helsingborg'),
      'before_widget' => '<div class="large-12 columns slider-container"><div class="orbit-container"><ul class="example-orbit" data-orbit>',
      'after_widget' => '</ul></div></div>'
  ));

  register_sidebar(array(
      'id' => 'content-area',
      'name' => __('Innehållsarea', 'helsingborg'),
      'description' => __('Lägg till det som ska visas under innehållet.', 'helsingborg')
  ));

  register_sidebar(array(
      'id' => 'content-area-bottom',
      'name' => __('Innehåll bottenarea', 'helsingborg'),
      'description' => __('Lägg till det som ska visas under "Innehållsarea".', 'helsingborg')
  ));

  register_sidebar(array(
      'id' => 'left-sidebar',
      'name' => __('Vänster area', 'helsingborg'),
      'description' => __('Lägg till de widgets som ska visas i högra sidebaren.', 'helsingborg'),
      'before_widget' => '<div class="widget large-12 medium-6 columns"><div class="widget-content">',
      'after_widget' => '</div></div>',
      'before_title' => '<h2>',
      'after_title' => '</h2><div class="divider"><div class="upper-divider"></div><div class="lower-divider"></div></div>'
  ));

  register_sidebar(array(
      'id' => 'left-sidebar-bottom',
      'name' => __('Vänster bottenarea', 'helsingborg'),
      'description' => __('Lägg till de widgets som ska visas i högra sidebaren.', 'helsingborg'),
      'before_widget' => '<div class="widget large-12 medium-6 columns"><div class="widget-content">',
      'after_widget' => '</div></div>',
      'before_title' => '<h2>',
      'after_title' => '</h2><div class="divider"><div class="upper-divider"></div><div class="lower-divider"></div></div>'
  ));

  register_sidebar(array(
      'id' => 'right-sidebar',
      'name' => __('Höger area', 'helsingborg'),
      'description' => __('Lägg till de widgets som ska visas i högra sidebaren.', 'helsingborg'),
      'before_widget' => '<div class="widget large-12 medium-6 columns"><div class="widget-content">',
      'after_widget' => '</div></div>',
      'before_title' => '<h2>',
      'after_title' => '</h2><div class="divider"><div class="upper-divider"></div><div class="lower-divider"></div></div>'
  ));
}

add_action( 'widgets_init', 'Helsingborg_sidebar_widgets' );

?>
