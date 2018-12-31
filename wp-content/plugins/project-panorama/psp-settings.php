<?php
/**
 * Update old settings with new single setting optoin
 * @return [array] [Array of all settings]
 */
function psp_get_settings() {

	$settings = get_option( 'psp_settings' );

	if ( empty( $settings ) ) {

		// Update old settings with new single option

		$general_settings      = is_array( get_option( 'psp_settings_general' ) ) ? get_option( 'psp_settings_general' ) : array();
		$appearance_settings   = is_array( get_option( 'psp_settings_appearance' ) ) ? get_option( 'psp_settings_appearance' ) : array();
		$notification_settings = is_array( get_option( 'psp_settings_notifications' ) ) ? get_option( 'psp_settings_notifications' ) : array();
		$advanced_settings     = is_array( get_option( 'psp_settings_advanced' ) ) ? get_option( 'psp_settings_advanced' ) : array();
		$addon_settings        = is_array( get_option( 'psp_settings_addons' ) ) ? get_option( 'psp_settings_addons' ) : array();

		$settings = array_merge( $general_settings, $appearance_settings, $notification_settings, $advanced_settings, $addon_settings );

		update_option( 'psp_settings', $settings );

	}

	return apply_filters( 'psp_get_settings', $settings );

}

// Global $psp_options
$psp_options = psp_get_settings();

/**
 * get an option from the new psp settings, if one doesn't exist try the default key
 *
 * @param  string $key [option name]
 * @param  [type] $default [set a default fallback]
 *
 * @return [mixed]          [value]
 */
function psp_get_option( $key = '', $default = false ) {

	$psp_db_ver = intval( get_option( 'psp_database_version' ) );

	// Check to see if the settings data has been migrated and use the old system if not...
	if ( $psp_db_ver < 6 ) {

		$value = get_option( $key, $default );

	} else {

		global $psp_options;

		$value = ! empty( $psp_options[ $key ] ) ? $psp_options[ $key ] : $default;

		$value = apply_filters( 'psp_get_option', $value, $key, $default );

	}

	return apply_filters( 'psp_get_option_' . $key, $value, $key, $default );

}

/**
 * Add the admin menu
 */
add_action( 'admin_menu', 'edd_panorama_license_menu' );
function edd_panorama_license_menu() {

	global $psp_settings_page;

	$psp_settings_page = add_submenu_page( 'options-general.php', 'Project Panorama Settings', __( 'Settings', 'psp_projects'), 'manage_options', 'panorama-license', 'edd_panorama_license_page' );

	global $submenu;

	$settings_index = null;

	if( empty( $submenu['options-general.php'] ) ) return;

	foreach ( $submenu['options-general.php'] as $key => $menu_item ) {

		// Index 2 is always the child page slug
		if ( $menu_item[2] == 'panorama-license' ) {
			$settings_index = $key;
			break;
		}

	}

	// We need to make the path more absolute
	$submenu['options-general.php'][ $settings_index ][2] = 'options-general.php?page=panorama-license';

	// Move the Menu Item
	$submenu['edit.php?post_type=psp_projects'][] = $submenu['options-general.php'][ $settings_index ];
	unset( $submenu['options-general.php'][ $settings_index ] );

}

add_filter( 'parent_file', 'psp_highlight_settings_parent_page', 10, 1 );
function psp_highlight_settings_parent_page( $parent_file ){

	global $current_screen;
	global $self;

	if ( $current_screen->base == 'settings_page_panorama-license' ) {

		// Render this as the Active Page Menu
		$parent_file = 'edit.php?post_type=psp_projects';

		// Ensure the top-level "Settings" doesn't show as active
		$self = 'edit.php?post_type=psp_projects';

	}

	return $parent_file;

}

add_filter( 'submenu_file', 'psp_highlight_settings_submenu_page', 10, 2 );
function psp_highlight_settings_submenu_page( $submenu_file, $parent_file ) {

	global $current_screen;

	if ( $current_screen->base == 'settings_page_panorama-license' ) {
		$submenu_file = 'options-general.php?page=panorama-license';
	}

	return $submenu_file;

}

add_action( 'adminmenu', 'psp_reset_options_general_menu' );
function psp_reset_options_general_menu() {

	global $current_screen;
	global $parent_file;

	if ( $current_screen->base == 'settings_page_panorama-license' ) {

		// We have to reset this after the Menu is generated so Settings Errors still appear
		$parent_file = 'options-general.php';

	}

}

/**
 * Return an array of all the settings tabs and their titles
 * @return array
 */
function psp_get_settings_tabs() {

	// $settings = edd_get_registered_settings();

	$tabs                               = array();
	$tabs['psp_settings_general']       = __( 'General', 'psp_projects' );
	$tabs['psp_settings_appearance']    = __( 'Appearance', 'psp_projects' );
	$tabs['psp_settings_notifications'] = __( 'Notifications', 'psp_projects' );
	$tabs['psp_settings_advanced']      = __( 'Advanced', 'psp_projects' );

	$addon_settings = apply_filters( 'psp_settings_addons', array() );

	if ( ! empty( $addon_settings ) ) {

		$tabs['psp_settings_addons'] = __( 'Addons', 'psp_projects' );

	}

	return apply_filters( 'psp_settings_tabs', $tabs );

}

/**
 * Get the content of each setting tab
 *
 * @param  [string] $tab [ID of the setting tab to return]
 *
 * @return [markup?]      [description]
 */
function psp_get_settings_tab_sections( $tab = false ) {
	$tabs     = false;
	$sections = psp_get_registered_settings_sections();
	if ( $tab && ! empty( $sections[ $tab ] ) ) {
		$tabs = $sections[ $tab ];
	} else if ( $tab ) {
		$tabs = false;
	}

	return $tabs;
}

function psp_get_registered_settings_sections() {
	static $sections = false;
	if ( false !== $sections ) {
		return $sections;
	}
	$sections = array(
		'psp_settings_general'       => apply_filters( 'edd_settings_sections_general', array(
			'main' => __( 'General', 'psp_projects' ),
		) ),
		'psp_settings_appearance'    => apply_filters( 'edd_settings_sections_appearance', array(
			'main'           => __( 'General', 'psp_projects' ),
			'header'         => __( 'Header', 'psp_projects' ),
			'body'           => __( 'Body', 'psp_projects' ),
			'phases'         => __( 'Phases', 'psp_projects' ),
			'custom_styling' => __( 'Custom Styling', 'psp_projects' ),
			'calendar'       => __( 'Calendar', 'psp_projects' )
		) ),
		'psp_settings_notifications' => apply_filters( 'psp_settings_sections_notifications', array(
			'email' => __( 'Email', 'psp_projects' ),
		) ),
		'psp_settings_advanced'      => apply_filters( 'psp_settings_sections_advanced', array(
			'main' => __( 'General', 'psp_projects' ),
		) ),
		'psp_settings_addons'        => apply_filters( 'psp_settings_sections_addons', array() )
	);
	$sections = apply_filters( 'psp_settings_sections', $sections );

	return $sections;
}

