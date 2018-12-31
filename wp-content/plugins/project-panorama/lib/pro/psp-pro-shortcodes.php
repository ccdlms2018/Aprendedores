<?php
/**
 * Description of psp-pro-shortcodes
 *
 * Shortcodes related to paid version of Project Panorama
 *
 * @package psp-projects
 *
 */

/**
 * psp_project_part()
 *
 * Embed a portion of a project via shortcode
 *
 *
 * @param $atts (array)
 * @param $atts['id'] (int) Post ID
 * @param $atts['display'] (string) Use the shortcode wrapper
 *
 * @return markup
 *
 */

add_shortcode( 'project_status_part' , 'psp_project_part_shortcode' );
function psp_project_part_shortcode( $atts ) {

        // Don't allow embeds on projects
        if( get_post_type() == 'psp_projects' )
            return;

        psp_front_assets(true);

        extract( shortcode_atts(
			array(
				'id'	 	=> '',
				'display' 	=> '',
				'style'	 	=> ''
			), $atts )
		);


		$project = new WP_Query( array( 'p' => $id, 'post_type' => 'psp_projects' ) );

		// Check to see if a post is returned

		if( $project->have_posts() ) {

			while( $project->have_posts() ) { $project->the_post();

				$output = '<div id="psp-projects" class="psp-part-project psp-theme-template psp-shortcode">';

				if( $display == 'overview' ) {

					$output .= do_action( 'psp_before_essentials' ) . psp_essentials( $id, 'psp-shortcode', 'none' ) . do_action( 'psp_after_essentials' );

				} elseif ( $display == 'documents' ) {

					$output .= '<div id="psp-essentials" class="psp-shortcode"><div id="psp-documents"><div id="psp-documents-list">' . psp_documents( $id, 'shortcode' ) . '</div></div></div>';

				} elseif ( $display == 'progress' ) {


					$output .= do_action( 'psp_before_progress' ) . psp_total_progress( $id, 'psp-shortcode', $style ) . do_action( 'psp_after_progress' );

				} elseif ( $display == 'phases' ) {

					$output .= do_action( 'psp_before_phases' ) . '<div id="psp-phases">' . psp_phases( $id, 'psp-shortcode', $style ) . '</div>' . do_action( 'psp_after_phases' );

				} elseif ( $display == 'tasks' ) {

					$output .= psp_task_table( $id, 'psp-shortcode', $style );

				}

				$output .= '</div>';

			}

            wp_reset_query();

            return $output;

		} else {

			return '<p>'.__('No project with that ID','psp_projects').'</p>';

		}

}

/**
 * psp_single_project()
 *
 * Embed an entire project via shortcode
 *
 *
 * @param $atts['id] (int) : Post ID
 * @param $atts['overview'] (string) : If 'yes' include the project overview
 * @param $atts['progress'] (string) : If 'yes' include the project overall progress
 * @param $atts['milestones'] (string) : If 'yes' include the project milestones
 * @param $atts['phases'] (string) : If 'yes' include the project phases
 * @param $atts['tasks'] (string) : If 'yes' include the project tasks
 *
 * @return markup
 *
 */

add_shortcode( 'project_status', 'psp_single_project_shortcode' );
function psp_single_project_shortcode( $atts ) {

		psp_front_assets(1);

		extract( shortcode_atts(
			array(
				'id' 			=> '',
				'overview' 		=> 'yes',
				'progress' 		=> 'yes',
				'milestones'	=> 'condensed',
				'phases' 		=> 'yes',
				'tasks' 		=> 'yes'
			), $atts )
		);

		$project 			= new WP_Query( array( 'p' => $id, 'post_type'	=> 'psp_projects' ) );
		$panorama_access 	= panorama_check_access( $id );

		if( $project->have_posts() ) {

			ob_start();

			// Make sure the user has access to this project

			if( $panorama_access ) { ?>

					<?php while( $project->have_posts() ) { $project->the_post(); ?>

					<div id="psp-projects" class="psp-single-project psp-reset psp-theme-template psp-shortcode">

						<?php

						global $post;

						// Is the overview to be displayed?

						if($overview == 'yes') {

							echo do_action( 'psp_before_essentials' ) . psp_essentials( $id, 'psp-shortcode' ) . do_action( 'psp_after_essentials' );

						}

						if($progress == 'yes') {

							echo do_action( 'psp_before_progress' ) . psp_total_progress($id,'psp-shortcode',$milestones) . do_action( 'psp_after_progress' );

						}

						if($phases == 'yes') { ?>

								<?php do_action( 'psp_between_progress_phases' ); ?>

								<?php do_action( 'psp_before_phases' ); ?>

								<div class="single-project-phases">

									<h2><?php _e( 'Project Phases', 'psp_projects' ); ?></h2>

									<?php echo psp_phases( $id, 'psp-shortcode', $tasks ); ?>

								</div>

								<?php do_action( 'psp_after_phases' ); ?>

						<?php } ?>

						</div>

					<?php }


					?>

			<?php } else { ?>

			<?php // TODO: This should be a global element ?>
			<div id="psp-projects" class="psp-shortcode">
				<div id="psp-login">
					<?php if(( ! $panorama_access ) && (get_field('restrict_access_to_specific_users',$id))): ?>
						<h2><?php _e('This Project Requires a Login','psp_projects'); ?></h2>
						<?php if(!is_user_logged_in()) {
							echo panorama_login_form();
						} else {
							echo "<p>".__('You don\'t have permission to access this project','psp_projects')."</p>";
						}
						?>
					<?php endif; ?>
					<?php if((post_password_required()) && (!current_user_can('manage_options'))): ?>
						<h2><?php _e('This Project is Password Protected','psp_projects'); ?></h2>
						<?php echo get_the_password_form(); ?>
					<?php endif; ?>
				</div>
			</div>
			<?php }

			return ob_get_clean();

			wp_reset_querydata();

		} else {

			return '<p>'.__('No project with that ID','psp_projects').'</p>';

		}

	}

