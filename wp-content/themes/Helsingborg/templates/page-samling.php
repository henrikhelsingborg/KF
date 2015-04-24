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
	        <div class="large-12 medium-12 columns clearfix">
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

				<?php
				//Hämta vald Parent-node(page) från metaboxen på samlingssidan
				$hbgMeta = get_post_meta($post->ID, '_helsingborg_meta', true); ?>
				<section class="samlingssidor_output">
					<ul class="row">		
						<?php									
						// Get the child pages of the chosen parent-node(page)
						$pages = get_pages(array(
						  'sort_order' => 'DESC',
						  'sort_column' => 'post_modified',
						  //'child_of' => $post->ID,
						  'child_of' => $hbgMeta['rss_select_id'],
						  'post_type' => 'page',
						  'post_status' => 'publish')
						);
						// Go through all childs
						for ($i = 0; $i < count($pages); $i++) {
							$child_post = $pages[$i];
							$child_post_id = $pages[$i]->ID; 
							$link = get_permalink($child_post_id);
							
							//Hämta (Ja eller Nej) så vi vet om ingress ska visas - (metabox "Visa Ingress" från Advanced Custom Fields plugin)							
						    $visa_ingress_i_samling = get_post_meta($child_post_id, 'visa_ingress_i_samling'); ?>
						<li class="small-12 medium-6 large-4 columns left samling_child_li">	
							<div class="samling_child_content">				
								<?php
								// Try to get the thumbnail for the child-page	
							    if (has_post_thumbnail( $child_post_id ) ) {
							      $image_id = get_post_thumbnail_id( $child_post_id );
							      $image = wp_get_attachment_image_src( $image_id, 'single-post-thumbnail' );
							      $alt_text = get_post_meta($image_id, '_wp_attachment_image_alt', true); 
								} ?>
								<a href="<?php echo $link; ?>"><img src="<?php echo $image[0]; ?>" alt="<?php echo $alt_text; ?>"></a>
								<h2><a href="<?php echo $link; ?>"><?php echo $child_post->post_title; ?></a></h2>
								<?php get_template_part('templates/partials/divider','section'); ?>
						
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
								//kolla om vi ska visa ingressen
								if($visa_ingress_i_samling[0] != 'Nej'){	
									echo '<p class="samling_child_excerpt">'.$excerpt.'</p>'; 
								}
								global $wpdb;	
								//tabellen "_customize_sidebars" anger om artikeln har custom eller generella sidebars
								$customize_sidebars = $wpdb->get_results("SELECT meta_value FROM $wpdb->postmeta WHERE meta_key = '_customize_sidebars' AND post_id ='$child_post_id'",ARRAY_A);	
								if( $customize_sidebars[0]['meta_value'] == 'yes' ) {			
									$sidebars_widgets = $wpdb->get_results("SELECT * FROM $wpdb->postmeta WHERE meta_key = '_sidebars_widgets' AND post_id ='$child_post_id'",ARRAY_A);	
									$sidebars_widgetsUnserialized = unserialize($sidebars_widgets[0]['meta_value']);									
									$widget_id = checkforbookingwidget($sidebars_widgetsUnserialized);
									
									if($widget_id != null || $widget_id != ''){			
										$bookingWidgetsOnThisPostInDatabase = 'widget_'.$child_post_id.'_hbgbookingwidget';			
										$result = $wpdb->get_results("SELECT * FROM $wpdb->options WHERE option_name = '$bookingWidgetsOnThisPostInDatabase'",ARRAY_A);			
										$result_ = unserialize($result[0]['option_value']);			
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