<?php
add_action( 'psp_between_phases_discussion', 'psp_fe_notification' );
function psp_fe_notification() {
    if( is_single() ) require_once( PSP_FE_BASE_DIR . '/lib/view/notifications.php' );
}

add_filter( 'psp_get_nav_items', 'psp_fe_nav_items' );
function psp_fe_nav_items( $nav_items ) {

    $new_nav_items = array();

    $allowed = apply_filters( 'psp_fe_back_button_allowed_pages', array(
        'new',
        'edit'
    ) );

    if( !is_single() || !current_user_can('edit_psp_projects') ) return $nav_items;

    global $post;
    $users        = psp_get_project_users();
    $edit_link    = ( PSP_FE_PERMALINKS ? 'manage/edit/' . $post->ID .'/' : '&psp_manage_page=edit&psp_manage_option=' . $post->ID );

    $new_nav_items['edit'] = array(
        'title' =>  __( 'Edit Project', 'psp-front-edit' ),
        'id'    =>  'nav-edit',
        'link'  =>  get_post_type_archive_link('psp_projects') . $edit_link,
        'icon'  =>  'psp-fi-edit'
    );

    $new_nav_items['access'] = array(
        'title' =>  __( 'Manage Access', 'psp-front-edit' ),
        'id'    =>  'nav-access',
        'link'  =>  get_post_type_archive_link('psp_projects') . $edit_link . '?section=access',
        'icon'  =>  'psp-fi-teams'
    );

    $new_nav_items['settings'] = array(
        'title' =>  __( 'Project Settings', 'psp-front-edit' ),
        'id'    =>  'nav-settings',
        'link'  =>  get_post_type_archive_link('psp_projects') . $edit_link . '?section=settings',
        'icon'  =>  'fa fa-gears'
    );

    if( !empty( $users ) ) {

        $new_nav_items['notifications'] = array(
            'title' =>  __( 'Send Notification', 'psp-front-edit' ),
            'id'    =>  'nav-notify',
            'link'  =>  '#psp-notify-users',
            'icon'  =>  'psp-fe-icon-mail psp-fi-icon',
            'class' =>  'psp-fe-notify-modal',
        );

	}

    $new_nav_items['delete'] = array(
        'title' =>  __( 'Delete Project', 'psp_projects' ),
        'id'    =>  'nav-delete',
        'link'  =>  '#',
        'atts'  =>  array(
            'data-postid'   =>  $post->ID,
            'data-redirect' =>  get_post_type_archive_link('psp_projects'),
        ),
        'icon'  =>  'psp-fi-icon-trash psp-fi-icon'
    );

    if( !empty($nav_items) ) {
        $new_nav_items = array_merge( $new_nav_items, $nav_items );
    }

    return $new_nav_items;

}

// add_action( 'psp_menu_items', 'psp_fe_nav_items' );
function psp_fe_nav_items_old() {

	global $post;

	$allowed = apply_filters( 'psp_fe_back_button_allowed_pages', array(
		'new',
		'edit'
	) );

	if( is_single() ) {

		$users        = psp_get_project_users();
        $edit_link    = ( PSP_FE_PERMALINKS ? 'manage/edit/' . $post->ID .'/' : '&psp_manage_page=edit&psp_manage_option=' . $post->ID );

        if( current_user_can( 'edit_psp_projects' ) ) {
	        echo '<li id="nav-edit"><a href="' . get_post_type_archive_link('psp_projects') . $edit_link . '"><i class="psp-fe-icon-edit psp-fi-icon"></i> ' . __( 'Edit Project', 'psp-front-edit' ) . '</a></li>';
            echo '<li id="nav-delete"><a href="#" data-postid="' . esc_attr($post->ID) . '" data-redirect="' . esc_url( get_post_type_archive_link('psp_projects') ) . '"><i class="psp-fi-icon-trash psp-fi-icon"></i>' . __( 'Delete Project', 'psp-front-edit' ) . '</a></li>';
        }

		if( !empty( $users ) ):
			echo '<li id="nav-notify"><a href="#psp-notify-users" class="psp-fe-notify-modal"><i class="psp-fe-icon-mail psp-fi-icon"></i> ' . __( 'Send Notification', 'psp-front-edit' ) . '</a></li>';
		endif;

	}

}

