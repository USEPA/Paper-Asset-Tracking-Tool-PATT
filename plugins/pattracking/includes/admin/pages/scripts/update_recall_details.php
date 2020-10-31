<?php

global $wpdb, $current_user, $wpscfunction;

$path = preg_replace('/wp-content.*$/','',__DIR__);
include($path.'wp-load.php');

$recall_id = isset($_POST['recall_id']) ? sanitize_text_field($_POST['recall_id']) : '';
$type = isset($_POST['type']) ? sanitize_text_field($_POST['type']) : '';
$title = isset($_POST['title']) ? sanitize_text_field($_POST['title']) : '';
$recall_ids = $_REQUEST['recall_ids']; 
$ticket_id = isset($_POST['ticket_id']) ? sanitize_text_field($_POST['ticket_id']) : '';
$new_agent_id = isset($_POST['new_agent_id']) ? sanitize_text_field($_POST['new_agent_id']) : ''; // not being used. 
$array_of_agent_ids = $_REQUEST['agent_id_array']; 

//$recall_ids = json_decode($recall_ids);
//$num_of_recalls = count($recall_ids);


if( $type == 'request_date' ) {
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
	//echo 'Combo: '.$recall_requestors_string.PHP_EOL;
	//echo 'ticket id: '.$ticket_id.PHP_EOL;
	echo 'OLD Recall Requestors Array: '.PHP_EOL;
	print_r($old_recall_requestors);
	echo 'NEW Recall Requestors Array: '.PHP_EOL;
	print_r($recall_requestors);	
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
	
	//
	// Set PM Notifications 
	//
	
	$notifications = '';
	$notification_post = 'email-user-added-to-recall';

	$unique_agent_id_array = [];
	
/*
	foreach($new_agents_array as $status_and_user_array ) {
		
		echo 'unique_agent_id_array: ' . PHP_EOL;
		print_r($unique_agent_id_array);
		echo 'status_and_user_array: ' . PHP_EOL;
		print_r( $status_and_user_array );
		if( $status_and_user_array['agents'] != null ) {
			$unique_agent_id_array = array_merge( $unique_agent_id_array, $status_and_user_array['agents'] );
		}
	}
*/

	
	
	// remove any agent_ids that were previously assigned to this box.
	//$new_unique_agent_array = array_diff( $unique_agent_id_array, $old_recall_requestors );
	
	
	
	$data = [
        'item_id' => 'R-' . $recall_id,
        'action_initiated_by' => $current_user->display_name
    ];
	$email = 0;
	
	$new_notification = Patt_Custom_Func::insert_new_notification( $notification_post, $recall_requestors, 'R-' . $recall_id, $data, $email );
	
	//die();
	
	// D E B U G 
/*
	echo 'T h e    N o t i f i c a i t o n: ';
	echo 'notification_post: ' . $notification_post ;
	echo 'agent array: ';
	print_r( $new_agents_array );
	print_r($unique_agent_id_array);
	echo 'id' . $id ;
	echo 'data: ';
	print_r( $data );
	echo PHP_EOL . PHP_EOL;
	echo 'old requestors: ' . PHP_EOL;
	print_r( $old_recall_requestors );
	echo 'new_unique_agent_array: ' . PHP_EOL;
	print_r( $new_unique_agent_array );
*/

	
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
	
} elseif( $type == 'approve_recall' ) {
	
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
	
	
	// Only Approve if recall is in status: Recalled
	if ( $recall_obj->recall_status_id == 729 ) {
		$data_status = [ 'recall_status_id' => 877 ]; //change status from Recalled to Recall Approved
		$obj = Patt_Custom_Func::update_recall_data( $data_status, $where );
		
		do_action('wpppatt_after_recall_approved', $ticket_id, 'R-'.$recall_id);
		
		
		//Update the Updated Date
		$current_datetime = date("yy-m-d H:i:s");
		$update = [	'updated_date' => $current_datetime ];
		$where = [ 'id' => $recall_id ];
		Patt_Custom_Func::update_recall_dates($update, $where);
		
		
		
		// Set PM Notifications 
		$notifications = '';

		
		// Get people on Recall 
		$where = [
			'recall_id' => $recall_id
		];
		$recall_data = Patt_Custom_Func::get_recall_data( $where );

		$agent_id_array = Patt_Custom_Func::translate_user_id( $recall_data[0]->user_id, 'agent_term_id' );
		
		// Merge the 3 arrays, and remove any duplicates
		//$pattagentid_array = array_unique( array_merge( $agent_id_array, $pattagentid_admin_array, $pattagentid_manager_array ) );
		$pattagentid_array = array_unique( $agent_id_array );
		
		// Split up array of users on Recall into 2 arrays: digitization staff and requesters
		$role_array_digi_staff = [ 'Administrator', 'Manager', 'Agent' ];
		$results_digi = Patt_Custom_Func::return_agent_ids_in_role( $pattagentid_array, $role_array_digi_staff);
		
		$role_array_requester = [  'Requester' ];
		$results_requester = Patt_Custom_Func::return_agent_ids_in_role( $pattagentid_array, $role_array_requester);
		

		$requestid = 'R-'.$recall_id; 			
		$data = [
	        'action_initiated_by' => $current_user->display_name
	    ];
		$email = 1;
		
		//$notification_post = 'email-recall-id-has-been-approved';
		// PM Notification to the Digitization Staff
		$notification_post = 'email-recall-id-has-been-approved-digitization-staff';		
		$new_notification = Patt_Custom_Func::insert_new_notification( $notification_post, $results_digi, $requestid, $data, $email );
	
		// PM Notification to the Requestors
		$notification_post = 'email-recall-id-has-been-approved-requestors';		
		$new_notification = Patt_Custom_Func::insert_new_notification( $notification_post, $results_requester, $requestid, $data, $email );
		
		
		
	}
	
	
	
} elseif( $type == 'deny_recall' ) {
	
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
	
	
	// Only Deny if recall is in status: Recalled
	if ( $recall_obj->recall_status_id == 729 ) {
		$data_status = [ 'recall_status_id' => 878 ]; //change status from Recalled to Recall Denied
		$obj = Patt_Custom_Func::update_recall_data( $data_status, $where );
		
		do_action('wpppatt_after_recall_denied', $ticket_id, 'R-'.$recall_id);
		
		//Update the Updated Date
		$current_datetime = date("yy-m-d H:i:s");
		$update = [	'updated_date' => $current_datetime ];
		$where = [ 'id' => $recall_id ];
		Patt_Custom_Func::update_recall_dates($update, $where);
		
		
		
		// Set PM Notifications 
		$notifications = '';
		$notification_post = 'email-recall-id-has-been-denied';
		
		// Get people on Recall 
		$where = [
			'recall_id' => $recall_id
		];
		$recall_data = Patt_Custom_Func::get_recall_data( $where );

		$agent_id_array = Patt_Custom_Func::translate_user_id( $recall_data[0]->user_id, 'agent_term_id' );
		
		
		$role_array_requester = [  'Requester' ];
		$results_requester = Patt_Custom_Func::return_agent_ids_in_role( $agent_id_array, $role_array_requester);
		
		// set pattagentid_array
// 		$pattagentid_array = $agent_id_array; 
		$pattagentid_array = $results_requester; 

		$requestid = 'R-'.$recall_id; 			
		$data = [
	        'action_initiated_by' => $current_user->display_name
	    ];
		$email = 1;
		
		$new_notification = Patt_Custom_Func::insert_new_notification( $notification_post, $pattagentid_array, $requestid, $data, $email );
		
		
	}
	
	
	
} elseif( $type == 'check_assignment_balance' ) {
	
	
	
	$where = [
		'recall_id' => $recall_id
	];
	$recall_data = Patt_Custom_Func::get_recall_data( $where );
	$agent_id_array = Patt_Custom_Func::translate_user_id( $recall_data[0]->user_id, 'agent_term_id' );
	
	// Add NEW to agent id array
	if( $new_agent_id != '' ) { // not being used. 
// 		$agent_id_array[] = $new_agent_id;
		$agent_id_array[] = (int)$new_agent_id;
	}
	
	if( isset( $array_of_agent_ids ) ) {
		//$agent_id_array = array_unique( array_merge( $agent_id_array, $array_of_agent_ids ) );
		//$agent_id_array =  array_merge( $agent_id_array, $array_of_agent_ids ) ;
		$test = 'array_of_agent_ids is happening';
		$agent_id_array = $array_of_agent_ids;
	}
	
	// Check for
	$role_array_digi_staff = [ 'Administrator', 'Manager', 'Agent' ];
	$results_digi = Patt_Custom_Func::return_agent_ids_in_role( $agent_id_array, $role_array_digi_staff );
	
	$role_array_requester = [  'Requester' ];
	$results_requester = Patt_Custom_Func::return_agent_ids_in_role( $agent_id_array, $role_array_requester );
	
	$recall_staff_meets_requirements = false;
	$recall_staff_digi_valid = true;
	$recall_staff_requester_valid = true;
	
	if( count( $results_digi ) > 0 && count( $results_requester ) > 0 ) {
		$recall_staff_meets_requirements = true;
	} 
	if ( count( $results_digi ) == 0 ) {
		$recall_staff_digi_valid = false;
	}
	if ( count( $results_requester ) == 0 ) {
		$recall_staff_requester_valid = false;
	}

	$response = array(
		"staff_meets_requirements" => $recall_staff_meets_requirements,
		"staff_digi_vali" => $recall_staff_digi_valid,
		"staff_requester_valid" => $recall_staff_requester_valid,
		"agent_id_array" => $agent_id_array,
		"new_agent_id" => $new_agent_id,
		"test" => $test
	);
	
/*
	$response = [
		"staff_meets_requirements" => $recall_staff_meets_requirements,
		"staff_digi_vali" => $recall_staff_digi_valid,
		"staff_requester_valid" => $recall_staff_requester_valid
	];
*/
	
	echo json_encode($response);
	
	//return $response;
	
	
}




?>