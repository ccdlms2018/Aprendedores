<?php
/**
 * Custom Templating and routes
 * @var array
 */

/**
 * Important Definitions
 * @var array
 */
$defs = array(
    'PSP_BASE_FILE'     =>  __FILE__,
    'PSP_BASE_DIR'      =>  dirname( __FILE__ ),
    'PSP_PLUGIN_URL'    =>  plugin_dir_url( __FILE__ )
);

foreach( $defs as $definition => $value ) {
    if( !defined( $definition ) ) define( $definition, $value );
}

/**
 * Custom function to mimic get_template_part but specific to project panorama
 * @param  [type] $path [description]
 * @return [type]       [description]
 */
function psp_get_template_part( $path ) {
	include( psp_template_hierarchy( $path . '.php' ) );
}

/**
 * Custom Rewrites for Templates
 * @return null
 */
function psp_custom_page_rewrite_rules() {

    global $wp_rewrite;

	$slug = psp_get_slug();

    if( isset( $wp_rewrite->front ) ) $slug = substr( $wp_rewrite->front, 1 ) . $slug;

    /**
     * Custom task pages
     */
    add_rewrite_rule( '^' . $slug . '/tasks/?$', 'index.php?post_type=psp_projects&psp_tasks_page=home', 'top' );
	add_rewrite_rule( '^' . $slug . '/tasks/([^/]*)/?', 'index.php?post_type=psp_projects&psp_tasks_page=$matches[1]', 'top' );

    /**
     * Custom Status
     */
     add_rewrite_rule( '^' . $slug . '/status/([^/]+)/?$', 'index.php?post_type=psp_projects&psp_status_page=$matches[1]', 'top' );
     add_rewrite_rule( '^' . $slug . '/status/([^/]+)/page/([0-9]+)?/?$', 'index.php?post_type=psp_projects&psp_status_page=$matches[1]&paged=$matches[2]', 'top' );

}
add_action( 'init', 'psp_custom_page_rewrite_rules', 20, 0 );

/**
 * Custom tags for query variables
 */
add_filter( 'query_vars', 'psp_status_page_query_vars' );
function psp_status_page_query_vars( $vars ) {

    $custom_vars = apply_filters( 'psp_custom_query_vars', array(
        'psp_status_page',
        'psp_tasks_page',
    ) );

    foreach( $custom_vars as $var )
        $vars[] = $var;

    return $vars;

}

add_filter( 'template_include', 'psp_template_chooser', 100, 1 );
function psp_template_chooser( $template ) {

    // Post ID
    $post_type    = get_post_type();
    $post_type    = ( is_search() ? 'psp_projects' : $post_type );
    $post_id      = get_the_ID();
    $supported    = apply_filters( 'psp_template_chooser_supported', array(
        'psp_projects'  =>  array(
            'single'    =>  'projects/single',
            'archive'   =>  'archive-psp_projects',
        ),
        'psp_teams'     =>  array(
            'single'    =>  'dashboard/components/teams/single',
            'archive'   =>  'dashboard/components/teams/index'
        )
    ) );

    $supported_post_types = array_keys( $supported );

    // If this isn't a Panorama project or Panorama archive, return as normal
    if ( ! in_array( get_post_type(), $supported_post_types ) && !is_post_type_archive( $supported_post_types ) && !is_psp_search() ) return $template;

    /**
     * Check to see if the user has a custom template set, return the custom template if so
     */
    $use_custom_template 	= psp_get_option( 'psp_use_custom_template' );
    $custom_template 		= psp_get_option( 'psp_custom_template' );

    if ( $use_custom_template && !empty( $custom_template ) ) return psp_custom_template( $custom_template );

    // If the user doesn't have access, redirect them to a login form
    if( !panorama_check_access() && !$use_custom_template ) {
        add_filter( 'psp_body_classes', 'psp_add_login_template_to_body_class' );
        return apply_filters( 'psp_login_template', psp_template_hierarchy( 'global/index' ) );
    }

    // Is this a single project
    if ( is_single() ) {
      return apply_filters( 'psp_single_template_' . $post_type, psp_template_hierarchy( $supported[$post_type]['single'] ) );
    }

    if( is_psp_search() ) {
        return apply_filters( 'psp_archive_template_search', psp_template_hierarchy( $supported['psp_projects']['archive'] ) );
    }

    if ( is_post_type_archive() ) {
      $post_type_archive = psp_find_archive_post_type( array_keys($supported) );
      return apply_filters( 'psp_archive_template_' . $post_type, psp_template_hierarchy( $supported[$post_type_archive]['archive'] ) );
    }

}

function psp_find_archive_post_type( $supported ) {

    foreach( $supported as $type ) {
        if( is_post_type_archive($type) ) return $type;
    }

    if ( is_post_type_archive( 'psp_teams' ) ) {
      return apply_filters( 'psp_teams_archive_template_psp_projects', psp_template_hierarchy( 'dashboard/components/teams/index.php' ) );
    }

}

/**
* Get the custom template if is set
*
* @since 1.0
*/

