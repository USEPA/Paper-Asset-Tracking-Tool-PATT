<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user,$wpscfunction,$wpdb;
if (!($current_user->ID && $current_user->has_cap('wpsc_agent'))) {
  exit;
}

$ticket_id   = isset($_POST['ticket_id']) ? sanitize_text_field($_POST['ticket_id']) : 0 ;
if(!$ticket_id) die();

$meta_value =array(
	'active' => '1'
);

$wpdb->update($wpdb->prefix.'wpsc_ticket', $meta_value, array('id'=>$ticket_id));

do_action('wpsc_restore_ticket',$ticket_id);

//PATT BEGIN

//sends email/notification to admins/managers when request is restored
$agent_admin_group_name = 'Administrator';
$pattagentid_admin_array = Patt_Custom_Func::agent_from_group($agent_admin_group_name);
$agent_manager_group_name = 'Manager';
$pattagentid_manager_array = Patt_Custom_Func::agent_from_group($agent_manager_group_name);
$pattagentid_array = array_merge($pattagentid_admin_array,$pattagentid_manager_array);
$data = [];
$email = 1;

$padded_request_id = Patt_Custom_Func::convert_request_db_id($ticket_id);

//insert notification for each request
Patt_Custom_Func::insert_new_notification('email-request-restored',$pattagentid_array,$padded_request_id,$data,$email);

//PATT END