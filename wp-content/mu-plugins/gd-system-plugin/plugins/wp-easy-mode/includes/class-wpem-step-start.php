<?php

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

final class WPEM_Step_Start extends WPEM_Step {

	/**
	 * Class constructor
	 */
	public function __construct() {

		$this->args = array(
			'name'       => 'start',
			'title'      => __( 'Start', 'wp-easy-mode' ),
			'page_title' => __( 'WordPress Setup', 'wp-easy-mode' ),
		);

	}

	/**
	 * Step content
	 */
	public function content() {

		update_option( 'wpem_last_viewed', $this->name );

		?>
		<p><?php _e( "Welcome to our WordPress setup wizard. It's designed to help get your site's basic configurations done quickly and easily so you can get online faster.", 'wp-easy-mode' ) ?></p>

		<p><?php _e( "It's completely optional and will only take a few minutes.", 'wp-easy-mode' ) ?></p>
		<?php

	}

	/**
	 * Step actions
	 */
	public function actions() {

		?>
		<input type="hidden" id="wpem_continue" name="wpem_continue" value="yes">
		<input type="submit" id="wpem_no_thanks" class="button button-secondary" value="<?php esc_attr_e( 'No, thanks', 'wp-easy-mode' ) ?>">
		<input type="submit" class="button button-primary" value="<?php esc_attr_e( 'Continue', 'wp-easy-mode' ) ?>">
		<?php

	}

	/**
	 * Step callback
	 */
	public function callback() {

		$continue = filter_input( INPUT_POST, 'wpem_continue' );

		$log = new WPEM_Log;

		$log->add_step_field( 'wpem_continue', $continue );

		if ( 'no' === $continue ) {

			wpem_quit();

			return;

		}

		if ( isset( $log->geodata ) ) {

			new WPEM_Smart_Defaults( $log->geodata );

		}

		wpem_mark_as_started();

	}

}
