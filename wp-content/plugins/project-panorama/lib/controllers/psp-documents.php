<?php
/**
 * psp-documents.php
 *
 * Controls documents and document management.
 *
 * @category controller
 * @package psp-projects
 * @author Ross Johnson
 * @since 1.3.6
 */

function psp_documents_upload_directory( $param ) {

    $mydir = '/panorama';

    $param['path'] 	= $param['basedir'] . $mydir;
    $param['url'] 	= $param['baseurl'] . $mydir;

    return $param;

}

add_action( 'wp', 'psp_download_redirect' );
function psp_download_redirect() {

	if( ( isset( $_GET[ 'psp_download' ] ) ) && ( get_post_type() == 'psp_projects' ) ) {

		global $post;

		if( !panorama_check_access( $post->ID ) ) {

			wp_die( __( 'You don\'t have access to this file', 'psp_projects' ) );

		}

		$i				= $_GET[ 'psp_download' ];
		$files 			= get_field( 'documents', $post->ID );
		$attachment_id	= $files[ $i ][ 'file' ][ 'id' ];
		$attachment		= get_attached_file( $attachment_id );

		if( ! file_exists( $attachment ) ) {

			wp_die( __( 'File no longer exists', 'psp_projects' ) );

		}

		// required for IE
		if(ini_get('zlib.output_compression')) { ini_set('zlib.output_compression', 'Off');	}

		// get the file mime type using the file extension
		switch(strtolower(substr(strrchr($attachment, '.'), 1))) {
			case 'pdf': $mime = 'application/pdf'; break;
			case 'zip': $mime = 'application/zip'; break;
			case 'jpeg':
			case 'jpg': $mime = 'image/jpg'; break;
			default: $mime = 'application/force-download';
		}

		header('Pragma: public'); 	// required
		header('Expires: 0');		// no cache
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Last-Modified: '.gmdate ('D, d M Y H:i:s', filemtime ($attachment)).' GMT');
		header('Cache-Control: private',false);
		header('Content-Type: '.$mime);
		header('Content-Disposition: attachment; filename="'.basename($attachment).'"');
		header('Content-Transfer-Encoding: binary');
		header('Content-Length: '.filesize($attachment));	// provide file size
		header('Connection: close');

        ob_clean();   // discard any data in the output buffer (if possible)
        flush();      // flush headers (if possible)

		readfile( $attachment );		// push it out

		exit();

	}

}

function psp_translate_doc_status( $status ) {

    $translation = '';

 	if( $status == 'Approved' ) {
	 	$translation = __( 'Approved' , 'psp_projects' );
 	}

 	if( $status == 'In Review' ) {
	 	$translation = __( 'In Review', 'psp_projects' );
 	}

 	if( $status == 'Revisions' ) {
	 	$translation = __( 'Revisions','psp_projects' );
 	}

 	if( $status == 'Rejected' ) {
		$translation = __( 'Rejected', 'psp_projects' );
	}

    return apply_filters( 'psp_translate_doc_status', $translation, $status );

}

function psp_parse_phase_documents( $documents = null , $phase_comment_key = null, $tasks = null ) {

    if( empty($documents) || empty($phase_comment_key) ) return false;

    $i = 0;
    $phase_docs = array(
        'all'   => array(),
        'phase' => array(),
        'tasks' => array()
    );

    foreach( $documents as $doc ) {

        if( $doc['document_phase'] == $phase_comment_key ) {

            $doc['index'] = $i;

            $phase_docs['phase'][]  = $doc;
            $phase_docs['all'][]    = $doc;

        }

        $i++;

    }

    // Count tasks if they exist
    if( $tasks && !empty($tasks) ) {

        $task_keys = array();

        foreach( $tasks as $task ) {

            if( !isset($task['task_id']) ) {
                continue;
            }

            $task_keys[] = $task['task_id'];
        }

        foreach( $documents as $doc ) {

            if( !isset($doc['document_task']) ) {
                continue;
            }

            if( in_array($doc['document_task'], $task_keys ) ) {
                $phase_docs['tasks'][]  = $doc;
                $phase_docs['all'][]    = $doc;
            }

        }

    }

    return $phase_docs;

}

/**
 * Grab all Documents that are assigned to a Task
 *
 * @param		array  $documents Documents within a Project
 * @param		string $task_id   Task ID
 *
 * @since		{{VERSION}}
 * @return		array  Documents within the Task
 */
function psp_parse_task_documents( $documents = null , $task_id = null ) {

    if( empty($documents) || empty($task_id) ) return false;

    $i = 0;
    $task_docs = array();

    foreach( $documents as $doc ) {

        if( $doc['document_task'] == $task_id ) {
            $doc['index'] = $i;
            $task_docs[] = $doc;
        }

        $i++;

    }

    return $task_docs;

}

function psp_count_approved_documents( $documents = null ) {

    if( empty($documents) ) {
        return 0;
    }

    $approved   = 0;

    foreach( $documents as $document ) {
        if( $document['status'] == 'Approved' || $document['status'] == 'none' ) {
            $approved++;
        }
    }

    return $approved;

}

add_filter( 'upload_dir', 'psp_modify_psp_upload_directory' );
function psp_modify_psp_upload_directory( $param ) {

    $id = ( isset( $_REQUEST['post_id'] ) ? $_REQUEST['post_id'] : '' );

    if( !$id ) return $param;

    if( get_post_type( $id ) != 'psp_projects' ) return $param;

    $psp_dir        = '/psp';
    $param['path']  = $param['basedir'] . $psp_dir;
    $param['url']   = $param['baseurl'] . $psp_dir;

    return $param;

}

add_action( 'init', 'psp_add_index_file_in_psp_uploads' );
function psp_add_index_file_in_psp_uploads() {

    $has_run = get_option( 'psp_uploads_add_index_file' );

    if( $has_run ) {
        return;
    }

    $uploads_dir = wp_upload_dir();

    touch( $uploads_dir['basedir'] . '/psp/index.php' );

    update_option( 'psp_uploads_add_index_file', 1 );

}
