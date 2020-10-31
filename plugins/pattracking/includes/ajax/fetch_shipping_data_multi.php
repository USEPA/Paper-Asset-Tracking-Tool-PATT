<?php

$path = preg_replace('/wp-content.*$/','',__DIR__);
include($path.'wp-load.php');

$host = 'mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset='.DB_CHARSET;
$connect = new PDO($host, DB_USER, DB_PASSWORD);

$method = $_SERVER['REQUEST_METHOD'];

global $wpdb, $current_user, $wpscfunction;

$db_null = -99999;
$cancelled_notification_str = '<b>This item has been cancelled. Any Changes will not be saved.</b>';
$new_status_notification_str = '<b>This item has a status of "New". Any Changes will not be saved.</b>';
$already_shipped_notification_str = '<b>This item has already shipped. Any Changes will not be saved.</b>';
$already_delivered_notification_str = '<b>This item has already been delivered. Any Changes will not be saved.</b>';
$recall_not_approved_notification_str = '<b>This item has not yet been Approved or Denied. Any Changes will not be saved.</b>';
$recall_denied_notification_str = '<b>This item has been Denied. Any Changes will not be saved.</b>';

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
			

			$item_details_obj_status = $item_details_obj->status;
			
			// Update the displayed shipping status with any restrictions 
			if( $item_details_obj->recall_status == 'Recall Cancelled' ) {
				$item_details_obj_status = $cancelled_notification_str;
			} elseif( $item_details_obj->recall_status == 'Recalled' ) {
				$item_details_obj_status = $recall_not_approved_notification_str;
			} elseif( $item_details_obj->recall_status == 'Recall Denied' ) {
				$item_details_obj_status = $recall_denied_notification_str;
			} elseif( $item_details_obj->delivered == 1 ) {
				$item_details_obj_status .= ' '.$already_delivered_notification_str;
			} elseif( $item_details_obj->shipped == 1 ) {
				$item_details_obj_status .= ' '.$already_shipped_notification_str;
			}
			
		
			//NEW
            $tracking_num = '';

            if (substr( strtoupper($item_details_obj->tracking_number), 0, 4 ) === "DHL:") {
                $tracking_num = substr($item_details_obj->tracking_number, 4);
            } else {
                $tracking_num = $item_details_obj->tracking_number;
            }
            
			$output[] = array(
				'id'    => $item_id,
				'recall_id'    => "R-".$item_id,
				'ticket_id'    => $item_details_obj->ticket_id, 
				'company_name'  => $item_details_obj->shipping_carrier,
//				'tracking_number'   =>  $item_details_obj->tracking_number, 
                'tracking_number'   =>  $tracking_num,
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
			
			
			$item_details_obj_status = $item_details_obj->status;
			
			// Update the displayed shipping status with any restrictions 
			if( $item_details_obj->return_status_id == 791 ) { //Old 785; now: 791
				$item_details_obj_status = $cancelled_notification_str;
			} elseif( $item_details_obj->delivered == 1 ) {
				$item_details_obj_status .= ' '.$already_delivered_notification_str;
			} elseif( $item_details_obj->shipped == 1 ) {
				$item_details_obj_status .= ' '.$already_shipped_notification_str;
			}
		
			
			//NEW
            $tracking_num = '';

            if (substr( strtoupper($item_details_obj->tracking_number), 0, 4 ) === "DHL:") {
                $tracking_num = substr($item_details_obj->tracking_number, 4);
            } else {
                $tracking_num = $item_details_obj->tracking_number;
            }
			
			$output[] = array(
				'id'    => $item_id,
// 				'recall_id'    => "RTN-".$item_id,
				'recall_id'    => "D-".$item_id,
				'ticket_id'    => $item_details_obj->ticket_id, 
				'company_name'  => $item_details_obj->shipping_carrier,
// 				'tracking_number'   =>  $item_details_obj->tracking_number,
				'tracking_number'   =>  $tracking_num, 
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
					
			$query =   "SELECT
						    Tracking.id as shipping_id,
						    Recall.recall_id as recall_id,
						    Recall.recall_status_id as recall_status_id,
						    Ticket.request_id as request_id, 
						    Ticket.ticket_status as ticket_status,
						    ReturnX.return_id as return_id,
						    ReturnX.return_status_id as return_status_id,
						    Tracking.tracking_number as tracking_number,
						    Tracking.status as status,
						    Tracking.company_name as company_name,
						    Tracking.shipped,
                            Tracking.delivered
						FROM
						    wpqa_wpsc_epa_shipping_tracking Tracking
						INNER JOIN wpqa_wpsc_ticket Ticket ON
						    Ticket.id = Tracking.ticket_id
						INNER JOIN wpqa_wpsc_epa_recallrequest Recall ON
						    Recall.id = Tracking.recallrequest_id
						INNER JOIN wpqa_wpsc_epa_return ReturnX ON
						    ReturnX.id = Tracking.return_id
						WHERE
						    Tracking.id = :shipping_table_id";
			
			$statement = $connect->prepare($query);
			$statement->execute($data);
			$result = $statement->fetch(); 			
		 	
			// check if it's request, recall, or return
			// if recall, check if it's cancelled
			// if return, check if it's cancelled
			// if request, check if it's status is New [3] which cannot accept tracking. 
			
			$status = $result['status'];
			
			if( $result['recall_id'] != -99999 ) {
				
				$item_id = 'R-'.$result['recall_id'];
				if( $result['recall_status_id'] == 734 ) { // Recall Cancelled
					$status = $cancelled_notification_str;
				} elseif( $result['recall_status_id'] == 729 ) { // Recalled
					$status = $recall_not_approved_notification_str;
				} elseif( $result['recall_status_id'] == 878 ) { // Recall Denied
					$status = $recall_denied_notification_str;
				} elseif( $result['delivered'] == 1 ) {
					$status .= ' '.$already_delivered_notification_str;
				} elseif( $result['shipped'] == 1 ) {
					$status .= ' '.$already_shipped_notification_str;
				}
				
			} elseif( $result['return_id'] != -99999 ) {
				
// 				$item_id = 'RTN-'.$result['return_id'];
				$item_id = 'D-'.$result['return_id'];
				if( $result['return_status_id'] == 791 ) {
					$status = $cancelled_notification_str;
				} elseif( $result['delivered'] == 1 ) {
					$status .= ' '.$already_delivered_notification_str;
				} elseif( $result['shipped'] == 1 ) {
					$status .= ' '.$already_shipped_notification_str;
				}
				
			} elseif( $result['request_id'] != -99999 ) {
				$item_id = $result['request_id'];
				if( $result['ticket_status'] == 3 ) {
					$status = $new_status_notification_str;
				} elseif( $result['delivered'] == 1 ) {
					$status .= ' '.$already_delivered_notification_str;
				} elseif( $result['shipped'] == 1 ) {
					$status .= ' '.$already_shipped_notification_str;
				}
			}

			//NEW
            $tracking_num = '';

            if (substr( strtoupper($result['tracking_number']), 0, 4 ) === "DHL:") {
                $tracking_num = substr($result['tracking_number'], 4);
            } else {
                $tracking_num = $result['tracking_number'];
            }

		 
			$output[] = array(
 				'id'    => $result['shipping_id'],
//				'id'    => $item_id, 				
// 				'recall_id'    => $result['id'],
				'recall_id'    => $item_id,
				'ticket_id'    => $result['ticket_id'], 
				'company_name'  => $result['company_name'],
// 				'tracking_number'   =>  $result['tracking_number'], 
				'tracking_number'   =>  $tracking_num, 
 		 		'status'    => $status
// 		 		'status'    => 'TEST'
			);
		}
	}

	header("Content-Type: application/json");
	echo json_encode($output);
}

