<?php do_action( 'psp_dashboard_before_my_projects' ); ?>

<div class="psp-archive-section">

	<?php do_action( 'psp_dashboard_my_projects_before_heading' ); ?>

	<div class="psp-table-header cf">
		<ul class="psp-sub-nav psp-pull-left">
			<?php
			do_action( 'psp_dashboard_my_projects_before_nav' );

			$active_link 	= get_post_type_archive_link('psp_projects') . ( get_option( 'permalink_structure' ) ? 'status/active' : '&psp_status_page=active' );
			$completed_link = get_post_type_archive_link('psp_projects') . ( get_option( 'permalink_structure' ) ? 'status/completed' : '&psp_status_page=completed' ); ?>
			<li><a href="<?php echo esc_url($active_link); ?>" class="psp-archive-link <?php if( ( get_query_var( 'psp_status_page' ) == 'active' ) || ( !get_query_var( 'psp_status_page' ) ) ) { echo 'active'; } ?>"><?php esc_html_e( 'Active Projects', 'psp_projects' ); ?></a></li>
			<li><a href="<?php echo esc_url($completed_link); ?>" class="psp-archive-link <?php if( get_query_var( 'psp_status_page' ) == 'completed' ) { echo 'active'; } ?>"><?php esc_html_e( 'Completed Projects', 'psp_projects' ); ?></a></li>
			<?php do_action( 'psp_dashboard_my_projects_after_nav' ); ?>
		</ul>
		<?php include( psp_template_hierarchy( 'global/search-form.php' ) ); ?>
	</div>

	<?php do_action( 'psp_dashboardmy_projects_after_heading' ); ?>

	<div class="psp-archive-list-wrapper">
		<?php echo psp_archive_project_listing( $projects ); ?>
	</div>

</div>

<?php do_action( 'psp_dashboard_after_my_projects' ); ?>
