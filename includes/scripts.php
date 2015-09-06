<?php
/**
 * Scripts
 *
 * @package     SaaSaparilla\Scripts
 * @since       1.0.0
 */


// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Load admin scripts
 *
 * @since       1.0.0
 * @param       string $hook The hook for the page we are viewing
 * @global      object $post The WordPress post object
 * @global      string $wp_version The WordPress version
 * @return      void
 */
function saasaparilla_load_admin_scripts( $hook ) {
	if( ! apply_filters( 'saasaparilla_load_admin_scripts', saasaparilla_is_admin_page( $hook ), $hook ) ) {
		return;
	}

	global $post, $wp_version;

	$js_dir     = SAASAPARILLA_URL . 'assets/js/';
	$css_dir    = SAASAPARILLA_URL . 'assets/css/';

	// Use minified libraries if SCRIPT_DEBUG is turned off
	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

	wp_enqueue_script( 'saasaparilla', $js_dir . 'admin' . $suffix . '.js', array( 'jquery' ), SAASAPARILLA_VER, false );
	wp_localize_script( 'saasaparilla', 'saasaparilla_vars', array(
	) );

	wp_enqueue_style( 'wp-color-picker' );
	wp_enqueue_script( 'wp-color-picker' );

	wp_enqueue_style( 'colorbox', $css_dir . 'colorbox' . $suffix . '.css', array(), '1.3.20' );
	wp_enqueue_script( 'colorbox', $js_dir . 'jquery.colorbox-min.js', array( 'jquery' ), '1.3.20' );

	if( function_exists( 'wp_enqueue_media' ) && version_compare( $wp_version, '3.5', '>=' ) ) {
		wp_enqueue_media();
	}

	wp_enqueue_script( 'jquery-ui-datepicker' );
	wp_enqueue_script( 'jquery-ui-dialog' );

	$ui_style = ( get_user_option( 'admin_color' ) == 'classic' ) ? 'classic' : 'fresh';
	wp_enqueue_style( 'jquery-ui-css', $css_dir . 'jquery-ui-' . $ui_style . $suffix . '.css' );

	wp_enqueue_script( 'media-upload' );
	wp_enqueue_script( 'thickbox' );
	wp_enqueue_style( 'thickbox' );

	wp_enqueue_style( 'saasaparilla', $css_dir . 'saasaparilla' . $suffix . '.css', SAASAPARILLA_VER );
	wp_enqueue_style( 'saasaparilla-font', $css_dir . 'font' . $suffix . '.css', SAASAPARILLA_VER );
}
add_action( 'admin_enqueue_scripts', 'saasaparilla_load_admin_scripts', 100 );
