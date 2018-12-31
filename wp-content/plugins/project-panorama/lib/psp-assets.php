<?php
/**
 * psp-assets.php
 * Register and enqueue styles and scripts for Project Panorama
 *
 * @author Ross Johnson
 * @copyright 3.7 MEDIA
 * @license GNU GPL version 3 (or later) {@see license.txt}
 * @package panorama
 **/

function psp_custom_template_assets() {

    $post_types = array(
        'psp_projects',
        'psp_teams'
    );


    if( in_array( get_post_type(), $post_types ) && psp_get_option('psp_use_custom_template') ) psp_front_assets(true);

}
add_action( 'wp_enqueue_scripts', 'psp_custom_template_assets' );

function psp_enqueue_calendar_assets() {

    wp_register_script( 'psp-admin-lib' , plugins_url() . '/' . PSP_PLUGIN_DIR . '/dist/assets/js/psp-admin-lib.min.js' , array( 'jquery' ) , PSP_VER , false );
    wp_enqueue_script( 'psp-admin-lib' );
    wp_enqueue_script( 'psp-frontend' );
    wp_enqueue_script( 'psp-custom' );

}

// Frontend Style and Behavior
// add_action( 'wp_enqueue_scripts', 'psp_front_assets');
function psp_front_assets( $add_psp_scripts = null ) {

    if( ( get_post_type() == 'psp_projects' && psp_get_option('psp_use_custom_template') ) || ( $add_psp_scripts == 1 ) ) {

        // Frontend styling

        wp_register_style( 'psp-frontend', plugins_url() . '/' . PSP_PLUGIN_DIR . '/dist/assets/css/psp-frontend.css', false, PSP_VER );
        wp_register_style( 'psp-custom', plugins_url() . '/' . PSP_PLUGIN_DIR . '/dist/assets/css/psp-custom.css.php', false, PSP_VER );
        wp_register_style( 'lato', '//fonts.googleapis.com/css?family=Lato' );

		wp_register_script( 'psp-admin-lib' , plugins_url() . '/' . PSP_PLUGIN_DIR . '/dist/assets/js/psp-admin-lib.min.js' , array( 'jquery' ) , PSP_VER , false );

        wp_enqueue_style( 'psp-frontend' );
        wp_enqueue_style( 'psp-custom' );
        wp_enqueue_style( 'lato' );

        // Frontend Scripts
        wp_register_script( 'psp-frontend-library', plugins_url() . '/' . PSP_PLUGIN_DIR . '/dist/assets/js/psp-frontend-lib.min.js', array( 'jquery' ), PSP_VER, false );
        wp_register_script( 'psp-frontend-behavior', plugins_url() . '/' . PSP_PLUGIN_DIR . '/dist/assets/js/psp-frontend-behavior.js', array( 'jquery' ), PSP_VER, false );

        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'psp-frontend-library' );
        wp_enqueue_script( 'psp-admin-lib' );
        wp_enqueue_script( 'psp-frontend-behavior' );

    }

}

// Admin Style and Behavior

add_action( 'admin_enqueue_scripts', 'psp_admin_assets' );
function psp_admin_assets( $hook ) {

	global $post_type;
    $screen = get_current_screen();

    // Admin Styling

    wp_register_style( 'psp-admin' , plugins_url() . '/' . PSP_PLUGIN_DIR . '/dist/assets/css/psp-admin.css', false, PSP_VER );
    wp_register_style( 'jquery-ui-psp' , plugins_url() . '/' . PSP_PLUGIN_DIR . '/dist/assets/css/jquery-ui-custom.css');

    wp_enqueue_media();
    wp_enqueue_style('psp-admin');
    wp_enqueue_style('wp-color-picker');

	// Determine if we need wp-color-picker or not

	if( $hook == 'settings_page_panorama-license') {
		wp_register_script( 'pspadmin' , plugins_url() . '/' . PSP_PLUGIN_DIR . '/dist/assets/js/psp-admin-behavior.js' , array( 'jquery' , 'wp-color-picker' ) , PSP_VER , true );
	} else {
		wp_register_script( 'pspadmin' , plugins_url() . '/' . PSP_PLUGIN_DIR . '/dist/assets/js/psp-admin-behavior.js' , array( 'jquery' ) , PSP_VER , true );
	}

	// Standard Needs
	wp_register_script( 'psp-admin-lib' , plugins_url() . '/' . PSP_PLUGIN_DIR . '/dist/assets/js/psp-admin-lib.min.js' , array( 'jquery' ) , PSP_VER , false );
	
	// PSP determines whether we load this or not. Keeping as a separate file just simplifies things for now, but localizing the value into JS may be better for lowering requests
	wp_register_script( 'psp-wysiwyg' , plugins_url() . '/' . PSP_PLUGIN_DIR . '/dist/assets/js/psp-wysiwyg.js' , array( 'jquery' ) , PSP_VER , false );


	// If this is the dashboard load dependencies
    if( $screen->id == 'dashboard' || $screen->id == 'psp_projects_page_panorama-calendar' ) {

        $assets = array(
            'scripts'   =>  array(
                'psp-frontend-library',
                'psp-admin-lib',
            ),
            'styles'    =>  array(
                'psp-frontend',
            )
        );

        foreach( $assets['scripts'] as $script ) wp_enqueue_script($script);
        foreach( $assets['styles'] as $style ) wp_enqueue_style($style);

    }

 	// If this is a Panorama project load dependencies
	if( $post_type == 'psp_projects' ) {
	    wp_enqueue_script( 'jquery-ui-datepicker' );
	    wp_enqueue_script( 'jquery-ui-slider' );
        wp_enqueue_style( 'jquery-ui-psp' );
	}

	// If this is a project page or settings page load the admin scripts
 	if( ( $post_type == 'psp_projects' ) || ( $hook == 'settings_page_panorama-license' ) ) {
	    wp_enqueue_script( 'pspadmin' );
	}
	
	if ( $hook == 'settings_page_panorama-license' ) {
		wp_enqueue_script( 'psp-admin-lib' );
	}

	// If the shortcode helpers are not disabled load the WYSIWYG buttons
	if((psp_get_option('psp_disable_js') === '0') || (psp_get_option('psp_disable_js') == NULL)) {
		wp_enqueue_script( 'psp-wysiwyg' );
	}

}

