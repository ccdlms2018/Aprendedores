<?php
/**
 * Description of psp-pro-init
 *
 * Load all the dependencies for paid version of Panorama
 * @package psp-projects
 *
 *
 */

$profesional_library = array(
	'psp-pro-shortcodes',		// Shortcodes specific to PRO
	'psp-notifications',		// Notification management
	'psp-documents',			// Document management
	'psp-ajax',					// Front end modifications / updating
	'psp-teams'					// Populate team list
);

foreach( $profesional_library as $book ) {

	include_once( $book . '.php' );

}

// ACF doesn't support hidden fields by default
if ( ! class_exists( 'acf_field_hidden' ) ) {
	
	require_once __DIR__ . '/fields/acf-hidden/acf-hidden.php';
	
}

/**
 * psp_load_meta_fields
 *
 * Load custom meta fields via a hook so they can be adjusted later.
 * Uses psp_load_field_template() so users can override fields in their theme directory
 *
 */

add_action( 'init' , 'psp_load_meta_fields' , 999 );
function psp_load_meta_fields() {

	// Make sure ACF is running in some capacity
	if(function_exists("register_field_group")) {

		// Load the fields via psp_load_field_template so users can import their own

		psp_load_field_template( 'overview' );
		psp_load_field_template( 'milestones' );
		psp_load_field_template( 'phases' );
		psp_load_field_template( 'teams' );

	}

}