function psp_template_hierarchy( $template ) {

  $template	= ( substr( $template, -4, 4 ) == '.php' ? $template : $template . '.php' );
  $base_dir = ( $template == 'archive-psp_projects.php' ? 'projects/' : '' );

  if ( $theme_file = locate_template( array( 'panorama/' . $base_dir . $template ) ) ) {
  	$file = $theme_file;
  } else {
  	$file = PSP_BASE_DIR . '/templates/' . $base_dir . $template;
  }

  return apply_filters( 'psp_standard_template_' . $template, $file );

}

function psp_custom_template( $template ) {

  if($theme_file = locate_template( array( $template ))) {

  	$file = $theme_file;

    return apply_filters( 'psp_custom_template_' . $template, $file );

  } else {

	  psp_template_hierarchy( $template );

  }

}

function psp_panorama_inject_into_custom_template( $content ) {

  if( is_admin() ) return $content;

  global $post;

  if ( empty( $post ) ) return $content;

  $use_custom_template 	= psp_get_option( 'psp_use_custom_template' );
  $custom_template 		= psp_get_option( 'psp_custom_template' );
  $custom_template 		= !empty( $custom_template ) ? $custom_template : false;
  $template             = NULL;

  if( !$use_custom_template ) {
	  return $content;
  }

  $archives = apply_filters( 'psp_inject_into_archive_templates', array(
      'psp_projects'    =>      PSP_BASE_DIR . '/templates/projects/archive-psp_projects.php',
      'psp_teams'       =>      PSP_BASE_DIR . '/templates/dashboard/components/teams/index.php'
  ) );

  /**
   * If this is a single project, then return the single template and close comments
   */
  if ( is_single() && ( bool ) $use_custom_template && $custom_template && get_post_type( $post->ID ) == 'psp_projects') {
        $template = PSP_BASE_DIR . '/templates/projects/custom-template-single.php';
  }

  $psp_custom_archive_templates = apply_filters( 'psp_custom_archive_templates', array(
      array(
          'query_var' => 'psp_calendar_page',
          'template'  => PSP_BASE_DIR . '/templates/dashboard/components/calendar/index.php'
      ),
      array(
          'query_var' =>  'psp_ical_page',
          'template'  =>  PSP_BASE_DIR . '/templates/dashboard/components/calendar/ical.php'
      ),
      array(
          'query_var' =>  'psp_tasks_page',
          'template'  =>  PSP_BASE_DIR . '/templates/dashboard/components/tasks/index.php'
      ),
  ) );

  if( !panorama_check_access() ) {
      $template = PSP_BASE_DIR . '/templates/global/index.php';
  } else {

      $is_custom_template = false;
      foreach( $psp_custom_archive_templates as $custom_template ) {

          if( get_query_var( $custom_template['query_var'] ) && is_post_type_archive('psp_projects') ) {
              $is_custom_template = true;
              $template = $custom_template['template'];
          }

      }

      if( is_post_type_archive() && in_array( get_post_type(), array_keys($archives) ) && !$is_custom_template ) {

          foreach( $archives as $slug => $path ) {
              if( $slug == get_post_type() ) $template = $path;
          }

      }

  }

  if( !empty( $template ) ) {

      ob_start();
      include $template;
      $content = ob_get_contents();
      ob_end_clean();

  }

  return $content;

}
add_filter( 'the_content', 'psp_panorama_inject_into_custom_template' );


function psp_disable_custom_template_comments( $comment_template ) {

    if( is_singular() && get_post_type() == 'psp_projects' ) {
        return PSP_BASE_DIR . '/templates/parts/blank.php';
    }

}

add_filter( 'psp_standard_template_dashboard/header.php', 'psp_custom_archive_header', 999, 1 );
function psp_custom_archive_header( $file ) {

    $use_custom_template 	= psp_get_option( 'psp_use_custom_template' );
    $custom_template 		= psp_get_option( 'psp_custom_template' );

    if ( ( bool ) $use_custom_template && $custom_template ) {
        return PSP_BASE_DIR . '/templates/parts/wrapper-start.php';
    }

    return $file;

}

add_filter( 'psp_standard_template_dashboard/footer.php', 'psp_custom_archive_footer' );
function psp_custom_archive_footer( $file ) {

    $use_custom_template 	= psp_get_option( 'psp_use_custom_template' );
    $custom_template 		= psp_get_option( 'psp_custom_template' );

    if ( ( bool ) $use_custom_template && $custom_template ) {
        return PSP_BASE_DIR . '/templates/parts/wrapper-end.php';
    }

    return $file;

}

add_action( 'template_redirect', 'psp_custom_archive_redirects', 900, 1 );
function psp_custom_archive_redirects( $template ) {

    if( psp_get_option('psp_use_custom_template') ) return $template;

    $psp_custom_archive_templates = apply_filters( 'psp_custom_archive_templates', array(
        array(
            'query_var' => 'psp_calendar_page',
            'callback'  => 'psp_return_calendar_template'
        ),
        array(
            'query_var' =>  'psp_ical_page',
            'callback'  =>  'psp_return_ical_template'
        ),
        array(
            'query_var' =>  'psp_tasks_page',
            'callback'  =>  'psp_return_tasks_template'
        )
    ) );

    foreach( $psp_custom_archive_templates as $custom_template ) {

        if( get_query_var( $custom_template['query_var'] ) && is_post_type_archive('psp_projects') && isset( $custom_template['callback'] ) ) {
            return add_filter( 'template_include', $custom_template['callback'], 101 );
        }

    }

}

