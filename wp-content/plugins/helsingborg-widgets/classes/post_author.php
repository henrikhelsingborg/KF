<?php
class post_author_filter {

    /**
     * Constructor
     *
     * Register all actions and filters
     */
    function __construct() {
        add_filter( 'restrict_manage_posts' ,   array( 'post_author_filter', 'add_author_filter' ) );
    }

    /**
     * Adds the author selectbox to the lis post/page site
     *
     */
    static public  function add_author_filter() {
        $arguments = array( 'name' => 'author',
                            'show_option_all' => __( 'Alla redaktörer', 'post_author_filter' )
                          );
        if ( isset( $_GET[ 'user' ] ) ) {
            $arguments[ 'selected' ] = $_GET[ 'user' ];
        }
        wp_dropdown_users( $arguments );
    }
}
?>
