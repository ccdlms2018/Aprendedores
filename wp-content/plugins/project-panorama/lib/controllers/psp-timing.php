<?php
/**
 * psp-timing.php
 *
 * Controlls all things related to timing.
 *
 * @category controller
 * @package psp-projects
 * @author Ross Johnson
 * @since 1.3.6
 */

/**
 * Get the projects start date
 * @param  [date string] $format  optional start date
 * @param  [int] $post_id projec tID
 * @return formated date
 */
function psp_get_the_start_date( $format = NULL, $post_id = NULL) {

	$post_id 	= ( $post_id == NULL ? get_the_ID() : $post_id );
	$start_date = get_field( 'start_date', $post_id );

	if( !$start_date ) return false;

	$format = ( $format == NULL ? get_option( 'date_format' ) : $format );

	return apply_filters( 'psp_start_date', date_i18n( $format, strtotime($start_date) ), $post_id, $format );

}

function psp_get_the_end_date( $format = NULL, $post_id = NULL) {

	$post_id 	= ( $post_id == NULL ? get_the_ID() : $post_id );
	$end_date 	= get_field( 'end_date', $post_id );

	if( !$end_date ) return false;

	$format = ( $format == NULL ? get_option( 'date_format' ) : $format );

	return apply_filters( 'psp_end_date', date_i18n( $format, strtotime($end_date) ), $post_id, $format );

}

function psp_the_start_date( $post_id = NULL ) {

	global $post;

	$post_id = ( !empty( $post_id ) ? $post_id : $post->ID );

    $months = array(
		__('Jan','psp_projects'),
		__('Feb','psp_projects'),
		__('Mar','psp_projects'),
		__('Apr','psp_projects'),
		__('May','psp_projects'),
		__('Jun','psp_projects'),
		__('Jul','psp_projects'),
		__('Aug','psp_projects'),
		__('Sep','psp_projects'),
		__('Oct','psp_projects'),
		__('Nov','psp_projects'),
		__('Dec','psp_projects')
		);

    $startDate 	= apply_filters( 'psp_the_start_date_cal', get_field( 'start_date', $post_id ), $post_id );

    $s_year 	= substr( $startDate, 0, 4 );
    $s_month 	= substr( $startDate, 4, 2 );
    $s_day 		= substr( $startDate, 6, 2 );

	if( !empty( $startDate ) ): ?>

	    <div class="psp-date">
	        <span class="cal">
	            <span class="month"><?php echo $months[$s_month - 1]; ?></span>
	            <span class="day"><?php echo $s_day; ?></span>
	        </span>
	        <b><?php echo $s_year; ?></b>
	    </div>

	    <?php
		endif;

}

function psp_the_end_date( $post_id ) {

	global $post;

	$post_id = ( !empty( $post_id ) ? $post_id : $post->ID );

	$months = array(
		__('Jan','psp_projects'),
		__('Feb','psp_projects'),
		__('Mar','psp_projects'),
		__('Apr','psp_projects'),
		__('May','psp_projects'),
		__('Jun','psp_projects'),
		__('Jul','psp_projects'),
		__('Aug','psp_projects'),
		__('Sep','psp_projects'),
		__('Oct','psp_projects'),
		__('Nov','psp_projects'),
		__('Dec','psp_projects')
		);

    $endDate = apply_filters( 'psp_the_end_date_cal', get_field( 'end_date', $post_id ), $post_id );

	if( !empty( $endDate ) ):

	    $e_year 	= substr($endDate,0,4);
	    $e_month 	= substr($endDate,4,2);
	    $e_day 		= substr($endDate,6,2); ?>

	    <div class="psp-date">
	        <span class="cal">
		        <span class="month"><?php echo $months[$e_month - 1]; ?></span>
			    <span class="day"><?php echo $e_day; ?></span>
	    	</span>
	        <b><?php echo $e_year; ?></b>
	    </div>

    <?php
	endif;

}

