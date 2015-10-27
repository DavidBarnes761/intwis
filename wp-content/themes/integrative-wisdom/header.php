<?php
/**
 * The header for our theme.
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Integrative_Wisdom
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
	<div id="page" class="hfeed site">
		<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e('Skip to content', 'integrative-wisdom'); ?></a>
		<header id="masthead" class="site-header row" role="banner">
			<div class="site-branding middle small-12 large-3 columns">
				<?php if (is_front_page() && is_home()) : ?>
					<h1 class="site-title"><a href="<?php echo esc_url(home_url('/')); ?>" rel="home"><img title="Integrative Wisdom Home" src="<?php echo get_template_directory_uri(); ?>/img/logo.png" /></a></h1>
				<?php else : ?>
					<p class="site-title"><a href="<?php echo esc_url(home_url('/')); ?>" rel="home"><img title="Integrative Wisdom Home" src="<?php echo get_template_directory_uri(); ?>/img/logo.png" /></a></p>
				<?php endif; ?>
			</div>
			<nav id="site-navigation" class="main-navigation middle large-9 columns" role="navigation">
				<button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false"><?php esc_html_e('Primary Menu', 'integrative-wisdom'); ?></button>
				<?php wp_nav_menu(array('theme_location' => 'primary', 'menu_id' => 'primary-menu')); ?>
				<div class="search-wrapper">
					<form role="search" method="get" class="search-form" action="/">
						<label>
							<input type="search" class="search-field" value="" name="s" title="Search for:">
							<button class="search-button">
								<span aria-hidden="true" class="icon-search"></span>
							</button>
						</label>
						<input type="submit" class="search-submit" value="Search">
					</form>
				</div>
			</nav>
		</header>
		<div id="content" class="site-content">
