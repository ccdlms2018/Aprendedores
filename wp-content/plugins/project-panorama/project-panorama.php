<?php
/**
 * Plugin Name: Project Panorama
 * Plugin URI: http://www.projectpanorama.com
 * Description: Give your team and your clients a 360 degree view of your projects
 * Version: 1.6.2
 * Author: SnapOrbital
 * Author URI: http://www.projectpanorama.com
 * License: GPL2
 * Text Domain: psp_projects
 * Domain Path: /languages/
 */

/**
 * Initialize the plugin by loading the initial files
 *
 *
 * @param NULL
 * @return NULL
 */
$library = array(
	'lib/psp-init.php', 		// Primary initilization file
	'lib/psp-welcome.php',		// Welcome screen
	'psp-settings.php'  		// License management file
);

foreach( $library as $book ) include_once( $book );

if( !class_exists( 'EDD_SL_Plugin_Updater' ) ) {
	// load our custom updater
	include( dirname( __FILE__ ) . '/EDD_SL_Plugin_Updater.php' );
}


/**
 * Important definitions used throughout the application
 *
 */
$constants = array(
	'PROJECT_PANARAMA_URI'			=>	plugins_url( '', __FILE__ ),
	'PROJECT_PANORAMA_DIR'			=>	__DIR__,
	'PROJECT_PANORAMA_STORE_URL'	=> 'https://www.projectpanorama.com',
	'EDD_PROJECT_PANORAMA'			=> 'Project Panorama Premium',
	'PSP_VER'						=>	'1.6.2',
	'PSP_ACF_VER'					=>	( function_exists( 'update_sub_field' ) ? 5 : 4 ),
	'PSP_PLUGIN_LICENSE_PAGE'		=>	'panorama-license',
	'PSP_DB_VER'					=>	8
);

foreach( $constants as $constant => $val ) {
	if( !defined( $constant ) ) define( $constant, $val );
}

/***
  *
  * Initalization and activiation hooks
  *
  */
$plugin = plugin_basename( __FILE__ );

add_filter( "plugin_action_links_$plugin", "add_license_link" );
add_action( 'after_plugin_row_project-panorama/project-panorama.php', 'add_license_after_row' );

register_activation_hook( __FILE__, 'psp_activation_hook' );
function psp_activation_hook() {
	add_action( 'psp_loaded_post_type_project', 'flush_rewrite_rules' );
	psp_welcome_screen_activate();
}

/***
  *
  * Localization for translations
  *
  */
add_action( 'plugins_loaded', 'psp_localize_init', 901 );
function psp_localize_init() {
    load_plugin_textdomain( 'psp_projects', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}

/**
  *
  * Licensing and update script
  *
  * @return NULL
  *
  */
add_action( 'admin_init', 'edd_project_panorama_plugin_updater' );
function edd_project_panorama_plugin_updater() {

	// retrieve our license key from the DB
	$license_key = trim( psp_get_option( 'edd_panorama_license_key' ) );

	// setup the updater
	$edd_updater = new EDD_SL_Plugin_Updater( PROJECT_PANORAMA_STORE_URL, __FILE__, array(
			'version' 	=> PSP_VER, 				// current version number
			'license' 	=> $license_key, 		// license key (used get_option above to retrieve from DB)
			'item_name' => EDD_PROJECT_PANORAMA, 	// name of this plugin
			'author' 	=> 'SnapOrbital',  // author of this plugin
			'url'       => home_url()
		)
	);

}


function add_license_link( $links ) {

	$license_key 	= trim( psp_get_option('edd_panorama_license_key') );
	$label 			= ( !$license_key ? __( 'Register License', 'psp_projects' ) : __( 'Settings', 'psp_projects' ) );
	$settings_link 	= '<a href="' . site_url() . '/wp-admin/options-general.php?page=panorama-license">' . $label . '</a>';

	array_unshift( $links, $settings_link );

	return $links;

}

/**
  * Add a row after the Panorama plugin row, reminding users to activate their license
  *
  *
  * @return NULL
  *
  **/

function add_license_after_row() {

	$license_key = trim( psp_get_option( 'edd_panorama_license_key' ) );
	if( !$license_key ) {
		echo '</tr><tr class="plugin-update-tr"><td colspan="3"><div class="update-message"><a href="' . site_url() . '/wp-admin/options-general.php?page=panorama-license">' . __( 'Activate your license', 'psp_projects' ) . '</a> ' . __( 'for automatic upgrades. Need a license?', 'psp_projects' ) . ' <a href="http://www.projectpanorama.com" target="_new">' . __( 'Purchase one', 'psp_projects' ) . '</a></div></td>';
	}

}

/**
 * Deactivation Clean-up
 */

 register_deactivation_hook(__FILE__, 'psp_plugin_deactivation');
 function psp_plugin_deactivation() {
 	wp_clear_scheduled_hook('psp_send_cron_notifications');
 }
