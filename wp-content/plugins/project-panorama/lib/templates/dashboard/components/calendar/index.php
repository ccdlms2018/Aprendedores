<?php
/**
 * Teams Listing Page
 *
 * Lists all the teams on the site that you have access to
 * @var post_type	psp_teams
 */
include( psp_template_hierarchy( 'dashboard/header.php' ) );

$calendar_id 	= get_query_var( 'psp_calendar_page' );
$cuser 			= wp_get_current_user();
$calendar_id	= ( $calendar_id == 'home' ? $cuser->ID : $calendar_id ); ?>

<?php include( psp_template_hierarchy( 'global/header/navigation-sub' ) ); ?>

<div id="psp-archive-container" class="psp-grid-container-fluid">

	<div class="psp-grid-row">

        <div id="psp-archive-content" class="psp-col-md-12">

			<?php
            if( ( $cuser->ID == $calendar_id ) || ( current_user_can( 'edit_others_psp_projects' ) ) && ( get_user_by( 'id', $calendar_id )) ): ?>

                <div class="psp-archive-section">

                    <h2 class="psp-box-title"><?php esc_html_e( 'Calendar', 'psp_projects' ); ?></h2>

                    <?php echo psp_output_project_calendar( $calendar_id ); ?>

                </div>

            <?php else: ?>

                <div class="psp-col-md-6 psp-col-md-offset-3">

                    <div class="psp-error">
                        <p><em><?php esc_html_e( 'You do not have access to this calendar', 'psp_projects' ); ?></em></p>
                    </div>

            </div>

        <?php endif; ?>

    </div>
<?php
include( psp_template_hierarchy( 'dashboard/footer.php' ) );
