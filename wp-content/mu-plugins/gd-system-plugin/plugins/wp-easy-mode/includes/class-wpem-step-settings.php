<?php

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

final class WPEM_Step_Settings extends WPEM_Step {

	/**
	 * Class constructor
	 */
	public function __construct() {

		$this->args = array(
			'name'       => 'settings',
			'title'      => __( 'Settings', 'wp-easy-mode' ),
			'page_title' => __( 'Settings', 'wp-easy-mode' ),
		);

		add_action( 'wpem_template_notices', array( $this, 'display_notice' ) );

	}

	/**
	 * Step content
	 */
	public function content() {

		?>
		<p class="lead-text align-center"><?php _e( 'Please tell us more about your website (all fields are required)', 'wp-easy-mode' ) ?></p>

		<p>
			<label for="wpem_site_type"><?php _e( 'Type', 'wp-easy-mode' ) ?></label>
			<br>
			<select id="wpem_site_type" name="wpem_site_type" required>
				<option value=""><?php _e( '- Select a type -', 'wp-easy-mode' ) ?></option>
				<option value="standard"><?php _e( 'Standard (Recommended)', 'wp-easy-mode' ) ?></option>
				<option value="blog"><?php _e( 'Blog', 'wp-easy-mode' ) ?></option>
				<option value="store"><?php _e( 'Online Store', 'wp-easy-mode' ) ?></option>
			</select>
			<span class="description"><?php _e( 'What type of website are you creating?', 'wp-easy-mode' ) ?></span>
		</p>

		<p>
			<label for="wpem_site_industry"><?php _e( 'Industry', 'wp-easy-mode' ) ?></label>
			<br>
			<select id="wpem_site_industry" name="wpem_site_industry" required>
				<option value=""><?php _e( '- Select an industry -', 'wp-easy-mode' ) ?></option>
				<option value="business"><?php _e( 'Business / Finance / Law', 'wp-easy-mode' ) ?></option>
				<option value="design"><?php _e( 'Design / Art / Portfolio', 'wp-easy-mode' ) ?></option>
				<option value="education"><?php _e( 'Education', 'wp-easy-mode' ) ?></option>
				<option value="health"><?php _e( 'Health / Beauty', 'wp-easy-mode' ) ?></option>
				<option value="construction"><?php _e( 'Home Services / Construction', 'wp-easy-mode' ) ?></option>
				<option value="entertainment"><?php _e( 'Music / Movies / Entertainment', 'wp-easy-mode' ) ?></option>
				<option value="non-profit"><?php _e( 'Non-profit / Causes / Religious', 'wp-easy-mode' ) ?></option>
				<option value="other"><?php _e( 'Other', 'wp-easy-mode' ) ?></option>
				<option value="personal"><?php _e( 'Personal / Family / Wedding', 'wp-easy-mode' ) ?></option>
				<option value="pets"><?php _e( 'Pets / Animals', 'wp-easy-mode' ) ?></option>
				<option value="real-estate"><?php _e( 'Real Estate', 'wp-easy-mode' ) ?></option>
				<option value="restaurant"><?php _e( 'Restaurant / Food', 'wp-easy-mode' ) ?></option>
				<option value="sports"><?php _e( 'Sports / Recreation', 'wp-easy-mode' ) ?></option>
				<option value="transportation"><?php _e( 'Transportation / Automotive', 'wp-easy-mode' ) ?></option>
				<option value="travel"><?php _e( 'Travel / Hospitality / Leisure', 'wp-easy-mode' ) ?></option>
			</select>
			<span class="description"><?php _e( 'What will your website be about?', 'wp-easy-mode' ) ?></span>
		</p>

		<p>
			<label for="blogname"><?php _e( 'Title', 'wp-easy-mode' ) ?></label>
			<br>
			<input type="text" id="blogname" name="blogname" value="<?php echo esc_attr( get_option( 'blogname' ) ) ?>" placeholder="<?php esc_attr_e( 'Enter your website title here', 'wp-easy-mode' ) ?>" required>
			<span class="description"><?php _e( 'The title of your website appears at the top of all pages and in search results.', 'wp-easy-mode' ) ?></span>
		</p>

		<p>
			<label for="blogdescription"><?php _e( 'Tagline', 'wp-easy-mode' ) ?></label>
			<br>
			<input type="text" id="blogdescription" name="blogdescription" value="<?php echo esc_attr( get_option( 'blogdescription' ) ) ?>" placeholder="<?php esc_attr_e( 'Enter your website tagline here', 'wp-easy-mode' ) ?>" required>
			<span class="description"><?php _e( 'Think of the tagline as a slogan that describes what makes your website special. It will also appear in search results.', 'wp-easy-mode' ) ?></span>
		</p>
		<?php

		/**
		 * Fires after the Settings content
		 */
		do_action( 'wpem_step_settings_after_content' );

	}

