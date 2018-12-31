<?php
/*
add_action( 'psp_after_task_entry', 'psp_sub_task_list', 10, 3 );
function psp_sub_task_list( $post_id = null, $phase_id = null, $task_id = null ) {

    $post_id    = $post_id == null ? get_the_ID() : $post_id;
    $phases     = get_field( 'phases', $post_id );

    if( !isset( $phases[$phase_id]['tasks'][$task_id]['sub_task'] ) ) return false;

    $s = 0;
    foreach( $phases[$phase_id]['tasks'][$task_id]['sub_task'] as $sub_task ) {
        // $sub_task['ID'] = $s; $s++;
        include( PSP_ST_PATH . 'lib/views/subtask-template.php' );
    }

}*/

add_filter( 'psp_task_classes', 'psp_add_sequential_task_class', 10, 4 );
function psp_add_sequential_task_class( $classes, $post_id = null, $phase_id, $task_id ) {

    $post_id    = $post_id == null ? get_the_ID() : $post_id;
    $phases     = get_field( 'phases', $post_id );

    if( !isset( $phases[$phase_id]['tasks'][$task_id]['seq_task'][0] ) ) return $classes;

    $classes .= ' psp-is-sequential';

    return $classes;

}
