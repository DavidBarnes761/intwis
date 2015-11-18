<?php

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

final class WPEM_Geodata {

	/**
	 * GeoIP API URL
	 *
	 * @var string
	 */
	const API_URL = 'https://freegeoip.net/json/';

	/**
	 * Alternate GeoIP API URL
	 *
	 * @var string
	 */
	const ALT_API_URL = 'https://telize.com/geoip/';

	/**
	 * Array of geodata
	 *
	 * @var array
	 */
	private $data = array();

	/**
	 * Max seconds for API requests
	 *
	 * @var int
	 */
	private $request_timeout = 5;

	/**
	 * Class constructor
	 *
	 * @return array
	 */
	public function __construct() {

		$ip = filter_input( INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP );

		if ( $this->is_public_ip( $ip ) ) {

			$this->data = $this->get_geodata( $ip );

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

		if ( isset( $this->data[ $key ] ) ) {

			return $this->data[ $key ];

		}

		throw new Exception( "Unrecognized property: '{$key}'" );

	}

	/**
	 * Check if an IP address is valid and public
	 *
	 * @param  string $ip
	 *
	 * @return bool
	 */
	private function is_public_ip( $ip ) {

		// IPv4
		$ip = filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE );

		if ( $ip ) {

			return true;

		}

		// IPv6
		$ip = filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 );

		if ( $ip ) {

			return true;

		}

		return false;

	}

	/**
	 * Normalize geodata between multiple API sources
	 *
	 * @param  array $geodata
	 *
	 * @return array
	 */
	private function normalize( $geodata ) {

		if ( isset( $geodata['country'] ) ) {

			$geodata['country_name'] = $geodata['country'];

		}

		if ( isset( $geodata['latitude'] ) ) {

			$geodata['latitude'] = wpem_round( $geodata['latitude'] );

		}

		if ( isset( $geodata['longitude'] ) ) {

			$geodata['longitude'] = wpem_round( $geodata['longitude'] );

		}

		if ( isset( $geodata['region'] ) ) {

			$geodata['region_name'] = $geodata['region'];

		}

		if ( isset( $geodata['time_zone'] ) ) {

			$geodata['timezone'] = $geodata['time_zone'];

		}

		if ( isset( $geodata['zip_code'] ) ) {

			$geodata['postal_code'] = $geodata['zip_code'];

		}

		$whitelist = array(
			'city',
			'country_code',
			'country_name',
			'ip',
			'latitude',
			'longitude',
			'postal_code',
			'region_code',
			'region_name',
			'timezone',
		);

		$geodata = array_intersect_key( $geodata, array_flip( $whitelist ) );

		ksort( $geodata );

		return $geodata;

	}

	/**
	 * Return the geodata of a given IP address
	 *
	 * @param  string $ip
	 *
	 * @return array
	 */
	private function get_geodata( $ip ) {

		$response = $this->request( static::API_URL, $ip );

		if ( ! $response ) {

			$response = $this->request( static::ALT_API_URL, $ip );

		}

		if ( ! $response ) {

			return array();

		}

		return $this->normalize( $response );

	}

	/**
	 * Request geodata from the API
	 *
	 * @param  string $url
	 * @param  string $ip
	 *
	 * @return array|bool
	 */
	private function request( $url, $ip ) {

		$url = esc_url_raw( trailingslashit( $url ) . $ip );

		$response = wp_remote_get( $url, array( 'timeout' => $this->request_timeout ) );

		if ( is_wp_error( $response ) || empty( $response ) ) {

			return false;

		}

		return json_decode( wp_remote_retrieve_body( $response ), true );

	}

}