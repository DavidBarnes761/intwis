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
			<div class="large-8 columns end">
				<h1><?php echo the_title(); ?></h1>
				<?php echo the_content(); ?>
			</div>
		</div>
	</main>
</div>
<?php get_footer(); ?>
