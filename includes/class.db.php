<?php
/**
 * DB base class
 *
 * @package     SaaSaparilla\DB
 * @since       1.0.0
 */


// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * DB base class
 *
 * @since       1.0.0
 */
abstract class SaaSaparilla_DB {


	/**
	 * @access      public
	 * @since       1.0.0
	 * @var         string $table_name The name of our database table
	 */
	public $table_name;


	/**
	 * @access      public
	 * @since       1.0.0
	 * @var         string $version The database table version
	 */
	public $version;


	/**
	 * @access      public
	 * @since       1.0.0
	 * @var         string $primary_key The table primary key
	 */
	public $primary_key;


	/**
	 * Get things started
	 *
	 * @access      public
	 * @since       1.0.0
	 * @return      void
	 */
	public function __construct() {}


	/**
	 * Whitelist of columns
	 *
	 * @access      public
	 * @since       1.0.0
	 * @return      void
	 */
	public function get_columns() {
		return array();
	}


	/**
	 * Default column values
	 *
	 * @access      public
	 * @since       1.0.0
	 * @return      void
	 */
	public function get_column_defaults() {
		return array();
	}


	/**
	 * Retrieve a row by the primary key
	 *
	 * @access      public
	 * @since       1.0.0
	 * @param       int $row_id The primary key to retrieve
	 * @global      object $wpdb The WordPress database object
	 * @return      object The retrieved row
	 */
	public function get( $row_id ) {
		global $wpdb;

		return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $this->table_name WHERE $this->primary_key = %s LIMIT 1;", $row_id ) );
	}


	/**
	 * Retrieve a row by a specific column/value
	 *
	 * @access      public
	 * @since       1.0.0
	 * @param       string $column The column to search
	 * @param       string $row_id The value to search
	 * @global      object $wpdb The WordPress database object
	 * @return      object The retrieved row
	 */
	public function get_by( $column, $row_id ) {
		global $wpdb;

		$column = esc_sql( $column );

        return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $this->table_name WHERE $column = %s LIMIT 1;", $row_id ) );
	}


	/**
	 * Retrieve a specific columns' value by the primary key
	 *
	 * @access      public
	 * @since       1.0.0
	 * @param       string $column The column to search
	 * @param       string $row_id The value to search
	 * @global      object $wpdb The WordPress database object
	 * @return      mixed The retrieved value
	 */
	public function get_column( $column, $row_id ) {
		global $wpdb;

		$column = esc_sql( $column );

        return $wpdb->get_var( $wpdb->prepare( "SELECT $column FROM $this->table_name WHERE $this->primary_key = %s LIMIT 1;", $row_id ) );
	}


	/**
	 * Retrieve a specific columns' value by the specified column/value
	 *
	 * @access      public
	 * @since       1.0.0
	 * @param       string $column The column to search
	 * @param       string $column_where
	 * @param       string $column_value
	 * @global      object $wpdb The WordPress database object
	 * @return      mixed The retrieved value
	 */
	public function get_column_by( $column, $column_where, $column_value ) {
		global $wpdb;

		$column_where   = esc_sql( $column_where );
		$column         = esc_sql( $column );

		return $wpdb->get_var( $wpdb->prepare( "SELECT $column FROM $this->table_name WHERE $column_where = %s LIMIT 1;", $column_value ) );
	}


	/**
	 * Insert a new row
	 *
	 * @access      public
	 * @since       1.0.0
	 * @param       array $data The data to insert
	 * @param       string $type The data type
	 * @global      object $wpdb The WordPress database object
	 * @return      int The new row primary key
	 */
	public function insert( $data, $type = '' ) {
		global $wpdb;

		// Set default values
		$data = wp_parse_args( $data, $this->get_column_defaults() );

        do_action( 'saasaparilla_pre_insert_' . $type, $data );

		// Initialize column format array
		$column_formats = $this->get_columns();

		// Force fields to lower case
		$data = array_change_key_case( $data );

		// White list columns
		$data = array_intersect_key( $data, $column_formats );

		// Reorder $column_formats to match the order of columns given in $data
		$data_keys = array_keys( $data );
		$column_formats = array_merge( array_flip( $data_keys ), $column_formats );

		$wpdb->insert( $this->table_name, $data, $column_formats );

		do_action( 'saasaparilla_post_insert_' . $type, $data, $column_formats );

		return $wpdb->insert_id;
	}


	/**
	 * Update a row
	 *
	 * @access      public
	 * @since       1.0.0
	 * @param       int $row_id The row ID to update
	 * @param       array $data The data to replace
	 * @param       string $where
	 * @global      object $wpdb The WordPress database object
	 * @return      bool
	 */
	public function update( $row_id, $data = array(), $where = '' ) {
		global $wpdb;

		// Row ID must be positive integer
		$row_id = absint( $row_id );

        if( empty( $row_id ) ) {
			return false;
		}

		if( empty( $where ) ) {
			$where = $this->primary_key;
		}

		// Initialize column format array
		$column_formats = $this->get_columns();

		// Force fields to lower case
		$data = array_change_key_case( $data );

		// White list columns
		$data = array_intersect_key( $data, $column_formats );

        // Reorder $column_formats to match the order of columns given in $data
		$data_keys = array_keys( $data );
		$column_formats = array_merge( array_flip( $data_keys ), $column_formats );

		if( $wpdb->update( $this->table_name, $data, array( $where => $row_id ), $column_formats ) === false ) {
			return false;
		}

        return true;
	}


	/**
	 * Delete a row identified by the primary key
	 *
	 * @access      public
	 * @since       1.0.0
	 * @param       int $row_id The row to delete
	 * @global      object $wpdb The WordPress database object
	 * @return      bool
	 */
	public function delete( $row_id = 0 ) {
		global $wpdb;

		// Row ID must be a positive integer
		$row_id = absint( $row_id );

        if( empty( $row_id ) ) {
			return false;
		}

		if( $wpdb->query( $wpdb->query( $wpdb->prepare( "DELETE FROM $this->table_name WHERE $this->primary_key = %d", $row_id ) ) ) === false ) {
			return false;
		}

		return true;
	}


	/**
	 * Check if the given table exists
	 *
	 * @access      public
	 * @since       1.0.0
	 * @param       string $table The table to check
	 * @global      object $wpdb The WordPress database object
	 * @return      bool
	 */
	public function table_exists( $table ) {
		global $wpdb;

		$table = sanitize_text_field( $table );

        return $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE '%s'", $table ) ) === $table;
	}
}
