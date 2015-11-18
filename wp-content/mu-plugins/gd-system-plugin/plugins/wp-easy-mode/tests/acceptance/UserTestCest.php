<?php
use Codeception\Util\Debug;

class UserTestCest {

	const PRIMARY_BUTTON = 'input.button-primary';

	protected $is_dirty = false;

	public function _before( AcceptanceTester $I ) {

		$this->maybeReset();

		$this->is_dirty = true;

		$this->login( $I );

	}

	public function _after( AcceptanceTester $I ) {

		$this->maybeReset();

	}

	protected function maybeReset() {

		if ( false === $this->is_dirty ) {

			return;

		}

		Debug::debug( 'Resetting WordPress ...' );

		WP_CLI::launch_self( 'easy-mode reset', [], [ 'yes' => true ], false );

		$this->is_dirty = false;

	}

	/**
	 * Codeception might be deactivated after certain user actions
	 */
	protected function reactivateCodeception() {

		activate_plugin( 'wp-codeception/wp-codeception.php' );

	}

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

		// Taken to the wizard
		$I->seeInCurrentUrl( '/wp-admin/?page=wpem' );

		// Validate the successful loading of the WP Easy Mode plugin
		$I->see( 'WordPress Setup' );

		// Make sure we don't see the normal dashboard
		$I->cantSee( 'Dashboard' );

	}

	/**
	 * Test that wp easy mode is the first thing they see after they log in
	 *
	 * @param AcceptanceTester $I
	 */
	public function canCompleteAllSteps( AcceptanceTester $I ) {

		$I->wantTo( 'Go through all the steps of WP Easy Mode' );

		/**
		 * Step 1: Start
		 */
		$I->canSeeSetting( 'wpem_last_viewed', 'start' );

		Debug::debug( 'Clicking continue ...' );

		$I->click( self::PRIMARY_BUTTON );

		$I->waitForElementNotVisible( '.wpem-step-1 form', 15 );

		/**
		 * Step 2: Settings
		 */
		$I->seeInCurrentUrl( '/wp-admin/?page=wpem&step=settings' );

		$I->canSeeSetting( 'wpem_started', 1 );

		$I->canSeeSetting( 'wpem_last_viewed', 'settings' );

		$I->canSee( 'Settings' );

		Debug::debug( 'Filling out settings ...' );

		$I->selectOption( 'Type', 'Standard (Recommended)' );

		$I->selectOption( 'Industry', 'Design / Art / Portfolio' );

		$I->fillField( 'Title', 'My portfolio website' );

		$I->fillField( 'Tagline', 'My portfolio website tagline' );

		$I->click( self::PRIMARY_BUTTON );

		Debug::debug( 'Installing plugins ...' );

		$I->waitForElementNotVisible( '.wpem-step-2 form', 60 );

		$I->canSeeSetting( 'wpem_site_type', 'standard' );

		$I->canSeeSetting( 'wpem_site_industry', 'design' );

		$I->canSeeSetting( 'blogname', 'My portfolio website' );

		$I->canSeeSetting( 'blogdescription', 'My portfolio website tagline' );

		/**
		 * Step 3: Theme
		 */
		$I->seeInCurrentUrl( '/wp-admin/?page=wpem&step=theme' );

		$I->canSeeSetting( 'wpem_last_viewed', 'theme' );

		$I->canSee( 'Choose a Theme' );

		Debug::debug( 'Choosing a theme ...' );

		$I->waitForElementVisible( '.themes .theme.crawford', 5 );

		$I->click( '.themes .theme.crawford .button-primary' );

		Debug::debug( 'Installing theme ...' );

		$I->waitForElementNotVisible( '.wpem-step-3 form', 60 );

		$I->canSeeSetting( 'current_theme', 'Crawford' );

		/**
		 * Done
		 */
		Debug::debug( 'Validating forms & plugins ...' );

		$I->canSeePluginActive( 'ninja-forms/ninja-forms.php' );

		$I->canSeePageWithForm( 'contact-us', '[ninja_forms id=1]' );

		Debug::debug( 'Viewing the theme Customizer ...' );

		$I->canSeeInCurrentUrl( 'wp-admin/customize.php' );

		$I->waitForElementVisible( '.wp-pointer', 5 );

		$I->canSee( 'Congratulations!' );

		$I->cantSeeElement( '.change-theme' );

		$I->click( '.wp-pointer .close' );

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
	 * Test to make sure a user cannot go manipulate the form flow
	 *
	 * @param AcceptanceTester $I
	 */
	public function validateFormSequence( AcceptanceTester $I ) {

		$I->wantTo( 'The user can not manipulate the form sequence manually' );

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
	 * Test that the can dismiss WP Easy Mode completely
	 *
	 * @after reactivateCodeception
	 * @param \AcceptanceTester $I
	 */
	public function canDismissPlugin( AcceptanceTester $I ) {

		$I->wantTo( 'Dismiss WP Easy Mode' );

		// This is a workaround to acceptPopup in headless mode
		$I->executeJS( 'window.confirm = function(){ return true; }' );

		$I->click( 'input#wpem_no_thanks' );

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