function edd_panorama_register_options() {
	if ( false == get_option( 'psp_settings' ) ) {
		add_option( 'psp_settings' );
	}
	foreach ( psp_get_registered_settings() as $tab => $sections ) {
		foreach ( $sections as $section => $settings ) {
			// Check for backwards compatibility
			$section_tabs = psp_get_settings_tab_sections( $tab );
			if ( ! is_array( $section_tabs ) || ! array_key_exists( $section, $section_tabs ) ) {
				$section  = 'main';
				$settings = $sections;
			}
			add_settings_section(
				'psp_settings_' . $tab . '_' . $section,
				__return_null(),
				'__return_false',
				'psp_settings_' . $tab . '_' . $section
			);
			foreach ( $settings as $key => $option ) {
				// For backwards compatibility
				if ( empty( $option['id'] ) ) {
					continue;
				}

				$name = isset( $option['name'] ) ? $option['name'] : '';
				$args = wp_parse_args( $option, array(
					'section' => $section,
					'id'      => null,
					'desc'    => '',
					'name'    => null,
					'default' => null,
				) );

				$callback = $option['type'] == 'custom' && isset( $option['callback'] ) ? $option['callback'] : "psp_$option[type]_callback";
				add_settings_field(
					'psp_settings[' . $option['id'] . ']',
					$name,
					is_callable( $callback ) ? $callback : 'psp_missing_callback',
					'psp_settings_' . $tab . '_' . $section,
					'psp_settings_' . $tab . '_' . $section,
					$args
				);
			}
		}
	}
	// Creates our settings in the options table
	register_setting( 'psp_settings', 'psp_settings', 'psp_settings_sanitize' );
}

add_action( 'admin_init', 'edd_panorama_register_options' );

function psp_settings_sanitize( $input = array() ) {
	global $psp_options;

	if ( empty( $_POST['_wp_http_referer'] ) ) {
		return $input;
	}

	parse_str( $_POST['_wp_http_referer'], $referrer );

	$settings = psp_get_registered_settings();
	$tab      = isset( $referrer['tab'] ) ? $referrer['tab'] : 'psp_settings_general';
	$section  = isset( $referrer['section'] ) ? $referrer['section'] : 'main';

	$input = $input ? $input : array();
	$input = apply_filters( 'psp_settings_' . $tab . '-' . $section . '_sanitize', $input );

	if ( 'main' === $section ) {
		// Check for extensions that aren't using new sections
		$input = apply_filters( 'psp_settings_' . $tab . '_sanitize', $input );
	}

	// Loop through each setting being saved and pass it through a sanitization filter
	foreach ( $input as $key => $value ) {
		// Get the setting type (checkbox, select, etc)
		$type = isset( $settings[ $tab ][ $key ]['type'] ) ? $settings[ $tab ][ $key ]['type'] : false;
		if ( $type ) {
			// Field type specific filter
			$input[ $key ] = apply_filters( 'psp_settings_sanitize_' . $type, $value, $key );
		}
		// General filter
		$input[ $key ] = apply_filters( 'psp_settings_sanitize', $input[ $key ], $key );
	}

	// Loop through the whitelist and unset any that are empty for the tab being saved
	$main_settings    = $section == 'main' ? $settings[ $tab ] : array(); // Check for extensions that aren't using new sections
	$section_settings = ! empty( $settings[ $tab ][ $section ] ) ? $settings[ $tab ][ $section ] : array();
	$found_settings   = array_merge( $main_settings, $section_settings );

	if ( ! empty( $found_settings ) ) {
		foreach ( $found_settings as $key => $value ) {
			// settings used to have numeric keys, now they have keys that match the option ID. This ensures both methods work
			if ( is_numeric( $key ) ) {
				$key = $value['id'];
			}
			if ( empty( $input[ $key ] ) ) {
				unset( $psp_options[ $key ] );
			}
		}
	}

	// Merge our new settings with the existing
	$output = array_merge( $psp_options, $input );

	add_settings_error( 'psp-notices', '', __( 'Settings Updated.', 'psp_projects' ), 'updated' );

	return $output;
}

function psp_text_callback( $args ) {

	global $psp_options;

	$args = wp_parse_args( $args, array(
		'value'       => false,
		'id'          => '',
		'desc'        => '',
		'default'     => '',
		'faux'        => false,
		'readonly'    => false,
		'size'        => 'regular',
		'placeholder' => false,
	) );

	if ( $args['value'] ) {

		$value = $args['value'];

	} elseif ( isset( $psp_options[ $args['id'] ] ) ) {

		$value = $psp_options[ $args['id'] ];

	} else {

		$value = $args['default'];
	}

	if ( $args['faux'] === true ) {
		$args['readonly'] = true;
		$value            = $args['default'];
		$name             = '';
	} else {
		$name = 'name="psp_settings[' . esc_attr( $args['id'] ) . ']"';
	}

	$readonly = $args['readonly'] === true ? ' readonly="readonly"' : '';
	$html     = '<input type="text" class="' . sanitize_html_class( $args['size'] ) . '-text" id="psp_settings[' . psp_sanitize_key( $args['id'] ) . ']" ' . $name . ' value="' . esc_attr( stripslashes( $value ) ) . '"' . $readonly . ( $args['placeholder'] ? "placeholder=\"$args[placeholder]\"" : '' ) . '/>';
	$html .= '<label for="psp_settings[' . psp_sanitize_key( $args['id'] ) . ']"> ' . wp_kses_post( $args['desc'] ) . '</label>';

	echo $html;
}

function psp_html_callback( $args ) {

	$html = '<label for="psp_settings[' . psp_sanitize_key( $args['id'] ) . ']"> ' . wp_kses_post( $args['desc'] ) . '</label>';

	echo $html;

}

function psp_hidden_callback( $args ) {

	global $psp_options;

	$args = wp_parse_args( $args, array(
		'value'   => false,
		'id'      => '',
		'name'    => false,
		'default' => '',
	) );

	if ( $args['value'] ) {

		$value = $args['value'];

	} elseif ( isset( $psp_options[ $args['id'] ] ) ) {

		$value = $psp_options[ $args['id'] ];

	} else {

		$value = $args['default'];
	}

	$name = $args['name'] !== false ? esc_attr( $args['name'] ) : 'psp_settings[' . esc_attr( $args['id'] ) . ']';
	?>
	<input type="hidden" name="<?php echo $name; ?>" value="<?php echo $value; ?>"/>
	<?php
}

function psp_button_link_callback( $args ) {

	if ( $args['button_text'] !== '' ) {
		$text = $args['button_text'];
	} else {
		$text = $args['name'];
	}

	echo '<a id="' . $args['button_id'] . '" class="button button-primary" href="' . $args['href'] . '" title="' . $args['name'] . '">' . $text . '</a>';

}

function psp_color_callback( $args ) {

	global $psp_options;

	$args = wp_parse_args( $args, array(
		'id'      => '',
		'default' => '',
		'desc'    => '',
		'value'   => false,
	) );

	if ( $args['value'] ) {

		$value = $args['value'];

	} elseif ( isset( $psp_options[ $args['id'] ] ) ) {

		$value = $psp_options[ $args['id'] ];

	} else {

		$value = $args['default'];
	}

	if ( strpos( $value, '#' ) === false ) {
		$value = "#$value";
	}

	$html = '<input class="color-field" id="psp_settings[' . psp_sanitize_key( $args['id'] ) . ']" name="psp_settings[' . esc_attr( $args['id'] ) . ']" value="' . esc_attr( $value ) . '" rel="' . esc_attr( $args['default'] ) . '" />';
	$html .= '<label for="psp_settings[' . psp_sanitize_key( $args['id'] ) . ']"> ' . wp_kses_post( $args['desc'] ) . '</label>';

	echo $html;

}

