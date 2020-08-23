<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
// set default filter for agents and customers //not true
global $current_user, $wpscfunction, $current_user;

if (!$current_user->ID) die();

$title = isset($_POST['title']) ? sanitize_text_field($_POST['title']) : '';
$wp_user_id = isset($_POST['wp_user_id']) ? sanitize_text_field($_POST['wp_user_id']) : '';
$agent_user_id = isset($_POST['agent_user_id']) ? sanitize_text_field($_POST['agent_user_id']) : '';
$return_reason = isset($_POST['return_reason']) ? sanitize_text_field($_POST['return_reason']) : '';
$comment = isset($_POST['comment']) ? sanitize_text_field($_POST['comment']) : '';

$shipping_tracking = isset($_POST['shipping_tracking']) ? sanitize_text_field($_POST['shipping_tracking']) : '';
$shipping_carrier = isset($_POST['shipping_carrier']) ? sanitize_text_field($_POST['shipping_carrier']) : '';

$item_ids = $_REQUEST['item_ids'];

// Create separate arrays of boxes & folderdocs for insertion
//$box_obj = Patt_Custom_Func::get_box_file_details_by_id('0000001-2');

$item_ids_box = array();
$item_ids_folderdoc = array();
foreach( $item_ids as $id ) {
	if( substr_count($id, '-') == 1 ) {
		$box_obj = Patt_Custom_Func::get_box_file_details_by_id($id);
		$item_ids_box[] = $box_obj->Box_id_FK;
// 		$item_ids_box[] = $id;
	} elseif( substr_count($id, '-') == 3 ) {
		$folderdoc_obj = Patt_Custom_Func::get_box_file_details_by_id($id);
		$item_ids_folderdoc[] = $folderdoc_obj->Folderdoc_Info_id_FK;
// 		$item_ids_folderdoc[] = $id;
	}
}

// Set arrays to null if no contents. 
if( count($item_ids_box) < 1 ) {
	$item_ids_box = null;
}
if( count($item_ids_folderdoc) < 1 ) {
	$item_ids_folderdoc = null;
}

// Get term ids for return reason
if( !taxonomy_exists('wppatt_return_reason') ) {

	$args = array(
		'public' => false,
		'rewrite' => false
	);
	register_taxonomy( 'wppatt_return_reason', 'wpsc_ticket', $args );
}

$reasons = get_terms([
	'taxonomy'   => 'wppatt_return_reason',
	'hide_empty' => false,
	'orderby'    => 'meta_value_num',
	'order'    	 => 'ASC',
	'meta_query' => array('order_clause' => array('key' => 'wppatt_return_reason_load_order')),
]);
$return_reason_id = null;
foreach($reasons as $reason) {
    if ($return_reason == $reason->name) {
        $return_reason_id = $reason->term_id;
        break;
    }
}

// Lookup Table for Return Reason wording & Term ID
/*
switch($return_reason) {
	case 'Damaged':
		
		break;
	case '':
		break;
}
*/

// Constants 
$db_null = -99999;
$date_null = '0000-00-00 00:00:00';

$current_datetime = date("yy-m-d H:i:s"); //("yy-m-d H:i:s"); //New "m-d-yy H:i:s"
$expiration_date = date_create($current_datetime);
date_add($expiration_date,date_interval_create_from_date_string("90 days"));
$expiration_date_string = date_format($expiration_date,"yy-m-d H:i:s");


// Data and Insertion
/*
$data = [
// 	'return_id' => "$return_id",
	'box_id' => $item_ids_box, 
// 	'box_id' => $box_obj, 
	'folderdoc_id' => $item_ids_folderdoc,
	'shipping_tracking_id' => $shipping_tracking,
	'shipping_carrier' => $shipping_carrier,	
	'user_id' => $wp_user_id, //[2,5,67,5]
	'return_reason_id' => $return_reason_id,
// 	'return_reason_id' => $reasons,
	'return_date' => $current_datetime,
	'return_receipt_date' => $date_null,
	'expiration_date' => $expiration_date_string,
	'comments' => $comment,
];
*/


$data = [
// 	'return_id' => "$return_id",
	'box_id' => $item_ids_box, 
// 	'box_id' => $box_obj, 
	'folderdoc_id' => $item_ids_folderdoc,
	'shipping_tracking_info' => [
    			'tracking_number' => $shipping_tracking, 
    			'company_name' => $shipping_carrier,
			 ],
// 	'shipping_tracking_id' => $shipping_tracking,
// 	'shipping_carrier' => $shipping_carrier,	
	'user_id' => $wp_user_id, //[2,5,67,5]
	'return_reason_id' => $return_reason_id,
// 	'return_reason_id' => $reasons,
	'return_date' => $current_datetime,
	'return_receipt_date' => $date_null,
	'expiration_date' => $expiration_date_string,
	'comments' => $comment,
];

/*
		$data = [
// 			'return_id' => "$return_id",
			'box_id' => ['1','2'],
			'folderdoc_id' => [2],
			'shipping_tracking_id' => 3,
			'user_id' => 2, //[2,5,67,5]
			'return_reason_id' => 5,
			'return_date' => '2020-06-02 00:00:00',
			'return_receipt_date' => '2020-06-02 00:00:00',
			'expiration_date' => '2020-06-02 00:00:00',
			'comments' => 'dfbdfbd',
		];
*/

$return_id = Patt_Custom_Func::insert_return_data($data);
$str_length = 7;
$return_id = substr("000000{$return_id}", -$str_length);

//$return_id = '0000044'; //fake return id

//$error_array = array();

if($return_id == 0) {
	$error_message = 'Return Not Submitted';
} else {
	$error_message = 'No Errors';
	$return_id_old = $return_id;
	$str_length = 7;
	$return_id = substr("000000{$return_id}", -$str_length);
	$return_id = 'RTN-'.$return_id;
	
	// Audit Log wpppatt_after_recall_created
	$where = ['return_id' => $return_id_old ];
	//$recall_array = Patt_Custom_Func::get_recall_data($where);
	


	// Audit Log for each item
	foreach( $item_ids as $id ) {
		if( substr_count($id, '-') == 1 ) {
			$return_type = 'Box';
			$arr = explode("-", $id, 2);
			$ticket_id = (int)$arr[0];
			$item_id = $id;
			
		} elseif( substr_count($id, '-') == 3 ) {
			$return_type = 'Folder/File';
			$arr = explode("-", $id, 2);
			$ticket_id = (int)$arr[0];
			$item_id = $id;

		}

		$item_name_and_id_str = $return_type .': '. $item_id.' due to: '.$return_reason;
		do_action('wpppatt_after_return_created', $ticket_id, $return_id, $item_name_and_id_str );		
	}
}


$output = array(
  'customer_name'   => 'Not Needed',
  'data' => $data,
  'date' => date_format($expiration_date,"yy-m-d H:i:s"),
  'return_id' => $return_id,
  'error'  => $error_message,
);
echo json_encode($output);