function psp_text_date( $date ) {

	$date 	= strtotime( $date );
	$format = get_option( 'date_format' );

	if( empty( $date ) ) return false;

	return date_i18n( $format, $date );

}

function psp_the_timebar( $id ) {

    $startDate 	= get_field( 'start_date', $id );
    $endDate 	= get_field( 'end_date', $id );

	if( ( empty( $startDate ) ) || ( empty( $endDate ) ) ) { return; }

    $s_year 	= substr( $startDate, 0, 4 );
    $s_month 	= substr( $startDate, 4, 2 );
    $s_day 		= substr( $startDate, 6, 2 );

    $e_year 	= substr( $endDate, 0, 4 );
    $e_month 	= substr( $endDate, 4, 2 );
    $e_day 		= substr( $endDate, 6, 2 );

    $textStartDate 	= psp_text_date( $startDate );
    $textEndDate 	= psp_text_date( $endDate );

    $all_time 			= psp_calculate_timing( $id );
	$project_completion = psp_compute_progress( $id );

    if( $all_time[ 'percentage_complete' ] < 0 ) {
        $all_time[ 'percentage_complete' ] = 100;
    }

	$marks = array( 10, 20, 30, 40, 50, 60, 70, 80, 90 );

	$progress_class = psp_get_the_schedule_status_class( $all_time[ 'percentage_complete' ], $project_completion ); ?>

	 	<div class="psp-timebar">

		 <?php
		 if( $all_time[ 'days_ellapsed' ] > $all_time[ 'total_days' ] ) {
		 	$days_left = ' <span class="psp-time-details">' . $all_time[ 'days_ellapsed' ] . __( 'days past project end date.', 'psp_projects' ) . '</span>';
	   	 } else {
		 	$days_left = ' <span class="psp-time-details">' . $all_time[ 'days_ellapsed' ] . __( 'days remaining', 'psp_projects' ) . '</span>';
	 	 } ?>

    		 <p class="psp-time-start-end"><?php echo $textStartDate; ?> <span><?php echo $textEndDate; ?></span></p>

    		 <div class="psp-time-progress">

       		  	<p class="psp-time-bar <?php echo esc_attr( $progress_class ); ?>"><span class="psp-<?php echo $all_time[ 'percentage_complete' ]; ?>"></span></p>

      			<ol class="psp-time-ticks <?php echo esc_attr( $progress_class ); ?>">
					<?php foreach( $marks as $mark ): ?>
						<li class="psp-tt-<?php echo esc_attr( $mark ); ?> <?php if( $all_time[ 'percentage_complete' ] >= $mark ) { echo esc_attr( 'active' ); } ?>"></li>
					<?php endforeach; ?>
        		</ol>

        		<span class="psp-time-indicator <?php echo esc_attr( $progress_class ); ?>" style="left: <?php echo $all_time[ 'percentage_complete' ]; ?>%"><span></span><?php echo $all_time[ 'percentage_complete' ]; ?>%</span>

   	 	  </div> <!--/.psp-time-progress-->

	</div> <!--/.psp-timebar-->

	<?php

}

