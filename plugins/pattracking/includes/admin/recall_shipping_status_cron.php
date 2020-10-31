<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// UPDATE to update database based on list of items that are listed as shipped '1'.

global $current_user, $wpscfunction, $wpdb;

// For Recall Status to change from Recall Approved [877] to Shipped [730]
$shipped_recall_status_query = $wpdb->get_results(
	"SELECT 
      shipping.id,
      shipping.tracking_number,
      shipping.shipped,
      shipping.delivered,
      shipping.recallrequest_id,
      rr.id,
      rr.recall_id as recall_id,
      rr.recall_status_id as recall_status
    FROM 
	    wpqa_wpsc_epa_shipping_tracking AS shipping
    INNER JOIN 
		wpqa_wpsc_epa_recallrequest AS rr 
	ON (
        shipping.recallrequest_id = rr.id
	   )
	WHERE 
        shipping.recallrequest_id <> -99999
      AND 
        shipping.company_name <> ''
      AND
        shipping.shipped = 1
      AND 
        rr.recall_status_id = 877
      ORDER BY shipping.id ASC"
	);

/* // OLD before Recall Approve / Recall Deny statuses.
// For Recall Status to change from Recalled [729] to Shipped [730]
$shipped_recall_status_query = $wpdb->get_results(
	"SELECT 
      shipping.id,
      shipping.tracking_number,
      shipping.shipped,
      shipping.delivered,
      shipping.recallrequest_id,
      rr.id,
      rr.recall_id as recall_id,
      rr.recall_status_id as recall_status
    FROM 
	    wpqa_wpsc_epa_shipping_tracking AS shipping
    INNER JOIN 
		wpqa_wpsc_epa_recallrequest AS rr 
	ON (
        shipping.recallrequest_id = rr.id
	   )
	WHERE 
        shipping.recallrequest_id <> -99999
      AND 
        shipping.company_name <> ''
      AND
        shipping.shipped = 1
      AND 
        rr.recall_status_id = 729
      ORDER BY shipping.id ASC"
	);
*/
	
// For Recall Status to change from Recall Approved [729] to Shipped [730]
foreach ($shipped_recall_status_query as $item) {
	
	// update recall status to Shipped [730]
	$recall_id = $item->recall_id;	
	$where = [ 'id' => $recall_id ];
	$data_status = [ 'recall_status_id' => 730 ]; //change status from Recall Approved to Shipped 
	$obj = Patt_Custom_Func::update_recall_data( $data_status, $where );
	
	// Update recall db request_receipt_date when shipped. 
	$where = [ 'id' => $recall_id ];
	$current_datetime = date("yy-m-d H:i:s");
 	$data = [ 'request_receipt_date' => $current_datetime ]; 
	Patt_Custom_Func::update_recall_data( $data, $where );
	
	// No need to clear shipped status as all shipping data will need to be preserved for Delivered column
/*
	$data = [
		'company_name' => '',
		'tracking_number' => '',
 		'shipped' => 0,
		'status' => ''
	];
	$where = [
		'recall_id' => $recall_id
	];

	$recall_array = Patt_Custom_Func::update_recall_shipping( $data, $where );	
*/
	
}




// For Recall Status to change from Shipped [730] to On Loan [731]
$on_loan_recall_status_query = $wpdb->get_results(
	"SELECT 
      shipping.id,
      shipping.tracking_number,
      shipping.shipped,
      shipping.delivered,
      shipping.recallrequest_id,
      rr.id,
      rr.recall_id as recall_id,
      rr.recall_status_id as recall_status
    FROM 
	    wpqa_wpsc_epa_shipping_tracking AS shipping
    INNER JOIN 
		wpqa_wpsc_epa_recallrequest AS rr 
	ON (
        shipping.recallrequest_id = rr.id
	   )
	WHERE 
        shipping.recallrequest_id <> -99999
      AND 
        shipping.company_name <> ''
      AND
        shipping.delivered = 1
      AND 
        rr.recall_status_id = 730
      ORDER BY shipping.id ASC"
	);
	
// For Recall Status to change from Shipped [730] to On Loan [731]
foreach ($on_loan_recall_status_query as $item) {
	
	// update recall status to On Loan [731]
	$recall_id = $item->recall_id;	
	$where = [ 'id' => $recall_id ];
	$data_status = [ 'recall_status_id' => 731 ]; //change status from Shipped to On Loan 
	$obj = Patt_Custom_Func::update_recall_data( $data_status, $where );
	
	// Reset the shipping details as the same id is used for shipping to requestor and back to digitization center.
	$data = [
		'company_name' => '',
		'tracking_number' => '',
		'shipped' => 0,
		'delivered' => 0,		
		'status' => ''
	];
	$where = [
		'recall_id' => $recall_id
	];

	$recall_array = Patt_Custom_Func::update_recall_shipping( $data, $where );	
	
	
	  
	
	
	
	// Update Recall DB Received date 
	$where = [ 'id' => $recall_id ];
	$current_datetime = date("yy-m-d H:i:s");
	$data = [ 'return_date' => $current_datetime ]; 
	Patt_Custom_Func::update_recall_data( $data, $where );
	
	// Need to update Recall shipping dates in recallrequest table.
	
	
}



