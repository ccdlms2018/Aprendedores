<?php
add_action( 'init', 'psp_fe_create_page_custom_rewrite_rule', 20, 0 );
function psp_fe_create_page_custom_rewrite_rule() {

	global $wp_rewrite;

	$slug = psp_get_option( 'psp_slug', 'panorama' );

	if( isset( $wp_rewrite->front ) ) $slug = substr( $wp_rewrite->front, 1 ) . $slug;

  	add_rewrite_rule( '^' . $slug . '/manage(/([^/]+))?(/([^/]+))?/?', 'index.php?post_type=psp_projects&psp_manage_page=$matches[2]&psp_manage_option=$matches[4]', 'top' );

	if( get_option( 'psp_fe_database' > PSP_FE_DB_VER ) ) {
		flush_rewrite_rules();
		update_option( 'psp_fe_database', PSP_FE_DB_VER );
	}

}

add_filter( 'query_vars', 'psp_fe_management_query_vars' );
function psp_fe_management_query_vars( $vars ) {

	$vars[] = 'psp_manage_page';
	$vars[] = 'psp_manage_option';

	return $vars;

}

add_action( 'psp_head', 'psp_fe_acf_form_head' );
function psp_fe_acf_form_head() {

	if( !get_query_var('psp_manage_page') ) return;

	show_admin_bar( false );
	acf_form_head();
	wp_head();

}

add_filter( 'psp_custom_archive_templates', 'psp_fe_custom_template_management_templates' );
function psp_fe_custom_template_management_templates( $templates ) {

	switch( get_query_var('psp_manage_page') ) {
		case('new'):
			$template = PSP_FE_BASE_DIR . '/lib/view/new/index.php';
			break;
		case('edit'):
			$template = PSP_FE_BASE_DIR . '/lib/view/edit/index.php';
			break;
		case('duplicate'):
			$template = PSP_FE_BASE_DIR . '/lib/view/duplicate/index.php';
			break;
	}

	if( isset($template) ) {
		$templates[] = array(
			'query_var' =>  'psp_manage_page',
			'template' 	=>  $template
		);
	}

	return $templates;

}

add_action( 'template_redirect', 'psp_fe_management_templates' );
function psp_fe_management_templates( $template ) {

	if( psp_get_option( 'psp_use_custom_template') ) return $template;

	if( isset( $_POST[ 'return' ] ) && get_query_var( 'psp_manage_page' ) ) wp_redirect( $_POST[ 'return' ] );

	if( ( get_query_var( 'psp_manage_page' ) ) && ( is_post_type_archive( 'psp_projects' ) ) ) {

		$slugs = array(
			'new',
			'edit',
			'duplicate',
		);

		$priority = ( psp_get_option('psp_use_custom_template') ? 99 : 10001 );

		foreach( $slugs as $slug ) {
			if( $slug == get_query_var( 'psp_manage_page' ) ) add_filter( 'template_include', 'psp_fe_return_' . $slug . '_template', $priority );
		}

	}

}

function psp_fe_return_new_template() {
	return apply_filters( 'psp_fe_new_template_path', PSP_FE_BASE_DIR . '/lib/view/new/index.php' );
}

function psp_fe_return_edit_template() {
	return apply_filters( 'psp_fe_edit_template_path', PSP_FE_BASE_DIR . '/lib/view/edit/index.php' );
}

function psp_fe_return_duplicate_template() {
	return apply_filters( 'psp_fe_edit_template_path', PSP_FE_BASE_DIR . '/lib/view/duplicate/index.php' );
}

function psp_fe_is_editable_post( $post_id = null ) {

	if( empty( $post_id ) || get_post_type( $post_id ) != 'psp_projects' ) return false;

	return true;

}

add_action( 'acf/save_post', 'psp_fe_update_poststamp' );
function psp_fe_update_poststamp( $post_id ) {

	if( get_post_type($post_id) != 'psp_projects' ) {
		return;
	}

	$post = array(
		'ID'	=>	$post_id
	);
	wp_update_post($post);

}

add_filter( 'acf/pre_save_post' , 'psp_fe_pre_save_post', 100, 1 );
function psp_fe_pre_save_post( $post_id ) {

	/**
	 * If this is not a new post, check to see if the title is being updated
	 *
	 */
	if( $post_id != 'new_post' && $post_id != 'new' ) {

		$post = array(
			'ID'	=>	$post_id,
		);

		if( isset( $_POST['psp_acf4_title'] ) ) {

			$title = $_POST['psp_acf4_title'];

			if( $title != get_the_title( $post_id ) ) {

				$post = array(
					'ID'		 =>	$post_id,
					'post_title' =>	$_POST['psp_acf4_title'],
					'post_name'	 => ''
				);
				wp_update_post($post);

			}

			$_POST['return'] = get_permalink($post_id);

		}

		// Not updating a post so bail
		return $post_id;

	}

	// vars
	$title	= $_POST[ 'psp_acf4_title' ];
	$slug 	= psp_get_option( 'psp_slug', 'panorama' );

	// Create a new post
	$post = array(
		'post_status'	=> 'publish',
		'post_type'		=> 'psp_projects',
		'post_title'	=> $title,
		'post_name'		=>	'',
	);

	// insert the post
	$post_id = wp_insert_post( $post );

	if( PSP_FE_PERMALINKS ) {
		$_POST[ 'return' ] = get_site_url() . '/' . $slug . '/manage/edit/' . $post_id . '/?status=new';
	} else {
		$_POST['return'] = get_site_url() . '/?post_type=psp_projects&psp_manage_page=edit&psp_manage_option=' . $post_id;
	}

	// return the new ID

	return $post_id;

}

