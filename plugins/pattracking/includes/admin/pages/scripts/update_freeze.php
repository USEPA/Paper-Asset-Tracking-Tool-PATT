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

$destroyed = 0;
$unathorized_destroy = 0;
$freeze_reversal = 0;
$freeze_approval = 0;

foreach($folderdocid_arr as $key) {
$get_destroyed = $wpdb->get_row("SELECT b.box_destroyed as box_destroyed FROM wpqa_wpsc_epa_folderdocinfo a LEFT JOIN wpqa_wpsc_epa_boxinfo b ON a.box_id = b.id WHERE a.freeze = 0 AND a.folderdocinfo_id = '".$key."'");
$get_destroyed_val = $get_destroyed->box_destroyed;

if ($get_destroyed_val == 1) {
$destroyed++;
}
}

foreach($folderdocid_arr as $key) {
$get_unathorized_destroy = $wpdb->get_row("SELECT unauthorized_destruction FROM wpqa_wpsc_epa_folderdocinfo WHERE folderdocinfo_id = '".$key."'");
$get_unathorized_destroy_val = $get_unathorized_destroy->unauthorized_destruction;

if ($get_unathorized_destroy_val == 1) {
$unathorized_destroy++;
}
}


foreach($folderdocid_arr as $key) {
$get_freeze_approval = $wpdb->get_row("SELECT c.freeze_approval as freeze_approval FROM wpqa_wpsc_epa_folderdocinfo a LEFT JOIN wpqa_wpsc_epa_boxinfo b ON a.box_id = b.id LEFT JOIN wpqa_wpsc_ticket c ON b.ticket_id = c.id WHERE a.folderdocinfo_id = '".$key."'");
$freeze_approval_val = $get_freeze_approval->freeze_approval;

if ($freeze_approval_val == 1) {
$freeze_approval++;
}
}

$folderdocid_arr_count = count($folderdocid_arr);


if(($page_id == 'boxdetails' || $page_id == 'folderfile') && $destroyed == 0 && $unathorized_destroy == 0 && $freeze_approval == $folderdocid_arr_count) {
foreach($folderdocid_arr as $key) {    
$get_freeze = $wpdb->get_row("SELECT freeze FROM wpqa_wpsc_epa_folderdocinfo WHERE folderdocinfo_id = '".$key."'");
$get_freeze_val = $get_freeze->freeze;

$get_request_id = substr($key, 0, 7);
$get_ticket_id = $wpdb->get_row("SELECT id FROM wpqa_wpsc_ticket WHERE request_id = '".$get_request_id."'");
$ticket_id = $get_ticket_id->id;

if ($get_freeze_val == 1){
$freeze_reversal = 1;
$data_update = array('freeze' => 0);
$data_where = array('folderdocinfo_id' => $key);
$wpdb->update($table_name , $data_update, $data_where);
do_action('wpppatt_after_freeze_unflag', $ticket_id, $key);
}

if ($get_freeze_val == 0){
$data_update = array('freeze' => 1);
$data_where = array('folderdocinfo_id' => $key);
$wpdb->update($table_name , $data_update, $data_where);
do_action('wpppatt_after_freeze', $ticket_id, $key);
}

}

} elseif($destroyed > 0) {
echo "A destroyed folder/file has been selected and cannot be frozen.<br />Please unselect the destroyed folder/file.";
} elseif($unathorized_destroy > 0) {
echo "A folder/file flagged as unauthorized destruction has been selected and cannot be frozen.<br />Please unselect the folder/file flagged as unauthorized destruction folder/file.";
} elseif($freeze_approval == 0) {
echo "A folder/file flagged does not contain a litigation approval letter.<br />Please unselect the folder/file flagged as not containing a litigiation approval letter.";
}

if($page_id == 'filedetails') {

$get_freeze = $wpdb->get_row("SELECT freeze FROM wpqa_wpsc_epa_folderdocinfo WHERE folderdocinfo_id = '".$folderdocid_string."'");

$get_freeze_val = $get_freeze->freeze;

$get_request_id = substr($folderdocid_string, 0, 7);
$get_ticket_id = $wpdb->get_row("SELECT id FROM wpqa_wpsc_ticket WHERE request_id = '".$get_request_id."'");
$ticket_id = $get_ticket_id->id;

if ($get_freeze_val == 1){
$freeze_reversal = 1;
$data_update = array('freeze' => 0);
$data_where = array('folderdocinfo_id' => $folderdocid_string);
$wpdb->update($table_name , $data_update, $data_where);
do_action('wpppatt_after_freeze_unflag', $ticket_id, $folderdocid_string);
}

if ($get_freeze_val == 0){
$data_update = array('freeze' => 1);
$data_where = array('folderdocinfo_id' => $folderdocid_string);
$wpdb->update($table_name , $data_update, $data_where);
do_action('wpppatt_after_freeze', $ticket_id, $folderdocid_string);
}

}

if ($freeze_reversal == 1 && $destroyed == 0 && $unathorized_destroy == 0 && $freeze_approval == $folderdocid_arr_count) {
echo "Freeze has been updated. A Freeze flag has been reversed.";
} elseif ($freeze_reversal == 0 && $destroyed == 0 && $unathorized_destroy == 0 && $freeze_approval == $folderdocid_arr_count) {
echo "Freeze has been updated";
}

} else {
   echo "Please select one or more items to mark as frozen.";
}
?>