<?php

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

/**
 * Return the current step
 *
 * @return object
 */
function wpem_get_current_step() {

	if ( ! wp_easy_mode()->admin->is_wizard() ) {

		return;

	}

	$step = wpem_get_step_by( 'name', filter_input( INPUT_GET, 'step' ) );

	return ! empty( $step ) ? $step : wpem_get_step_by( 'position', 1 ); // Default to first step

}

/**
 * Return the next step
 *
 * @return object
 */
function wpem_get_next_step() {

	return wpem_get_step_by( 'position', wpem_get_current_step()->position + 1 );

}

/**
 * Get a step by name or actual position
 *
 * @param  string $field
 * @param  mixed  $value
 *
 * @return object
 */
function wpem_get_step_by( $field, $value ) {

	return wp_easy_mode()->admin->get_step_by( $field, $value );

}

/**
 * Return the URL for the setup wizard
 *
 * @return string
 */
function wpem_get_wizard_url() {

	$url = add_query_arg(
		array(
			'page' => WPEM_Admin::SLUG,
		),
		self_admin_url()
	);

	return $url;

}

/**
 * Return the customizer version of a given URL
 *
 * @param  array $args (optional)
 *
 * @return string
 */
function wpem_get_customizer_url( $args = array() ) {

	$url = self_admin_url( 'customize.php' );

	if ( ! $args || ! is_array( $args ) ) {

		return $url;

	}

	return add_query_arg( array_map( 'urlencode', $args ), $url );

}

/**
 * Return the site type
 *
 * @return string
 */
function wpem_get_site_type() {

	return get_option( 'wpem_site_type', 'standard' );

}

/**
 * Return the site industry
 *
 * @return string
 */
function wpem_get_site_industry() {

	return get_option( 'wpem_site_industry', 'business' );

}

/**
 * Mark the wizard as started
 */
function wpem_mark_as_started() {

	update_option( 'wpem_started', 1 );

	update_option( 'wpem_done', 0 );

}

/**
 * Mark the wizard as done
 */
function wpem_mark_as_done() {

	delete_option( 'wpem_last_viewed' );

	update_option( 'wpem_done', 1 );

	wp_easy_mode()->self_destruct();

	wp_easy_mode()->deactivate();

}

/**
 * Quit the wizard
 */
function wpem_quit() {

	update_option( 'wpem_opt_out', 1 );

	wpem_mark_as_done();

	if ( ! function_exists( 'get_plugins' ) ) {

		require_once ABSPATH . 'wp-admin/includes/plugin.php';

	}

	/**
	 * Filter plugins to be deactivated on quit
	 *
	 * @var array
	 */
	$plugins = apply_filters( 'wpem_deactivate_plugins_on_quit', array_keys( get_plugins() ) );

	if ( is_array( $plugins ) ) {

		deactivate_plugins( $plugins );

	}

	if ( function_exists( 'wp_safe_redirect' ) ) {

		wp_safe_redirect( self_admin_url() );

		exit;

	}

}

/**
 * Execute a WP-CLI command from PHP
 *
 * Example:
 *
 * wpem_wp_cli_exec(
 *     array( 'theme', 'install', 'twentysixteen' ),
 *     array( 'activate' => true )
 * );
 *
 * @param  array $commands
 * @param  array $options (optional)
 *
 * @return mixed
 */
function wpem_wp_cli_exec( $commands, $options = array() ) {

	if ( ! exec( 'wp --info' ) ) {

		wp_die( __( 'Error: WP-CLI not found', 'wp-easy-mode' ) );

	}

	$commands = array_map( 'escapeshellcmd', $commands );

	foreach ( $options as $option => $value ) {

		if ( true === $value || '' === $value ) {

			$commands[] = sprintf( '--%s', $option );

		} else {

			$commands[] = sprintf( '--%s=%s', $option, escapeshellarg( $value ) );

		}

	}

	return exec( 'wp ' . implode( ' ', $commands ) );

}

/**
 * Round a float and preserve trailing zeros
 *
 * @param  float $value
 * @param  int   $precision (optional)
 *
 * @return float
 */
function wpem_round( $value, $precision = 3 ) {

	$precision = absint( $precision );

	return sprintf( "%.{$precision}f", round( $value, $precision ) );

}

/**
 * Get a WPEM page ID by its meta name
 *
 * @param  string $page
 *
 * @return int|bool
 */
function wpem_get_page_id_by_meta_name( $page ) {

	$operator = ( false !== strpos( $page, '%' ) ) ? 'LIKE' : '=';

	global $wpdb;

	$results = $wpdb->get_col(
		$wpdb->prepare(
			"SELECT post_id FROM $wpdb->postmeta WHERE meta_key = 'wpem_page' AND meta_value {$operator} %s",
			$page
		)
	);

	if ( empty( $results[0] ) ) {

		return false;

	}

	return absint( $results[0] );

}
