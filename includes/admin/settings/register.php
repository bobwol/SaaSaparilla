<?php
/**
 * Register settings
 *
 * @package     SaaSaparilla\Admin\Settings\Register
 * @since       1.0.0
 */


// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Retrieve the settings tabs
 *
 * @since       1.0.0
 * @return      array $tabs The registered settings tabs
 */
function saasaparilla_get_settings_tabs() {
	$settings = saasaparilla_get_registered_settings();

	$tabs               = array();
	$tabs['general']    = __( 'General', 'saasaparilla' );
	
	return apply_filters( 'saasaparilla_settings_tabs', $tabs );
}


/**
 * Retrieve the array of plugin settings
 *
 * @since       1.0.0
 * @return      array $saasaparilla_settings The registered settings
 */
function saasaparilla_get_registered_settings() {
	$saasaparilla_settings = array(
		// General Settings
		'general' => apply_filters( 'saasaparilla_settings_general', array(
			array(
				'id'        => 'general_header',
				'name'      => __( 'General Settings', 'saasaparilla' ),
				'desc'      => '',
				'type'      => 'header'
			)
		) )
	);

	return apply_filters( 'saasaparilla_registered_settings', $saasaparilla_settings );
}


/**
 * Retrieve an option
 *
 * @since       1.0.0
 * @global      array $saasaparilla_options The SaaSaparilla options
 * @return      mixed
 */
function saasaparilla_get_option( $key = '', $default = false ) {
	global $saasaparilla_options;

	$value = ! empty( $saasaparilla_options[$key] ) ? $saasaparilla_options[$key] : $default;
	$value = apply_filters( 'saasaparilla_get_option', $value, $key, $default );

	return apply_filters( 'saasaparilla_get_option_' . $key, $value, $key, $default );
}


/**
 * Retrieve all options
 *
 * @since       1.0.0
 * @return      array $saasaparilla_options The SaaSaparilla options
 */
function saasaparilla_get_settings() {
	$saasaparilla_settings = get_option( 'saasaparilla_settings' );

	if( empty( $saasaparilla_settings ) ) {
		$saasaparilla_settings = array();

		update_option( 'saasaparilla_settings', $saasaparilla_settings );
	}

	return apply_filters( 'saasaparilla_get_settings', $saasaparilla_settings );
}


/**
 * Add settings sections and fields
 *
 * @since       1.0.0
 * @return      void
 */
function saasaparilla_register_settings() {
	if( get_option( 'saasaparilla_settings' ) == false ) {
		add_option( 'saasaparilla_settings' );
	}

	foreach( saasaparilla_get_registered_settings() as $tab => $settings ) {
		add_settings_section(
			'saasaparilla_settings_' . $tab,
			__return_null(),
			'__return_false',
			'saasaparilla_settings_' . $tab
		);

		foreach( $settings as $option ) {
			$name = isset( $option['name'] ) ? $option['name'] : '';

			add_settings_field(
				'saasaparilla_settings[' . $option['id'] . ']',
				$name,
				function_exists( 'saasaparilla_' . $option['type'] . '_callback' ) ? 'saasaparilla_' . $option['type'] . '_callback' : 'saasaparilla_missing_callback',
				'saasaparilla_settings_' . $tab,
				'saasaparilla_settings_' . $tab,
				array(
					'section'       => $tab,
					'id'            => isset( $option['id'] )           ? $option['id']             : null,
					'desc'          => ! empty( $option['desc'] )       ? $option['desc']           : '',
					'name'          => isset( $option['name'] )         ? $option['name']           : null,
					'size'          => isset( $option['size'] )         ? $option['size']           : null,
					'options'       => isset( $option['options'] )      ? $option['options']        : '',
					'std'           => isset( $option['std'] )          ? $option['std']            : '',
					'min'           => isset( $option['min'] )          ? $option['min']            : null,
					'max'           => isset( $option['max'] )          ? $option['max']            : null,
					'step'          => isset( $option['step'] )         ? $option['step']           : null,
					'placeholder'   => isset( $option['placeholder'] )  ? $option['placeholder']    : null,
					'rows'          => isset( $option['rows'] )         ? $option['rows']           : null,
					'buttons'       => isset( $option['buttons'] )      ? $option['buttons']        : null,
					'wpautop'       => isset( $option['wpautop'] )      ? $option['wpautop']        : null,
					'teeny'         => isset( $option['teeny'] )        ? $option['teeny']          : null,
					'notice'        => isset( $option['notice'] )       ? $option['notice']         : false,
					'style'         => isset( $option['style'] )        ? $option['style']          : null,
					'header'        => isset( $option['header'] )       ? $option['header']         : null,
					'icon'          => isset( $option['icon'] )         ? $option['icon']           : null,
					'class'         => isset( $option['class'] )        ? $option['class']          : null
				)
			);
		}
	}

	register_setting( 'saasaparilla_settings', 'saasaparilla_settings', 'saasaparilla_settings_sanitize' );
}
add_action( 'admin_init', 'saasaparilla_register_settings' );


