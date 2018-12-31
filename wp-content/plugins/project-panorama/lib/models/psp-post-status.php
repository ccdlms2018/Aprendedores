<?php
class PSP_WordPress_Custom_Status {

  // ...

  	public function set_status() {

	  	$set_status = apply_filters( 'psp_custom_post_status_' . $this->slug, true );

		if( ! $set_status ) {
			return;
		}
		global $post;

		if( ! in_array( $post->post_type, $this->post_type ) ) {
			return;
		}

		$complete = '';
		$label = '';

		if( $post->post_status == $this->slug ) {
			$complete = ' selected=\"selected\"';
			$label = '<span id=\"post-status-display\">' . $this->settings['label'] . '</span>';
		}
		?>
		<script>
			( function($){
				$(document).ready(function(){
					$('select#post_status').append( "<option value='<?php echo $this->slug; ?>' <?php echo $complete; ?>><?php echo $this->settings['label']; ?></option>");
					$('.misc-pub-section label').append( "<?php echo $label; ?>");
					<?php if( $complete != '' ) {
						// If the post has this status check the preferred action
						// If true or 'publish', we leave it as default
						if( ! $this->enable_action ) {
							echo '$("#publish").remove();';
						} elseif( $this->enable_action === 'update' ) {
							echo '$("#publish").val("Update");$("#publish").attr("name","save");$("#original_publish").val("Update");';
						}
					} ?>
				});
			})( jQuery );
		</script>
	<?php
	}
}

add_action( 'init', 'psp_register_custom_post_status' );
function psp_register_custom_post_status() {

    $statuses = apply_filters( 'psp_custom_post_statues', array(
        array(
            'slug'  =>  'psp_completed',
            'label' =>  __( 'Completed', 'psp_projects' )
        ),
        array(
            'slug'  =>  'psp_hold',
            'label' =>  __( 'On Hold', 'psp_projects' )
        ),
        array(
            'slug'  =>  'psp_canceled',
            'label' =>  __( 'Canceled', 'psp_projects' )
        ),
    ) );

    foreach( $statuses as $status ) {

        new PSP_WordPress_Custom_Status( array(
            'post_type' => array( 'psp_project' ),
            'slug' => $status['slug'],
            'label' => $status['label'],
            'action' => 'update',
            'label_count' => _n_noop( 'Custom <span class="count">(%s)</span>', 'Custom <span class="count">(%s)</span>' ),
        ));

    }

}