// Hack to try and prevent Yoast SEO from causing chaos
add_filter( 'psp_project_post_type_args', 'psp_fe_undo_yoast' );
function psp_fe_undo_yoast( $args ) {

	if( class_exists('WPSEO_Link_Watcher') && is_user_logged_in() ) {
		$args['public'] = false;
	}
	return $args;

}

function psp_fe_acf_form( $post_id = NULL ) {

	$status				= ( isset( $_GET[ 'status' ] ) ? $_GET[ 'status' ] : null );
	$button_text		= ( $post_id != NULL ? __( 'Save', 'psp-front-edit' ) : __( 'Save & Continue', 'psp-front-edit' ) );
	$field_groups 		= array( 'acf_overview' );
	$slug 				= psp_get_option( 'psp_slug', 'panorama' );
	$html_before_fields	= '';

	if( PSP_ACF_VER == 4 ) {

		$post_id 	= ( $post_id == NULL ? 'new' : $post_id );
		$options	= array();
		$title		= ( $post_id == 'new' ? '' : get_the_pspf_title( $post_id ) );

		$html_before_fields .= '
			<div class="acf_postbox psp_fe_title_fields">
				<div class="field">
					<p class="label"><label for="acf-field-title">' . __( 'Project Title', 'psp-front-edit' ) . '</label></p>
					<input type="text" class="text" name="psp_acf4_title" value="' . $title .'" placeholder="' . __( 'Enter Project Title', 'psp_projects' ) . '">
					<br>
				</div>
			</div>';


		if( $post_id != 'new' ) {
			$field_groups[] = 'acf_psp_milestones';
			$field_groups[] = 'acf_phases';
		}

		$options = array(
			'post_id'				=> $post_id,
			'field_groups'			=> apply_filters( 'psp_fe_acf4_field_groups', $field_groups ),
			'post_title'	    	=> 'true',
			'submit_value'	    	=> $button_text,
			'form_attributes'   	=> array( 'autocomplete' => 'off' ),
			'html_before_fields'	=> apply_filters( 'psp_fe_acf4_before_fields', $html_before_fields )
		);

		if( $post_id != 'new' && $status != 'template' ) {
			$options = array_merge( $options, array( 'return' => get_the_permalink( $post_id ) ) );
		}

		$options = apply_filters( 'psp_fe_acf4_new_form', $options );

		acf_form($options);

	} elseif ( PSP_ACF_VER == 5 ) {

		$post_id 		= ( $post_id == NULL ? 'new_post' : $post_id );
		$redirect		= ( $post_id != 'new_post' ? '%post_url%#new' : '%post_url%?action=step2' );
		$title			= ( $post_id == 'new_post' ? '' : get_the_pspf_title( $post_id ) );

		if( $post_id != 'new_post' || $status == 'template' ) {
			$field_groups[] = 'group_563d1e4aac15c';
			$field_groups[] = 'acf_phases';
		}

		$field_sets = apply_filters( 'acf_fe_acf5_field_sets', array(
			'overview'	=>	array(
				'id'	=>	'acf_acf_overview',
				'group'	=>	'acf_overview',
			),
			'milestones'	=>	array(
				'id'	=>	'acf_acf_psp_milestones',
				'group'	=>	'group_563d1e4aac15c'
			),
			'phases'		=>	array(
				'id'	=>	'acf_acf_phases',
				'group'	=>	'acf_phases'
			)
		) ); ?>

		<form id="post" class="acf-form" action="" method="post">
			<input type="hidden" name="_acf_post_id" value="<?php echo esc_attr($post_id); ?>">
			<input type="hidden" name="_acf" value="">
			<div class="acf_postbox psp_fe_title_fields">
				<div class="field">
					<p class="label"><label for="acf-field-title"><?php esc_html_e( 'Project Title', 'psp-front-edit' ); ?></label></p>
					<input type="text" class="text" name="psp_acf4_title" value="<?php echo esc_attr($title); ?>" placeholder="<?php esc_html_e( 'Enter Project Title', 'psp_projects' ); ?>">
					<br>
				</div>
			</div>
			<?php
			foreach( $field_sets as $set ):

				$args = apply_filters( 'psp_fe_fieldset_' . $set['id'], array(
					'post_id'		=>	$post_id,
					'field_groups'	=>	array($set['group']),
					'form'			=>	false,
					'return'		=>	$redirect,
				) ); ?>

				<div id="<?php echo esc_attr($set['id']); ?>">
					<?php acf_form($args); ?>
				</div>

			<?php endforeach; ?>

			<div class="acf-form-submit">
				<input type="submit" class="acf-button button button-primary button-large" value="<?php echo esc_attr($button_text); ?>">
				<span class="acf-spinner"></span>
			</div>

		</form>
		<?php
		/*
		$options = apply_filters( 'psp_fe_acf5_new_form', array(
			'post_id'   =>  $post_id,
			'new_post'  =>  array(
				'post_type'     =>  'psp_projects',
				'post_status'   =>  'publish'
			),
			'field_groups'		=> apply_filters( 'psp_fe_acf5_field_groups', $field_groups ),
			'return'            => apply_filters( 'psp_fe_acf5_save_redirect', $redirect ),
			'post_title'	    => 'true',
			'submit_value'	    => $button_text,
			'form_attributes'   => array( 'autocomplete' => 'off' ),
		) ); */

	}

	// return $options;

}

