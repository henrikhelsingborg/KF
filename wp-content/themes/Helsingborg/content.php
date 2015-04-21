<?php
/**
 * The default template for displaying content. Used for both single and index/archive/search.
 *
 * @subpackage Helsingborg
 * @since Helsingborg 1.0
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header>
		<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
		<div class="divider fade"><div class="upper-divider"></div><div class="lower-divider"></div></div>
		<?php Helsingborg_entry_meta(); ?>
	</header>
	<div class="entry-content">
		<?php if ( has_post_thumbnail() ): ?>
			<div class="row">
				<div class="column">
					<?php the_post_thumbnail('', array('class' => 'th')); ?>
				</div>
			</div>
		<?php endif; ?>
		<?php the_content(__('Continue reading...', 'Helsingborg')); ?>
	</div>
	<footer>
		<?php $tag = get_the_tags(); if (!$tag) { } else { ?><p><?php the_tags(); ?></p><?php } ?>
	</footer>
	<hr />
</article>