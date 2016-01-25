<?php
use Codeception\Util\Debug;

class UserTestCest {

	const PRIMARY_BUTTON = 'input.button-primary';

	/**
	 * Reset DB to default state
	 */
	protected function reset() {

		Debug::debug( 'Resetting WordPress ...' );

		WP_CLI::launch_self( 'easy-mode reset', [], [ 'yes' => true ], false );

	}

	/**
	 * Helping function to login without checking cookie since not compatible
	 * with browserstack ie8-9
	 *
	 * @param AcceptanceTester $I
	 */
	protected function login( AcceptanceTester $I ) {

		Debug::debug( 'Logging into WordPress ...' );

		// Let's start on the login page
		$I->amOnPage( wp_login_url() );

		// Populate the login form's user id field
		$I->fillField('input#user_login', 'admin' );

		// Populate the login form's password field
		$I->fillField('input#user_pass', 'password' );

		// Submit the login form
		$I->click( '#wp-submit' );

	}

	/**
	 * Validate that we are in the wizard
	 *
	 * @param AcceptanceTester $I
	 */
	protected function validateWizard( AcceptanceTester $I ) {

		// Taken to the wizard
		$I->seeInCurrentUrl( '/wp-admin/?page=wpem' );

		// Validate the successful loading of the WPEM
		$I->see( 'WordPress Setup' );

		// Make sure we don't see the normal dashboard
		$I->cantSee( 'Dashboard' );

	}


	/**
	 * Codeception might be deactivated after certain user actions
	 */
	protected function reactivateCodeception() {

		activate_plugin( 'wp-codeception/wp-codeception.php' );

	}

	/**
	 * Helper to generate posts before logging into WordPress
	 */
	protected function generatePosts() {

		DEBUG::debug( 'Generating 5 posts ...' );

		WP_CLI::launch_self( 'post generate', [], [ 'count' => 5 ], false );

	}

	protected function stepStart( AcceptanceTester $I, $continue = true ) {

		$I->canSeeSetting( 'wpem_last_viewed', 'start' );

		Debug::debug( 'Clicking continue ...' );

		if ( ! $continue ) {

			$I->click( '#wpem_no_thanks' );

			return;

		}

		$I->click( self::PRIMARY_BUTTON );

		$I->waitForElementNotVisible( '.wpem-step-1 form', 15 );

	}

	protected function stepSettings( AcceptanceTester $I, $settings = [] ) {

		$I->seeInCurrentUrl( '/wp-admin/?page=wpem&step=settings' );

		$I->canSeeSetting( 'wpem_started', 1 );

		$I->canSeeSetting( 'wpem_last_viewed', 'settings' );

		$I->canSee( 'Settings' );

		Debug::debug( 'Filling out settings ...' );

		foreach ( $settings as $setting ) {

			switch ( $setting['type'] ) {

				case 'select':

					$I->selectOption( '#' . $setting['key'], $setting['value'] );

					break;

				case 'input':

					$I->fillField( '#' . $setting['key'], $setting['value'] );

					break;

			}

		}

		$I->click( self::PRIMARY_BUTTON );

		Debug::debug( 'Installing plugins ...' );

		$I->waitForElementNotVisible( '.wpem-step-2 form', 90 );

		foreach ( $settings as $setting ) {

			$I->canSeeSetting( $setting['key'], $setting['value'] );

		}

	}

	protected function stepTheme( AcceptanceTester $I, $theme ) {

		$I->seeInCurrentUrl( '/wp-admin/?page=wpem&step=theme' );

		$I->canSeeSetting( 'wpem_last_viewed', 'theme' );

		$I->canSee( 'Choose a Theme' );

		Debug::debug( 'Choosing a theme ...' );

		$I->waitForElementVisible( ".themes .theme.{$theme}", 15 );

		$I->click( ".themes .theme.{$theme} .button-primary" );

		Debug::debug( 'Installing theme ...' );

		$I->waitForElementNotVisible( '.wpem-step-3 form', 90 );

		$I->canSeeSetting( 'template', $theme );

	}

	protected function stepCustomizer( AcceptanceTester $I ) {

		Debug::debug( 'Viewing the theme Customizer ...' );

		$I->canSeeInCurrentUrl( 'wp-admin/customize.php' );

		$I->waitForElementVisible( '.wp-pointer', 5 );

		$I->canSee( 'Congratulations!' );

		$I->cantSeeElement( '.change-theme' );

		$I->click( '.wpem-pointer .button-primary' );

		$I->canSeeElement( '#wpem-overlay' );

		$I->click( '.wpem-overlay-control span' );

		$I->cantSeeElement( '#wpem-overlay' );

		$I->click( '.wpem-pointer .button-secondary' );

		$I->cantSeeElement( '.wp-pointer' );

		$I->wait( 1 );

		$I->reloadPage();

		$I->waitForElementNotVisible( '.wp-pointer', 5 );

		$I->cantSee( 'Congratulations!' );

		$I->cantSeeElement( '.change-theme' );

		$I->canSeeSetting( 'wpem_done', 1 );

		$I->canSeeSetting( 'wpem_log' );

	}