add_filter( 'psp_body_classes', 'psp_fe_body_classes' );
function psp_fe_body_classes( $classes ) {

    $conditions = array(
        'new',
        'edit'
    );

    if( in_array( get_query_var( 'psp_manage_page' ), $conditions ) ) {
        $classes .= ' psp-fe-manage-page-' . get_query_var('psp_manage_page');
    }

    if( isset($_GET['status']) && $_GET['status'] == 'new' ) {
        $classes .= ' psp-fe-manage-page-new';
    }

    return $classes;

}

add_filter( 'psp_section_nav_link_class', 'psp_section_nav_item_highlight', 10, 2 );
function psp_section_nav_item_highlight( $class, $slug ) {

    $conditions = array(
        'new',
        'edit'
    );

    if( in_array( get_query_var( 'psp_manage_page' ), $conditions ) ) {

        if( $slug == 'dashboard' ) return 'inactive';

        if( $slug == 'new-project' && get_query_var( 'psp_manage_page' ) == 'new' ) return 'active';

    }

    return $class;

}

add_action( 'psp_before_sub_navigation', 'psp_fe_section_nav_items' );
function psp_fe_section_nav_items( $items ) {

    if( current_user_can( 'publish_psp_projects' ) ) {

        $relative = ( is_post_type_archive() ? '' : '/' );

        $url  = ( PSP_FE_PERMALINKS ? get_post_type_archive_link('psp_projects') . 'manage/new/' : '&psp_manage_page=new' );

        echo wp_kses_post('<a href="' . $url . '" class="psp-btn psp-new-project-btn psp-pull-right psp-fi-icon psp-fi-add"><i clas="psp-fi-icon psp-fi-add"></i> ' . __( 'New Project', 'psp_projects' ) . '</a>');

    }

}

add_filter( 'psp_archive_query_vars', 'psp_fe_archive_query_vars' );
function psp_fe_archive_query_vars( $query_vars ) {

    if( get_query_var( 'psp_manage_page' ) == 'new' ) {
        $query_vars = array_merge( $query_vars, array( 'psp_manage_page' => __( 'New Project', 'psp_projects' ) ) );
    }

    if( get_query_var( 'psp_manage_page' ) == 'edit' ) {
        $query_vars = array_merge( $query_vars, array( 'psp_manage_page' => __( 'Edit Project', 'psp_projects' ) ) );
    }

    return $query_vars;

}

