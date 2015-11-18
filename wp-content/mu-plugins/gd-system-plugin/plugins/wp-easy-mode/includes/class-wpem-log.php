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
	private $log = array();

	/**
	 * Current step
	 *
	 * @var object
	 */
	private $step;

	/**
	 * Class constructor
	 *
	 * @return array
	 */
	public function __construct() {

		$this->step = wpem_get_current_step();

		$log = get_option( static::OPTION_KEY );

		if ( $log ) {

			$this->log = json_decode( $log, true );

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

		if ( isset( $this->log[ $key ] ) ) {

			return $this->log[ $key ];

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

		$this->log[ $key ] = $value;

		$this->save();

	}

	/**
	 * Add a new log entry for a step field
	 *
	 * @param string $key
	 * @param mixed  $value
	 */
	public function add_step_field( $key, $value ) {

		$this->log['steps'][ $this->step->name ]['fields'][ $key ] = $value;

		$this->save();

	}

	/**
	 * Add a new log entry for step time
	 *
	 * @param float $value
	 */
	public function add_step_time( $value ) {

		$this->log['steps'][ $this->step->name ]['took'] = $total = wpem_round( $value );

		$this->save();

		$this->recalculate_total_time();

	}

	/**
	 * Recalculate the total for all time logs
	 */
	public function recalculate_total_time() {

		$steps = (array) $this->log['steps'];

		$total = 0.000;

		foreach ( $this->log['steps'] as $step => $data ) {

			if ( ! isset( $data['took'] ) ) {

				continue;

			}

			$total = wpem_round( $total + $data['took'] );

		}

		$this->add( 'took', $total );

	}

	/**
	 * Save log to the database
	 *
	 * @return bool
	 */
	private function save() {

		return update_option( static::OPTION_KEY, wp_json_encode( $this->log ) );

	}

}
