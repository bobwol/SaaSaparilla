<?php
/**
 * Misc functions
 *
 * @package     SaaSaparilla\Functions
 * @since       1.0.0
 */


// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Check if a given integer is odd
 *
 * @since       1.0.0
 * @param       int $int The integer to check
 * @return      bool
 */
function saasaparilla_is_odd( $int ) {
	return (bool) ( $int & 1 );
}


/**
 * Get user IP
 *
 * @since       1.0.0
 * @return      string $ip The users' IP address
 */
function saasaparilla_get_ip() {
	$ip = '127.0.0.1';

	if( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
		// Check IP from internet
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	} elseif( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
		// Check IP pass from proxy
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} elseif( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
		$ip = $_SERVER['REMOTE_ADDR'];
	}

	return apply_filters( 'saasaparilla_get_ip', $ip );
}


/**
 * Get user host
 *
 * @since       1.0.0
 * @return      mixed string $host if detected, false otherwise
 */
function saasaparilla_get_host() {
	$host = false;

	if( defined( 'WPE_APIKEY' ) ) {
		$host = 'WP Engine';
	} elseif( defined( 'PAGELYBIN' ) ) {
		$host = 'Pagely';
	} elseif( DB_HOST == 'localhost:/tmp/mysql5.sock' ) {
		$host = 'ICDSoft';
	} elseif( DB_HOST == 'mysqlv5' ) {
		$host = 'NetworkSolutions';
	} elseif( strpos( DB_HOST, 'ipagemysql.com' ) !== false ) {
		$host = 'iPage';
	} elseif( strpos( DB_HOST, 'ipowermysql.com' ) !== false ) {
		$host = 'IPower';
	} elseif( strpos( DB_HOST, '.gridserver.com' ) !== false ) {
		$host = 'MediaTemple Grid';
	} elseif( strpos( DB_HOST, '.pair.com' ) !== false ) {
		$host = 'pair Networks';
	} elseif( strpos( DB_HOST, '.stabletransit.com' ) !== false ) {
		$host = 'Rackspace Cloud';
	} elseif( strpos( DB_HOST, '.sysfix.eu' ) !== false ) {
		$host = 'SysFix.eu Power Hosting';
	} elseif( strpos( $_SERVER['SERVER_NAME'], 'Flywheel' ) !== false ) {
		$host = 'Flywheel';
	} else {
		// General fallback for data gathering
		$host = 'DBH: ' . DB_HOST . ', SRV: ' . $_SERVER['SERVER_NAME'];
	}

	return $host;
}


/**
 * Month num to name
 *
 * @since       1.0.0
 * @param       integer $num The month number to look up
 * @return      string Short month name
 */
function saasaparilla_month_num_to_name( $num ) {
	$timestamp = mktime( 0, 0, 0, $num, 1, 2005 );

	return date_i18n( 'M', $timestamp );
}


/**
 * Get PHP arg separator output
 *
 * @since       1.0.0
 * @return      string Arg separator output
 */
function saasaparilla_get_php_arg_separator_output() {
	return ini_get( 'arg_separator.output' );
}


/**
 * Get the current page URL
 *
 * @since       1.0.0
 * @global      object $post The current post object
 * @return      string $page_url Current page URL
 */
function saasaparilla_get_current_page_url() {
	global $post;

	if( is_front_page() ) {
		$page_url = home_url();
	} else {
		$page_url = 'http';

		if( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] == 'on' ) {
			$page_url .= 's';
		}

		$page_url .= '://';

		if( isset( $_SERVER['SERVER_PORT'] ) && $_SERVER['SERVER_PORT'] != '80' ) {
			$page_url .= $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . $_SERVER['REQUEST_URI'];
		} else {
			$page_url .= $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
		}
	}

	return apply_filters( 'saasaparilla_get_current_page_url', esc_url( $page_url ) );
}


/**
 * Convert an object to an associative array
 *
 * @since       1.0.0
 * @param       object $data The object to convert
 * @return      array The converted object
 */
function saasaparilla_object_to_array( $data ) {
	if( is_array( $data ) || is_object( $data ) ) {
		$result = array();

		foreach( $data as $key => $value ) {
			$result[$key] = saasaparilla_object_to_array( $value );
		}

		return $result;
	}

	return $data;
}
