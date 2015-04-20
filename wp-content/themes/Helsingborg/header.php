<!doctype html>
<html class="no-js" <?php language_attributes(); ?> >
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />

		<title>Helsingborg stad</title>
		<meta name="description" content="">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="pubdate" content="<?php echo the_time('d M Y'); ?>">
		<meta name="moddate" content="<?php echo the_modified_time('d M Y'); ?>">
		<meta name="google-translate-customization" content="10edc883cb199c91-cbfc59690263b16d-gf15574b8983c6459-12"></meta>

		
		<link rel="icon" href="<?php echo get_stylesheet_directory_uri() ; ?>/assets/img/icons/favicon.ico" type="image/x-icon">
		<link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?php echo get_stylesheet_directory_uri() ; ?>/assets/img/icons/apple-touch-icon-144x144-precomposed.png">
		<link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?php echo get_stylesheet_directory_uri() ; ?>/assets/img/icons/apple-touch-icon-114x114-precomposed.png">
		<link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?php echo get_stylesheet_directory_uri() ; ?>/assets/img/icons/apple-touch-icon-72x72-precomposed.png">
		<link rel="apple-touch-icon-precomposed" href="<?php echo get_stylesheet_directory_uri() ; ?>/assets/img/icons/apple-touch-icon-precomposed.png">

		<?php
		$colorcode = get_option('helsingborg_color_code', '#ed8b00');
		if ($colorcode !== NULL) :
			if($colorcode == '') {
				$colorcode = '#ed8b00'; // Default color
			}
			list($r,$g,$b) = array_map('hexdec',str_split(ltrim($colorcode, '#'),2)); ?>
			<style>
				.divider.fade .upper-divider,
				.divider.fade .lower-divider {
					background: rgb(<?php echo "$r,$g,$b" ?>); /* Old browsers */
					/* IE9 SVG, needs conditional override of 'filter' to 'none' */
					background: url(data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiA/Pgo8c3ZnIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgdmlld0JveD0iMCAwIDEgMSIgcHJlc2VydmVBc3BlY3RSYXRpbz0ibm9uZSI+CiAgPGxpbmVhckdyYWRpZW50IGlkPSJncmFkLXVjZ2ctZ2VuZXJhdGVkIiBncmFkaWVudFVuaXRzPSJ1c2VyU3BhY2VPblVzZSIgeDE9IjAlIiB5MT0iMCUiIHgyPSIxMDAlIiB5Mj0iMCUiPgogICAgPHN0b3Agb2Zmc2V0PSI2MCUiIHN0b3AtY29sb3I9IiNlZDhiMDAiIHN0b3Atb3BhY2l0eT0iMSIvPgogICAgPHN0b3Agb2Zmc2V0PSIxMDAlIiBzdG9wLWNvbG9yPSIjZmZmZmZmIiBzdG9wLW9wYWNpdHk9IjEiLz4KICA8L2xpbmVhckdyYWRpZW50PgogIDxyZWN0IHg9IjAiIHk9IjAiIHdpZHRoPSIxIiBoZWlnaHQ9IjEiIGZpbGw9InVybCgjZ3JhZC11Y2dnLWdlbmVyYXRlZCkiIC8+Cjwvc3ZnPg==);
					background: -moz-linear-gradient(left,  rgba(<?php echo "$r,$g,$b" ?>,1) 60%, rgba(255,255,255,1) 100%); /* FF3.6+ */
					background: -webkit-gradient(linear, left top, right top, color-stop(60%,rgba(<?php echo "$r,$g,$b" ?>,1)), color-stop(100%,rgba(255,255,255,1))); /* Chrome,Safari4+ */
					background: -webkit-linear-gradient(left,  rgba(<?php echo "$r,$g,$b" ?>,1) 60%,rgba(255,255,255,1) 100%); /* Chrome10+,Safari5.1+ */
					background: -o-linear-gradient(left,  rgba(<?php echo "$r,$g,$b" ?>,1) 60%,rgba(255,255,255,1) 100%); /* Opera 11.10+ */
					background: -ms-linear-gradient(left,  rgba(<?php echo "$r,$g,$b" ?>,1) 60%,rgba(255,255,255,1) 100%); /* IE10+ */
					background: linear-gradient(to right,  rgba(<?php echo "$r,$g,$b" ?>,1) 60%,rgba(255,255,255,1) 100%); /* W3C */
					filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='<?php echo $colorcode ?>', endColorstr='#ffffff',GradientType=1 ); /* IE6-8 */
				}
				.divider .upper-divider,
		        .divider .lower-divider {
		              background-color: rgb(<?php echo "$r,$g,$b" ?>);
		        }

				.button {
					background-color: rgb(<?php echo "$r,$g,$b" ?>) !important;
					border: 1px solid rgb(<?php echo "$r,$g,$b" ?>) !important;
					color: #FFF !important;
				}
				.input-field {
					border: 2px solid rgb(<?php echo "$r,$g,$b" ?>) !important;
				}
				.current-pager > a {
					background-color: rgb(<?php echo "$r,$g,$b" ?>) !important;
					border: 1px solid rgb(<?php echo "$r,$g,$b" ?>) !important;
					box-shadow: 2px 2px 5px 0 rgba(0, 0, 0, 0.4);
					color: #FFF !important;
				}
			</style>
	<?php endif; ?>
	<?php wp_head(); ?>

	</head>
	<body>
	<div class="off-canvas-wrap" data-offcanvas>
	<div class="inner-wrap">

	<?php get_template_part('templates/partials/mobile','menu'); ?>

	<a class="exit-off-canvas"></a>
	<nav class="mobile-nav" role="navigation">
			<div class="mobile-navigation clearfix" role="navigation">
					<a href="#" class="show-mobile-nav left-off-canvas-toggle">Meny</a>
					<a href="#" class="show-mobile-search">Sök</a>
			</div>
			<div class="mobile-search">
					<div class="mobile-search-input-container">
						<form role="search" method="get" id="searchform" action="<?php echo home_url('/'); ?>">
							<input type="text" class="mobile-search-input" name="s" placeholder="Din S&ouml;kning"/>
							<input type="submit" class="mobile-search-btn" value="s&ouml;k" />
						</form>
					</div>
			</div>
	</nav><!-- mobile top nav -->

	<!-- HEADER START -->
	<div class="main-site-container">
		<div class="site-bg"></div>
			<div class="site-header row">
					<div class="site-logo large-4 medium-4 columns">
							<?php
								// Get the baseurl of this site to set the logo href
								$logo_link = parse_url(get_site_url(), PHP_URL_SCHEME) . '://' . parse_url(get_site_url(), PHP_URL_HOST);
							?>
							<a href="<?php echo $logo_link; ?>" class="logo-link">
								<img src="<?php echo get_stylesheet_directory_uri() ; ?>/assets/img/images/hbg-logo.svg" alt="helsingborg stad" class="logo" />
							</a>
					</div><!-- /.site-logo -->

					<?php Helsingborg_support_menu(); ?>

			</div><!-- /.site-header -->