add_action( 'psp_after_task_list', 'psp_fe_add_new_task', 10, 2 );
function psp_fe_add_new_task( $post_id, $phase_index ) {

    if( !psp_can_edit_project($post_id) ) return; ?>

    <div class="psp-fe-add-task">
        <div class="psp-add-task-modal psp-modal" id="psp-add-task-<?php echo esc_attr($phase_index); ?>">
            <h2><?php esc_html_e( 'Add Task', 'psp_projects' ); ?></h2>

            <form method="" action="post" class="psp-frontend-form" data-postid="<?php echo esc_attr($post_id); ?>" data-parent="phase-<?php echo esc_attr($phase_index + 1); ?>" data-phase-id="<?php echo esc_attr($phase_index); ?>">
                <ol>
                <?php
                do_action( 'psp_before_add_task_fields', $post_id, $phase_index );
                do_action( 'psp_before_add_task_fields-' . $post_id . '-' . $phase_index );

                $hidden_fields = apply_filters( 'psp_fe_add_task_button_data', array(
                    'action'    =>  'psp_fe_update_task',
                    'phase_index'  =>  $phase_index,
                    'post_id'   =>  $post_id,
                    'task_index'   =>  'new',
					'phase_id'	=> '',
					'task_id'	=> '',
                ) );

                foreach( $hidden_fields as $name => $value ): ?>
                    <input type="hidden" name="<?php echo esc_attr($name); ?>" value="<?php echo esc_attr($value); ?>">
                <?php
                endforeach;

                $users = psp_get_project_users( $post_id );
                $user_options = array();
                if( $users ) {
                    foreach( $users as $user ) {
                        $user_options[$user['ID']] = psp_username_by_id($user['ID']);
                    }
                }

				// A user could be assigned from the Backend but these conditions not be met, which could cause the assignment to be wiped on update. So we instead make it a hidden field
				$show_assigned_field = get_field( 'restrict_access_to_specific_users', $post_id ) && ! empty( $user_options );

                $fields = apply_filters( 'psp_fe_add_task_fields', array(
                    array(
                        'name'          =>  'task',
                        'callback'      =>  'psp_field_type_text',
                        'label'         =>  __( 'Task Name', 'psp_projects' ),
                    ),
                    array(
                        'name'          => 'assigned',
                        'callback'      => $show_assigned_field ? 'psp_field_type_select' : 'psp_field_type_hidden',
                        'label'         => $show_assigned_field ? __( 'Assigned to', 'psp_projects' ) : '',
                        'options'       =>  $user_options,
                    ),
                    array(
                        'name'    =>  'due_date',
                        'callback'      =>  'psp_field_type_text',
                        'label'         =>  __( 'Due Date', 'psp_projects' ),
                        'attributes'    =>  array(
                            'class'     =>  'psp-datepicker'
                        )
                    )
                ) );

                foreach( $fields as $field ) {

                    do_action( 'psp_fe_add_task_fields_before_' . $field['name'], $post_id, $phase_index );

                    if( isset($field['condition']) && $field['condition'] == FALSE ) continue;

                    echo '<li>' . psp_get_frontend_field( $field ) . '</li>';

                    do_action( 'psp_fe_add_task_fields_after_' . $field['name'], $post_id, $phase_index );

                }

                do_action( 'psp_after_add_task_fields', $post_id, $phase_index );
                do_action( 'psp_after_add_task_fields-' . $post_id . '-' . $phase_index );

                ?>
                </ol>

                <div class="psp-modal-actions">
                    <p class="pano-modal-add-btn">
                        <input type="submit" class="pano-btn pano-btn-primary" value="<?php esc_attr_e( 'Add Task', 'psp_projects' ); ?>">
                        <a class="modal-close" href="#"><?php esc_html_e( 'Cancel', 'psp_projects' ); ?></a>
                        <a class="fe-del-task-link psp-hide" href="#" data-confirmation="<?php esc_html_e( 'Are you sure you want to delete this task?', 'psp_projects' ); ?>"><i class="fa fa-trash"></i> <?php esc_html_e( 'Delete', 'psp_projects' ); ?></a>
                    </p>
                </div>

            </form>
        </div>
        <a class="pano-btn psp-modal-btn psp-fe-add-element" href="#psp-add-task-<?php echo esc_attr($phase_index); ?>" data-modal_title="<?php esc_attr_e( 'Add Task','psp_projects' ); ?>" data-submit_label="<?php esc_attr_e( 'Add Task', 'psp_projects' ); ?>"><?php esc_html_e( 'Add Task', 'psp_projects' ); ?></a>
    </div>

    <?php
}

function psp_the_frontend_field( $field ) {
    echo wp_kses_post( psp_get_frontend_field($field) );
}

function psp_get_frontend_field( $field = NULL ) {

    if( $field == NULL ) return false;

    ob_start(); ?>

    <div class="psp-frontend-field">

        <?php
        if( isset($field['label']) ) echo '<label for="' . esc_attr($field['name']) . '">' . esc_html($field['label']) . '</label>';

        echo call_user_func( $field['callback'], $field ); ?>

    </div>

    <?php
    return ob_get_clean();

}

function psp_field_extract_attributes( $attributes = NULL ) {

    if( $attributes == NULL ) return false;

    $atts = '';
    foreach( $attributes as $attribute => $value ) {
        $atts .= $attribute .= '="' . $value . '" ';
    }

    return $atts;

}

function psp_field_type_text( $field ) {

    $atts = '';
    if( isset( $field['attributes'] ) ) $atts = psp_field_extract_attributes( $field['attributes'] );

    return '<input type="text" data-name="field-' . $field['name'] . '" name="' . $field['name'] . '" ' . $atts . '>';

}

function psp_field_type_select( $field ) {

    $atts = '';
    if( isset( $field['attributes'] ) ) $atts = psp_field_extract_attributes( $field['attributes'] );

    ob_start(); ?>

    <div class="psp-select-wrapper">
        <select type="text" data-name="field-<?php echo esc_attr($field['name']); ?>" name="<?php echo esc_attr($field['name']); ?>" <?php echo $atts; ?>>
            <option value=""></option>
            <?php
            if( isset($field['options']) ): foreach( $field['options'] as $option => $value ): ?>
                <option value="<?php echo esc_attr($option); ?>" <?php if( isset( $field['value'] ) && $field['value'] === $option ) echo 'selected'; ?>><?php echo esc_html( $value ); ?></option>
            <?php endforeach; endif; ?>
        </select>
    </div>

    <?php
    return ob_get_clean();

}

