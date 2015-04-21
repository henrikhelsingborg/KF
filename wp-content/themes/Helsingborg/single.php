<?php get_header(); ?>
<?php get_template_part('templates/partials/beforeblogloop','section'); ?>
<div class="row">
	<div class="small-12 medium-12 large-12 columns" role="main">
	<?php while (have_posts()) : the_post(); ?>
		<article <?php post_class() ?> id="post-<?php the_ID(); ?>">
			<header>
				<h1 class="entry-title"><?php the_title(); ?></h1>
				<?php Helsingborg_entry_meta(); ?>
			</header>
			<?php do_action('Helsingborg_post_before_entry_content'); ?>
			<div class="entry-content">

			<?php if ( has_post_thumbnail() ): ?>
				<div class="row">
					<div class="column">
						<?php the_post_thumbnail('', array('class' => 'th')); ?>
					</div>
				</div>
			<?php endif; ?>

			<?php the_content(); ?>
			</div>
			<footer>
				<?php wp_link_pages(array('before' => '<nav id="page-nav"><p>' . __('Pages:', 'Helsingborg'), 'after' => '</p></nav>' )); ?>
				<p><?php the_tags(); ?></p>
			</footer>
			<?php comments_template(); ?>
		</article>
	<?php endwhile;?>
	</div>
</div>
<?php get_template_part('templates/partials/afterblogloop','section'); ?>
<?php get_footer(); ?>