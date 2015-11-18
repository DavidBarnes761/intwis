/* globals wpem_vars */

jQuery( document ).ready( function( $ ) {

	var start = new Date().getTime() / 1000;

	$( '.wpem-screen form' ).on( 'click', '#wpem_no_thanks', function( e ) {

		e.preventDefault();

		if ( window.confirm( wpem_vars.i18n.exit_confirm ) ) {

			$( '#wpem_continue' ).val( 'no' );

			$( '.wpem-screen form' ).submit();

		}

	} );

	var validated = false;

	$( '.wpem-screen' ).on( 'submit', 'form', function( e ) {

		// Submit now if validated
		if ( validated ) {

			return true;

		}

		var $form = $( this );

		if ( ! $form[0].checkValidity() ) {

			return false;

		}

		e.preventDefault();

		var now = new Date().getTime() / 1000;

		$( '#wpem_step_took' ).val( parseFloat( now - start ).toFixed( 3 ) );

		$form.find( 'input, select' ).blur();

		$form.find( 'input[type=submit]' ).prop( 'disabled', true ).addClass( 'disabled' );

		$( '#wpbody-content' ).block( {
			message: '&nbsp;',
			overlayCSS: {
				backgroundColor: '#fff',
				opacity: '0.8'
			}
		} );

		// Workaround for Safari not repainting the DOM on submit
		setTimeout( function() {

			validated = true;

			$form.submit();

		}, 250 );

	} );

} );

window.onload = function() {

	var $refresh = jQuery( '#wpem_force_refresh' );

	( 'true' === $refresh.val() ) ? window.location.reload( true ) : $refresh.val( 'true' );

};
