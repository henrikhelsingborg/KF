<?php do_action('Helsingborg_before_searchform'); ?>
<form role="search" class="search-inputs large-12 columns" method="get" id="searchform" action="<?php echo home_url('/'); ?>">
		<?php do_action('Helsingborg_searchform_top'); ?>
			<input type="text" value="" class="input-field" name="s" id="s" placeholder="<?php esc_attr_e('Vad letar du efter&#63;', 'Helsingborg'); ?>">
			<?php do_action('Helsingborg_searchform_before_search_button'); ?>
            <button id="searchsubmit" class="button search" type="submit"><?php esc_attr_e('S&ouml;k', 'Helsingborg'); ?></button>
			<a href="http://helsingborg.arkivbyran.se/" class="archive-search-link">Du kan också söka i arkivet</a>
		<?php do_action('Helsingborg_searchform_after_search_button'); ?>
</form>
<?php do_action('Helsingborg_after_searchform'); ?>