/**
 * Settings sanitization
 *
 * @since       1.0.0
 * @param       array $input The value entered in the field
 * @global      array $saasaparilla_options The SaaSaparilla options
 * @return      string $input The sanitized value
 */
function saasaparilla_settings_sanitize( $input = array() ) {
	global $saasaparilla_options;

	if( empty( $_POST['_wp_http_referer'] ) ) {
		return $input;
	}
	
	parse_str( $_POST['_wp_http_referer'], $referrer );

	$settings   = saasaparilla_get_registered_settings();
	$tab        = isset( $referrer['tab'] ) ? $referrer['tab'] : 'settings';

	$input = $input ? $input : array();
	$input = apply_filters( 'saasaparilla_settings_' . $tab . '_sanitize', $input );

	foreach( $input as $key => $value ) {
		$type = isset( $settings[$tab][$key]['type'] ) ? $settings[$tab][$key]['type'] : false;

		if( $type ) {
			// Field type specific filter
			$input[$key] = apply_filters( 'saasaparilla_settings_sanitize_' . $type, $value, $key );
		}

		// General filter
		$input[$key] = apply_filters( 'saasaparilla_settings_sanitize', $input[$key], $key );
	}

	if( ! empty( $settings[$tab] ) ) {
		foreach( $settings[$tab] as $key => $value ) {
			if( is_numeric( $key ) ) {
				$key = $value['id'];
			}

			if( empty( $input[$key] ) || ! isset( $input[$key] ) ) {
				unset( $saasaparilla_options[$key] );
			}
		}
	}

	// Merge our new settings with the existing
	$input = array_merge( $saasaparilla_options, $input );

	add_settings_error( 'saasaparilla-notices', '', __( 'Settings updated.', 'saasaparilla' ), 'updated' );

	return $input;
}


/**
 * Sanitize text fields
 *
 * @since       1.0.0
 * @param       array $input The value entered in the field
 * @return      string $input The sanitized value
 */
function saasaparilla_sanitize_text_field( $input ) {
	return trim( $input );
}
add_filter( 'saasaparilla_settings_sanitize_text', 'saasaparilla_sanitize_text_field' );


/**
 * Header callback
 *
 * @since       1.0.0
 * @param       array $args Arguments passed by the setting
 * @return      void
 */
function saasaparilla_header_callback( $args ) {
	echo '<hr />';
}


/**
 * Checkbox callback
 *
 * @since       1.0.0
 * @param       array $args Arguments passed by the setting
 * @global      array $saasaparilla_options The SaaSaparilla options
 * @return      void
 */
function saasaparilla_checkbox_callback( $args ) {
	global $saasaparilla_options;

	$checked = isset( $saasaparilla_options[$args['id']] ) ? checked( 1, $saasaparilla_options[$args['id']], false ) : '';

	$html  = '<input type="checkbox" id="saasaparilla_settings[' . $args['id'] . ']" name="saasaparilla_settings[' . $args['id'] . ']" value="1" ' . $checked . '/>&nbsp;';
	$html .= '<label for="saasaparilla_settings[' . $args['id'] . ']">' . $args['desc'] . '</label>';

	echo $html;
}


/**
 * Color callback
 *
 * @since       1.0.0
 * @param       array $args Arguments passed by the settings
 * @global      array $saasaparilla_options The SaaSaparilla options
 * @return      void
 */
