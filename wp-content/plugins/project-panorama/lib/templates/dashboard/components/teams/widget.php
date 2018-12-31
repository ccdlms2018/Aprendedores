<?php
$teams = psp_get_user_teams( $cuser->ID );
if( !empty( $teams ) ): ?>

    <?php do_action( 'psp_before_teams_dashboard_widget' ); ?>

        <div id="psp-dashboard-widget" class="psp-archive-section">

            <h2><?php echo esc_html( _n( 'Team', 'Teams', 'psp_projects' ) ); ?></h2>

            <ul class="psp-team-list">
                <?php foreach( $teams as $team ): ?>
                    <li>
                        <div class="psp-team-thumbnail">
                            <a href="<?php echo esc_url( get_the_permalink( $team ) ); ?>">
                                <?php psp_the_team_thumbnail( $team ); ?>
                            </a>
                        </div>
                        <div class="psp-team-description">
                            <a href="<?php echo esc_url( get_the_permalink( $team ) ); ?>">
                                <?php echo esc_html( get_the_title( $team ) ); ?>
                            </a>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>

        </div>

        <?php do_action( 'psp_after_teams_dashboard_widget' ); ?>

<?php endif; ?>
