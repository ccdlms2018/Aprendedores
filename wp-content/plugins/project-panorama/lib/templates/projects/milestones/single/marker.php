<?php
$i			= 0;
$post_id 	= ( isset( $post_id ) ? $post_id : get_the_ID() );
$date 		= get_sub_field( 'date' ); ?>

<li class="<?php echo esc_attr( psp_milestone_marker_classes( $milestones, $completed ) ); ?>" data-milestone="<?php echo esc_attr($milestones[0]['occurs']); ?>">

	<b class="psp-milestone-dot"><?php echo esc_html_e( $milestones[0]['occurs'] . '%' ); ?></b>

	<?php do_action( 'psp_before_milestone_marker_text', $milestones, $post_id ); ?>

	<?php
	foreach( $milestones as $milestone ):

		$id 	= 'psp-milestone-' . $milestones[0]['occurs'] . '-' . $id;
		$class 	= 'psp-single-milestone ' . psp_late_class($milestone['date']); ?>

		<strong class="<?php echo esc_attr($class); ?>">

			<span class="psp-marker-title">
				<?php
				echo esc_html($milestone['title']);
				if( !empty($milestone['date']) && $milestone['date'] ) psp_the_milestone_due_date($milestone['date']); ?>
			</span>

			<?php if( !empty($milestone['description']) ): ?>
				<span class="psp-hide psp-milestone-description" id="<?php echo esc_attr($id); ?>">
					<?php echo wp_kses_post( do_shortcode($milestone['description']) ); ?>
				</span>
			<?php endif; ?>

		</strong>

	<?php $i++; endforeach; ?>

	<?php do_action( 'psp_after_milestone_marker_text', $milestones, $post_id ); ?>
</li>