function psp_missing_callback( $args ) {
	printf(
		__( 'The callback function used for the %s setting is missing.', 'psp_projects' ),
		'<strong>' . $args['id'] . '</strong>'
	);
}

function psp_dummy_callback( $args ) {
	printf(
		__( 'The "dummy" Setting Type used on %s is meant to be used in conjunction with the %s filter set to "__return_false" in order to use a custom &lt;form&gt; rather than the included one.', 'psp_projects' ),
		'<strong>' . $args['id'] . '</strong>',
		'<em>psp_settings_section_' . $args['section'] . '_form</em>'
	);
}

function psp_upload_callback( $args ) {

	global $psp_options;

	$args = wp_parse_args( $args, array(
		'id'        => '',
		'default'   => '',
		'desc'      => '',
		'value'     => false,
		'size'      => 'regular',
		'button_id' => '',
	) );

	if ( $args['value'] ) {

		$value = $args['value'];

	} elseif ( isset( $psp_options[ $args['id'] ] ) ) {

		$value = $psp_options[ $args['id'] ];

	} else {

		$value = $args['default'];
	}

	$html = '<input type="text" id="' . $args['id'] . '" class="' . sanitize_html_class( $args['size'] ) . '-text" id="psp_settings[' . psp_sanitize_key( $args['id'] ) . ']" name="psp_settings[' . esc_attr( $args['id'] ) . ']" value="' . esc_attr( stripslashes( $value ) ) . '"/>';
	$html .= '<span>&nbsp;<input type="button" id="' . $args['button_id'] . '" class="psp_settings_upload_button button-secondary" value="' . __( 'Upload File', 'psp_projects' ) . '"/></span>';
	$html .= '<label for="psp_settings[' . psp_sanitize_key( $args['id'] ) . ']"> ' . wp_kses_post( $args['desc'] ) . '</label>';

	echo $html;

}

function psp_checkbox_callback( $args ) {

	global $psp_options;

	$args = wp_parse_args( $args, array(
		'id'      => '',
		'default' => '',
		'desc'    => '',
		'value'   => false,
		'faux'    => false,
	) );

	if ( $args['value'] ) {

		$value = $args['value'];

	} elseif ( isset( $psp_options[ $args['id'] ] ) ) {

		$value = $psp_options[ $args['id'] ];

	} else {

		$value = $args['default'];
	}

	if ( true === $args['faux'] ) {
		$name = '';
	} else {
		$name = 'name="psp_settings[' . psp_sanitize_key( $args['id'] ) . ']"';
	}

	$checked = checked( 1, $value, false );

	$html = '<input type="checkbox" id="psp_settings[' . psp_sanitize_key( $args['id'] ) . ']"' . $name . ' value="1" ' . $checked . '/>';
	$html .= '<label for="psp_settings[' . psp_sanitize_key( $args['id'] ) . ']"> ' . wp_kses_post( $args['desc'] ) . '</label>';

	echo $html;

}

function psp_select_callback( $args ) {

	global $psp_options;

	$args = wp_parse_args( $args, array(
		'id'          => '',
		'default'     => '',
		'desc'        => '',
		'value'       => false,
		'placeholder' => '',
		'chosen'      => false,
		'options'     => array(),
	) );

	if ( $args['value'] ) {

		$value = $args['value'];

	} elseif ( isset( $psp_options[ $args['id'] ] ) ) {

		$value = $psp_options[ $args['id'] ];

	} else {

		$value = $args['default'];
	}

	if ( $args['chosen'] ) {
		$chosen = 'class="psp-chosen"';
	} else {
		$chosen = '';
	}

	$html = '<select id="psp_settings[' . psp_sanitize_key( $args['id'] ) . ']" name="psp_settings[' . esc_attr( $args['id'] ) . ']" ' . $chosen . 'data-placeholder="' . esc_html( $args['placeholder'] ) . '" />';

	foreach ( $args['options'] as $option => $name ) {
		$selected = selected( $option, $value, false );
		$html .= '<option value="' . esc_attr( $option ) . '" ' . $selected . '>' . esc_html( $name ) . '</option>';
	}

	$html .= '</select>';

	$html .= '<label for="psp_settings[' . psp_sanitize_key( $args['id'] ) . ']"> ' . wp_kses_post( $args['desc'] ) . '</label>';

	echo $html;

}

function psp_header_callback( $args ) {
	echo '';
}

function psp_textarea_callback( $args ) {

	global $psp_options;

	$args = wp_parse_args( $args, array(
		'id'      => '',
		'default' => '',
		'desc'    => '',
		'value'   => false,
	) );

	if ( $args['value'] ) {

		$value = $args['value'];

	} elseif ( isset( $psp_options[ $args['id'] ] ) ) {

		$value = $psp_options[ $args['id'] ];

	} else {

		$value = $args['default'];
	}

	$html = '<textarea class="large-text" cols="50" rows="5" id="psp_settings[' . psp_sanitize_key( $args['id'] ) . ']" name="psp_settings[' . esc_attr( $args['id'] ) . ']">' . esc_textarea( stripslashes( $value ) ) . '</textarea>';
	$html .= '<label for="psp_settings[' . psp_sanitize_key( $args['id'] ) . ']"> ' . wp_kses_post( $args['desc'] ) . '</label>';

	echo $html;

}

function psp_license_key_callback( $args ) {

	global $psp_options;

	$status 	= get_option( 'edd_panorama_license_status' );
	$license 	= get_option( 'edd_panorama_license_key' );
	$options 	= get_option( 'psp_options' );

	if ( $status !== false && $status == 'valid' && isset( $psp_options['edd_panorama_license_key'] ) && !empty( $psp_options['edd_panorama_license_key'] ) ) {
		$html = '<span style="color:green;" class="psp-activation-notice">' . __( 'Active', 'psp_projects' ) . '</span>';
		$html .= wp_nonce_field( 'edd_panorama_nonce', 'edd_panorama_nonce', true, false );
		$html .= ' <input type="submit" class="button-secondary" name="edd_license_deactivate" value="' . __( 'Deactivate License', 'psp_projects' ) . '"/>';
	} else {
		$html = '<span style="color:red;" class="psp-activation-notice">' . __( 'Inactive', 'psp_projects' ) . '</span>';
		$html .= wp_nonce_field( 'edd_panorama_nonce', 'edd_panorama_nonce', true, false );
		if( isset( $psp_options['edd_panorama_license_key'] ) && !empty( $psp_options['edd_panorama_license_key'] ) ) {
			$html .= ' <input type="submit" class="button-secondary" name="edd_license_activate" value="' . __( 'Activate License', 'psp_projects' ) . '"/>';
			$html .= ' <a class="button" href="' . admin_url() . 'options-general.php?page=panorama-license&psp_activate_response=true">' . __( 'Check Activation Message', 'psp_projects' ) . '</a>';
		} else {
			$html .= '<strong>&nbsp;&nbsp;' . __( 'Please enter a license key and save to activate.', 'psp_projects' ) . '</strong>';
		}

	}

	echo $html;
}

