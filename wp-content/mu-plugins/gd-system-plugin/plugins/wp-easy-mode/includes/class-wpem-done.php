<?php

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

final class WPEM_Done {

	/**
	 * Site type
	 *
	 * @var string
	 */
	private $site_type;

	/**
	 * Menu ID
	 *
	 * @var int
	 */
	private $menu_id;

	/**
	 * Class constructor
	 */
	public function __construct() {

		$this->site_type = wpem_get_site_type();

		$this->permalinks();

		$this->menus();

		$this->pages();

		add_action( 'wpem_page_created', array( $this, 'assign_woocommerce_pages' ), 10, 2 );

		$this->widgets();

		$this->user_meta();

		$this->ninja_forms();

		$this->woocommerce();

		$this->redirect();

	}

	private function permalinks() {

		/**
		 * Always use "Post name" permalink structure for SEO reasons
		 *
		 * See: https://yoast.com/articles/wordpress-seo/#permalink-structure
		 */
		update_option( 'permalink_structure', '/%postname%/' );

	}

	private function menus() {

		// Create nav menu
		$this->menu_id = $this->create_nav_menu( __( 'Primary Menu', 'wp-easy-mode' ) );

		// Assign nav menu to primary location
		wpem_wp_cli_exec( array( 'menu', 'location', 'assign', $this->menu_id, 'primary' ) );

	}

	/**
	 * Create a new nav menu
	 *
	 * @param  string $name
	 *
	 * @return int|bool
	 */
	private function create_nav_menu( $name ) {

		$exists = wp_get_nav_menu_object( $name );

		if ( ! empty( $exists->term_id ) ) {

			return absint( $exists->term_id );

		}

		$id = wp_create_nav_menu( $name );

		if ( is_wp_error( $id ) ) {

			return false;

		}

		return absint( $id );

	}

	/**
	 * Append a post to an existing nav menu
	 *
	 * @param  string|int $menu    Menu ID, name or slug
	 * @param  int        $post_id
	 *
	 * @return bool
	 */
	private function add_to_nav_menu( $menu, $post_id ) {

		$menu = wp_get_nav_menu_object( $menu );

		if ( empty( $menu->term_id ) ) {

			return false;

		}

		$post = get_post( absint( $post_id ) );

		if ( ! $post ) {

			return false;

		}

		wp_update_nav_menu_item(
			$menu->term_id,
			0,
			array(
				'menu-item-object-id' => $post->ID,
				'menu-item-object'    => $post->post_type,
				'menu-item-status'    => 'publish',
				'menu-item-type'      => 'post_type',
				'menu-item-title'     => $post->post_title,
			)
		);

		return true;

	}