// Enqeue All
function psp_add_script( $script ) {
	echo '<script src="' . plugins_url() . '/' . PSP_PLUGIN_DIR . '/dist/assets/js/' . $script . '?ver=' . PSP_VER . '"></script> ';
}

function psp_add_style( $style ) {
	echo '<link rel="stylesheet" type="text/css" href="' . plugins_url() . '/' . PSP_PLUGIN_DIR . '/dist/assets/css/' .$style .'?ver=' . PSP_VER .'"> ';
}

add_action( 'psp_enqueue_scripts' , 'psp_add_assets_to_templates');
function psp_add_assets_to_templates() {

	$global_scripts = apply_filters( 'psp_global_scripts', array(
		'jquery.js', // Ensures it is easily available to other plugins without crazy scope problems
		'psp-frontend-lib.min.js',
		'psp-frontend-behavior.js'
	) );

	$pdf_scripts = apply_filters( 'psp_pdf_scripts', array(
		'jspdf.min.js',
		'vendor/html2canvas.js',
		'vendor/html2canvas.svg.js'
	) );

	$global_styles = apply_filters( 'psp_global_styles', array(
		'psp-frontend.css',
		'psp-custom.css.php',
	) );

    $psp_settings = get_option('psp_settings');

    if( isset($psp_settings['psp_use_rtl']) && $psp_settings['psp_use_rtl'] ) {
        $global_styles[] = 'psp-rtl.css';
    }

	$pdf_styles = apply_filters( 'psp_pdf_styles', array(
		'psp-print.css'
	) );

	/* If this is a PDF view, load the necissary assets */

	if( isset( $_GET['pdfview'] ) ) {

		add_action( 'psp_body_classes', 'psp_add_pdf_view_body_class' );

		$global_scripts   = array_merge( $global_scripts, $pdf_scripts);
		$global_styles    = array_merge( $global_styles, $pdf_styles );

	}

	/* If this is the dashboard page, load the necissary assets */

	if( is_archive() ) {

		$global_styles[] .= 'psp-calendar.css';

		$global_scripts[] .= 'psp-admin-lib.min.js';

	}

    $global_scripts = apply_filters( 'psp_global_scripts', $global_scripts );
    $global_styles  = apply_filters( 'psp_global_styles', $global_styles );

	foreach( $global_scripts as $script ) {
		psp_add_script( $script );
	}

	foreach( $global_styles as $style ) {
		psp_add_style( $style );
	}
	
	psp_localize_js(
		'projectPanorama',
		array(
			'psp_slug' => psp_get_option( 'psp_slug' , 'panorama' ),
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
		)
	);

}

add_filter( 'psp_body_classes' , 'psp_add_pdf_view_body_class' );
function psp_add_pdf_view_body_class( $classes ) {

	if( isset ( $_GET['pdfview'] ) ) {
		$classes .= 'psp-pdf-view ';
	}

	return $classes;

}

add_filter( 'psp_project_wrapper_classes' , 'psp_add_pdf_view_single_row_class' );
function psp_add_pdf_view_single_row_class( $classes ) {

	if( isset ( $_GET['pdfview'] ) ) {
		$classes .= 'psp-width-single ';
	}

	return $classes;

}

add_action( 'psp_js_variables', 'psp_js_translation_strings' );
function psp_js_translation_strings() {

    echo 'var psp_js_label_more = "' . __( 'more', 'psp_projects' ) . '"';

}

add_action( 'admin_footer', 'psp_hide_add_button_from_owners' );
function psp_hide_add_button_from_owners() {

    $screen = get_current_screen();

    if( $screen->parent_file != 'edit.php?post_type=psp_projects' ) return;

    $user = wp_get_current_user();
    if ( in_array( 'psp_project_owner', (array) $user->roles ) ) : ?>

        <style type="text/css">
            .page-title-action {
                display: none;
            }
        </style>

    <?php endif;

}

add_action( 'psp_head', 'psp_add_typeface_style' );
function psp_add_typeface_style() {
    psp_register_style( 'lato', 'https://fonts.googleapis.com/css?family=Lato:400,400i,700,700i' );
}
