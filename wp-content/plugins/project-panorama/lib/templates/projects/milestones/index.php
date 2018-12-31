<?php
$post_id 		= isset( $post_id ) ? $post_id : get_the_ID();

$completed 		= psp_compute_progress( $post_id );
$all_milestones	= psp_organize_milestones( get_field( 'milestones', $post_id ) );

if( !empty( $all_milestones ) ): ?>

	<div class="<?php echo esc_attr($style); ?>">

		<hgroup class="psp-section-heading">
			<?php do_action( 'psp_before_milestone_title', $all_milestones, $completed, $post_id ); ?>
			<h2 class="psp-section-title"><?php esc_html_e( 'Milestones', 'psp_projects' ); ?></h2>
			<?php do_action( 'psp_after_milestone_title', $all_milestones, $completed, $post_id ); ?>
			<p class="psp-section-data"><?php echo esc_html($all_milestones['completed']); ?> / <?php echo esc_html( count( get_field('milestones', $post_id )) ) . ' ' . __( 'Completed', 'psp_projects' ); ?></p>
			<?php do_action( 'psp_after_milestone_data', $all_milestones, $completed, $post_id ); ?>
		</hgroup> <!--/.psp-section-heading-->

		<div class="psp-milestone-timeline">

			<p class="psp-progress"><span class="psp-<?php echo esc_attr($completed); ?>"><b><?php echo esc_html($completed); ?>%</b></span></p>

			<div class="psp-enhanced-milestones">
				<ul class="psp-milestone-dots">
					<?php foreach( $all_milestones['milestones'] as $milestones ) include( psp_template_hierarchy( 'projects/milestones/single/marker' ) ); ?>
				</ul> <!--/.psp-milestone-dots-->
			</div> <!--/.psp-enhanced-milestones-->

		</div> <!--/.psp-milestone-timeline-->

	</div>

<?php endif; ?>
