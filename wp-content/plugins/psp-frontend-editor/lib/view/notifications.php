<?php
$users = psp_get_project_users();

if( !empty( $users ) ): ?>
    <div id="psp-notify-users" class="psp-modal psp-hide">

            <h2><?php esc_html_e( 'Send Notification', 'psp-front-edit' ); ?></h2>

            <div class="psp-notify-list-message psp-alert psp-hide">
                <p><?php esc_html_e( 'Message Sent', 'psp-front-edit' ); ?></p>
            </div>

            <div class="psp-notify-list-body">

                <?php global $post; ?>
                
                <input type="hidden" id="psp-fe-post-id" value="<?php echo esc_attr_e( $post->ID ); ?>">

                <p><label for="psp-notify-list"><?php esc_html_e( 'Notify', 'psp-front-edit' ); ?></label></p>

                <p class="all-upload-line">
                    <label for="psp-fe-all">
                        <input type="checkbox" class="all-fe-checkbox" id="psp-fe-all" name="psp-fe-all" value="yes" id="psp-fe-all">
                        <?php esc_html_e( 'All Users', 'psp_projects' ); ?>
                    </label>
                    <label for="psp-fe-specific">
                        <input type="checkbox" class="specific-fe-checkbox" id="psp-fe-specific" name="psp-fe-specific" value="specific">
                        <?php esc_html_e( 'Specific Users', 'psp_projects' ); ?>
                    </label>
                </p>

                <ul class="psp-notify-list">
                    <?php
                    $i = 0;
                    foreach( $users as $user ):
                        $username = psp_get_nice_username( $user ); ?>
                        <li><label for="notify-<?php echo esc_attr($i); ?>"><input type="checkbox" name="psp-user[]" id="notify-<?php echo esc_attr($i); ?>" value="<?php echo esc_attr($user['ID']); ?>" class="psp-notify-user-box psp-fe-user"><?php echo esc_html($username); ?></label></li>
                    <?php $i++; endforeach; ?>
                </ul>

                <p>
                    <label for="psp-fe-subject"><?php esc_html_e( 'Subject', 'psp-front-edit' ); ?></label>
                    <input type="text" name="psp-fe-subject" id="psp-fe-subject" value="<?php echo esc_attr( psp_get_option( 'psp_default_subject' ) ); ?>" data-default="<?php echo esc_attr( psp_get_option( 'psp_default_subject' ) ); ?>">
                </p>

                <p><label for="psp-fe-message"><?php esc_html_e( 'Message', 'psp-front-edit' ); ?></label>
                <textarea name="psp-fe-message" id="psp-fe-message" data-default="<?php echo esc_html( psp_get_option( 'psp_default_message' ) ); ?>"><?php echo esc_html( psp_get_option( 'psp_default_message' ) ); ?></textarea></p>

            </div>

            <div class="psp-modal-actions">
                <p><input type="submit" name="update" value="<?php esc_attr_e( 'Send', 'psp-front-edit'); ?>" class="psp-fe-notify-send pano-btn pano-btn-primary"> <a href="#" class="modal-close"><?php esc_html_e( 'Cancel', 'psp-front-edit' ); ?></a></p>
            </div> <!--/.pano-modal-actions-->

    </div>
<?php endif; ?>
