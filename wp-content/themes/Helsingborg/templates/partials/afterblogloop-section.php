<?php if ( (is_active_sidebar('content-area') == TRUE) ) : ?>
					<?php dynamic_sidebar("content-area"); ?>
				<?php endif; ?>
			<footer>
				<ul class="socialmedia-list">
					<li class="fbook"><a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(get_the_permalink()); ?>">Facebook</a></li>
					<li class="twitter"><a href="http://twitter.com/share?text=<?php echo strip_tags(get_the_excerpt()); ?>&amp;url=<?php echo urlencode(wp_get_shortlink()); ?>">Twitter</a></li>
				</ul>
			</footer>

			<div class="timestamp">
			<?php if (get_the_modified_time() != get_the_time()) : ?>
 				<p class= "timestamp">Publicerad: <?php the_time('j F, Y'); ?> kl <?php the_time('H:i'); ?> </BR>   Senast ändrad: <?php the_modified_time('j F, Y'); ?> kl <?php the_modified_time('H:i'); ?></p>
 			<?php else: ?>
 				<p class= "timestamp">Publicerad: <?php the_time('j F, Y'); ?> kl <?php the_time('H:i'); ?></p>
 			<?php endif; ?>
 			</div><!-- /.timestamp -->

			</div><!-- /.columns -->
		</div><!-- /.main-content -->

		<div class="lower-content row">
			<div class="sidebar large-4 columns">
				<div class="row">
					<?php if ( (is_active_sidebar('left-sidebar-bottom') == TRUE) ) : ?>
						<?php dynamic_sidebar("left-sidebar-bottom"); ?>
					<?php endif; ?>
				</div><!-- /.row -->
			</div><!-- /.sidebar -->

			<?php if ( (is_active_sidebar('content-area-bottom') == TRUE) ) : ?>
				<?php dynamic_sidebar("content-area-bottom"); ?>
			<?php endif; ?>

		</div><!-- /.lower-content -->
	</div>  <!-- /.main-area -->

	<div class="sidebar sidebar-right large-3 columns">
		<div class="row">

			<?php /* Add the page's widgets */ ?>
			<?php if ( (is_active_sidebar('right-sidebar') == TRUE) ) : ?>
				<?php dynamic_sidebar("right-sidebar"); ?>
			<?php endif; ?>

		</div><!-- /.rows -->
	</div><!-- /.sidebar -->
</div><!-- /.article-page-layout -->