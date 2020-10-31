<?php

global $wpdb, $current_user, $wpscfunction;

$path = preg_replace('/wp-content.*$/','',__DIR__);
include($path.'wp-load.php');

if(isset($_POST['postvarsboxidname'])){
   $box_id = $_POST['postvarsboxidname'];
   $dc = $_POST['postvarsdc'];

   $box_details = $wpdb->get_row(
"SELECT 
wpqa_wpsc_epa_boxinfo.storage_location_id as storage_location_id, 
wpqa_wpsc_epa_boxinfo.id as id, 
wpqa_wpsc_epa_boxinfo.box_id as box_id, 
wpqa_wpsc_epa_storage_location.digitization_center as digitization_center,
wpqa_terms.name as dc_name,
wpqa_wpsc_epa_storage_location.aisle as aisle,
wpqa_wpsc_epa_storage_location.bay as bay,
wpqa_wpsc_epa_storage_location.shelf as shelf,
wpqa_wpsc_epa_storage_location.shelf as position
FROM wpqa_wpsc_epa_boxinfo
INNER JOIN wpqa_wpsc_epa_storage_location ON wpqa_wpsc_epa_boxinfo.storage_location_id = wpqa_wpsc_epa_storage_location.id
INNER JOIN wpqa_terms ON '".$dc."' = wpqa_terms.term_id
WHERE wpqa_wpsc_epa_boxinfo.id = '" . $box_id . "'"
			);
			

			$box_storage_location_id = $box_details->storage_location_id;
			$box_storage_digitization_center = $box_details->digitization_center;
			$box_storage_aisle = $box_details->aisle;
			$box_storage_bay = $box_details->bay;
			$box_storage_shelf = $box_details->shelf;
			$box_sotrage_shelf_id = $box_storage_aisle . '_' . $box_storage_bay . '_' . $box_storage_shelf;
			$box_id_val = $box_details->box_id;
			$box_dc_name = $box_details->dc_name;

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

$get_ticket_id = $wpdb->get_row("
SELECT ticket_id
FROM wpqa_wpsc_epa_boxinfo
WHERE
box_id = '" . $box_id_val . "'
");

$ticket_id = $get_ticket_id->ticket_id;

do_action('wpppatt_after_digitization_center', $ticket_id, $box_id_val, $box_dc_name);

echo "Box ID #: " . $box_id_val . " has been updated.\nAssigned Digitization Center: " .$box_dc_name;
   
} else {
 echo "Error updating location status table.";    
}

} else {
   echo "Update not successful.";
}
?>