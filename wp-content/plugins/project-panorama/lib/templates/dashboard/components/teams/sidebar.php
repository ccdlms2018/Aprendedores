<aside id="team-single-sidebar" class="psp-col-md-4">

    <aside class="thumbnail">

        <?php
        if( has_post_thumbnail() ):
            the_post_thumbnail( 'thumbnail' );
        else: ?>
            <img src="<?php echo PROJECT_PANARAMA_URI; ?>/assets/images/default-team.png" alt="<?php the_title(); ?>">
        <?php endif; ?>

    </aside>

    <?php if( get_field( 'description' ) ): ?>

        <div class="psp-archive-widget psp-widget">

            <h2 class="psp-widget-title"><?php the_title(); ?></h2>

            <?php the_field( 'description' ); ?>

        </div>

    <?php endif; ?>

    <?php $members = psp_get_team_members( $post->ID ); ?>

    <div class="psp-archive-widget psp-widget">

        <h2 class="psp-widget-title"><?php esc_html_e( 'Members', 'psp_projects' ); ?></h2>

        <?php if( $members ): ?>
            <ul class="psp-team-member-list">
                <?php foreach( $members as $user ): ?>
                    <li>
                        <?php echo $user[ 'user_avatar' ]; ?>
                        <strong><?php echo psp_get_nice_username_by_id( $user[ 'ID' ] ); ?></strong>
                        <span class="psp-last-login"><?php echo psp_verbose_login( $user[ 'ID' ] ); ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p><?php esc_html_e( 'No users assigned to this team.', 'psp_projects' ); ?></p>
        <?php endif; ?>

    </div>

</aside>
