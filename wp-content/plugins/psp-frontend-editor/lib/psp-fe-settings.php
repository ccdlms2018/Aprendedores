<?php
add_filter( 'psp_settings_sections_addons', 'psp_fe_settings_section' );
add_filter( 'psp_settings_addons', 'psp_fe_settings' );

function psp_fe_license_key_callback( $args ) {

    $status = get_option( 'psp_fe_license_key_status' );
    $html   = '';

    if( isset( $_GET['psp_fe_activate_response'] ) ) :
        ob_start(); ?>
            <div class="psp-status-message">
                <?php var_dump( psp_fe_check_activation_response() ); ?>
            </div>
        <?php
        $html .= ob_get_clean();
    endif;

    if ( $status !== false && $status == 'valid' ) {
        $html .= '<span style="color:green;" class="psp-activation-notice">' . __( 'Active', 'psp-front-edit' ) . '</span>';
        $html .= wp_nonce_field( 'edd_panorama_nonce', 'edd_panorama_nonce', true, false );
        $html .= '<input type="submit" class="button-secondary" name="psp_fe_license_deactivate" value="' . __('Deactivate License','psp-front-edit') . '"/>';
    }
    else {
        $html .= '<span style="color:red;" class="psp-activation-notice">' . __( 'Inactive', 'psp-front-edit' ) . '</span>';
        $html .= wp_nonce_field( 'edd_panorama_nonce', 'edd_panorama_nonce', true, false );
        $html .= ' <input type="submit" class="button-secondary" name="psp_fe_license_activate" value="'. __('Activate License','psp-front-edit') . '"/>';
        $html .= '<a class="button" href="' . admin_url() . 'options-general.php?page=panorama-license&tab=psp_settings_addons&psp_fe_activate_response=true">' . __( 'Check Activation Message', 'psp_projects' ) . '</a>';

    }

    echo $html;

}

function psp_fe_settings_section( $sections ) {

    $sections[ 'psp_frontend_editor_settings' ] = __( 'Front End Editor', 'psp-front-edit' );

    return $sections;

}

// TODO: Change this so it's a license key
function psp_fe_settings( $settings ) {

    $psp_fe_settings[ 'psp_frontend_editor_settings' ] = array(
        'psp_invoices_nag'  => array(
            'id' => 'psp_fe_title',
            'name' => '<h2>' . __( 'Front End Editor', 'psp-front-edit' ) . '</h2>',
            'type' => 'html',
        ),
        'psp_fe_license_key' => array(
            'id'    => 'psp_fe_license',
            'name'  => __( 'License Key', 'psp-front-edit' ),
            'desc'  => __( 'Enter your license key, save and then activate.', 'psp-front-edit' ),
            'type'  => 'text',
        ),
        'psp_fe_activate_license' => array(
            'id'    =>  'psp_fe_activate_license',
            'name'  =>  __( 'Activate License', 'psp-front-edit' ),
            'type'  =>  'fe_license_key'
        ),
        'psp_fe_dequeue' => array(
            'id'    => 'psp_fe_dequeue',
            'name'  => __( 'Dequeue Scripts', 'psp-front-edit' ),
            'desc'  => __( 'If you have theme or plugin scripts effecting the style, enter them here separated by a comma', 'psp-front-edit' ),
            'type'  => 'text',
        ),
    );

    return array_merge( $settings, $psp_fe_settings );

}



add_action( 'admin_init', 'psp_fe_activate_license', 0 );
function psp_fe_activate_license() {

    if( isset( $_POST[ 'psp_fe_license_activate' ] ) ) {
        // run a quick security check
	 	if( ! check_admin_referer( 'edd_panorama_nonce', 'edd_panorama_nonce' ) )
			return; // get out if we didn't click the Activate button

		// retrieve the license from the database
        $license = get_option( 'psp_settings' );
		$license = trim( $license[ 'psp_fe_license' ] );


		// data to send in our API request
		$api_params = array(
			'edd_action'=> 'activate_license',
			'license' 	=> $license,
			'item_name' => urlencode( PSP_FE_ITEM_NAME ), // the name of our product in EDD
		    'url'   => home_url()
        );

		// Call the custom API.
		$response = wp_remote_get( add_query_arg( $api_params, PSP_FE_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) )
			return false;

		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		// $license_data->license will be either "active" or "inactive"

		update_option( 'psp_fe_license_key_status', $license_data->license );

    }

}

function psp_fe_deactivate_license() {

	// listen for our activate button to be clicked
	if( isset( $_POST[ 'psp_fe_license_deactivate' ] ) ) {

		// run a quick security check
	 	if( ! check_admin_referer( 'edd_panorama_nonce', 'edd_panorama_nonce' ) )
			return; // get out if we didn't click the Activate button

		// retrieve the license from the database
		$license = get_option( 'psp_settings' );
		$license = trim( $license[ 'psp_fe_license' ] );


		// data to send in our API request
		$api_params = array(
			'edd_action'=> 'deactivate_license',
			'license' 	=> $license,
			'item_name' => urlencode( PSP_FE_ITEM_NAME ) // the name of our product in EDD
		);

		// Call the custom API.
		$response = wp_remote_get( add_query_arg( $api_params, PSP_FE_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) )
			return false;

		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		// $license_data->license will be either "deactivated" or "failed"
		if( $license_data->license == 'deactivated' )
			delete_option( 'psp_fe_license_key_status' );

	}
}
add_action('admin_init', 'psp_fe_deactivate_license');

function psp_fe_check_activation_response() {
    $license = get_option( 'psp_settings' );
    $license = trim( $license[ 'psp_fe_license' ] );


    // data to send in our API request
    $api_params = array(
        'edd_action'=> 'activate_license',
        'license' 	=> $license,
        'item_name' => urlencode( PSP_FE_ITEM_NAME ), // the name of our product in EDD
        'url'   => home_url()
    );

    // Call the custom API.
    $response = wp_remote_get( add_query_arg( $api_params, PSP_FE_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

    return $response;

}
