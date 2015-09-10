<?php
/**
 * Admin pages
 *
 * @package     SaaSaparilla\Admin\Pages
 * @since       1.0.0
 */


// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Create the admin menu pages
 *
 * @since       1.0.0
 * @global      string $saasaparilla_settings_page The SaaSaparilla settings page
 * @global      string $saasaparilla_tools_page The SaaSaparilla tools page
 * @global      string $saasaparilla_upgrades_page The SaaSaparilla upgrades page
 * @return      void
 */
function saasaparilla_add_menu_items() {
	global $saasaparilla_settings_page, $saasaparilla_tools_page, $saasaparilla_upgrades_page;

	add_menu_page( __( 'SaaSaparilla Settings', 'saasaparilla' ), __( 'SaaSaparilla', 'saasaparilla' ), 'manage_options', 'saasaparilla_settings', 'saasaparilla_render_settings_page' );
	$saasaparilla_settings_page     = add_submenu_page( 'saasaparilla_settings', __( 'SaaSaparilla Settings', 'saasaparilla' ), __( 'Settings', 'saasaparilla' ), 'manage_options', 'saasaparilla-settings', 'saasaparilla_render_settings_page' );
	$saasaparilla_tools_page        = add_submenu_page( 'saasaparilla_settings', __( 'SaaSaparilla Info and Tools', 'saasaparilla' ), __( 'Tools', 'saasaparilla' ), 'install_plugins', 'saasaparilla-tools', 'saasaparilla_tools_page' );
	$saasaparilla_upgrades_page     = add_submenu_page( null, __( 'SaaSaparilla Upgrades', 'saasaparilla' ), __( 'SaaSaparilla Upgrades', 'saasaparilla' ), 'manage_options', 'saasaparilla-upgrades', 'saasaparilla_upgrades_screen' );
}
add_action( 'admin_menu', 'saasaparilla_add_menu_items' );


/**
 * Determine if the current page is a SaaSaparilla-specific admin page
 *
 * @since       1.0.0
 * @param       string $hook The current page hook
 * @global      string $pagenow The current page
 * @global      string $typenow The current post type
 * @global      string $saasaparilla_settings_page The SaaSaparilla settings page
 * @global      string $saasaparilla_tools_page The SaaSaparilla tools page
 * @global      string $saasaparilla_upgrades_page The SaaSaparilla upgrades page
 * @return      bool
 */
function saasaparilla_is_admin_page( $hook = '' ) {
	global $pagenow, $typenow, $saasaparilla_settings_page, $saasaparilla_tools_page, $saasaparilla_upgrades_page;

	$admin_pages = apply_filters( 'saasaparilla_admin_pages', array( $saasaparilla_settings_page, $saasaparilla_tools_page, $saasaparilla_upgrades_page ) );
	$found = false;

	if( $pagenow == 'index.php' ) {
		$found = true;
	} elseif( in_array( $pagenow, $admin_pages ) || in_array( $hook, $admin_pages ) ) {
		$found = true;
	}

	return (bool) apply_filters( 'saasaparilla_is_admin_page', $found, $hook );
}
