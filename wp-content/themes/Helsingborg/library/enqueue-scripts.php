<?php
if (!function_exists('Helsingborg_scripts')) :
  function Helsingborg_scripts() {

    // deregister the jquery version bundled with wordpress
    wp_deregister_script( 'jquery' );

    // register scripts
    wp_enqueue_script( 'modernizr', get_template_directory_uri() . '/js/modernizr/modernizr.min.js', array(), '1.0.0', false );
    wp_enqueue_script( 'jquery', get_template_directory_uri() . '/js/jquery/dist/jquery.min.js', array(), '1.0.0', false );
    wp_enqueue_script( 'jquery-ui', get_template_directory_uri() . '/js/jquery/dist/jquery-ui.min.js', array(), '1.0.0', false );

     /**
     * EVENT LIST PAGE
     **/
    if ( is_page_template( 'templates/event-list-page.php' )) {
      // Register scripts
      wp_enqueue_script( 'zurb5-multiselect',     get_template_directory_uri() . '/js/foundation-multiselect/zmultiselect/zurb5-multiselect.js', array(), '1.0.0', false );
      wp_enqueue_script( 'jquery-datetimepicker', get_template_directory_uri() . '/js/jquery.datetimepicker.js', array(), '1.0.0', false );
      wp_enqueue_script( 'knockout',              get_template_directory_uri() . '/js/knockout/dist/knockout.js', array(), '3.2.0', false );
      wp_enqueue_script( 'event-list-model',      get_template_directory_uri() . '/js/helsingborg/event_list_model.js', array(), '1.0.0', false );

      // Register styles
      wp_enqueue_style( 'zurb5-multiselect',      get_template_directory_uri() . '/css/multiple-select.css', array(), '1.0.0', 'all' );
      wp_enqueue_style( 'jquery-datetimepicker',  get_template_directory_uri() . '/js/jquery.datetimepicker.css', array(), '1.0.0', 'all' );
      
    }

    wp_enqueue_script( 'foundation', get_template_directory_uri() . '/js/app.js', array('jquery'), '1.0.0', true );
    wp_enqueue_script( 'tablesorter', get_template_directory_uri() . '/js/plugins/jquery.tablesorter.min.js', array(), '1.0.0', true );

    // TODO: Remove! This should be merged into app.js
    wp_enqueue_script( 'dev', get_template_directory_uri() . '/js/dev/hbg.dev.js', array(), '1.0.0', true );
		
	
    // Readspeaker should be added last
    wp_enqueue_script( 'readspeaker', 'http://f1.eu.readspeaker.com/script/5507/ReadSpeaker.js?pids=embhl', array(), '1.0.0', false);
        
    // Enqueue vergic.js previously in footer.php hakan
	wp_enqueue_script( 'script-vergic', get_template_directory_uri() . '/js/helsingborg/vergic.js', array(), '1.0.0', true );
    // Enqueue styles previously in header.php hakan
    wp_enqueue_style( 'style-normalize', get_template_directory_uri() . '/css/normalize.css' );
    wp_enqueue_style( 'style-app', get_template_directory_uri() . '/css/app.css' );    
    
  }
  add_action( 'wp_enqueue_scripts', 'Helsingborg_scripts' );

  function load_custom_wp_admin_style() {
	  //hakan 
	wp_enqueue_script( 'jquery', get_template_directory_uri() . '/js/jquery/dist/jquery.min.js', array(), '1.0.0', false );

    wp_enqueue_style( 'custom_wp_admin_css', get_template_directory_uri() . '/css/admin-hbg.css', false, '1.0.0' );

    wp_enqueue_script( 'jquery-ui', get_template_directory_uri() . '/js/jquery/dist/jquery-ui.min.js', array(), '1.0.0', false );
    wp_enqueue_script( 'select2' , (get_template_directory_uri() . '/js/helsingborg/select2.min.js'), array(), '1.0.0', false);
    
    // Enqueue jquery.multiple.select.js previously in meta-ui-listselection.php hakan
    wp_enqueue_script( 'script-multi-select', get_template_directory_uri() . '/js/dev/jquery.multiple.select.js', array(), '1.0.0', true );
     // Enqueue multiple-select.css previously in meta-ui-listselection.php hakan
     wp_enqueue_style( 'style-multi-select', get_template_directory_uri() . '/css/multiple-select.css' );
    
  }
  add_action( 'admin_enqueue_scripts', 'load_custom_wp_admin_style' );

endif;
?>