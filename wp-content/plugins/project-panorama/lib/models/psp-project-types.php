<?php
/**
 * psp-project-types.php
 *
 * Register custom taxonomies for Project Panorama and any specific management of this taxonomy.
 *
 * @category controller
 * @package psp-projects
 * @author Ross Johnson
 * @version 1.0
 * @since 1.3.6
 */

function psp_project_taxonomy() {

    $psp_slug = psp_get_option( 'psp_slug' , 'panorama' );

    $labels = array(
        'name'                       => _x( 'Project Types', 'Taxonomy General Name', 'psp_projects' ),
        'singular_name'              => _x( 'Project Type', 'Taxonomy Singular Name', 'psp_projects' ),
        'menu_name'                  => __( 'Project Types', 'psp_projects' ),
        'all_items'                  => __( 'All Project Types', 'psp_projects' ),
        'parent_item'                => __( 'Parent Project Type', 'psp_projects' ),
        'parent_item_colon'          => __( 'Parent Project Type:', 'psp_projects' ),
        'new_item_name'              => __( 'New Project Type', 'psp_projects' ),
        'add_new_item'               => __( 'Add Project Type', 'psp_projects' ),
        'edit_item'                  => __( 'Edit Project Type', 'psp_projects' ),
        'update_item'                => __( 'Update Project Type', 'psp_projects' ),
        'separate_items_with_commas' => __( 'Separate items with commas', 'psp_projects' ),
        'search_items'               => __( 'Search Project Types', 'psp_projects' ),
        'add_or_remove_items'        => __( 'Add or remove project types', 'psp_projects' ),
        'choose_from_most_used'      => __( 'Choose from the most used items', 'psp_projects' ),
        'not_found'                  => __( 'Not Found', 'psp_projects' ),
    );
    $rewrite = array(
        'slug'                       => $psp_slug . '-projects',
        'with_front'                 => true,
        'hierarchical'               => true,
    );
    $args = array(
        'labels'                     => $labels,
        'hierarchical'               => true,
        'public'                     => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => true,
        'rewrite'                    => $rewrite
    );
    register_taxonomy( 'psp_tax', array( 'psp_projects' ), $args );

    $labels = array(
                'name'                       => _x( 'Project Status', 'Taxonomy General Name', 'psp_projects' ),
                'singular_name'              => _x( 'Project Status', 'Taxonomy Singular Name', 'psp_projects' ),
                'menu_name'                  => __( 'Project Status', 'psp_projects' ),
                'all_items'                  => __( 'All Project Statuses', 'psp_projects' ),
                'parent_item'                => __( 'Parent Project Status', 'psp_projects' ),
                'parent_item_colon'          => __( 'Parent Project Status:', 'psp_projects' ),
                'new_item_name'              => __( 'New Project Status', 'psp_projects' ),
                'add_new_item'               => __( 'Add Project Status', 'psp_projects' ),
                'edit_item'                  => __( 'Edit Project Status', 'psp_projects' ),
                'update_item'                => __( 'Update Project Status', 'psp_projects' ),
                'separate_items_with_commas' => __( 'Separate items with commas', 'psp_projects' ),
                'search_items'               => __( 'Search Project Statuses', 'psp_projects' ),
                'add_or_remove_items'        => __( 'Add or remove project statues', 'psp_projects' ),
                'choose_from_most_used'      => __( 'Choose from the most used items', 'psp_projects' ),
                'not_found'                  => __( 'Not Found', 'psp_projects' ),
            );
            $rewrite = array(
                'slug'                       => 'panorama-status',
                'with_front'                 => true,
                'hierarchical'               => false,
            );
            $args = array(
                'labels'                     => $labels,
                'hierarchical'               => false,
                'public'                     => true,
                'show_ui'                    => false,
                'show_admin_column'          => false,
                'show_in_nav_menus'          => false,
                'show_tagcloud'              => false,
                'rewrite'                    => $rewrite
            );
            register_taxonomy( 'psp_status', array( 'psp_projects' ), $args );

}

// Hook into the 'init' action
add_action( 'init', 'psp_project_taxonomy', 0 );
