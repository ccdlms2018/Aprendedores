<?php
$post_id 	= ( isset($post_id) ? $post_id : get_the_ID() );

/**
 * Setup the document variable
 * @var array
 */
$document   = array(
    'title'     =>  $doc['title'],
    'file'      =>  $doc['file'],
    'url'       =>  $doc['url'],
    'status'    =>  psp_translate_doc_status($doc['status']),
    'type'      =>  '',
    'class'     =>  apply_filters( 'psp_document_icon_class', psp_get_icon_class($doc['file']['url']) ),
    'link'      =>  '',
    'index'     =>  $doc['index'],
    'description'   =>  $doc['description']
);
$document['type']	= psp_get_doc_type($document['class']);

// Check to see if there is a file, if not, use the manually entered URL - make sure permalinks are enabled
if( psp_get_option( 'psp_disable_file_obfuscation' ) ) {
	$document['link'] 	= ( !empty( $document['file'] ) ? $document['file']['url'] : $document['url'] );
} elseif( get_option('permalink_structure') ) {
	$document['link'] 	= ( !empty( $document['file'] ) ? get_permalink( $post_id ) . '?psp_download=' . $document['index'] : $document['url'] );
} else {
	$document['link'] 	= ( !empty( $document['file'] ) ? get_permalink( $post_id ) . '&psp_download=' . $document['index'] : $document['url'] );
}

$data_atts = array(
	'ID'		=>	$doc['index'],
	'title'		=>	$doc['title'],
	'status'	=>	$doc['status'],
); ?>

<li id="<?php esc_attr_e( 'psp-project-' . $post_id . '-doc-' . $document['index'] ); ?>" class="list-item psp-document <?php psp_the_task_item_classes($post_id); ?>" <?php psp_parse_data_atts($data_atts); ?>>

	 <?php do_action( 'psp_before_document' ); ?>

	    <a href="<?php echo esc_url($document['link']); ?>" class="psp-icon <?php echo esc_attr($document['class']); ?>" target="_new" data-placement="left" data-toggle="psp-tooltip" title="<?php echo esc_attr($document['type']); ?>"></a>

	    <p class="psp-doc-title">

			<?php do_action( 'psp_before_document_title', $post_id ); ?>

		    <a href="<?php echo esc_url($document['link']); ?>" target="_new"><strong class="doc-title"><?php echo esc_html($document['title']); ?></strong></a>

			<?php
			do_action( 'psp_before_document_status', $post_id );

			/* Check to see if this document doesn't get a status */
			if( $doc['status'] != 'none' ): ?>
			    <a class="doc-status status-<?php echo esc_attr($document['status']); ?> psp-modal-link" href="#psp-document-status-modal">
					<?php echo esc_html($document['status']); ?>
					<?php if( is_user_logged_in() && get_post_type() == 'psp_projects' ): ?>
						<span class="fa fa-pencil" href="#"></span>
					<?php endif; ?>
				</a>
			<?php endif; ?>

			<?php do_action( 'psp_before_document_description', $post_id ); ?>

		    <span class="description"><?php esc_html_e( $document['description'] ); ?></span>

			<?php do_action( 'psp_after_document_description', $post_id ); ?>

	    </p>

	<?php do_action( 'psp_after_document' ); ?>

</li>
