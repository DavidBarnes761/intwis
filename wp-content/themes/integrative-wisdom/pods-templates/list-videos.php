<?php
	/*
	 * Template Name: Videos Page
	 */

	// Get category from URL
	$category = pods_v_sanitized( 'last', 'url' );

	if ( $category ) {
//		echo 'The category is this: ', $category;
	} else {
//		echo 'There is no category.';
	}

	$videos = pods( 'video', [] );
?>
<?php get_header(); ?>
<div class="hero-wrapper">
	<div class="video-filters-wrapper row">
		<nav class="video-filters large-12 columns">
			<?php wp_nav_menu(array('theme_location' => 'video-menu', 'menu_id' => 'video-menu')); ?>
		</nav>
	</div>
</div>
<div id="primary" class="content-area">
	<div id="content" class="site-main" role="main">
		<div class="video-thumbs-wrapper row">
			<div class="video-thumbs large-12 columns">
				<ul class="small-block-grid-2 medium-block-grid-3 large-block-grid-4">
					<?php
						if ( $videos->total() > 0 ) {
							while ( $videos->fetch() ) : ?>
								<li class="video-thumb">
									<a href="<?php echo get_permalink($videos->id()); ?>">
										<div class="thumbnail" data-src="http://img.youtube.com/vi/<?php echo $videos->field('video-id'); ?>/0.jpg">
											<span class="thumb-speaker-name">Speaker Name</span>
											<span class="thumb-video-title">Video Title</span>
										</div>
									</a>
								</li>
							<?php endwhile;
						}
					?>
				</ul>
			</div>
		</div>
	</div>
	<?php get_footer(); ?>