add_action( 'template_redirect', 'psp_fe_check_for_edit_redirect' );
function psp_fe_check_for_edit_redirect() {

	if( ( get_post_type() != 'psp_projects' ) || ( !isset( $_GET[ 'action' ] ) ) ) return;

	global $post;

	$action 	= $_GET[ 'action' ];
	$slug 		= psp_get_option( 'psp_slug', 'panorama' );

	if( $action == 'step2' ) {

		wp_redirect( get_site_url() . '/' . $slug . '/manage/edit/' . $post->ID . '?status=new'  );
		exit();

	}

}

function psp_fe_get_current_url() {

    $currentURL = (@$_SERVER["HTTPS"] == "on") ? "https://" : "http://";
    $currentURL .= $_SERVER["SERVER_NAME"];

    if($_SERVER["SERVER_PORT"] != "80" && $_SERVER["SERVER_PORT"] != "443") {
        $currentURL .= ":".$_SERVER["SERVER_PORT"];
    }

    $currentURL .= $_SERVER["REQUEST_URI"];

	return $currentURL;

}

/**
 * Global Template Metabox
 */
add_action( 'post_submitbox_misc_actions', 'psp_fe_global_template_metabox' );
function psp_fe_global_template_metabox() {

	global $post;

	if ( get_post_type( $post ) == 'psp_projects' ) { ?>

		<div class="misc-pub-section misc-pub-section-last" style="border-top: 1px solid #eee;">

			<?php
			wp_nonce_field( plugin_basename( __FILE__ ), 'psp_fe_global_template_nonce' );
			$val = get_post_meta( $post->ID, '_psp_fe_global_template', true ) ? get_post_meta( $post->ID, '_psp_fe_global_template', true ) : ''; ?>

			<input type="checkbox" name="psp_fe_global_template" id="psp_fe_global_template" value="yes" <?php echo checked( $val, 'yes', false ); ?>> <label for="psp_fe_global_template"><?php esc_html_e( 'Frontend Template', 'psp_projects' ); ?></label>

		</div>

    <?php
	}
}

add_action( 'save_post', 'psp_fe_save_global_template_meta' );
function psp_fe_save_global_template_meta( $post_id ) {

    if ( !isset( $_POST[ 'post_type' ] ) || !isset( $_POST['psp_fe_global_template_nonce'] ) )
        return $post_id;

    if ( !wp_verify_nonce( $_POST['psp_fe_global_template_nonce'], plugin_basename(__FILE__) ) )
        return $post_id;

    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
        return $post_id;

    if ( 'psp_projects' == $_POST['post_type'] && !current_user_can( 'edit_post', $post_id ) )
        return $post_id;

    if (!isset($_POST['psp_fe_global_template']))
        return $post_id;
    else {
        $mydata = $_POST['psp_fe_global_template'];
        update_post_meta( $post_id, '_psp_fe_global_template', $_POST['psp_fe_global_template'], get_post_meta( $post_id, '_psp_fe_global_template', true ) );
    }

}

function psp_get_global_templates() {

	$args = array(
		'post_type'			=>	'psp_projects',
		'post_status'		=>	array( 'draft', 'publish' ),
		'posts_per_page'	=>	-1,
	);

	$meta_args = array(
		'meta_value'	=>	'yes',
		'meta_key'		=>	'_psp_fe_global_template'
	);

	$args 			= array_merge( $args, $meta_args );

	return new WP_Query( $args );

}

