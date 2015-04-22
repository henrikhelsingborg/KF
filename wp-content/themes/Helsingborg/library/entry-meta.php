<?php
if(!function_exists('Helsingborg_entry_meta')) :
    function Helsingborg_entry_meta() {
		if ( comments_open() ) {
		  echo '<span class="right">';
		  comments_popup_link( __('No comments yet','helsingborg'), __('1 comment','helsingborg'), __('% comments','helsingborg'), __('comments-link','helsingborg'), __('Comments are off for this post','helsingborg') );
		  echo '</span>';
		}
       echo '<time class="updated" datetime="'. get_the_time('c') .'">'. sprintf(__('Posted on %s at %s.', 'helsingborg'), get_the_time('l, F jS, Y'), get_the_time()) .'</time>';
        echo '<p class="byline author">'. __('Written by', 'helsingborg') .' <a href="'. get_author_posts_url(get_the_author_meta('ID')) .'" rel="author" class="fn">'. get_the_author() .'</a></p>';
    }
endif;
?>