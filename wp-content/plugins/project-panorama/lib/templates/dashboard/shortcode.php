<div id="psp-projects">
    <div id="psp-archive-container" class="psp-grid-container-fluid">

    	<div class="psp-grid-row">

    		<div id="psp-archive-content" class="psp-col-md-12">

    			<div class="psp-grid-row">

    				<div class="psp-col-lg-9 psp-col-md-8">

    					<?php
    					do_action( 'psp_dashboard_before_my_projects' );

    					include( psp_template_hierarchy( 'dashboard/components/projects/my-projects.php' ) );

    					do_action( 'psp_dashboard_after_my_projects' );

    					if( $tasks ) include( psp_template_hierarchy( 'dashboard/components/tasks/dashboard.php' ) );

    					do_action( 'psp_dashboard_after_my_tasks' );

    					?>

    				</div> <!--/.psp-col-md-8-->

    				<aside id="psp-archive-sidebar" class="psp-col-lg-3 psp-col-md-4">

    					<?php do_action( 'psp_before_dashboard_widgets' ); ?>

                        <?php do_action( 'psp_dashboard_widgets' ); ?>

    					<div class="psp-archive-widget cf">

    						<h4><?php esc_html_e( 'Overview', 'psp_projects' ); ?></h4>

    						<?php echo psp_get_project_breakdown(); ?>

    					</div>

    					<?php
    					$teams = psp_get_user_teams();
    					if( $teams ): ?>
    						<div class="psp-archive-widget">

    							<h4><?php esc_html_e( 'My Teams', 'psp_projects' ); ?></h4>

    							<ul class="psp-team-list">
    								<?php foreach( $teams as $team ): $members = get_field( 'team_members', $team->ID ); ?>
    									<li>
    										<?php psp_team_user_icons( $team->ID ); ?>
    										<a href="<?php echo esc_url( get_the_permalink( $team->ID ) ); ?>">
    											<span>
    												<strong class="psp-accent-color-1"><?php echo esc_html( get_the_title( $team->ID ) ); ?></strong>
    												<em><?php echo count( $members ) . ' ' . __( 'Members', 'psp_projects' ); ?></em>
    											</span>
    										</a>
    									</li>
    								<?php endforeach; ?>
    							</ul>

    						</div>
    					<?php endif; ?>

    					<div class="psp-archive-widget">
    						<p><a class="psp-ical-link pull-right psp-archive-ical-link" href="<?php echo psp_get_ical_link(); ?>" target="_new"><?php echo esc_html_e( 'iCal Feed', 'psp_projects' ); ?></a></p>
    						<h4><?php esc_html_e( 'Calendar', 'psp_projects' ); ?></h4>
    						<?php echo psp_output_project_calendar(); ?>
    					</div> <!--/.psp-archive-widget-->

                        <?php do_action( 'psp_after_dashboard_widgets' ); ?>

    				</aside>

    			</div>
    		</div>
    	</div>
    </div>
</div>
