<?php
	/**
	 * The template for displaying individual speaker pages.
	 *
	 * Template Name: Single Speaker
	 */

	$speaker = pods( 'speaker', pods_v( 'last', 'url' ) );
	$videos = $speaker->field( 'speaker-videos' );
	$otherSpeakers = pods( 'speaker', ['where' => 'id != ' . $speaker->id(), 'limit' => 4, 'orderby' => 'RAND()'] );
?>
<?php get_header(); ?>
	<div class="hero-wrapper">
		<div class="gray-bar">&nbsp;</div>
	</div>
	<div id="primary" class="content-area">
		<div id="content" class="site-main speaker row" role="main">
			<div class="speaker-bio large-8 columns">
				<div class="biography-wrapper row">
					<div class="biography large-12 columns">
						<?php the_post_thumbnail( 'speaker-bio-image' ); ?>
						<h1 class="speaker-name"><?php the_title(); ?></h1>
						<hr>
						<?php echo $speaker->field( 'speaker-biography' ); ?>
						<?php echo the_content(); ?>
					</div>
				</div>
				<div class="slider-title-wrapper row">
					<h1 class="slider-title large-12 columns"><span>Watch additional videos by this speaker</span></h1>
				</div>
				<div class="video-slider-wrapper row">
					<div class="video-slider large-12 columns">
						<ul class="small-block-grid-2 medium-block-grid-3 large-block-grid-4">
							<?php
								if ($videos) {
									foreach ($videos as $video) : ?>
										<li class="video-thumb">
											<a href="<?php echo get_permalink( $video['ID'] ); ?>" title="<?php echo get_the_title( $video['ID'] ); ?>">
												<div class="thumbnail group">
													<img data-lazy="http://img.youtube.com/vi/<?php echo get_post_meta( $video['ID'], 'video-id', true ); ?>/0.jpg" />
												</div>
												<p class="thumb-video-title"><?php echo get_the_title( $video['ID'] ); ?></p>
											</a>
										</li>
									<?php endforeach;
								}
							?>
						</ul>
					</div>
				</div>
			</div>
			<div class="speaker-sidebar large-4 columns">
				<h2><span>Additional Speakers</span></h2>
				<div class="row">
					<?php
						if ( $otherSpeakers->total() > 0 ) {
							while ( $otherSpeakers->fetch() ) : ?>
								<div class="other-speaker-listing large-12 columns">
										<div class="other-speaker group">
											<a href="<?php echo get_permalink( $otherSpeakers->id() ); ?>">
												<?php echo get_the_post_thumbnail( $otherSpeakers->id(), 'other-speakers-thumbnail' ); ?>
												<span class="other-speaker-name"><?php echo get_the_title( $otherSpeakers->id() ); ?></span>
											</a>
											<hr>
											<span class="other-speaker-blurb"><?php echo $otherSpeakers->field( 'speaker-blurb' ); ?></span>
										</div>
								</div>
							<?php endwhile;
						}
					?>
				</div>
			</div>
		</div>
	</div>
	<script>
		$(document).ready(function() {
			$('.video-slider ul').slick({
				arrows: false,
				autoplay: true,
				infinite: true,
				slidesToShow: 4,
				slidesToScroll: 1
			});
		});
	</script>
<?php get_footer(); ?>