function psp_field_type_wysiwyg( $field ) {

    $atts = '';
    if( isset( $field['attributes'] ) ) $atts = psp_field_extract_attributes( $field['attributes'] );

    $value = ( isset($field['value']) ? $field['value'] : '' );

    ob_start(); ?>

    <textarea name="<?php echo esc_attr($field['name']); ?>" id="<?php echo esc_attr('field-' . $field['name']); ?>" <?php echo $atts; ?>><?php echo wp_kses_post($value); ?></textarea>

    <?php

    return ob_get_clean();

}

function psp_field_type_hidden( $field ) {

    $atts = '';
    if( isset( $field['attributes'] ) ) $atts = psp_field_extract_attributes( $field['attributes'] );

    return '<input type="hidden" data-name="field-' . $field['name'] . '" name="' . $field['name'] . '" ' . $atts . ' value="' . $field['value'] . '">';

}

add_action( 'psp_localize', 'psp_fe_delete_project_message' );
function psp_fe_delete_project_message() {
    echo 'var psp_fe_delete_project_message = "' . __( 'Are you sure you want to delete this project?', 'psp_projects' ) . '";';
}

add_filter( 'psp_project_edit_post_link', 'psp_fe_custom_edit_post_link' );
function psp_fe_custom_edit_post_link( $link ) {

    global $post;
    return get_post_type_archive_link('psp_projects') . ( PSP_FE_PERMALINKS ? 'manage/edit/' . $post->ID .'/' : '&psp_manage_page=edit&psp_manage_option=' . $post->ID );

}

// add_filter( 'psp_body_classes_array', 'psp_fe_custom_body_classes' );
function psp_fe_custom_body_classes( $classes = array() ) {

    if( get_query_var( 'psp_manage_page' ) ) $classes[] = 'psp-fe-manage-template';

    if( get_query_var('psp_manage_page') == 'edit' ) $classes[] = 'psp-fe-edit-project';

    return $classes;

}

add_filter( 'psp_body_classes', 'psp_fe_body_classes_backwards_compatibility' );
function psp_fe_body_classes_backwards_compatibility( $classes ) {

    if( get_query_var( 'psp_manage_page' ) ) $classes .= ' psp-fe-manage-template';
    if( get_query_var('psp_manage_page') == 'edit' ) $classes .= ' psp-fe-edit-project';

    if( isset($_GET['status']) && get_query_var('psp_manage_page') == 'edit' ) $classes .= ' psp-fe-edit-project-status-' . $_GET['status'];

    return $classes;

}

add_filter( 'ajax_query_attachments_args', 'psp_fe_only_show_current_user_attachments', 10, 1 );
function psp_fe_only_show_current_user_attachments( $query = array() ) {

    if( !psp_get_option('psp_restrict_media_gallery') ) return $query;

    if( current_user_can('edit_others_psp_projects') || $query['post_parent'] != 'new' ) return $query;

    $user_id = get_current_user_id();
    if( $user_id ) $query['author'] = $user_id;

    return $query;

}

add_filter( 'psp_get_the_title', 'psp_fe_custom_page_titles' );
function psp_fe_custom_page_titles( $title ) {

    if( is_post_type_archive('psp_projects') && get_query_var('psp_manage_page') == 'edit' ) {
        $cuser      = wp_get_current_user();
        $title = __( 'Editing', 'psp_project' ) . ' ' . get_the_title( get_query_var('psp_manage_option') );
        if( is_user_logged_in() ) $title .= ' | ' . psp_get_nice_username_by_id( $cuser->ID );
    }

    if( is_post_type_archive('psp_projects') && get_query_var('psp_manage_page') == 'new' ) {
        $cuser      = wp_get_current_user();
        $title = __( 'New Project', 'psp_projects' );
        if( is_user_logged_in() ) $title .= ' | ' . psp_get_nice_username_by_id( $cuser->ID );
    }

    return $title;

}