	/**
	 * Step actions
	 */
	public function actions() {

		?>
		<input type="submit" class="button button-primary" value="<?php echo esc_attr_e( 'Continue', 'wp-easy-mode' ) ?>">
		<?php

	}

	/**
	 * Step callback
	 */
	public function callback() {

		$options = array(
			array(
				'name'      => 'wpem_site_type',
				'label'     => __( 'Type', 'wp-easy-mode' ),
				'sanitizer' => 'sanitize_key',
				'required'  => true,
			),
			array(
				'name'      => 'wpem_site_industry',
				'label'     => __( 'Industry', 'wp-easy-mode' ),
				'sanitizer' => 'sanitize_key',
				'required'  => true,
			),
			array(
				'name'      => 'blogname',
				'label'     => __( 'Title', 'wp-easy-mode' ),
				'sanitizer' => 'sanitize_text_field',
				'required'  => true,
			),
			array(
				'name'      => 'blogdescription',
				'label'     => __( 'Tagline', 'wp-easy-mode' ),
				'sanitizer' => 'sanitize_text_field',
				'required'  => true,
			),
		);

		/**
		 * Filter the options to be saved
		 *
		 * @var array
		 */
		$options = (array) apply_filters( 'wpem_step_settings_options', $options );

		$this->bulk_update_options( $options );

		$this->install_ninja_forms();

		if ( 'store' === wpem_get_site_type() ) {

			$this->install_woocommerce();

		}

	}

	/**
	 * Display template notice
	 *
	 * @action wpem_template_notices
	 */
	public function display_notice() {

		if ( ! filter_input( INPUT_GET, 'error' ) ) {

			return;

		}

		?>
		<ul class="wpem-notice-list">

			<li class="wpem-notice-list-item error-notice"><?php _e( 'All fields are required', 'wp-easy-mode' ) ?></li>

		</ul>
		<?php

	}

	/**
	 * Update options in bulk
	 *
	 * @param array $options
	 */
	private function bulk_update_options( $options ) {

		if ( empty( $options ) || ! is_array( $options ) ) {

			return;

		}

		foreach ( $options as $option ) {

			$name = sanitize_key( $option['name'] );

			$label = ! empty( $option['label'] ) ? $option['label'] : $name;

			$value = filter_input( INPUT_POST, $name );

			// Validate
			if ( ! empty( $option['required'] ) && '' === $value ) {

				wp_safe_redirect(
					add_query_arg(
						array(
							'step'  => $this->name,
							'error' => true,
						),
						wpem_get_wizard_url()
					)
				);

				exit;

			}

			// Sanitize
			if ( ! empty( $option['sanitizer'] ) && function_exists( $option['sanitizer'] ) ) {

				$value = call_user_func( $option['sanitizer'], $value );

			}

			update_option( $name, $value );

			$log = new WPEM_Log;

			$log->add_step_field( $name, $value );

		}

	}

	/**
	 * Install Ninja Forms plugin
	 */
	private function install_ninja_forms() {

		wpem_wp_cli_exec(
			array( 'plugin', 'install', 'ninja-forms' ),
			array(
				'activate' => true,
			)
		);

		// Don't redirect after activation
		delete_transient( '_nf_activation_redirect' );

		$value = get_option( 'nf_admin_notice', array() );

		$value['one_week_support']['dismissed'] = 1;

		$value['two_week_review']['dismissed'] = 1;

		// Remove Ninja Forms admin notices
		update_option( 'nf_admin_notice', $value );

		$post_id = absint( post_exists( 'ninja_forms_preview_page' ) );

		if ( $post_id ) {

			// Trash the "Preview Page" created by Ninja Forms
			wp_delete_post( $post_id, true );

		}

	}

	/**
	 * Install WooCommerce plugin
	 */
	private function install_woocommerce() {

		wpem_wp_cli_exec(
			array( 'plugin', 'install', 'woocommerce' ),
			array(
				'activate' => true,
			)
		);

		// Don't redirect after activation
		delete_transient( '_wc_activation_redirect' );

	}

}
