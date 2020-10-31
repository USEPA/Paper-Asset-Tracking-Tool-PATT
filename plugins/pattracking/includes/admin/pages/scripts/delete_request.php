<?php

global $wpdb, $current_user, $wpscfunction;

$path = preg_replace('/wp-content.*$/','',__DIR__);
include($path.'wp-load.php');

if(
!empty($_POST['postvarsrequest_id'])
){

$requestid_string = $_POST['postvarsrequest_id'];
$requestid_arr = explode (",", $requestid_string);  

//print_r($requestid_arr);


foreach($requestid_arr as $key) {
$get_ticket_status = $wpdb->get_row("SELECT request_id, ticket_status FROM wpqa_wpsc_ticket WHERE id = '".$key."'");
$get_ticket_requestid_val = $get_ticket_status->request_id;
$get_ticket_status_val = $get_ticket_status->ticket_status;

$ticket_status = 0;
$box_status = 0;

if($get_ticket_status_val == 69){
   $ticket_status = 1; 
}

$get_box_status = $wpdb->get_results("SELECT box_status FROM wpqa_wpsc_epa_boxinfo WHERE ticket_id = '".$key."'");

$get_box_status_array = array();

foreach ($get_box_status as $boxstatus) {
	array_push($get_box_status_array, $boxstatus->box_status);
	}

if (array_unique($get_box_status_array) === array('67')) { 
    $box_status = 1;
}

$table_name = 'wpqa_wpsc_ticket';

if($ticket_status == 1 || $box_status == 1){
$data_update = array('active' => 0);
$data_where = array('id' => $key);
$wpdb->update($table_name , $data_update, $data_where);
do_action('wpsc_after_recycle', $key);

echo '<strong>'.$get_ticket_requestid_val.'</strong> - Has been moved to the recycle bin.<br />';

//sends email/notification to admins/managers when request is deleted
$agent_admin_group_name = 'Administrator';
$pattagentid_admin_array = Patt_Custom_Func::agent_from_group($agent_admin_group_name);
$agent_manager_group_name = 'Manager';
$pattagentid_manager_array = Patt_Custom_Func::agent_from_group($agent_manager_group_name);
$pattagentid_array = array_merge($pattagentid_admin_array,$pattagentid_manager_array);
$data = [];

//disabled email notification
$email = 1;

//insert notification for each request
Patt_Custom_Func::insert_new_notification('email-request-deleted',$pattagentid_array,$get_ticket_requestid_val,$data,$email);

//BEGIN TESTING ONLY REMOVE
$get_ticket_tmp_contact = $wpdb->get_row("SELECT tmp_contact FROM wpqa_wpsc_ticket WHERE id = '".$key."'");

$tmp_contact = $get_ticket_tmp_contact->tmp_contact;

$test_agent_id = Patt_Custom_Func::convert_db_contact_email($tmp_contact);

if ($tmp_contact != '' && $test_agent_id != 'error') {

$email = 1;
$data = [];
$requestid = Patt_Custom_Func::convert_request_db_id($key);

Patt_Custom_Func::insert_new_notification('email-request-deleted',$test_agent_id,$requestid,$data,$email);

}
//END TESTING ONLY REMOVE

} else {
    echo '<strong>'.$get_ticket_requestid_val.'</strong> - Request must either be cancelled or all boxes under the request must be in the dispositioned status.<br />';
}

}


} else {
   echo "Please select one or more items to delete.";
}
?>