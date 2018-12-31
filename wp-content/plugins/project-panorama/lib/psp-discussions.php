<?php
add_filter( 'comments_template', 'psp_custom_discussion_template' );

function psp_custom_discussion_template( $comment_template ) {

	global $post;

	if( get_post_type() == 'psp_projects' ) {
		return dirname( __FILE__ ) . '/templates/projects/discussion/discussion-loop.php';
	}

	return $comment_template;

}
