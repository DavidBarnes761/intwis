/*global jQuery, Backbone, _ */

var WPEM = WPEM || {};

WPEM.Overlay = ( function ( $, Backbone, _ ) {

	var self = {};

	self.View = Backbone.View.extend({

		initialize: function( options ) {

			this.content       = options.content;
			this.templateId    = options.templateId;
			this.keyDownEvent  = $.proxy( this.keyDownHandler, this );

			this.render();

			$( document ).on( 'keydown', this.keyDownEvent )
		},

		events: {

			'click .wpem-overlay-background' : 'remove',
			'click .wpem-overlay-control span' : 'remove',

		},

		template: function() {

			return _.template( $( this.templateId ).html(), { content: this.content } )

		},

		render: function() {

			this.$el.html( this.template() );

			$( this.el ).appendTo( 'body' );

		},

		keyDownHandler: function( e ) {

			if ( 27 === e.which ) {

				this.remove();

				$( document ).off( 'keydown', this.keyDownEvent );

			}

		}

	});


	return self;

} )( jQuery, Backbone, _ );


/**
 * Customizer page init
 */
jQuery( document ).ready( function( $ ) {

	$( '.wp-pointer.wpem-pointer .button-primary' ).on( 'click', function() {

		new WPEM.Overlay.View( {

			templateId: '#wpem-overlay-template'

		} );

	} );

} );
