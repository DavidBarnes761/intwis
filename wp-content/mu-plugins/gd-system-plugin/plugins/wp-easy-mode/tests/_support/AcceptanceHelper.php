<?php
namespace Codeception\Module;
use Codeception\Util\Debug;
use \WP_CLI\Utils;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class AcceptanceHelper extends \Codeception\Module {

	public function msWait( $ms ) {

		usleep( $ms * 1000 );

	}

	public function seeSetting( $option_name, $option_value = null ) {

		// We need to flush the object cache as it might have been updated by our tests
		Utils\wp_clear_object_cache();

		$option = get_option( $option_name );

		$message = sprintf( 'Option name %s does not exist', $option_name );

		$this->assertNotEmpty( $option, $message );

		if ( func_num_args() > 1 ) {

			$message = sprintf( 'Option %s does not contain expected value', $option_name );

			$this->assertEquals( $option, $option_value, $message );

		}

	}

	/**
	 * Make assertion that a plugin is active
	 *
	 * @param $plugin_basename
	 */
	public function canSeePluginActive( $plugin_basename ) {

		$is_active = is_plugin_active( $plugin_basename );

		$message = sprintf( 'Plugin name %s is active', $plugin_basename );

		$this->assertTrue( $is_active, $message );

	}

	/**
	 * Make assertion that a plugin is inactive
	 *
	 * @param $plugin_basename
	 */
	public function canSeePluginInactive( $plugin_basename ) {

		$is_inactive = ! is_plugin_active( $plugin_basename );

		$message = sprintf( 'Plugin name %s is inactive', $plugin_basename );

		$this->assertTrue( $is_inactive, $message );

	}

	/**
	 * Validate that a certain page contains a ninja form
	 *
	 * @param $page
	 * @param $form_shortcode
	 */
	public function canSeePageWithForm( $page, $form_shortcode ) {

		if ( ! function_exists( 'wpem_get_page_id_by_meta_name' ) ) {

			require_once WPEM_INC_DIR . 'functions.php';

		}

		$post_id = wpem_get_page_id_by_meta_name( $page );

		$post = get_post( $post_id );

		$message = sprintf( "The page %s doesn't contain %s", $page, $form_shortcode );

		$string_contains_shortcode = strpos( $post->post_content, $form_shortcode );

		$this->assertNotEquals( false, $string_contains_shortcode, $message );

	}

}
