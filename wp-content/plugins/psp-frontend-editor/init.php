<?php
define( 'PSP_FE_BASE_DIR', dirname( __FILE__ ) );
define( 'PSP_FE_BASE_URI', plugin_dir_url( __FILE__ ) );
define( 'PSP_FE_DB_VER', 1 );

do_action( 'psp-fe-before-init' );

$deps = array(
	'lib/psp-fe-controller',		// Controller
	'lib/psp-fe-assets',			// Load assets
	'lib/psp-fe-notifications',		// Custom notifications
	'lib/psp-views',				// Views
	'lib/psp-fe-settings'			// Custom settings
);

if( defined( 'PSP_VER' ) ) {
	foreach( $deps as $dep ) require_once( $dep . '.php' );
}

do_action( 'psp-fe-after-init' );

if( !class_exists( 'EDD_SL_Plugin_Updater' ) ) {
	// load our custom updater
	include( dirname( __FILE__ ) . '/EDD_SL_Plugin_Updater.php' );
}

if(! defined( 'PSP_ACF_VER' ) ) {

	if( function_exists( 'update_sub_field' ) ) {
		define( 'PSP_ACF_VER', 5 );
	} else {
		define( 'PSP_ACF_VER', 4 );
	}

}

if( !get_option( 'psp_fe_data_models' ) || get_option( 'psp_fe_data_models' ) < PSP_FE_VER ) {
	add_action( 'init', 'flush_rewrite_rules' );
	update_option( 'psp_data_models', PSP_FE_VER );
}