function psp_the_simplified_timebar( $post_id ) {

    $start_date = get_field( 'start_date', $post_id );
    $end_date 	= get_field( 'end_date', $post_id );

	if( empty($start_date) || empty($end_date) ) return;

	$start = array(
		'year'	=>	substr( $start_date, 0, 4 ),
		'month'	=>	substr( $start_date, 4, 2 ),
		'day'	=>	substr( $start_date, 6, 2 )
	);

	$end = array(
		'year'	=>	substr( $end_date, 0, 4 ),
		'month'	=>	substr( $end_date, 4, 2 ),
		'day'	=>	substr( $end_date, 6, 2 )
	);

    $all_time 			= psp_calculate_timing( $post_id );
	$project_completion = psp_compute_progress( $post_id );

    if( $all_time['percentage_complete'] < 0 ) {
        $all_time['percentage_complete'] = 100;
    }

	$progress_class = psp_get_the_schedule_status_class( $all_time['percentage_complete'], $project_completion ); ?>

 	<div class="psp-simplified-timebar">
		<p class="psp-tb-progress <?php echo esc_attr($progress_class); ?>">
			<span class="psp-<?php echo esc_attr($all_time['percentage_complete']); ?>" data-toggle="psp-tooltip" data-placement="top" title="<?php echo esc_attr($all_time['percentage_complete'] . '% ' . __( 'Time Ellapsed', 'psp_projects' )); ?>">
				<b><?php echo esc_html($all_time['percentage_complete']); ?>%</b>
			</span>
			<i class="psp-progress-label"> <?php esc_html_e('Timing','psp_projects'); ?> </i>
		</p>
	</div>

	<?php

}

function psp_calculate_timing( $post_id = NULL ) {

	$post_id = ( $post_id == NULL ? get_the_ID() : $post_id );

    $start_date 	= get_field( 'start_date', $post_id);
    $end_date 		= get_field( 'end_date', $post_id);

    $today 		= time();
    $s_year 	= substr( $start_date, 0, 4 );
    $s_month 	= substr( $start_date, 4, 2 );
    $s_day 		= substr( $start_date, 6, 2 );

    $e_year 	= substr( $end_date, 0, 4 );
    $e_month 	= substr( $end_date, 4, 2 );
    $e_day 		= substr( $end_date, 6, 2 );

    $start_date = strtotime( $s_year . '-' . $s_month . '-' . $s_day );
    $end_date 	= strtotime( $e_year . '-' . $e_month . '-' . $e_day );

    $total_days = abs( $start_date - $end_date);
    $total_days = floor( $total_days / ( 60 * 60 * 24 ) );

    $datediff 	= abs( $today - $end_date );

    $time_completed = floor( $datediff / ( 60 * 60 * 24 ) );

	if( $start_date > $today ) {
        $time_percentage = 0;
    } elseif( $end_date < $today || $total_days == 0 ) {
        $time_percentage = 100;
	} else {
	    $time_percentage = floor( 100 - ( $time_completed / $total_days * 100 ) );
	}

	// Check to make sure we don't round up unintentionally
	if( $time_percentage == 100 && $end_date > $today ) { $time_percentage = 99; }

    $all_time = array( 'percentage_complete' => $time_percentage, 'total_days' => $total_days, 'days_ellapsed' => $time_completed );

    return apply_filters( 'psp_calculate_timing', $all_time, $post_id );

}

function psp_verbal_status( $all_time, $calc_completed ) {

    if($all_time[ 'percentage_complete' ] > $calc_completed) { return 'behind'; } else { return 'time'; }

}

function psp_the_timing_bar( $post_id ) {

    $time_elapsed 	= psp_calculate_timing( $post_id );
    $completed 		= psp_compute_progress( $post_id );

	$progress_class = ( $completed < $time_elapsed[ 'percentage_complete' ] ? 'psp-behind' : 'psp-ontime' );

    if( $time_elapsed[ 'percentage_complete' ] < 0 ) {
        $time_elapsed[ 'percentage_complete' ] = 100;
    }

    echo '<p class="psp-timing-progress psp-progress ' . $progress_class . '"><span class="psp-' . $time_elapsed[ 'percentage_complete' ] . '"><strong>%' . $time_elapsed[ 'percentage_complete' ] . '</strong></span></p>';

}

