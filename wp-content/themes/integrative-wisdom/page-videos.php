<?php
	/**
	 * The template for displaying the videos listing page.
	 *
	 * Template Name: Videos Page
	 */

	// Get category from URL
	$category = pods_v_sanitized( 1, 'url' );
	$params = ['limit' => '0'];

	if ( $category ) {
		$params['where'] = 'category.slug LIKE "' . $category . '"';
	}

	$videos = pods( 'video', $params);
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
				<h1><?php echo the_title(); ?></h1>
				<?php echo the_content(); ?>
				<ul class="small-block-grid-1 medium-block-grid-3 large-block-grid-3">
					<?php
						if ( $videos->total() > 0 ) {
							while ( $videos->fetch() ) :
								$speaker = $videos->field( 'video-speaker' ); ?>
								<li class="video-thumb">
									<a href="<?php echo get_permalink( $videos->id() ); ?>">
										<div class="thumbnail group">
											<img src="http://img.youtube.com/vi/<?php echo $videos->field( 'video-id' ); ?>/0.jpg" />
											<span class="thumb-speaker-name"><?php echo get_the_title( $speaker['ID'] ); ?></span>
										</div>
										<div class="video-description">
											<p class="thumb-video-title"><?php echo get_the_title( $videos->id() ); ?></p>
											<?php echo $videos->field( 'video-summary' ); ?>
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
</div>
<script>
	$(document).ready(function() {
		var url = location.href;
		var menuItem = $('.all-videos');;

		if (url.indexOf('for-students') >= 0) {
			menuItem = $('.for-students');
		} else if (url.indexOf('for-practitioners') >= 0) {
			menuItem = $('.for-practitioners');
		} else if (url.indexOf('for-patients') >= 0) {
			menuItem = $('.for-patients');
		}

		menuItem.find('a').css('color', '#fec630');
		$('.menu-item-75').addClass('current-menu-item');
	});
</script>
<?php get_footer(); ?>