function psp_repeater_callback( $args ) {

	global $psp_options;

	$args = wp_parse_args( $args, array(
		'id'                        => '',
		'input_name'                => false,
		'default'                   => '',
		'desc'                      => false,
		'fields'                    => array(),
		'values'                    => false,
		'sortable'                  => true,
		'collapsable'               => false,
		'collapsable_title'         => false,
		'collapsable_title_default' => '',
		'add_item_text'             => __( 'Add Item', 'psp_projects' ),
		'delete_item_text'          => __( 'Delete', 'psp_projects' ),
	) );

	if ( $args['values'] !== false ) {

		$values = $args['values'];

	} elseif ( isset( $psp_options[ $args['id'] ] ) ) {

		$values = (array) $psp_options[ $args['id'] ];

	} else {

		$values = isset( $args['default'] ) ? (array) $args['default'] : array();
	}

	$name = $args['input_name'] !== false ? $args['input_name'] : 'psp_settings[' . esc_attr( $args['id'] ) . ']';

	$field_count = count( $values ) >= 1 ? count( $values ) : 1;

	$repeater_classes = array(
		'psp-repeater',
	);

	if ( $args['collapsable'] ) {
		$repeater_classes[] = 'psp-repeater-collapsable';
	}

	if ( $args['sortable'] ) {
		$repeater_classes[] = 'psp-repeater-sortable';
	}
	?>
	<div class="<?php echo implode( ' ', $repeater_classes ); ?>" data-psp-repeater
		<?php echo $args['collapsable'] ? 'data-repeater-collapsable' : ''; ?>
		<?php echo $args['sortable'] ? 'data-repeater-sortable' : ''; ?>>
		<div class="psp-repeater-list" data-repeater-list="<?php echo $name; ?>">
			<?php for ( $i = 0; $i < $field_count; $i ++ ) : ?>
				<div class="psp-repeater-item closed" data-repeater-item
					<?php echo ! isset( $values[ $i ] ) ? 'data-repeater-item-dummy style="display: none;"' : '' ?>>
					<table class="psp-repeater-item-header">
						<tr>
							<td class="psp-repeater-item-handle" data-repeater-item-handle>
								<?php echo $i + 1; ?>
							</td>

							<td class="psp-repeater-collapsable-handle" data-repeater-collapsable-handle>
								<?php if ( $args['collapsable'] ) : ?>
									<h3>
										<?php
										if ( $args['collapsable_title'] !== false ) {
											if ( isset( $values[ $i ][ $args['collapsable_title'] ] ) ) : ?>
												<span data-repeater-collapsable-handle-title>
													<?php echo $values[ $i ][ $args['collapsable_title'] ]; ?>
												</span>
											<?php endif; ?>

											<span data-repeater-collapsable-handle-default
												<?php echo isset( $values[ $i ][ $args['collapsable_title'] ] ) ?
													'style="display: none;"' : ''; ?>>
												<?php echo $args['collapsable_title_default']; ?>
											</span>
											<?php
										} else {
											echo __( 'Collapse / Expand', 'psp_projects' );
										}
										?>

										<span class="psp-repeater-collapsable-handle-arrow">
											<span class="opened dashicons dashicons-arrow-up"></span>
											<span class="closed dashicons dashicons-arrow-down"></span>
										</span>
									</h3>
								<?php endif; ?>
							</td>

							<td class="psp-repeater-item-actions">
								<input data-repeater-delete type="button" class="psp-repeater-delete button"
								       value="<?php echo $args['delete_item_text']; ?>"/>
							</td>
						</tr>
					</table>

					<div class="psp-repeater-item-content">
						<div class="psp-repeater-item-fields">
							<?php
							foreach ( (array) $args['fields'] as $field_ID => $field ) {

								$field = wp_parse_args( $field, array(
									'type'    => 'text',
									'label'   => $field_ID,
									'classes' => array(),
									'args'    => array(),
								) );

								$field['args'] = wp_parse_args( $field['args'], array(
									'id'    => $field_ID,
									'value' => isset( $values[ $i ][ $field_ID ] ) ? $values[ $i ][ $field_ID ] : null,
									'desc'  => '',
								) );

								$field['classes'][] = 'psp-repeater-item-field';
								$field['classes'][] = "psp-repeater-item-field-$field[type]";
								$field['classes'][] = "psp-repeater-item-field-$field_ID";


								if ( is_callable( "psp_{$field['type']}_callback" ) ) : ?>
									<div class="<?php echo implode( ' ', $field['classes'] ); ?>">

										<label class="psp-repeater-item-field-label">
											<?php echo esc_attr( $field['label'] ); ?>
										</label>
										<br/>

										<?php call_user_func( "psp_{$field['type']}_callback", $field['args'] ); ?>

									</div>
								<?php endif;
							}
							?>
						</div>
					</div>
				</div>
			<?php endfor; ?>
		</div>

		<input data-repeater-create type="button" class="psp-repeater-add button"
		       value="<?php echo $args['add_item_text']; ?>"/>

		<?php if ( $args['desc'] ) : ?>
			<p class="description">
				<?php echo strip_tags( $args['desc'], '<br><em><strong><i><b>' ); ?>
			</p>
		<?php endif; ?>
	</div>
	<?php
}

add_filter( 'psp_settings_sanitize_license_key', 'psp_license_key_sanitize', 10, 2 );
function psp_license_key_sanitize( $value, $key ) {
	$old = get_option( 'edd_panorama_license_key' );
	if ( $old && $old != $value ) {
		delete_option( 'edd_panorama_license_status' ); // new license has been entered, so must reactivate
	}

	return $value;
}

function psp_hook_callback( $args ) {
	do_action( 'psp_' . $args['id'], $args );
}

function psp_sanitize_key( $key ) {
	$raw_key = $key;
	$key     = preg_replace( '/[^a-zA-Z0-9_\-\.\:\/]/', '', $key );

	return apply_filters( 'psp_sanitize_key', $key, $raw_key );
}