function psp_duplicate_post_create_duplicate( $post, $status = null ) {

		// We don't want to clone revisions
		if ($post->post_type == 'revision') return;

		if ($post->post_type != 'attachment'){
			$prefix = get_option('duplicate_post_title_prefix');
			$suffix = get_option('duplicate_post_title_suffix');
			if (!empty($prefix)) $prefix.= " ";
			if (!empty($suffix)) $suffix = " ".$suffix;
			if (get_option('duplicate_post_copystatus') == 0) $status = 'publish';
		}
		$new_post_author = psp_duplicate_post_get_current_user();

		$new_post = array(
			'menu_order' 		=> $post->menu_order,
			'comment_status' 	=> $post->comment_status,
			'ping_status' 		=> $post->ping_status,
			'post_author' 		=> $new_post_author->ID,
			'post_content' 		=> $post->post_content,
			'post_excerpt' 		=> (get_option('duplicate_post_copyexcerpt') == '1') ? $post->post_excerpt : "",
			'post_mime_type' 	=> $post->post_mime_type,
			'post_parent' 		=> $new_post_parent = empty($parent_id)? $post->post_parent : $parent_id,
			'post_password' 	=> $post->post_password,
			'post_status' 		=> $new_post_status = (empty($status))? $post->post_status: $status,
			'post_title' 		=> $prefix.$post->post_title.$suffix,
			'post_type' 		=> $post->post_type,
		);

		if(get_option('duplicate_post_copydate') == 1){
			$new_post['post_date'] = $new_post_date =  $post->post_date ;
			$new_post['post_date_gmt'] = get_gmt_from_date($new_post_date);
		}

		$new_post_id = wp_insert_post($new_post);

		// If you have written a plugin which uses non-WP database tables to save
		// information about a post you can hook this action to dupe that data.
		if ($post->post_type == 'page' || (function_exists('is_post_type_hierarchical') && is_post_type_hierarchical( $post->post_type ))) {
			do_action( 'psp_dp_duplicate_page', $new_post_id, $post );
		} else {
			do_action( 'psp_dp_duplicate_post', $new_post_id, $post );
		}

		delete_post_meta($new_post_id, '_dp_original');
		delete_post_meta($new_post_id, '_psp_fe_global_template' );
		add_post_meta($new_post_id, '_dp_original', $post->ID);

		// If the copy is published or scheduled, we have to set a proper slug.
		if ($new_post_status == 'publish' || $new_post_status == 'future'){
			$post_name = wp_unique_post_slug($post->post_name, $new_post_id, $new_post_status, $post->post_type, $new_post_parent);

			$new_post = array();
			$new_post['ID'] = $new_post_id;
			$new_post['post_name'] = $post_name;

			// Update the post into the database
			wp_update_post( $new_post );
		}

		return $new_post_id;

}

function psp_duplicate_post_get_current_user() {
	if (function_exists('wp_get_current_user')) {
		return wp_get_current_user();
	} else if (function_exists('get_currentuserinfo')) {
		global $userdata;
		get_currentuserinfo();
		return $userdata;
	} else {
		$user_login = $_COOKIE[USER_COOKIE];
		$current_user = $wpdb->get_results("SELECT * FROM $wpdb->users WHERE user_login='$user_login'");
		return $current_user;
	}
}

add_action( 'psp_dp_duplicate_post', 'psp_duplicate_post_copy_post_meta_info', 10, 2 );
add_action( 'psp_dp_duplicate_page', 'psp_duplicate_post_copy_post_meta_info', 10, 2 );
function psp_duplicate_post_copy_post_meta_info($new_id, $post) {

	$post_meta_keys = get_post_custom_keys($post->ID);

	if (empty($post_meta_keys)) return;

	foreach ($post_meta_keys as $meta_key) {
		$meta_values = get_post_custom_values($meta_key, $post->ID);
		foreach ($meta_values as $meta_value) {
			$meta_value = maybe_unserialize($meta_value);
			add_post_meta($new_id, $meta_key, $meta_value);
		}
	}
}

add_action( 'wp_ajax_psp_fe_delete_project', 'psp_fe_delete_project' );
function psp_fe_delete_project() {

	$post_id = $_POST['post_id'];

	if( !$post_id || !psp_can_edit_project($post_id) ) wp_send_json_error( array( 'success' => false ) );

	wp_trash_post($post_id);

	wp_send_json_success( array( 'success' => true ) );

	die();

}

/**
 * DEPRICATED -- REMOVE
 * @var [type]
 */
add_action( 'wp_ajax_psp_fe_add_task', 'psp_fe_add_task' );
function psp_fe_add_task() {

	if( !isset($_POST['post_id']) || !(isset($_POST['phase_id']) ) ) return false;

	$phases 	= get_field( 'phases', $_POST['post_id'] );
	$task_id 	= count($phases[$_POST['phase_id']]['tasks']);

	$task = apply_filters( 'psp_fe_add_task_data', array(
		'ID'		=>	$task_id,
		'task'		=>	$_POST['task'],
		'assigned'	=>	$_POST['assigned'],
		'due_date'	=>	psp_fe_noramlize_due_date($_POST['due_date']),
		'status'	=>	'0'
	), $_POST['post_id'], $_POST['phase_id'], $task_id, $phases, $_POST );

	$phases[$_POST['phase_id']]['tasks'][$task_id] = $task;

	update_field( 'phases', $phases, $_POST['post_id'] );

	do_action( 'psp_fe_add_task_ajax', $_POST['post_id'], $_POST['phase_id'], $_POST );

	ob_start();
	include( psp_template_hierarchy( 'projects/phases/tasks/single/entry.php' ) );
	$markup = ob_get_clean();

	wp_send_json_success( array( 'success' => true, 'output' => $markup ) );

	exit();

}