/**
 * psp_single_project_admin_dialog_modal()
 *
 * Output the markup and javascript for managing the single project embed WYSIWYG button
 *
 */

add_action( 'admin_footer-post.php', 'psp_single_project_admin_dialog_modal' ); // Fired on the page with the posts table
add_action( 'admin_footer-edit.php', 'psp_single_project_admin_dialog_modal' ); // Fired on the page with the posts table
add_action( 'admin_footer-post-new.php', 'psp_single_project_admin_dialog_modal' ); // Fired on the page with the posts table

function psp_single_project_admin_dialog_modal() {

		$output = '

			<style type="text/css">
				#TB_Window { z-index: 9000 !important; }
			</style>

			<script>

				function psp_full_project() {

					jQuery("#psp-full-project-table").show();
					jQuery("#psp-part-project-table").hide();

				}

				function psp_part_project() {

					jQuery("#psp-full-project-table").hide();
					jQuery("#psp-part-project-table").show();


				}

				function psp_part_change() {

					target = jQuery("#psp-part-display").val();
					jQuery("tr.psp-part-option-row").hide();
					jQuery("#psp-part-" + target + "-option").show();

				}

				function psp_single_phases_select() {

					target = jQuery("#psp-single-phases").val();

					if(target == "yes") {
						jQuery(".psp-single-tasks-row").show();
					} else {
						jQuery(".psp-single-tasks-row").hide();
					}


				}

				jQuery(document).ready(function() {

					jQuery("#psp-full-project").attr("checked",false);
					jQuery("#psp-part-project").attr("checked",false);

					psp_single_phases_select();
					psp_part_change();

				});

			</script>
		';

		$output .= '<div class="psp-dialog" style="display:none">';
		$output .= '<div id="psp-single-project-diaglog">';
		$output .= '<h3>'.__('Insert a Project Overview','psp_projects').'</h3>';
		$output .= '<p>'.__('Select a project below to add it to your post or page.','psp_projects').'</p>';
		$output .= '<table class="form-table">';
		$output .= '<tr><th>Project</th><td>';
		$output .= '<div class="psp-loading"></div>';
		$output .= '<div id="psp-single-project-list"></div>';
		$output .= '</td></tr>';
		$output .= '<tr><th><label for="psp-display-style">Style</label></th><td><label for="psp-display-style"><input unchecked type="radio" name="psp-display-style" onClick="psp_full_project();" id="psp-full-project" value="full"> ' . __( 'Full Project', 'psp_projects' ) . '</label>&nbsp;&nbsp;&nbsp;<label for="psp-display-style"><input type="radio" unchecked name="psp-display-style" onClick="psp_part_project()" id="psp-part-project" value="part"> '.__('Portion of Project','psp_projects').'</label></td></tr>';
		$output .= '</table>';

		$output .= '<table class="form-table psp-hide-table" id="psp-full-project-table">';

		$output .= '<tr>
						<th><label for="psp-single-overview">'.__('Overview','psp_projects').'</label></th>
						<td>
							<select id="psp-single-overview">
								<option value="yes">'.__('Show Overview','psp_projects').'</option>
								<option value="no">'.__('No Overview','psp_projects').'</option>
							</select>
						</td>
					</tr>';

		$output .= '<tr>
						<th><label for="psp-single-progress">'.__('Progress Bar','psp_projects').'</label></th>
						<td>
							<select id="psp-single-progress">
								<option value="yes">'.__('Show Progress Bar','psp_projects').'</option>
								<option value="no">'.__('No Progress Bar','psp_projects').'</option>
							</select>
						</td>
					</tr>';

		$output .= '<tr>
						<th><label for="psp-single-milestones">'.__('Milestones','psp_projects').'</label></th>
						<td>
							<select id="psp-single-milestones">
								<option value="condensed">'.__('Condensed','psp_projects').'</option>
								<option value="full">'.__('Full Width','psp_projects').'</option>
								<option value="no">'.__('No Milestones','psp_projects').'</option>
							</select>
						</td>
					</tr>';

		$output .= '<tr>
						<th><label for="psp-single-phases">Phases:</label></td>
						<td>
							<select id="psp-single-phases" onChange="psp_single_phases_select();">
								<option value="yes">'.__('Show Phases','psp_projects').'</option>
								<option value="no">'.__('No Phases','psp_projects').'</option>
							</select>
						</td>
					</tr>';

		$output .= '<tr class="psp-single-tasks-row">
						<th><label for="psp-single-tasks">'.__('Tasks','psp_projects').'</label></th>
						<td>
							<select id="psp-single-tasks">
								<option value="yes">'.__('All Tasks','psp_projects').'</option>
								<option value="incomplete">'.__('Incomplete Only','psp_projects').'</option>
								<option value="complete">'.__('Completed Only','psp_projects').'</option>
								<option value="no">'.__('No Tasks','psp_projects').'</option>
							</select>
						</td>
						<td colspan="2">&nbsp;</td>
					</tr>
				</table>';

		$output .= '<table class="form-table psp-hide-table" id="psp-part-project-table">
						<tr>
							<th><label for="psp-part-display">'.__('Display','psp_projects').'</label></th>
							<td>
								<select id="psp-part-display" onChange="psp_part_change();">
									<option value="overview">' . __( 'Overview','psp_projects').'</option>
									<option value="documents">' . __( 'Documents','psp_projects').'</option>
									<option value="progress">' . __( 'Overall Progress','psp_projects').'</option>
									<option value="phases">' . __( 'Phases','psp_projects').'</option>
									<option value="tasks">' . __( 'Tasks','psp_projects').'</option>
								</select>
							</td>
						</tr>
						<tr id="psp-part-progress-option" class="psp-part-option-row">
							<th><label for="psp-part-overview-progress-select">'.__('Milestones','psp_projects').'</label></th>
							<td><select id="psp-part-overview-progress-select">
									<option value="full">'.__('Full Width','psp_projects').'</option>
									<option value="condensed">'.__('Condensed','psp_projects').'</option>
									<option value="no">'.__('None','psp_projects').'</option>
								</select>
							</td>
						</tr>
						<tr id="psp-part-phases-option" class="psp-part-option-row">
							<th><label for="psp-part-phases-select">'.__('Tasks','psp_projects').'</label></th>
							<td><select id="psp-part-phases-select">
									<option value="all">'.__('All Tasks','psp_projects').'</option>
									<option value="complete">'.__('Completed Tasks','psp_projects').'</option>
									<option value="incomplete">'.__('Incomplete Tasks','psp_projects').'</option>
									<option value="no">'.__('None','psp_projects').'</option>
								</select>
							</td>
						</tr>
						<tr id="psp-part-tasks-option" class="psp-part-option-row">
							<th><label for="psp-part-tasks-select">'.__('Show','psp_projects').'</label></th>
							<td><select id="psp-part-tasks-select">
									<option value="tasks">'.__('All Tasks','psp_projects').'</option>
									<option value="completed">'.__('Completed','psp_projects').'</option>
									<option value="open">'.__('Incomplete','psp_projects').'</option>
								</select>
							</td>
						</tr>
					</table>
			';


		$output .= '<p><input class="button-primary" type="button" onclick="InsertPspProject();" value="'.__('Insert Project','psp_projects').'"> <a class="button" onclick="tb_remove(); return false;" href="#">'.__('Cancel','psp_projects').'</a></p>';
		$output .= '</div></div>';

		echo $output;

	}