function saasaparilla_color_callback( $args ) {
	global $saasaparilla_options;

	if( isset( $saasaparilla_options[$args['id']] ) ) {
		$value = $saasaparilla_options[$args['id']];
	} else {
		$value = isset( $args['std'] ) ? $args['std'] : '';
	}

	$default = isset( $args['std'] ) ? $args['std'] : '';
	$size    = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';

	$html  = '<input type="text" class="saasaparilla-color-picker" id="saasaparilla_settings[' . $args['id'] . ']" name="saasaparilla_settings[' . $args['id'] . ']" value="' . esc_attr( $value ) . '" data-default-color="' . esc_attr( $default ) . '" />&nbsp;';
	$html .= '<span class="saasaparilla-color-picker-label"><label for="saasaparilla_settings[' . $args['id'] . ']">' . $args['desc'] . '</label></span>';

	echo $html;
}


/**
 * Editor callback
 *
 * @since       1.0.0
 * @param       array $args Arguments passed by the setting
 * @global      array $saasaparilla_options The SaaSaparilla options
 * @return      void
 */
function saasaparilla_editor_callback( $args ) {
	global $saasaparilla_options;

	if( isset( $saasaparilla_options[$args['id']] ) ) {
		$value = $saasaparilla_options[$args['id']];
	} else {
		$value = isset( $args['std'] ) ? $args['std'] : '';
	}

	$rows       = ( isset( $args['rows'] ) && ! is_numeric( $args['rows'] ) ) ? $args['rows'] : '10';
	$wpautop    = isset( $args['wpautop'] ) ? $args['wpautop'] : true;
	$buttons    = isset( $args['buttons'] ) ? $args['buttons'] : true;
	$teeny      = isset( $args['teeny'] ) ? $args['teeny'] : false;

	wp_editor(
		$value,
		'saasaparilla_settings_' . $args['id'],
		array(
			'wpautop'       => $wpautop,
			'media_buttons' => $buttons,
			'textarea_name' => 'saasaparilla_settings[' . $args['id'] . ']',
			'textarea_rows' => $rows,
			'teeny'         => $teeny
		)
	);
	echo '<br /><label for="saasaparilla_settings[' . $args['id'] . ']">' . $args['desc'] . '</label>';
}


/**
 * Info callback
 *
 * @since       1.0.0
 * @param       array $args Arguments passed by the setting
 * @global      array $saasaparilla_options The SaaSaparilla options
 * @return      void
 */
function saasaparilla_info_callback( $args ) {
	global $saasaparilla_options;

	$notice = ( $args['notice'] == true ? '-notice' : '' );
	$class  = ( isset( $args['class'] ) ? $args['class'] : '' );
	$style  = ( isset( $args['style'] ) ? $args['style'] : 'normal' );
	$header = '';

	if( isset( $args['header'] ) ) {
		$header = '<b>' . $args['header'] . '</b><br />';
	}

	echo '<div id="saasaparilla_settings[' . $args['id'] . ']" name="saasaparilla_settings[' . $args['id'] . ']" class="saasaparilla-info' . $notice . ' saasaparilla-info-' . $style . '">';

	if( isset( $args['icon'] ) ) {
		echo '<p class="saasaparilla-info-icon">';
		echo '<i class="fa fa-' . $args['icon'] . ' ' . $class . '"></i>';
		echo '</p>';
	}

	echo '<p class="saasaparilla-info-desc">' . $header . $args['desc'] . '</p>';
	echo '</div>';
}


/**
 * Multicheck callback
 *
 * @since       1.0.0
 * @param       array $args Arguments passed by the setting
 * @global      array $saasaparilla_options The SaaSaparilla options
 * @return      void
 */
function saasaparilla_multicheck_callback( $args ) {
	global $saasaparilla_options;

	if( ! empty( $args['options'] ) ) {
		foreach( $args['options'] as $key => $option ) {
			$enabled = ( isset( $saasaparilla_options[$args['id']][$key] ) ? $option : NULL );

			echo '<input name="saasaparilla_settings[' . $args['id'] . '][' . $key . ']" id="saasaparilla_settings[' . $args['id'] . '][' . $key . ']" type="checkbox" value="' . $option . '" ' . checked( $option, $enabled, false ) . ' />&nbsp;';
			echo '<label for="saasaparilla_settings[' . $args['id'] . '][' . $key . ']">' . $option . '</label><br />';
		}
		echo '<p class="description">' . $args['desc'] . '</p>';
	}
}


