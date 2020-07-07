<?php

global $wpdb, $current_user, $wpscfunction;

$path = preg_replace('/wp-content.*$/','',__DIR__);
include($path.'wp-load.php');

if(
!empty($_POST['postvarsfolderdocid'])
){


$folderdocid_string = $_POST['postvarsfolderdocid'];
$get_userid = $_POST['postvarsuserid'];
$folderdocid_arr = explode (",", $folderdocid_string);  
$page_id = $_POST['postvarpage'];

$table_name = 'wpqa_wpsc_epa_folderdocinfo';

$validation_reversal = 0;


if($page_id == 'boxdetails' || $page_id == 'folderfile') {
foreach($folderdocid_arr as $key) {    
$get_validation = $wpdb->get_row("SELECT validation FROM wpqa_wpsc_epa_folderdocinfo WHERE folderdocinfo_id = '".$key."'");
$get_validation_val = $get_validation->validation;

$get_request_id = substr($key, 0, 7);
$get_ticket_id = $wpdb->get_row("SELECT id FROM wpqa_wpsc_ticket WHERE request_id = '".$get_request_id."'");
$ticket_id = $get_ticket_id->id;

if ($get_validation_val == 1){
$validation_reversal = 1;
$data_update = array('validation' => 0, 'validation_user_id'=>'');
$data_where = array('folderdocinfo_id' => $key);
$wpdb->update($table_name , $data_update, $data_where);
do_action('wpppatt_after_invalidate_document', $ticket_id, $key);
}

if ($get_validation_val == 0){
$data_update = array('validation' => 1, 'validation_user_id'=>$get_userid);
$data_where = array('folderdocinfo_id' => $key);
$wpdb->update($table_name , $data_update, $data_where);
do_action('wpppatt_after_validate_document', $ticket_id, $key);
}

//echo $get_validation_val;
}
}

if($page_id == 'filedetails') {
 
$get_validation = $wpdb->get_row("SELECT validation FROM wpqa_wpsc_epa_folderdocinfo WHERE folderdocinfo_id = '".$folderdocid_string."'");
$get_validation_val = $get_validation->validation;

$get_request_id = substr($folderdocid_string, 0, 7);
$get_ticket_id = $wpdb->get_row("SELECT id FROM wpqa_wpsc_ticket WHERE request_id = '".$get_request_id."'");
$ticket_id = $get_ticket_id->id;

if ($get_validation_val == 1){
$validation_reversal = 1;
$data_update = array('validation' => 0, 'validation_user_id'=>'');
$data_where = array('folderdocinfo_id' => $folderdocid_string);
$wpdb->update($table_name , $data_update, $data_where);
do_action('wpppatt_after_invalidate_document', $ticket_id, $folderdocid_string);
}

if ($get_validation_val == 0){
$data_update = array('validation' => 1, 'validation_user_id'=>$get_userid);
$data_where = array('folderdocinfo_id' => $folderdocid_string);
$wpdb->update($table_name , $data_update, $data_where);
do_action('wpppatt_after_validate_document', $ticket_id, $folderdocid_string);
}

}

if ($validation_reversal == 1) {
//print_r($folderdocid_arr);
echo "Validation has been updated. A validation has been reversed.";
} else {
echo "Validation has been updated";
}


} else {
   echo "Please select one or more items to validate.";
}
?>