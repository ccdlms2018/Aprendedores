<?php
/**
 * Teams Listing Page
 *
 * Lists all the teams on the site that you have access to
 * @var post_type	psp_teams
 */
include( psp_template_hierarchy( 'dashboard/header.php' ) );

$tasks_id   = get_query_var( 'psp_tasks_page' );
$cuser 	    = wp_get_current_user();
$tasks_id   = ( $tasks_id == 'home' ? $cuser->ID : $tasks_id ); ?>

<?php include( psp_template_hierarchy( 'global/header/navigation-sub' ) ); ?>

<div id="psp-archive-container" class="psp-grid-container-fluid">
	<div class="psp-grid-row">

        <div id="psp-archive-content" class="psp-col-md-12">

			<?php
            if( ( $cuser->ID == $tasks_id || current_user_can( 'edit_others_psp_projects' ) ) && get_user_by( 'id', $tasks_id ) ): ?>

                <div class="psp-archive-section">
                    <h2 class="psp-box-title"><?php esc_html_e( 'Upcoming Tasks', 'psp_projects' ); ?></h2>
                    <?php echo psp_all_my_tasks_shortcode( array( 'id' => $tasks_id ), false ); ?>
                </div>

            <?php else: ?>

                <div class="psp-col-md-6 psp-col-md-offset-3">
                    <div class="psp-error">
                        <p><em><?php esc_html_e( 'You do not have access to these tasks', 'psp_projects' ); ?></em></p>
                    </div>
                </div>

        <?php endif; ?>

    </div>
<?php
include( psp_template_hierarchy( 'dashboard/footer.php' ) );
