<?php
/*
 * File: edit/index.php
 *
 * Edit page view, slightly different than creating a new project
 */

// Setup necissary variables to see user, place and markers.
$cuser      = wp_get_current_user();
$sections   = psp_fe_get_edit_sections();
$markers    = psp_fe_get_milestone_markers();
$post_id    = get_query_var( 'psp_manage_option' );
$editable   = psp_fe_is_editable_post( $post_id );

// Identify what section we're on
$section    = ( isset( $_GET['section'] ) && in_array( $_GET['section'], $sections ) ? $_GET['section'] : 'overview' );
$section    = ( isset( $_GET['status'] ) && $_GET['status'] == 'new' ? 'milestones' : $section );
$title      = ( isset($_GET['status']) && $_GET['status'] == 'new' ? __( 'New Project', 'psp-front-edit' ) : __( 'Editing ' . get_the_title($post_id) ) );

// Figure out completion
$completion = $markers[$section]['complete'];

include( psp_template_hierarchy('dashboard/header') ); ?>

    <input id="psp-ajax-url" type="hidden" value="<?php echo esc_url( admin_url() . 'admin-ajax.php' ); ?>">

	<?php
    do_action( 'psp_dashboard_page' );
    do_action( 'psp_dashboard_page_' . __FILE__ ); ?>

	<?php if( is_user_logged_in() ): ?>

		<div id="psp-archive-container" class="psp-grid-container-fluid psp-fe-page">

			<?php include( psp_template_hierarchy( 'global/header/masthead' ) ); ?>

			<div class="psp-grid-row">
				<div id="psp-archive-content" class="psp-col-md-12">
					<div class="psp-grid-row">

                    	<div class="psp-fe-wizard-wrap">

                            <?php
                            if( $editable && psp_can_edit_project($post_id) ): ?>

                                <hgroup class="psp-fe-hgroup psp-group">
                                    <h4 class="psp-fe-tall-headline"><?php echo esc_html($title); ?></h4>
                                    <?php include( PSP_FE_PATH . 'lib/view/partials/timeline.php' ); ?>
                                </hgroup>

                                <h4 class="psp-wizard-section">
                                    <?php if( isset($_GET['status']) && $_GET['status'] == 'new' ): ?>
                                        <strong><?php esc_html_e( 'Step', 'psp-front-edit' );?> <span class="step">5</span> <?php esc_html_e( 'of', 'psp-front-edit' ); ?> 6</strong>
                                    <?php endif; ?>
                                    <span class="timeline-title"><?php esc_html_e( 'Loading...', 'psp_projects' ); ?></span>
                                </h4>

                                <div id="cssload-pgloading">
                                	<div class="cssload-loadingwrap">
                                		<ul class="cssload-bokeh">
                                			<li></li>
                                			<li></li>
                                			<li></li>
                                			<li></li>
                                		</ul>
                                	</div>
                                </div>

                        		<?php
                                // Setup the form arguments based on the post
                                psp_fe_acf_form( $post_id ); ?>

                                <div class="psp-wizard-actions">
                                    <input type="button" class="psp-wizard-edit-prev-button psp-btn" value="Back"> <input type="button" class="psp-wizard-edit-next-button psp-btn" value="Next">
                                </div>

                            <?php
                            else:

                                if( !$editable ): ?>

                                    <h2><?php esc_html_e( 'No project found', 'psp_projects' ); ?></h2>
                                    <p><?php esc_html_e( 'Sorry, but there doesn\'t appear to be a project with that ID.', 'psp_projects' ); ?></p>

                                <?php else: ?>

                                    <h2><?php esc_html_e( 'Permission Denied', 'psp_projects' ); ?></h2>
                                    <p><?php esc_html_e( 'You don\'t have permission to edit this project.', 'psp-projects' ); ?></p>

                                <?php endif;

                            endif; ?>

                    	</div>

                        <p class="psp-wizard-cancel"><a href="<?php echo esc_url(get_permalink($post_id)); ?>"><?php esc_html_e( 'Cancel', 'psp_projects' ); ?></a></p>


			</div> <!--/.psp-row-grid-->
		</div> <!--/.psp-container-->
	</div>

	<?php else: ?>
        <div id="overview" class="psp-comments-wrapper">

			<?php include( psp_template_hierarchy( 'global/login.php' ) ); ?>

        </div>
	<?php endif; ?>

    <?php
    include( psp_template_hierarchy( 'global/navigation-off.php' ) );
    wp_footer(); ?>

</body>
</html>
