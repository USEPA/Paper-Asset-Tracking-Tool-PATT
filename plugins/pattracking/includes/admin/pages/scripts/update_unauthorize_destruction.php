<?php

global $wpdb, $current_user, $wpscfunction;

$path = preg_replace('/wp-content.*$/','',__DIR__);
include($path.'wp-load.php');

if(
!empty($_POST['postvarsfolderdocid'])
){


$folderdocid_string = $_POST['postvarsfolderdocid'];
$folderdocid_arr = explode (",", $folderdocid_string);  
$page_id = $_POST['postvarpage'];
$box_id = $_POST['boxid'];

$table_name = 'wpqa_wpsc_epa_folderdocinfo';

$destruction_reversal = 0;
$destruction_violation = 0;
// Determine if violation occured

$return_array = array();
$recall_array = array();
foreach($folderdocid_arr as $key) {    

$getfolderdocinfo_db_id = $wpdb->get_row(
"SELECT 
id
FROM wpqa_wpsc_epa_folderdocinfo
WHERE folderdocinfo_id = '" . $key . "'"
			);

$folderdocinfo_db_id = $getfolderdocinfo_db_id->id;

$getrecall_violation_count = $wpdb->get_row(
"SELECT IF( EXISTS( SELECT * FROM wpqa_wpsc_epa_recallrequest WHERE folderdoc_id = '" . $folderdocinfo_db_id . "'), 1, 0) as count"
			);

$recall_violation_count = $getrecall_violation_count->count;

if($recall_violation_count == 1) {
array_push($recall_array,$folderdocinfo_db_id);
}

$getreturn_violation_count = $wpdb->get_row(
"SELECT IF( EXISTS( SELECT * FROM wpqa_wpsc_epa_return WHERE folderdoc_id = '" . $folderdocinfo_db_id . "'), 1, 0) as count"
			);

$return_violation_count = $getreturn_violation_count->count;

if($return_violation_count == 1) {
array_push($return_array,$folderdocinfo_db_id);
}

if ($recall_violation_count > 0 || $return_violation_count > 0) {
$destruction_violation = 1;
}

}

//echo $destruction_violation;

if($page_id == 'boxdetails' || $page_id == 'folderfile') {
foreach($folderdocid_arr as $key) {    
$get_destruction = $wpdb->get_row("SELECT unauthorized_destruction FROM wpqa_wpsc_epa_folderdocinfo WHERE folderdocinfo_id = '".$key."'");
$get_destruction_val = $get_destruction->unauthorized_destruction;

$get_request_id = substr($key, 0, 7);
$get_ticket_id = $wpdb->get_row("SELECT id FROM wpqa_wpsc_ticket WHERE request_id = '".$get_request_id."'");
$ticket_id = $get_ticket_id->id;

$get_box_id = $wpdb->get_row("
SELECT box_id FROM wpqa_wpsc_epa_folderdocinfo 
WHERE folderdocinfo_id = '" . $key . "'
");
$box_id = $get_box_id->box_id;

$get_storage_id = $wpdb->get_row("
SELECT id, storage_location_id FROM wpqa_wpsc_epa_boxinfo 
WHERE id = '" . $box_id . "'
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
AND a.id = '" . $box_id . "'"
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

$folder_file_count = $wpdb->get_row(
"SELECT 
count(id) as sum
FROM wpqa_wpsc_epa_folderdocinfo
WHERE box_id = '" . $box_id . "'"
			);

$folder_file_count_sum = $folder_file_count->sum;

$destruction_count = $wpdb->get_row(
"SELECT 
count(id) as sum
FROM wpqa_wpsc_epa_folderdocinfo
WHERE unauthorized_destruction = 1 AND box_id = '" . $box_id . "'"
			);

$destruction_count_sum = $destruction_count->sum;

if ($get_destruction_val == 1 && $destruction_violation == 0){
$destruction_reversal = 1;

if($folder_file_count_sum == $destruction_count_sum) {
$data_update = array('unauthorized_destruction' => 0, 'location_status_id' => '-99999');
$data_where = array('folderdocinfo_id' => $key);
$wpdb->update($table_name , $data_update, $data_where);
} else {
$data_update = array('unauthorized_destruction' => 0);
$data_where = array('folderdocinfo_id' => $key);
$wpdb->update($table_name , $data_update, $data_where);
}

do_action('wpppatt_after_unauthorized_destruction_unflag', $ticket_id, $key);
}

if ($get_destruction_val == 0 && $destruction_violation == 0){
$data_update = array('unauthorized_destruction' => 1);
$data_where = array('folderdocinfo_id' => $key);
$wpdb->update($table_name , $data_update, $data_where);

if($folder_file_count_sum == $destruction_count_sum) {
//SET PHYSICAL LOCATION TO DESTROYED
$table_pl = 'wpqa_wpsc_epa_boxinfo';
$pl_update = array('location_status_id' => '6','box_destroyed' => '1');
$pl_where = array('id' => $box_id);
$wpdb->update($table_pl , $pl_update, $pl_where);

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

}
do_action('wpppatt_after_unauthorized_destruction', $ticket_id, $key);

}

}
}

if($page_id == 'filedetails') {

$get_destruction = $wpdb->get_row("SELECT unauthorized_destruction FROM wpqa_wpsc_epa_folderdocinfo WHERE folderdocinfo_id = '".$folderdocid_string."'");

$get_destruction_val = $get_destruction->unauthorized_destruction;

$get_request_id = substr($folderdocid_string, 0, 7);
$get_ticket_id = $wpdb->get_row("SELECT id FROM wpqa_wpsc_ticket WHERE request_id = '".$get_request_id."'");
$ticket_id = $get_ticket_id->id;

$folder_file_count = $wpdb->get_row(
"SELECT 
count(id) as sum
FROM wpqa_wpsc_epa_folderdocinfo
WHERE box_id = '" . $box_id . "'"
			);

$folder_file_count_sum = $folder_file_count->sum;

$destruction_count = $wpdb->get_row(
"SELECT 
count(id) as sum
FROM wpqa_wpsc_epa_folderdocinfo
WHERE unauthorized_destruction = 1 AND box_id = '" . $box_id . "'"
			);

$destruction_count_sum = $destruction_count->sum;

if ($get_destruction_val == 1 && $destruction_violation == 0){
$destruction_reversal = 1;

$data_update = array('unauthorized_destruction' => 0);
$data_where = array('folderdocinfo_id' => $folderdocid_string);
$wpdb->update($table_name , $data_update, $data_where);


do_action('wpppatt_after_unauthorized_destruction_unflag', $ticket_id, $folderdocid_string);
}

if ($get_destruction_val == 0 && $destruction_violation == 0){
$data_update = array('unauthorized_destruction' => 1);
$data_where = array('folderdocinfo_id' => $folderdocid_string);
$wpdb->update($table_name , $data_update, $data_where);

if($folder_file_count_sum == $destruction_count_sum) {
//SET PHYSICAL LOCATION TO DESTROYED
$table_pl = 'wpqa_wpsc_epa_boxinfo';
$pl_update = array('location_status_id' => '6','box_destroyed' => '1');
$pl_where = array('id' => $box_id);
$wpdb->update($table_pl , $pl_update, $pl_where);

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

}
do_action('wpppatt_after_unauthorized_destruction', $ticket_id, $key);
}

}

$get_destruction_sum = $wpdb->get_row("SELECT sum(unauthorized_destruction) as sum FROM wpqa_wpsc_epa_folderdocinfo WHERE box_id = '".$box_id."'");

$get_destruction_sum_val = $get_destruction_sum->sum;


if ($page_id == 'boxdetails') {
if ($get_destruction_sum_val > 0) {

if ($destruction_violation == 1) {
    //print_r($recall_array);
    //print_r($return_array);
echo "A violation has occured and a folder/file you selected cannot be set to unauthorized destruction due to a return/recall.". PHP_EOL ."Please check your selection.";
} else {

if ($destruction_reversal == 1 && $destruction_violation == 0) {
    //print_r($recall_array);
    //print_r($return_array);
echo "Unauthorized destruction has been updated. A unauthorized destruction has been reversed.";
} else {
    //print_r($recall_array);
    //print_r($return_array);
echo "Unauthorized destruction has been updated";
}
}

} else {
    //print_r($recall_array);
    //print_r($return_array);
echo "All unauthorized destruction flags removed";
}
}

if ($page_id == 'filedetails' || $page_id == 'folderfile') {
if ($destruction_violation == 1) {
    //print_r($recall_array);
    //print_r($return_array);
echo "A violation has occured and a folder/file you selected cannot be set to unauthorized destruction due to a return/recall.". PHP_EOL ."Please check your selection.";
} else {

if ($destruction_reversal == 1 && $destruction_violation == 0) {
    //print_r($recall_array);
    //print_r($return_array);
echo "Unauthorized destruction has been updated. A unauthorized destruction has been reversed.";
} else {

echo "Unauthorized destruction has been updated";
}
}
}

} else {
   echo "Please select one or more items to mark as unauthorized destruction.";
}
?>