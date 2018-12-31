<?php
$approved_class = ( empty($phase_docs) ? 'psp-hide' : '' );
$empty_class    = ( empty($phase_docs) ? '' : 'psp-hide' );
$phase_approved = psp_count_approved_documents( $phase_docs['phase'] );

do_action( 'psp_before_phase_docs_wrapper', $post_id, $phase_index, $phase_comment_key ); ?>

<div id="phase-documents-<?php esc_attr_e( $phase_index + 1 ); ?>" class="psp-phase-documents">

    <?php do_action( 'psp_before_phase_docs_title', $post_id, $phase_index, $phase_comment_key ); ?>

    <h4>
        <a href="#" class="doc-list-toggle">
            <span class="psp-doc-approved <?php esc_attr_e($approved_class); ?>">
                <?php
                echo sprintf(
                    __( "<b data-toggle='psp-tooltip' data-placement='top' title='%s'><i class='fa fa-files-o'></i> <b class='doc-approved-count'>%s</b>/<b class='doc-total-count'>%s</b></b>", "psp_projects" ),
                    __( 'Phase Documents Approved', 'psp_projects' ),
                    $phase_approved,
                    count($phase_docs['phase'])
                ); ?>
            </span>
            <span class="psp-doc-empty <?php esc_attr_e($empty_class); ?>">
                <?php esc_html_e( 'No Documents', 'psp_projects' ); ?>
            </span>
            <?php esc_html_e( 'Phase Documents', 'psp_projects' ); ?>
        </a>
    </h4>

    <?php do_action( 'psp_before_phase_doc_list', $post_id, $phase_index, $phase_comment_key ); ?>

    <div class="psp-phase-documents-wrapper">
        <?php do_action( 'psp_inside_phase_doc_wrapper' ); ?>

            <ul class="psp-phase-docs-list psp-documents-row">
                <?php
                if( !empty( $phase_docs['phase'] ) ):
                    do_action( 'psp_start_of_doc_list', $post_id, $phase_index, $phase_comment_key );
                    foreach( $phase_docs['phase'] as $doc ) include( psp_template_hierarchy( 'projects/phases/documents/single.php') );
                    do_action( 'psp_end_of_doc_list', $post_id, $phase_index, $phase_comment_key );
                endif; ?>
            </ul>
        <?php if( empty($phase_docs['phase']) ): ?>
            <p class="phase-docs-empty-message"><em><?php esc_html_e( 'No documents attached to this phase.', 'psp_projects' ); ?></em></p>
        <?php endif;

        do_action( 'psp_inside_phase_doc_wrapper_after', $post_id, $phase_index, $phase_comment_key ); ?>
    </div>

    <?php do_action( 'psp_after_phase_doc_list', $post_id, $phase_index, $phase_comment_key ); ?>

</div>
