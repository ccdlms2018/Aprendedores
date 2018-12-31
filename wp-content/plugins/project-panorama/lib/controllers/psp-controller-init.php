<?php
/**
 * psp-controller-init.php
 *
 * Master controller file, builds all the controllers.
 */

$library = apply_filters( 'psp_controller_files', array(
    'psp-discussions',					// Comment management
    'psp-permissions',					// User and permission management
    'psp-documents',					// Document management
    'psp-timing',					    // Timing management
    'psp-progress',                     // Progress management
    'psp-phases',                       // Phase controller
    'psp-tasks',
    'psp-notifications',
    'psp-notification-email',
    'psp-activity'
) );

do_action( 'psp_before_controllers_loaded' );

foreach( $library as $lib ) {

    require_once( $lib . '.php' );

}

do_action( 'psp_after_controllers_loaded' );
