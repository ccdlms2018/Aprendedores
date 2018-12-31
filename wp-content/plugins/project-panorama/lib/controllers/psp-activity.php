<?php
function psp_add_activity( $args, $post_id = null) {

    $post_id    = ( $post_id == null ? get_the_ID() : $post_id );
    $cuser      = wp_get_current_user();

    $defaults   = apply_filters( 'psp_add_activity_defaults', array(
        'user_id'   =>  $cuser->ID,
        'activity'  =>  __( 'updated project', 'psp_projects' ),
        'date'      =>  date('Ymd'),
    ) );

    $args = wp_parse_args( $args, $defaults );

}

add_action( 'psp_project_progress_change', 'psp_record_progress_change', 10, 2 );
function psp_record_progress_change( $post_id = null, $progress ) {

    $post_id    = ( $post_id == null ? get_the_ID() : $post_id );
    $cuser      = wp_get_current_user();

    $args = apply_filters( 'psp_project_completion_args', array(
        'user_id'   =>  $cuser->ID,
        'activity'  =>  __( 'updated project', 'psp_projects' ),
        'date'      =>  date('Ymd'),
        'progress'  =>  $progress
    ), $post_id, $progress );

    add_post_meta( $post_id, '_psp_progress_record', $args );

}

function psp_parse_activity_meta( $meta = NULL, $post_id = null ) {

    $post_id = ( $post_id == null ? get_the_ID() : $post_id );

    if( empty($meta) ) return false;

    $activity = array();

    $activity[] = array(
        'date'      =>  get_the_date( 'Ymd', $post_id ),
        'progress'  =>  0,
    );

    foreach( $meta as $point ) {
        if($point['progress']) {
            $activity[] = array( 'date' => $point['date'], 'progress' => $point['progress'] );
        }
    }

    return $activity;

}
