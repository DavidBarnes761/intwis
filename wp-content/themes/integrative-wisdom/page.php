<?php
	/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Integrative_Wisdom
 */
?>
<?php get_header(); ?>
<div class="hero-wrapper">
	<div class="gray-bar">&nbsp;</div>
</div>
<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">
		<div class="row">
			<div class="large-12 columns">
				<?php while ( have_posts() ) : the_post(); ?>
					<?php get_template_part( 'template-parts/content', 'page' ); ?>
					<?php
						// If comments are open or we have at least one comment, load up the comment template.
						if ( comments_open() || get_comments_number() ) :
							comments_template();
						endif;
					?>
				<?php endwhile; // End of the loop. ?>
			</div>
		</div>
	</main>
</div>
<?php get_footer(); ?>
