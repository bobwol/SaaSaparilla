<?php
/**
 * Upgrade functions
 *
 * @package     SaaSaparilla\Upgrade
 * @since       1.0.0
 */


// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Check if the upgrade routine has been run for a specific action
 *
 * @since       1.0.0
 * @param       string $upgrade_action The upgrade action to check completion for
 * @return      bool
 */
function saasaparilla_has_upgrade_completed( $upgrade_action = '' ) {
	if( empty( $upgrade_action ) ) {
		return false;
	}

	$completed_upgrades = saasaparilla_get_completed_upgrades();

	return in_array( $upgrade_action, $completed_upgrades );
}


/**
 * Gets the array of completed upgrade actions
 *
 * @since       1.0.0
 * @return      array $completed_upgrades The array of completed upgrades
 */
function saasaparilla_get_completed_upgrades() {
	$completed_upgrades = get_option( 'saasaparilla_completed_upgrades' );

	if( $completed_upgrades === false ) {
		$completed_upgrades = array();
	}

	return $completed_upgrades;
}