/**
 * Number callback
 *
 * @since       1.0.0
 * @param       array $args Arguments passed by the setting
 * @global      array $saasaparilla_options The SaaSaparilla options
 * @return      void
 */
function saasaparilla_number_callback( $args ) {
	global $saasaparilla_options;

	if( isset( $saasaparilla_options[$args['id']] ) ) {
		$value = $saasaparilla_options[$args['id']];
	} else {
		$value = isset( $args['std'] ) ? $args['std'] : '';
	}

	$max    = isset( $args['max'] ) ? $args['max'] : 999999;
	$min    = isset( $args['min'] ) ? $args['min'] : 0;
	$step   = isset( $args['step'] ) ? $args['step'] : 1;
	$size   = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';

	$html  = '<input type="number" step="' . esc_attr( $step ) . '" max="' . esc_attr( $max ) . '" min="' . esc_attr( $min ) . '" class="' . $size . '-text" id="saasaparilla_settings[' . $args['id'] . ']" name="saasaparilla_settings[' . $args['id'] . ']" value="' . esc_attr( stripslashes( $value ) ) . '" />&nbsp;';
	$html .= '<label for="saasaparilla_settings[' . $args['id'] . ']">' . $args['desc'] . '</label>';

	echo $html;
}


/**
 * Password callback
 * 
 * @since       1.0.0
 * @param       array $args Arguments passed by the settings
 * @global      array $saasaparilla_options The SaaSaparilla options
 * @return      void
 */
function saasaparilla_password_callback( $args ) {
	global $saasaparilla_options;

	if( isset( $saasaparilla_options[$args['id']] ) ) {
		$value = $saasaparilla_options[$args['id']];
	} else {
		$value = isset( $args['std'] ) ? $args['std'] : '';
	}

	$size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';

	$html  = '<input type="password" class="' . $size . '-text" id="saasaparilla_settings[' . $args['id'] . ']" name="saasaparilla_settings[' . $args['id'] . ']" value="' . esc_attr( $value )  . '" />&nbsp;';
	$html .= '<label for="saasaparilla_settings[' . $args['id'] . ']">' . $args['desc'] . '</label>';

	echo $html;
}


/**
 * Radio callback
 *
 * @since       1.0.0
 * @param       array $args Arguments passed by the setting
 * @global      array $saasaparilla_options The SaaSaparilla options
 * @return      void
 */
function saasaparilla_radio_callback( $args ) {
	global $saasaparilla_options;

	if( ! empty( $args['options'] ) ) {
		foreach( $args['options'] as $key => $option ) {
			$checked = false;

			if( isset( $saasaparilla_options[$args['id']] ) && $saasaparilla_options[$args['id']] == $key ) {
				$checked = true;
			} elseif( isset( $args['std'] ) && $args['std'] == $key && ! isset( $saasaparilla_options[$args['id']] ) ) {
				$checked = true;
			}

			echo '<input name="saasaparilla_settings[' . $args['id'] . ']" id="saasaparilla_settings[' . $args['id'] . '][' . $key . ']" type="radio" value="' . $key . '" ' . checked( true, $checked, false ) . '/>&nbsp;';
			echo '<label for="saasaparilla_settings[' . $args['id'] . '][' . $key . ']">' . $option . '</label><br />';
		}

		echo '<p class="description">' . $args['desc'] . '</p>';
	}
}


/**
 * Select callback
 * 
 * @since       1.0.0
 * @param       array $args Arguments passed by the setting
 * @global      array $saasaparilla_options The SaaSaparilla options
 * @return      void
 */
