<?php

global $wpdb, $current_user, $wpscfunction;

$path = preg_replace('/wp-content.*$/','',__DIR__);
include($path.'wp-load.php');

$recall_id = isset($_POST['recall_id']) ? sanitize_text_field($_POST['recall_id']) : '';
$type = isset($_POST['type']) ? sanitize_text_field($_POST['type']) : '';
$title = isset($_POST['title']) ? sanitize_text_field($_POST['title']) : '';
$recall_ids = $_REQUEST['recall_ids']; 
$ticket_id = isset($_POST['ticket_id']) ? sanitize_text_field($_POST['ticket_id']) : '';
//$recall_ids = json_decode($recall_ids);
//$num_of_recalls = count($recall_ids);


if($type == 'request_date') {
	$request_date = isset($_POST['new_date']) ? sanitize_text_field($_POST['new_date']) : ''; 
	$old_date = isset($_POST['old_date']) ? sanitize_text_field($_POST['old_date']) : '';	
	
	$request_date_string = $old_date.' -> '.$request_date;

	echo 'Recall ID: '.$recall_id.PHP_EOL;
	echo 'Type: '.$type.PHP_EOL;
	echo $title.' Date: '.$request_date.PHP_EOL;
	echo 'Audit: '.$request_date_string.PHP_EOL;
	echo 'ticket_id: '.$ticket_id.PHP_EOL;
	
	
	$update = [
		'request_date' => $request_date
	];
	$where = [
		'id' => $recall_id
	];
	$recall_array = Patt_Custom_Func::update_recall_dates($update, $where);
	
	//Update the Updated Date
	$current_datetime = date("yy-m-d H:i:s");
	$update = [	'updated_date' => $current_datetime ];
	$where = [ 'id' => $recall_id ];
	Patt_Custom_Func::update_recall_dates($update, $where);
	
	
	do_action('wpppatt_after_recall_request_date', $ticket_id, 'R-'.$recall_id, $request_date_string);
	
} elseif ( $type == 'received_date' ) {
	$received_date = isset($_POST['new_date']) ? sanitize_text_field($_POST['new_date']) : '';
	$old_date = isset($_POST['old_date']) ? sanitize_text_field($_POST['old_date']) : '';	

	$received_date_string = $old_date.' -> '.$received_date;
	
	echo 'Recall ID: '.$recall_id.PHP_EOL;
	echo 'Type: '.$type.PHP_EOL;
	echo $title.' Date: '.$received_date.PHP_EOL;	
	echo 'Audit: '.$received_date_string.PHP_EOL;
	echo 'ticket_id: '.$ticket_id.PHP_EOL;
	
	$update = [
		'request_receipt_date' => $received_date
	];
	$where = [
		'id' => $recall_id
	];
	$recall_array = Patt_Custom_Func::update_recall_dates($update, $where);
	
	//Update the Updated Date
	$current_datetime = date("yy-m-d H:i:s");
	$update = [	'updated_date' => $current_datetime ];
	$where = [ 'id' => $recall_id ];
	$recall_data = Patt_Custom_Func::update_recall_dates($update, $where);
	
	// State Machine: if in Shipped [730] and received_date changed, update to On Loan [731]
	// Now being handled by /admin/recall_shipping_status_cron.php
/*
	if ( $recall_data[0]->recall_status_id = 730 ) {
		$data_status = [ 'recall_status_id' => 731 ]; //change status from Recalled to Shipped
		$obj = Patt_Custom_Func::update_recall_data( $data_status, $where );
	}
*/
	
	// Clear out old shipping data. Required for State Machine to function properly.
	// Shipping row is used twice, shipping back to requestor and shipping back to Digitization Center
	$data = [
		'company_name' => '',
		'tracking_number' => '',
		'shipped' => '',
		'status' => ''
	];
	$where = [
		'recall_id' => $recall_id
	];

	$recall_array = Patt_Custom_Func::update_recall_shipping( $data, $where );
	
	
	do_action('wpppatt_after_recall_received_date', $ticket_id, 'R-'.$recall_id, $received_date_string);
	
} elseif( $type == 'returned_date' ) {
	$returned_date = isset($_POST['new_date']) ? sanitize_text_field($_POST['new_date']) : '';
	$old_date = isset($_POST['old_date']) ? sanitize_text_field($_POST['old_date']) : '';	 

	$returned_date_string = $old_date.' -> '.$returned_date;	

	echo 'Recall ID: '.$recall_id.PHP_EOL;
	echo 'Type: '.$type.PHP_EOL;
	echo $title.' Date: '.$returned_date.PHP_EOL;	
	echo 'Audit: '.$returned_date_string.PHP_EOL;
	echo 'ticket_id: '.$ticket_id.PHP_EOL;	
	
	$update = [
		'return_date' => $returned_date
	];
	$where = [
		'id' => $recall_id
	];
	$recall_array = Patt_Custom_Func::update_recall_dates($update, $where);
	
	//Update the Updated Date
	$current_datetime = date("yy-m-d H:i:s");
	$update = [	'updated_date' => $current_datetime ];
	$where = [ 'id' => $recall_id ];
	Patt_Custom_Func::update_recall_dates($update, $where);
	
	// State Machine: if in Shipped Back [732] and return_date changed, update to Recall Complete [733]
	// Now being handled by /admin/recall_shipping_status_cron.php
/*
	if ( $recall_data[0]->recall_status_id = 732 ) {
		$data_status = [ 'recall_status_id' => 733 ]; //change status from Recalled to Shipped 
		$obj = Patt_Custom_Func::update_recall_data( $data_status, $where );
	}
*/

	
	do_action('wpppatt_after_recall_returned_date', $ticket_id, 'R-'.$recall_id, $returned_date_string);
	
}  elseif( $type == 'requestor' ) {
	$recall_requestors = $_REQUEST['new_requestors']; 
	$old_recall_requestors = $_REQUEST['old_requestors'];
	
	
	$old_assigned_agents_string = '';
	$old_assigned_agents_array = array();
	foreach ( $old_recall_requestors as $agent ) {
		$old_assigned_agents_string .= get_term_meta( $agent, 'label', true);
		array_push($old_assigned_agents_array, get_term_meta( $agent, 'user_id', true));
		$old_assigned_agents_string .= ', ';
	}
	$old_assigned_agents_string = substr($old_assigned_agents_string, 0, -2);
	
	$new_assigned_agents_string = '';
	$new_assigned_agents_array = array();
	foreach ( $recall_requestors as $agent ) {
		$new_assigned_agents_string .= get_term_meta( $agent, 'label', true);
		array_push($new_assigned_agents_array, get_term_meta( $agent, 'user_id', true));
		$new_assigned_agents_string .= ', ';
	}
	$new_assigned_agents_string = substr($new_assigned_agents_string, 0, -2);

	$recall_requestors_string = $old_assigned_agents_string.' -> '.$new_assigned_agents_string;	
	
	echo 'Recall ID: '.$recall_id.PHP_EOL;
	echo 'Type: '.$type.PHP_EOL;
	echo 'Recall Requestors user_id Array: '.PHP_EOL;
	print_r($new_assigned_agents_array);
//	echo 'Old: '.$old_assigned_agents_string;
//	echo 'New: '.$new_assigned_agents_string;	
	echo 'Combo: '.$recall_requestors_string.PHP_EOL;
	echo 'ticket id: '.$ticket_id.PHP_EOL;
	//print_r($old_recall_requestors);	
	//echo 'Requestor String: '.$recall_requestors_string.PHP_EOL;
// 	echo 'Requestor Value: '.$new_requestor_value.PHP_EOL;
	
	// Update the Users associated with the Recall. 
	$data = [
			'recall_id' => $recall_id,			
			'user_id' => $new_assigned_agents_array
		];
	Patt_Custom_Func::update_recall_user_by_id($data);
	
	//Update the Updated Date
	$current_datetime = date("yy-m-d H:i:s");
	$update = [	'updated_date' => $current_datetime ];
	$where = [ 'id' => $recall_id ];
	Patt_Custom_Func::update_recall_dates($update, $where);
	
	do_action('wpppatt_after_recall_requestor', $ticket_id, 'R-'.$recall_id, $recall_requestors_string);
	
} elseif( $type == 'cancel' ) {
	
	//echo '!Recall ID: '.$recall_id.PHP_EOL;
	//echo 'POST recall id: '.$_POST['recall_id'].PHP_EOL;
	//echo 'Type: '.$type.PHP_EOL;
	//echo 'Recall status before: '.$recall_obj->recall_status_id.PHP_EOL;
	//print_r($recall_array);

	
	
	$where = [
		'recall_id' => $recall_id
	];
	$recall_array = Patt_Custom_Func::get_recall_data($where);
	
	//Added for servers running < PHP 7.3
	if (!function_exists('array_key_first')) {
	    function array_key_first(array $arr) {
	        foreach($arr as $key => $unused) {
	            return $key;
	        }
	        return NULL;
	    }
	}
	
	$recall_array_key = array_key_first($recall_array);	
	$recall_obj = $recall_array[$recall_array_key];
	
	//echo 'current status: '.$recall_obj->recall_status_id;
	
	// Only cancel if recall is in status: Recalled
	if ( $recall_obj->recall_status_id == 729 ) {
		$data_status = [ 'recall_status_id' => 734 ]; //change status from Recalled to Cancelled
		$obj = Patt_Custom_Func::update_recall_data( $data_status, $where );
		
		do_action('wpppatt_after_recall_cancelled', $ticket_id, 'R-'.$recall_id);
	}
	
	
	//Update the Updated Date
	$current_datetime = date("yy-m-d H:i:s");
	$update = [	'updated_date' => $current_datetime ];
	$where = [ 'id' => $recall_id ];
	Patt_Custom_Func::update_recall_dates($update, $where);

//	do_action('wpppatt_after_recall_cancelled', $ticket_id, 'R-'.$recall_id);
	
} 




?>