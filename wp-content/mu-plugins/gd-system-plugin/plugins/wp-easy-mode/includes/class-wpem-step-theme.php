<?php

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

final class WPEM_Step_Theme extends WPEM_Step {

	/**
	 * Class constructor
	 */
	public function __construct() {

		$this->args = array(
			'name'       => 'theme',
			'title'      => __( 'Theme', 'wp-easy-mode' ),
			'page_title' => __( 'Choose a Theme', 'wp-easy-mode' ),
		);

		add_filter( 'wpem_themes', array( $this, 'themes' ), 10, 2 );

	}

	/**
	 * Step content
	 */
	public function content() {

		?>
		<p class="lead-text align-center"><?php _e( "Choose a design for your website (don't worry, you can change this later)", 'wp-easy-mode' ) ?></p>

		<div class="theme-browser rendered">

			<div class="themes"></div>

		</div>

		<script type="text/html" id="wpem-template-theme">
			<div class="theme">
				<div class="theme-screenshot"><img src="#"></div>
				<span class="more-details"><?php _e( 'Preview', 'wp-easy-mode' ) ?></span>
				<div class="theme-author"><?php _e( 'By', 'wp-easy-mode' ) ?> <span></span></div>
				<h3 class="theme-name"></h3>
				<div class="theme-actions">
					<a href="#" class="button button-primary select-theme"><?php _e( 'Activate', 'wp-easy-mode' ) ?></a>
				</div>
			</div>
		</script>

		<script type="text/html" id="wpem-template-theme-preview">
			<div class="theme-install-overlay wp-full-overlay expanded">
				<div class="wp-full-overlay-sidebar">
					<div class="wp-full-overlay-header">
						<a href="#" class="close-full-overlay"><span class="screen-reader-text"><?php _e( 'Close', 'wp-easy-mode' ) ?></span></a>
						<a href="#" class="previous-theme"><span class="screen-reader-text"><?php _e( 'Previous', 'wp-easy-mode' ) ?></span></a>
						<a href="#" class="next-theme"><span class="screen-reader-text"><?php _e( 'Next', 'wp-easy-mode' ) ?></span></a>
						<a href="#" class="button button-primary theme-install"><?php _e( 'Activate', 'wp-easy-mode' ) ?></a>
					</div>
					<div class="wp-full-overlay-sidebar-content">
						<div class="install-theme-info">
							<h3 class="theme-name"></h3>
							<span class="theme-by"><?php _e( 'By', 'wp-easy-mode' ) ?> <span></span></span>
							<img class="theme-screenshot" src="#" />
							<div class="theme-details">
								<div class="theme-version"><?php _e( 'Version:', 'wp-easy-mode' ) ?> <span></span></div>
								<div class="theme-description"></div>
							</div>
						</div>
					</div>
					<div class="wp-full-overlay-footer">
						<button type="button" class="collapse-sidebar button-secondary expanded" aria-expanded="true" aria-label="<?php esc_attr_e( 'Collapse Sidebar', 'wp-easy-mode' ) ?>">
							<span class="collapse-sidebar-arrow"></span>
							<span class="collapse-sidebar-label"><?php _e( 'Collapse', 'wp-easy-mode' ) ?></span>
						</button>
					</div>
				</div>
				<div class="wp-full-overlay-main"></div>
			</div>
		</script>
		<?php

	}

	/**
	 * Step actions
	 */
	public function actions() {

		?>
		<input type="hidden" id="wpem_selected_theme" name="wpem_selected_theme" value="">
		<?php

	}

	/**
	 * Step callback
	 */
	public function callback() {

		$stylesheet = filter_input( INPUT_POST, 'wpem_selected_theme' );

		$stylesheet = ! empty( $stylesheet ) ? sanitize_key( $stylesheet ) : WP_DEFAULT_THEME;

		$log = new WPEM_Log;

		$log->add_step_field( 'wpem_selected_theme', $stylesheet );

		wpem_wp_cli_exec(
			array( 'theme', 'install', $stylesheet ),
			array(
				'activate' => true,
			)
		);

	}

	/**
	 * Show certain themes based on site type
	 *
	 * @filter wpem_themes
	 *
	 * @param  array  $themes
	 * @param  string $site_type
	 *
	 * @return array
	 */
	public function themes( $themes, $site_type ) {

		switch ( $site_type ) {

			case 'standard':

				$themes = array(
					'twentysixteen',
					'sela',
					'crawford',
					'hemingway',
					'radcliffe',
					'baskerville',
				);

				break;

			case 'blog':

				$themes = array(
					'twentyfifteen',
					'wilson',
					'hoffman',
					'hemingway',
					'isola',
					'kelly',
				);

				break;

			case 'store':

				$themes = array(
					'storefront',
					'store-wp',
					'store',
				);

				break;

		}

		shuffle( $themes );

		return $themes;

	}

}
