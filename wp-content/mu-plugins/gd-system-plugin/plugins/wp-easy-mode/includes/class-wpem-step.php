<?php

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

abstract class WPEM_Step {

	/**
	 * Array of args
	 *
	 * @var array
	 */
	protected $args = array();

	/**
	 * Class constructor
	 */
	abstract public function __construct();

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

		if ( isset( $this->args[ $key ] ) ) {

			return $this->args[ $key ];

		}

		throw new Exception( "Unrecognized property: '{$key}'" );

	}

	/**
	 * Magic setter method
	 *
	 * @param string $key
	 * @param mixed  $value
	 */
	public function __set( $key, $value ) {

		$this->args[ $key ] = $value;

	}

	/**
	 * Step content
	 */
	abstract public function content();

	/**
	 * Step actions
	 */
	abstract public function actions();

	/**
	 * Step callback
	 */
	abstract public function callback();

}
