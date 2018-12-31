<?php
add_action( 'wp_ajax_nopriv_psp_fe_notify', 'psp_fe_notify' );
add_action( 'wp_ajax_psp_fe_notify', 'psp_fe_notify' );
function psp_fe_notify() {

    $post_id    = $_POST[ 'post_id' ];
    $message    = apply_filters( 'psp-fe-message', psp_populate_project_template_variables( stripslashes( $_POST[ 'message' ] ), $post_id ) );
    $subject    = apply_filters( 'psp-fe-subject', psp_populate_project_template_variables( stripslashes( $_POST[ 'subject' ] ), $post_id ) );
    $users      = psp_sanitize_integers( $_POST[ 'users' ] );
    $progress   = psp_compute_progress( $post_id );

    if( !empty( $users ) ) {

        foreach( $users as $user_id ) {

            psp_send_progress_email( $user_id, $subject, $message, $post_id, $progress );

        }

    }

    wp_send_json_success( array( 'success' => true ) );

}

add_action( 'wp_ajax_nopriv_psp_fe_userlist', 'psp_fe_userlist' );
add_action( 'wp_ajax_psp_fe_userlist', 'psp_fe_userlist' );
function psp_fe_userlist() {

    $user_ids   = $_POST[ 'users' ];
    $html       = '';

    if( empty( $user_ids ) ) {

        wp_send_json_error();

    }

    $users = get_users( array( 'include' => $user_ids ) );
    $html  = '<option value="unassigned"></option>';

    foreach( $users as $user ) {

        $html .= '<option value="' . $user->ID . '">' . $user->display_name . '</option>';

    }

    echo $html;

    die();

}
