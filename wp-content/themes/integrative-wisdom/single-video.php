<?php
	/**
	 * The template for displaying a single video page.
	 *
	 * Template Name: Single Video
	 */

	$video = pods( 'video', pods_v_sanitized( 'last', 'url' ) );
	$otherVideos = pods( 'video', ['where' => 'id != ' . $video->id()]);
	$speaker = $video->field( 'video-speaker' );
	add_post_meta($video->id, 'description', $video->field( 'video-description' ));
?>
<?php get_header(); ?>
	<div class="hero-wrapper dark">
		<div class="single-video-section row">
			<div class="embed-container large-10 large-centered columns">
				<iframe src="http://www.youtube.com/embed/<?php echo $video->field( 'video-id' ); ?>?autoplay=0&controls=2&iv_load_policy=1&modestbranding=0&rel=0&showinfo=0" frameborder="0" allowfullscreen></iframe>
			</div>
		</div>
	</div>
	<div id="primary" class="content-area">
		<div id="content" class="site-content" role="main">
			<div class="video-info row">
				<div class="video-description large-8 columns">
					<h2 class="video-speaker"><?php echo get_the_title( $speaker['ID'] ); ?></h2>
					<h1 class="video-title"><?php echo get_the_title( $video->id() ); ?></h1>
					<hr class="short double" />
					<?php echo $video->field( 'video-description' ); ?>
					<?php echo the_content(); ?>
				</div>
				<aside class="video-aside large-3 large-offset-1 columns">
					<div class="row">
						<div class="video-share-links large-12 columns">
							<h3>Share this video</h3>
							<?php if ( function_exists( 'ADDTOANY_SHARE_SAVE_KIT' ) ) { ADDTOANY_SHARE_SAVE_KIT(); } ?>
						</div>
					</div>
					<div class="row">
						<div class="email-signup-form large-12 columns">
							<h3>Subscribe to Email List</h3>
							<?php echo do_shortcode( '[contact-form-7 id="380" title="Email Signup 2"]' ); ?>
						</div>
					</div>
					<div class="row">
						<div class="large-12 columns">
							<h3><?php echo get_the_title( $speaker['ID'] ); ?></h3>
							<?php echo get_post_meta( $speaker['ID'], 'speaker-blurb', true ); ?>
							<a class="full-bio-link" href="<?php echo get_the_permalink( $speaker['ID'] ); ?>">Full Bio</a>
						</div>
					</div>
				</aside>
			</div>
			<div class="slider-title-wrapper row">
				<h1 class="slider-title large-12 columns"><span>Watch our latest videos</span></h1>
			</div>
			<div class="video-slider-wrapper row">
				<div class="video-slider large-12 columns">
					<ul class="small-block-grid-1 medium-block-grid-3 large-block-grid-3">
						<?php
							if ( $otherVideos->total() > 0 ) {
								while ( $otherVideos->fetch() ) :
									$speaker = $otherVideos->field( 'video-speaker' ); ?>
									<li class="video-thumb">
										<a href="<?php echo get_permalink( $otherVideos->id() ); ?>">
											<div class="thumbnail group">
												<img src="http://img.youtube.com/vi/<?php echo $otherVideos->field( 'video-id' ); ?>/0.jpg" alt="<?php echo get_the_title( $speaker['ID'] ); ?>" />
												<span class="thumb-speaker-name"><?php echo get_the_title( $speaker['ID'] ); ?></span>
											</div>
											<p class="thumb-video-title"><?php echo get_the_title( $otherVideos->id() ); ?></p>
										</a>
									</li>
								<?php endwhile;
							}
						?>
					</ul>
				</div>
			</div>
		</div>
	</div>
	<script>
		$(document).ready(function() {
			$('.video-slider ul').slick({
				autoplay: true,
				infinite: true,
				slidesToShow: 4,
				slidesToScroll: 1,
				responsive: [
					{
						breakpoint: 1024,
						settings: {
							slidesToShow: 3,
							arrows: false
						}
					},
					{
						breakpoint: 640,
						settings: {
							slidesToShow: 1,
							arrows: false
						}
					}
				]
			});
		});
	</script>
<?php get_footer(); ?>