add_action( 'psp_after_task_name', 'psp_fe_manage_task_links', 10, 5 );
function psp_fe_manage_task_links( $post_id, $phase_index, $task_index, $phases, $phase ) {

    if( !psp_can_edit_project($post_id) || is_post_type_archive('psp_projects') ) return;

    $date_format_opt 	= get_option( 'date_format' );
    $date_format        = ( substr( $date_format_opt, 0, 1 ) == 'd' ? 'd/m/Y' : 'm/d/Y' );
    $date = ( isset($phases[$phase_index]['tasks'][$task_index]['due_date']) && !empty($phases[$phase_index]['tasks'][$task_index]['due_date']) ? date( $date_format, strtotime($phases[$phase_index]['tasks'][$task_index]['due_date']) ) : '' );

    $edit_data = apply_filters( 'psp_fe_edit_task_values', array(
        'post_id'   =>  $post_id,
        'phase_index'  =>  $phase_index,
        'task_index'   =>  $task_index,
		'phase_id'  =>  $phases[$phase_index]['phase_id'],
        'task_id'   =>  $phases[$phase_index]['tasks'][$task_index]['task_id'],
        'modal_title'   =>  __( 'Edit Task', 'psp_projects' ),
        'task'      =>  $phases[$phase_index]['tasks'][$task_index]['task'],
        'assigned'  =>  $phases[$phase_index]['tasks'][$task_index]['assigned'],
        'due_date'  =>  $date,
        'submit_label'  =>  __( 'Save', 'psp_projects' )
    ), $post_id, $phase_index, $task_index, $phases, $phase );

    $html = '<a href="#psp-add-task-' . esc_attr($phase_index) . '" class="fe-edit-task-link psp-modal-btn" ';

    foreach( $edit_data as $attribute => $value ) {
        $html .= 'data-' . $attribute . '="' . esc_attr($value) . '" ';
    }

    $html .= '><i class="fa fa-pencil psp-fe-edit-icon"></i></a>';

    echo $html;

}

function psp_fe_get_milestone_markers() {

    return apply_filters( 'psp_fe_milestone_markers', array(
        'overview'      =>  array(
            'title'     =>  __( 'Overview', 'psp-front-edit' ),
            'step'      =>  '1',
            'type'      =>  'tab',
            'complete'  =>  '8',
            'section'   =>  'overview',
            'target'    =>  'acf_acf_overview',
            'acf5'      =>  'overview'
        ),
        'documents'     => array(
            'title'     =>  __( 'Documents', 'psp-front-edit' ),
            'step'      =>  '2',
            'type'      =>  'tab',
            'complete'  =>  '24.66',
            'section'   =>  'documents',
            'target'    =>  'acf_acf_overview',
            'acf5'      =>  'overview'
        ),
        'access'        =>  array(
            'title'     =>  __( 'Access', 'psp-front-edit' ),
            'step'      =>  '3',
            'type'      =>  'tab',
            'complete'  =>  '41.33',
            'section'   =>  'access',
            'target'    =>  'acf_acf_overview',
            'acf5'      =>  'overview'
        ),
        'settings'      =>  array(
            'title'     =>  __( 'Settings', 'psp-front-edit' ),
            'step'      =>  '4',
            'type'      =>  'tab',
            'complete'  =>  '58',
            'section'   =>  'settings',
            'target'    =>  'acf_acf_overview',
            'acf5'      =>  'overview'
        ),
        'milestones'    =>  array(
            'title'     =>  __( 'Milestones', 'psp-front-edit' ),
            'step'      =>  '5',
            'type'      =>  'section',
            'complete'  =>  '74.5',
            'section'   =>  'milestones',
            'target'    =>  'acf_acf_psp_milestones',
            'acf5'      =>  'milestones'
        ),
        'phases'        =>  array(
            'title'     =>  __( 'Phases & Tasks', 'psp-front-edit' ),
            'step'      =>  '6',
            'type'      =>  'section',
            'complete'  =>  '91.2',
            'section'   =>  'phases',
            'target'    =>  'acf_acf_phases',
            'acf5'      =>  'phases'
        ),
    ) );

}

function psp_fe_get_edit_sections() {

    return apply_filters( 'psp_fe_edit_sections', array(
        'overview',
        'documents',
        'access',
        'settings',
        'milestones',
        'phases'
    ) );

}

add_action( 'psp_after_milestone_title', 'psp_fe_milestone_edit_link', 10, 3 );
function psp_fe_milestone_edit_link( $all_milestones, $completed, $post_id ) {

    $cuser = wp_get_current_user();

    if( psp_can_edit_project($cuser->ID) ) { ?>
        <span class="psp-fe-edit-link psp-fe-edit-milestones"><span class="psp-pipe psp-fe-pipe">|</span> <a href="<?php echo esc_url( get_post_type_archive_link('psp_projects') . '/manage/edit/' . $post_id . '?section=milestones'); ?>"><i class="fa fa-pencil"></i> <?php esc_html_e( 'Edit Milestones', 'psp-front-edit' ); ?></a></span>
    <?php
    }

}