/**
 * psp_ajax_project_list()
 *
 * Dynamically populate the project list for embed modals to prevent slow admin experience
 *
 */

add_action( 'wp_ajax_psp_get_projects', 'psp_ajax_get_project_list' );
function psp_ajax_get_project_list() { ?>

	<?php
	$args = array(
		'post_type'			=>		'psp_projects',
		'posts_per_page'	=>		-1,
	);

	$projects = new WP_Query( $args ); ?>

	<p>
		<select id="psp-single-project-id">

			<?php while( $projects->have_posts() ) { $projects->the_post(); global $post; ?>

				<option value="<?php echo $post->ID; ?>"><?php the_title(); ?></option>

			<?php } ?>

		</select>
	</p>

<?php }


add_action( 'admin_footer-post.php', 'psp_project_listing_dialog' ); // Fired on the page with the posts table
add_action( 'admin_footer-edit.php', 'psp_project_listing_dialog' ); // Fired on the page with the posts table
add_action( 'admin_footer-post-new.php', 'psp_project_listing_dialog' ); // Fired on the page with the posts table


/**
 * psp_populate_user_dashboard_widget()
 *
 * Create the dashboard widget with project completion breakdown and recently updated projects
 * Also usable with shortcode [user-dashboard-widget]
 *
 */

