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
$destroyed = 0;
$rescan = 0;
$unathorized_destroy = 0;

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
$get_rescan = $wpdb->get_row("SELECT rescan FROM wpqa_wpsc_epa_folderdocinfo WHERE folderdocinfo_id = '".$key."'");
$get_rescan_val = $get_rescan->rescan;

if ($get_rescan_val == 1) {
$rescan++;
}
}

if($page_id == 'folderfile' && $destroyed == 0 && $unathorized_destroy == 0 && $rescan == 0) {

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

if ($validation_reversal == 1 && $destroyed == 0) {
echo "<strong>".$key."</strong> : Validation has been updated. A validation has been reversed<br />";
} elseif ($validation_reversal == 0 && $destroyed == 0) {
echo "<strong>".$key."</strong> : Validation has been updated<br />";
}

}
    
} elseif($destroyed > 0) {
echo "A destroyed folder/file has been selected and cannot be validated.<br />Please unselect the destroyed folder/file.";
} elseif($unathorized_destroy > 0) {
echo "A folder/file flagged as unauthorized destruction has been selected and cannot be validated.<br />Please unselect the folder/file flagged as unauthorized destruction folder/file.";
} elseif($rescan > 0) {
echo "A folder/file has been selected that has been flagged as requiring a re-scan.<br />Please unselect the folder/file flagged as re-scan before validating.";
}

if($page_id == 'filedetails') {
 
$get_validation = $wpdb->get_row("SELECT validation FROM wpqa_wpsc_epa_folderdocinfo WHERE folderdocinfo_id = '".$folderdocid_string."'");
$get_validation_val = $get_validation->validation;

$get_request_id = substr($folderdocid_string, 0, 7);
$get_ticket_id = $wpdb->get_row("SELECT id FROM wpqa_wpsc_ticket WHERE request_id = '".$get_request_id."'");
$ticket_id = $get_ticket_id->id;

$get_rescan = $wpdb->get_row("SELECT rescan FROM wpqa_wpsc_epa_folderdocinfo WHERE folderdocinfo_id = '".$folderdocid_string."'");
$get_rescan_val = $get_rescan->rescan;

if ($get_validation_val == 1 && $get_rescan_val == 0){
$validation_reversal = 1;
$data_update = array('validation' => 0, 'validation_user_id'=>'');
$data_where = array('folderdocinfo_id' => $folderdocid_string);
$wpdb->update($table_name , $data_update, $data_where);
do_action('wpppatt_after_invalidate_document', $ticket_id, $folderdocid_string);
}

if ($get_validation_val == 0 && $get_rescan_val == 0){
$data_update = array('validation' => 1, 'validation_user_id'=>$get_userid);
$data_where = array('folderdocinfo_id' => $folderdocid_string);
$wpdb->update($table_name , $data_update, $data_where);
do_action('wpppatt_after_validate_document', $ticket_id, $folderdocid_string);
}



if ($get_rescan_val == 1 && $destroyed == 0 && $unathorized_destroy == 0) {
echo "You must unflag document from re-scanning before validating";
} elseif ($validation_reversal == 1 && $destroyed == 0 && $unathorized_destroy == 0) {
//print_r($folderdocid_arr);
echo "Validation has been updated. A validation has been reversed";
} elseif ($validation_reversal == 0 && $destroyed == 0 && $unathorized_destroy == 0) {
echo "Validation has been updated";
}

}

} else {
   echo "Please select one or more items to validate.";
}
?>