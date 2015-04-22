<?php
/*
Template Name: Samling
*/
get_header();
// Get the content, see if <!--more--> is inserted
$the_content = get_extended($post->post_content);
$main = $the_content['main'];
$content = $the_content['extended']; // If content is empty, no <!--more--> tag was used -> content is in $main
?>
<div class="full-width-page-layout samlingssida row">
    <!-- main-page-layout -->
    <div class="main-area large-12 columns">
	    <div class="main-content row">
	        <div class="large-12 medium-12 columns">
	          	<div class="alert row"></div>
			  	<?php get_template_part('templates/partials/header','image'); ?>
	            <div class="row no-image"></div><!-- /.row -->
	            <?php the_breadcrumb(); ?>
	            <?php /* Start loop */ ?>
	            <?php while (have_posts()) : the_post(); ?>
	              	<article class="article" id="article">
	                	<header>
							<?php get_template_part('templates/partials/accessability','menu'); ?>
							<h1 class="article-title"><?php the_title(); ?></h1>
	                	</header>
		                <?php if (!empty($content)) : ?>
		                  	<div class="ingress"><?php echo apply_filters('the_content', $main); ?></div><!-- /.ingress -->
		                <?php endif; ?>
		                <div class="article-body">
		                  	<?php if(!empty($content)){
		                    	echo apply_filters('the_content', $content);
		                  	} else {
		                    	echo apply_filters('the_content', $main);
		                  	} ?>
		                </div>
		                <footer>
		                  	<ul class="socialmedia-list">
		                      	<li class="fbook"><a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(get_the_permalink()); ?>">Facebook</a></li>
							  	<li class="twitter"><a href="http://twitter.com/share?text=<?php echo strip_tags(get_the_excerpt()); ?>&amp;url=<?php echo urlencode(wp_get_shortlink()); ?>">Twitter</a></li>
		                  	</ul>
		                </footer>
		            </article>
	            <?php endwhile; // End the loop ?>
				
				<?php if ( (is_active_sidebar('content-area') == TRUE) ) : ?>
                  <?php dynamic_sidebar("content-area"); ?>
                <?php endif; ?>
				
				
				
				<section class="samlingssidor_output">
					<ul class="row">		
						<?php			
						include_once('dump_r.php'); //debugverktyg ska tas bort när allt är klart
						function shorten_Post_Content($string, $link) {
							//$string = strip_tags($string);
							if (strlen($string) > 200) {
							    $stringCut = substr($string, 0, 200);
							    $string = '<a class="clickable_excerpt_text" href="'.$link.'">'.substr($stringCut, 0, strrpos($stringCut, ' ')).'...</a> <a href="'.$link.'">Läs mer</a>'; 
							}else {
								$string = '<a class="clickable_excerpt_text" href="'.$link.'">'.$string.'...</a><a href="'.$link.'">Läs mer</a>';
							}
							return $string;
						}
						function checkforbookingwidget($sidebars_widgetsUnserialized){
							foreach ($sidebars_widgetsUnserialized as $dd) {
								if($dd != null){
									foreach ($dd as $d) {										
										$string = 'hbgbookingwidget';
										if (stripos($d,$string) !== false) {
										    //dump_r($d);
										    $data = $d;    
											$widget_id = substr($data, strpos($data, "-") + 1);    
											//dump_r($widget_id);
										    return $widget_id;
										} 	
									}
								}
							}
						}			
						// Get the child pages
						$pages = get_pages(array(
						  'sort_order' => 'DESC',
						  'sort_column' => 'post_modified',
						  'child_of' => $post->ID,
						  'post_type' => 'page',
						  'post_status' => 'publish')
						);
						// Go through all childs and compare with selected keys from page
						for ($i = 0; $i < count($pages); $i++) {
							//dump_r($pages[$i]);
							$child_post = $pages[$i];
							$post_id = $pages[$i]->ID; 
							$link = get_permalink($post_id);
							
							// Get some meta data from child
						    $visa_ingress_i_samling = get_post_meta($pages[$i]->ID, visa_ingress_i_samling);
						    //dump_r($visa_ingress_i_samling);
							?>
						<li class="small-12 medium-6 large-4 columns left samling_child_li">	
							<div class="samling_child_content">				
								<?php
								// Try to get the thumbnail for the page	
							    if (has_post_thumbnail( $post_id ) ) {
							      $image_id = get_post_thumbnail_id( $post_id );
							      $image = wp_get_attachment_image_src( $image_id, 'single-post-thumbnail' );
							      $alt_text = get_post_meta($image_id, '_wp_attachment_image_alt', true); 
								} ?>
								<a href="<?php echo $link; ?>"><img src="<?php echo $image[0]; ?>" alt="<?php echo $alt_text; ?>"></a>
								<h2><a href="<?php echo $link; ?>"><?php echo $child_post->post_title; ?></a></h2>
								<div class="divider">
								    <div class="upper-divider"></div>
								    <div class="lower-divider"></div>
								</div>
						
								<?php
								if($pages[$i]->post_excerpt !=null || $pages[$i]->post_excerpt !=''){
									$excerpt = $pages[$i]->post_excerpt;
								}else{
									$excerpt = $pages[$i]->post_content;
									$excerpt = preg_split( '/<!--more(.*?)?-->/', $excerpt );
									$excerpt = $excerpt[0];
									$excerpt = strip_tags($excerpt);		
									$excerpt = shorten_Post_Content($excerpt,$link);		
								}
								if($visa_ingress_i_samling[0] != 'Nej'){	
									echo '<p class="samling_child_excerpt">'.$excerpt.'</p>'; 
								}
								global $wpdb;	
								//tabellen "_customize_sidebars" anger om artikeln har custom eller generella sidebars
								$customize_sidebars = $wpdb->get_results("SELECT meta_value FROM $wpdb->postmeta WHERE meta_key = '_customize_sidebars' AND post_id ='$post_id'",ARRAY_A);	
								//dump_r($customize_sidebars);		
								if( $customize_sidebars[0]['meta_value'] == 'yes' ) {			
									$sidebars_widgets = $wpdb->get_results("SELECT * FROM $wpdb->postmeta WHERE meta_key = '_sidebars_widgets' AND post_id ='$post_id'",ARRAY_A);	
									//dump_r($sidebars_widgets);		
									$sidebars_widgetsUnserialized = unserialize($sidebars_widgets[0]['meta_value']);		
									//dump_r($sidebars_widgetsUnserialized);						
									$widget_id = checkforbookingwidget($sidebars_widgetsUnserialized);
									if($widget_id != null ){			
										$bookingWidgetsOnThisPostInDatabase = 'widget_'.$post_id.'_hbgbookingwidget';			
										$result = $wpdb->get_results("SELECT * FROM $wpdb->options WHERE option_name = '$bookingWidgetsOnThisPostInDatabase'",ARRAY_A);			
										$result_ = unserialize($result[0]['option_value']);			
										//dump_r($result_[$widget_id]);			
										$datum = $result_[$widget_id]['datum'];
										$rubrik_kopknapp = $result_[$widget_id]['rubrik_kopknapp'];
										$lank_till_webbshop = $result_[$widget_id]['lank_till_webbshop'];			
										echo '<p class="samling_child_datum">'.$datum.'</p><div class="samling_child_button"><button id="searchsubmit" class="button" type="submit"><a href="'.$lank_till_webbshop.'">'.$rubrik_kopknapp.'</a></button></div>';			
									} // if bookingwidget exists
								} // if custom sidebars ?>				
							</div>
						</li> <?php	
						} //for loop ?>
					</ul>
				</section>
				
				<?php if ( (is_active_sidebar('content-area-bottom') == TRUE) ) : ?>
					<?php dynamic_sidebar("content-area-bottom"); ?>
				<?php endif; ?>
                

        	</div><!-- /.columns -->
    	</div><!-- /.main-content -->
    </div>  <!-- /.main-area -->
</div><!-- /.article-page-layout -->
</div><!-- /.main-site-container -->
<?php get_footer(); ?>