function psp_output_project_calendar( $user_id = null ) {

	$cuser 		= wp_get_current_user();
	$user_id 	= ( $user_id == null ? $cuser->ID : $user_id );

	if(!is_admin()) {
		// psp_enqueue_calendar_assets();
	}

	$date_url 	= ( get_option( 'permalink_structure' ) ? home_url() . '/psp-dates/' . $user_id . '/' : home_url() . '/index.php?psp_dates=' . $user_id );
	$hashed_id	=	psp_get_hashed_id( wp_get_current_user() );

	ob_start(); ?>

		<div id="psp-project-calendar"></div>

		<p><a class="psp-ical-link" href="<?php echo psp_get_ical_link(); ?>" target="_new"><?php echo esc_html_e( 'iCal Feed', 'psp_projects' ); ?></a></p>

		<script>

			// Fixes odd problem with calendar on-load
			// https://stackoverflow.com/a/22723412
			( function( $ ) {

				$( window ).on( 'load', function() {

					$('#psp-project-calendar').fullCalendar({
						events: '<?php echo esc_url($date_url); ?>',
						<?php if( psp_get_option( 'psp_calendar_language' ) ) { ?>
							lang: '<?php echo psp_get_option( 'psp_calendar_language' ); ?>',
						<?php } ?>
						eventRender: function(event, element) {
							element.popover({
								animation	: false,
								title		: event.title,
								placement	: 'left',
								trigger		: 'hover',
								content		: event.description,
								container	: '#psp-project-calendar',
								html		: true
							});
							element.hover(function() {
								$(this).css( 'zIndex', 1001 );
								var parents = $(this).parents('.fc-row .fc-content-skeleton');
								$(element).parents('.fc-row').css( 'zIndex', 1000 );
							},function(){
								$(element).parents('.fc-row').css( 'zIndex', 1 );
							});
						}
					});

				} );

			} )( jQuery );

		</script>

	<?php
	return ob_get_clean();

}

// Add a rewrite rule to output JSON
add_action( 'init', 'psp_calendar_rewrite_rules' );
function psp_calendar_rewrite_rules() {

	global $wp_rewrite;

	$slug 	= psp_get_slug();
	$front 	= '';

	if( isset( $wp_rewrite->front ) ) $slug 	= substr( $wp_rewrite->front, 1 ) . $slug;

	/**
	 * Allow the user ID to be passed in to create a JSON feed or iCAL feed
	 */
	add_rewrite_tag( '%psp_dates%', '([^&]+)' );
	add_rewrite_rule( '^psp-dates/([^&]+)/?', 'index.php?psp_dates=$matches[1]', 'top' );

	/**
	 * Setup a page to just output the calendar
	 */
	add_rewrite_rule( '^' . $slug . '/calendar/([^/]*)/?', 'index.php?post_type=psp_projects&psp_calendar_page=$matches[1]', 'top' );
	add_rewrite_rule( '^' . $slug . '/ical/([^/]*)/?', 'index.php?post_type=psp_projects&psp_ical_page=$matches[1]', 'top' );

}

add_filter( 'query_vars', 'psp_calendar_page_query_vars' );
function psp_calendar_page_query_vars( $vars ) {

	$vars[] = 'psp_calendar_page';
	$vars[] = 'psp_ical_page';

	return $vars;

}

add_action( 'template_redirect', 'psp_dates_endpoint_data' );
function psp_dates_endpoint_data() {

    global $wp_query;

    $date_tag 	= $wp_query->get( 'psp_dates' );
 	$cuser 		= wp_get_current_user();

    if ( ( ! $date_tag ) || ( $date_tag != $cuser->ID ) ) return;

	$meta_query	= psp_access_meta_query( $cuser->ID );
    $date_data 	= array();

	// Update to make more custom

    $args = array(
        'post_type'      => 'psp_projects',
        'posts_per_page' => -1,
    );

    if( ! current_user_can('delete_others_psp_projects') ) {

        $meta_args = array(
            'meta_query' 	=> $meta_query,
			'has_password' 	=> false
        );
		$args = array_merge( $args, $meta_args );

	}

    $projects = new WP_Query( $args );

	$date_data = psp_get_project_dates($projects);

    wp_send_json( apply_filters( 'psp_date_data_json', $date_data, $projects ) );

}