add_action( 'psp_after_phases_title', 'psp_fe_phases_edit_link', 10, 2 );
function psp_fe_phases_edit_link( $phases, $post_id ) {

    $cuser = wp_get_current_user();

    if( psp_can_edit_project($cuser->ID) ) { ?>
        <span class="psp-fe-edit-link psp-fe-edit-phases"><span class="psp-pipe psp-fe-pipe">|</span> <a href="<?php echo esc_url( get_post_type_archive_link('psp_projects') . '/manage/edit/' . $post_id . '?section=phases'); ?>"><i class="fa fa-pencil"></i> <?php esc_html_e( 'Edit Phases', 'psp-front-edit' ); ?></a></span>
    <?php
    }

}

add_action( 'psp_after_project_title', 'psp_fe_edit_project_overview', 10, 1 );
function  psp_fe_edit_project_overview( $post_id ) {

    $cuser = wp_get_current_user();

    if( psp_can_edit_project($cuser->ID) ) { ?>
        <span class="psp-fe-edit-link psp-fe-edit-overview"><span class="psp-pipe psp-fe-pipe">|</span> <a href="<?php echo esc_url( get_post_type_archive_link('psp_projects') . '/manage/edit/' . $post_id ); ?>"><i class="fa fa-pencil"></i> <?php esc_html_e( 'Edit Overview', 'psp-front-edit' ); ?></a></span>
    <?php }

}

add_action( 'psp_before_document_section_title', 'psp_fe_edit_documents_link', 100, 1 );
function psp_fe_edit_documents_link( $post_id ) {

    $cuser = wp_get_current_user();

    if( psp_can_edit_project($cuser->ID) ) { ?>
        <p class="psp-fe-edit-link psp-fe-edit-documents"><a href="<?php echo esc_url( get_post_type_archive_link('psp_projects') . '/manage/edit/' . $post_id . '?section=documents' ); ?>"><i class="fa fa-pencil"></i> <?php esc_html_e( 'Edit Documents', 'psp-front-edit' ); ?></a></p>
    <?php }

}

add_action( 'psp_before_document_title', 'psp_fe_delete_document_link', 10, 1 );
function psp_fe_delete_document_link( $post_id ) {

    $cuser = wp_get_current_user();

    if( !psp_can_edit_project($cuser->ID) ) {
        return;
    }

    echo '<i class="fa fa-trash js-psp-delete-document psp-fe-edit-icon" data-project="' . esc_attr($post_id) . '" data-confirm="' . esc_attr(__( 'Are you sure you want to delete this document?', 'psp_projects' )) . '"></i>';

}

add_action( 'psp_timing_before_start_date', 'psp_fe_timing_edit_link', 10, 3 );
function psp_fe_timing_edit_link( $post_id ) {

    $cuser      = wp_get_current_user();

    if( !psp_can_edit_project($cuser->ID) ) {
        return;
    } ?>

    <i class="fa fa-pencil js-psp-edit-date psp-fe-edit-icon"></i>

    <?php

}

add_action( 'before_project_description', 'psp_fe_edit_project_description' );
function psp_fe_edit_project_description() {

    if( !psp_can_edit_project($cuser->ID) ) {
        return;
    }

    echo '<a class="fa fa-pencil js-psp-edit-description psp-fe-edit-icon" href="#psp-edit-description-modal" data-post_id="' . get_the_ID() . '"></a>';

}

add_action( 'after_project_description', 'psp_fe_edit_project_description_modal' );
function psp_fe_edit_project_description_modal() { ?>

    <div class="psp-modal psp-hide" id="psp-edit-description-modal">

        <div class="psp-loading"></div>

        <h2>
            <span><?php esc_html_e( 'Edit Description', 'psp-front-edit'); ?></span>
            <strong></strong>
        </h2>
        <form class="js-psp-edit-description-form" method="post" action="">
            <input type="hidden" name="post_id" value="<?php echo get_the_ID(); ?>">
            <?php
            $fields = apply_filters( 'psp_fe_edit_description_fields', array(
                array(
                    'name'    =>  'description',
                    'callback'      =>  'psp_field_type_wysiwyg',
                    'label'         =>  __( 'Description', 'psp-front-edit' ),
                    'value'         => get_field('project_description')
                )
            ) ); ?>
            <ol>
                <?php
                foreach( $fields as $field ) {

                    do_action( 'psp_fe_edit_description_fields_before_' . $field['name'], $post_id, $phase_index );

                    if( isset($field['condition']) && $field['condition'] == FALSE ) continue;

                    echo '<li>' . psp_get_frontend_field( $field ) . '</li>';

                    do_action( 'psp_fe_edit_description_fields_after_' . $field['name'], $post_id, $phase_index );

                } ?>
                <?php
                do_action( 'psp_after_description_fields', $post_id, $phase_index );
                do_action( 'psp_after_description_fields-' . $post_id . '-' . $phase_index ); ?>
                </ol>

                <div class="psp-modal-actions">
                    <p class="pano-modal-add-btn">
                        <input type="submit" class="pano-btn pano-btn-primary" value="<?php esc_attr_e( 'Save', 'psp-front-edit' ); ?>">
                        <a class="modal-close" href="#"><?php esc_html_e( 'Cancel', 'psp-front-edit' ); ?></a>
                    </p>
                </div>

        </form>
    </div>

    <?php

}