	/**
	 * Complete all steps - Blog
	 *
	 * @before reset
	 * @before login
	 * @before validateWizard
	 *
	 * @param AcceptanceTester $I
	 */
	public function canCompleteAllStepsBlog( AcceptanceTester $I ) {

		$I->wantTo( 'Setup a Blog using WP Easy Mode' );

		/**
		 * Step 1: Start
		 */
		$this->stepStart( $I );

		/**
		 * Step 2: Settings
		 */
		$this->stepSettings(
			$I,
			[
				[
					'type'  => 'select',
					'key'   => 'wpem_site_type',
					'value' => 'blog',
				],
				[
					'type'  => 'select',
					'key'   => 'wpem_site_industry',
					'value' => 'personal',
				],
				[
					'type'  => 'input',
					'key'   => 'blogname',
					'value' => 'My personal blog',
				],
				[
					'type'  => 'input',
					'key'   => 'blogdescription',
					'value' => 'My personal blog tagline',
				],
			]
		);

		/**
		 * Step 3: Theme
		 */
		$this->stepTheme( $I, 'hoffman' );

		/**
		 * Done
		 */
		Debug::debug( 'Validating forms & plugins ...' );

		$I->canSeePluginActive( 'ninja-forms/ninja-forms.php' );

		$I->canSeePageWithShortcode( 'contact-me', '[ninja_forms id=1]' );

		$this->stepCustomizer( $I );

	}

	/**
	 * Complete all steps - Standard
	 *
	 * @before reset
	 * @before login
	 * @before validateWizard
	 *
	 * @param AcceptanceTester $I
	 */
	public function canCompleteAllStepsStandard( AcceptanceTester $I ) {

		$I->wantTo( 'Setup a Standard Website using WP Easy Mode' );

		/**
		 * Step 1: Start
		 */
		$this->stepStart( $I );

		/**
		 * Step 2: Settings
		 */
		$this->stepSettings(
			$I,
			[
				[
					'type'  => 'select',
					'key'   => 'wpem_site_type',
					'value' => 'standard',
				],
				[
					'type'  => 'select',
					'key'   => 'wpem_site_industry',
					'value' => 'design',
				],
				[
					'type'  => 'input',
					'key'   => 'blogname',
					'value' => 'My portfolio website',
				],
				[
					'type'  => 'input',
					'key'   => 'blogdescription',
					'value' => 'My portfolio website tagline',
				],
			]
		);

		/**
		 * Step 3: Theme
		 */
		$this->stepTheme( $I, 'crawford' );

		/**
		 * Done
		 */
		Debug::debug( 'Validating forms & plugins ...' );

		$I->canSeePluginActive( 'ninja-forms/ninja-forms.php' );

		$I->canSeePageWithShortcode( 'contact-us', '[ninja_forms id=1]' );

		$I->canSeePageWithShortcode( 'estimates', '[ninja_forms id=5]' );

		$this->stepCustomizer( $I );

	}

	/**
	 * Complete all steps - Online Store
	 *
	 * @before reset
	 * @before login
	 * @before validateWizard
	 *
	 * @param AcceptanceTester $I
	 */
	public function canCompleteAllStepsOnlineStore( AcceptanceTester $I ) {

		$I->wantTo( 'Setup an Online Store using WP Easy Mode' );

		/**
		 * Step 1: Start
		 */
		$this->stepStart( $I );

		/**
		 * Step 2: Settings
		 */
		$this->stepSettings(
			$I,
			[
				[
					'type'  => 'select',
					'key'   => 'wpem_site_type',
					'value' => 'store',
				],
				[
					'type'  => 'select',
					'key'   => 'wpem_site_industry',
					'value' => 'entertainment',
				],
				[
					'type'  => 'input',
					'key'   => 'blogname',
					'value' => 'My ecommerce website',
				],
				[
					'type'  => 'input',
					'key'   => 'blogdescription',
					'value' => 'My ecommerce website tagline',
				],
			]
		);

		/**
		 * Step 3: Theme
		 */
		$this->stepTheme( $I, 'storefront' );

		/**
		 * Done
		 */
		Debug::debug( 'Validating forms & plugins ...' );

		$I->canSeePluginActive( 'ninja-forms/ninja-forms.php' );

		$I->canSeePluginActive( 'woocommerce/woocommerce.php' );

		$I->canSeePageWithShortcode( 'contact-us', '[ninja_forms id=1]' );

		$I->canSeePageWithShortcode( 'account', '[woocommerce_my_account]' );

		$I->canSeePageWithShortcode( 'cart', '[woocommerce_cart]' );

		$I->canSeePageWithShortcode( 'checkout', '[woocommerce_checkout]' );

		$I->canSeePageWithShortcode( 'store', '[products]' );

		/**
		 * WooCommerce Wizard
		 */
		$I->seeInCurrentUrl( '/wp-admin/index.php?page=wc-setup' );

		$I->canSee( 'Thank you for choosing WooCommerce' );

		$I->click( 'Not right now' );

		$I->waitForElementNotVisible( '.wc-setup', 15 );

		$I->seeInCurrentUrl( '/wp-admin/' );

		$I->see( 'Dashboard' );

		$I->see( 'Run the Setup Wizard' );

	}

