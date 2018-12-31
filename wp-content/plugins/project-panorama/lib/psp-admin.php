<?php
/**
 * Description of psp-admin
 *
 * Functionality that manages the admin experience.
 * @package psp-projects
 *
 *
 */

/**
 * Add a tab for Invoices
 * @param  [array] $tabs tab->slug and tab->name
 * @return [array] modified tabs
 */
add_filter( 'psp_settings_tabs', 'psp_invoices_tab' );
function psp_invoices_tab( $tabs ) {

    // If sprout invoices is loaded, don't add the tab
    if( !function_exists( 'sprout_invoices_load' ) ) {
        $tabs = array_merge( $tabs, array( 'psp_settings_invoices' => __( 'Invoices', 'psp_projects' ) ) );
    }

    return $tabs;

}

add_filter( 'psp_settings_sections', 'psp_invoices_section' );
function psp_invoices_section( $sections ) {

    return array_merge( $sections, array( 'psp_settings_invoices'   => apply_filters( 'psp_settings_sections_invoices', array() ) ) );

}

add_filter( 'psp_registered_settings', 'psp_invoices_settings' );
function psp_invoices_settings( $settings ) {

    $invoice_settings = array(
        'psp_settings_invoices' =>  apply_filters( 'psp_settings_invoices',
            array(
                'psp_invoices_nag'  => array(
                        'id' => 'psp_invoices_nag',
                        'desc' => __( 'You can send invoices and collect payments through Panorama by integrating with', 'psp_projects' ) . ' <a href="http://sproutapps.co/sprout-invoices/?_si_d=project-panorama" target="_new">Sprout Invoices</a>',
                        'name' => '<a href="http://sproutapps.co/sprout-invoices/?_si_d=project-panorama" target="_new"><img src="' .  PROJECT_PANARAMA_URI . '/assets/images/sproutapps-logo.png" alt="Sprout Invoices"></a>',
                        'type' => 'html',
                ),
            )
        )
    );

    $settings = array_merge( $settings, $invoice_settings );

    return $settings;

}

/**
 * Insert a plug for Sprout Invoices
 * @return [markup]
 */
function psp_invoice_settings() {

    $active_tab = ( isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'psp_general_settings' ); ?>

    <div id="psp_invoices" class="psp-settings-tab <?php if( $active_tab == 'psp_invoices') { echo ' psp-settings-tab-active'; } ?>">

        <p><a href="http://sproutapps.co/sprout-invoices/?_si_d=project-panorama" target="_new"><img src="<?php echo PROJECT_PANARAMA_URI; ?>/assets/images/sproutapps-logo.png" alt="Sprout Invoices"></a></p>

        <p><?php echo __( 'You can send invoices and collect payments through Panorama by integrating with', 'psp_projects' ) . ' <a href="http://sproutapps.co/sprout-invoices/?_si_d=project-panorama" target="_new">Sprout Invoices</a>.'; ?></p>

    </div>

<?php }


add_action( 'psp_settings_page', 'psp_calendar_settings' );
/**
 * Insert a plug for Sprout Invoices
 * @return [markup]
 */
