<?php

/* Custom Single.php for project only view */
$cuser = wp_get_current_user();
acf_form_head();

include( psp_template_hierarchy('dashboard/header') ); ?>

    <input id="psp-ajax-url" type="hidden" value="<?php echo esc_url(admin_url()); ?>admin-ajax.php">

	<?php
    do_action('psp_dashboard_page');
    do_action( 'psp_dashboard_page_' . __FILE__ ); ?>

	<?php if(is_user_logged_in()): ?>

        <style type="text/css">
            #psp-primary-header {
                display: none;
            }
        </style>

		<div id="psp-archive-container" class="psp-grid-container-fluid">

			<div class="psp-grid-row psp-equal-column-container">

				<div id="psp-archive-content" class="psp-col-md-12">

					<div class="psp-grid-row">

                        <div class="psp-fe-wizard-wrap">

                            <hgroup class="psp-fe-hgroup psp-group">

                                <h4 class="psp-fe-tall-headline"><?php esc_html_e( 'New Project', 'psp_projects' ); ?></h4>

                                <aside class="psp-timeline">
                                    <ol>
                                        <li class="psp-timeline--marker active" data-complete="10" data-step="1">
                                            <span class="psp-timeline--dot"></span>
                                            <strong><?php esc_html_e( 'Overview', 'psp-front-edit' ); ?></strong>
                                        </li>
                                        <li class="psp-timeline--marker" data-complete="24.66" data-step="2">
                                            <span class="psp-timeline--dot"></span>
                                            <strong><?php esc_html_e( 'Documents', 'psp-front-edit' ); ?></strong>
                                        </li>
                                        <li class="psp-timeline--marker" data-complete="41.33" data-step="3">
                                            <span class="psp-timeline--dot"></span>
                                            <strong><?php esc_html_e( 'Access', 'psp-front-edit' ); ?></strong>
                                        </li>
                                        <li class="psp-timeline--marker" data-complete="58" data-step="4">
                                            <span class="psp-timeline--dot"></span>
                                            <strong><?php esc_html_e( 'Settings', 'psp-front-edit' ); ?></strong>
                                        </li>
                                        <li class="psp-timeline--marker" date-complete="74.5" data-step="5">
                                            <span class="psp-timeline--dot"></span>
                                            <strong><?php esc_html_e( 'Milestones', 'psp-front-edit' ); ?></strong>
                                        </li>
                                        <li class="psp-timeline--marker" date-complete="91.25" data-step="6">
                                            <span class="psp-timeline--dot"></span>
                                            <strong><?php esc_html_e( 'Phases & Tasks', 'psp-front-edit' ); ?></strong>
                                        </li>
                                    </ol>
                                    <p class="psp-timeline--bar"><span></span></p>
                                </aside>

                            </hgroup>

                            <h4 class="psp-wizard-section">
                                <strong><?php esc_html_e( 'Step', 'psp-front-edit' );?> <span class="step">1</span> <?php esc_html_e( 'of', 'psp-front-edit' ); ?> 6</strong>
                                <span class="timeline-title"><?php esc_html_e( 'Overview', 'psp-front-edit' ); ?></span>
                            </h4>

                            <?php

                            $templates  = psp_get_global_templates();
                            $action    = ( PSP_FE_PERMALINKS ? 'manage/duplicate/' : '&psp_manage_page=duplicate' );

                            if( $templates->have_posts() ): ?>

                                <div class="new-project-type-group">
                                    <p>
                                        <label for="psp-fe-brand-new">
                                            <input type="radio" name="psp-fe-project-type" value="new" id="psp-fe-brand-new" checked autocomplete="off"> <?php esc_html_e( 'Blank Project', 'psp_projects' ); ?>
                                        </label>
                                        <label for="psp-fe-from-template">
                                            <input type="radio" name="psp-fe-project-type" value="template" id="psp-fe-from-template" autocomplete="off"> <?php esc_html_e( 'Use Template', 'psp_projects' ); ?>
                                        </label>
                                    </p>
                                </div>

                                <div class="psp-form psp-fe-template-form">
                                    <form method="post" action="<?php echo esc_attr( get_post_type_archive_link('psp_projects') . $action ); ?>">
                                    <?php
                                    $cuser = wp_get_current_user();
                                    wp_nonce_field( 'duplicate_project_' . $cuser->ID ); ?>
                                    <input type="hidden" name="user_id" value="<?php echo esc_attr($cuser->ID); ?>">
                                    <ol>
                                        <li>
                                            <select name="psp-fe-use-template" id="psp-fe-use-template">
                                                <option value="---"><?php esc_html_e( 'Use Template', 'psp_projects' ); ?>
                                                <?php while( $templates->have_posts() ): $templates->the_post(); global $post; ?>
                                                    <option value="<?php echo esc_attr( $post->ID ); ?>"><?php the_title(); ?></option>
                                                <?php endwhile; ?>
                                            </select>
                                        </li>
                                        <li><input type="submit" class="psp-fe-use-template-submit pano-btn" value="Select" name="Select"></li>
                                    </ol>
                                    </form>
                                </div>
                            <?php
                            endif; ?>

                            <?php
                            psp_fe_acf_form();  ?>

                            <div class="psp-wizard-actions">
                                <input type="button" class="psp-wizard-prev-button psp-btn" value="Back"> <input type="button" class="psp-wizard-next-button psp-btn" value="Next">
                            </div>

                        </div> <!--/.psp-wizard-wrap-->

                        <p class="psp-wizard-cancel"><a href="<?php echo esc_url(get_post_type_archive_link('psp_projects')); ?>"><?php esc_html_e( 'Cancel', 'psp_projects' ); ?></a></p>

                	</div> <!--/.psp-grid-row-->

		        </div> <!--/#psp-archive-content-->

	        </div> <!--/.psp-archive-content-->

	    </div> <!--/.psp-grid-row-->

	<?php else: ?>

        <div id="overview" class="psp-comments-wrapper">

			<?php include( psp_template_hierarchy( 'global/login.php' ) ); ?>

        </div>
	<?php endif; ?>

    <?php
    if( PSP_VER == 5 ) {
        acf_enqueue_uploader();
    } ?>

    <?php
    include( psp_template_hierarchy( 'dashboard/footer.php' ) );
    wp_footer(); ?>

</body>
</html>