function psp_get_project_dates( $projects = NULL ) {

	if( $projects == NULL ) return false;

	$date_data = array();

	if( $projects->have_posts() ): while( $projects->have_posts() ): $projects->the_post();

		global $post;

		/* Start and end dates */
		if( get_field( 'start_date' ) || get_field( 'end_date' ) ) {

			$start_date 	= get_field( 'start_date' );
			$end_date 		= get_field( 'end_date' );
			$title 			= get_the_title();

			$s_year 	= substr( $start_date, 0, 4 );
			$s_month 	= substr( $start_date, 4, 2 );
			$s_day 		= substr( $start_date, 6, 2 );

			$e_year 	= substr( $end_date, 0, 4 );
			$e_month 	= substr( $end_date, 4, 2 );
			$e_day 		= substr( $end_date, 6, 2 );

			if( $start_date ) {
				$date_data[] = apply_filters( 'psp_project_start_ical_date', array(
					'title'  		=>  __( 'Project Start', 'psp_projects' ) . ' ' . html_entity_decode(get_the_title()),
					'start'			=>	$s_year . '-' . $s_month . '-' . $s_day,
					'url' 			=> 	get_permalink(),
					'description' 	=>  '<h3>' . esc_html(get_the_title()) . '</h3>' . ' - ' . esc_html(get_field('client')),
					'ical_desc'		=>	esc_html(get_the_title()) . ' - ' . __( 'Client:', 'psp_project' ) . esc_html(get_field('client')),
					'color'			=>	apply_filters( 'psp_calendar_start_date', '#3299BB' ),
					'ID'			=>	$post->ID
				), $post->ID );
			}

			if( $end_date ) {
				$date_data[] = apply_filters( 'psp_project_end_ical_date', array(
					'title'  		=> 	__( 'Project End', 'psp_projects' ) . ' ' . html_entity_decode(get_the_title()),
					'start'			=>	$e_year . '-' . $e_month . '-' . $e_day,
					'url' 			=> 	get_permalink(),
					'description' 	=> '<h3>' . get_the_title() . '</h3>' . ' - ' . get_field('client'),
					'ical_desc'		=>	get_the_title() . ' - ' . __( 'Client:', 'psp_project' ) . get_field('client'),
					'color'			=>	apply_filters( 'psp_calendar_end_date', '#C44D58' ),
					'ID'			=>	$post->ID
				), $post->ID );
			}

		}

		/**
		 * Milestones
		 */

		if( get_field( 'milestones', $post->ID ) ) {
			while( have_rows( 'milestones', $post->ID ) ) { the_row();

				if( !get_sub_field( 'date' ) ) continue;

				$date	=	strtotime( get_sub_field( 'date' ) );
				$date	=	date( 'Y-m-d', $date );

				$date_data[] = apply_filters( 'psp_milestone_ical_date', array(
					'title'			=>		__( 'Project Milestone', 'psp_projects' ),
					'start'			=>		$date,
					'url'			=>		get_permalink( $post->ID ),
					'description'	=>		'<h3>' . get_sub_field( 'title' ) . '</h3><br><p><strong>' . __( 'Project:', 'psp_projects' ) . '</strong> ' . get_the_title( $post->ID ) . '</p><p><strong>' . __( 'Client:', 'psp_projects' ) . '</strong> ' . get_field( 'client', $post->ID ) . '</p>',
					'ical_desc'		=>		__('Milestone: ','psp_project') . get_sub_field('title') . "\n" . __('Client:', 'psp_projects') . get_field('client', $post->ID),
					'color'			=>		'#2a3542',
					'ID'			=>		$post->ID
				), $post->ID );

			}
		}

		/**
		 * Tasks
		 */

		if( !get_field( 'phases', $post->ID ) ) continue;

		while( have_rows( 'phases', $post->ID ) ) { the_row();

			if( !get_sub_field( 'tasks' ) ) continue;

			while( have_rows( 'tasks' ) ) { the_row();

				if( !get_sub_field('due_date') || get_sub_field('status') == '100' ) {
					continue;
				}

				$date 	= strtotime( get_sub_field( 'due_date' ) );
				$date	= date( 'Y-m-d', $date );

				$date_data[] = apply_filters( 'psp_task_ical_date', array(
					'title'			=>		__( 'Task Due', 'psp_projects' ),
					'start'			=>		$date,
					'url'			=>		get_permalink( $post->ID ),
					'description'	=>		'<h3>' . get_sub_field( 'task' ) . ' - ' . get_sub_field( 'status' ) . '%</h3><br><p><strong>' . __( 'Project:', 'psp_projects' ) . '</strong> ' . get_the_title( $post->ID ) . '</p><p><strong>' . __( 'Client:', 'psp_projects' ) . '</strong> ' . get_field( 'client', $post->ID ) . '</p>',
					'ical_desc'		=>		__( 'Task', 'psp_projects' ) . ": " . get_sub_field( 'task' ) . "(" . get_sub_field('status') . "%)\n" . __( 'Project', 'psp_projects' ) . ": " . get_the_title($post->ID) . "\n" . __('Client', 'psp_projects') . ": " . get_field('client', $post->ID ),
					'color'			=>		'#99c262',
					'ID'			=>		$post->ID
				), $post->ID );

			}

		}

	endwhile; wp_reset_postdata();

	else:

		return false;

	endif;

	return $date_data;


}