function psp_get_registered_settings() {

	/**
	 * 'Whitelisted' Project Panorama settings, filters are provided for each settings
	 * section to allow extensions and other plugins to add their own settings
	 */
	$psp_settings = array(
		/** General Settings */
		'psp_settings_general'       => apply_filters( 'psp_settings_general',
			array(
				'main' => array(
					'edd_panorama_license_key'      => array(
						'id'   => 'edd_panorama_license_key',
						'name' => __( 'License Key', 'psp_projects' ),
						'desc' => __( 'Enter your License Key', 'psp_projects' ),
						'type' => 'text',
					),
					'edd_panorama_activate_license' => array(
						'id'   => 'edd_panorama_activate_license',
						'name' => __( 'Activate License', 'psp_projects' ),
						'type' => 'license_key',
					),
					'psp_slug'                      => array(
						'id'      => 'psp_slug',
						'name'    => __( 'Project Slug', 'psp_projects' ),
						'type'    => 'text',
						'default' => 'panorama',
					),
					'psp_back'                      => array(
						'id'   => 'psp_back',
						'name' => __( 'Back Button Link', 'psp_projects' ),
						'type' => 'text',
						'desc' => __( 'Leave blank for dashboard', 'psp_projects' ),
					)
				),
			)
		),
		'psp_settings_appearance'    => apply_filters( 'psp_settings_appearance',
			array(
				'main'           => array(
					'psp_settings_appearance_general' => array(
						'id'   => 'psp_settings_appearance_general',
						'name' => '<h3>' . __( 'General Settings', 'psp_projects' ) . '</h3>',
						'desc' => '',
						'type' => 'header',
					),
					'psp_logo'                        => array(
						'id'        => 'psp_logo',
						'name'      => __( 'Logo for Panorama Pages', 'psp_projects' ),
						'type'      => 'upload',
						'button_id' => 'psp_upload_image_button',
					),
					'psp_logo_link'                        => array(
						'id'        => 'psp_logo_link',
						'name'      => __( 'Logo Link', 'psp_projects' ),
						'desc'		=>	__( 'Where would you like the logo to link?', 'psp_projects' ),
						'type'      => 'text',
					),
					'psp_favicon'					=> array(
						'id'		=>	'psp_favicon',
						'name'		=>	__( 'Favicon', 'psp_projects' ),
						'desc'		=>	__( 'Upload a 128 x 128 png or ICO to use as a favicon on Panorama pages', 'psp_projects' ),
						'type'		=>	'upload',
						'button_id'	=>	'psp_upload_favicon_button'
					),
					'psp_dashboard_sorting'	=>	array(
						'id'		=>	'psp_dashboard_sorting',
						'name'		=>	__( 'Dashboard sort by', 'psp_projects' ),
						'desc'		=>	__( 'How do you want dashboard projects to be sorted?', 'psp_projects' ),
						'type'		=>	'select',
						'options'	=>	array(
							'default'		=>	__( 'Creation date', 'psp_projects' ),
							'start_date'	=>	__( 'Start date', 'psp_projects' ),
							'end_date'		=>	__( 'End date', 'psp_projects' ),
							'alphabetical'	=>	__( 'Alphabetical', 'psp_projects' ),
						),
					),
					'psp_dashboard_sort_order' => array(
						'id'	=>	'psp_dashboard_sort_order',
						'name'	=>	__( 'Dashboard sort order', 'psp_projects' ),
						'type'	=>	'select',
						'options'	=>	array(
							'asc'	=>	__( 'Ascending', 'psp_projects' ),
							'desc'	=>	__( 'Descending', 'psp_projects' )
						),
					),
					'psp_use_custom_template'         => array(
						'id'   => 'psp_use_custom_template',
						'name' => __( 'Use Custom Template', 'psp_projects' ),
						'type' => 'checkbox',
					),
					'psp_custom_template'             => array(
						'id'      => 'psp_custom_template',
						'name'    => __( 'Choose Custom Template', 'psp_projects' ),
						'type'    => 'select',
						'options' => psp_get_project_templates(),
						'desc'    => __( 'Warning: This is an unsupported feature and might not work or display well with all themes.', 'psp_projects' )
					),
					'psp_use_rtl'	=>	array(
						'id'	=>	'psp_use_rtl',
						'name'	=>	__( 'Display in RTL', 'psp_projects' ),
						'type'	=>	'checkbox'
					),
				),
				'header'         => array(
					'psp_settings_appearance_header' => array(
						'id'   => 'psp_settings_appearance_header',
						'name' => '<h3>' . __( 'Header Settings', 'psp_projects' ) . '</h3>',
						'desc' => '',
						'type' => 'header',
					),
					'psp_menu_background'            => array(
						'id'      => 'psp_menu_background',
						'name'    => __( 'Menu Background', 'psp_projects' ),
						'type'    => 'color',
						'default' => '#2a3542'
					),
					'psp_menu_text'                  => array(
						'id'      => 'psp_menu_text',
						'name'    => __( 'Menu Text', 'psp_projects' ),
						'type'    => 'color',
						'default' => '#fff'
					),
					'psp_reset_colors'               => array(
						'id'          => 'psp_reset_colors',
						'name'        => __( 'Reset Colors to Default', 'psp_projects' ),
						'type'        => 'button_link',
						'href'        => '',
						'button_id'   => 'psp-reset-colors',
						'button_text' => __( 'Reset', 'psp_projects' )
					)
				),
				'body'           => array(
					'psp_settings_appearance_body' => array(
						'id'   => 'psp_settings_appearance_body',
						'name' => '<h3>' . __( 'Body Settings', 'psp_projects' ) . '</h3>',
						'desc' => '',
						'type' => 'header',
					),
					'psp_body_background'          => array(
						'id'      => 'psp_body_background',
						'name'    => __( 'Body Background', 'psp_projects' ),
						'type'    => 'color',
						'default' => '#f1f2f7'
					),
					'psp_body_heading'             => array(
						'id'      => 'psp_body_heading',
						'name'    => __( 'Background Heading', 'psp_projects' ),
						'type'    => 'color',
						'default' => '#999'
					),
					'psp_background_link'             => array(
						'id'      => 'psp_background_link',
						'name'    => __( 'Background Link', 'psp_projects' ),
						'type'    => 'color',
						'default' => '#3299bb'
					),
					'psp_sub_nav_link'             => array(
						'id'      => 'psp_sub_nav_link',
						'name'    => __( 'Subnav Link', 'psp_projects' ),
						'type'    => 'color',
						'default' => '#555555'
					),
					'psp_sub_nav_link_active'             => array(
						'id'      => 'psp_sub_nav_link_active',
						'name'    => __( 'Subnav Link Active', 'psp_projects' ),
						'type'    => 'color',
						'default' => '#000000'
					),
					'psp_sub_nav_link_accent'             => array(
						'id'      => 'psp_sub_nav_link_accent',
						'name'    => __( 'Subnav Link Accent', 'psp_projects' ),
						'type'    => 'color',
						'default' => '#4ecdc4'
					),
					'psp_body_text'                => array(
						'id'      => 'psp_body_text',
						'name'    => __( 'Body Text', 'psp_projects' ),
						'type'    => 'color',
						'default' => '#333'
					),
					'psp_body_link'                => array(
						'id'      => 'psp_body_link',
						'name'    => __( 'Body Link', 'psp_projects' ),
						'type'    => 'color',
						'default' => '#000'
					),
					'psp_footer_background'        => array(
						'id'      => 'psp_footer_background',
						'name'    => __( 'Footer Background', 'psp_projects' ),
						'type'    => 'color',
						'default' => '#2a3542'
					),
					'psp_timeline_color'           => array(
						'id'      => 'psp_timeline_color',
						'name'    => __( 'Timeline', 'psp_projects' ),
						'type'    => 'color',
						'default' => '#99C262'
					),
					'psp_reset_colors'             => array(
						'id'          => 'psp_reset_colors',
						'name'        => __( 'Reset Colors to Default', 'psp_projects' ),
						'type'        => 'button_link',
						'href'        => '',
						'button_id'   => 'psp-reset-colors',
						'button_text' => __( 'Reset', 'psp_projects' )
					)
				),
				'phases'         => array(
					'psp_settings_appearance_phases' => array(
						'id'   => 'psp_settings_appearance_phases',
						'name' => '<h3>' . __( 'Phases Settings', 'psp_projects' ) . '</h3>',
						'desc' => '',
						'type' => 'header',
					),
					'psp_accent_color_1'             => array(
						'id'      => 'psp_accent_color_1',
						'name'    => __( 'Accent Color #1', 'psp_projects' ),
						'type'    => 'color',
						'default' => '#3299BB'
					),
					'psp_accent_color_1_txt'         => array(
						'id'      => 'psp_accent_color_1_txt',
						'name'    => __( 'Accent Color #1 Text', 'psp_projects' ),
						'type'    => 'color',
						'default' => '#FFFFFF'
					),
					'psp_accent_color_2'             => array(
						'id'      => 'psp_accent_color_2',
						'name'    => __( 'Accent Color #2', 'psp_projects' ),
						'type'    => 'color',
						'default' => '#4ECDC4'
					),
					'psp_accent_color_2_txt'         => array(
						'id'      => 'psp_accent_color_2_txt',
						'name'    => __( 'Accent Color #2 Text', 'psp_projects' ),
						'type'    => 'color',
						'default' => '#FFFFFF'
					),
					'psp_accent_color_3'             => array(
						'id'      => 'psp_accent_color_3',
						'name'    => __( 'Accent Color #3', 'psp_projects' ),
						'type'    => 'color',
						'default' => '#CBE86B'
					),
					'psp_accent_color_3_txt'         => array(
						'id'      => 'psp_accent_color_3_txt',
						'name'    => __( 'Accent Color #3 Text', 'psp_projects' ),
						'type'    => 'color',
						'default' => '#FFFFFF'
					),
					'psp_accent_color_4'             => array(
						'id'      => 'psp_accent_color_4',
						'name'    => __( 'Accent Color #4', 'psp_projects' ),
						'type'    => 'color',
						'default' => '#FF6B6B'
					),
					'psp_accent_color_4_txt'         => array(
						'id'      => 'psp_accent_color_4_txt',
						'name'    => __( 'Accent Color #4 Text', 'psp_projects' ),
						'type'    => 'color',
						'default' => '#FFFFFF'
					),
					'psp_accent_color_5'             => array(
						'id'      => 'psp_accent_color_5',
						'name'    => __( 'Accent Color #5', 'psp_projects' ),
						'type'    => 'color',
						'default' => '#C44D58'
					),
					'psp_accent_color_5_txt'         => array(
						'id'      => 'psp_accent_color_5_txt',
						'name'    => __( 'Accent Color #5 Text', 'psp_projects' ),
						'type'    => 'color',
						'default' => '#FFFFFF'
					),
					'psp_reset_colors'               => array(
						'id'          => 'psp_reset_colors',
						'name'        => __( 'Reset Colors to Default', 'psp_projects' ),
						'type'        => 'button_link',
						'href'        => '',
						'button_id'   => 'psp-reset-colors',
						'button_text' => __( 'Reset', 'psp_projects' )
					)
				),
				'custom_styling' => array(
					'psp_open_css' => array(
						'id'   => 'psp_open_css',
						'name' => __( 'Custom CSS', 'psp_projects' ),
						'type' => 'textarea'
					)
				),
				'calendar'       => array(
					'psp_calendar_language' => array(
						'id'      => 'psp_calendar_language',
						'name'    => __( 'Calendar Language', 'psp_projects' ),
						'type'    => 'select',
						'options' => psp_calendar_langauges(),
					),
				),
			)
		),
		'psp_settings_notifications' => apply_filters( 'psp_settings_notifications', array(
			'email' => array(
				'psp_email_header'    => array(
					'id'   => 'psp_email_header',
					'name' => '<h2>' . __( 'Default Email Settings', 'psp_projects' ) . '</h2>',
					'type' => 'header',
				),
				'psp_from_name'       => array(
					'id'   => 'psp_from_name',
					'name' => __( 'From Name', 'psp_projects' ),
					'type' => 'text',
					'desc'	=> __( 'Use <code>%current_user%</code> for current user\'s name', 'psp_projects' )
				),
				'psp_from_email'      => array(
					'id'   	=> 'psp_from_email',
					'name' 	=> __( 'From E-Mail', 'psp_projects' ),
					'type' 	=> 'text',
					'desc'	=> __( 'Use <code>%current_user%</code> for current user\'s e-mail', 'psp_projects' )
				),
				'psp_include_logo'    => array(
					'id'   => 'psp_include_logo',
					'name' => __( 'Include Logo', 'psp_projects' ),
					'type' => 'select',
					'options'	=>	array(
						'0'		=>	__( 'No', 'psp_projects' ),
						'1'		=>	__( 'Yes', 'psp_projects' ),
					),

				),
				'psp_default_subject' => array(
					'id'   => 'psp_default_subject',
					'name' => __( 'Subject Line', 'psp_projects' ),
					'type' => 'text',
				),
				'psp_default_message' => array(
					'id'   => 'psp_default_message',
					'name' => __( 'Message', 'psp_projects' ),
					'type' => 'textarea',
					'desc' => __( 'Available dynamic variables: <code>%project_title%</code> <code>%client%</code> <code>%project_url%</code> <code>%dashboard%</code> <code>%date%</code>', 'psp_projects' ),
				),
			)
		) ),
		'psp_settings_advanced'      => apply_filters( 'psp_settings_advanced',
			array(
				'main' => array(
					'psp_enable_wp_footer'	=>	array(
						'id'	=>	'psp_enable_wp_footer',
						'name'	=>	__( 'Enable wp_footer call on project pages', 'psp_projects' ),
						'type'	=>	'checkbox',
						'desc'	=>	__( 'If you\'re having issues with shortcodes try turning this on first.', 'psp_projects' )
					),
					'psp_restrict_media_gallery'	=>	array(
						'id'	=>	'psp_restrict_media_gallery',
						'name'	=>	__( 'Project Specific Media Gallery', 'psp_projects' ),
						'type'	=>	'checkbox',
						'desc'	=>	__( 'Enable this if you\'d only like files uploaded to a specific project to show up in the media gallery', 'psp_projects' ),
					),
					'psp_enable_wp_head'	=>	array(
						'id'	=>	'psp_enable_wp_head',
						'name'	=>	__( 'Enable wp_head call on project pages', 'psp_projects' ),
						'type'	=>	'checkbox',
						'desc'	=>	__( 'If you\'re having issues with shortcodes and wp_footer didnt work, try turning this on.', 'psp_projects' )
					),
					'psp_disable_file_obfuscation'	=>	array(
						'id'	=>	'psp_disable_file_obfuscation',
						'name'	=>	__( 'Turn off file link obfuscation', 'psp_projects' ),
						'type'	=>	'checkbox',
						'desc'	=>	__( 'If you\'re having trouble with people downloading documents, try turning this on.', 'psp_projects' )
					),
					'psp_lazyload_wysiwyg'	=>	array(
						'id'	=>	'psp_lazyload_wysiwyg',
						'name'	=>	__( 'Lazy load WYSIWYG editors', 'psp_projects' ),
						'type'	=>	'checkbox',
						'desc'	=>	__( 'If you\'re having trouble with performance while editing projects, try enabling this', 'psp_projects' )
					),
					'psp_disable_clone_post' => array(
						'id'   => 'psp_disable_clone_post',
						'name' => __( 'Disable Clone Post', 'psp_projects' ),
						'type' => 'checkbox',
						'desc' => __( 'If you\'re using the Duplicate Post plugin and getting errors, check this box.', 'psp_projects' )
					),
				)
			)
		),
		'psp_settings_addons'        => apply_filters( 'psp_settings_addons', array() ),
	);

	if( !psp_get_option('edd_panorama_license_key') ) unset( $psp_settings['psp_settings_general']['edd_panorama_activate_license'] );

	return apply_filters( 'psp_registered_settings', $psp_settings );
}

