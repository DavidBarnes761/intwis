<?php

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

final class WPEM_Customizer {

	/**
	 * Class constructor
	 */
	public function __construct() {

		if ( ! is_admin() ) {

			return;

		}

		add_action( 'init', array( $this, 'init' ) );

	}

	/**
	 * Register pointer and hooks
	 *
	 * @action init
	 */
	public function init() {

		$pointer = new WPEM_Pointer;

		$pointer->register(
			array(
				'id'        => 'wpem_done',
				'screen'    => 'customize',
				'target'    => '#customize-theme-controls ul',
				'cap'       => 'manage_options',
				'query_var' => array( 'wpem' => 1 ),
				'options'   => array(
					'content'  => wp_kses_post(
						sprintf(
							'<h3>%s</h3><p>%s</p>',
							__( 'Congratulations!', 'wp-easy-mode' ),
							__( "You've just created your website! Use the panel to the left to customize the look &amp; feel of your website's design.", 'wp-easy-mode' )
						)
					),
					'position' => array(
						'edge'  => 'left',
						'align' => 'right',
					),
				),
			)
		);

		add_action( 'customize_controls_print_styles', array( $this, 'print_styles' ) );

	}

	/**
	 * Print custom styles
	 *
	 * @action customize_controls_print_styles
	 */
	public function print_styles() {

		if ( '1' !== filter_input( INPUT_GET, 'wpem' ) ) {

			return;

		}

		?>
		<style type="text/css">
		body.wp-customizer .change-theme {
			display: none;
		}
		</style>
		<?php

	}

}