add_action( 'wp_ajax_psp_duplicate_template', 'psp_ajax_duplicate_template' );
function psp_ajax_duplicate_template() {

	if( !current_user_can('publish_psp_projects') ) {
		wp_send_json_error( array( 'sucess' => false, 'message' => __( 'You do not have permission to create projects', 'psp_projects' ) ) );
		exit();
	}

	$template = intval($_POST['template']);

	if( !$template || empty($template) ) {
		wp_send_json_error( array( 'sucess' => false, 'message' => __( 'No template selected', 'psp_projects' ) ) );
		exit();
	}

	$cuser		= wp_get_current_user();
	$post       = get_post( $template );
	$new_id     = psp_duplicate_post_create_duplicate( $post, 'publish' );
	$redirect   = ( PSP_FE_PERMALINKS ? 'manage/edit/' . $new_id .'/?status=template' : '&psp_manage_page=edit&psp_manage_option=' . $new_id . '&status=template' );

	update_post_meta( $new_id, '_psp_post_author', $cuser->ID );

	wp_send_json_success( array(
		'success'	=>	true,
		'redirect'	=>	get_post_type_archive_link('psp_projects') . $redirect
	) );

	exit();

}

add_action( 'wp_ajax_psp_fe_update_task', 'psp_fe_update_task' );
function psp_fe_update_task() {

	// Gates

	if( !isset($_POST['post_id']) || !isset($_POST['phase_index']) ) wp_send_json_error( array( 'success' => false, 'message' => __( 'No post or phase ID set', 'psp_projects' ) ) );

	// Confirm user can edit this project

	if( !psp_can_edit_project($_POST['post_id']) ) {
		wp_send_json_error( array( 'sucess' => false, 'message' => __( 'You do not have permission to edit this task', 'psp_projects' ) ) );
		exit();
	}

	// Simplify
	$post_id 		= $_POST['post_id'];
	$phase_index 		= $_POST['phase_index'];
	$phase_completed_prev = psp_get_phase_progress( $post_id, $phase_index );
	$do_notify		= false;

	// Setup the automatic progress reporting
	$overall_auto	= get_field( 'automatic_progress', $post_id );
	$phase_auto		= get_field( 'phases_automatic_progress', $post_id );

	$phases 	= get_field( 'phases', $post_id );
	$task_index 	= ( $_POST['task_index'] == 'new' ? count($phases[$phase_index]['tasks']) : $_POST['task_index'] );

	$status	= ( $task_index == 'new' ? 0 : $phases[$phase_index]['tasks'][$task_index]['status'] );

	$task = apply_filters( 'psp_fe_update_task_data', array(
		'ID'				=>	$task_index,
		'task'				=>	stripslashes($_POST['task']),
		'assigned'			=>	$_POST['assigned'],
		'due_date'			=>	psp_fe_noramlize_due_date( $_POST['due_date'] ),
		'status'			=>	$status,
		'task_id'			=>	( isset( $_POST['task_id'] ) && ! empty( $_POST['task_id'] ) ) ? $_POST['task_id'] : psp_generate_task_id(),
	), $post_id, $phase_index, $task_index, $phases, $_POST );

	/*
 	 * See if a notification should be sent. Need to see if:
 	 *
 	 * A) This is a new task that's assigned to someone
 	 * B) This is an old task that's been assigned to someone new
	 */

	 if( $_POST['task_index'] == 'new' && !empty($_POST['assigned']) ) {
		 $do_notify = true;
	 }

	 if( !empty($phases[$phase_index]['tasks'][$task_index]['assigned']) && $phases[$phase_index]['tasks'][$task_index]['assigned'] != $_POST['assigned'] ) {
		 $do_notify = true;
	 }

	$phases[$phase_index]['tasks'][$task_index] = $task;

	update_field( 'phases', $phases, $post_id );

	ob_start();

	include( psp_template_hierarchy( 'projects/phases/tasks/single/entry.php' ) );

	/**
	 * Return information to update front end
	 * @var [type]
	 */

	$stats 				= psp_get_project_summary($post_id);
	$phase_completed 	= psp_get_phase_progress( $post_id, $phase_index );

	$target 	= ( $_POST['task_index'] == 'new' ? 'ul.psp-task-list' : '.task-item-' . $task_index );
	$method		= ( $_POST['task_index'] == 'new' ? 'append' : 'replace' );

	$return = apply_filters( 'psp_fe_update_task_return', array(
		'success'			=>	true,
		'project_progress'	=>	$stats['progress']['total'],
		'phase_progress'	=>	array(
			'completed'		=>	$phase_completed,
			'remaining'		=>	100 - $phase_completed,
			'previous'		=>	$phase_completed_prev,
		),
		'modify'			=>	array(
			array(
				'target'	=>	'#phase-' . ( $phase_index + 1 ) . ' ' . $target,
				'markup'	=>	ob_get_clean(),
				'method'	=>	$method
			),
			array(
				'target'	=>	'#phase-' . ( $phase_index + 1 ) . ' .total-task-count',
				'markup'	=>	count($phases[$phase_index]['tasks']),
				'method'	=>	'replace'
			),
			array(
				'target'	=>	'#psp-stat-tasks h3',
				'markup'	=>	'<h3><span>' . $stats['tasks']['complete'] . '</span>/' . $stats['tasks']['total'] . '</h3>',
				'method'	=>	'replace'
			)
		),
		'debug'	=>	array(
			'task_assigned'	=>	$_POST['assigned'],
			'do_notify'		=>	$do_notify,
			'due_date'		=>	$task['due_date']
		)
	) );

	if( $do_notify ) {

		/**
		 * Prep the notifications if we need one!
		 * @var array
		 */

		$notification = array(
			'post_id'	=>	$post_id,
			'project_id'	=>	$post_id,
			'user_id'	=>	$_POST['assigned'],
			'user_ids'	=>	array( $_POST['assigned'] ),
			'phases'	=>	array(
				$phase_id => array(
					'phase_title'	=>	stripslashes($phases[$phase_id]['title']),
					'tasks'	=>	array(
						array(
							'name'		=>	$task['task'],
							'task_id'	=>	$task['ID'], // Legacy
							'due_date'	=>	$task['due_date'],
							'status'	=>	$task['status']
						)
					)
				)
			)
		);
		// Do the action
		do_action( 'psp_notify', 'task_assigned', $notification );
	}

	wp_send_json_success( $return );

}