	private function pages() {

		// Trash the default "Sample Page" created by WordPress
		wp_delete_post( 2, false );

		$pages = array(
			'store' => array(
				'site_types'        => array( 'store' ),
				'post_title'        => __( 'Store', 'wp-easy-mode' ),
				'post_name'         => esc_html_x( 'store', 'Slug name for Store page, must be URL safe', 'wp-easy-mode' ),
				'post_content_path' => $this->get_sample_content_path( 'store.txt' ),
				'nav_menu'          => $this->menu_id,
				'front_page'        => true,
			),
			'cart' => array(
				'site_types'        => array( 'store' ),
				'post_title'        => __( 'Cart', 'wp-easy-mode' ),
				'post_name'         => esc_html_x( 'cart', 'Slug name for My Cart page, must be URL safe', 'wp-easy-mode' ),
				'post_content_path' => $this->get_sample_content_path( 'cart.txt' ),
			),
			'checkout' => array(
				'site_types'        => array( 'store' ),
				'post_title'        => __( 'Checkout', 'wp-easy-mode' ),
				'post_name'         => esc_html_x( 'checkout', 'Slug name for Checkout page, must be URL safe', 'wp-easy-mode' ),
				'post_content_path' => $this->get_sample_content_path( 'checkout.txt' ),
			),
			'account' => array(
				'site_types'        => array( 'store' ),
				'post_title'        => __( 'My Account', 'wp-easy-mode' ),
				'post_name'         => esc_html_x( 'account', 'Page slug name used in URL, must be URL safe', 'wp-easy-mode' ),
				'post_content_path' => $this->get_sample_content_path( 'my-account.txt' ),
				'nav_menu'          => $this->menu_id,
			),
			'about-me' => array(
				'site_types'        => array( 'blog' ),
				'post_title'        => __( 'About Me', 'wp-easy-mode' ),
				'post_name'         => esc_html_x( 'about', 'Slug name for About Me page, must be URL safe', 'wp-easy-mode' ),
				'post_content_path' => $this->get_sample_content_path( 'about-me.txt' ),
				'nav_menu'          => $this->menu_id,
			),
			'about-us' => array(
				'site_types'        => array( 'standard' ),
				'post_title'        => __( 'About Us', 'wp-easy-mode' ),
				'post_name'         => esc_html_x( 'about', 'Slug name for About Us page, must be URL safe', 'wp-easy-mode' ),
				'post_content_path' => $this->get_sample_content_path( 'about-us.txt' ),
				'nav_menu'          => $this->menu_id,
				'front_page'        => ( 'standard' === $this->site_type ),
			),
			'contact-me' => array(
				'site_types'        => array( 'blog' ),
				'post_title'        => __( 'Contact Me', 'wp-easy-mode' ),
				'post_name'         => esc_html_x( 'contact', 'Slug name for Contact Me page, must be URL safe', 'wp-easy-mode' ),
				'post_content_path' => $this->get_sample_content_path( 'contact-me.txt' ),
				'nav_menu'          => $this->menu_id,
			),
			'contact-us' => array(
				'site_types'        => array( 'standard', 'store' ),
				'post_title'        => __( 'Contact Us', 'wp-easy-mode' ),
				'post_name'         => esc_html_x( 'contact', 'Slug name for Contact Us page, must be URL safe', 'wp-easy-mode' ),
				'post_content_path' => $this->get_sample_content_path( 'contact-us.txt' ),
				'nav_menu'          => $this->menu_id,
			),
			'faq' => array(
				'site_types'        => array( 'standard', 'store' ),
				'post_title'        => __( 'FAQs', 'wp-easy-mode' ),
				'post_name'         => esc_html_x( 'faq', 'Slug name for FAQ page, must be URL safe', 'wp-easy-mode' ),
				'post_content_path' => $this->get_sample_content_path( 'faq.txt' ),
				'nav_menu'          => $this->menu_id,
			),
			'estimates' => array(
				'site_types'        => array( 'standard' ),
				'post_title'        => __( 'Get A Quote', 'wp-easy-mode' ),
				'post_name'         => esc_html_x( 'estimates', 'Slug name for Get A Quote page, must be URL safe', 'wp-easy-mode' ),
				'post_content_path' => $this->get_sample_content_path( 'estimates.txt' ),
				'nav_menu'          => $this->menu_id,
			),
			'testimonials' => array(
				'site_types'        => array( 'standard', 'store' ),
				'post_title'        => __( 'Testimonials', 'wp-easy-mode' ),
				'post_name'         => esc_html_x( 'testimonials', 'Slug name for Testimonials page, must be URL safe', 'wp-easy-mode' ),
				'post_content_path' => $this->get_sample_content_path( 'testimonials.txt' ),
				'nav_menu'          => $this->menu_id,
			),
		);

		$this->create_pages( $pages );

	}

	/**
	 * Get the path for sample page content
	 *
	 * @param  string $file
	 * @param  string $locale
	 *
	 * @return string
	 */
	private function get_sample_content_path( $file, $locale = '' ) {

		$locale = ! empty( $locale ) ? $locale : get_locale();

		$path = WPEM_DIR . sprintf( 'content/%s/%s', $locale, $file );

		if ( ! is_readable( $path ) ) {

			// Fallback to en_US
			$path = WPEM_DIR . sprintf( 'content/en_US/%s', $file );

		}

		return $path;

	}

