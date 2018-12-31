<?php
$task_class 	=	'task-item sub-task-item sub-task-item-' . $sub_task['ID'];
$task_class 	.= 	( $sub_task[ 'status' ] == 100 ? ' complete' : '' );
$assigned 		= 	$sub_task[ 'assigned' ];
$user			=	FALSE;
$sub_task['status']	=	( empty( $sub_task['status'] ) ? 0 : $sub_task['status'] );
$sub_task['status'] = 	apply_filters( 'psp_sub_task_status', $sub_task['status'], $post_id, $phase_id, $sub_task['ID'] );

if( ( !empty($assigned) ) && ( $assigned != 'unassigned' ) && ( $assigned != 'null' ) ) {
	$user	=	get_userdata( $assigned );
}

do_action( 'psp_before_task_entry', $post_id, $phase_id, $sub_task['ID'] ); ?>

<li class="<?php echo apply_filters( 'psp_task_classes', $task_class ); ?>" data-progress="<?php echo esc_attr($sub_task['status']); ?>">

	<?php do_action( 'psp_before_task_name', $post_id, $phase_id, $sub_task['ID'] ); ?>

	<strong><?php echo apply_filters( 'psp_sub_task_name', $sub_task['task'], $post_id, $phase_id, $sub_task['ID'] ); ?></strong>

	<?php do_action( 'psp_after_sub_task_name', $post_id, $phase_id, $sub_task['ID'] ); ?>

	<?php if($user) : ?>
		<b class="psp-assigned-to"><?php echo apply_filters( 'psp_sub_task_assigned', psp_username_by_id( $user->ID ), $post_id, $phase_id, $sub_task[ 'ID' ] ); ?></b>
	<?php endif; ?>

	<?php do_action( 'psp_after_task_assigned', $post_id, $phase_id, $sub_task['ID'] ); ?>

	<?php if(psp_can_edit_task( $post_id, $phase_id, $sub_task['ID'] )) { ?>

		<span class="psp-task-edit-links">

			<a href="#edit-task-<?php echo esc_attr($sub_task['ID']); ?>" class="task-edit-link"><b class="fa fa-pencil"></b> <?php _e('update','psp_projects'); ?></a>

			<?php $task_atts = apply_filters( 'psp_sub_task_attributes', array(
				'target'			=>	$sub_task['ID'],
				'task'				=>	$sub_task['ID'],
				'phase'				=>	$phase_id,
				'project'			=>	$post_id,
				'phase-auto'		=>	( $phase_auto[0] !== NULL ? $phase_auto[0] : 'No' ),
				'overall-auto'		=>	( $overall_auto[0] !== NULL ? $overall_auto[0] : 'No' ),
			), $post_id, $phase_id, $sub_task['ID'] ); ?>

			<a href="#" class="complete-task-link"
				<?php foreach( $task_atts as $att => $val ): ?>
					data-<?php echo $att; ?>="<?php echo esc_attr( $val ); ?>"
				<?php endforeach; ?>
				>
				<b class="fa fa-check"></b>
				<?php esc_html_e( 'complete', 'psp_projects' ); ?>
			</a>

		</span>

		<div id="edit-task-<?php echo $sub_task['ID']; ?>" class="task-select">

			<select id="edit-task-select-<?php echo $phase_id . '-' . $sub_task['ID']; ?>" class="edit-task-select">
				<option value="<?php echo esc_attr( $sub_task['status'] ); ?>"><?php echo $sub_task['status']; ?>%</option>
				<?php $values = apply_filters( 'psp_task_values', array(
					'0'		=>	'0%',
					'10'	=>	'10%',
					'15'	=>	'15%',
					'20'	=>	'20%',
					'25'	=>	'25%',
					'30'	=>	'30%',
					'35'	=>	'35%',
					'40'	=>	'40%',
					'45'	=>	'45%',
					'50'	=>	'50%',
					'55'	=>	'55%',
					'60'	=>	'60%',
					'65'	=>	'65%',
					'70'	=>	'70%',
					'75'	=>	'75%',
					'80'	=>	'80%',
					'85'	=>	'85%',
					'90'	=>	'90%',
					'95'	=>	'95%',
					'100'	=>	'100%'
				) );
				foreach( $values as $value => $label ): ?>
					<option value="<?php echo esc_attr( $value ); ?>"><?php echo esc_html( $label ); ?></option>
				<?php endforeach; ?>
			</select>

			<input type="submit" name="save" value="save" class="task-save-button"
				data-target="<?php echo $sub_task[ 'ID' ]; ?>"
				data-task="<?php echo $sub_task[ 'ID' ]; ?>"
				data-phase="<?php echo $phase_id; ?>"
				data-project="<?php echo $post_id; ?>"
				data-phase-auto="<?php echo $phase_auto[0]; ?>"
				data-overall-auto="<?php echo $overall_auto[0]; ?>">

        </div>
	<?php } ?>

	<?php do_action( 'psp_before_task_progress', $post_id, $phase_id, $sub_task[ 'ID' ] ); ?>

	<span><em class="status psp-<?php echo $sub_task[ 'status' ]; ?>"></em></span>

	<?php do_action( 'psp_after_task_progress', $post_id, $phase_id, $sub_task[ 'ID' ] ); ?>

</li>

<?php do_action( 'psp_after_task_entry', $post_id, $phase_id, $sub_task[ 'ID' ] ); ?>
