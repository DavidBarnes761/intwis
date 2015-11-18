<?php

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

/**
 *
 *
 * @version 1.1.0
 *
 * @author Frankie Jarrett <fjarrett@godaddy.com>
 */
class GD_System_Plugin_Admin_Page {

	/**
	 * User cap to view page
	 *
	 * @var string
	 */
	private $cap = 'manage_options';

	/**
	 * Page slug
	 *
	 * @var string
	 */
	private $slug = 'godaddy';

	/**
	 * Menu position
	 *
	 * @var string
	 */
	private $position = '2.000001';

	/**
	 *
	 *
	 * @var array
	 */
	private $tabs = array();

	/**
	 * Class constructor
	 */
	public function __construct() {

		if ( gd_is_mt() || gd_is_reseller() ) {

			return;

		}

		/**
		 * Filter the admin menu position
		 *
		 * Note: By default we will use a long decimal string to reduce the
		 * chance of position conflicts with other menu items, see Codex.
		 *
		 * @return string
		 */
		$this->position = (string) apply_filters( 'gd_system_page_menu_position', $this->position );

		$this->tabs = array(
			'help' => __( 'FAQ &amp; Support', 'gd_system' ),
//			'hire' => __( 'Hire A Pro', 'gd_system' ),
		);

		// Only display hire tab to accounts that have used WPEM
		if ( ! gd_has_used_wpem() ) {

			unset( $this->tabs['hire'] );

		}

		$this->widgets = array(
			array(
				'column'   => 1,
				'name'     => 'messages',
				'title'    => __( 'Messages', 'gd_system' ),
				'dashicon' => 'comments',
			),
		);

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

		add_action( 'admin_menu', array( $this, 'register_menu_page' ) );

	}

	/**
	 * Enqueue admin styles
	 *
	 * @action admin_enqueue_scripts
	 */
	public function admin_enqueue_scripts() {

		wp_enqueue_style( 'gd_system_admin_css', GD_SYSTEM_PLUGIN_URL . 'gd-system-plugin/css/admin.css', array(), '0.0.2' );

	}

	/**
	 * Register menu page
	 *
	 * @action admin_menu
	 */
	public function register_menu_page() {

		add_menu_page(
			__( 'GoDaddy', 'gd_system' ),
			__( 'GoDaddy', 'gd_system' ),
			$this->cap,
			$this->slug,
			array( $this, 'render_menu_page' ),
			'div',
			$this->position
		);

	}

	/**
	 * Render menu page
	 */
	public function render_menu_page() {

		$tab = filter_input( INPUT_GET, 'tab' );

		$tab = ! empty( $tab ) ? $tab : 'help';

		?>
		<div class="wrap">

			<h1><?php echo esc_html( get_admin_page_title() ) ?></h1>

			<?php if ( ! empty( $this->tabs ) ) : ?>

				<h2 class="nav-tab-wrapper">

					<?php foreach ( $this->tabs as $name => $label ) : ?>

						<a href="<?php echo esc_url( add_query_arg( array( 'tab' => $name ) ) ) ?>" class="nav-tab<?php if ( $tab === $name ) : ?> nav-tab-active<?php endif; ?>"><?php echo esc_html( $label ) ?></a>

					<?php endforeach; ?>

				</h2>

			<?php endif;

			if ( array_key_exists( $tab, $this->tabs ) && method_exists( $this, "render_menu_page_{$tab}" ) ) {

				call_user_func( array( $this, "render_menu_page_{$tab}" ) );

			}

	}

	public function render_menu_page_help() {

		$language = get_option( 'WPLANG' );

		$parts = explode( '_', $language );

		$subdomain = isset( $parts[1] ) ? strtolower( $parts[1] ) : 'www';

		?>
		<iframe src="<?php echo esc_url( "https://{$subdomain}.godaddy.com/help/managed-wordpress-1000021" ) ?>" frameborder="0" scrolling="no" width="100%" height="2000px"></iframe>
		<?php

	}

	public function render_menu_page_hire() {

		?>
		<div class="dashboard-widgets-wrap">

			<div id="dashboard-widgets" class="metabox-holder">

				<div id="postbox-container-1" class="postbox-container">

					<div id="normal-sortables" class="meta-box-sortables ui-sortable">

						<div id="dashboard_marketplace" class="postbox">

							<h2 class="hndle ui-sortable-handle"><span>Freelancer Marketplace</span></h2>

							<div class="inside">

								<p><strong>Lorem ipsum dolor sit amet.</strong></p>

								<p>Nullam mattis dapibus cursus. Donec mattis hendrerit porta. Maecenas cursus, magna eget pharetra dignissim.</p>

								<p>Vivamus in lacus molestie, condimentum sem quis, eleifend lectus. Ut vel purus sit amet quam pulvinar posuere vel imperdiet nunc.</p>

								<div style="text-align:right;">

									<a href="#" class="button button-primary">Learn More</a>

								</div>

							</div>

						</div>

					</div>

				</div>

				<div id="postbox-container-2" class="postbox-container">

					<div id="normal-sortables" class="meta-box-sortables ui-sortable">

						<div id="dashboard_pws" class="postbox">

							<h2 class="hndle ui-sortable-handle"><span>Professional Web Services</span></h2>

							<div class="inside">

								<p><strong>Get an industry-specific, ready-to-go website that fits your brand.</strong></p>

								<p>Want a web design service that can create a professional site and get you online in as little as one week? Leave it to the experts at GoDaddy.</p>

								<p>Our Professional Web Services Team uses industry-specific themes to build an effective, eye-catching website for your business.</p>

								<div style="text-align:right;">

									<a href="#" class="button button-primary">Learn More</a>

								</div>

							</div>


						</div>

					</div>

				</div>

				<div id="postbox-container-3" class="postbox-container">

					<div id="normal-sortables" class="meta-box-sortables ui-sortable">

						<div id="dashboard_pro" class="postbox">

							<h2 class="hndle ui-sortable-handle"><span>Expert Services</span></h2>

							<div class="inside">

								<p><strong>Lorem ipsum dolor sit amet.</strong></p>

								<p>Nullam mattis dapibus cursus. Donec mattis hendrerit porta. Maecenas cursus, magna eget pharetra dignissim.</p>

								<p>Vivamus in lacus molestie, condimentum sem quis, eleifend lectus. Ut vel purus sit amet quam pulvinar posuere vel imperdiet nunc.</p>

								<div style="text-align:right;">

									<a href="#" class="button button-primary">Learn More</a>

								</div>

							</div>

						</div>

					</div>

				</div>

			</div>

		</div>
		<?php

	}

}
