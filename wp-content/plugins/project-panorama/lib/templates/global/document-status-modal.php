<?php
$post_id = ( isset($post_id) ? $post_id : get_the_ID() ); ?>

<div class="document-update-dialog psp-hide psp-modal" id="psp-document-status-modal">
    <form method="post" action="<?php echo esc_url( get_permalink($post_id) ); ?>" class="document-update-form">

        <?php if( is_user_logged_in() && get_post_type() == 'psp_projects' ) {
            apply_filters( 'psp_document_update_form_fields', psp_the_document_form_fields( $post_id, get_current_user_id() ) );
        } ?>

        <div class="psp-document-form">

            <h2><?php esc_html_e( 'Update Status', 'psp_projects' ); ?></h2>

            <div class="psp-hide psp-message-form">
                <p><strong><?php esc_html_e( 'Document Status Updated', 'psp_projects' ); ?></strong></p>
                <p class="psp-hide psp-confirm-note"><?php esc_html_e( 'Notifications have been sent.', 'psp_projects' ); ?></p>
            </div>

            <p>
                <label for="psp-doc-status-field"><?php esc_html_e('Status','psp_projects'); ?></label>
                <div class="psp-select-wrapper">
                    <select name="doc-status" class="psp-doc-status-field">
                        <?php
                        $options = apply_filters( 'psp_document_options', array(
                            '---'						=>	'---',
                            'Approved'					=>	__( 'Approved', 'psp_projects' ),
                            'In Review'					=>	__( 'In Review', 'psp_projects' ),
                            'Revisions'					=>	__( 'Revisions', 'psp_projects' ),
                            'Rejected'					=>	__( 'Rejected', 'psp_projects' )
                        ) );

                        foreach( $options as $value => $title ): ?>
                            <option value="<?php esc_attr_e( $value ); ?>"><?php esc_html_e( $title ); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </p>

            <?php if( psp_get_project_users() ): ?>

                <p><label for="psp-doc-notify"><?php esc_html_e( 'Notify', 'psp_projects' ); ?></label></p>

                <p class="all-line"><label for="psp-du-doc-all">
                    <label for="psp-du-doc-all">
                        <input type="checkbox" class="all-checkbox" name="psp-notify-all" id="psp-du-doc-all" value="all"> <?php esc_html_e( 'All Users', 'psp_projects' ); ?>
                    </label>
                    <label for="psp-du-doc-specific">
                        <input type="checkbox" class="specific-checkbox" name="psp-notify-specific" value="specific" id="psp-du-doc-specific"> <?php esc_html_e( 'Specific Users', 'psp_projects' ); ?>
                    </label>
                </p>

                <ul class="psp-notify-list">
                    <?php
                    $users      = psp_get_project_users();
                    $included   = array();

                    foreach( $users as $user ):

                        if( in_array( $user, $included ) ) continue;

                        $included[] = $user;
                        $username   = psp_get_nice_username( $user ); ?>

                        <li class="psp-notify-user">
                            <label for="<?php esc_attr_e( 'psp-du-doc-' . $user['ID'] ); ?>">
                                <input id="<?php esc_attr_e( 'psp-du-doc-' . $user['ID'] ); ?>" type="checkbox" name="psp-user[]" value="<?php esc_attr_e($user['ID']); ?>" class="psp-notify-user-box"><?php esc_html_e($username); ?>
                            </label>
                        </li>

                    <?php endforeach; ?>
                </ul>

                <p><label for="psp-doc-message"><?php esc_html_e( 'Message', 'psp_projects' ); ?></label></p>

                <p><textarea name="psp-doc-message"></textarea></p>

            <?php endif; ?>

        </div> <!--/.psp-document-form-->

        <div class="psp-modal-actions">
            <p><input type="submit" name="update" value="<?php esc_attr_e( 'Update', 'psp_projects' ); ?>"> <a href="#" class="modal-close js-psp-doc-status-reset"><?php esc_html_e( 'Cancel', 'psp_projects' ); ?></a></p>
        </div> <!--/.pano-modal-actions-->

    </form>
</div>
