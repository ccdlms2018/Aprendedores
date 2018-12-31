<div class="psp-archive-section cf psp-my-tasks">

	<input id="psp-ajax-url" type="hidden" value="<?php echo admin_url(); ?>admin-ajax.php">
	<input id="psp-task-style" type="hidden" value="Yes">

	<h2 class="psp-box-title"><?php esc_html_e( 'Your Tasks', 'psp_projects' ); ?></h2>

	<div class="psp-grid-row psp-masonry">
		<?php
		$phases = get_field('phases');
		foreach( $tasks as $task ) {
			include( psp_template_hierarchy( 'dashboard/components/tasks/summary.php' ) );
		} ?>
	</div> <!--/.psp-grid-row-->

</div> <!--/.psp-archive-section.psp-my-tasks-->