/**
 * Custom dashboard template callbacks
 */

function psp_return_calendar_template() {
	return psp_template_hierarchy( 'dashboard/components/calendar/index.php' );
}

function psp_return_tasks_template() {
    return psp_template_hierarchy( 'dashboard/components/tasks/index.php' );
}

function psp_return_ical_template() {
    return psp_template_hierarchy( 'dashboard/components/calendar/ical.php' );
}

/**
 * Custom dashboard limit posts
 */

add_action( 'pre_get_posts', 'psp_custom_template_limit_archive_posts' );
function psp_custom_template_limit_archive_posts( $query ) {

    if( ( is_post_type_archive('psp_projects') || is_post_type_archive('psp_teams') ) && !is_admin() && psp_get_option('psp_use_custom_template') && $query->is_main_query() ) {
        $query->set( 'posts_per_page', 1 );
    }

}

add_action( 'psp_head', 'psp_check_wp_head_enabled' );
function psp_check_wp_head_enabled() {
	if( psp_get_option( 'psp_enable_wp_head' ) ) wp_head();
}

add_action( 'init', 'psp_custom_routes_rewrite_rules' );
function psp_custom_routes_rewrite_rules() {

	$slug = psp_get_slug();

	/**
	 * @package dates JSON
	 *
	 * Allow the user ID to be passed in to create a JSON feed
	 */
    add_rewrite_tag( '%psp_dates%', '([^&]+)' );
    add_rewrite_rule( 'psp-dates/([^&]+)/?', 'index.php?psp_dates=$matches[1]', 'top' );

    /**
     * @package Status
     *
     * Filter all projects by active or complete
     */

     add_rewrite_rule( '^' . $slug . '/status/([^/]+)/?$', 'index.php?post_type=psp_projects&psp_status_page=$matches[1]', 'top' );
     add_rewrite_rule( '^' . $slug . '/status/([^/]+)/page/([0-9]+)?/?$', 'index.php?post_type=psp_projects&psp_status_page=$matches[1]&paged=$matches[2]', 'top' );

    /**
     * @package Calendar
     *
     * Pull up a calendar by user ID
     */

     add_rewrite_rule( '^' . $slug . '/calendar/?$', 'index.php?post_type=psp_projects&psp_calendar_page=home', 'top' );
     add_rewrite_rule( '^' . $slug . '/calendar/([^/]*)/?', 'index.php?post_type=psp_projects&psp_calendar_page=$matches[1]', 'top' );

}

add_filter( 'query_vars', 'psp_archive_query_vars' );
function psp_archive_query_vars( $vars ) {

	$new_vars = apply_filters( 'psp_archive_query_vars', array(
		'psp_calendar_page',
		'psp_user_page',
        'psp_status_page'
	) );

	foreach( $new_vars as $var ) {

		$vars[] = $var;

	}

	return $vars;

}

add_action( 'template_redirect', 'psp_archive_template_redirects', 10 );
function psp_archive_template_redirects( $template ) {

	$archive_templates = apply_filters( 'psp_archive_template_redirects', array(
		'psp_calendar_page',
		'psp_user_page',
	) );

	foreach( $archive_templates as $query_var ) {

		if( get_query_var( $query_var ) && is_post_type_archive( 'psp_projects' ) ) {

			return add_filter( 'template_include', $query_var . '_template_redirect' );

		}

	}

}

function psp_calendar_page_template_redirect() {
    return psp_template_hierarchy( 'dashboard/components/calendar/index.php' );
}

function psp_user_page_template_redirect() {
    return psp_template_hierarchy( 'dashboard/components/user/index.php' );
}

add_action( 'psp_footer', 'psp_check_wp_footer_enabled' );
function psp_check_wp_footer_enabled() {
	if( psp_get_option( 'psp_enable_wp_footer' ) ) wp_footer();
}

add_filter( 'body_class', 'psp_custom_template_class' );
function psp_custom_template_class( $classes ) {

    $post_types = apply_filters( 'psp_post_types', array(
        'psp_projects',
        'psp_team'
    ) );

    if( in_array( get_post_type(), $post_types ) && psp_get_option('psp_use_custom_template') ) {
        $classes[] = 'psp-custom-template';
    }

    return $classes;

}

/**
 * Adds the Task Panel into Single, Dashboard, and Your Tasks Views
 * 
 * @since {{VERSION}}
 * @return void
 */
add_action( 'psp_before_menu', 'psp_add_task_panel' );
function psp_add_task_panel() {
	
	include( psp_template_hierarchy( 'global/task-panel.php' ) );
	
}