<?php

global $wpdb, $current_user, $wpscfunction;

$path = preg_replace('/wp-content.*$/','',__DIR__);
include($path.'wp-load.php');

//$recall_id = isset($_POST['recall_id']) ? sanitize_text_field($_POST['recall_id']) : '';
$type = isset($_POST['type']) ? sanitize_text_field($_POST['type']) : '';
$return_reason = isset($_POST['return_reason']) ? sanitize_text_field($_POST['return_reason']) : '';
$return_ids = $_REQUEST['return_ids']; 
$shipping_tracking_number = isset($_POST['shipping_tracking_number']) ? sanitize_text_field($_POST['shipping_tracking_number']) : '';
$shipping_carrier = isset($_POST['shipping_carrier']) ? sanitize_text_field($_POST['shipping_carrier']) : '';
$comment = isset($_POST['comment']) ? sanitize_text_field($_POST['comment']) : '';
//$num_of_recalls = count($recall_ids);


if($type == 'boxes') {
	//$request_date = isset($_POST['new_date']) ? sanitize_text_field($_POST['new_date']) : '';

	echo PHP_EOL.'Item IDs: ';
	print_r($return_ids);
	echo PHP_EOL.'Type: '.$type.PHP_EOL;
	echo 'Return Reason: '.$return_reason.PHP_EOL;
	echo 'Shipping Tracking Number: '.$shipping_tracking_number.PHP_EOL;
	echo 'Carrier: '.$shipping_carrier.PHP_EOL;
	echo 'Comment: '.$comment.PHP_EOL;

	
} elseif ( $type == 'received_date' ) {
	$received_date = isset($_POST['new_date']) ? sanitize_text_field($_POST['new_date']) : '';

	echo 'Recall ID: '.$recall_id.PHP_EOL;
	echo 'Type: '.$type.PHP_EOL;
	echo $title.' Date: '.$received_date.PHP_EOL;	

	
} 




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