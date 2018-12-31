<div class="document-update-dialog psp-hide psp-modal" id="psp-du-doc-<?php esc_attr_e($document['index']); ?>">
    <form method="post" action="<?php echo esc_url(get_permalink($post_id)); ?>" class="document-update-form">

        <?php if( is_user_logged_in() && get_post_type() == 'psp_projects' ) {
            apply_filters( 'psp_document_update_form_fields', psp_the_document_form_fields( $post_id, $document['index'], $document['title'], get_current_user_id() ) );
        } ?>

        <div class="psp-document-form">

            <h4><?php esc_html_e( 'Update Status', 'psp_projects' ); ?><strong><?php echo esc_html($document['title']); ?></strong></h4>

            <div class="psp-hide psp-message-form">
                <p><strong><?php esc_html_e('Document Status Updated','psp_projects'); ?></strong></p>
                <p class="psp-hide psp-confirm-note"><?php esc_html_e('Notifications have been sent.','psp_projects'); ?></p>
            </div>

            <p><label for="psp-doc-status-field"><?php _e('Status','psp_projects'); ?></label>
                <div class="psp-select-wrapper">
                    <select class="psp-doc-status-field" id="psp-pro-<?php esc_attr_e($post_id); ?>-doc-<?php echo $document['index']; ?>">
                        <?php
                        $options = apply_filters( 'psp_document_options', array(
                            $document['status'] => $document['status'],
                            '---'				=>	'---',
                            'Approved'			=>	__( 'Approved', 'psp_projects' ),
                            'In Review'			=>	__( 'In Review', 'psp_projects' ),
                            'Revisions'			=>	__( 'Revisions', 'psp_projects' ),
                            'Rejected'			=>	__( 'Rejected', 'psp_projects' )
                        ), $post_id );

                        foreach( $options as $value => $title ): ?>
                            <option value="<?php echo esc_attr($value); ?>"><?php echo esc_html($title); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </p>

            <?php if( psp_get_project_users() ) { ?>

                <p><label for="psp-doc-notify"><?php _e('Notify','psp_projects'); ?></label></p>

                <p class="all-line"><label for="psp-du-doc-<?php esc_attr_e($i); ?>-all">
                    <label for="psp-du-doc-<?php esc_attr_e($document['index']); ?>-all">
                        <input type="checkbox" class="all-checkbox" name="psp-notify-all" id="psp-du-doc-<?php esc_attr_e($document['index']); ?>-all" value="all"> <?php esc_html_e( 'All Users', 'psp_projects' ); ?>
                    </label>
                    <label for="psp-du-doc-<?php esc_attr_e($document['index']); ?>-specific">
                        <input type="checkbox" class="specific-checkbox" name="psp-notify-specific" value="specific" id="psp-du-doc-<?php esc_attr_e($document['index']); ?>-specific"> <?php esc_html_e( 'Specific Users', 'psp_projects' ); ?>
                    </label>
                </p>

                <ul class="psp-notify-list">
                    <?php
                    $users = psp_get_project_users();
                    $included = array();

                    foreach( $users as $user ):

                        if( in_array( $user, $included ) ) continue;

                        $included[] = $user;
                        $username = psp_get_nice_username( $user ); ?>

                        <li class="psp-notify-user">
                            <label for="psp-du-doc-<?php echo esc_attr( $document['index'] . '-' . $user['ID'] ); ?>">
                                <input id="psp-du-doc-<?php echo esc_attr( $document['index'] . '-' . $user['ID'] ); ?>" type="checkbox" name="psp-user[]" value="<?php esc_attr_e($user['ID']); ?>" class="psp-notify-user-box"><?php echo esc_html($username); ?>
                            </label>
                        </li>

                    <?php endforeach; ?>
                </ul>

                <p><label for="psp-doc-message"><?php esc_html_e( 'Message', 'psp_projects' ); ?></label></p>

                <p><textarea name="psp-doc-message"></textarea></p>

            <?php } ?>

        </div> <!--/.psp-document-form-->

        <div class="psp-modal-actions">
            <p><input type="submit" name="update" value="<?php esc_attr_e('update', 'psp_projects'); ?>"> <a href="#" class="modal-close"><?php _e('Cancel', 'psp_projects'); ?></a></p>
        </div> <!--/.pano-modal-actions-->

    </form>
</div>
