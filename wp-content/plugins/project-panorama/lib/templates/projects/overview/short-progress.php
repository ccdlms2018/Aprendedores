<?php
/**
 * Created by PhpStorm.
 * User: rossjohnson
 * Date: 1/3/15
 * Time: 1:18 PM
 */

if( !isset( $post_id ) ) { global $post; $post_id = $post->ID; }
$completed = psp_compute_progress( $post_id ); ?>

<div id="psp-short-progress">
	<h4><?php _e('Project Progress','psp_projects'); ?></h4>
	<p class="psp-progress">
		<span class="psp-<?php echo esc_attr($completed); ?>" data-toggle="psp-tooltip" data-placement="top" title="<?php echo esc_attr($completed . '% ' . __( 'Complete', 'psp_projects' ) ); ?>">
			<b><?php echo esc_html($completed); ?>%</b>
		</span>
		<i class="psp-progress-label"> <?php esc_html_e('Progress','psp_projects'); ?> </i>
	</p>
</div>