	/**
	 * Create pages and assign them places
	 *
	 * A unique associative key should be used for each page in the array.
	 * This is for internal naming/identification purposes. We cannot use
	 * the `post_name` field for this because it must be translatable.
	 *
	 * Example:
	 *
	 * $pages = array(
	 *     'about-us' => array(
	 *         'site_types'        => array( 'standard' ),
	 *         'post_title'        => 'About Us',
	 *         'post_name'         => 'about',
	 *         'post_content_path' => '/path/to/about-us.txt',
	 *         'nav_menu'          => 13,
	 *         'front_page'        => true,
	 *     ),
	 * );
	 *
	 * @param array $pages
	 */
	private function create_pages( $pages ) {

		if ( empty( $pages ) || ! is_array( $pages ) ) {

			return;

		}

		foreach ( $pages as $_name => $page ) {

			if (
				empty( $page['site_types'] )
				||
				empty( $page['post_title'] )
				||
				! in_array( $this->site_type, $page['site_types'] )
				||
				post_exists( $page['post_title'] ) > 0
			) {

				continue;

			}

			$post_id = wp_insert_post(
				array(
					'post_type'    => 'page',
					'post_status'  => 'publish',
					'post_title'   => $page['post_title'],
					'post_name'    => ! empty( $page['post_name'] ) ? $page['post_name'] : sanitize_title( $page['post_title'] ),
					'post_content' => ! empty( $page['post_content_path'] ) ? file_get_contents( $page['post_content_path'] ) : null,
				)
			);

			if ( ! $post_id ) {

				continue;

			}

			$post_id = absint( $post_id );

			// Add post meta so we know WPEM created this page
			update_post_meta( $post_id, 'wpem_page', $_name );

			/**
			 * Fires when a page has been created
			 *
			 * @param int    $post_id
			 * @param string $_name   Internal name (this is NOT the post_name field)
			 */
			do_action( 'wpem_page_created', $post_id, $_name );

			if ( ! empty( $page['nav_menu'] ) && wp_get_nav_menu_object( $page['nav_menu'] ) ) {

				$this->add_to_nav_menu( $page['nav_menu'], $post_id );

			}

			if ( ! empty( $page['front_page'] ) ) {

				update_option( 'show_on_front', 'page' );

				update_option( 'page_on_front', $post_id );

			}

		}

	}

