<?php
$team_fields = array (
		'id' => 'acf_teams',
		'title' => __('Teams','psp_projects'),
		'fields' => array (
			array (
				'key' => 'field_569707a983243',
				'label' => __('Description','psp_projects'),
				'name' => 'description',
				'type' => 'wysiwyg',
				'default_value' => '',
				'toolbar' => 'full',
				'media_upload' => 'no',
			),
			array (
				'key' => 'field_569707be83244',
				'label' => __('Team Members','psp_projects'),
				'name' => 'team_members',
				'type' => 'repeater',
				'sub_fields' => array (
					array (
						'key' => 'field_569707c883245',
						'label' => __('Member','psp_projects'),
						'name' => 'team_member',
						'type' => 'user',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array (
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'role' => '',
						'allow_null' => 0,
						'multiple' => 0,
					),
				),
				'row_min' => '',
				'row_limit' => '',
				'layout' => 'row',
				'button_label' => __('Add Team Member','psp_projects'),
			),
		),
		'location' => array (
			array (
				array (
					'param' => 'post_type',
					'operator' => '==',
					'value' => 'psp_teams',
					'order_no' => 0,
					'group_no' => 0,
				),
			),
		),
		'options' => array (
			'position' => 'normal',
			'layout' => 'no_box',
			'hide_on_screen' => array (
			),
		),
		'menu_order' => 0,
	);

$team_fields = apply_filters( 'psp_team_fields' , $team_fields );

register_field_group( $team_fields ); ?>
