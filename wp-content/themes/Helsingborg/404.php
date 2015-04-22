<?php get_header(); ?>
 
<div class="row errorpage">

		<article <?php post_class() ?> id="post-<?php the_ID(); ?>">
			<header>
				<h1 class="entry-title"><?php _e('Sidan kan inte hittas', 'helsingborg'); ?></h1>
			</header>
			<div class="small-12 large-12 columns" role="main">

			<div class="entry-content">
				<div class="search-container row">
						<div class="search-inputs large-12 columns">
							<form id="searchform" class="search-inputs large-12 columns" action="<?php echo home_url('/'); ?>" method="get" role="search">
								<input id="s" class="input-field" type="text" placeholder="Vad letar du efter?" name="s" value=""></input>
    							<input id="searchsubmit" class="button search" type="submit" value="Sök"></input>
    							<a class="archive-search-link" href="http://helsingborg.arkivbyran.se/"></a>
							</form>
						</div>
				</div><!-- /.search-container -->
				<div class="error">
					<p class="bottom"><?php _e('Den sida du vill nå kan inte hittas. Du kan använda sökfunktionen för att försöka hitta det du letar efter.', 'helsingborg'); ?></p>
				</div>
				<p><?php _e('<h2>Det här kan du göra för att hitta det du söker:</h2>', 'helsingborg'); ?></p>
				<ul>
					<li><?php _e('Kontrollera stavningen. Har du skrivit rätt adress?', 'helsingborg'); ?></li>
					<li><?php _e('Använd sökrutan ovanför för att söka rätt på det du letar efter.', 'helsingborg'); ?></li>
					<li><?php printf(__('Gå till <a href="%s">startsidan</a>', 'helsingborg'), home_url()); ?></li>
					<li><?php _e('Använd <a href="javascript:history.back()">webbläsarens bakåt-knapp</a>', 'helsingborg'); ?></li>
				</ul>
				<p><?php _e('<h2>Vill du komma i kontakt med Helsingborgs stad?</h2> Du kan ringa till Helsingborg kontaktcenter på telefonnummer 042-10 50 00 för att bli lotsad rätt.', 'helsingborg'); ?></p>

		</div>
		</article>
	</div>
	<?php get_sidebar(); ?>
</div>
<?php get_footer(); ?>