function psp_fe_noramlize_due_date( $date = NULL ) {

	if( $date == NULL ) return false;

	return date('Ymd', strtotime($date) );

}

add_action( 'wp_ajax_nopriv_psp_delete_document', 'psp_fe_delete_document' );
add_action( 'wp_ajax_psp_delete_document', 'psp_fe_delete_document' );
function psp_fe_delete_document() {

	$post_id = $_POST['post_id'];
	$doc_id  = $_POST['doc_id'];
	$title	 = urldecode($_POST['title']);

	$cuser = wp_get_current_user();

	if( !psp_can_edit_project($cuser->ID) ) {
		wp_send_json_error( array( 'success' => false, 'message' => __('Access denied', 'psp_projects') ) );
    }

	$documents = get_field( 'documents', $post_id );

	if( $documents[$doc_id]['title'] == $title ) {

		unset( $documents[$doc_id] );
		update_field( 'documents', $documents, $post_id );

		wp_send_json_success( array( 'success' => true, 'message' => 'Document successfully deleted!' ) );
		exit();

	} /* else {

		for( $i = 0; $i >= count($documents); $i++ ) {
			if( $documents[$i]['title'] == $title ) {

				unset( $documents[$doc_id] );
				update_field( 'documents', $documents, $post_id );

				wp_send_json_success();
				exit();

			}
		}

	} */

	wp_send_json_error( array( 'success' => false, 'message' => __('Document not found, please refresh your browser and try again', 'psp_projects'), 'title' => $title, 'doc_id' => $doc_id ) );
	exit();

}

add_action( 'wp_ajax_nopriv_psp_fe_get_phase_data', 'psp_fe_get_phase_data' );
add_action( 'wp_ajax_psp_fe_get_phase_data', 'psp_fe_get_phase_data' );
function psp_fe_get_phase_data() {

	// TODO NEXT!

	$post_id = $_POST['post_id'];
	$phase_id = $_POST['phase_id'];

	$phases = get_field( 'phases', $post_id );
	$phase 	= $phases[$phase_id];
	$tasks  = $phases['tasks'];
	$total	= 0;
	$completion = 0;

	$data = array(
		'completion'	=>	0,
		'remaining'		=>	0,
		'tasks'			=>	0,
		'completed'		=>	0,
		'count_string'	=>	'',
		'task_list_string'	=>	'',
	);

	foreach( $tasks as $task ) {

		$data['tasks']++;
		$total += 100;
		$completion += $tasks['percent_complete'];
		if( $tasks['percent_complete'] == 100 ) $data['completed']++;

	}

	/**
	 * Calculate the phase competion percentage and remaining
	 *
	 */
	if( $total == 0 ) {
		$data['completion'] = 0;
	} else {
		$data['completion'] = ceil( $completion / $total * 100 );
	}
	$data['remaining'] = floor( 100 - $data['completion'] );


	$return = apply_filters( 'psp_fe_update_task_return', array(
		'success'	=>	true,
		'completion'=>	$complition,
		'total'		=>	$total,
		'modify'	=>	array(
			array(
				'target'	=>	'#phase-' . $phase_index . ' ' . $target,
				'markup'	=>	ob_get_clean(),
				'method'	=>	$method
			),
			array(
				'target'	=>	'#phase-' . $phase_index . ' .total-task-count',
				'markup'	=>	count($phases[$phase_id]['tasks']),
				'method'	=>	'replace'
			),
			array(
				'target'	=>	'#psp-stat-tasks h3',
				'markup'	=>	'<h3><span>' . $stats['tasks']['complete'] . '</span>/' . $stats['tasks']['total'] . '</h3>',
				'method'	=>	'replace'
			)
		),
	) );

	wp_send_json_success( array( 'success' => true, 'data' => $data ) );
	exit();

}