function saasaparilla_select_callback( $args ) {
	global $saasaparilla_options;

	if( isset( $saasaparilla_options[$args['id']] ) ) {
		$value = $saasaparilla_options[$args['id']];
	} else {
		$value = isset( $args['std'] ) ? $args['std'] : '';
	}

	$placeholder = isset( $args['placeholder'] ) ? $args['placeholder'] : '';

	$html = '<select id="saasaparilla_settings[' . $args['id'] . ']" name="saasaparilla_settings[' . $args['id'] . ']" placeholder="' . $placeholder . '" />';

	foreach( $args['options'] as $option => $name ) {
		$selected = selected( $option, $value, false );

		$html .= '<option value="' . $option . '" ' . $selected . '>' . $name . '</option>';
	}

	$html .= '</select>&nbsp;';
	$html .= '<label for="saasaparilla_settings[' . $args['id'] . ']">' . $args['desc'] . '</label>';

	echo $html;
}


/**
 * Text callback
 * 
 * @since       1.0.0
 * @param       array $args Arguments passed by the setting
 * @global      array $saasaparilla_options The SaaSaparilla options
 * @return      void
 */
function saasaparilla_text_callback( $args ) {
	global $saasaparilla_options;

	if( isset( $saasaparilla_options[$args['id']] ) ) {
		$value = $saasaparilla_options[$args['id']];
	} else {
		$value = isset( $args['std'] ) ? $args['std'] : '';
	}

	$size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';

	$html  = '<input type="text" class="' . $size . '-text" id="saasaparilla_settings[' . $args['id'] . ']" name="saasaparilla_settings[' . $args['id'] . ']" value="' . esc_attr( stripslashes( $value ) )  . '" />&nbsp;';
	$html .= '<label for="saasaparilla_settings[' . $args['id'] . ']">' . $args['desc'] . '</label>';

	echo $html;
}


/**
 * Textarea callback
 * 
 * @since       1.0.0
 * @param       array $args Arguments passed by the setting
 * @global      array $saasaparilla_options The SaaSaparilla options
 * @return      void
 */
function saasaparilla_textarea_callback( $args ) {
	global $saasaparilla_options;

	if( isset( $saasaparilla_options[$args['id']] ) ) {
		$value = $saasaparilla_options[$args['id']];
	} else {
		$value = isset( $args['std'] ) ? $args['std'] : '';
	}

	$html  = '<textarea class="large-text" cols="50" rows="5" id="saasaparilla_settings[' . $args['id'] . ']" name="saasaparilla_settings[' . $args['id'] . ']">' . esc_textarea( stripslashes( $value ) ) . '</textarea>&nbsp;';
	$html .= '<label for="saasaparilla_settings[' . $args['id'] . ']">' . $args['desc'] . '</label>';

	echo $html;
}


/**
 * Upload callback
 * 
 * @since       1.0.0
 * @param       array $args Arguments passed by the setting
 * @global      array $saasaparilla_options The SaaSaparilla options
 * @return      void
 */
function saasaparilla_upload_callback( $args ) {
	global $saasaparilla_options;

	if( isset( $saasaparilla_options[$args['id']] ) ) {
		$value = $saasaparilla_options[$args['id']];
	} else {
		$value = isset( $args['std'] ) ? $args['std'] : '';
	}

	$size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';

	$html  = '<input type="text" class="' . $size . '-text" id="saasaparilla_settings[' . $args['id'] . ']" name="saasaparilla_settings[' . $args['id'] . ']" value="' . esc_attr( stripslashes( $value ) ) . '" />&nbsp;';
	$html .= '<span><input type="button" class="saasaparilla_settings_upload_button button-secondary" value="' . __( 'Upload File', 'saasaparilla' ) . '" /></span>&nbsp;';
	$html .= '<label for="saasaparilla_settings[' . $args['id'] . ']">' . $args['desc'] . '</label>';

	echo $html;
}


/**
 * Hook callback
 *
 * @since       1.0.0
 * @param       array $args Arguments passed by the setting
 * @return      void
 */
function saasaparilla_hook_callback( $args ) {
	do_action( 'saasaparilla_' . $args['id'] );
}


/**
 * Missing callback
 *
 * @since       1.0.0
 * @param       array $args Arguments passed by the setting
 * @return      void
 */
function saasaparilla_missing_callback( $args ) {
	printf( __( 'The callback function used for the <strong>%s</strong> setting is missing.', 'saasaparilla' ), $args['id'] );
}
