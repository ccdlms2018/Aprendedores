<?php do_action( 'psp_before_archive_project_listing' ); ?>

<div class="psp-archive-project-list cf">
    <?php
    while( $projects->have_posts() ): $projects->the_post(); global $post;

        $start_date = psp_text_date(get_field( 'start_date', $post->ID ));
        $end_date   = psp_text_date(get_field( 'end_date', $post->ID ));

        $priorities = psp_get_priorities_list();
        $priority = ( get_field('_psp_priority') ? get_field('_psp_priority') : 'normal' );
        $priority = $priorities[$priority];

        do_action( 'psp_archive_project_listing_before_row' ); ?>

        <div id="psp-archive-project-<?php echo esc_attr($post->ID); ?>" class="psp-archive-project" data-project="<?php the_title(); ?>" data-client="<?php the_field('client'); ?>" data-url="<?php the_permalink(); ?>">

            <?php do_action( 'psp_archive_project_listing_before_open', $post->ID ); ?>

            <div class="psp-row cf">
                <div class="psp-archive-project-title psp-col-md-12">

                    <?php if( current_user_can('see_priority_psp_projects') ): ?>
                        <span class="psp-priority psp-priority-<?php echo esc_attr($priority['slug']); ?>" data-placement="left" data-toggle="psp-tooltip" title="<?php echo esc_attr($priority['label']) . ' ' . esc_html( 'Priority', 'psp_projects' ); ?>" style="background-color: <?php echo $priority['color']; ?>"></span>
                    <?php endif; ?>

                    <?php do_action( 'psp_archive_project_listing_before_summary', $post->ID ); ?>

                    <hgroup>
                        <h3>
                            <a href="<?php the_permalink(); ?>">
                                <?php the_title(); ?>
                                <?php if( get_field('client') ): ?>
                                    <span class="psp-ali-client"><?php the_field('client'); ?></span>
                                <?php endif; ?>
                            </a>
                        </h3>
                        <p class="psp-archive-updated"><?php esc_html_e( 'Updated on ', 'psp_projects' ); echo esc_html(get_the_modified_date()); ?></p>
                    </hgroup>

                    <?php do_action( 'psp_archive_project_listing_after_summary', $post->ID ); ?>

                </div>
            </div>
            <div class="psp-row cf">
                <div class="psp-col-md-6 psp-col-sm-6">
                    <?php
                    do_action( 'psp_archive_project_listing_before_progress' );

                    $completed = psp_compute_progress($post->ID);
                    if( !$completed ) $completed = 0; ?>

                    <p class="psp-progress">
                        <span class="psp-<?php echo esc_attr($completed); ?>" data-toggle="psp-tooltip" data-placement="top" title="<?php echo esc_attr($completed . '% ' . __( 'Complete', 'psp_projects' ) ); ?>">
                            <b><?php echo esc_html($completed); ?>%</b>
                        </span>
                        <i class="psp-progress-label"> <?php esc_html_e('Progress','psp_projects'); ?> </i>
                    </p>

                    <?php
                    do_action( 'psp_archive_project_listing_before_timing' );

                    psp_the_simplified_timebar($post->ID);

                    do_action( 'psp_archive_project_listing_after_timing' );  ?>
                </div>
                <div class="psp-col-md-2 psp-col-sm-3 psp-col-xs-6 psp-archive-list-dates">
                    <?php if( $start_date ): ?>
                        <h5><?php esc_html_e( 'Start Date', 'psp_projects' ); ?></h5>
                        <p><?php echo esc_html($start_date); ?></p>
                    <?php endif; ?>
                </div>
                <div class="psp-col-md-2 psp-col-sm-3 psp-col-xs-6 psp-archive-list-dates">
                    <?php if( $end_date ): ?>
                        <h5><?php esc_html_e( 'End Date', 'psp_projects' ); ?></h5>
                        <p><?php echo esc_html($end_date); ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <?php do_action( 'psp_archive_project_listing_before_close', $post->ID ); ?>
        </div>
    <?php endwhile; ?>
</div>

<?php do_action( 'psp_after_archive_project_listing' ); ?>

<?php if( $projects->max_num_pages > 1 ): ?>

    <p class="psp-project-pager"><?php echo get_next_posts_link( '<span class="psp-ajax-more-projects pano-btn">&laquo; More Projects</span>', $projects->max_num_pages ) . ' ' . get_previous_posts_link( '<span class="psp-ajax-prev-projects pano-btn">Previous Projects &raquo;</span>' ); ?></p>

<?php endif; ?>
