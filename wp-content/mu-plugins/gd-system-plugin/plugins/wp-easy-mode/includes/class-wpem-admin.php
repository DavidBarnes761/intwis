<?php

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

final class WPEM_Admin {

	/**
	 * Slug for WP Easy Mode admin page
	 *
	 * @var string
	 */
	const SLUG = 'wpem';

	/**
	 * Capability required to view and run the setup wizard
	 *
	 * @var string
	 */
	private $cap = 'manage_options';

	/**
	 * Array of registered steps
	 *
	 * @var array
	 */
	private $steps = array();

	/**
	 * Last viewed step
	 *
	 * @var object
	 */
	private $last_viewed;

	/**
	 * Class constructor
	 */
	public function __construct() {

		add_action( 'init', array( $this, 'load' ) );

	}

	/**
	 * Return an array of steps
	 *
	 * @return array
	 */
	public function get_steps() {

		$steps = (array) $this->steps;

		if ( ! $steps ) {

			return array();

		}

		return $steps;

	}

	/**
	 * Load admin area
	 *
	 * @action init
	 */
	public function load() {

		if ( ! current_user_can( $this->cap ) ) {

			return;

		}

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

			return;

		}

		$this->register_steps();

		$this->maybe_force_redirect();

		add_action( 'admin_menu', array( $this, 'menu' ) );

		add_action( 'admin_init', array( $this, 'submit' ) );

		add_action( 'admin_init', array( $this, 'screen' ) );

	}

	/**
	 * Determine if we are viewing the wizard
	 *
	 * @return bool
	 */
	public function is_wizard() {

		return ( current_user_can( $this->cap ) && static::SLUG === filter_input( INPUT_GET, 'page' ) );

	}

	/**
	 * Register the steps used by the wizard
	 */
	private function register_steps() {

		$this->steps = array(
			new WPEM_Step_Start,
			new WPEM_Step_Settings,
			new WPEM_Step_Theme,
		);

		foreach ( $this->steps as $i => $step ) {

			$step->position = $i + 1;

			$step->url = add_query_arg(
				array(
					'step' => $step->name,
				),
				wpem_get_wizard_url()
			);

		}

		$this->last_viewed = $this->get_step_by( 'name', get_option( 'wpem_last_viewed', 'start' ) );

	}

	/**
	 * Force the wizard to be completed
	 *
	 * 1. You cannot bypass the wizard.
	 * 2. You cannot skip ahead to future steps.
	 * 3. You cannot go back to previous steps.
	 */
	private function maybe_force_redirect() {

		if ( ! $this->is_wizard() ) {

			wp_safe_redirect( wpem_get_wizard_url() );

			exit;

		}

		if ( $this->last_viewed->name !== wpem_get_current_step()->name ) {

			wp_safe_redirect( $this->last_viewed->url );

			exit;

		}

	}

	/**
	 * Register admin menu and assets
	 *
	 * @action admin_menu
	 */
	public function menu() {

		add_dashboard_page(
			_x( 'WP Easy Mode', 'Main plugin title', 'wp-easy-mode' ),
			_x( 'Easy Mode', 'Menu title', 'wp-easy-mode' ),
			$this->cap,
			static::SLUG,
			array( $this, 'screen' )
		);

		wp_register_style(
			'wpem-fullscreen',
			WPEM_URL . 'assets/css/fullscreen.css',
			array( 'dashicons', 'buttons', 'install' ),
			WPEM_VERSION
		);

		wp_register_script(
			'jquery-blockui',
			WPEM_URL . 'assets/js/jquery.blockui.min.js',
			array( 'jquery' ),
			'2.70.0'
		);

		wp_register_script(
			'wpem',
			WPEM_URL . 'assets/js/common.js',
			array( 'jquery' ),
			WPEM_VERSION
		);

		wp_register_script(
			'wpem-theme',
			WPEM_URL . 'assets/js/theme.js',
			array( 'wpem' ),
			WPEM_VERSION
		);

		wp_localize_script(
			'wpem',
			'wpem_vars',
			array(
				'step' => wpem_get_current_step()->name,
				'i18n' => array(
					'exit_confirm' => esc_attr__( 'Are you sure you want to exit and configure WordPress on your own?', 'wp-easy-mode' ),
				),
			)
		);

		/**
		 * Filter the list of themes to display
		 *
		 * 1. The 'theme' step must be registered.
		 * 2. Only themes available on WordPress.org are supported.
		 * 3. If a user selects a theme that is not yet installed
		 *    it will be downloaded and activated automatically.
		 *
		 * @param string $site_type
		 *
		 * @var   array
		 */
		$themes = (array) apply_filters(
			'wpem_themes',
			array(
				'twentysixteen',
				'twentyfifteen',
				'twentyfourteen',
				'twentythirteen',
				'twentytwelve',
				'twentyeleven',
			),
			wpem_get_site_type()
		);

		wp_localize_script(
			'wpem-theme',
			'wpem_theme_vars',
			array(
				'themes' => array_map( 'esc_js', array_values( array_unique( $themes ) ) ),
				'i18n'   => array(
					'expand'   => esc_attr__( 'Expand Sidebar', 'wp-easy-mode' ),
					'collapse' => esc_attr__( 'Collapse Sidebar', 'wp-easy-mode' ),
				),
			)
		);

	}

	/**
	 * Listen for POST requests and process them
	 *
	 * @action admin_init
	 */
	public function submit() {

		$nonce = filter_input( INPUT_POST, 'wpem_step_nonce' );

		$name = filter_input( INPUT_POST, 'wpem_step_name' );

		if ( false === wp_verify_nonce( $nonce, sprintf( 'wpem_step_nonce-%s-%d', $name, get_current_user_id() ) ) ) {

			return;

		}

		$step = $this->get_step_by( 'name', $name );

		if ( ! $step ) {

			return;

		}

		$took = filter_input( INPUT_POST, 'wpem_step_took' );

		if ( $took ) {

			$log = new WPEM_Log;

			$log->add_step_time( $took );

		}

		$step->callback();

		$next_step = wpem_get_next_step();

		if ( $next_step ) {

			update_option( 'wpem_last_viewed', $next_step->name );

			wp_safe_redirect( $next_step->url );

			exit;

		}

		new WPEM_Done;

		wp_safe_redirect( wpem_get_customizer_url() );

		exit;

	}

	/**
	 * Register admin menu screen
	 *
	 * @action admin_init
	 */
	public function screen() {

		$template = WPEM_DIR . 'templates/fullscreen.php';

		if ( is_readable( $template ) ) {

			require_once $template;

			exit;

		}

	}

	/**
	 * Get a step by name or actual position
	 *
	 * @param  string $field
	 * @param  mixed  $value
	 *
	 * @return object
	 */
	public function get_step_by( $field, $value ) {

		$steps = (array) $this->steps;

		if ( empty( $steps ) || empty( $value ) ) {

			return;

		}

		if ( 'name' === $field ) {

			foreach ( $steps as $step ) {

				if ( $step->name !== $value ) {

					continue;

				}

				return $step;

			}

		}

		if ( 'position' === $field && is_numeric( $value ) ) {

			foreach ( $steps as $step ) {

				if ( $step->position !== $value ) {

					continue;

				}

				return $step;

			}

		}

	}

}