add_action( 'psp_timing_after_dates', 'psp_fe_timing_edit_start_date', 10, 3 );
function psp_fe_timing_edit_start_date( $post_id, $start_date = null, $end_date = null) {

    $cuser = wp_get_current_user();

    if( !psp_can_edit_project($cuser->ID) ) {
        return;
    }

    if( $start_date ) {
        $start_date = psp_get_the_start_date( NULL, $post_id );
        $start_date = date( 'm/d/Y', strtotime($start_date) );
    } else {
        $start_date = date( 'm/d/Y');
    }

    if( $end_date ) {
        $end_date   = psp_get_the_end_date( NULL, $post_id );
        $end_date   = date( 'm/d/Y', strtotime($end_date) );
    } else {
        $end_date = date( 'm/d/Y' );
    } ?>

    <div class="psp-fe-edit-date-input">
        <form method="" action="post" class="psp-js-date-update">
            <?php do_action( 'psp_fe_before_edit_date_inputs', $post_id, $start_date, $end_date ); ?>
            <input type="hidden" name="psp-post-id" class="psp-post-id" value="<?php echo esc_attr($post_id); ?>">
            <div class="psp-date-label">
                <label for="psp-start-date">
                    <?php esc_html_e( 'Start Date', 'psp_projects' ); ?>
                </label>
                <input type="text" value="<?php echo esc_attr($start_date); ?>" name="psp-start-date" class="psp-datepicker psp-fe-project-date-field">
            </div>
            <div class="psp-date-label">
                <label for="psp-end-date">
                    <?php esc_html_e( 'End Date', 'psp_projects' ); ?>
                </label>
                <input type="text" value="<?php echo esc_attr($end_date); ?>" name="psp-end-date" class="psp-datepicker psp-fe-project-date-field">
            </div>
            <?php do_action( 'psp_fe_after_date_inputs', $post_id, $start_date, $end_date ); ?>
            <input type="submit" class="pano-btn" value="Save">
        </form>
    </div>

    <?php
}

add_action( 'psp_after_all_phases', 'psp_fe_phase_edit_modal' );
function psp_fe_phase_edit_modal() {

    $post_id = get_the_ID(); ?>

    <div class="psp-modal psp-hide" id="psp-edit-phases-modal">

        <div class="psp-loading"></div>

        <h2>
            <span><?php esc_html_e( 'Edit Phase', 'psp-front-edit'); ?></span>
            <strong></strong>
        </h2>
        <form class="js-psp-edit-phases-form" method="post" action="">
            <input type="hidden" name="psp-post-id" value="<?php echo get_the_ID(); ?>">
            <input type="hidden" name="psp-progress-type" value="<?php the_field( 'progress_type', $post_id ); ?>">
            <input type="hidden" name="phase_index" value="">
            <?php

            $project_type   = get_field( 'progress_type', $post_id );
            $progress_field = array(
                'name'      => '',
                'callback'  => 'psp_field_type_text',
                'label'     =>  '',
                'value'     =>  ''
            );

            switch( $project_type ) {
                case( 'Weighting' ):
                    $progress_field['name'] = 'weight';
                    $progress_field['label'] = __( 'Phase Weight', 'psp-front-edit' );
                    break;
                case( 'Hours' ):
                    $progress_field['name'] = 'hours';
                    $progress_field['label'] = __( 'Phase Hours', 'psp-front-edit' );
                    break;
                case( 'Percentage' ):
                    $progress_field['name'] = 'percentage';
                    $progress_field['label'] = __( 'Phase Percentage', 'psp-front-edit' );
                    break;
            }


            $fields = apply_filters( 'psp_fe_edit_phase_fields', array(
                array(
                    'name'    =>  'phase-title',
                    'callback'      =>  'psp_field_type_text',
                    'label'         =>  __( 'Title', 'psp-front-edit' ),
                ),
                array(
                    'name'      =>  'post_id',
                    'callback'  =>  'psp_field_type_hidden',
                    'value'     =>  $post_id
                ),
                $progress_field,
                array(
                    'name'    =>  'phase-description',
                    'callback'      =>  'psp_field_type_wysiwyg',
                    'label'         =>  __( 'Description', 'psp-front-edit' ),
                )
            ) );
            ?>
            <ol>
                <?php
                foreach( $fields as $field ) {

                    do_action( 'psp_fe_edit_phases_fields_before_' . $field['name'], $post_id, $phase_index );

                    if( isset($field['condition']) && $field['condition'] == FALSE ) continue;

                    echo '<li>' . psp_get_frontend_field( $field ) . '</li>';

                    do_action( 'psp_fe_edit_phases_fields_after_' . $field['name'], $post_id, $phase_index );

                } ?>
                <?php
                do_action( 'psp_after_add_task_fields', $post_id, $phase_index );
                do_action( 'psp_after_add_task_fields-' . $post_id . '-' . $phase_index ); ?>
                </ol>

                <div class="psp-modal-actions">
                    <p class="pano-modal-add-btn">
                        <input type="submit" class="pano-btn pano-btn-primary" value="<?php esc_attr_e( 'Save', 'psp-front-edit' ); ?>">
                        <a class="modal-close" href="#"><?php esc_html_e( 'Cancel', 'psp-front-edit' ); ?></a>
                    </p>
                </div>

        </form>
    </div>
<?php
}