//function

function edd_panorama_license_page() {

	flush_rewrite_rules();

	$settings_tabs = psp_get_settings_tabs();
	$settings_tabs = empty( $settings_tabs ) ? array() : $settings_tabs;
	$active_tab    = isset( $_GET['tab'] ) && array_key_exists( $_GET['tab'], $settings_tabs ) ? $_GET['tab'] : 'psp_settings_general';
	$sections      = psp_get_settings_tab_sections( $active_tab );
	$key           = 'main';
	if ( is_array( $sections ) ) {
		$key = key( $sections );
	}
	$registered_sections = psp_get_settings_tab_sections( $active_tab );
	$section             = isset( $_GET['section'] ) && ! empty( $registered_sections ) && array_key_exists( $_GET['section'], $registered_sections ) ? $_GET['section'] : $key;
	ob_start();
	?>
	<div class="wrap">
		<h1 class="nav-tab-wrapper">
			<?php
			foreach ( psp_get_settings_tabs() as $tab_id => $tab_name ) {
				$tab_url = add_query_arg( array(
					'settings-updated' => false,
					'tab'              => $tab_id,
				) );
				// Remove the section from the tabs so we always end up at the main section
				$tab_url = remove_query_arg( 'section', $tab_url );
				$active  = $active_tab == $tab_id ? ' nav-tab-active' : '';
				echo '<a href="' . esc_url( $tab_url ) . '" title="' . esc_attr( $tab_name ) . '" class="nav-tab' . $active . '">';
				echo esc_html( $tab_name );
				echo '</a>';
			}
			?>
		</h1>
		<?php
		$number_of_sections = count( $sections );
		$number             = 0;
		if ( $number_of_sections > 1 ) {
			echo '<div><ul class="subsubsub">';
			foreach ( $sections as $section_id => $section_name ) {
				echo '<li>';
				$number ++;
				$tab_url = add_query_arg( array(
					'settings-updated' => false,
					'tab'              => $active_tab,
					'section'          => $section_id
				) );
				$class   = '';
				if ( $section == $section_id ) {
					$class = 'current';
				}
				echo '<a class="' . $class . '" href="' . esc_url( $tab_url ) . '">' . $section_name . '</a>';
				if ( $number != $number_of_sections ) {
					echo ' | ';
				}
				echo '</li>';
			}
			echo '</ul></div>';
		}
		?>
		<div id="tab_container">
			<?php if ( apply_filters( 'psp_settings_section_' . $section . '_form', true ) ) : ?>
			<form method="post" action="options.php">
				<?php do_action( 'psp_before_settings_table' ); ?>

				<table class="form-table">
					<?php endif;

					if ( ( 'psp_settings_general' == $active_tab ) && ( 'main' === $section ) ) {
						if ( isset( $_GET['psp_activate_response'] ) ) : ?>
							<div class="psp-status-message">
                            <pre>
                                <?php var_dump( psp_check_activation_response() ); ?>
                            </pre>
							</div>
						<?php endif;
					}

					settings_fields( 'psp_settings' );
					if ( 'main' === $section ) {
						do_action( 'psp_settings_tab_top', $active_tab );
					}
					do_action( 'psp_settings_tab_top_' . $active_tab . '_' . $section );

					if ( apply_filters( 'psp_settings_section_' . $section . '_form', true ) ) {
						do_settings_sections( 'psp_settings_' . $active_tab . '_' . $section );
					} else {
						// If Form is disabled, allow users to chuck whatever they'd like in here
						do_action( 'psp_settings_section_' . $section );
					}

					do_action( 'psp_settings_tab_bottom_' . $active_tab . '_' . $section );
					// For backwards compatibility
					if ( 'main' === $section ) {
						do_action( 'psp_settings_tab_bottom', $active_tab );
					}
					?>
					<?php if ( apply_filters( 'psp_settings_section_' . $section . '_form', true ) ) : ?>
				</table>

				<?php do_action( 'psp_after_settings_table' ); ?>
				<?php 
	
				// WordPress automatically gives the Submit Button an ID and Name of "submit"
				// This is not necessary and causes problems with jQuery submit()
				// https://api.jquery.com/submit/ under "Additional Notes"
				submit_button( null, 'primary', false ); 
				
				?>
			</form>
		<?php endif; ?>
		</div><!-- #tab_container-->
	</div><!-- .wrap -->
	<?php
	echo ob_get_clean();

}

