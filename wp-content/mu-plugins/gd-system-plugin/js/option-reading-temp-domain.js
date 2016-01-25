/* global jQuery, gd_system_option_reading_temp_domain_vars */

jQuery( document ).ready( function( $ ) {

	$( '#blog_public').prop( 'disabled', true );

	var $description = $( '<p class="description" />' );

	$description.text( gd_system_option_reading_temp_domain_vars.description_blog_public );

	$( '.option-site-visibility p.description' ).after( $description );

} );
