<?php
/*
Plugin Name: Project Panorama Front End Editor
Plugin URI: https://www.projectpanorama.com
Description: Create and edit projects from the front end
Version: 1.3.2
Author: SnapOrbital
Author URI: http://www.snaporbital.com
Text Domain: psp-front-edit
Domain Path: /languages
*/

add_action( 'plugins_loaded', 'psp_fe_init', 99999 );
function psp_fe_init() {

    do_action( 'psp_fe_before_init' );

    if( function_exists('psp_get_option') ) {
        require_once( 'init.php' );
    } else {
        add_action( 'admin_notices', 'psp_fe_needs_panorama' );
    }

    do_action( 'psp_fe_after_init' );

}

function psp_fe_needs_panorama() { ?>

    <div class="notice notice-error is-dismissible">
        <p><?php esc_html_e( 'Project Panorama Frontend Editor requires Project Panorama 1.4.2 or higher to run', 'psp_projects' ); ?></p>
    </div>

    <?php
}

 add_action( 'plugins_loaded', 'psp_fe_localize_init' );
 function psp_fe_localize_init() {
     load_plugin_textdomain( 'psp-front-edit', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
 }

$constants = array(
    'PSP_FE_URL'        =>  plugin_dir_url( __FILE__ ),
    'PSP_FE_STORE_URL'  =>  'https://www.projectpanorama.com',
    'PSP_FE_PATH'       =>  plugin_dir_path( __FILE__ ),
    'PSP_FE_ITEM_NAME'  =>  'Front End Editor',
    'PSP_FE_VER'        =>  '1.3.2',
    'PSP_FE_PERMALINKS' =>  ( get_option('permalink_structure') ? TRUE : FALSE ),
);

foreach( $constants as $constant => $val ) {
    if( !defined( $constant ) ) define( $constant, $val );
}

if( !class_exists( 'EDD_SL_Plugin_Updater' ) ) {
	// load our custom updater
	include( dirname( __FILE__ ) . '/EDD_SL_Plugin_Updater.php' );

}

register_activation_hook( __FILE__, 'psp_fe_activation_hook' );
function psp_fe_activation_hook() {

    add_action( 'psp_fe_after_init', 'psp_fe_create_page_custom_rewrite_rule', 99999 );
    add_action( 'psp_fe_after_init', 'flush_rewrite_rules', 99999 );

}

add_action( 'admin_init', 'psp_fe_project_panorama_plugin_updater' );
function psp_fe_project_panorama_plugin_updater() {

    $settings = get_option('psp_settings');

    if( !isset( $settings['psp_fe_license'] ) ) {
        return;
    }

    // retrieve our license key from the DB
    $license_key = trim( $settings['psp_fe_license'] );

	// setup the updater
	$edd_updater = new EDD_SL_Plugin_Updater( PSP_FE_STORE_URL, __FILE__, array(
			'version' 	=> PSP_FE_VER, 				// current version number
			'license' 	=> $license_key, 		// license key (used get_option above to retrieve from DB)
			'item_name' => PSP_FE_ITEM_NAME, 	// name of this plugin
			'author' 	=> 'SnapOrbital',  // author of this plugin
			'url'       => home_url()
		)
	);

}
