<?php

abstract class PATT_DB {

	/**
	 * The name of our database table
	 *
	 * @access  public
	 * @since   1.0
	 */
	public $table_name;

	/**
	 * The version of our database table
	 *
	 * @access  public
	 * @since   1.0
	 */
	public $version;

	/**
	 * The name of the primary column
	 *
	 * @access  public
	 * @since   1.0
	 */
	public $primary_key;

	/**
	 * Get things started
	 *
	 * @access  public
	 * @since   1.0
	 */
	public function __construct() {}

	/**
	 * Whitelist of columns
	 *
	 * @access  public
	 * @since   1.0
	 * @return  array
	 */
	public function get_columns() {
		global $wpdb;
		$result = array();
		$columns = $wpdb->get_results("SHOW COLUMNS FROM $this->table_name");
		foreach ($columns as $key => $value) {
			$result[$value->Field] = '%s';
		}
		return $result;
	}

	/**
	 * Default column values
	 *
	 * @access  public
	 * @since   1.0
	 * @return  array
	 */
	public function get_column_defaults() {
		global $wpdb;
		$result = array();
		$columns = $wpdb->get_results("SHOW COLUMNS FROM $this->table_name");
		foreach ($columns as $key => $value) {
			if($value->Default) {
				$result[$value->Field] = $value->Default;
			}
		}
		return $result;
	}

	/**
	 * Retrieve a row by the primary key
	 *
	 * @access  public
	 * @since   1.0
	 * @return  object
	 */
	public function get_row( $args, $count =  false) {
		global $wpdb;

		$order = '';
		if(isset($args['order'])){
			$order = "ORDER BY {$args['order'][0]} {$args['order'][1]}";
		}

		$where = '';
		if(isset($args['where'])){
			if(is_array($args['where'][0])){
				$i = 1;
				foreach($args['where'] as $cond) {
					if($i == 1){
						$where = " WHERE {$cond[0]} = {$cond[1]}";
					} else {
						$cond[2] = isset($cond[2]) ? : 'AND';
						$where .= " {$cond[2]} {$cond[0]} = {$cond[1]}";
					}
					$i++;
				}
			} else {
				$where = " WHERE {$args['where'][0]} = {$args['where'][1]}";
			}
		}

		$join_query = '';
		if(isset($args['join'])){
			foreach($args['join'] as $join){
				$base_table = isset($join['base_table']) ? $join['base_table'] : $this->table_name;
				$join_query .= " {$join['type']} {$join['table']} ON {$join['table']}.{$join['key']} {$join['compare']} {$base_table}.{$join['foreign_key']}";
			}
		}

		$select = isset($args['select']) ? $args['select'] : '*';
		$query = "SELECT {$select} FROM $this->table_name {$join_query} {$where} LIMIT 1;";
		return $wpdb->get_row($query);
	}

	/**
	 * Retrieve a var by the primary key
	 *
	 * @access  public
	 * @since   1.0
	 * @return  object
	 */
	public function get_value( $key, $value, $count =  false) {
		global $wpdb;
		if($count) {
			$result = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $this->table_name WHERE $key = $value LIMIT 1"));
		} else {
			$result = $wpdb->get_var( $wpdb->prepare( "SELECT * FROM $this->table_name WHERE $key = $value LIMIT 1;" ) );
		}
		return $result;
	}

	/**
	 * Retrieve a var by the primary key
	 *
	 * @access  public
	 * @since   1.0
	 * @return  object
	 */
	public function get_results( $args, $count =  false) {
		global $wpdb;

		$order = '';
		if(isset($args['order'])){
			$order = "ORDER BY {$args['order'][0]} {$args['order'][1]}";
		}
		
// 		$limits = 'LIMIT 20';
		if (isset($args['limit'])) {
			if(isset($args['limit'][1])){
				$limits = "LIMIT {$args['limit'][0]}, {$args['limit'][1]}";
			} else {
				$limits = "LIMIT {$args['limit'][0]}";
			}
		}
		
		$select = isset($args['select']) ? $args['select'] : '*';

		$where = '';
		// print_r($args['where']);
		if(isset($args['where'])){
			if(is_array($args['where'][0])){
				$i = 1;
				foreach($args['where'] as $column => $cond) {
					//print_r($cond);
					if($i == 1){
						if($column && $column == 'custom'){
//							$where = " WHERE {$cond[0]}";
							$where = " WHERE {$cond}";								
											
						} else {
							$cond[2] = isset($cond[2]) ? $cond[2] : ' = ';
							$cond[2] = ($cond[2] == 'AND' || $cond[2] == 'OR') ? $cond[3] : ' = ';
							$where = " WHERE {$cond[0]} {$cond[2]} {$cond[1]}";
						}
					} else {
						if($column && $column == 'custom'){
							$where .= " AND {$cond[0]}";
						} else {
							$cond[2] = isset($cond[2]) ? $cond[2] : 'AND';
							$cond[3] = isset($cond[3]) ? $cond[3] : ' = ';
							$where .= " {$cond[2]} {$cond[0]} {$cond[3]} {$cond[1]}";
						}
					}
					$i++;
				}
			} else {
				if(isset($args['where']['custom'])){
					$where = " WHERE {$args['where']['custom']}";
				} else {
					$where = " WHERE {$args['where'][0]} = {$args['where'][1]}";
				}
			}
		}

		$join_query = '';
		if(isset($args['join'])){
			foreach($args['join'] as $join){
				$base_table = isset($join['base_table']) ? $join['base_table'] : $this->table_name;
				$join_query .= " {$join['type']} {$join['table']} ON {$join['table']}.{$join['key']} {$join['compare']} {$base_table}.{$join['foreign_key']}";
			}
		}

		$groupby = '';
		if(isset($args['groupby'])){
			$groupby = "GROUP BY {$args['groupby']}";
		}

		if($count) {
			$result = $wpdb->get_results("SELECT COUNT(*) FROM $this->table_name {$join_query} {$where} {$groupby} {$limits}");
		} else {
			$query = "SELECT {$select} FROM $this->table_name {$join_query} {$where} {$groupby} {$order} {$limits}";
			$result = $wpdb->get_results($query);
			$result['query'] = $query;
		}
		return $result;
	}