add_action('admin_menu', 'psp_add_calendar_page');
function psp_add_calendar_page() {

	global $psp_add_calendar_page;

	$psp_add_calendar_page = add_submenu_page( 'edit.php?post_type=psp_projects','Project Calendar', __('Calendar', 'psp_projects'), 'manage_options', 'panorama-calendar', 'psp_project_calendar_page' );

}

function psp_project_calendar_page() { ?>

	<div class="wrap">

		<h1><?php _e('Project Calendar','psp_projects'); ?></h1>

		<br>

        <?php echo psp_output_project_calendar(); ?>

	</div>

	<?php
}

add_shortcode( 'psp_my_calendar', 'psp_my_calendar_shortcode' );
function psp_my_calendar_shortcode() {

	psp_enqueue_calendar_assets();

	ob_start();

	echo psp_output_project_calendar();

	return ob_get_clean();

}

function psp_project_schedule_status( $time_ellapsed, $project_progress ) {

	if( $time_ellapsed > $project_progress ) {

		$status = 'behind';

	} else {

		$status = 'ontime';

	}

	return apply_filters( 'psp_project_schedule_status', $status, $time_ellapsed, $project_progress );

}

function psp_get_the_schedule_status_class( $time_ellapsed, $project_progress ) {

	return apply_filters( 'psp_schedule_status_class', 'psp-' . psp_project_schedule_status( $time_ellapsed, $project_progress ), $time_ellapsed, $project_progress );

}

function psp_the_milestone_due_date( $date ) {

	$date 	= strtotime( $date );
	$format = get_option( 'date_format' );

	$date_class = ( $date < strtotime( 'today' ) ? 'late' : '' );

	echo '<b class="psp-mm-marker-date ' . $date_class . '">' . date_i18n( $format, $date ) . '</b>';

}


function psp_late_class( $date = NULL ) {

    if( empty( $date ) )
        return;

    $date = strtotime( $date );

	return ( $date < strtotime( 'today' ) ? 'late' : '' );

}

function psp_get_ical_link() {

	$hashed_id 	= psp_get_hashed_id( wp_get_current_user() );
	$link 		= psp_strip_http( get_post_type_archive_link('psp_projects') . 'ical/' . $hashed_id );

	return 'webcal://' . $link;

}
