<?php
/**
 * Teams Listing Page
 *
 * Lists all the teams on the site that you have access to
 * @var post_type	psp_teams
 */
include( psp_template_hierarchy( 'dashboard/header.php' ) );
include( psp_template_hierarchy( 'global/header/navigation-sub' ) ); ?>

<div id="psp-archive-container" class="psp-grid-container-fluid">

	<div class="psp-grid-row">

		<div id="psp-archive-content" class="psp-col-md-12">
	        <div class="psp-teams-section">

				<?php
				$i		= 0;
				$teams 	= ( current_user_can( 'delete_others_psp_projects' ) ? psp_get_the_teams() : psp_get_user_teams_query( $cuser->ID ) );

				if( $teams->have_posts() ): ?>
					<div class="psp-row psp-teams">
						<?php while( $teams->have_posts() ): $teams->the_post();

							if( $i %3 == 0 && $i > 1 ) echo '</div><div class="psp-row psp-teams">'; ?>

							<div class="psp-col-lg-4 psp-col-sm-6">
								<div class="psp-team-card">
									<aside class="thumbnail">
										<?php
										if( has_post_thumbnail() ):
											the_post_thumbnail( 'thumbnail' );
										else: ?>
											<img src="<?php echo PROJECT_PANARAMA_URI; ?>/assets/images/default-team.png" alt="<?php the_title(); ?>">
										<?php endif; ?>
									</aside>
									<article>

										<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>

										<ul class="team-meta">
											<li><strong><?php echo count( get_field( 'team_members', $post->ID ) ); ?></strong> <span><?php esc_html_e( 'Members', 'psp_projects' ); ?></span></li>
											<li><strong><?php echo count( psp_get_team_projects( $post->ID, 'incomplete' ) ); ?></strong> <span><?php esc_html_e( 'Active', 'psp_projects' ); ?></span></li>
											<li><strong><?php echo count( psp_get_team_projects( $post->ID, 'completed' ) ); ?></strong> <span><?php esc_html_e( 'Completed', 'psp_projects' ); ?></span> </li>
										</ul>

										<div class="team-members">
											<?php psp_team_user_icons( $post->ID, 10 ); ?>
										</div>

									</article>
								</div>
							</div>

						<?php endwhile; ?>
					</div>

				<?php else: ?>

					<p class="psp-notice psp-notice-alert"><?php esc_html_e( 'No teams found.', 'psp_projects' ); ?></p>

				<?php endif; ?>

            </div>
        </div>
    </div>

<?php include( psp_template_hierarchy( 'dashboard/footer.php' ) );
