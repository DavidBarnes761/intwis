<?php
	/**
	 * The template for displaying the Why Wisdom Matters page.
	 *
	 * Template Name: Why Wisdom Matters Page
	 */
?>
<?php get_header(); ?>
<div class="hero-wrapper">
	<div class="gray-bar">&nbsp;</div>
</div>
<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">
		<div class="why-wisdom row">
			<div class="small-12 medium-8 large-8 columns">
				<h1><?php echo the_title(); ?></h1>
				<hr>
				<?php echo the_content(); ?>
				<hr>
			</div>
			<div class="medium-4 large-4 columns">
				<?php echo get_the_post_thumbnail(); ?>
			</div>
		</div>
	</main>
</div>
<?php get_footer(); ?>
