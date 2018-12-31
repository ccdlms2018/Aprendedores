<?php
add_filter( 'psp_notifications', 'psp_add_email_notification' );
function psp_add_email_notification( $notifications ) {

	$notifications['email'] = array(
		'name'               => __( 'Email', 'psp_projects' ),
		'default_feed_title' => __( 'New email notification', 'psp_projects' ),
		'fields'             => array(
			'recipient_email' => array(
				'id'    => 'recipient_email',
				'label' => __( 'Recipients', 'psp_projects' ),
				'type'  => 'text',
				'args'  => array(
					'desc' => __( '<span class="psp-label-new-line">Separate emails with commas. Use <code>%users%</code> to send to all users or to specific roles: <code>%subscribers%</code>, <code>%project_owners%</code>, <code>%project_managers%</code>. <span class="psp-dyn-var task_assigned users_assigned task_overdue">Use <code>%target%</code> to send to related user.</span>', 'psp_projects' ),
				),
			),
			'recipient_cc' 	=> array(
				'id'    => 'recipient_cc',
				'label' => __( 'CC Emails', 'psp_projects' ),
				'type'  => 'text',
				'args'  => array(
					'desc' => __( '<span class="psp-label-new-line">Separate emails with commas. Use <code>%users%</code> to send to all users or to specific roles: <code>%subscribers%</code>, <code>%project_owners%</code>, <code>%project_managers%</code>.', 'psp_projects' ),
				),
			),
			'recipient_bcc' 	=> array(
				'id'    => 'recipient_bcc',
				'label' => __( 'BCC Emails', 'psp_projects' ),
				'type'  => 'text',
				'args'  => array(
					'desc' => __( '<span class="psp-label-new-line">Separate emails with commas. Use <code>%users%</code> to send to all users or to specific roles: <code>%subscribers%</code>, <code>%project_owners%</code>, <code>%project_managers%</code>.', 'psp_projects' ),
				),
			),
			'subject'         => array(
				'id'    => 'subject',
				'label' => __( 'Subject Line', 'psp_projects' ),
				'type'  => 'text',
			),
			'message'         => array(
				'id'    => 'message',
				'label' => __( 'Message', 'psp_projects' ),
				'type'  => 'textarea',
				'args'  => array(
					'desc' => sprintf(
						__( 'Possible available dynamic variables: %s', 'psp_projects' ),
						'<code>' . implode( '</code><code>', array(
							'<span class="psp-dyn-var all">%project_title%</span>',
							'<span class="psp-dyn-var all">%project_url%',
							'<span class="psp-dyn-var all">%dashboard%',
							'<span class="psp-dyn-var all">%date%',
							'<span class="psp-dyn-var all">%client%',
							'<span class="psp-dyn-var task_overdue task_due task_complete new_comment">%phase_title%</span>',
							'<span class="psp-dyn-var task_overdue task_due task_complete">%task_title%</span>',
							'<span class="psp-dyn-var task_assigned task_overdue task_due task_complete">%username%</span>',
							'<span class="psp-dyn-var task_assigned">%tasks_assigned%</span>',
							'<span class="psp-dyn-var new_comment">%comment_author%</span>',
							'<span class="psp-dyn-var new_comment">%comment_content%</span>',
							'<span class="psp-dyn-var new_comment">%comment_link%</span>',
							'<span class="psp-dyn-var project_progress">%progress%</span>'
						) ) . '</code>'
					),
				),
			),
		),
	);

	return $notifications;
}

add_action( 'psp_do_notification_email', 'psp_notify_email', 10, 6 );
function psp_notify_email( $post, $fields, $args, $notification_ID, $notification_args, $notification_type, $parts = array() ) {

	$fields = wp_parse_args( array_filter( $fields ), array(
		'subject'         	=> psp_get_option( 'psp_default_subject' ),
		'message'         	=> psp_get_option( 'psp_default_message' ),
		'recipient_email' 	=> '',
		'recipient_cc'		=> '',
		'recipient_bcc'		=> '',
		'post_id'			=> $args['post_id'],
		'user_ids'			=> ( isset($args['user_ids']) ? $args['user_ids'] : array() ),
	) );

	$args['project_id'] 	= $args['post_id'];

	$replacements = psp_notifications_replacements(
		array( 'subject' => $fields['subject'], 'message' => $fields['message'] ),
		$notification_type,
		$args,
		array(),
		$notification_ID
	);


	$parts = apply_filters( 'psp_notify_email_parts', $parts, $post, $fields, $args, $notification_type );

	$fields['subject'] = $replacements['subject'];
	$fields['message'] = $replacements['message'];

	$target_variables = apply_filters( 'psp_target_recipient_types', array(
		'task_due',
		'task_overdue',
	) );

	if( in_array( $notification_type, $target_variables ) && $fields['recipient_email'] == '%target%') {

		if( isset( $args['user_ids'] ) ) {

			$fields['recipient_email'] = '';
			foreach( $args['user_ids'] as $user_id ) {
				$recipient = get_user_by( 'id', $args['user_id'] );
				$fields['recipient_email'] .= $recipient->user_email . ',';
			}

		} elseif( isset( $args['user_id'] ) ) {
			$recipient = get_user_by( 'id', $args['user_id'] );
			$fields['recipient_email'] = $recipient->user_email;
		}

	}

	psp_send_email( $parts, $fields, $notification_type );

}

add_filter( 'psp_notify_email_parts', 'psp_default_email_parts', 10, 5 );
function psp_default_email_parts( $parts, $post, $fields, $args, $notification_type ) {

	switch( $notification_type ) {

		case 'users_assigned';

			$parts = array( 'logo', 'heading', 'message', 'link' );
			break;

	}

	return $parts;

}

function psp_send_progress_email( $to, $subject, $message, $post_id = null, $progress = null ) {

	global $post;

	// If a POST ID isn't set, let's use the current page
	$post_id 	= ( empty( $post_id ) ? $post->ID : $post_id );

	// Get the users ID and E-Mail
	$user 		= get_user_by( 'id', $to );

	if ( empty( $user ) ) return;

	$args = array(
		'recipient_email' => $user->user_email,
		'recipient_name'  => $user->display_name,
		'progress'        => $progress,
		'post_id'         => $post_id,
		'subject'         => $subject,
		'message'         => $message,
	);

	$email_parts = array(
		'logo',
		'heading',
		'message',
		'progress',
		'link',
	);

	psp_send_email( $email_parts, $args );

}
