<?php

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

final class WPEM_Log {

	/**
	 * Option key
	 */
	const OPTION_KEY = 'wpem_log';

	/**
	 * Log data
	 *
	 * @var array
	 */
	private static $log = array();

	/**
	 * Current step
	 *
	 * @var object
	 */
	private $step;

	/**
	 * Class constructor
	 */
	public function __construct() {

		if ( empty( static::$log ) ) {

			$log = get_option( static::OPTION_KEY );

			if ( $log ) {

				static::$log = json_decode( $log, true );

				return;

			}

			add_action( 'init', array( $this, 'maybe_set_defaults' ) );

		}

	}

	/**
	 * Magic getter method
	 *
	 * @throws Exception
	 *
	 * @param  string $key
	 *
	 * @return mixed
	 */
	public function __get( $key ) {

		if ( property_exists( $this, $key ) ) {

			return $this->{$key};

		}

		if ( isset( static::$log[ $key ] ) ) {

			return static::$log[ $key ];

		}

		throw new Exception( "Unrecognized property: '{$key}'" );

	}

	/**
	 * Add a new log entry
	 *
	 * @param string $key
	 * @param mixed  $value
	 */
	public function add( $key, $value ) {

		static::$log[ $key ] = $value;

		$this->save();

	}

	/**
	 * Get current step for functions who needs it
	 */
	private function get_step() {

		if ( ! isset( $this->step ) ) {

			$this->step = wpem_get_current_step();

		}

	}

	/**
	 * Add a new log entry for a step field
	 *
	 * @param string $key
	 * @param mixed  $value
	 */
	public function add_step_field( $key, $value ) {

		$this->get_step();

		static::$log['steps'][ $this->step->name ]['fields'][ $key ] = $value;

		$this->save();

	}

	/**
	 * Add a new log entry for step time
	 *
	 * @param float $value
	 */
	public function add_step_time( $value ) {

		$this->get_step();

		static::$log['steps'][ $this->step->name ]['took'] = $total = wpem_round( $value );

		$this->save();

		$this->recalculate_total_time();

	}

	/**
	 * Recalculate the total for all time logs
	 */
	public function recalculate_total_time() {

		$total = 0.000;

		foreach ( static::$log['steps'] as $step => $data ) {

			if ( ! isset( $data['took'] ) ) {

				continue;

			}

			$total = wpem_round( $total + $data['took'] );

		}

		$this->add( 'took', $total );

	}

	/**
	 * Set log defaults if not yet present
	 *
	 * @action init
	 */
	public function maybe_set_defaults() {

		$defaults = array(
			'datetime',
			'fqdn',
			'site_url',
			'account_id',
			'user_email',
			'locale',
			'wp_version',
		);

		if ( ! array_diff_key( $defaults, static::$log ) ) {

			return;

		}

		$defaults = array(
			'datetime'   => gmdate( 'c' ),
			'fqdn'       => gethostname(),
			'site_url'   => get_option( 'siteurl' ),
			'account_id' => exec( 'whoami' ),
			'user_email' => get_userdata( 1 )->user_email,
			'locale'     => ( $locale = get_option( 'WPLANG' ) ) ? $locale : 'en_US',
			'wp_version' => get_bloginfo( 'version' ),
		);

		static::$log = $defaults;

		$this->save();

		$geodata = new WPEM_Geodata; // Saves to log

	}

	/**
	 * Save log to the database
	 *
	 * @return bool
	 */
	private function save() {

		return update_option( static::OPTION_KEY, wp_json_encode( static::$log ) );

	}

}
