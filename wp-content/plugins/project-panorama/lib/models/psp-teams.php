<?php
/**
 * psp-teams.php
 *
 * Register custom teams post type and any admin management around the CPT
 *
 * @category controller
 * @package psp-projects
 * @author Ross Johnson
 * @version 1.0
 * @since 1.3.6
 */

add_action( 'init', 'psp_create_psp_teams' );
function psp_create_psp_teams() {

    $psp_slug = psp_get_option( 'psp_slug' , 'panorama' );

    $labels = array(
        'name'                => _x( 'Teams', 'Post Type General Name', 'psp_projects' ),
        'singular_name'       => _x( 'Team', 'Post Type Singular Name', 'psp_projects' ),
        'menu_name'           => __( 'Teams', 'psp_projects' ),
        'parent_item_colon'   => __( 'Parent Team:', 'psp_projects' ),
        'all_items'           => __( 'All Teams', 'psp_projects' ),
        'view_item'           => __( 'View Team', 'psp_projects' ),
        'add_new_item'        => __( 'Add New Team', 'psp_projects' ),
        'add_new'             => __( 'New Team', 'psp_projects' ),
        'edit_item'           => __( 'Edit Team', 'psp_projects' ),
        'update_item'         => __( 'Update Team', 'psp_projects' ),
        'search_items'        => __( 'Search Teams', 'psp_projects' ),
        'not_found'           => __( 'No teams found', 'psp_projects' ),
        'not_found_in_trash'  => __( 'No teams found in Trash', 'psp_projects' ),
    );

    $rewrite = array(
        'slug'                => $psp_slug . '-teams',
        'with_front'          => true,
        'pages'               => true,
        'feeds'               => true,
    );

    $args = array(
        'label'               => __( 'psp_teams', 'psp_projects' ),
        'description'         => __( 'Teams', 'psp_projects' ),
        'labels'              => $labels,
        'supports'            => array( 'title', 'revisions', 'thumbnail'),
        'hierarchical'        => false,
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => false,
        'show_in_nav_menus'   => true,
        'show_in_admin_bar'   => true,
        'menu_position'       => 20,
        'menu_icon'           => '',
        'can_export'          => true,
        'has_archive'         => true,
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'rewrite'             => $rewrite,
        'capability_type'     => array( 'psp_team', 'psp_teams' ),
        'map_meta_cap'        => true,
    );

    register_post_type( 'psp_teams' , $args);

}

add_action( 'admin_menu' , 'psp_add_teams_submenu_page' );
function psp_add_teams_submenu_page() {

	add_submenu_page( 'edit.php?post_type=psp_projects', 'Teams', 'Teams', 'publish_psp_teams', 'edit.php?post_type=psp_teams', NULL );
    add_submenu_page( 'post-new.php?post_type=psp_teams', 'Teams', 'Teams', 'publish_psp_teams', 'post-new.php?post_type=psp_teams', NULL );

}

add_filter( 'manage_psp_teams_posts_columns', 'psp_teams_admin_header', 999, 1 );
function psp_teams_admin_header( $defaults ) {

    $new = array();

    foreach( $defaults as $key => $title ) {

        if( $key == 'title' ) {

		    $new[$key] 			= __( 'Team', 'psp_projects' );
            $new['users'] 		= __( 'Users','psp_projects');
            $new['active']      = __( 'Active Projects', 'psp_projects' );

		} else {

			// Clear out other irrelevant headers
			continue;

		}
    }

    return $new;

}

add_action('manage_psp_teams_posts_custom_column', 'psp_teams_header_content', 10, 2);
function psp_teams_header_content( $column_name, $post_id ) {

	if( $column_name == 'users' ) {

		$team_members   = psp_get_team_members( $post_id );

        if( !$team_members ) return;

        $limit          = apply_filters( 'psp_teams_header_content_user_display_limit', 10 );
        $i              = 1;
        $total          = count( $team_members );

		foreach( $team_members as $member ) {

            if( $i < $limit ) { ?>

				<div class="psp_user_assigned">
					<?php
                    $link = ( current_user_can('list_users') ? array('<a href="' . get_edit_user_link( $member['ID'] ) . '">', '</a>' ) : array('','') );
                    echo $link[0] . $member[ 'user_avatar' ] . $link[1]; ?>
					<span><?php echo psp_username_by_id( $member[ 'ID' ] ); ?></span>
				</div>

	        <?php
            }
            $i++;
        }

        if( $total > $limit ) echo '<div class="psp_user_assigned overage"><strong>+' . ( $total - $limit ) . '</strong></div>';

	}

    if( $column_name == 'active' ) {
        echo count( psp_get_team_projects( $post_id, 'completed' ) );
    }

}

add_action('do_meta_boxes', 'psp_relabel_team_featured_image_box');
function psp_relabel_team_featured_image_box()
{
    remove_meta_box( 'postimagediv', 'psp_teams', 'side' );
    add_meta_box('postimagediv', __('Team Thumbnail', 'psp_projects'), 'post_thumbnail_meta_box', 'psp_teams', 'side' );
}
