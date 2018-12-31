<div class="psp-archive-project-list cf">
    <?php while($projects->have_posts()): $projects->the_post();

        global $post;
		
        $start_date = psp_text_date(get_field( 'start_date', $post->ID ));
        $end_date   = psp_text_date(get_field( 'end_date', $post->ID ));
        $priorities = psp_get_priorities_list();
        $priority = ( get_field('_psp_priority') ? get_field('_psp_priority') : 'normal' );
        $priority = $priorities[$priority]; ?>
        <div id="psp-archive-project-<?php echo esc_attr($post->ID); ?>" class="psp-archive-project" data-project="<?php the_title(); ?>" data-client="<?php the_field('client'); ?>" data-url="<?php the_permalink(); ?>">
           <div class="psp-row cf">
               <div class="psp-archive-project-title psp-col-md-12">

                   <hgroup>
                       <h3>
                           <?php the_title(); ?>
                           <?php if( get_field('client') ): ?>
                               <span class="psp-ali-client"><?php the_field('client'); ?></span>
                           <?php endif; ?>
                       </h3>
                       <p class="psp-archive-updated"><em><?php esc_html_e( 'Updated on ', 'psp_projects' ); echo esc_html(get_the_modified_date()); ?></em></p>
                   </hgroup>
				   
				    <?php if( $start_date || $end_date ): ?>
						<p>
							<?php if( $start_date ): ?>
								<strong><?php esc_html_e( 'Start', 'psp_projects' ); ?>:</strong>
								<?php echo esc_html($start_date); ?>
							<?php
							endif;
							if( $start_date && $end_date ) echo '<span class="psp-pipe">|</span>';
							if( $end_date ): ?>
								<strong><?php esc_html_e( 'End', 'psp_projects' ); ?>:</strong>
								<?php echo esc_html($end_date); ?>
							<?php endif; ?>
						</p>
					<?php endif; ?>
               </div>
           </div>
           <div class="psp-row cf">
               <div class="psp-col-md-12">
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
           </div>
           <?php do_action( 'psp_archive_project_listing_before_close', $post->ID ); ?>
		 </div>
    <?php endwhile; ?>
</div>

<p><?php echo get_next_posts_link( '&laquo; More Projects', $projects->max_num_pages ) . ' ' . get_previous_posts_link( 'Previous Projects &raquo;' ); ?></p>