// Not used 
if($method == "POST") {

/*
	$data = array(
		':ticket_id'  => $_GET['ticket_id'],
		':company_name'  => $_POST["company_name"],
		':tracking_number'    => $_POST["tracking_number"]
	);
	
	$query = "INSERT INTO wpqa_wpsc_epa_shipping_tracking (ticket_id, company_name, status, tracking_number, recallrequest_id) VALUES (:ticket_id, :company_name, '', :tracking_number, '0')";
	$statement = $connect->prepare($query);
	$statement->execute($data);
	do_action('wpppatt_after_add_request_shipping_tracking', $_GET['ticket_id'], $_POST["tracking_number"]);
*/
}

if($method == 'PUT') {
	
	$category = isset($_GET['category']) ? sanitize_text_field($_GET['category']) : '';  

	if( $category == 'recall' || $category == '' ) {
		
		parse_str(file_get_contents("php://input"), $_PUT);
	
		$item_id = $_PUT['id'];
		$item_name = $_PUT['recall_id'];	
//         $carrier_name = $_PUT['company_name']; // OLD
        $carrier_name = Patt_Custom_Func::get_shipping_carrier($_PUT['tracking_number']); // NEW
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
		
		// If NOT in a restricted state (Cancelled [734], Recalled [729], Recall Denied [878] shipped, delivered) --> Update shipping data
		if( $recall_obj->recall_status_id != 734 && $recall_obj->recall_status_id != 729 && $recall_obj->recall_status_id != 878 && $recall_obj->shipped != 1 && $recall_obj->delivered != 1 ) {
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
//         $carrier_name = $_PUT['company_name']; // OLD
        $carrier_name = Patt_Custom_Func::get_shipping_carrier($_PUT['tracking_number']); // NEW
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
		
		// If NOT in a restricted state (Cancelled [791], shipped, delivered) --> Update shipping data
//		if( $return_obj->return_status_id != 791 ) { //Old 785; now: 791
		if( $return_obj->return_status_id != 791 && $return_obj->shipped != 1 && $return_obj->delivered != 1) {	
			$return_array = Patt_Custom_Func::update_return_shipping( $data, $where );
			
			//Update the Updated Date
			$current_datetime = date("yy-m-d H:i:s");
			$update = [	'updated_date' => $current_datetime ];
			$where = [ 'id' => $item_id ];
			Patt_Custom_Func::update_return_dates($update, $where);
			
			
			// Audit log
			foreach( $ticket_id_array as $ticket_id ) {
// 				do_action('wpppatt_after_return_details_shipping',  $ticket_id, 'RTN-'.$return_obj->return_id, strtoupper($carrier_name).' - '.$tracking_number ); 
				do_action('wpppatt_after_return_details_shipping',  $ticket_id, 'D-'.$return_obj->return_id, strtoupper($carrier_name).' - '.$tracking_number ); 
			}
			
			 
		}
		
		
		return true;
	} elseif( $category == 'shipping-status-editor' ) {
		
		parse_str(file_get_contents("php://input"), $_PUT);
		$validated = true;	
		$item_id = $_PUT['id']; // shipping table PK
		$item_name = $_PUT['recall_id']; //called recall_id on jsGrid // Not used?
//      $carrier_name = $_PUT['company_name']; // OLD
        $carrier_name = Patt_Custom_Func::get_shipping_carrier($_PUT['tracking_number']); // NEW
		$tracking_number = $_PUT['tracking_number'];
		
		$data = [
			'id' => $item_id,
			'company_name' => $carrier_name,
			'tracking_number' => $tracking_number
		];
/*
		$where = [
			'return_id' => $item_id
		];
*/
		
		
		// Check conditions.
		$search_data = array(
			':shipping_table_id'  => $item_id
		);
				
		$query =   "SELECT
					    Tracking.id as shipping_id,
					    Recall.recall_id as recall_id,
					    Recall.recall_status_id as recall_status_id,
					    Ticket.request_id as request_id, 
					    Ticket.ticket_status as ticket_status,
					    ReturnX.return_id as return_id,
					    ReturnX.return_status_id as return_status_id,
					    Tracking.tracking_number as tracking_number,
					    Tracking.status as status,
					    Tracking.company_name as company_name,
					    Tracking.shipped,
                        Tracking.delivered
					FROM
					    wpqa_wpsc_epa_shipping_tracking Tracking
					INNER JOIN wpqa_wpsc_ticket Ticket ON
					    Ticket.id = Tracking.ticket_id
					INNER JOIN wpqa_wpsc_epa_recallrequest Recall ON
					    Recall.id = Tracking.recallrequest_id
					INNER JOIN wpqa_wpsc_epa_return ReturnX ON
					    ReturnX.id = Tracking.return_id
					WHERE
					    Tracking.id = :shipping_table_id";
		
		$statement = $connect->prepare($query);
		$statement->execute($search_data);
		$result = $statement->fetch(); 	
		
		// check if it's request, recall, or return
		// if recall, check if it's cancelled
		// if return, check if it's cancelled
		// if request, check if it's status is New [3] which cannot accept tracking. 
		
		$status = $result['status'];
		$validated = true;
		$type = '';
		
		if( $result['recall_id'] != -99999 ) {
			$type = 'recall';
			$item_id_no_prefix = $result['recall_id'];
/*
			if( $result['recall_status_id'] == 734 ) {
				$status = $cancelled_notification_str;
				$validated = false;
			}
*/
			// Don't allow saving for these conditions
			// // Irestricted states (Cancelled [734], Recalled [729], Recall Denied [878] shipped, delivered) --> Update shipping data
		//if( $recall_obj->recall_status_id != 734 && $recall_obj->recall_status_id != 729 && $recall_obj->recall_status_id != 878 
		
			if( $result['recall_status_id'] == 734 ) {
				$status = $cancelled_notification_str;
				$validated = false;
			} elseif( $result['recall_status_id'] == 729 ) {
				$status = $recall_not_approved_notification_str;
				$validated = false;
			} elseif( $result['recall_status_id'] == 878 ) {
				$status = $recall_denied_notification_str;
				$validated = false;
			} elseif( $result['delivered'] == 1 ) {
				$status .= ' '.$already_delivered_notification_str;
				$validated = false;
			} elseif( $result['shipped'] == 1 ) {
				$status .= ' '.$already_shipped_notification_str;
				$validated = false;
			}
			
		} elseif( $result['return_id'] != -99999 ) {
			$type = 'return';
			$item_id_no_prefix = $result['return_id']; //$item_id = 'RTN-'.$result['return_id'];
/*
			if( $result['return_status_id'] == 791 ) {
				$status = $cancelled_notification_str;
				$validated = false;
			}
*/
			
			if( $result['return_status_id'] == 791 ) {
				$status = $cancelled_notification_str;
				$validated = false;
			} elseif( $result['delivered'] == 1 ) {
				$status .= ' '.$already_delivered_notification_str;
				$validated = false;
			} elseif( $result['shipped'] == 1 ) {
				$status .= ' '.$already_shipped_notification_str;
				$validated = false;
			}
			
		} elseif( $result['request_id'] != -99999 ) {
			$type = 'request';
			$item_id_no_prefix = $result['request_id']; //$item_id = $result['request_id'];
/*
			if( $result['ticket_status'] == 3 ) {
				$status = $new_status_notification_str;
				$validated = false;
			}
*/
			
			if( $result['ticket_status'] == 3 ) {
				$status = $new_status_notification_str;
				$validated = false;
			} elseif( $result['delivered'] == 1 ) {
				$status .= ' '.$already_delivered_notification_str;
				$validated = false;
			} elseif( $result['shipped'] == 1 ) {
				$status .= ' '.$already_shipped_notification_str;
				$validated = false;
			}
			
		}
		
		
		
		
		
		// If validated update the data.

		if( $validated ) { 
			
			// Update Data
			$query = "UPDATE wpqa_wpsc_epa_shipping_tracking SET company_name=:company_name, tracking_number=:tracking_number, status='' WHERE id =:id ";
			$statement = $connect->prepare($query);
			$statement->execute($data);

			
			
			//Audit Log
			if( $type == '' ) {
				// error - should never happen
			} elseif ( $type == 'recall' ) {
				
				//$item_id expected to be 0000001 if R-0000001
				$where = [
					'recall_id' => $item_id_no_prefix
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

				
				
			} elseif ( $type == 'return' ) {
				
				$validated = true;	
				
				$data = [
					'company_name' => $carrier_name,
					'tracking_number' => $tracking_number
				];
				$where = [
					'return_id' => $item_id_no_prefix
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
				$ret_ticket_id_array = [];
				
				foreach( $collated_box_folderfile_list as $item ) {
					$dataX = [ 'box_folder_file_id' => $item ];
					$ticket_obj = Patt_Custom_Func::get_ticket_id_from_box_folder_file($dataX);
		// 			$ticket_id_array[] = $ticket_obj->ticket_id;
					$ret_ticket_id_array[] = $ticket_obj['ticket_id'];
				}
				
				$ret_ticket_id_array = array_unique($ret_ticket_id_array);		
				
				// If Not in state Cancelled: Update shipping data
				if( $return_obj->return_status_id != 791 ) { //Old 785; now: 791
					//$return_array = Patt_Custom_Func::update_return_shipping( $data, $where );
					
					//Update the Updated Date
/*
					$current_datetime = date("yy-m-d H:i:s");
					$update = [	'updated_date' => $current_datetime ];
					$where = [ 'id' => $item_id ];
					Patt_Custom_Func::update_return_dates($update, $where);
*/
					
					
					// Audit log
					foreach( $ret_ticket_id_array as $ticket_id ) {
// 						do_action('wpppatt_after_return_details_shipping',  $ticket_id, 'RTN-'.$return_obj->return_id, strtoupper($carrier_name).' - '.$tracking_number ); 
						do_action('wpppatt_after_return_details_shipping',  $ticket_id, 'D-'.$return_obj->return_id, strtoupper($carrier_name).' - '.$tracking_number ); 
					}
				}

	
				
			} elseif ( $type == 'request' ) {
				
				$ticket_id = (int)$item_id_no_prefix;
				do_action('wpppatt_after_modify_request_shipping_tracking',  $ticket_id, strtoupper($carrier_name).' - '.$tracking_number ); 
				
			}	
		}
		return true;
	}


	// For testing. Must comment out return true for the section in question
	$output = array(
		'data' => $data,
		'validated' => $validated,
		'type' => $type,
		'item_id_no_prefix' => $item_id_no_prefix,
		'recall_obj' => $recall_obj,
		'carrier name' => $carrier_name,
		'tracking_number' => $tracking_number,
		'return ticket_id_array' => $ret_ticket_id_array,
		'return_obj' => $return_obj,
		'request ticket_id' => $ticket_id
	);
	
	header("Content-Type: application/json");
	//echo json_encode($output); //When uncommented, output appears in console, but modal removes number.

}

if($method == "DELETE") {
	parse_str(file_get_contents("php://input"), $_DELETE);
	$query = "DELETE FROM wpqa_wpsc_epa_shipping_tracking WHERE id = '".$_DELETE["id"]."'";
	$statement = $connect->prepare($query);
	$statement->execute();
	do_action('wpppatt_after_remove_request_shipping_tracking', $_GET['ticket_id'], $_DELETE["tracking_number"]);
}

?>