function psp_get_project_templates() {

	$templates = wp_get_theme()->get_page_templates();

	if ( ! empty( $templates ) ) :

		$templates['page.php'] = __( 'Standard Page', 'psp_projects' );

	else:

		$templates['single.php'] = __( 'Single Post', 'psp_projects' );

	endif;

	array_unshift( $templates, __( 'Choose Template', 'psp_projects' ) );

	return $templates;

}

function psp_calendar_langauges() {

	$languages = array(
		'en'      => 'en',
		'ar-ma'   => 'ar-ma',
		'ar-sa'   => 'ar-sa',
		'ar-tn'   => 'ar-tn',
		'ar'      => 'ar',
		'bg'      => 'bg',
		'ca'      => 'ca',
		'cs'      => 'cs',
		'da'      => 'da',
		'de-at'   => 'de-at',
		'de'      => 'de',
		'el'      => 'el',
		'en-au'   => 'en-au',
		'en-ca'   => 'en-ca',
		'en-gb'   => 'en-gb',
		'en-ie'   => 'en-ie',
		'en-nz'   => 'en-nz',
		'es'      => 'es',
		'fa'      => 'fa',
		'fi'      => 'fi',
		'fr-ca'   => 'fr-ca',
		'fr-ch'   => 'fr-ch',
		'fr'      => 'fr',
		'he'      => 'he',
		'hi'      => 'hi',
		'hr'      => 'hr',
		'hu'      => 'hu',
		'id'      => 'id',
		'is'      => 'is',
		'it'      => 'it',
		'ja'      => 'ja',
		'ko'      => 'ko',
		'lt'      => 'lt',
		'lv'      => 'lv',
		'nb'      => 'nb',
		'nl'      => 'nl',
		'pl'      => 'pl',
		'pt-br'   => 'pt-br',
		'pt'      => 'pt',
		'ro'      => 'ro',
		'ru'      => 'ru',
		'sk'      => 'sk',
		'sl'      => 'sl',
		'sr-cyrl' => 'sr-cyrl',
		'sr'      => 'sr',
		'sv'      => 'sv',
		'th'      => 'th',
		'tr'      => 'tr',
		'uk'      => 'uk',
		'vi'      => 'vi',
		'zh-cn'   => 'zh-cn',
		'zh-tw'   => 'zh-tw',
	);

	return $languages;

}

