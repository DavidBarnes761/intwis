<?php

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

/**
 * Note: Requires WP_DEBUG to be set to TRUE
 */
final class WPEM_CLI extends WP_CLI_Command {

	/**
	 * Reset the WP Easy Mode plugin and WordPress to default values
	 *
	 * ## OPTIONS
	 *
	 * [--yes]
	 * : Answer yes to the confirmation message.
	 *
	 * [--codeception]
	 * : Activate codeception if it wasn't already active
	 *
	 * ## EXAMPLES
	 *
	 *     wp easy-mode reset [--yes]
	 */
	public function reset( $args, $assoc_args ) {

		/**
		 * Confirm
		 */

		if ( ! isset( $assoc_args['yes'] ) ) {

			WP_CLI::confirm( 'Are you sure you want to reset the plugin? This cannot be undone.' );

		}

		/**
		 * Settings
		 */

		WP_CLI::line( 'Restoring default settings ...' );

		global $wpdb;

		$wpdb->query( "DELETE FROM {$wpdb->options} WHERE ( option_name LIKE 'wpem_%' ) OR ( option_name LIKE '%_transient_%' ) OR ( option_name LIKE 'theme_mods_%' );" );

		update_option( 'blogname', 'My Site' );

		update_option( 'blogdescription', 'Just another WordPress site' );

		$wpdb->query( "DELETE FROM {$wpdb->usermeta} WHERE ( meta_key = 'sk_ignore_notice' ) OR ( meta_key = 'dismissed_wp_pointers' AND meta_value LIKE '%wpem_%' );" );

		WP_CLI::line( 'Deleting all sidebar widgets ...' );

		delete_option( 'sidebars_widgets' );

		/**
		 * Nav menus
		 */

		WP_CLI::line( 'Deleting all nav menus ...' );

		$term_ids = get_terms(
			'nav_menu',
			array(
				'fields'     => 'ids',
				'hide_empty' => 0,
			)
		);

		foreach ( $term_ids as $term_id ) {

			wp_delete_term( $term_id, 'nav_menu' );

		}

		/**
		 * Pages and nav menu items
		 */

		WP_CLI::line( 'Deleting all pages and nav menu items ...' );

		$query = new WP_Query(
			array(
				'posts_per_page' => 999,
				'post_type'      => array( 'page', 'nav_menu_item' ),
				'post_status'    => 'any',
			)
		);

		if ( $query->have_posts() ) {

			foreach ( $query->posts as $post ) {

				wp_delete_post( $post->ID, true );

			}

		}

		/**
		 * Plugins
		 */

		WP_CLI::line( 'Deleting plugin: ninja-forms ...' );

		WP_CLI::launch_self( 'plugin deactivate ninja-forms', [], [], false );

		WP_CLI::launch_self( 'plugin delete ninja-forms', [], [], false );

		WP_CLI::line( 'Deleting plugin: woocommerce ...' );

		WP_CLI::launch_self( 'plugin deactivate woocommerce', [], [], false );

		WP_CLI::launch_self( 'plugin delete woocommerce', [], [], false );

		WP_CLI::line( 'Dropping custom database tables ...' );

		$mysql = $wpdb->get_results(
			"SELECT CONCAT( 'DROP TABLE ', GROUP_CONCAT( table_name ) , ';' )
			AS query FROM INFORMATION_SCHEMA.TABLES
			WHERE ( table_name LIKE '{$wpdb->prefix}nf_%' )
			OR ( table_name LIKE '{$wpdb->prefix}ninja_forms_%' )
			OR ( table_name LIKE '{$wpdb->prefix}woocommerce_%' );"
		);

		if ( isset( $mysql[0]->query ) ) {

			$wpdb->query( $mysql[0]->query );

		}

		$wpdb->query( "DELETE FROM {$wpdb->options} WHERE ( option_name LIKE 'nf_%' ) OR ( option_name LIKE '%ninja_forms%' ) OR ( option_name LIKE '%woocommerce%' );" );

		/**
		 * Themes
		 */

		WP_CLI::line( sprintf( 'Activating default theme: %s ...', WP_DEFAULT_THEME ) );

		WP_CLI::launch_self( 'theme install ' . WP_DEFAULT_THEME . ' --activate', [], [], false );

		WP_CLI::line( 'Deleting non-default themes ...' );

		$inactive = shell_exec( 'wp theme list --status=inactive --field=name --format=csv' );

		$inactive = array_filter( explode( "\n", $inactive ) );

		$default_themes = array_filter( $inactive, function ( $theme ) {

			return ( 'twenty' === substr( $theme, 0, 6 ) );

		} );

		$inactive = implode( "\n", array_diff( $inactive, $default_themes ) );

		WP_CLI::launch_self( "theme delete {$inactive}", [], [], false );

		/**
		 * Success
		 */

		WP_CLI::success( 'DONE!' );

	}

}

WP_CLI::add_command( 'easy-mode', 'WPEM_CLI' );
