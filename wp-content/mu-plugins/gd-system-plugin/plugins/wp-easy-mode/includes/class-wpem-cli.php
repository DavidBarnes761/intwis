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
	 * ## EXAMPLES
	 *
	 *     wp easy-mode reset [--yes]
	 */
	public function reset( $args, $assoc_args ) {

		global $wpdb;

		if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) {

			WP_CLI::error( 'WP_DEBUG must be enabled to reset WP Easy Mode.' );

		}

		/**
		 * Confirm
		 */

		if ( ! isset( $assoc_args['yes'] ) ) {

			WP_CLI::confirm( 'Are you sure you want to reset the plugin? This cannot be undone.' );

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
			"SELECT GROUP_CONCAT( table_name ) AS query FROM INFORMATION_SCHEMA.TABLES
				WHERE ( table_name LIKE '{$wpdb->prefix}nf_%' )
					OR ( table_name LIKE '{$wpdb->prefix}ninja_forms_%' )
					OR ( table_name LIKE '{$wpdb->prefix}woocommerce_%' );"
		);

		if ( isset( $mysql[0]->query ) ) {

			$tables = implode( ',', array_unique( explode( ',', $mysql[0]->query ) ) );

			$wpdb->query( "DROP TABLE IF EXISTS {$tables};" );

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
		 * Users
		 */

		WP_CLI::line( 'Removing all users except main admin ...' );

		$wpdb->query( "DELETE FROM {$wpdb->users} WHERE ID > 1" );

		/**
		 * Settings
		 */

		WP_CLI::line( 'Restoring default settings ...' );

		$wpdb->query(
			"DELETE FROM {$wpdb->options}
			WHERE ( option_name LIKE 'wpem_%' )
			OR ( option_name LIKE '%_transient_%' )
			OR ( option_name LIKE 'theme_mods_%' );"
		);

		update_option( 'WPLANG', '' );

		update_option( 'blogname', 'My Site' );

		update_option( 'blogdescription', 'Just another WordPress site' );

		$wpdb->query(
			"DELETE FROM {$wpdb->usermeta}
			WHERE ( meta_key = 'sk_ignore_notice' )
			OR ( meta_key = 'dismissed_wp_pointers'
			AND meta_value
			LIKE '%wpem_%' );"
		);

		WP_CLI::line( 'Deleting all sidebar widgets ...' );

		update_option( 'sidebars_widgets', array( 'wp_inactive_widgets' => array() ) );

		/**
		 * Site content
		 */

		WP_CLI::line( 'Resetting site content ...' );

		$wpdb->query( "TRUNCATE TABLE {$wpdb->posts}" );
		$wpdb->query( "TRUNCATE TABLE {$wpdb->postmeta}" );
		$wpdb->query( "TRUNCATE TABLE {$wpdb->terms}" );
		$wpdb->query( "TRUNCATE TABLE {$wpdb->term_taxonomy}" );
		$wpdb->query( "TRUNCATE TABLE {$wpdb->term_relationships}" );
		$wpdb->query( "TRUNCATE TABLE {$wpdb->termmeta}" );

		/**
		 * Success
		 */

		WP_CLI::success( 'DONE!' );

	}

}

WP_CLI::add_command( 'easy-mode', 'WPEM_CLI' );