add_shortcode( 'user-dashboard-widget', 'psp_populate_user_dashboard_widget' );
function psp_populate_user_dashboard_widget() {

	psp_front_assets(1);

	$cuser 			= wp_get_current_user();
	$cid 			= $cuser->ID;
	$projects 		= get_posts( array(
								'post_type' 		=> 'psp_projects',
								'posts_per_page' 	=> '-1',
								'meta_query' 		=> psp_access_meta_query( $cid ),
	                			)
	            			);

	$total_projects 	= count( $projects );
	$taxonomies 		= get_terms( 'psp_tax' , 'fields=count' );
	$completed_projects = 0;
	$not_started 		= 0;
	$active 			= 0;

	foreach( $projects as $project ) {

		if( get_post_meta( $project->ID, '_psp_completed', true ) == '1' ) {
			$completed_projects++;
		}

		if( psp_compute_progress( $project->ID ) == 0) {
			$not_started++;
		} else {
			$active++;
		}

	}

	// Calculate percentage of complete projects
	if( $completed_projects > 0 && $total_projects > 0 ) {
		$percent_complete = floor( $completed_projects / $total_projects * 100 );
	} else {
		$percent_complete = 0;
	}

	// Calculate percentage of projects that haven't been started yet
    if( $not_started > 0 && $total_projects > 0 ) {
        $percent_not_started = floor( $not_started / $total_projects * 100 );
    } else {
        $percent_not_started = 0;
    }

	 // Calculate percent remaining
	 if( $percent_complete > 0 && $total_projects > 0 ) {
		 $percent_remaining = 100 - $percent_complete - $percent_not_started;
	} else {
		$percent_remaining = 100;
	}

	ob_start(); ?>

		<div class="psp-chart">
			<canvas id="psp-dashboard-chart" width="100%" height="150"></canvas>
		</div>

		<script>

	        jQuery(document).ready(function() {

				var chartOptions = {
					responsive: true
				}

	            var data = [
	                {
	                    value: <?php echo $percent_complete; ?>,
	                    color: "#2a3542",
	                    label: "Completed"
	                },
	                {
	                    value: <?php echo $percent_remaining; ?>,
	                    color: "#3299bb",
	                    label: "In Progress"
	                },
	                {
	                    value: <?php echo $percent_not_started; ?>,
	                    color: "#666666",
	                    label: "Not Started"
	                }
	            ];


	            var psp_dashboard_chart = document.getElementById("psp-dashboard-chart").getContext("2d");

	            new Chart(psp_dashboard_chart).Doughnut(data,chartOptions);

	        });

		</script>

    	 <ul class="psp-projects-overview">
			<li><span class="psp-dw-projects"><?php esc_html_e($total_projects); ?></span> <strong><?php _e( 'Projects', 'psp_projects' ); ?></strong> </li>
			<li><span class="psp-dw-completed"><?php esc_html_e($completed_projects); ?></span> <strong><?php _e( 'Completed', 'psp_projects' ); ?></strong></li>
			<li><span class="psp-dw-active"><?php esc_html_e($active); ?></span> <strong><?php _e( 'Active', 'psp_projects' ); ?></strong></li>
			<li><span class="psp-dw-types"><?php esc_html_e($taxonomies); ?></span> <strong><?php _e( 'Types', 'psp_projects' ); ?></strong></li>
    	  </ul>

		<?php
	    return ob_get_clean();

}


add_shortcode('task-list','psp_output_my_tasks_shortcode');
function psp_output_my_tasks_shortcode( $atts ) {

    extract( shortcode_atts(
		array(
			'columns' => 'no'
		), $atts )
	);

	return '<div id="psp-projects" class="psp-theme-template">' . psp_output_my_tasks( $columns ) . '</div>';

}


