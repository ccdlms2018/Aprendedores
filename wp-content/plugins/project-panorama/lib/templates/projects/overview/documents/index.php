<?php
$post_id = ( isset($post_id) ? $post_id : get_the_ID() ); ?>

<div id="psp-documents" class="<?php echo esc_attr($style); ?> psp-col-md-5  psp-overview-col">

	<?php do_action( 'psp_before_documents', $post_id ); ?>

	<div class="psp-documents-wrap">

		<?php do_action( 'psp_before_document_section_title', $post_id ); ?>
		<h4><?php esc_html_e( 'Documents', 'psp_projects' ); ?></h4>
		<?php do_action( 'psp_after_document_section_title', $post_id ); ?>

		<?php
		include( psp_template_hierarchy( 'projects/overview/documents/documents-loop' ) );

		if( $style != 'shortcode' ) do_action( 'psp_after_documents', $post_id ); ?>

	</div> <!--/.psp-documents-wrap-->

	<?php // do_action( 'psp_after_documents', $post_id ); ?>

</div> <!--/#project-documents-->