function psp_calendar_settings() {

    $active_tab = ( isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'psp_general_settings' ); ?>

    <div id="psp_invoices" class="psp-settings-tab <?php if( $active_tab == 'psp_calendar') { echo ' psp-settings-tab-active'; } ?>">

        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row" valign="top">
                        <label for="psp_calendar_langauge"><?php _e('Calendar Localization','psp_projects'); ?></label>
                    </th>
                    <td>
                        <select id="psp_calendar_langauge" name="psp_calendar_language">
                            <option value="<?php echo psp_get_option( 'psp_calendar_language', 'en' ); ?>"><?php echo psp_get_option( 'psp_calendar_language', 'en' ); ?></option>
                            <option value="---" disabled>---</option>
                            <option value="en">en</option>
                            <option value="ar-ma">ar-ma</option>
                            <option value="ar-sa">ar-sa</option>
                            <option value="ar-tn">ar-tn</option>
                            <option value="ar">ar</option>
                            <option value="bg">bg</option>
                            <option value="ca">ca</option>
                            <option value="cs">cs</option>
                            <option value="da">da</option>
                            <option value="de-at">de-at</option>
                            <option value="de">de</option>
                            <option value="el">el</option>
                            <option value="en-au">en-au</option>
                            <option value="en-ca">en-ca</option>
                            <option value="en-gb">en-gb</option>
                            <option value="en-ie">en-ie</option>
                            <option value="en-nz">en-nz</option>
                            <option value="es">es</option>
                            <option value="fa">fa</option>
                            <option value="fi">fi</option>
                            <option value="fr-ca">fr-ca</option>
                            <option value="fr-ch">fr-ch</option>
                            <option value="fr">fr</option>
                            <option value="he">he</option>
                            <option value="hi">hi</option>
                            <option value="hr">hr</option>
                            <option value="hu">hu</option>
                            <option value="id">id</option>
                            <option value="is">is</option>
                            <option value="it">it</option>
                            <option value="ja">ja</option>
                            <option value="ko">ko</option>
                            <option value="lt">lt</option>
                            <option value="lv">lv</option>
                            <option value="nb">nb</option>
                            <option value="nl">nl</option>
                            <option value="pl">pl</option>
                            <option value="pt-br">pt-br</option>
                            <option value="pt">pt</option>
                            <option value="ro">ro</option>
                            <option value="ru">ru</option>
                            <option value="sk">sk</option>
                            <option value="sl">sl</option>
                            <option value="sr-cyrl">sr-cyrl</option>
                            <option value="sr">sr</option>
                            <option value="sv">sv</option>
                            <option value="th">th</option>
                            <option value="tr">tr</option>
                            <option value="uk">uk</option>
                            <option value="vi">vi</option>
                            <option value="zh-cn">zh-cn</option>
                            <option value="zh-tw">zh-tw</option>
                        </select>
                    </td>
                </tr>
            </tbody>
        </table>

    </div>

<?php }

// TODO: Merge into new settings API
add_action( 'admin_init', 'psp_calendar_settings_options' );
function psp_calendar_settings_options() {

    register_setting( 'edd_panorama_license', 'psp_calendar_language' );

}

function psp_user_project_list() {

    $user_id 	= $_GET['user'];
    $user 		= get_user_by('id',$user_id);
    $username 	= psp_username_by_id($user_id); ?>

    <div class="wrap">

        <h2 class="psp-user-list-title">
            <?php echo get_avatar($user_id); ?> <span><?php _e('Projects Assigned to','psp_projects'); ?> <?php echo $username; ?></span>
        </h2>

        <br style="clear:both">

        <table id="psp-user-list-table" class="wp-list-table widefat fixed posts">
            <thead>
            <tr>
                <th><?php esc_html_e( 'Title', 'psp_projects' ); ?></th>
                <th><?php esc_html_e( 'Client', 'psp_projects' ); ?></th>
                <th><?php esc_html_e( '% Complete', 'psp_projects' ); ?></th>
                <th><?php esc_html_e( 'Timing', 'psp_projects' ); ?></th>
                <th><?php esc_html_e( 'Project Types', 'psp_projects' ); ?></th>
                <th><span><span class="vers"><span title="Comments" class="comment-grey-bubble"></span></span></span></th>
                <th><?php esc_html_e( 'Last Updated', 'psp_projects' ); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php

            $args = array(
                'post_type'         =>  'psp_projects',
                'posts_per_page'    =>  '-1',
                'meta_query'        =>  array(
        			'relation'	=>	'OR',
        	       	array(
        				'key' 	=> 'allowed_users_%_user',
        				'value' => $user_id
        			),
        			array(
        				'key' 	=> 'restrict_access_to_specific_users',
        				'value' => ''
        			),
        			array(
        				'key'	=>	'_psp_post_author',
        				'value'	=>	$user_id
        			)
        		)
            );

            $projects   = new WP_Query( $args );
            $i          = 0;

            while( $projects->have_posts() ): $projects->the_post(); global $post; ?>

                <tr <?php if($i %2 == 0) { echo 'class="alternate"'; } ?>>
                    <td><strong><a href="post.php?post=<?php echo $post->ID; ?>&action=edit"><?php the_title(); ?></a></strong></td>
                    <td><?php the_field( 'client' ); ?></td>
                    <td>
                        <?php
                        $completed = psp_compute_progress( $post->ID );
                        if( $completed > 10 ) {
                            echo '<p class="psp-progress"><span class="psp-' . $completed . '"><strong>%' . $completed . '</strong></span></p>';
                        } else {
                            echo '<p class="psp-progress"><span class="psp-' . $completed . '"></span></p>';
                        } ?>
                    </td>
                    <td>
                        <?php psp_the_timing_bar( $post->ID ); ?>
                    </td>
                    <td><?php the_terms( $post->ID, 'psp_tax' ); ?></td>
                    <td><div class="post-com-count-wrapper">
                            <a href='edit-comments.php?p=<?php echo $post->ID; ?>' class='post-com-count'><span class='comment-count'><?php comments_number( '0', '1', '%' ); ?></span></a></div></td>
                    <td><?php the_modified_date(); ?></td>
                </tr>

                <?php $i++; endwhile; ?>

            </tbody>
        </table>
    </div>

<?php
}

