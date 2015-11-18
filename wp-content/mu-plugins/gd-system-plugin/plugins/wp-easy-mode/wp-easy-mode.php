<?php
/**
 * Plugin Name: WP Easy Mode
 * Description: Helping users launch their new WordPress site in just a few clicks.
 * Version: 1.0.0
 * Author: GoDaddy
 * Author URI: https://www.godaddy.com
 * Text Domain: wp-easy-mode
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

define( 'WPEM_VERSION', '1.0.0' );

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

		if ( defined( 'WP_CLI' ) && WP_CLI && defined( 'WP_DEBUG' ) && WP_DEBUG ) {

			require_once WPEM_INC_DIR . 'class-wpem-cli.php';

		}

		if ( ! is_admin() ) {

			return;

		}

		add_action( 'plugins_loaded', array( $this, 'i18n' ) );

		spl_autoload_register( array( $this, 'autoload' ) );

		new WPEM_Customizer;

		if ( $this->is_done() ) {

			add_action( 'init', array( $this, 'maybe_redirect' ) );

			return;

		}

		define( 'WPEM_DOING_STEPS', true );

		require_once WPEM_INC_DIR . 'functions.php';

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
	 * Has the wizard already been done?
	 *
	 * @return bool
	 */
	public function is_done() {

		$status = get_option( 'wpem_done' );

		return ! empty( $status );

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
	 * Deactivate the plugin silently
	 */
	public function deactivate() {

		if ( ! function_exists( 'deactivate_plugins' ) ) {

			require_once ABSPATH . 'wp-admin/includes/plugin.php';

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
