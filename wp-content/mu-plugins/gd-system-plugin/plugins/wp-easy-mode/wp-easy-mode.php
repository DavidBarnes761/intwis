<?php
/**
 * Plugin Name: WP Easy Mode
 * Description: Helping users launch their new WordPress site in just a few clicks.
 * Version: 1.0.3
 * Author: GoDaddy
 * Author URI: https://www.godaddy.com
 * Text Domain: wp-easy-mode
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

define( 'WPEM_VERSION', '1.0.3' );

define( 'WPEM_PLUGIN', plugin_basename( __FILE__ ) );

define( 'WPEM_DIR', plugin_dir_path( __FILE__ ) );

define( 'WPEM_URL', plugin_dir_url( __FILE__ ) );

define( 'WPEM_INC_DIR', WPEM_DIR . 'includes/' );

define( 'WPEM_LANG_PATH', dirname( WPEM_PLUGIN ) . '/languages' );

/**
 * WP Easy Mode
 *
 * Helping users launch their new WordPress site in just a few clicks.
 *
 * @author Frankie Jarrett <fjarrett@godaddy.com>
 * @author Jonathan Bardo <jbardo@godaddy.com>
 */
final class WPEM_Plugin {

	/**
	 * Plugin instance
	 *
	 * @var object
	 */
	private static $instance;

	/**
	 * Admin object
	 *
	 * @var WPEM_Admin
	 */
	public $admin;

	/**
	 * Class constructor
	 */
	private function __construct() {

		if ( defined( 'WP_CLI' ) && WP_CLI ) {

			require_once WPEM_INC_DIR . 'class-wpem-cli.php';

			return;

		}

		if ( ! is_admin() ) {

			return;

		}

		spl_autoload_register( array( $this, 'autoload' ) );

		require_once WPEM_INC_DIR . 'functions.php';

		if ( ! $this->is_fresh_wp() ) {

			if ( ! $this->is_done() ) {

				add_filter( 'wpem_deactivate_plugins_on_quit', '__return_false' );

				wpem_quit();

			}

			return;

		}

		add_action( 'plugins_loaded', array( $this, 'i18n' ) );

		new WPEM_Customizer;

		if ( $this->is_done() ) {

			$this->self_destruct();

			$this->deactivate();

			add_action( 'init', array( $this, 'maybe_redirect' ) );

			return;

		}

		define( 'WPEM_DOING_STEPS', true );

		require_once WPEM_INC_DIR . 'template-tags.php';

		$this->admin = new WPEM_Admin;

	}

	/**
	 * Get plugin instance
	 *
	 * @return WPEM_Plugin
	 */
	public static function instance() {

		if ( ! static::$instance ) {

			static::$instance = new static();

		}

		return static::$instance;

	}

	/**
	 * Is this a fresh WordPress install?
	 *
	 * @return bool
	 */
	private function is_fresh_wp() {

		$log = new WPEM_Log();

		try {

			$is_fresh = $log->is_fresh_wp;

		} catch ( Exception $e ) {

			$is_fresh = $this->check_is_fresh_wp();

			$log->add( 'is_fresh_wp', $is_fresh );

		}

		return $is_fresh;

	}

	/**
	 * Check the WordPress database for freshness
	 *
	 * @return bool
	 */
	private function check_is_fresh_wp() {

		global $wpdb;

		$highest_post_id = (int) $wpdb->get_var( "SELECT ID FROM {$wpdb->posts} ORDER BY ID DESC LIMIT 0,1" );

		$highest_user_id = (int) $wpdb->get_var( "SELECT ID FROM {$wpdb->users} ORDER BY ID DESC LIMIT 0,1" );

		$is_fresh = ( $highest_post_id <= 2 && 1 === $highest_user_id );

		return (bool) apply_filters( 'wpem_check_is_fresh_wp', $is_fresh );

	}

	/**
	 * Has the wizard already been done?
	 *
	 * @return bool
	 */
	public function is_done() {

		$status = get_option( 'wpem_done' );

		return ! empty( $status );

	}

	/**
	 * Is WPEM running as a standalone plugin?
	 *
	 * @return bool
	 */
	public function is_standalone_plugin() {

		if ( ! function_exists( 'is_plugin_active' ) ) {

			require_once ABSPATH . 'wp-admin/includes/plugin.php';

		}

		return is_plugin_active( WPEM_PLUGIN );

	}

	/**
	 * Redirect away from the wizard screen
	 *
	 * @action init
	 */
	public function maybe_redirect() {

		if ( 'wpem' !== filter_input( INPUT_GET, 'page' ) ) {

			return;

		}

		wp_safe_redirect( self_admin_url() );

		exit;

	}

	/**
	 * Load languages
	 *
	 * @action plugins_loaded
	 */
	public function i18n() {

		load_plugin_textdomain( 'wp-easy-mode', false, WPEM_LANG_PATH );

	}

	/**
	 * Autoload includes and instantiate required objects
	 *
	 * @action plugins_loaded
	 */
	public function autoload( $class ) {

		$path = WPEM_INC_DIR . sprintf( 'class-%s.php', strtolower( str_replace( '_', '-', $class ) ) );

		if ( is_readable( $path ) ) {

			require_once $path;

		}

	}

	/**
	 * Self-destruct the plugin
	 */
	public function self_destruct() {

		if ( ! $this->is_standalone_plugin() ) {

			return;

		}

		/**
		 * Filter to self-destruct when done
		 *
		 * @var bool
		 */
		if ( ! (bool) apply_filters( 'wpem_self_destruct', true ) ) {

			return;

		}

		if ( ! class_exists( 'WP_Filesystem' ) ) {

			require_once ABSPATH . 'wp-admin/includes/file.php';

		}

		WP_Filesystem();

		global $wp_filesystem;

		$wp_filesystem->rmdir( WPEM_DIR, true );

	}

	/**
	 * Deactivate the plugin silently
	 */
	public function deactivate() {

		if ( ! $this->is_standalone_plugin() ) {

			return;

		}

		/**
		 * Filter to deactivate when done
		 *
		 * @var bool
		 */
		if ( ! (bool) apply_filters( 'wpem_deactivate', true ) ) {

			return;

		}

		deactivate_plugins( WPEM_PLUGIN, true );

	}

}

/**
 * Returns the plugin instance
 *
 * @return WPEM_Plugin
 */
function wp_easy_mode() {

	return WPEM_Plugin::instance();

}

wp_easy_mode();
