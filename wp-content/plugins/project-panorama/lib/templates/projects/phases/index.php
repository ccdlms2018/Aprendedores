<!-- Hidden admin URL so we can do Ajax -->
<?php
$post_id 	 = ( isset($post_id) ? $post_id : get_the_ID() );
$phases		 = get_field( 'phases', $post_id );
$phase_index = 0;
$i		     = 0;
$c			 = 0;
$colors 	 = psp_get_phase_color();
$phase_summary 	= psp_get_phase_summary( get_field('phases', $post_id ) );
$wrapper_class	= ( $style == 'psp-shortcode' ? 'psp-shortcode-phases' : 'psp-row' );
?>

<input id="psp-ajax-url" type="hidden" value="<?php echo admin_url(); ?>admin-ajax.php">

<?php if( $phases ): ?>
	<hgroup class="psp-section-heading psp-row">
		<?php do_action( 'psp_before_phases_title', $phases, $post_id ); ?>
		<h2 class="psp-section-title"><?php esc_html_e( 'Phases', 'psp_projects' ); ?></h2>
		<?php do_action( 'psp_after_phases_title', $phases, $post_id ); ?>
		<p class="psp-section-data">
			<?php if(comments_open( $post_id )): ?>
				<a href="#" class="psp-expand-comments" data-toggle="<?php esc_attr_e( 'Collapse Discussions', 'psp_projects' ); ?>"><i class="fa fa-plus"></i> <span><?php esc_html_e( 'Expand Discussions', 'psp_projects' ); ?></span></a> <span class="psp-pipe">|</span>
			<?php endif; ?>
			<span class="psp-phases-completed" data-value="<?php echo esc_attr($phase_summary['completed']); ?>"><?php echo esc_html($phase_summary['completed']); ?></span>  / <span class="psp-phases-total" data-value="<?php echo esc_attr($phase_summary['total']); ?>"><?php echo esc_html($phase_summary['total']) . ' ' . __( 'Completed', 'psp_projects' ); ?></span>
		</p>
		<?php do_action( 'psp_after_phases_data', $phases, $post_id ); ?>
	</hgroup>
<?php endif; ?>

