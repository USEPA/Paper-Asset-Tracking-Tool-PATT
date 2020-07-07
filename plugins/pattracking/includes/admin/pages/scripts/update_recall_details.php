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
	
	
	if ( $recall_data[0]->recall_status_id = 730 ) {
		$data_status = [ 'recall_status_id' => 731 ]; //change status from Recalled to Shipped
		$obj = Patt_Custom_Func::update_recall_data( $data_status, $where );
		//update_recall_status('received-date-added');
	}
	
	// Clear out old shipping data. Required for State Machine to function properly.
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
	
	
	if ( $recall_data[0]->recall_status_id = 732 ) {
		$data_status = [ 'recall_status_id' => 733 ]; //change status from Recalled to Shipped 
		$obj = Patt_Custom_Func::update_recall_data( $data_status, $where );
		//update_recall_status('received-date-added');
	}
	//update_recall_status('return-date-added');
	
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




 /*
elseif( $type == 'shipping' ) { //Not actually used. Can be removed? done in ajax/fetch_shipping_data_multi
	$new_shipping_tracking = isset($_POST['new_shipping_tracking']) ? sanitize_text_field($_POST['new_shipping_tracking']) : '';
	$new_shipping_carrier = isset($_POST['new_shipping_carrier']) ? sanitize_text_field($_POST['new_shipping_carrier']) : '';	
	

	echo 'Recall ID: '.$recall_id.PHP_EOL;
	echo 'Type: '.$type.PHP_EOL;
	echo 'Shipping Tracking: '.$new_shipping_tracking.PHP_EOL;
	echo 'Shipping Carrier: '.$new_shipping_carrier.PHP_EOL;
	
	$new_shipping_tracking_carrier_string = $new_shipping_carrier.': '.$new_shipping_tracking;
	
	//Update the Updated Date
	$current_datetime = date("yy-m-d H:i:s");
	$update = [	'updated_date' => $current_datetime ];
	$where = [ 'id' => $recall_id ];
	Patt_Custom_Func::update_recall_dates($update, $where);
	
	update_recall_status('add-shipping-tracking');
	do_action('wpppatt_after_recall_details_shipping', $ticket_id, $recall_id, $new_shipping_tracking_carrier_string );
	
}  elseif( $type == 'status' ) { //Not actually used. Can be removed? status is updated when elements change. 
	$new_status = isset($_POST['new_status']) ? sanitize_text_field($_POST['new_status']) : '';
 	echo PHP_EOL;
	echo 'Recall IDs: '.PHP_EOL;
	print_r($recall_ids);
	echo PHP_EOL.'typeof: ';
	echo gettype($recall_ids).PHP_EOL;
	echo PHP_EOL.'Type: '.$type.PHP_EOL;
	echo 'New Status: '.$new_status.PHP_EOL;

	

	//Put this in foreach loop?
	foreach( $recall_ids as $id ) {
		do_action('wpppatt_after_recall_details_shipping', $ticket_id, $recall_id, $new_shipping_tracking_carrier_string );
	}

	
}
*/

//State machine for updating recall status as details are updated/changed. 
//Not used. Done elsewhere. 
/*
function update_recall_status( $status_type ) {
	
	if( $status_type == 'add-shipping-tracking' ) {
		$status = 'Shipped';
		$status_term_id = 730;
		
	} elseif( $status_type == 'received-date-added' ) {
		$status = 'On Loan';
		$status_term_id = 731;
		
		$where = [
			'recall_id' => $item_id
		];
		
		// Update Recall status state machine - must be done before inserting shipping data.
		$recall_data = Patt_Custom_Func::get_recall_data( $where );
		
		if ( $recall_data[0]->recall_status_id = 729 && $recall_data[0]->tracking_number == '' && $recall_data[0]->company_name == '' ) {
			$data_status = [ 'recall_status_id' => 730 ]; //change status from Recalled to Shipped
			$obj = Patt_Custom_Func::update_recall_data( $data_status, $where );
		}
		
	} elseif( $status_type == 'post-loan-shipped' ) {
		$status = 'Shipped Back';
		$status_term_id = 732;
		
	} elseif( $status_type == 'return-date-added' ) {
		$status = 'Recall Complete';
		$status_term_id = 733;
		
	} 
	
}
*/





/*
if(isset($_POST['postvarsboxidname'])){
   $box_id = $_POST['postvarsboxidname'];
   $dc = $_POST['postvarsdc'];

   $box_details = $wpdb->get_row(
"SELECT 
wpqa_wpsc_epa_boxinfo.storage_location_id as storage_location_id, 
wpqa_wpsc_epa_boxinfo.id as id, 
wpqa_wpsc_epa_boxinfo.box_id as box_id, 
wpqa_wpsc_epa_storage_location.digitization_center as digitization_center,
wpqa_wpsc_epa_storage_location.aisle as aisle,
wpqa_wpsc_epa_storage_location.bay as bay,
wpqa_wpsc_epa_storage_location.shelf as shelf,
wpqa_wpsc_epa_storage_location.shelf as position
FROM wpqa_wpsc_epa_boxinfo
INNER JOIN wpqa_wpsc_epa_storage_location ON wpqa_wpsc_epa_boxinfo.storage_location_id = wpqa_wpsc_epa_storage_location.id
WHERE wpqa_wpsc_epa_boxinfo.id = '" . $box_id . "'"
			);
			

			$box_storage_location_id = $box_details->storage_location_id;
			$box_storage_digitization_center = $box_details->digitization_center;
			$box_storage_aisle = $box_details->aisle;
			$box_storage_bay = $box_details->bay;
			$box_storage_shelf = $box_details->shelf;
			$box_sotrage_shelf_id = $box_storage_aisle . '_' . $box_storage_bay . '_' . $box_storage_shelf;
			$box_id_val = $box_details->box_id;

$box_storage_status = $wpdb->get_row(
"SELECT 
occupied,
remaining
FROM wpqa_wpsc_epa_storage_status
WHERE shelf_id = '" . $box_sotrage_shelf_id . "'"
			);

$box_storage_status_occupied = $box_storage_status->occupied;
$box_storage_status_remaining = $box_storage_status->remaining;
$box_storage_status_remaining_added = $box_storage_status->remaining + 1;

if ($box_storage_status_remaining <= 4) {
$table_ss = 'wpqa_wpsc_epa_storage_status';
$ssr_update = array('remaining' => $box_storage_status_remaining_added);
$ssr_where = array('shelf_id' => $box_sotrage_shelf_id, 'digitization_center' => $box_storage_digitization_center);
$wpdb->update($table_ss , $ssr_update, $ssr_where);

if($box_storage_status_remaining == 4){
$sso_update = array('occupied' => 0);
$sso_where = array('shelf_id' => $box_sotrage_shelf_id, 'digitization_center' => $box_storage_digitization_center);
$wpdb->update($table_ss , $sso_update, $sso_where);
}

$table_sl = 'wpqa_wpsc_epa_storage_location';
$sl_update = array('digitization_center' => $dc, 'aisle' => '0' ,'bay'=>'0','shelf'=>'0','position'=>'0');
$sl_where = array('id' => $box_storage_location_id);
$wpdb->update($table_sl , $sl_update, $sl_where);

echo "Box ID #: " . $box_id_val . " has been updated.\nAssigned Digitization Center: " .$dc;
   
} else {
 echo "Error updating location status table.";    
}

} else {
   echo "Update not successful.";
}
*/
?>