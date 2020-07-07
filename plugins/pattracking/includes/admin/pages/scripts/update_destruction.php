<?php

global $wpdb, $current_user, $wpscfunction;

$path = preg_replace('/wp-content.*$/','',__DIR__);
include($path.'wp-load.php');

if(
!empty($_POST['postvarsboxid'])
){


$boxid_string = $_POST['postvarsboxid'];
$boxid_arr = explode (",", $boxid_string);  
$page_id = $_POST['postvarpage'];

$box_table_name = 'wpqa_wpsc_epa_boxinfo';

$destruction_reversal = 0;

$counter = 0;

foreach($boxid_arr as $key) {    
$counter++;


$get_box_db_id = $wpdb->get_row("select id from wpqa_wpsc_epa_boxinfo where box_id = '".$key."'");
$box_db_id = $get_box_db_id->id;

$get_sum_total = $wpdb->get_row("select count(id) as sum_total_count from wpqa_wpsc_epa_folderdocinfo where box_id = '".$box_db_id."'");
$sum_total_val = $get_sum_total->sum_total_count;

$get_sum_validation = $wpdb->get_row("select sum(validation) as sum_validation from wpqa_wpsc_epa_folderdocinfo where validation = 1 AND box_id = '".$box_db_id."'");
$sum_validation = $get_sum_validation->sum_validation;

$get_status = $wpdb->get_row("select b.ticket_status as status from wpqa_wpsc_epa_boxinfo a INNER JOIN wpqa_wpsc_ticket b ON a.ticket_id = b.id where a.id = '".$box_db_id."'");
$request_status = $get_status->status;

$get_storage_id = $wpdb->get_row("
SELECT id, storage_location_id FROM wpqa_wpsc_epa_boxinfo 
WHERE box_id = '" . $key . "'
");
$storage_location_id = $get_storage_id->storage_location_id;

$box_details = $wpdb->get_row(
"SELECT 
b.digitization_center,
b.aisle,
b.bay,
b.shelf,
b.position
FROM wpqa_wpsc_epa_boxinfo a
INNER JOIN wpqa_wpsc_epa_storage_location b WHERE a.storage_location_id = b.id
AND a.box_id = '" . $key . "'"
			);
			
			$box_storage_digitization_center = $box_details->digitization_center;
			$box_storage_aisle = $box_details->aisle;
			$box_storage_bay = $box_details->bay;
			$box_storage_shelf = $box_details->shelf;
			$box_storage_shelf_id = $box_storage_aisle . '_' . $box_storage_bay . '_' . $box_storage_shelf;

$box_storage_status = $wpdb->get_row(
"SELECT 
occupied,
remaining
FROM wpqa_wpsc_epa_storage_status
WHERE shelf_id = '" . $box_storage_shelf_id . "'"
			);

$box_storage_status_occupied = $box_storage_status->occupied;
$box_storage_status_remaining = $box_storage_status->remaining;
$box_storage_status_remaining_added = $box_storage_status->remaining + 1;


if(($sum_total_val != $sum_validation) || ($request_status != 68)) {
    echo '<strong>'.$key.'</strong> : ';
    echo 'Please ensure all documents are validated and the request status is approved for destruction before destroying the box.';
    
if ($counter > 0) {
echo '<br />';
echo '<br />';
}

} else {
$get_destruction = $wpdb->get_row("SELECT box_destroyed FROM wpqa_wpsc_epa_boxinfo WHERE box_id = '".$key."'");
$get_destruction_val = $get_destruction->box_destroyed;

$get_request_id = substr($key, 0, 7);
$get_ticket_id = $wpdb->get_row("SELECT id FROM wpqa_wpsc_ticket WHERE request_id = '".$get_request_id."'");
$ticket_id = $get_ticket_id->id;

if ($get_destruction_val == 1){
$destruction_reversal = 1;
$box_data_update = array('box_destroyed' => 0, 'location_status_id' => '-99999');
$box_data_where = array('box_id' => $key);
$wpdb->update($box_table_name , $box_data_update, $box_data_where);

do_action('wpppatt_after_box_destruction_unflag', $ticket_id, $key);

}

if ($get_destruction_val == 0){
$box_data_update = array('box_destroyed' => 1);
$box_data_where = array('box_id' => $key);
$wpdb->update($box_table_name , $box_data_update, $box_data_where);

//SET PHYSICAL LOCATION TO DESTROYED
$pl_update = array('location_status_id' => '6');
$pl_where = array('box_id' => $key);
$wpdb->update($box_table_name , $pl_update, $pl_where);

//SET SHELF LOCATION TO 0
$table_sl = 'wpqa_wpsc_epa_storage_location';
$sl_update = array('digitization_center' => '666','aisle' => '0','bay' => '0','shelf' => '0','position' => '0');
$sl_where = array('id' => $storage_location_id);
$wpdb->update($table_sl , $sl_update, $sl_where);

//ADD AVALABILITY TO STORAGE STATUS
if ($box_storage_status_remaining <= 4) {
$table_ss = 'wpqa_wpsc_epa_storage_status';
$ssr_update = array('remaining' => $box_storage_status_remaining_added);
$ssr_where = array('shelf_id' => $box_storage_shelf_id, 'digitization_center' => $box_storage_digitization_center);
$wpdb->update($table_ss , $ssr_update, $ssr_where);
}

if($box_storage_status_remaining == 4){
$sso_update = array('occupied' => 0);
$sso_where = array('shelf_id' => $box_storage_shelf_id, 'digitization_center' => $box_storage_digitization_center);
$wpdb->update($table_ss , $sso_update, $sso_where);
}

do_action('wpppatt_after_box_destruction', $ticket_id, $key);
}

if ($destruction_reversal == 1) {
echo '<strong>'.$key.'</strong> : ';
echo "Box destruction has been updated. A box destruction has been reversed.";
if ($counter > 0) {
echo '<br />';
echo '<br />';
}

} else {
echo '<strong>'.$key.'</strong> : ';
echo "Box destruction has been updated";
if ($counter > 0) {
echo '<br />';
echo '<br />';
}
}

}

}

} else {
   echo "Please select one or more boxes to mark for destruction.";
}
?>