<?php
/**
 * Admin footer
 *
 * @package     SaaSaparilla\Admin\Footer
 * @since       1.0.0
 */


// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Add rating links to the admin dashboard
 *
 * @since       1.0.0
 * @param       string $footer_text The current footer text
 * @global      string $typenow The post type we are viewing
 * @return      string The updated footer text
 */
function saasaparilla_admin_footer( $footer_text ) {
	global $typenow;

	$rate_text = sprintf( __( 'Thank you for using <a href="%1$s" target="_blank">SaaSaparilla</a>! Please <a href="%2$s" target="_blank">rate us</a> on <a href="%2$s" target="_blank">WordPress.org</a>', 'saasaparilla' ),
		'https://saasaparilla.com',
		'https://wordpress.org/support/view/plugin-reviews/saasaparilla?filter=5#postform'
	);

	return str_replace( '</span>', '', $footer_text ) . ' | ' . $rate_text . '</span>';
}
add_filter( 'admin_footer_text', 'saasaparilla_admin_footer' );