add_action( 'psp_before_phase_title', 'psp_fe_edit_phase_link', 10, 4 );
function psp_fe_edit_phase_link( $post_id, $phase_index, $phases, $phase ) {

    $cuser = wp_get_current_user();

    if( !psp_can_edit_project($cuser->ID) ) {
        return;
    } ?>

    <a class="fa fa-pencil js-psp-edit-phase psp-fe-edit-icon" href="#psp-edit-phases-modal" data-post_id="<?php echo esc_attr($post_id); ?>" data-phase_index="<?php echo esc_attr($phase_index); ?>"></a>

    <?php
}

add_action( 'psp_timing_before_start_date', 'psp_fe_start_timing_edit_link', 10, 1 );
function psp_fe_start_timing_edit_link( $post_id ) {

    $cuser = wp_get_current_user();

    if( !psp_can_edit_project($cuser->ID) ) {
        return;
    }

    echo '<i class="fa fa-pencil js-psp-edit-date" data-project="' . esc_attr($post_id) . '" data-date="start"></i>';

}

add_action( 'psp_timing_before_end_date', 'psp_fe_end_timing_edit_link', 10, 1 );
function psp_fe_end_timing_edit_link( $post_id ) {

    $cuser = wp_get_current_user();

    if( !psp_can_edit_project($cuser->ID) ) {
        return;
    }

    echo '<i class="fa fa-pencil js-psp-edit-date" data-project="' . esc_attr($post_id) . '" data-date="end"></i>';

}

add_action( 'psp_timing_no_dates', 'psp_fe_no_date_set' );
function psp_fe_no_date_set( $post_id ) {

    $cuser = wp_get_current_user();

    if( !psp_can_edit_project($cuser->ID) ) {
        return;
    }

    if( get_field('start_date', $post_id) || get_field('end_date', $post_id) ) {
        return;
    }

    echo '<a href="#" class="js-psp-fe-set-dates" data-project="' . esc_attr($post_id) . '">' . __( 'Set Project Dates', 'psp_projects' ) . '</a>'; ?>

    <div class="psp-col-sm-6 psp-archive-list-dates psp-the-start-date psp-hide">
            <?php do_action( 'psp_timing_before_start_date', $post_id, $start_date ); ?>
            <h5><?php esc_html_e( 'Start Date', 'psp_projects' ); ?></h5>
            <p></p>
            <?php do_action( 'psp_timing_after_start_date', $post_id, $start_date ); ?>
    </div>
    <div class="psp-col-sm-6 psp-archive-list-dates psp-the-end-date psp-hide">
        <?php do_action( 'psp_timing_before_end_date', $post_id, $end_date ); ?>
        <h5><?php esc_html_e( 'End Date', 'psp_projects' ); ?></h5>
        <p></p>
        <?php do_action( 'psp_timing_after_end_date', $post_id, $end_date ); ?>
    </div>

    <?php
}
