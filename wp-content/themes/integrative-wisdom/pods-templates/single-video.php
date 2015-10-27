<?php
	/*
		Template Name: Single Video
	*/

	$video = pods( 'video', pods_v_sanitized( 'last', 'url' ) );
	$speaker = $video->field( 'video-speaker' );
?>
<?php get_header(); ?>
	<div class="hero-wrapper dark">
		<div class="single-video-section row">
			<div class="embed-container large-10 large-centered columns">
				<iframe src="http://www.youtube.com/embed/<?php echo $video->field('video-id'); ?>?autoplay=0&controls=2&iv_load_policy=3&modestbranding=0&rel=0&showinfo=0" frameborder="0" allowfullscreen></iframe>
			</div>
		</div>
	</div>
	<div id="primary" class="content-area">
		<div id="content" class="site-content" role="main">
			<div class="video-info row">
				<div class="video-description large-8 columns">

				</div>
				<aside class="video-aside large-3 large-offset-1 columns">

				</aside>
			</div>
		</main>
	</div>
<?php get_footer(); ?>