// For Recall Status to change from On Loan [731] to Shipped Back [732]
$shipped_back_recall_status_query = $wpdb->get_results(
	"SELECT 
      shipping.id,
      shipping.tracking_number,
      shipping.shipped,
      shipping.delivered,
      shipping.recallrequest_id,
      rr.id,
      rr.recall_id as recall_id,
      rr.recall_status_id as recall_status
    FROM 
	    wpqa_wpsc_epa_shipping_tracking AS shipping
    INNER JOIN 
		wpqa_wpsc_epa_recallrequest AS rr 
	ON (
        shipping.recallrequest_id = rr.id
	   )
	WHERE 
        shipping.recallrequest_id <> -99999
      AND 
        shipping.company_name <> ''
      AND
        shipping.shipped = 1
      AND 
        rr.recall_status_id = 731
      ORDER BY shipping.id ASC"
	);
	
// For Recall Status to change from On Loan [731] to Shipped Back [732]
foreach ($shipped_back_recall_status_query as $item) {
	
	// update recall status to Shipped Back [732]
	$recall_id = $item->recall_id;	
	$where = [ 'id' => $recall_id ];
	$data_status = [ 'recall_status_id' => 732 ]; //change status from On Loan to Shipped Back
	$obj = Patt_Custom_Func::update_recall_data( $data_status, $where );
	
	// No need to clear shipped status as all shipping data will need to be preserved for Delivered column
	
	// Update recall db request_receipt_date when shipped. 
	$where = [ 'id' => $recall_id ];
	$current_datetime = date("yy-m-d H:i:s");
 	$data = [ 'request_receipt_date' => $current_datetime ]; 
	Patt_Custom_Func::update_recall_data( $data, $where );
	
	
	
	
	// Set PM Notifications 
	$notification_post = 'email-recall-id-has-been-shipped-back';
	
	// Get digitization staff
	$agent_admin_group_name = 'Administrator';
	$pattagentid_admin_array = Patt_Custom_Func::agent_from_group( $agent_admin_group_name );
	 
	$agent_manager_group_name = 'Manager';
	$pattagentid_manager_array = Patt_Custom_Func::agent_from_group( $agent_manager_group_name );
	
	// Get people on Recall 
	$where = [
		'recall_id' => $recall_id
	];
	$recall_data = Patt_Custom_Func::get_recall_data( $where );

	$agent_id_array = Patt_Custom_Func::translate_user_id( $recall_data[0]->user_id, 'agent_term_id' );;
	
	// Merge the 3 arrays, and remove any duplicates
	$pattagentid_array = array_unique(array_merge( $agent_id_array, $pattagentid_admin_array, $pattagentid_manager_array ));
	
	$requestid = 'R-'.$recall_id; 			
	$data = [
        'action_initiated_by' => $current_user->display_name
    ];
	$email = 0;
	
	$new_notification = Patt_Custom_Func::insert_new_notification( $notification_post, $pattagentid_array, $requestid, $data, $email );
	
	
}



// For Recall Status to change from Shipped Back [732] to Recall Complete [733]
$recall_complete_recall_status_query = $wpdb->get_results(
	"SELECT 
      shipping.id,
      shipping.tracking_number,
      shipping.shipped,
      shipping.delivered,
      shipping.recallrequest_id,
      rr.id,
      rr.recall_id as recall_id,
      rr.recall_status_id as recall_status
    FROM 
	    wpqa_wpsc_epa_shipping_tracking AS shipping
    INNER JOIN 
		wpqa_wpsc_epa_recallrequest AS rr 
	ON (
        shipping.recallrequest_id = rr.id
	   )
	WHERE 
        shipping.recallrequest_id <> -99999
      AND 
        shipping.company_name <> ''
      AND
        shipping.delivered = 1
      AND 
        rr.recall_status_id = 732
      ORDER BY shipping.id ASC"
	);
	
// For Recall Status to change from Shipped Back [732] to Recall Complete [733]
foreach ($recall_complete_recall_status_query as $item) {
	
	// update recall status to Recall Complete [733]
	$recall_id = $item->recall_id;	
	$where = [ 'id' => $recall_id ];
	$data_status = [ 'recall_status_id' => 733 ]; //change status from On Loan to Shipped Back
	$obj = Patt_Custom_Func::update_recall_data( $data_status, $where );
	
	// Update Recall DB Received date 
	$where = [ 'id' => $recall_id ];
	$current_datetime = date("yy-m-d H:i:s");
	$data = [ 'return_date' => $current_datetime ]; 
	Patt_Custom_Func::update_recall_data( $data, $where );
	
	// No need to clear shipped status as all shipping data will need to be preserved for Delivered column
	// Reset the shipping details as the same id is used for shipping to requestor and back to digitization center.
/*
	$data = [
		'company_name' => '',
		'tracking_number' => '',
		'shipped' => 0,
		'delivered' => 0,		
		'status' => ''
	];
	$where = [
		'recall_id' => $recall_id
	];

	$recall_array = Patt_Custom_Func::update_recall_shipping( $data, $where );
*/
}



?>
