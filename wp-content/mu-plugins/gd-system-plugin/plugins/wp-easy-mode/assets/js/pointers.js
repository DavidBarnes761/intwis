/* globals ajaxurl, wpem_pointers, wpPointerL10n */

jQuery( document ).ready( function( $ ) {

	$.each( wpem_pointers, function( i, pointer ) {

		render( pointer );

	} );

	function render( pointer ) {

		var options = $.extend( pointer.options, {

			pointerClass: 'wp-pointer wpem-pointer',

			close: function() {
				$.post( ajaxurl, {
					pointer: pointer.id,
					action: 'dismiss-wp-pointer'
				} );
			},

			buttons: function( event, t ) {
				var close   = ( wpPointerL10n ) ? wpPointerL10n.dismiss : 'Dismiss',
						buttons = $(
								'<div class="buttons-wrapper">' +
									'<button class="button-secondary">' + close + '</button>' +
									'<button class="button-primary">' + pointer.btn_primary + '</button>' +
								'</div>'
						);

				return buttons.bind( 'click.pointer', function( e ) {
					e.preventDefault();

					if ( 'button-secondary' === e.target.className ) {
						t.element.pointer( 'close' );
					}
				} );
			}

		} );

		$( pointer.target ).pointer( options ).pointer( 'open' );

	}

	$( '.wp-pointer' ).css( { 'z-index': 999999 } );

} );
