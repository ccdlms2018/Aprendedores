<?php
if( !isset($_POST['psp-fe-use-template']) || empty($_POST['psp-fe-use-template']) ) {
    wp_redirect( get_post_type_archive_link('psp_projects') );
    exit();
}

$cuser = wp_get_current_user();

if( !wp_verify_nonce( $_REQUEST['_wpnonce'], 'duplicate_project_' . $cuser->ID ) {
    exit();
}

$post       = get_post( $_POST['psp-fe-use-template'] );
$new_id     = psp_duplicate_post_create_duplicate( $post, 'publish' );
$redirect   = ( PSP_FE_PERMALINKS ? 'manage/edit/' . $new_id .'/?status=template' : '&psp_manage_page=edit&psp_manage_option=' . $new_id . '&status=template' );

wp_redirect( get_post_type_archive_link('psp_projects') . $redirect );

exit();
