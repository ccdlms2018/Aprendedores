<?php
/* Adds classes to the body tag of Panorama templates, this will become more robust over time */
function psp_body_classes() {

	global $post;

	$classes		= array();
	$classes[] 		= 'psp-standalone-page';
	$classes[]		= 'psp-acf-ver-' . PSP_ACF_VER;
	$all_classes	= '';

	if( is_archive() ) {
		$classes[] = 'psp-dashboard-page';
	}

	if( is_single() ) {
		$classes[] = 'psp-single psp-single-' . $post->ID . ' psp-single-' . get_post_type();
	}

	if( is_admin_bar_showing() ) {
		$classes[] = 'admin-bar';
	}

	if( get_field( 'expand_tasks_by_default' ) ) {
		$classes[] = 'psp-task-expanded';
	}

	if( get_page_template_slug($post->ID) ) {
		$classes[] = get_page_template_slug($post->ID);
	}


	$classes = apply_filters( 'psp_body_classes_array', $classes );

	foreach( $classes as $class ) {
		$all_classes .= $class . ' ';
	}

	return apply_filters( 'psp_body_classes' , $all_classes );

}

add_filter( 'body_class', 'psp_custom_template_body_class' );
function psp_custom_template_body_class( $classes ) {

	if( get_post_type() == 'psp_projects' ) {

		global $post;

		if( get_field( 'expand_tasks_by_default', $post->ID ) ) {
			$classes[] = 'psp-task-expanded';
		}

	}

	return $classes;

}

function psp_the_body_classes() {
	echo esc_attr( psp_body_classes() );
}

function psp_project_wrapper_classes() {

	$classes[]		= '';
	$all_classes	= ''; // Eventually this will be an array, when needed

	return apply_filters( 'psp_project_wrapper_classes' , $all_classes );

}

function psp_the_project_wrapper_classes() {
	echo esc_attr( psp_project_wrapper_classes() );
}

add_filter( 'psp_body_classes', 'psp_user_role_body_classes' );
function psp_user_role_body_classes( $classes ) {

	if( !is_user_logged_in() ) return $classes;

	$cuser = wp_get_current_user();

	foreach( $cuser->roles as $role ) {
		$classes .= 'role-' . $role . ' ';
	}

	return $classes;

}

add_filter( 'psp_body_classes', 'psp_add_template_to_body_class' );
function psp_add_template_to_body_class( $classes ) {

	global $template;
	$template_class = sanitize_title(str_replace( '.php', '', basename($template) ));

	$classes .= $template_class . ' ';

	return $classes;

}

function psp_add_login_template_to_body_class( $classes ) {

	$classes .= 'psp-login-template ';

	return $classes;

}