<div class="<?php echo esc_attr($wrapper_class); ?> cf psp-phase-wrap psp-total-phases-<?php echo esc_attr(psp_get_phase_count()); ?>">

	<script>
		var chartOptions = {
			responsive: true,
			percentageInnerCutout : <?php echo esc_js( apply_filters( 'psp_graph_percent_inner_cutout', 92 ) ); ?>,
			maintainAspectRatio: true,
		}
        var allCharts = [];
	</script>

	<?php
	while ( have_rows( 'phases', $post_id ) ) :

		$phase = the_row();

		// Increment

		$c++;
		$i++;

		if( $c == count($colors) ) $c = 0;
		$color = $colors[$c];

		/**
		 * Important Phase Variables
		 *
		 * @var $phase_data [array] 'completed', 'completed-tasks', 'tasks'
		 * @var $remaining [int]
		 * @var $phase_comment_key [string]
		 * @var $comment_count [int]
		 */

		$phase_data 		= psp_get_phase_completed( $phase_index, $post_id );
		$remaining 			= 100 - $phase_data['completed'];
		$phase_comment_key 	= psp_validate_or_generate_comment_key( get_sub_field('phase_id'), $phase_index, $i - 1 );
		$comment_count		= ( ! empty( $phase_comment_key ) ? psp_get_phase_comment_count( $phase_comment_key ) : '0' );
		$phase_docs    		= psp_parse_phase_documents( get_field('documents'), get_sub_field('phase_id'), get_sub_field('tasks') );
		$phase_docs_count	= ( !empty($phase_docs['all']) ? count($phase_docs['all']) : 0 );
		$approved       	= psp_count_approved_documents( $phase_docs['all'] );

		do_action( 'psp_before_individual_phase_wrapper', $post_id, $phase_index, $phases, $phase ); ?>

		<div class="psp-phase color-<?php echo esc_attr($color['name']); ?> phase-<?php echo esc_attr($phase_index + 1); ?> <?php echo esc_attr( psp_get_phase_classes( $post_id, $phase_index ) ); ?>" data-phase-index="<?php esc_attr_e($phase_index); ?>" data-phase_id="<?php esc_attr_e( $phase_comment_key ); ?>" data-completed="<?php esc_attr_e( $phase_data['completed'] ); ?>" data-tasks="<?php esc_attr_e($phase_data['tasks']); ?>" id="phase-<?php esc_attr_e( $phase_index + 1 ); ?>">

			<?php do_action( 'psp_after_individual_phase_wrapper', $post_id, $phase_index, $phases, $phase ); ?>

			<h3 class="psp-phase-title-wrap">

				<span class="psp-phase-title"><?php the_sub_field('title'); ?></span>

				<span class="psp-top-complete psp-row cf">
					<?php
					$psp_phase_stats = apply_filters( 'psp_phase_stats', array(
						array(
							'wrapper_class'	=>	'psp-col-xs-4',
							'stat_class'	=>	'psp-phase-document-count',
							'stat'			=>	$phase_docs_count,
							'title'			=>	'<i class="fa fa-files-o"></i> ' . _n( __('Document', 'psp_projects'), __('Documents','psp_projects'), $phase_docs_count )
						),
						array(
							'wrapper_class'	=>	'psp-col-xs-4',
							'stat_class'	=>	'count task-count',
							'stat'			=>	'<span class="completed">' . $phase_data['completed_tasks'] . '</span> / <span class="total total-task-count">' . $phase_data['tasks'] . '</span>',
							'title'			=>	'<i class="fa fa-tasks"></i> ' . __( 'Tasks', 'psp_projects' ),
						),
						array(
							'condition'		=>	comments_open(),
							'wrapper_class'	=>	'psp-col-xs-4',
							'stat_class'	=>	'comment-count',
							'stat'			=>	$comment_count,
							'title'			=>	'<i class="fa fa-comment"></i> ' . __( 'Responses', 'psp_projects' ),
						)
					) );

					foreach( $psp_phase_stats as $stat ):

						if( isset($stat['condition']) && isset($stat['condition']) == false ) continue; ?>
						<span class="<?php echo esc_attr( $stat['wrapper_class'] ); ?>">
							<span class="<?php echo esc_attr( $stat['stat_class'] ); ?>">
								<?php echo wp_kses_post($stat['stat']); ?>
							</span>
							<b><?php echo wp_kses_post($stat['title']); ?></b>
						</span>
					<?php endforeach; ?>
				</span>
			</h3> <!--/h3.psp-phase-title-wrap-->

			<?php do_action( 'psp_after_phase_title', $post_id, $phase_index ); ?>

			<div class="psp-phase-overview cf psp-phase-progress-<?php echo esc_attr($phase_data['completed']); ?>">

				<div class="psp-chart">
					<span class="psp-chart-complete"><?php echo esc_html($phase_data['completed']); ?>%</span>
					<canvas class="phase-chart" data-chart-id="<?php echo esc_attr($i); ?>" id="chart-<?php echo esc_attr($phase_index); ?>" width="100%"></canvas>

					<script>
                        jQuery(document).ready(function() {

                            var data = [
                                {
                                    value: <?php echo $phase_data['completed']; ?>,
                                    color: "<?php echo $color[ 'hex' ]; ?>",
                                    label: "<?php esc_html_e( 'Completed', 'psp_projects' ); ?>"
                                },
                                {
                                    value: <?php echo $remaining; ?>,
                                    color: "#efefef",
                                    label: "<?php esc_html_e( 'Remaining', 'psp_projects' ); ?>"
                                }
                            ];

                            var chart_<?php echo $phase_index; ?> = document.getElementById("chart-<?php echo $phase_index; ?>").getContext("2d");
							allCharts[<?php echo $phase_index; ?>] = new Chart(chart_<?php echo $phase_index; ?>).Doughnut(data,chartOptions);

                        });
					</script>

				</div> <!--/.psp-chart-->

				<div class="psp-phase-info">
					<?php
					do_action( 'psp_before_phase_description', $post_id, $phase_index, $phases, $phase );
					if( get_sub_field('description') ): ?>
						<h5><?php esc_html_e( 'Description', 'psp_projects' ); ?></h5>
						<?php the_sub_field( 'description' ); ?>
					<?php
					endif;
					do_action( 'psp_after_phase_description', $post_id, $phase_index, $phases, $phase ); ?>
				</div>

			</div> <!-- tasks is '.$task_style.'-->

			<?php
			do_action( 'psp_before_phase_lists', $post_id, $phase_index, $phases, $phase );

			include( psp_template_hierarchy( 'projects/phases/tasks/index.php' ) );

			include( psp_template_hierarchy( 'projects/phases/documents/index.php' ) );

			if( comments_open( $post_id ) ) include( psp_template_hierarchy( 'projects/phases/discussions/index.php' ) );

			do_action( 'psp_after_phase_lists', $post_id, $phase_index, $phases, $phase ); ?>

		</div> <!--/.psp-task-list-->
		<?php $phase_index++;
	endwhile; ?>
</div>