	/**
	 * Retrieve a row by a specific column / value
	 *
	 * @access  public
	 * @since   1.0
	 * @return  object
	 */
	public function get_by( $column, $row_id ) {
		global $wpdb;
		$column = esc_sql( $column );
		return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $this->table_name WHERE $column = %s LIMIT 1;", $row_id ) );
	}

	/**
	 * Retrieve a specific column's value by the primary key
	 *
	 * @access  public
	 * @since   1.0
	 * @return  string
	 */
	public function get_column( $column, $row_id ) {
		global $wpdb;
		$column = esc_sql( $column );
		return $wpdb->get_var( $wpdb->prepare( "SELECT $column FROM $this->table_name WHERE $this->primary_key = %s LIMIT 1;", $row_id ) );
	}

	/**
	 * Retrieve a specific column's value by the the specified column / value
	 *
	 * @access  public
	 * @since   1.0
	 * @return  string
	 */
	public function get_column_by( $column, $column_where, $column_value ) {
		global $wpdb;
		$column_where = esc_sql( $column_where );
		$column       = esc_sql( $column );
		return $wpdb->get_var( $wpdb->prepare( "SELECT $column FROM $this->table_name WHERE $column_where = %s LIMIT 1;", $column_value ) );
	}

	/**
	 * Insert a new row
	 *
	 * @access  public
	 * @since   1.0
	 * @return  int
	 */
	public function insert( $data, $type = '' ) {
		global $wpdb;

		// Set default values
		$data = wp_parse_args( $data, $this->get_column_defaults() );

		do_action( 'edd_pre_insert_' . $type, $data );

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

		do_action( 'edd_post_insert_' . $type, $wpdb->insert_id, $data );

		return $wpdb->insert_id;
	}

	/**
	 * Update a row
	 *
	 * @access  public
	 * @since   1.0
	 * @return  bool
	 */
	public function update( $data = array(), $where = [] ) {

		global $wpdb;

		if( count( $where ) < 1) {
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

		if ( false === $wpdb->update( $this->table_name, $data, $where, $column_formats ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Delete a row identified by the primary key
	 *
	 * @access  public
	 * @since   1.0
	 * @return  bool
	 */
	public function delete( $row_id = 0 ) {

		global $wpdb;

		// Row ID must be positive integer
		$row_id = absint( $row_id );

		if( empty( $row_id ) ) {
			return false;
		}

		// echo $query = "DELETE FROM $this->table_name WHERE $this->primary_key = %d", $row_id;
		$this->primary_key = isset($this->primary_key) ? $this->primary_key : 'id'; 
		if ( false === $wpdb->query( $wpdb->prepare( "DELETE FROM $this->table_name WHERE $this->primary_key = %d", $row_id ) ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Check if the given table exists
	 *
	 * @since  1.0
	 * @param  string $table The table name
	 * @return bool  If the table name exists
	 */
	public function table_exists( $table ) {
		global $wpdb;
		$table = sanitize_text_field( $table );

		return $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE '%s'", $table ) ) === $table;
	}

}

class WP_CUST_QUERY Extends PATT_DB {
	
	/**
	 * Get things started
	 *
	 * @access  public
	 * @since   1.0
	 */
	public function __construct($table_name) {
		$this->table_name = $table_name;
	}
}
function dd($arg) {
/*
	echo '<pre>';
	print_r($arg); 
	echo '</pre>'; 
*/
	die();
}