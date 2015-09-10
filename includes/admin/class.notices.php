<?php
/**
 * Admin notices class
 *
 * @package     SaaSaparilla\Admin\Notices
 * @since       1.0.0
 */


// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * SaaSaparilla_Notices class
 *
 * @since       1.0.0
 */
class SaaSaparilla_Notices {


	/**
	 * Get things started
	 *
	 * @access      public
	 * @since       1.0.0
	 * @return      void
	 */
	public function __construct() {
		add_action( 'admin_notices', array( $this, 'show_notices' ) );
		add_action( 'saasaparilla_dismiss_notices', array( $this, 'dismiss_notices' ) );
	}


	/**
	 * Show relevant notices
	 *
	 * @access      public
	 * @since       1.0.0
	 * @return      void
	 */
	public function show_notices() {
		$notices = array(
			'updated'   => array(),
			'error'     => array()
		);

		// General messages
		if( isset( $_GET['saasaparilla_message'] ) ) {
			switch( $_GET['saasaparilla_message'] ) {
				case 'test_message':
					$notices['error']['test-message'] = __( 'Test notice.', 'saasaparilla' );
					break;
			}
		}

		if( count( $notices['updated'] ) > 0 ) {
			foreach( $notices['updated'] as $notice => $message ) {
				add_settings_error( 'saasaparilla-notices', $notice, $message, 'updated' );
			}
		}

		if( count( $notices['error'] ) > 0 ) {
			foreach( $notices['error'] as $notice => $message ) {
				add_settings_error( 'saasaparilla-notices', $notice, $message, 'error' );
			}
		}

		settings_errors( 'saasaparilla-notices' );
	}


	/**
	 * Dismiss admin notices
	 *
	 * @access      public
	 * @since       1.0.0
	 * @return      void
	 */
	function dismiss_notices() {
		if( isset( $_GET['saasaparilla_notice'] ) ) {
			update_user_meta( get_current_user_id(), '_saasaparilla_' . $_GET['saasaparilla_notice'] . '_dismissed', 1 );
			wp_redirect( remove_query_arg( array( 'saasaparilla_action', 'saasaparilla_notice' ) ) );
			exit;
		}
	}
}
new SaaSaparilla_Notices;
