<?php
	/**
	 * The template for displaying the speakers listing page.
	 *
	 * Template Name: Speakers Page
	 */

	$speakers = pods( 'speaker', [] );
?>
<?php get_header(); ?>
<div class="hero-wrapper">
	<div class="gray-bar">&nbsp;</div>
</div>
<div id="primary" class="content-area">
	<div id="content" class="site-main" role="main">
		<?php
			if ( $speakers->total() > 0 ) :
				while ( $speakers->fetch() ) :
					$speakerVideos = $speakers->field( ['name' => 'speaker-videos', 'orderby' => 'id DESC', 'limit' => '3'] ); ?>
					<div class="speaker-listing row">
						<div class="speaker-summary large-6 columns">
							<div class="row">
								<div class="small-12 medium-4 large-4 columns">
									<a href="<?php echo get_permalink( $speakers->id() ); ?>">
										<?php echo get_the_post_thumbnail( $speakers->id(), 'speaker-bio-image' ); ?>
									</a>
								</div>
								<div class="small-12 medium-8 large-8 columns">
									<h1><?php echo get_the_title( $speakers->id() ); ?></h1>
									<p><?php echo $speakers->field( 'speaker-blurb' ); ?></p>
									<a class="full-bio-link" href="<?php echo get_the_permalink( $speakers->id() ); ?>">Full Bio</a>
								</div>
							</div>
						</div>
						<div class="speaker-videos large-6 columns">
							<div class="slider-title-wrapper row">
								<div class="large-12 columns">
									<h2 class="slider-title"><span>Latest videos by this speaker</span></h2>
								</div>
							</div>
							<div class="video-slider-wrapper row">
								<div class="video-slider large-12 columns">
									<ul class="small-block-grid-1 medium-block-grid-3 large-block-grid-3">
										<?php
											if ($speakerVideos) {
												foreach ($speakerVideos as $video) :
													$speaker = get_post_meta($video['ID'], 'video-speaker', true); ?>
													<li class="video-thumb">
														<a href="<?php echo get_permalink($video['ID']); ?>">
															<div class="thumbnail group">
																<img src="http://img.youtube.com/vi/<?php echo get_post_meta($video['ID'], 'video-id', true); ?>/0.jpg" alt="<?php echo get_post_meta($video['ID'], 'video-summary', true); ?>" />
																<span class="thumb-video-title small"><?php echo get_the_title($video['ID']); ?></span>
															</div>
														</a>
													</li>
												<?php endforeach;
											}
										?>
									</ul>
								</div>
							</div>
						</div>
					</div>
				<?php endwhile;
			endif;
		?>
	</div>
</div>
<?php get_footer(); ?>
