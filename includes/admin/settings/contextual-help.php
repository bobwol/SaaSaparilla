<?php
/**
 * Contextual help
 *
 * @package     SaaSaparilla\Admin\Settings\ContextualHelp
 * @since       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Settings contextual help.
 *
 * @access      private
 * @since       1.0.0
 * @return      void
 */
function saasaparilla_settings_contextual_help() {
	$screen = get_current_screen();

	if ( $screen->id != 'toplevel_page_saasaparilla-settings' ) {
		return;
	}

	$screen->set_help_sidebar(
		'<p><strong>' . sprintf( __( 'For more information:', 'saasaparilla' ) . '</strong></p>' .
		'<p>' . sprintf( __( 'Visit the <a href="%s">documentation</a> on the SaaSaparilla website.', 'saasaparilla' ), esc_url( 'https://docs.saasaparilla.com/' ) ) ) . '</p>' .
		'<p>' . sprintf(
					__( '<a href="%s">Post an issue</a> on <a href="%s">GitHub</a>. View <a href="%s">extensions</a>.', 'saasaparilla' ),
					esc_url( 'https://github.com/SaaSaparilla/SaaSaparilla/issues' ),
					esc_url( 'https://github.com/SaaSaparilla/SaaSaparilla' ),
					esc_url( 'https://saasaparilla.com/extensions/' )
				) . '</p>'
	);

	$screen->add_help_tab( array(
		'id'	    => 'saasaparilla-settings-general',
		'title'	    => __( 'General', 'saasaparilla' ),
		'content'	=> '<p>' . __( 'This screen provides the most basic settings.', 'saasaparilla' ) . '</p>'
	) );

	do_action( 'saasaparilla_settings_contextual_help', $screen );
}
add_action( 'load-toplevel_page_saasaparilla-settings', 'saasaparilla_settings_contextual_help' );
