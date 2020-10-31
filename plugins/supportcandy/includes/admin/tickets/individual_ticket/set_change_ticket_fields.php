<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user, $wpscfunction,$wpdb;

$ticket_id = isset($_POST['ticket_id']) ? intval($_POST['ticket_id']) : 0 ;
if(!$ticket_id) die();

$fields_data = array();
foreach ($_POST as $key => $value) {
	if ($wpscfunction->is_cf_slug($key)) {
		$fields_data[$key] = $value;
	}
}
$fields_data = json_encode($fields_data);

$request = new WPSC_Rest_Child();
$data = array(
	'id'          => $ticket_id,
	'fields_data' => $fields_data,
);
$request->setApiParams($data);

$is_valid = WPSC_Rest_v1_Helper::validate_agent($request);
$allow_access = apply_filters('wpsc_allow_set_change_ticket_fields',false);
if ( !is_wp_error($is_valid) || $allow_access ) {
	$respose = WPSC_Rest_v1_Helper::update_ticket_fields($request);
}