add_action( 'admin_menu', 'psp_add_extra_links', 999 );
function psp_add_extra_links() {

    global $submenu;
    $submenu[ 'edit.php?post_type=psp_projects' ][] = array( __( 'Dashboard', 'psp_projects' ), 'read', get_post_type_archive_link( 'psp_projects' ) );

}

add_action( 'admin_menu', 'psp_register_submenus' );
function psp_register_submenus() {

    add_submenu_page(
        'edit.php?post_type=psp_projects',
        __( 'Add-ons', 'psp_projects' ),
        __( 'Add-ons', 'psp_projects' ),
        'manage_options',
        'panorama-add-ons',
        'psp_addons_admin_page'
    );

}

function psp_addons_admin_page() { ?>

    <div class="wrap">

        <h2><?php esc_html_e( 'Panorama Add-ons', 'psp_projects' ); ?></h2>

        <?php
        include_once( ABSPATH . WPINC . '/feed.php' );

        $rss = fetch_feed( 'https://www.projectpanorama.com/feed/addons?rand=2' );

        $maxitems = 0;

        if ( ! is_wp_error( $rss ) ) : // Checks that the object is created correctly

            // Figure out how many total items there are, but limit it to 5.
            $maxitems = $rss->get_item_quantity( 999 );

            // Build an array of all the items, starting with element 0 (first element).
            $rss_items = $rss->get_items( 0, $maxitems );

        endif; ?>

        <ul class="panorama-addon-list">
            <?php if ( $maxitems == 0 ) : ?>
                <li><?php _e( 'No items', 'my-text-domain' ); ?></li>
            <?php else : ?>
                <?php foreach( $rss_items as $item ): ?>
                    <li>
                        <h3><a href="<?php echo esc_url( $item->get_permalink() ); ?>" target="_new"><?php echo esc_html( $item->get_title() ); ?></a></h3>
                        <?php echo $item->get_content(); ?>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>

    </div>

    <?php

}

function filter_handler( $seconds ) {
    return 0;
}
add_filter( 'wp_feed_cache_transient_lifetime' , 'filter_handler' );

add_action( 'admin_notices', 'psp_check_addon_vers' );
function psp_check_addon_vers() {

    $addon_reqs = apply_filters( 'psp_addon_reqs', array(
        'front_end_uploader'    =>  array(
            'def'   =>  'PSP_FILE_UPLOAD_VER',
            'req'   =>  '1.6',
            'message'   =>  esc_html( 'This version of Panorama requires version 1.6 or higher of the Panorama Uploader add-on.', 'psp_projects' ) . ' <a href="https://www.projectpanorama.com/add-ons/front-end-uploader/" target="_new">' . esc_html( 'Download it here.', 'psp_projects' ) . '</a>'
        ),
        'frontend_editor'   =>  array(
            'def'   =>  'PSP_FE_VER',
            'req'   =>  '1.3',
            'message'   =>  esc_html( 'This version of Panorama requires version 1.4 or higher of the Front End Editor add-on.', 'psp_projects' ) . ' <a href="https://www.projectpanorama.com/my-account/" target="_new">' . esc_html( 'Download it here.', 'psp_projects' ) . '</a>'
        )
    ) );

    foreach( $addon_reqs as $addon ) {

        if( defined( $addon['def'] ) ) {

            $ver = constant( $addon['def'] );

            if( version_compare( $ver, $addon['req'] ) < 0 ) { ?>

                <div class="update-nag notice is-dismissable">
                    <p><?php echo wp_kses_post( $addon['message'] ); ?></p>
                </div>

            <?php
            }

        }

    }
    /*
    if( defined("PSP_FILE_UPLOAD_VER") && version_compare( PSP_FILE_UPLOAD_VER, '1.6' ) < 0 ) { ?>
        <div class="update-nag notice is-dismissable">
            <p>
                <?php esc_html_e( 'This version of Panorama requires version 1.6 or higher of the Front End Uploader add-on.', 'psp_projects' ); ?>
                <a href="https://www.projectpanorama.com/add-ons/front-end-uploader/" target="_new"><?php esc_html_e( 'Download it here.', 'psp_projects' ); ?></a>
            </p>
        </div>
    <?php }
    */
}
