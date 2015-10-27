<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Integrative_Wisdom
 */

?>

	</div><!-- #content -->

	<footer id="colophon" class="site-footer-wrapper" role="contentinfo">
		<div class="site-footer row">
			<div class="email-signup-wrapper large-6 columns">
				<div class="email-signup">
					<h2>Subscribe to Email List</h2>
					<p>Please enter your email address to be notified when new videos are added.</p>
					<form class="group">
						<input type="text" name="email" placeholder="Your email" />
						<input type="submit" value="Submit" />
					</form>
				</div>
			</div>
			<div class="additional-resources large-3 columns">

			</div>
			<div class="spread-the-word large-3 columns">

			</div>
		</div>
	</footer><!-- #colophon -->
	<div class="site-info-wrapper">
		<div class="site-info row">
			<span class="copyright large-12 columns">&copy; <?php echo date('Y'); ?> Integrative Wisdom. All rights reserved.</span>
		</div>
	</div><!-- .site-info -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