add_action( 'wp_ajax_psp_fe_delete_task', 'psp_fe_delete_task' );
function psp_fe_delete_task() {

	// Gates
	if( !isset($_POST['post_id']) || !isset($_POST['phase_index']) || !isset($_POST['task_index']) ) wp_send_json_error( array( 'success' => false, 'message' => __( 'No post or phase ID set', 'psp-front-edit' ) ) );

	// Confirm user can edit this project
	if( !psp_can_edit_project($_POST['post_id']) ) {
		wp_send_json_error( array( 'sucess' => false, 'message' => __( 'You do not have permission to edit this task', 'psp-front-edit' ) ) );
		exit();
	}

	// Setup variables
	$post_id 	= $_POST['post_id'];
	$phase_index 	= $_POST['phase_index'];
	$task_index 	= $_POST['task_index'];
	$phase_id = $_POST['phase_id'];
	$task_id = $_POST['task_id'];

	// Get the phases and remove the task
	$phases = get_field( 'phases', $post_id );
	$phase_completed_prev = psp_get_phase_progress( $post_id, $phase_index );
	unset( $phases[$phase_index]['tasks'][$task_index] );

	// Save
	update_field( 'phases', $phases, $post_id );

	$stats 				= psp_get_project_summary( $post_id );
	$phase_completed 	= psp_get_phase_progress( $post_id, $phase_index );
	$phase_data 		= psp_get_phase_completed( $phase_index, $post_id );

	ob_start();

	// This could be cleaner
	$i = 0;
	while( have_rows( 'phases', $post_id ) ) { the_row();
		if( $i == $phase_index ) {
			include( psp_template_hierarchy( 'projects/phases/tasks/index.php' ) );
		}
		$i++;
	}

	$return = apply_filters( 'psp_fe_delete_task_return', array(
				'project_progress'	=>	$stats['progress']['total'],
				'phase_progress'	=>	array(
					'completed'		=>	$phase_completed,
					'remaining'		=>	100 - $phase_completed,
					'previous'		=>	$phase_completed_prev,
				),
				'modify'			=>	array(
					array(
						'target'	=>	'#psp-stat-tasks h3',
						'markup'	=>	'<h3><span>' . $stats['tasks']['complete'] . '</span>/' . $stats['tasks']['total'] . '</h3>',
						'method'	=>	'replace'
					),
					array(
						'target'	=>	'#phase-' . ( $phase_index + 1 ) . ' .psp-top-complete .task-count',
						'markup'	=>	'<span class="completed">' . $phase_data['completed_tasks'] . '</span>' . '/' . count($phases[$phase_index]['tasks']),
						'method'	=>	'html'
					),
					array(
						'target'	=>	'#phase-' . ( $phase_index + 1 ) . ' .psp-task-list-wrapper',
						'markup'	=>	ob_get_clean(),
						'method'	=>	'replace'
					)
				),
			) );

	wp_send_json_success( $return );

}

add_action( 'wp_ajax_psp_fe_update_date', 'psp_fe_update_date' );
function psp_fe_update_date() {

	$post_id 	= $_POST['post_id'];
	$start_date = $_POST['start_date'];
	$end_date 	= $_POST['end_date'];

	$cuser = wp_get_current_user();

	if( !isset($_POST['post_id']) ) {
		wp_send_json_error( array( 'success' => false, 'message' => __( 'Post ID needs to be set', 'psp-front-edit' ) ) );
	}

	// Confirm user can edit this project
	if( !psp_can_edit_project($post_id) ) {
		wp_send_json_error( array( 'sucess' => false, 'message' => __( 'You do not have permission to edit project', 'psp-front-edit' ) ) );
		exit();
	}

	if( !empty($start_date) ) {
		$start_date = date( 'Ymd', strtotime($start_date) );
	}

	if( !empty($end_date) ) {
		$end_date = date( 'Ymd', strtotime($end_date) );
	}

	// Update start date
	update_field( 'start_date', $start_date, $post_id );

	// Update end date
	update_field( 'end_date', $end_date, $post_id );

	$time_ellapsed 		= psp_calculate_timing( $post_id );
	$project_completion = psp_compute_progress( $post_id );
	$progress_class 	= psp_get_the_schedule_status_class( $time_ellapsed['percentage_complete'], $project_completion );

	$return = apply_filters( 'psp_fe_update_dates_return', array(
				'success'	=>	true,
				'dates'		=>	array(
					'ellapsed'	=> $time_ellapsed['percentage_complete'],
					'title'		=> $time_ellapsed['percentage_complete'] . __('% Time Ellapsed', 'psp-front-edit' ),
					'class'		=> $progress_class
				),
				'modify'	=>	array(
					array(
						'target'	=>	'.psp-the-start-date p',
						'markup'	=>	'<p>' . psp_get_the_start_date( null, $post_id ) . '</p>',
						'method'	=>	'replace'
					),
					array(
						'target'	=>	'.psp-the-end-date p',
						'markup'	=>	'<p>' . psp_get_the_end_date( null, $post_id ) . '</p>',
						'method'	=>	'replace'
					),
				)
			) );

	wp_send_json_success( $return );

}

function get_the_pspf_title( $post_id ) {

	$title = esc_attr( get_the_title( $post_id ) );

	$findthese = apply_filters( 'get_the_pspf_title_find', array(
		'#Protected:#',
		'#Private:#'
	) );

	$replacewith = apply_filters( 'get_the_pspf_title_replace', array(
		'',
		''
	) );

	$title = preg_replace($findthese, $replacewith, $title);

	return $title;

}

function psp_fe_remove_documents_from_overview( $fields ) {

	$new_fields = array();
	foreach( $fields['fields'] as $sub_field ) {
		if( $sub_field['name'] != 'documents' && $sub_field['key'] != 'field_52a9e4594b146' ) $new_fields[] = $sub_field;
	}

	$fields['fields'] = $new_fields;

	return $fields;

}