	/**
	 * Assign WooCommerce pages as they are created
	 *
	 * @action wpem_page_created
	 *
	 * @param int    $post_id
	 * @param string $_name   Internal name (this is NOT the post_name field)
	 */
	public function assign_woocommerce_pages( $post_id, $_name ) {

		if ( 'store' !== $this->site_type || ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {

			return;

		}

		$post = get_post( $post_id );

		if ( 'store' === $_name ) {

			update_option( 'woocommerce_shop_page_id', $post_id );

		}

		if ( 'cart' === $_name ) {

			update_option( 'woocommerce_cart_page_id', $post_id );

		}

		if ( 'checkout' === $_name ) {

			update_option( 'woocommerce_checkout_page_id', $post_id );

		}

		if ( 'account' === $_name ) {

			update_option( 'woocommerce_myaccount_page_id', $post_id );

		}

	}

	private function widgets() {

		// Delete all widgets
		delete_option( 'sidebars_widgets' );

		// Get all the available sidebars
		$sidebars = wpem_wp_cli_exec( array( 'sidebar', 'list' ), array( 'fields' => 'id', 'format' => 'csv' ) );

		$sidebars = explode( "\n", $sidebars );

		// There is no standard ID for the "main" sidebar, so we need to check a few
		$sidebar = in_array( 'sidebar', $sidebars ) ? 'sidebar' : ( in_array( 'primary', $sidebars ) ? 'primary' : 'sidebar-1' );

		if ( 'standard' === $this->site_type ) {

			// Search widget
			wpem_wp_cli_exec( array( 'widget', 'add', 'search', $sidebar, 1 ) );

		}

		if ( 'blog' === $this->site_type ) {

			// Search widget
			wpem_wp_cli_exec( array( 'widget', 'add', 'search', $sidebar, 1 ) );

			// Recent Posts widget
			wpem_wp_cli_exec(
				array( 'widget', 'add', 'recent-posts', $sidebar, 2 ),
				array(
					'title'  => __( 'Recent Posts', 'wp-easy-mode' ),
					'number' => 5,
				)
			);

			// Archives widget
			wpem_wp_cli_exec(
				array( 'widget', 'add', 'archives', $sidebar, 3 ),
				array(
					'title'    => __( 'Archives', 'wp-easy-mode' ),
					'count'    => 0,
					'dropdown' => 0,
				)
			);

		}

		if ( 'store' === $this->site_type ) {

			// WooCommerce Search widget
			wpem_wp_cli_exec( array( 'widget', 'add', 'woocommerce_product_search', $sidebar, 1 ) );

			// WooCommerce Product Categories widget
			wpem_wp_cli_exec(
				array( 'widget', 'add', 'woocommerce_product_categories', $sidebar, 2 ),
				array(
					'title'              => __( 'Browse Products', 'wp-easy-mode' ),
					'orderby'            => 'name',
					'dropdown'           => 0,
					'count'              => 0,
					'hierarchical'       => 1,
					'show_children_only' => 0,
				)
			);

		}

	}

	private function user_meta() {

		// Don't display the Sidekick nag
		add_user_meta( get_current_user_id(), 'sk_ignore_notice', true );

	}

	private function ninja_forms() {

		if ( ! is_plugin_active( 'ninja-forms/ninja-forms.php' ) ) {

			return;

		}

		// Update localized strings in sample contact form

		$contact_form_meta = array(
			'form_title'     => __( 'Contact Form', 'wp-easy-mode' ),
			'success_msg'    => __( 'Your form has been successfully submitted.', 'wp-easy-mode' ),
			'user_email_msg' => __( 'Thank you so much for contacting us. We will get back to you shortly.', 'wp-easy-mode' ),
		);

		foreach ( $contact_form_meta as $key => $value ) {

			nf_update_object_meta( 1, $key, $value );

		}

		// Add contact form to WPEM contact page
		$this->add_ninja_form_to_page( 'contact-%', 1 );

		// Create sample quote form
		if ( 'standard' === $this->site_type ) {

			$contact_form_data = ninja_forms_serialize_form( 1 );

			// Duplicate the sample contact form
			$quote_form_id = ninja_forms_import_form( $contact_form_data );

			// Update localized strings in sample quote form

			$quote_form_meta = array(
				'form_title'     => __( 'Request A Quote Form', 'wp-easy-mode' ),
				'success_msg'    => __( 'Your form has been successfully submitted.', 'wp-easy-mode' ),
				'user_email_msg' => __( 'Thank you so much for contacting us. We will get back to you shortly.', 'wp-easy-mode' ),
			);

			foreach ( $quote_form_meta as $key => $value ) {

				nf_update_object_meta( $quote_form_id, $key, $value );

			}

			// Add quote form to WPEM estimates page
			$this->add_ninja_form_to_page( 'estimates', $quote_form_id );

		}

	}

	/**
	 * Append a Ninja Form shortcode to WPEM page content
	 *
	 * @param string $page
	 * @param int    $form_id
	 */
	private function add_ninja_form_to_page( $page, $form_id ) {

		$post_id = wpem_get_page_id_by_meta_name( $page );

		if ( ! $post_id ) {

			return;

		}

		$post = get_post( $post_id );

		if ( ! $post ) {

			return;

		}

		$post->post_content .= sprintf( "\n[ninja_forms id=%d]", $form_id );

		wp_update_post(
			array(
				'ID'           => $post_id,
				'post_content' => $post->post_content,
			)
		);

	}

	private function woocommerce() {

		if ( 'store' !== $this->site_type || ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {

			return;

		}

		// Force secure checkout when SSL is present
		if ( is_ssl() ) {

			update_option( 'woocommerce_force_ssl_checkout', 'yes' );

		}

		$log = new WPEM_Log;

		$country = ! empty( $log->geodata['country_code'] ) ? $log->geodata['country_code'] : null;

		$region = ! empty( $log->geodata['region_code'] ) ? $log->geodata['region_code'] : null;

		if ( $country ) {

			$this->woocommerce_set_country_data( $country, $region );

		}

	}

	private function woocommerce_set_country_data( $country, $region ) {

		$data = include trailingslashit( WP_PLUGIN_DIR ) . 'woocommerce/i18n/locale-info.php';

		if ( ! isset( $data[ $country ] ) ) {

			return;

		}

		if ( $region && isset( $data[ $country ]['tax_rates'][ $region ] ) ) {

			update_option( 'woocommerce_default_country', sprintf( '%s:%s', $country, $region ) );

		} else {

			update_option( 'woocommerce_default_country', $country );

		}

		update_option( 'woocommerce_currency', $data[ $country ]['currency_code'] );

		update_option( 'woocommerce_currency_pos', $data[ $country ]['currency_pos'] );

		update_option( 'woocommerce_price_decimal_sep', $data[ $country ]['decimal_sep'] );

		update_option( 'woocommerce_price_thousand_sep', $data[ $country ]['thousand_sep'] );

		update_option( 'woocommerce_dimension_unit', $data[ $country ]['dimension_unit'] );

		update_option( 'woocommerce_weight_unit', $data[ $country ]['decimal_sep'] );

	}

	private function redirect() {

		wpem_mark_as_done();

		if ( 'store' === $this->site_type && is_plugin_active( 'woocommerce/woocommerce.php' ) ) {

			wp_safe_redirect(
				add_query_arg(
					array(
						'page' => 'wc-setup',
					),
					self_admin_url( 'index.php' )
				)
			);

			exit;

		}

		wp_safe_redirect(
			wpem_get_customizer_url(
				array(
					'return' => self_admin_url(),
					'wpem'   => 1,
				)
			)
		);

		exit;

	}

}