	/**
	 * Test to make sure a user cannot go manipulate the form flow
	 *
	 * @before reset
	 * @before login
	 * @before validateWizard
	 *
	 * @param AcceptanceTester $I
	 */
	public function validateFormSequence( AcceptanceTester $I ) {

		$I->wantToTest( 'The user cannot manipulate the form sequence manually' );

		/**
		 * Step 1: Start
		 */
		DEBUG::debug( 'Trying to go forward 1 step without clicking continue ...' );

		$I->amOnPage( admin_url( '/?page=wpem&step=settings' ) );

		// Redirected back to the most current step
		$I->seeInCurrentUrl( '/wp-admin/?page=wpem&step=start' );

		$I->click( self::PRIMARY_BUTTON );

		$I->waitForElementNotVisible( '.wpem-step-1 form', 15 );

		/**
		 * Step 2: Settings
		 */
		$I->seeInCurrentUrl( '/wp-admin/?page=wpem&step=settings' );

		DEBUG::debug( 'Trying to go back to the previous step ...' );

		$I->moveBack();

		// Redirected back to the most current step
		$I->seeInCurrentUrl( '/wp-admin/?page=wpem&step=settings' );

		DEBUG::debug( "Typing in the previous step's URL directly ..." );

		$I->amOnPage( admin_url( '/?page=wpem&step=start' ) );

		// Redirected back to the most current step
		$I->seeInCurrentUrl( '/wp-admin/?page=wpem&step=settings' );

	}

	/**
	 * Test to make sure WPEM doesn't load if WP is not fresh (eg. Migrated manually)
	 *
	 * @before reset
	 * @before generatePosts
	 * @before login
	 *
	 * @param AcceptanceTester $I
	 */
	public function validateUnloadWPNotFresh( AcceptanceTester $I ) {

		$I->wantToTest( "The user doesn't see WPEM if WP isn't a fresh install" );

		$I->see( 'Dashboard' );

		$I->canSeeSetting( 'wpem_opt_out', 1 );

		$I->canSeeSetting( 'wpem_done', 1 );

		$I->canSeeSetting( 'wpem_log' );

	}

	/**
	 * Test that the can dismiss WP Easy Mode completely
	 *
	 * @before reset
	 * @before login
	 * @before validateWizard
	 *
	 * @after reactivateCodeception
	 * @after reset
	 *
	 * @param \AcceptanceTester $I
	 */
	public function canDismissPlugin( AcceptanceTester $I ) {

		$I->wantTo( 'Dismiss WP Easy Mode' );

		// This is a workaround to acceptPopup in headless mode
		$I->executeJS( 'window.confirm = function(){ return true; }' );

		$this->stepStart( $I, false );

		Debug::debug( 'Exiting the wizard ...' );

		$I->waitForElementNotVisible( '.wpem-step-1 form', 15 );

		// Redirected back to the WP Admin
		$I->seeInCurrentUrl( '/wp-admin/' );

		$I->see( 'Dashboard' );

		$I->canSeeSetting( 'wpem_opt_out', 1 );

		$I->canSeeSetting( 'wpem_done', 1 );

		$I->canSeeSetting( 'wpem_log' );

		$I->canSeePluginInactive( 'search-engine-visibility/sev.php' );

		$I->canSeePluginInactive( 'sidekick/sidekick.php' );

		$I->canSeePluginInactive( 'wp101-video-tutorial/wp101-video-tutorial.php' );

		$I->amOnPage( self_admin_url( 'customize.php' ) );

		$I->canSeeElement( '.change-theme' );

		$I->waitForElementNotVisible( '.wp-pointer', 5 );

		$I->cantSee( 'Congratulations!' );

	}

}
