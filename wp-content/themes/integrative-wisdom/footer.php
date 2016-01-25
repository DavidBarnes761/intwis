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
		</div>
		<footer id="colophon" class="site-footer-wrapper" role="contentinfo">
			<div class="site-footer row">
				<div class="email-signup-wrapper medium-12 large-6 columns">
					<div class="email-signup">
						<h2>Get Wisdom in your Inbox</h2>
						<p>Please enter your email address to be notified when new videos are added.</p>
						<?php echo do_shortcode( '[contact-form-7 id="202" title="Email Signup"]' ); ?>
					</div>
				</div>
				<div class="additional-resources small-12 medium-6 large-3 columns">
					<h1>Additional Resources</h1>
					<?php wp_nav_menu(array('theme_location' => 'secondary', 'menu_id' => 'secondary-menu')); ?>
				</div>
				<div class="spread-the-word small-12 medium-6 large-3 columns">
					<h1>Spread the Word</h1>
					<?php if ( function_exists( 'ADDTOANY_SHARE_SAVE_KIT' ) ) { ADDTOANY_SHARE_SAVE_KIT(); } ?>
				</div>
			</div>
		</footer>
		<div class="site-info row">
			<div class="copyright-wrapper large-12 columns">
				<a href="http://www.integrativepro.com/" title="Integrative Therapeutics Homepage"><img src="<?php echo get_template_directory_uri(); ?>/img/footerLogo.png" alt="Integrative Therapeutics Logo" /></a>
				<span class="copyright">&copy; <?php echo date('Y'); ?> Integrative Wisdom. All rights reserved.</span>
			</div>
		</div>
	</div>
	<?php wp_footer(); ?>
</body>
</html>
