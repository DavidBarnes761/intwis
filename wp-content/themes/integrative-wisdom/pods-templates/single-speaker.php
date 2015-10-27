<?php
	/**
	 * The template for displaying individual speaker pages.
	 *
	 * Template Name: Single Speaker
	 */

	$speaker = pods( 'speaker', pods_v( 'last', 'url' ) );
	$videos = $speaker->field( 'speaker-videos' );
?>
<?php get_header(); ?>
	<div id="primary" class="content-area">
		<div id="content" class="site-content speaker row" role="main">
			<div class="speaker-bio large-8 columns">
				<div class="biography-wrapper row">
					<div class="biography large-12 columns">
						<?php the_post_thumbnail( 'speaker-bio-image' ); ?>
						<h1 class="speaker-name"><?php the_title(); ?></h1>
						<hr>
						<?php echo $speaker->field( 'speaker-biography' ); ?>
					</div>
				</div>
				<div class="additional-videos-title-wrapper row">
					<h1 class="additional-videos-title large-12 columns"><span>Watch additional videos by this speaker</span></h1>
				</div>
				<div class="video-slider row">
					<?php
						foreach ($videos as $vid) {
							$link = get_the_permalink($vid['ID']);
							$videoId = get_post_meta($vid['ID'], 'video-id', true);
							$thumbnail = '<img src="http://img.youtube.com/vi/' . $videoId . '/0.jpg" />';
							$classes = 'video-slide large-3 columns';
							echo '<div class="', $classes, '">';
								echo '<a href="', $link, '">', $thumbnail, '</a>';
								echo '<div class="video-description">', wp_trim_words(get_post_meta($vid['ID'], 'video-description', true), 20), '</div>';
							echo '</div>';
						}
					?>
				</div>
			</div>
			<div class="speaker-sidebar large-3 large-offset-1 columns">
				<h2><span>Additional Speakers</span></h2>
			</div>
		</div>
	</div>
	<script>
		$('.video-slider').slick({
			arrows: false,
			autoplay: true,
			infinite: true,
			slidesToShow: 4,
			slidesToScroll: 1
		});
	</script>
<?php get_footer(); ?>