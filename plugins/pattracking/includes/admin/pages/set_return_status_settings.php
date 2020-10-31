<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user;
if (!($current_user->ID && $current_user->has_cap('manage_options'))) {exit;}

$type = isset($_POST['type']) ? sanitize_text_field($_POST['type']) : 0;

if( !taxonomy_exists('wppatt_return_reason') ) {
	$args = array(
		'public' => false,
		'rewrite' => false
	);
	register_taxonomy( 'wppatt_return_reason', 'wpsc_ticket', $args );
}



if( $type == 'set-return-status-color' ) {

	$status_id = isset($_POST) && isset($_POST['status_id']) ? sanitize_text_field($_POST['status_id']) : '';
	if (!$status_id) {exit;}
	
	$status_color = isset($_POST) && isset($_POST['status_color']) ? sanitize_text_field($_POST['status_color']) : '';
	if (!$status_color) {exit;}
	
	$status_bg_color = isset($_POST) && isset($_POST['status_bg_color']) ? sanitize_text_field($_POST['status_bg_color']) : '';
	if (!$status_bg_color) {exit;}
	
	if ($status_color==$status_bg_color) {
	  echo '{ "sucess_status":"0","messege":"'.__('Status color and background color should not be same.','supportcandy').'" }';
	  die();
	}
	
	update_term_meta($status_id, 'wppatt_return_status_color', $status_color);
	update_term_meta($status_id, 'wppatt_return_status_background_color', $status_bg_color);
	
	
	do_action('wpsc_set_edit_status',$status_id);
	
	echo '{ "sucess_status":"1","messege":"Success" }';
}

// Add new Return name and description to DB
if ( $type == 'add-new-return-reason' ) {
	
	$return_name = isset($_POST) && isset($_POST['return_name']) ? sanitize_text_field($_POST['return_name']) : '';
	if (!$return_name) {exit;}
	
	$return_description = isset($_POST) && isset($_POST['return_description']) ? sanitize_text_field($_POST['return_description']) : '';
	if (!$return_description) {exit;}
	
	$reasons = get_terms([
		'taxonomy'   => 'wppatt_return_reason',
		'hide_empty' => false,
		'orderby'    => 'meta_value_num',
		'order'    	 => 'ASC',
		'meta_query' => array('order_clause' => array('key' => 'wppatt_return_reason_load_order')),
	]);
	
	$num_of_reasons = count($reasons);
	
	$term = wp_insert_term( $return_name, 'wppatt_return_reason' );
	if (!is_wp_error($term) && isset($term['term_id'])) {
		add_term_meta ($term['term_id'], 'wppatt_return_reason_load_order', $num_of_reasons-1);
		add_term_meta ($term['term_id'], 'wppatt_return_reason_description', $return_description);
	}
	
	
	echo '{ "sucess_status":"1","messege":"Success" }';
} 

// Delete Return term from DB
if ( $type == 'delete-return-reason' ) {
	
	$return_term_id = isset($_POST) && isset($_POST['return_term_id']) ? sanitize_text_field($_POST['return_term_id']) : '';
	if (!$return_term_id) {exit;}
	
	wp_delete_term( $return_term_id, 'wppatt_return_reason' );
		
	
	echo '{ "sucess_status":"1","messege":"Success" }';
} 

// Update the Return name and description in DB
if ( $type == 'set-edit-return-reason' ) {
	
	$return_name = isset($_POST) && isset($_POST['return_name']) ? sanitize_text_field($_POST['return_name']) : '';
	if (!$return_name) {exit;}
	
	$return_description = isset($_POST) && isset($_POST['return_description']) ? sanitize_text_field($_POST['return_description']) : '';
	if (!$return_description) {exit;}
	
	$return_term_id = isset($_POST) && isset($_POST['return_term_id']) ? sanitize_text_field($_POST['return_term_id']) : '';
	if (!$return_term_id) {exit;}
	
	$args = [
	    'name' => $return_name,
	    'slug' => $return_name
	];
	
	// save to DB
	wp_update_term( $return_term_id, 'wppatt_return_reason', $args );
	update_term_meta($return_term_id, 'wppatt_return_reason_description', $return_description);
	
	echo '{ "sucess_status":"1","messege":"Success" }';
} 

