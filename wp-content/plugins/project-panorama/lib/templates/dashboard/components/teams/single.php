<?php
/**
 * Teams Listing Page
 *
 * Lists all the teams on the site that you have access to
 * @var post_type	psp_teams
 */
include( psp_template_hierarchy( 'dashboard/header.php' ) );
include( psp_template_hierarchy( 'global/header/navigation-sub' ) );
?>

<div id="psp-archive-container" class="psp-grid-container-fluid">

	<?php
    if( have_posts() ): while( have_posts() ): the_post(); global $post;

        if( psp_current_user_can_access_team( $post->ID ) ): ?>

            	<div id="psp-archive-content" class="psp-grid-row">

            		<div class="psp-grid-row">

                        <div class="psp-col-md-8">

                            <div class="psp-archive-section">

                                <h2 class="psp-box-title"><?php esc_html_e( 'Active Projects Assigned to', 'psp_projects' ); ?> <?php the_title(); ?></h2>

                                <?php
                                $projects = psp_get_team_projects( $post->ID );

                                echo psp_archive_project_listing( $projects );

                                wp_reset_postdata(); ?>

                            </div>

                        </div>

                        <?php include( psp_template_hierarchy( 'dashboard/components/teams/sidebar.php' ) ); ?>

                    </div> <!--/.psp-grid-row-->

                </div> <!--/#psp-archive-content-->

        <?php
        else:

            wp_die( __( 'You don\'t have access to this team.', 'psp_projects' ) );

        endif;

        endwhile; endif;

include( psp_template_hierarchy( 'dashboard/footer.php' ) );
