<?php
add_action( 'wp_enqueue_scripts', 'child_theme_enqueue_styles_and_scripts' );
function child_theme_enqueue_styles_and_scripts() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );    
    wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array('parent-style-app')
    );
}

/*
function debug_load_textdomain( $domain , $mofile  ){
    echo "Trying ",$domain," at ",$mofile,"<br />\n";
}
add_action('load_textdomain','debug_load_textdomain');
*/
?>