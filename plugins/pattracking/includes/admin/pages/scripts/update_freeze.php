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

$freeze_reversal = 0;

if($page_id == 'boxdetails' || $page_id == 'folderfile') {
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

$get_freeze_sum = $wpdb->get_row("SELECT sum(freeze) as sum FROM wpqa_wpsc_epa_folderdocinfo WHERE box_id = '".$box_id."'");

$get_freeze_sum_val = $get_freeze_sum->sum;


if ($page_id == 'boxdetails') {
if ($get_freeze_sum_val > 0) {
    
if ($freeze_reversal == 1) {
echo "Freeze has been updated. A freeze has been reversed.";
} else {
echo "Freeze has been updated";
}

} else {
echo "All frozen flags removed";
}
}

if ($page_id == 'filedetails' || $page_id == 'folderfile') {
if ($freeze_reversal == 1) {
echo "Freeze has been updated. A freeze has been reversed.";
} else {
echo "Freeze has been updated";
}
}


} else {
   echo "Please select one or more items to mark as frozen.";
}
?>