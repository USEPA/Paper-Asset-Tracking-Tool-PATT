<?php

$path = preg_replace('/wp-content.*$/','',__DIR__);
include($path.'wp-load.php');

$host = 'mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset='.DB_CHARSET;
$connect = new PDO($host, DB_USER, DB_PASSWORD);

$method = $_SERVER['REQUEST_METHOD'];

global $wpdb, $current_user, $wpscfunction;

$db_null = -99999;

if($method == 'GET')
{
	$category = isset($_GET['category']) ? sanitize_text_field($_GET['category']) : '';  
// 	$category = isset($_POST['category']) ? sanitize_text_field($_POST['category']) : '';  	

	
	if( $category == 'recall' || $category == '' ) {
		$new_recall_id_json = $_GET['recall_ids'];
		$new_recall_id_array = str_getcsv($new_recall_id_json);
	

		foreach($new_recall_id_array as $item_id) {

		 	$where = [
				'recall_id' => $item_id
			];
			$item_details_array = Patt_Custom_Func::get_recall_data($where);
			//$item_details_obj = $item_details_array[0];
			
			// Code block allows for new shipping and multi user added to wppatt-custom-function.php - 
			// Patt_Custom_Func::get_recall_data return array no longer has 0 index for object. Can be any number. 
			// Code grabs first array key and uses this to return the obj. 
			
			//Added for servers running < PHP 7.3
			if (!function_exists('array_key_first')) {
			    function array_key_first(array $arr) {
			        foreach($arr as $key => $unused) {
			            return $key;
			        }
			        return NULL;
			    }
			}
			
			$item_array_key = array_key_first($item_details_array);		
			$item_details_obj = $item_details_array[$item_array_key];
			
			//NEW - END
			$item_details_obj_status = $item_details_obj->status;
			if( $item_details_obj->recall_status == 'Recall Cancelled' ) {
				$item_details_obj_status = 'This item has cancelled. Any Changes will not be saved.';
			}
		
		 
			$output[] = array(
				'id'    => $item_id,
				'recall_id'    => "R-".$item_id,
				'ticket_id'    => $item_details_obj->ticket_id, 
				'company_name'  => $item_details_obj->shipping_carrier,
				'tracking_number'   =>  $item_details_obj->tracking_number, 
		 		'status'    => $item_details_obj_status 
// 		 		'status'    => '!!!'.$category.'!!!'
			);
		}
		
	} elseif( $category == 'return' ) {
		
		$new_return_id_json = $_GET['return_ids'];
		$new_return_id_array = str_getcsv($new_return_id_json);
	
		foreach($new_return_id_array as $item_id) {
			
		 	$where = [
				'return_id' => $item_id
			];
			$item_details_array = Patt_Custom_Func::get_return_data($where);
			
			// Code block allows for new shipping and multi user added to wppatt-custom-function.php - 
			// Patt_Custom_Func::get_recall_data return array no longer has 0 index for object. Can be any number. 
			// Code grabs first array key and uses this to return the obj. 
			
			//Added for servers running < PHP 7.3
			if (!function_exists('array_key_first')) {
			    function array_key_first(array $arr) {
			        foreach($arr as $key => $unused) {
			            return $key;
			        }
			        return NULL;
			    }
			}
			
			$item_array_key = array_key_first($item_details_array);		
			$item_details_obj = $item_details_array[$item_array_key];
			
			//NEW - END
			$item_details_obj_status = $item_details_obj->status;
			if( $item_details_obj->return_status_id == 791 ) { //Old 785; now: 791
				$item_details_obj_status = 'This item has cancelled. Any Changes will not be saved.';
			}
		
		 
			$output[] = array(
				'id'    => $item_id,
				'recall_id'    => "RTN-".$item_id,
				'ticket_id'    => $item_details_obj->ticket_id, 
				'company_name'  => $item_details_obj->shipping_carrier,
				'tracking_number'   =>  $item_details_obj->tracking_number, 
 		 		'status'    => $item_details_obj_status
// 		 		'status'    => 'TEST'
			);
		}
	} elseif( $category == 'shipping-status-editor' ) {
		$new_shipping_table_id_json = $_GET['shipping_table_ids'];
		$new_shipping_table_id_array = str_getcsv($new_shipping_table_id_json);
	
		foreach($new_shipping_table_id_array as $item_id) {
			
		 	// grab item row from db.
		 	
		 	$data = array(
				':shipping_table_id'  => $item_id
			);
			
			$query = "SELECT * FROM wpqa_wpsc_epa_shipping_tracking WHERE id = :shipping_table_id ";
/*
			$query = "SELECT * FROM wpqa_wpsc_epa_shipping_tracking Tracking 
						INNER JOIN wpqa_wpsc_ticket Ticket ON Ticket.id = Tracking.ticket_id
						INNER JOIN wpqa_wpsc_epa_recallrequest Recall ON Recall.id = Tracking.recallrequest_id
						INNER JOIN wpqa_wpsc_epa_return ReturnX ON ReturnX.id = Tracking.return_id
					WHERE id = :shipping_table_id ";
*/
			$statement = $connect->prepare($query);
			$statement->execute($data);
			//$result = $statement->fetchColumn(); 
			$result = $statement->fetch(); 			
		 	
			// check if it's request, recall, or return
			// if recall, check if it's cancelled
			// if return, check if it's cancelled
			// if request, what conditions don't allow it to be edited. 


		
		 
			$output[] = array(
				'id'    => $result['id'],
				'recall_id'    => $result['id'],
				'ticket_id'    => $result['ticket_id'], 
				'company_name'  => $result['company_name'],
				'tracking_number'   =>  $result['tracking_number'], 
 		 		'status'    => $result['status']
// 		 		'status'    => 'TEST'
			);
		}
	}

	header("Content-Type: application/json");
	echo json_encode($output);
}