function psp_output_my_tasks($columns = 'true') {

	// Make sure the user is logged in first
	if( is_user_logged_in() ):

		// Get the current logged in WordPress user object
		$cuser = wp_get_current_user();

		// Query all the projects where this user has been assigned a task
		$args = array(
			'post_type'		=>		'psp_projects',
			'limit'			=>		-1,
			'tax_query' 	=> array(
					array(
						'taxonomy'	=>	'psp_status',
						'field'		=>	'slug',
						'terms'		=>	'completed',
						'operator'	=>	'NOT IN'
					)
			),
            'meta_query' 	=> array(
                'key' 		=> 'tasks_%_assigned',
                'value' 	=> $cuser->ID,
            )
		);

		// Query with the above arguments
		$projects = new WP_Query($args);

			if($projects->have_posts()):

		        psp_front_assets(1);

    			ob_start(); ?>

    			<div class="psp-my-tasks">

    				<input id="psp-ajax-url" type="hidden" value="<?php echo admin_url(); ?>admin-ajax.php">

    				<?php
    				while($projects->have_posts()): $projects->the_post();

    					global $post;
    					$phases 		= NULL;
    					$task_id 		= 0;
    					$phase_count 	= 0;

    					// Loop through the phases
    					while(have_rows('phases')): the_row();

    						$phase_name = get_sub_field('title');
    						$tasks 		= NULL;

    						// Loop through the tasks
    						while(have_rows('tasks')): the_row();

    							$assigned = get_sub_field('assigned');

    							if($assigned == $cuser->ID) {

    								$overall_auto = get_field('automatic_progress',$post->ID);

    $link = '<a href="#edit-task-'.$task_id.'" class="task-edit-link"><b class="fa fa-pencil"></b> '.__('update','psp_projects').'</a> <a href="#" class="complete-task-link" data-target="'.$task_id.'" data-task="'.$task_id.'" data-task="'.$task_id.'" data-phase="'.$phase_count.'" data-project="'.$post->ID.'" data-phase-auto="null" data-overall-auto="'.$overall_auto[0].'"><b class="fa fa-check"></b> '.__('complete','psp_projects').'</a></strong>';

    								$tasks .= '<li class="task-item task-item-'.$task_id.'">'.get_sub_field('task').' '.$link.' <span class="psp-task-bar" data-progress="'.get_sub_field('status').'"><em class="status psp-'.get_sub_field('status').'"></em></span>';

    								$tasks .= '<div id="edit-task-'.$task_id.'" class="task-select">

                                    <select id="edit-task-select-'.$phase_count.'-'.$task_id.'" class="edit-task-select">
                                        <option value="'.get_sub_field('status',$post->ID).'">'.get_sub_field('status',$post->ID).'%</option>
                                        <option value="0">0%</option>
                                        <option value="10">10%</option>
                                        <option value="20">20%</option>
                                        <option value="30">30%</option>
                                        <option value="40">40%</option>
                                        <option value="50">50%</option>
                                        <option value="60">60%</option>
                                        <option value="70">70%</option>
                                        <option value="80">80%</option>
                                        <option value="90">90%</option>
                                        <option value="100">100%</option>
                                    </select>

    								<input type="submit" name="save" value="save" class="task-save-button" data-task="'.$task_id.'" data-phase="'.$phase_count.'" data-project="'.$post->ID.'" data-phase-auto="null" data-overall-auto="'.$overall_auto[0].'" data-userid="'.$cuser->ID.'">

                                	</div>
    							</li>';

    							}

    							$task_id++;

    						endwhile;

    						if(!empty($tasks)):
    							$phases .= '<div class="psp-tasks-phase">
    											<h3>'.$phase_name.'</h3>
    										</div>
    										<ul>
    											'.$tasks.'
    										</ul>';
						    endif;

					endwhile; // End phases loop

					if(!empty($phases)): ?>

						<?php $phases_and_tasks = psp_get_item_count($post,$cuser->ID);
						global $post; ?>

						<div class="psp-task-project <?php if($columns != 'no') { echo 'psp-col-lg-4 psp-col-md-6'; } ?> psp-task-project-<?php echo $post->ID; ?>">
							<div class="psp-task-project-wrapper">

								<h2 class="psp-task-project-title">
									<a href="<?php the_permalink(); ?>"><b><?php the_title(); ?></b> <strong><?php the_field('client'); ?></strong></a>
								</h2>

								<div class="psp-task-project-details">

									<h4><?php _e('Project Details','psp_projects'); ?></h4>

									<div class="psp-task-section">

										<p class="psp-progress"><span class="psp-<?php echo psp_compute_progress($post->ID); ?>"><b><?php echo psp_compute_progress($post->ID); ?>%</b></span></p>

										<?php echo psp_the_timebar($post->ID); ?>

									</div> <!--/.psp-tpd-content-->

								</div> <!--/.psp-task-project-details-->

								<div class="psp-task-project-tasks">

									<div class="psp-task-section">

										<ul class="psp-grid-row cf">
											<li class="psp-col-xs-4 psp-element-tally psp-element-tally-all" data-count="<?php echo $phases_and_tasks['tasks']; ?>">
												<strong><?php echo $phases_and_tasks['tasks']; ?></strong>
												<span><?php _e('Tasks<br>Assigned','psp_projects'); ?></span>
											</li>
											<li class="psp-col-xs-4 psp-element-tally psp-element-tally-started" data-count="<?php echo $phases_and_tasks['started']; ?>">
												<strong><?php echo $phases_and_tasks['started']; ?></strong>
												<span><?php _e('Tasks<br>Started','psp_projects'); ?></span>
											</li>
											<li class="psp-col-xs-4 psp-element-tally psp-element-tally-completed" data-count="<?php echo $phases_and_tasks['completed']; ?>">
												<strong><?php echo $phases_and_tasks['completed']; ?></strong>
												<span><?php _e('Tasks<br>Complete','psp_projects'); ?></span>
											</li>
										</ul>


										<?php echo $phases; ?>

									</div>

								</div> <!--/.psp-task-project-tasks-->

							</div>
						</div>

					<?php endif;

				// End query loop
				endwhile; ?>

			</div>

		<?php endif; ?>

	<?php endif;


	return ob_get_clean();

}

