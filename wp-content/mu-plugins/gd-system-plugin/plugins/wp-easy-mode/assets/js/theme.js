/* globals wpem_theme_vars */

jQuery( document ).ready( function( $ ) {

	$( document ).on( 'hover', '.theme', function() {

		$( '.theme' ).removeClass( 'hover' );

	} );

	$( document ).on( 'keyup', function( e ) {

		// Tab key
		if ( 9 === e.keyCode ) {

			$( '.theme' ).removeClass( 'hover' );

			$( '.select-theme:focus' ).closest( '.theme' ).addClass( 'hover' );

		}

	} );

	$.each( wpem_theme_vars.themes, function( index, theme ) {

		fetchTheme( index, theme );

	} );

	function fetchTheme( index, theme ) {

		$.ajax( {
			type: 'GET',
			url: '//api.wordpress.org/themes/info/1.1/',
			dataType: 'jsonp', // Required for IE8/9
			data: {
				action: 'theme_information',
				request: {
					'slug': theme
				}
			},
			success: function( response ) {

				wpem_theme_vars.themes[ index ] = response;

				renderTheme( response, index );

			}
		} );

	}

	function renderTheme( data, index ) {

		var $template  = $( '#wpem-template-theme' ),
		    $container = $( '.theme-browser .themes' );

		var slug           = ( 'undefined' !== typeof data.slug ) ? data.slug : '',
		    name           = ( 'undefined' !== typeof data.name ) ? data.name : '',
		    author         = ( 'undefined' !== typeof data.author ) ? data.author : '',
		    screenshot_url = ( 'undefined' !== typeof data.screenshot_url ) ? data.screenshot_url : '';

		$clone = $( $.trim( $template.clone().html() ) );

		$( $clone ).appendTo( $container );

		$clone.addClass( slug );

		$clone.attr( 'data-theme', slug );

		if ( screenshot_url ) {

			$clone.find( '.theme-screenshot img' ).attr( 'src', screenshot_url );

		} else {

			$clone.find( '.theme-screenshot' ).addClass( 'blank' );

			$clone.find( '.theme-screenshot img' ).remove();

		}

		$clone.find( '.theme-author span' ).text( author );

		$clone.find( '.theme-name' ).text( name );

		$clone.on( 'click', '.theme-actions a', function( e ) {

			e.preventDefault();

			var $theme = $( this ).closest( '.theme' );

			selectTheme( $theme );

			return false;

		} );

		$clone.on( 'click', function() {

			renderThemePreview( data, index );

		} );

	}

	function selectTheme( $theme ) {

		$( '#wpem_selected_theme' ).val( $theme.data( 'theme' ) );

		$theme.removeClass( 'hover' ).addClass( 'active' );

		$( '.wpem-screen form' ).submit();

	}

	function renderThemePreview( data, index ) {

		var $template  = $( '#wpem-template-theme-preview' ),
		    $container = $( '.wrap' ),
		    $input     = $( '#wpem_selected_theme' );

		var slug           = ( 'undefined' !== typeof data.slug ) ? data.slug : '',
		    name           = ( 'undefined' !== typeof data.name ) ? data.name : '',
		    author         = ( 'undefined' !== typeof data.author ) ? data.author : '',
		    version        = ( 'undefined' !== typeof data.version ) ? data.version : '',
		    preview_url    = ( 'undefined' !== typeof data.preview_url ) ? data.preview_url : '',
		    screenshot_url = ( 'undefined' !== typeof data.screenshot_url ) ? data.screenshot_url : '',
		    description    = ( 'undefined' !== typeof data.sections.description ) ? data.sections.description : '';

		$clone = $( $.trim( $template.clone().html() ) );

		$( $clone ).appendTo( $container ).show();

		$clone.find( '.theme-name' ).text( name );

		$clone.find( '.theme-by span' ).text( author );

		$clone.find( '.theme-screenshot' ).attr( 'src', screenshot_url );

		$clone.find( '.theme-version span' ).text( version );

		$clone.find( '.theme-description' ).text( description );

		var $main = $clone.find( '.wp-full-overlay-main' );

		$main.append( '<iframe>' );

		$main.find( 'iframe' ).attr( 'src', preview_url );

		if ( 0 === index ) {

			$( 'a.previous-theme' ).addClass( 'disabled' );

		} else {

			$clone.on( 'click', 'a.previous-theme', function( e ) {

				e.preventDefault();

				var theme_data = wpem_theme_vars.themes[ index - 1 ];

				$clone.remove();

				renderThemePreview( theme_data, index - 1 );

			} );

		}

		if ( wpem_theme_vars.themes.length === ( index + 1 ) ) {

			$( 'a.next-theme' ).addClass( 'disabled' );

		} else {

			$clone.on( 'click', 'a.next-theme', function( e ) {

				e.preventDefault();

				$clone.remove();

				var theme_data = wpem_theme_vars.themes[ index + 1 ];

				renderThemePreview( theme_data, index + 1 );

			} );

		}

		$clone.on( 'click', 'a.theme-install', function( e ) {

			e.preventDefault();

			var $theme = $( '.theme.' + slug );

			$clone.find( '.close-full-overlay' ).click();

			selectTheme( $theme );

		} );

		$( document ).on( 'keyup', function( e ) {

			// Esc key
			if ( 27 === e.keyCode ) {

				$clone.find( '.close-full-overlay' ).click();

			}

		} );

		$clone.on( 'click', '.close-full-overlay', function( e ) {

			e.preventDefault();

			$clone.remove();

		} );

		$( 'button.collapse-sidebar' ).on( 'click', function() {

			if ( 'true' === $( this ).attr( 'aria-expanded' ) ) {

				$( this ).attr( { 'aria-expanded': 'false', 'aria-label': wpem_theme_vars.i18n.expand } );

			} else {

				$( this ).attr( { 'aria-expanded': 'true', 'aria-label': wpem_theme_vars.i18n.collapse } );

			}

			$( this ).toggleClass( 'collapsed' ).toggleClass( 'expanded' );

			$( '.wp-full-overlay' ).toggleClass( 'collapsed' ).toggleClass( 'expanded' );

			return false;

		} );

	}

} );
