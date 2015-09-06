<?php
/**
 * Actions
 *
 * @package     SaaSaparilla\Actions
 * @since       1.0.0
 */


// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Process all actions sent via POST and GET by looking for the 'saasaparilla-action'
 * request and running do_action() to call the function
 *
 * @since       1.0.0
 * @return      void
 */
function saasaparilla_process_actions() {
	if( isset( $_POST['saasaparilla-action'] ) ) {
		do_action( 'saasaparilla_' . $_POST['saasaparilla-action'], $_POST );
	}

	if( isset( $_GET['saasaparilla-action'] ) ) {
		do_action( 'saasaparilla_' . $_GET['saasaparilla-action'], $_GET );
	}
}
add_action( 'init', 'saasaparilla_process_actions' );
add_action( 'admin_init', 'saasaparilla_process_actions' );
