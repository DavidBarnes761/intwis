<?php
	/**
	 * The template for displaying home page.
	 *
	 * Template Name: Homepage
	 */
	$videoID = get_option( 'site_settings_homepage_video_id' );

	get_header();
?>

	<div class="homepage-video-wrapper">
		<div id="homepage-video-section" class="row">
			<div class="embed-container large-10 large-centered columns">
				<iframe src='http://www.youtube.com/embed/<?php echo $videoID; ?>?autoplay=0&controls=2&iv_load_policy=3&modestbranding=0&rel=0&showinfo=0' frameborder='0' allowfullscreen></iframe>
			</div>
		</div>
	</div>
	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">
			<div class="title-wrapper row">
				<span class="title-ornament large-3 columns">&nbsp;</span>
				<h1 class="homepage-title large-6 columns"><?php the_title(); ?></h1>
				<span class="title-ornament large-3 columns">&nbsp;</span>
			</div>
			<div class="video-slider row">
				<div class="video-slide large-3 columns"><img src="http://img.youtube.com/vi/cbP2N1BQdYc/0.jpg" /></div>
				<div class="video-slide large-3 columns"><img src="http://img.youtube.com/vi/cbP2N1BQdYc/0.jpg" /></div>
				<div class="video-slide large-3 columns"><img src="http://img.youtube.com/vi/cbP2N1BQdYc/0.jpg" /></div>
				<div class="video-slide large-3 columns"><img src="http://img.youtube.com/vi/cbP2N1BQdYc/0.jpg" /></div>
				<div class="video-slide large-3 columns"><img src="http://img.youtube.com/vi/cbP2N1BQdYc/0.jpg" /></div>
				<div class="video-slide large-3 columns"><img src="http://img.youtube.com/vi/cbP2N1BQdYc/0.jpg" /></div>
				<div class="video-slide large-3 columns"><img src="http://img.youtube.com/vi/cbP2N1BQdYc/0.jpg" /></div>
				<div class="video-slide large-3 columns"><img src="http://img.youtube.com/vi/cbP2N1BQdYc/0.jpg" /></div>
				<div class="video-slide large-3 columns"><img src="http://img.youtube.com/vi/cbP2N1BQdYc/0.jpg" /></div>
			</div>
		</main>
	</div>
<?php get_footer(); ?>
<script>
	jQuery('.video-slider').slick({
		infinite: true,
		slidesToShow: 4,
		slidesToScroll: 1
	});
</script>