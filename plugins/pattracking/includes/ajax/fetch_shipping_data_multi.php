<?php

$path = preg_replace('/wp-content.*$/','',__DIR__);
include($path.'wp-load.php');

$host = 'mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset='.DB_CHARSET;
$connect = new PDO($host, DB_USER, DB_PASSWORD);

$method = $_SERVER['REQUEST_METHOD'];

global $wpdb, $current_user, $wpscfunction;

if($method == 'GET')
{
	
// 	$new_recall_id_array = $_REQUEST['recall_ids'];
	$new_recall_id_json = $_GET['recall_ids'];
	$new_recall_id_array = str_getcsv($new_recall_id_json);
// 	$new_recall_id_array = 'nothing';
	
 $data = array(
  ':company_name'   => "%" . $_GET['company_name'] . "%",
  ':tracking_number'   => "%" . $_GET['tracking_number'] . "%",
  ':status'     => "%" . $_GET['status'] . "%",
  ':ticket_id'    => $_GET['ticket_id']
 );

 $query = 'SELECT * FROM wpqa_wpsc_epa_shipping_tracking WHERE company_name LIKE :company_name AND tracking_number LIKE :tracking_number AND status LIKE :status AND ticket_id = :ticket_id ORDER BY id DESC';

 $statement = $connect->prepare($query);
 $statement->execute($data);
 $result = $statement->fetchAll();
 
 //$result2 = '';
 $test = "";
foreach($new_recall_id_array as $item_id) {
	$test = $test." ".$item_id;
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
	);
}
 
 
 header("Content-Type: application/json");
 echo json_encode($output);
}

if($method == "POST")
{
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

if($method == 'PUT')
{
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
	
	//NEW - END
	
	// Update Recall State Machine
	if ( $recall_obj->recall_status_id == 729 && $recall_obj->tracking_number == '' && $recall_obj->company_name == '' ) {
		$data_status = [ 'recall_status_id' => 730 ]; //change status from Recalled to Shipped
		$obj = Patt_Custom_Func::update_recall_data( $data_status, $where );
	} elseif ( $recall_obj->recall_status_id == 731 && $recall_obj->tracking_number == '' && $recall_obj->company_name == '') {
		$data_status = [ 'recall_status_id' => 732 ]; //change status from Recalled to Shipped
		$obj = Patt_Custom_Func::update_recall_data( $data_status, $where );
	}
	
	
	// If Not in state Cancelled: Update shipping data
	if( $recall_obj->recall_status_id != 734 ) {
		$recall_array = Patt_Custom_Func::update_recall_shipping( $data, $where );
		
		//Update the Updated Date
		$current_datetime = date("yy-m-d H:i:s");
		$update = [	'updated_date' => $current_datetime ];
		$where = [ 'id' => $item_id ];
		Patt_Custom_Func::update_recall_dates($update, $where);
	}
	
	
	
	// Audit log
    do_action('wpppatt_after_recall_details_shipping',  $recall_obj->ticket_id, 'R-'.$recall_obj->recall_id, strtoupper($carrier_name).' - '.$tracking_number );  

}

if($method == "DELETE")
{
 parse_str(file_get_contents("php://input"), $_DELETE);
 $query = "DELETE FROM wpqa_wpsc_epa_shipping_tracking WHERE id = '".$_DELETE["id"]."'";
 $statement = $connect->prepare($query);
 $statement->execute();
  do_action('wpppatt_after_remove_request_shipping_tracking', $_GET['ticket_id'], $_DELETE["tracking_number"]);
}

?>