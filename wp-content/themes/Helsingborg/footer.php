
</div><!-- /.main-site-container -->

<footer class="footer">
		<div class="row">
				<?php dynamic_sidebar("footer-area"); ?>
		</div>
</footer>

</div><!-- /.inner-wrap -->
</div><!-- /.off-canvas-wrap -->

<script type="text/javascript">
$(document).ready( function() {
	var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
	jQuery.post(ajaxurl, { action: 'big_notification' }, function(response) {
		jQuery('.alert').append(response);
	});
});
</script>



<?php
    // Google Translate
?>
<script type="text/javascript">
function googleTranslateElementInit() {
  new google.translate.TranslateElement({pageLanguage: "sv", autoDisplay: false, gaTrack: true, gaId: "UA-16678811-1"}, "google-translate-element");
}
</script><script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
<?php wp_footer(); ?>
</body>
</html>
