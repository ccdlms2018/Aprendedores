<?php
add_action( 'psp_head', 'psp_fe_acf_assets' );
function psp_fe_acf_assets() {

	psp_register_style( 'psp-fe-project-front', PSP_FE_BASE_URI . 'assets/css/front.css', false );
	psp_register_style( 'psp-fe-project', PSP_FE_BASE_URI . 'assets/css/project.css', false );
	psp_register_script( 'psp-fe-front', PSP_FE_BASE_URI . 'assets/js/front.js', false );

	if( is_singular('psp_projects') && psp_can_edit_project(get_the_ID() ) ) {

		psp_register_script( 'tinymce', get_site_url() . '/wp-includes/js/tinymce/wp-tinymce.php?c=1', false );
		psp_register_style( 'tinymce', get_site_url() . '/wp-includes/js/tinymce/skins/wordpress/wp-content.css', false );
		psp_register_style( 'tinymce-lg', get_site_url() . '/wp-includes/js/tinymce/skins/lightgray/skin.min.css', false );

	}

}

add_action( 'wp_enqueue_scripts', 'psp_fe_enqueue_scripts', 9999 );
function psp_fe_enqueue_scripts() {

	if( is_post_type_archive( 'psp_projects' ) && get_query_var( 'psp_manage_page' ) ) {

		wp_enqueue_media();
        wp_enqueue_script( 'jquery-ui-core' );
        wp_enqueue_script( 'jquery-ui-slider' );
        wp_enqueue_style( 'jquery-ui-core' );
        wp_enqueue_style( 'jquery-ui-slider' );

		if( psp_get_option( 'psp_use_custom_template' ) ) {

			wp_register_style( 'psp-fe-project-front', PSP_FE_BASE_URI . 'assets/css/front.css' );
			wp_register_style( 'psp-fe-project', PSP_FE_BASE_URI . 'assets/css/project.css' );
			wp_register_script( 'psp-fe-front', PSP_FE_BASE_URI . 'assets/js/front.js', array('jquery') );

			wp_enqueue_style( 'psp-fe-project-front' );
			wp_enqueue_style( 'psp-fe-pront' );
			wp_enqueue_script( 'psp-fe-front' );

		}

	}

}

add_action( 'wp_enqueue_scripts', 'psp_fe_manage_psp_assets', 9999 );
function psp_fe_manage_psp_assets( $scripts ) {

	$psp_options = get_option('psp_settings');

	if( psp_get_option('psp_use_custom_template') ) return;

	if( get_query_var( 'psp_manage_page' ) ) {

		// Drop the extra jquery
		wp_dequeue_script( 'jquery' );

		// Strip out any theme styles
		global $wp_styles;

		foreach( $wp_styles->registered as $style ) {
			if( strpos( $style->src, 'wp-content/themes' ) ) wp_dequeue_style( $style->handle );
		}

	}

}

// add_filter( 'psp_global_scripts', 'psp_fe_unset_jquery' );
function psp_fe_unset_jquery( $scripts ) {

	if( psp_get_option('psp_custom_template') ) return;

	if( get_query_var( 'psp_manage_page' ) ) {

		$i = 0;

		foreach( $scripts as $script ) {
			if( $script == 'jquery.js' ) unset( $scripts[ $i ] );
		}

	}

	return $scripts;

}


add_action( 'wp_enqueue_scripts', 'psp_fe_process_enqueue_scripts', 9999 );
function psp_fe_process_enqueue_scripts() {

	if( !is_post_type_archive( 'psp_projects' ) && psp_get_option( 'psp_fe_dequeue' ) ) {

		$scripts = psp_get_option( 'psp_fe_dequeue' );
		$scripts = explode( ',', $scripts );

		if( is_array( $scripts ) ) {
			foreach( $scripts as $script ) wp_dequeue_style( trim( $script ) );
		} else {
			wp_dequeue_style( trim( $scripts ) );
		}

	}

}

add_action( 'psp_head', 'psp_fe_localize_strings' );
function psp_fe_localize_strings() {
	echo '<script>var psp_delete_confirmation_message = "' . __( 'Are you sure you want to delete this project?', 'psp_projects' ) . '";</script>';
}

add_action( 'wp_head', 'psp_fe_localize_strings_ct' );
function psp_fe_localize_strings_ct() {

	if( is_singular() && get_post_type() == 'psp_projects' ) psp_fe_localize_strings();

}

add_action( 'get_header', 'psp_fe_acf_custom_template' );
function psp_fe_acf_custom_template() {

	if( is_post_type_archive('psp_projects') && psp_get_option('psp_use_custom_template') && get_query_var('psp_manage_page')  ) {
		acf_form_head();
	}

}
