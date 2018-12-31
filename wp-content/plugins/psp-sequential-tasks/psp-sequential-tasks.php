<?php
/**
 * Plugin Name: Project Panorama Sequential Tasks
 * Plugin URI: http://www.projectpanorama.com
 * Description: Require the previous task be complete before the next
 * Version: 1.1.5
 * Author: SnapOrbital
 * Author URI: http://www.projectpanorama.com
 * License: GPL2
 * Text Domain: psp_projects
 */

do_action( 'pspsq_before_init' );

$defintions = array(
    'PSP_SQ_VER'  =>  '1.1.5',
    'PSP_SQ_PATH' =>  plugin_dir_path( __FILE__ ),
    'PSP_SQ_URL'  =>  plugin_dir_url( __FILE__ )
);

foreach( $defintions as $definition => $value ) {
    if( !defined($definition) ) define( $definition, $value );
}

include_once( 'lib/init.php' );

do_action( 'pspsq_after_init' );

/**
 * Software Licensing
 * @var [code]
 */

 // License definitions
 define( 'PSP_SQ_STORE_URL', 'https://www.projectpanorama.com' );

 define( 'PSP_SQ_ITEM_ID', 24824 );

if( !class_exists( 'PSP_SQ_SL_Plugin_Updater' ) ) {
	// load our custom updater if it doesn't already exist
	include( dirname( __FILE__ ) . '/lib/vendor/EDD_SL_Plugin_Updater.php' );
}

psp_sq_run_updater_api();
function psp_sq_run_updater_api() {

    $psp_settings = get_option('psp_settings');

    if( !isset($psp_settings['psp_sq_license']) ) return;

    // retrieve our license key from the DB
    $license_key = trim( $psp_settings['psp_sq_license'] );

    // setup the updater
    $edd_updater = new PSP_SQ_SL_Plugin_Updater( PSP_SQ_STORE_URL, __FILE__, array(
    	'version' 	=> PSP_SQ_VER,		// current version number
    	'license' 	=> $license_key,	// license key (used get_option above to retrieve from DB)
    	'item_id'   => PSP_SQ_ITEM_ID,	// id of this plugin
    	'author' 	=> 'SnapOrbital',	// author of this plugin
    	'url'       => home_url(),
            'beta'          => false // set to true if you wish customers to receive update notifications of beta releases
    ) );

}