// Not used 
if($method == "POST") {

	$data = array(
		':ticket_id'  => $_GET['ticket_id'],
		':company_name'  => $_POST["company_name"],
		':tracking_number'    => $_POST["tracking_number"]
	);
	
	$query = "INSERT INTO wpqa_wpsc_epa_shipping_tracking (ticket_id, company_name, status, tracking_number, recallrequest_id) VALUES (:ticket_id, :company_name, '', :tracking_number, '0')";
	$statement = $connect->prepare($query);
	$statement->execute($data);
	do_action('wpppatt_after_add_request_shipping_tracking', $_GET['ticket_id'], $_POST["tracking_number"]);
}

if($method == 'PUT') {
	
	$category = isset($_GET['category']) ? sanitize_text_field($_GET['category']) : '';  

	if( $category == 'recall' || $category == '' ) {
		
		parse_str(file_get_contents("php://input"), $_PUT);
	
		$item_id = $_PUT['id'];
		$item_name = $_PUT['recall_id'];	
		$carrier_name = $_PUT['company_name'];
		$tracking_number = $_PUT['tracking_number'];
		
		$data = [
			'company_name' => $carrier_name,
			'tracking_number' => $tracking_number
		];
		$where = [
			'recall_id' => $item_id
		];
		
		// Update Recall status state machine - must be done before inserting shipping data.
		$recall_array = Patt_Custom_Func::get_recall_data( $where );
		
		// Code block allows for new shipping and multi user added to wppatt-custom-function.php - 
		// Patt_Custom_Func::get_recall_data return array no longer has 0 index for object. Can be any number. 
		// Code grabs first array key and uses this to return the obj. 
		
		//Added for servers running < PHP 7.3
		if (!function_exists('array_key_first')) {
		    function array_key_first(array $arr) {
		        foreach($arr as $key => $unused) {
		            return $key;
		        }
		        return NULL;
		    }
		}
		
		$item_array_key = array_key_first($recall_array);		
		$recall_obj = $recall_array[$item_array_key];
		
		
		// Update Recall State Machine
		// if in Recalled [729] and shipping data added, update to Shipped [730]
		// if in On Loan [731] and shipping data added, update to Shipped Back [732]
		// This piece of the State Machine is now located in /admin/recall_shipping_status_cron.php
/*
		if ( $recall_obj->recall_status_id == 729 && $recall_obj->tracking_number == '' && $recall_obj->company_name == '' ) {
			$data_status = [ 'recall_status_id' => 730 ]; //change status from Recalled to Shipped
			$obj = Patt_Custom_Func::update_recall_data( $data_status, $where );
		} elseif ( $recall_obj->recall_status_id == 731 && $recall_obj->tracking_number == '' && $recall_obj->company_name == '') {
			$data_status = [ 'recall_status_id' => 732 ]; //change status from Recalled to Shipped
			$obj = Patt_Custom_Func::update_recall_data( $data_status, $where );
		}
*/
		
		
		// If Not in state Cancelled [734]: Update shipping data
		if( $recall_obj->recall_status_id != 734 ) {
			$recall_array = Patt_Custom_Func::update_recall_shipping( $data, $where );
			
			//Update the Updated Date
			$current_datetime = date("yy-m-d H:i:s");
			$update = [	'updated_date' => $current_datetime ];
			$where = [ 'id' => $item_id ];
			Patt_Custom_Func::update_recall_dates($update, $where);
			
			// Audit log
			do_action('wpppatt_after_recall_details_shipping',  $recall_obj->ticket_id, 'R-'.$recall_obj->recall_id, strtoupper($carrier_name).' - '.$tracking_number );  
		}

		return true;
		
	} elseif( $category == 'return' ) {
		
		parse_str(file_get_contents("php://input"), $_PUT);
	
		$item_id = $_PUT['id'];
		$item_name = $_PUT['recall_id']; //called recall_id on jsGrid
		$carrier_name = $_PUT['company_name'];
		$tracking_number = $_PUT['tracking_number'];
		
		$data = [
			'company_name' => $carrier_name,
			'tracking_number' => $tracking_number
		];
		$where = [
			'return_id' => $item_id
		];
		
		// Update Recall status state machine - must be done before inserting shipping data.
		$return_array = Patt_Custom_Func::get_return_data( $where );
		
		// Code block allows for new shipping and multi user added to wppatt-custom-function.php - 
		// Patt_Custom_Func::get_recall_data return array no longer has 0 index for object. Can be any number. 
		// Code grabs first array key and uses this to return the obj. 
		
		//Added for servers running < PHP 7.3
		if (!function_exists('array_key_first')) {
		    function array_key_first(array $arr) {
		        foreach($arr as $key => $unused) {
		            return $key;
		        }
		        return NULL;
		    }
		}
		
		$item_array_key = array_key_first($return_array);		
		$return_obj = $return_array[$item_array_key];
		
		
		// Update Return State Machine // Temporary until CRON Job updates
		// if in Return Initiated [752] and shipping data added, update to Return Shipped [753]
		// if in Return Shipped [753] and shipping data added, update to Return Complete [754]
		// This piece of the State Machine is now located in /admin/return_shipping_status_cron.php	
/*
		if ( $return_obj->return_status_id == 752 ) {
			$data_status = [ 'return_status_id' => 753 ]; //change status from Returned to Shipped
			$obj = Patt_Custom_Func::update_return_data( $data_status, $where );
		} elseif ( $return_obj->return_status_id == 753 && $return_obj->tracking_number == '' && $return_obj->company_name == '') {
			$data_status = [ 'return_status_id' => 754 ]; //change status from Shipped to Received
			$obj = Patt_Custom_Func::update_return_data( $data_status, $where );
		}
*/
		
		
		// Get each ticket_id for Audit Log		
		$box_list = ($return_obj->box_id) ? $return_obj->box_id : []; 
		$folderfile_list = ($return_obj->folderdoc_id) ? $return_obj->folderdoc_id : [];
		
		// Create single array with box and folderdocs
		$collated_box_folderfile_list = [];
		
		foreach( $box_list as $key=>$box ) {
			if( $box != $db_null ) {
				$collated_box_folderfile_list[] = $box;
			} else {
				$collated_box_folderfile_list[] = $folderfile_list[$key];
			}
				
		}		
		
		// Get array of ticket_id's
		$ticket_id_array = [];
		
		foreach( $collated_box_folderfile_list as $item ) {
			$dataX = [ 'box_folder_file_id' => $item ];
			$ticket_obj = Patt_Custom_Func::get_ticket_id_from_box_folder_file($dataX);
// 			$ticket_id_array[] = $ticket_obj->ticket_id;
			$ticket_id_array[] = $ticket_obj['ticket_id'];
		}
		
		$ticket_id_array = array_unique($ticket_id_array);		
		
		// If Not in state Cancelled: Update shipping data
		if( $return_obj->return_status_id != 791 ) { //Old 785; now: 791
			$return_array = Patt_Custom_Func::update_return_shipping( $data, $where );
			
			//Update the Updated Date
			$current_datetime = date("yy-m-d H:i:s");
			$update = [	'updated_date' => $current_datetime ];
			$where = [ 'id' => $item_id ];
			Patt_Custom_Func::update_return_dates($update, $where);
			
			
			// Audit log
			foreach( $ticket_id_array as $ticket_id ) {
				do_action('wpppatt_after_return_details_shipping',  $ticket_id, 'RTN-'.$return_obj->return_id, strtoupper($carrier_name).' - '.$tracking_number ); 
			}
			
			 
		}
		
		
		return true;
	} elseif( $category == 'shipping-status-editor' ) {
		
		parse_str(file_get_contents("php://input"), $_PUT);
		$validated = true;	
		$item_id = $_PUT['id'];
		$item_name = $_PUT['recall_id']; //called recall_id on jsGrid // Not used?
		$carrier_name = $_PUT['company_name'];
		$tracking_number = $_PUT['tracking_number'];
		
		$data = [
			'id' => $item_id,
			'company_name' => $carrier_name,
			'tracking_number' => $tracking_number
		];
		$where = [
			'return_id' => $item_id
		];
		
		
		// If validated update the data.

		if( $validated ) { 
			
			
			
			$query = "UPDATE wpqa_wpsc_epa_shipping_tracking SET company_name=:company_name, tracking_number=:tracking_number WHERE id =:id ";
			$statement = $connect->prepare($query);
			$statement->execute($data);

			
			
			
			
			
			
			// Audit log
			// Audit log
			// Audit log			
			 
		}
		
		
		return true;
	}

	// For testing?
	$output = array(
		'return obj' => $return_obj,
		'collated_box_folderfile_list' => $collated_box_folderfile_list,
		'ticket_id_array' => $ticket_id_array,
		'folderfile_list' => $folderfile_list,
		'ticket_obj' => $ticket_obj,
		'return_array' => $return_array,
		'data' => $data,
		'where' => $where
	);
	
	header("Content-Type: application/json");
//	echo json_encode($output); //When uncommented, output appears in console, but modal removes number.

}

if($method == "DELETE") {
	parse_str(file_get_contents("php://input"), $_DELETE);
	$query = "DELETE FROM wpqa_wpsc_epa_shipping_tracking WHERE id = '".$_DELETE["id"]."'";
	$statement = $connect->prepare($query);
	$statement->execute();
	do_action('wpppatt_after_remove_request_shipping_tracking', $_GET['ticket_id'], $_DELETE["tracking_number"]);
}

?>