add_filter( 'psp_overview_fields', 'psp_fe_add_project_type_to_frontend' );
function psp_fe_add_project_type_to_frontend( $fields ) {

	if( is_admin() ) {
		return $fields;
	}

	$project_type_field = array (
				'key' => 'field_5b9d07f50b2bc',
				'label' => 'Project Type',
				'name' => 'project_type',
				'type' => 'taxonomy',
				'taxonomy' => 'psp_tax',
				'field_type' => 'checkbox',
				'allow_null' => 1,
				'load_save_terms' => 1,
				'return_format' => 'id',
				'multiple' => 0,
			);

	$new_fields = array();

	foreach( $fields['fields'] as $field ) {

		if( $field['key'] == 'field_527d5d61fb854' ) {
			$new_fields[] = $project_type_field;
		}

		$new_fields[] = $field;

	}

	$fields['fields'] = $new_fields;

	return $fields;

}

add_action( 'wp_ajax_psp_fe_populate_edit_phase_modal', 'psp_fe_populate_edit_phase_modal' );
function psp_fe_populate_edit_phase_modal( ) {

	$phase_index = $_POST['phase_index'];
	$post_id 	 = $_POST['post_id'];

	if( !$phase_index ) {
		$phase_index = 0;
	}

	if( !$post_id || empty($post_id) ) {
		wp_send_json_error( array( 'success' => false, 'message' => __( 'No post ID set' , 'psp-front-edit' ) ) );
	}

	$phases 		= get_field( 'phases', $post_id );
	$progress_type  = get_field( 'progress_type', $post_id );

	switch( $progress_type ) {
		case( 'Weighting' ):
			$progress_field = 'weighting';
			$progress_value = $phases[$phase_index]['weight'];
			break;
		case( 'Hours' ):
			$progress_field = 'hours';
			$progress_value = $phases[$phase_index]['hours'];
			break;
		case( 'Percentage' ):
			$progress_field = 'percentage';
			$progress_value = $phases[$phase_index]['percentage'];
			break;
	}

	$return = apply_filters( 'psp_fe_populate_phases_modal_return', array(
		'success'		=>	true,
		'title'			=>  $phases[$phase_index]['title'],
		'description'	=>	$phases[$phase_index]['description'],
		'progressfield'	=>	$progress_field,
		'progress'		=>	$progress_value
	), $post_id );

	wp_send_json_success( $return );
	die();

}

add_action( 'wp_ajax_psp_fe_update_phase', 'psp_fe_update_phase' );
function psp_fe_update_phase() {

	$post_id 		= $_POST['post_id'];
	$title   		= $_POST['phase-title'];
	$description 	= stripslashes($_POST['phase-description']);
	$phase_index	= $_POST['phase_index'];

	if( !psp_can_edit_project($post_id ) ) {
		wp_send_json_error( array( 'success' => false, 'message' => __( 'You do not have permission to modify this project', 'psp-front-edit' ) ) );
	}

	do_action( 'psp_fe_update_phase_ajax', $_POST );

	if( !$phase_index ) {
		$phase_index = 0;
	}

	$phases 		= get_field( 'phases', $post_id );
	$progress_type  = get_field( 'progress_type', $post_id );

	if( $title ) {
		$phases[$phase_index]['title'] = $title;
	}

	if( $description ) {
		$phases[$phase_index]['description'] = $description;
	}

	switch( $progress_type ) {
		case( 'Weighting' ):
			$phases[$phase_index]['weight'] = intval($_POST['weight']);
			break;
		case( 'Hours' ):
			$phases[$phase_index]['hours'] = intval($_POST['hours']);
			break;
		case( 'Percentage' ):
			$phases[$phase_index]['percentage'] = intval($_POST['percentage']);
			break;
	}

	update_field( 'phases', $phases, $post_id );

	$return = apply_filters( 'psp_fe_update_phase_return', array(
				'success'	=>	true,
				'modify'	=>	array(
					array(
						'target'	=>	'.psp-phase-id-' . $phase_index . ' span.psp-phase-title',
						'markup'	=>	$title,
						'method'	=>	'html'
					),
					array(
						'target'	=>	'.psp-phase-id-' . $phase_index . ' .psp-phase-info',
						'markup'	=>	'<h5>' . __( 'Description', 'psp-front-edit' ) . '</h5>' . $description,
						'method'	=>	'html'
					),
				),
				'progress'	=>	psp_compute_progress( $post_id )
			), $phase_index, $post_id );

	wp_send_json_success( $return );

}

add_action( 'wp_ajax_psp_fe_update_description', 'psp_fe_update_description' );
function psp_fe_update_description() {

	$post_id 		= $_POST['post_id'];
	$description 	= stripslashes($_POST['description']);

	if( !psp_can_edit_project($post_id) ) {
		wp_send_json_error( array( 'success' => false, 'message' => __( 'You do not have permission to modify this project', 'psp-front-edit' ) ) );
	}

	update_field( 'project_description', $description, $post_id );

	$return = apply_filters( 'psp_fe_update_description_return', array(
				'success'	=>	true,
				'modify'	=>	array(
					array(
						'target'	=>	'.psp-description-content',
						'markup'	=>	$description,
						'method'	=>	'html'
					),
			) ), $post_id );

	wp_send_json_success( $return );

}