add_action( 'admin_init', 'psp_check_if_rewrites_flushed' );
function psp_check_if_rewrites_flushed() {

	$flushed = get_option( 'psp_rewrites_flushed' );

	if ( $flushed != 'yes' ) {
		flush_rewrite_rules();
		update_option( 'psp_rewrites_flushed', 'yes' );
	}

}


/************************************
 * this illustrates how to activate
 * a license key
 *************************************/

function edd_panorama_activate_license() {

	// listen for our activate button to be clicked
	if ( isset( $_POST['edd_license_activate'] ) ) {

		// run a quick security check
		if ( ! check_admin_referer( 'edd_panorama_nonce', 'edd_panorama_nonce' ) ) {
			return;
		} // get out if we didn't click the Activate button

		// retrieve the license from the database
		$license = get_option( 'psp_settings' );
		$license = trim( $license['edd_panorama_license_key'] );

		if( isset($_POST['edd_panorama_license_key']) ) $license = $_POST['edd_panorama_license_key'];

		if( isset( $_POST['psp_settings']['edd_panorama_license_key'] ) ) {
			$license = $_POST['psp_settings']['edd_panorama_license_key'];
		}

		// data to send in our API request
		$api_params = array(
			'edd_action' => 'activate_license',
			'license'    => $license,
			'item_name'  => urlencode( EDD_PROJECT_PANORAMA ), // the name of our product in EDD
			'url'        => home_url()
		);

		// Call the custom API.
		$response = wp_remote_get( add_query_arg( $api_params, PROJECT_PANORAMA_STORE_URL ), array(
			'timeout'   => 15,
			'sslverify' => false
		) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

				$error_message = $response->get_error_message();

				if( is_wp_error( $response ) && ! empty( $error_message ) ) {
					$message = $response->get_error_message();
				} else {
					$message = __( 'An error occurred, please try again.', 'psp_projects' );
				}

			} else {
				$license_data = json_decode( wp_remote_retrieve_body( $response ) );
				if ( false === $license_data->success ) {
					switch( $license_data->error ) {
						case 'expired' :
							$message = sprintf(
								__( 'Your license key expired on %s.' ),
								date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
							);
							break;
						case 'revoked' :
							$message = __( 'Your license key has been disabled.', 'psp_projects' );
							break;
						case 'missing' :
							$message = __( 'Invalid license.', 'psp_projects' );
							break;
						case 'invalid' :
						case 'site_inactive' :
							$message = __( 'Your license is not active for this URL.', 'psp_projects' );
							break;
						case 'item_name_mismatch' :
							$message = sprintf( __( 'This appears to be an invalid license key for %s.', 'psp_projects' ), EDD_PROJECT_PANORAMA );
							break;
						case 'no_activations_left':
							$message = __( 'Your license key has reached its activation limit.', 'psp_projects' );
							break;
						default :
							$message = __( 'An error occurred, please try again.', 'psp_projects' );
							break;
					}
				}
			}
			// Check if anything passed on a message constituting a failure
			if ( ! empty( $message ) ) {
				$base_url = admin_url( 'options-general.php?page=' . PSP_PLUGIN_LICENSE_PAGE );
				$redirect = add_query_arg( array( 'sl_activation' => 'false', 'message' => urlencode( $message ) ), $base_url );
				wp_redirect( $redirect );
				exit();
			}

		// $license_data->license will be either "active" or "inactive"
		update_option( 'edd_panorama_license_status', $license_data->license );

	}


}

add_action( 'admin_init', 'edd_panorama_activate_license', 0 );

function psp_check_activation_response() {

	// retrieve the license from the database
	$license = get_option( 'psp_settings' );
	$license = trim( $license['edd_panorama_license_key'] );

	// data to send in our API request
	$api_params = array(
		'edd_action' => 'activate_license',
		'license'    => $license,
		'item_name'  => urlencode( EDD_PROJECT_PANORAMA ), // the name of our product in EDD
		'url'        => home_url()
	);

	// Call the custom API.
	$response = wp_remote_get( add_query_arg( $api_params, PROJECT_PANORAMA_STORE_URL ), array(
		'timeout'   => 15,
		'sslverify' => false
	) );

	return $response;

}


/***********************************************
 * Illustrates how to deactivate a license key.
 * This will descrease the site count
 ***********************************************/

function edd_panorama_deactivate_license() {

	// listen for our activate button to be clicked
	if ( isset( $_POST['edd_license_deactivate'] ) ) {

		// run a quick security check
		if ( ! check_admin_referer( 'edd_panorama_nonce', 'edd_panorama_nonce' ) ) {
			return;
		} // get out if we didn't click the Activate button

		// retrieve the license from the database
		$license = get_option( 'psp_settings' );
		$license = trim( $license['edd_panorama_license_key'] );

		// data to send in our API request
		$api_params = array(
			'edd_action' => 'deactivate_license',
			'license'    => $license,
			'item_name'  => urlencode( EDD_PROJECT_PANORAMA ) // the name of our product in EDD
		);

		// Call the custom API.
		$response = wp_remote_get( add_query_arg( $api_params, PROJECT_PANORAMA_STORE_URL ), array(
			'timeout'   => 15,
			'sslverify' => false
		) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) ) {
			return false;
		}

		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		// $license_data->license will be either "deactivated" or "failed"
		if ( $license_data->license == 'deactivated' ) {
			delete_option( 'edd_panorama_license_status' );
		}

	}
}

add_action( 'admin_init', 'edd_panorama_deactivate_license' );


/************************************
 * this illustrates how to check if
 * a license key is still valid
 * the updater does this for you,
 * so this is only needed if you
 * want to do something custom
 *************************************/

function edd_panorama_check_license() {

	global $wp_version;

	$license = get_option( 'psp_settings' );
	$license = trim( $license['edd_panorama_license_key'] );

	$api_params = array(
		'edd_action' => 'check_license',
		'license'    => $license,
		'item_name'  => urlencode( EDD_PROJECT_PANORAMA )
	);

	// Call the custom API.
	$response = wp_remote_get( add_query_arg( $api_params, PROJECT_PANORAMA_STORE_URL ), array(
		'timeout'   => 15,
		'sslverify' => false
	) );

	if ( is_wp_error( $response ) ) {
		return false;
	}

	$license_data = json_decode( wp_remote_retrieve_body( $response ) );

	if ( $license_data->license == 'valid' ) {
		echo 'valid';
		exit;
		// this license is still valid
	} else {
		echo 'invalid';
		exit;
		// this license is no longer valid
	}
}

function psp_license_admin_notices() {
	if ( isset( $_GET['sl_activation'] ) && ! empty( $_GET['message'] ) ) {
		switch( $_GET['sl_activation'] ) {
			case 'false':
				$message = urldecode( $_GET['message'] );
				?>
				<div class="error">
					<p><?php echo $message; ?></p>
				</div>
				<?php
				break;
			case 'true':
			default:
				// Developers can put a custom success message here for when activation is successful if they way.
				break;
		}
	}
}
add_action( 'admin_notices', 'psp_license_admin_notices' );
