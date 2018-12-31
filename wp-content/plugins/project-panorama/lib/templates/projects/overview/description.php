<?php  $post_id = ( isset($post_id) ? $post_id : get_the_ID() );  ?>
<div id="psp-description" class="psp-col-md-7 psp-overview-col">
	<div class="summary">

		<h4><?php esc_html_e( 'Project Description', 'psp_projects' ); ?></h4>

		<?php
		do_action( 'before_project_description', $post_id );

		the_field( 'project_description', $post_id );

		do_action( 'after_project_description', $post_id ); ?>

	</div>
</div> <!--/#psp-description-->