add_shortcode( 'before-phase', 'psp_before_phase' );
function psp_before_phase( $atts, $content = NULL ) {

	return '<div class="psp-before-phase">' . $content . '</div>';

}

add_shortcode( 'during-phase', 'psp_during_phase' );
function psp_during_phase( $atts, $content = NULL ) {

	return '<div class="psp-during-phase">' . $content . '</div>';

}

add_shortcode( 'after-phase', 'psp_after_phase' );
function psp_after_phase( $atts, $content = NULL ) {

	return '<div class="psp-after-phase">' . $content . '</div>';

}

add_shortcode( 'before-milestone', 'psp_before_milestone_shortcode' );
function psp_before_milestone_shortcode( $atts, $content = NULL ) {

    return '<div class="psp-before-milestone">' . wpautop( $content ) . '</div>';

}

add_shortcode( 'after-milestone', 'psp_after_milestone_shortcode' );
function psp_after_milestone_shortcode( $atts, $content = NULL ) {

    return '<div class="psp-after-milestone">' . wpautop( $content ) . '</div>';

}

add_shortcode( 'psp-upcoming-tasks', 'psp_all_my_tasks_shortcode' );
function psp_all_my_tasks_shortcode( $atts, $shortcode = TRUE ) {

    /*
     * Setup defaults
     */

    $cuser      = wp_get_current_user();
    $user_id    = ( !empty( $atts[ 'id' ] ) ? $atts[ 'id' ] : $cuser->ID );
    $amount     = ( !empty( $atts[ 'count' ] ) ? $atts[ 'count' ] : 10 );
    $tasks      = array();

    global $post;
    $shortcode = ( has_shortcode( $post->post_content, 'psp-upcoming-tasks' ) ? true : false );

    $class = 'psp-table psp-task-table ' . ( $shortcode ? 'is-shortcode' : 'not-shortcode' );

    psp_front_assets( 1 );

    ob_start();

	// Gets all Assigned Tasks. Each index is a new Project
	// Similarly to the Dashboard though, this is only used for determining which Projects we have Assigned Tasks in.
	// We have to use psp_get_tasks() in order to get some more data on them
    $tasks_query = psp_get_all_my_tasks();

    if ( $tasks_query ) {

        foreach ( $tasks_query as $task_group ) {

			$phases = array();
			$phase_id = 0;

            // Skip any unpublished projects
            if ( get_post_status( $task_group['project_id'] ) != 'publish' ) continue;

			$phases	= get_field( 'phases', $task_group['project_id'] );

			while ( have_rows( 'phases', $task_group['project_id'] ) ) {

				$phase = the_row();
				$get_tasks = psp_get_tasks( $task_group['project_id'], $phase_id, 'incomplete' );

				if ( ! empty( $get_tasks ) ):

					foreach ( $get_tasks as $task ) {

						$show_task = $task[ 'assigned' ] == $cuser->ID;
						$show_task = apply_filters( 'psp_show_task_on_user_tasks', $show_task, $phase, $task );

						if ( $show_task ) {

							$tasks[] = array(
								'name'      =>  $task['task'],
								'ID'        =>  $task['ID'], // Legacy, is actually an Index
								'task_id'	=>	$task['task_id'],
								'phase_id'  =>  $phase_id,
								'post_id'   =>  $task_group['project_id'],
								'phases'    =>  $phases,
								'phase'     =>  $phase['title'],
								'due_date'  =>  $task[ 'due_date' ],
								'status'    =>  $task[ 'status' ],
								'assigned'	=>	$task['assigned'],
							);

						}

					}

				endif;

				$phase_id++;

			}

        }

        $tasks = array_intersect_key( $tasks, array_unique( array_map( 'serialize', $tasks ) ) );

        usort( $tasks, 'psp_sort_tasks_by_due_date' );

        // $tasks = array_reverse( $tasks ); ?>
        <?php if( $shortcode == TRUE ): ?>
            <div id="psp-projects" class="psp-shortcode">
        <?php endif; ?>

            <input type="hidden" id="psp-ajax-url" value="<?php echo admin_url(); ?>admin-ajax.php">

            <?php
            $table_headings = apply_filters( 'psp_all_my_tasks_table_headings', array(
                __( 'Task', 'psp_projects' ),
                __( '', 'psp_projects' ),
                __( 'Due Date', 'psp_projects' ),
            ) ); ?>

            <table class="<?php echo esc_attr($class); ?>">
                <?php if( $shortcode ): ?>
                    <thead>
                        <tr>
                            <?php foreach( $table_headings as $heading ): ?>
                                <th><?php echo esc_html( $heading ); ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                <?php endif; ?>
                <tbody>
                    <?php
                    if( empty( $tasks ) ): ?>
                        <tr>
                            <td rowspan="2"><strong><?php esc_html_e( 'There are no tasks assigned to you at this time.','psp_projects' ); ?></strong></td>
                        </tr>
                    <?php else:
                        foreach( $tasks as $task ):

                            $link_attributes = array(
                                'target'    =>  $task['ID'],
                                'task'      =>  $task['ID'],
                                'phase'     =>  $task['phase_id'],
                                'project'   =>  $task['post_id'],
                            );

							$task_class = 'task-row project-' . esc_attr( $task['post_id'] ) . '-task-' . esc_attr( $task['ID'] ) . ' task-row-' . esc_attr( $task[ 'ID' ] );

							$phases = get_field( 'phases', $task['post_id'] ); ?>

                            <tr class="<?php echo esc_attr( apply_filters( 'psp_task_classes', $task_class, $task['post_id'], $task['phase_id'], $task['ID'], $phases, $phases[ $task['phase_id'] ] ) ); ?>" data-task_index="<?php echo esc_attr( $task['ID'] ); ?>" data-task_id="<?php echo esc_attr( $task['task_id'] ); ?>" data-phase_id="<?php echo esc_attr( $phase['phase_id'] ); ?>" data-progress="<?php echo $task[ 'status' ]; ?>">
                                <td class="psp-task-table-complete-link">

                                    <?php if( psp_can_edit_task( $task['post_id'], $task['phase_id'], $task['ID'] ) ): ?>
                                        <a href="#" class="complete-task-link task-table"
                                            <?php foreach( $link_attributes as $attribute => $value ): ?>
                                                data-<?php echo $attribute; ?>="<?php echo esc_attr( $value ); ?>"
                                            <?php endforeach; ?> >
                                            <b class="fa fa-check"></b>
                                        </a>
                                    <?php endif; ?>

                                </td>
                                <td class="psp-task-table-body">

									<?php

									$date 	= strtotime( $task[ 'due_date' ] );
									$format = get_option( 'date_format' );

									$task_panel_atts = apply_filters( 'psp_task_panel_your_tasks_attributes', array(
										'task_index'		=>	$task['ID'],
										'task_id'			=>  $task['task_id'],
										'phase_index'		=>	$task['phase_id'],
										'phase_id'			=>  $phase['phase_id'],
										'project'			=>	$task['post_id'],
										'project_name'		=>  get_the_title( $task['post_id'] ),
										'due_date'			=>	date_i18n( $format, $date ),
										'assigned_name'		=>	psp_username_by_id( $task['assigned'] ),
										'project_permalink' =>	get_permalink( $task['post_id'] ),
									), $task['post_id'], $task['phase_id'], $task['ID'] );

									$task_documents = psp_parse_task_documents( get_field( 'documents', $task['post_id'] ), $task['task_id'] );
									$task_document_count = ( $task_documents ) ? count( $task_documents ) : 0;

									$task_comment_count = psp_get_task_comment_count( $task['task_id'], $task['post_id'] );

									?>

                                    <div class="psp-row">
                                        <div class="psp-task-title psp-col-md-12">
                                            <a class="psp-task-table-link psp-task-title" href="#psp-open-task-panel"
											<?php foreach( $task_panel_atts as $att => $val ): ?>
												data-<?php echo $att; ?>="<?php echo esc_attr( $val ); ?>"
											<?php endforeach; ?>
												>
                                                <strong><?php echo wp_kses_post( $task['name'] ); ?></strong>
											</a>
											<span class="psp-task-meta">
												<span class="psp-task-meta-item psp-tmi-project">
													<em><?php esc_html_e( 'Project:', 'psp_projects' ); ?></em>
                                                    <a href="<?php echo esc_url( get_the_permalink($task['post_id']) ); ?>">
													     <?php echo esc_html( get_the_title($task['post_id']) ); ?>
                                                    </a>
												</span>
												<?php if( get_field( 'client', $task['post_id'] ) ): ?>
													<span class="psp-task-meta-item psp-tmi-client">
														<b class="psp-separator">|</b>
														<em><?php esc_html_e( 'Client:', 'psp_projects' ); ?></em>
														<?php the_field( 'client', $task['post_id'] ); ?>
													</span>
												<?php endif; ?>
												<span class="psp-task-meta-item psp-tmi-phase">
													<b class="psp-separator">|</b>
													<em><?php esc_html_e( 'Phase:', 'psp_projects' ); ?></em>
													<?php echo esc_html( $task['phase'] ); ?>
												</span>
												<?php if ( $task_document_count > 0 ) : ?>
													<span class="psp-task-meta-item psp-tmi-documents">
														<b class="psp-separator">|</b>
														<em><?php esc_html_e( 'Documents:', 'psp_projects' ); ?></em>
														<span class="text"><?php echo esc_html( $task_document_count ); ?></span>
													</span>
												<?php endif; ?>

												<?php // We always "show" comment count since this could be updated from 0 to 1 ?>
												<span class="psp-task-meta-item psp-tmi-comments<?php echo ( $task_comment_count <= 0 ) ? ' hidden' : ''; ?>">
													<b class="psp-separator">|</b>
													<em><?php esc_html_e( 'Comments:', 'psp_projects' ); ?></em>
													<span class="text"><?php echo esc_html( $task_comment_count ); ?></span>
												</span>

											</span>
                                        </div>
                                    </div>
                                    <div class="psp-row">
                                        <div class="psp-col-md-8">
                                            <div class="psp-task-table-status task-item-<?php echo esc_attr( $task['ID'] ); ?>">
                                                <?php if( psp_can_edit_task( $task[ 'post_id' ], $task[ 'phase_id' ], $task[ 'ID' ] ) ): ?>
                                                    <span class="psp-task-edit-links">
                                                        <a href="#edit-task-<?php echo $task[ 'ID' ]; ?>" class="task-table-edit-link"><b class="fa fa-adjust"></b> <?php esc_html_e( 'update', 'psp_projects' ); ?></a>
                                                        <a href="#" class="complete-task-link"
                                                            <?php foreach( $link_attributes as $attribute => $value ): ?>
                                                                data-<?php echo $attribute; ?>="<?php echo esc_attr( $value ); ?>"
                                                            <?php endforeach; ?> >
                                                            <b class="fa fa-check"></b>
                                                            <?php esc_html_e( 'complete', 'psp_projects' ); ?>
                                                        </a>
                                                    </span> <!--/.psp-task-edit-links-->
                                                    <div id="<?php echo esc_attr('#edit-task-' . $task['ID'] ); ?>" class="task-select">
                                                        <?php $select_options = psp_get_status_percentages(); ?>
                                                        <select id="edit-task-select-<?php echo $task[ 'phase_id' ] . '-' . $task[ 'ID' ]; ?>" class="edit-task-select">
                                                            <option value="<?php echo esc_attr( $task[ 'status' ] ); ?>"><?php echo $task[ 'status' ]; ?>%</option>
                                                            <?php foreach( $select_options as $value => $label ): ?>
                                                                <option value="<?php echo esc_attr( $value ); ?>"><?php echo esc_html( $label ); ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                        <input type="submit" name="save" value="save" class="task-save-button"
                                                            <?php foreach( $link_attributes as $attribute => $value ): ?>
                                                                data-<?php echo $attribute; ?>="<?php echo esc_attr( $value ); ?>"
                                                            <?php endforeach; ?>>
                                                    </div> <!--/.task-select-->
                                                <?php endif; ?>
                                                <span class="psp-progress-bar"><em class="status psp-<?php echo esc_attr( $task[ 'status' ] ); ?>"></em></span>
                                            </div>
                                        </div> <!--/.psp-task-title-->
                                        <div class="col-md-4 psp-task-due psp-task-meta-item">
                                            <?php if( !empty($task['due_date']) ): ?>
                                                <?php
                                                $date_class = ( strtotime( $task['due_date'] ) < strtotime( 'today' ) ? 'late' : '' );
                                                echo ' <b class="psp-tag ' . $date_class . '">' . esc_html( 'Due', 'psp_projects' ) . ' ' . date_i18n( get_option('date_format'), strtotime($task['due_date']) ) . '</b>';
                                            endif; ?>
                                        </div> <!--/.psp-task-meta-item-->
                                    </div> <!--/.psp-row-->
                                </td>
                            </tr>
							<?php
                            $task_phase = ( isset($phase[$task['phase_id']]) ? $phase[$task['phase_id']] : null );
                            do_action( 'psp_all_my_tasks_shortcode_after_task', $task['post_id'], $task['phase_id'], $task['ID'], $task['phases'], $task_phase ); ?>
                        <?php endforeach;
                    endif; ?>
                </tbody>
            </table>

        <?php if( $shortcode == TRUE ): ?>
            </div>
        <?php endif; ?>

    <?php }

    return ob_get_clean();

}

function psp_sort_tasks_by_due_date($a, $b) {
    if( !is_int( $a['due_date'] ) || !is_int( $b['due_date'] ) ) {
        return 0;
    }
    return $a['due_date'] - $b[ 'due_date' ];
}

add_shortcode( 'psp-dashboard', 'psp_archive_dashboard_shortcode' );
function psp_archive_dashboard_shortcode() {

    psp_front_assets(1);

    $count     = apply_filters( 'psp_archive_project_listing_count', ( isset($_GET['count']) ? $_GET['count'] : get_option('posts_per_page') ) );
    $status    = apply_filters( 'psp_archive_project_listing_status', ( get_query_var('psp_status_page') ? get_query_var('psp_status_page') : 'active' ) );
    $paged     = ( get_query_var('paged') ? get_query_var('paged') : 1 );
    $args      = apply_filters( 'psp_archive_project_listing_args', psp_setup_all_project_args($_GET) );
    $projects	= psp_get_all_my_projects( $status, $count, $paged, $args );
    $tasks 		= psp_get_all_my_tasks();

    include( psp_template_hierarchy('dashboard/shortcode') );

}
