<?php
/**
 * Description of psp-phases
 *
 * Functions related to the core Panorama phase capabilities
 * @package psp-projects
 *
 *
 */
// TODO: Test this before launch
function psp_get_phase_color() {

	return apply_filters( 'psp_get_phase_color', array(
		array(
			'name'	=>	'blue',
			'hex'	=>	( psp_get_option( 'psp_accent_color_1' ) ? psp_get_option( 'psp_accent_color_1' ) : '#3299BB' ),
		),
		array(
			'name'	=>	'teal',
			'hex'	=>	( psp_get_option( 'psp_accent_color_2' ) ? psp_get_option( 'psp_accent_color_2' ) : '#4ECDC4' ),
		),
		array(
			'name'	=>	'green',
			'hex'	=>	( psp_get_option( 'psp_accent_color_3' ) ? psp_get_option( 'psp_accent_color_3' ) : '#CBE86B' ),
		),
		array(
			'name'	=>	'pink',
			'hex'	=>	( psp_get_option( 'psp_accent_color_4' ) ? psp_get_option( 'psp_accent_color_4' ) : '#FF6B6B' ),
		),
		array(
			'name'	=>	'maroon',
			'hex'	=>	( psp_get_option( 'psp_accent_color_5' ) ? psp_get_option( 'psp_accent_color_5' ) : '#C44D58' ),
		)
	) );

}

function psp_get_phase_title_by_key( $phase_key = null, $post_id = null ) {

	if( $phase_key == null ) return false;

	$post_id 	= ( $post_id == null ? get_the_ID() : $post_id );

	while( have_rows( 'phases', $post_id ) ) { the_row();
		if( get_sub_field('phase_id') == $phase_key ) return get_sub_field('title');
	}
	return false;

}

function psp_get_phase_classes( $post_id = NULL, $phase_id = 0 ) {

	$post_id = ( $post_id == NULL ? get_the_ID() : $post_id );

	$phase_data = psp_get_phase_completed( $phase_id, $post_id );

	$completion = ( $phase_data['completed'] == 100 ? 'phase-complete' : '' );

	return apply_filters( 'psp_get_phase_classes', $completion . ' psp-phase-complete-' . $phase_data['completed'] . ' psp-phase-remaining-' . ( 100 - $phase_data['completed'] ) . ' psp-phase-id-' . $phase_id, $post_id, $phase_id );

}

if ( is_admin() ) {

	add_action( 'save_post', 'psp_save_phase_task_ids' );

}

// The Frontend Editor uses its own, ACF-specific, save routine
add_filter( 'acf/save_post', 'psp_save_phase_task_ids' );

/**
 * Generate Phase/Task IDs
 * 
 * @param		integer $post_id Post ID
 *                               
 * @access		public
 * @since		{{VERSION}}
 * @return		void
 */
function psp_save_phase_task_ids( $post_id ) {

	if ( get_post_type() !== 'psp_projects' )
		return;

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
		return;

	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) 
		return;

	if ( ! current_user_can( 'edit_post', $post_id ) )
		return;

	if ( false !== wp_is_post_revision( $post_id ) )
		return;
	
	$phases_key = 'field_527d5dc12fa29';
	$tasks_key = 'field_527d5dfd2fa2d';
	
	// For simplicity/readability
	$phases = $_POST['fields'][ $phases_key ];

	foreach ( $phases as $phase_index => $phase ) {
		
		if ( ! isset( $phase['psp_phase_id'] ) || 
		   empty( trim( $phase['psp_phase_id'] ) ) ) {

			$phase['psp_phase_id'] = psp_generate_phase_id();
			$phases[ $phase_index ] = $phase;

		}

		$tasks = $phases[ $phase_index ][ $tasks_key ];

		foreach ( $tasks as $task_index => $task ) {
			
			if ( $task_index === 'acfcloneindex' ) continue;

			if ( ! isset( $task['psp_task_id'] ) || 
			   empty( trim( $task['psp_task_id'] ) ) ) {
				
				$task['psp_task_id'] = psp_generate_task_id();
				$phases[ $phase_index ][ $tasks_key ][ $task_index ] = $task;
					
			}

		}

	}
	
	$_POST['fields'][ $phases_key ] = $phases;

	return $post_id;

}

/**
 * Easy way for extensions to always generate Phase IDs in the same way as Project Panorama Core, even if implementation were to change down the road
 * 
 * @since		{{VERSION}}
 * @return		string Phase ID
 */
function psp_generate_phase_id() {
	
	return wp_generate_uuid4();
	
}

/**
 * Easy way for extensions to always generate Task IDs in the same way as Project Panorama Core, even if implementation were to change down the road
 * 
 * @since		{{VERSION}}
 * @return		string Task ID
 */
function psp_generate_task_id() {
	
	return wp_generate_uuid4();
	
}