<?php

global $wpdb, $current_user, $wpscfunction;

$path = preg_replace('/wp-content.*$/','',__DIR__);
include($path.'wp-load.php');

if(
!empty($_POST['postvarsrequest_id']) || !empty($_POST['postvarsemail'])
){


$postvarsrequest_id = $_POST['postvarsrequest_id'];
$postvarsemail = $_POST['postvarsemail'];

$get_ticket_tmp_contact = $wpdb->get_row("SELECT tmp_contact FROM wpqa_wpsc_ticket WHERE id = '".$postvarsrequest_id."'");
$tmp_contact = $get_ticket_tmp_contact->tmp_contact;

if($tmp_contact != $postvarsemail) {
$table_name = 'wpqa_wpsc_ticket';

$data_update = array('tmp_contact' => $postvarsemail);
$data_where = array('id' => $postvarsrequest_id);
$wpdb->update($table_name , $data_update, $data_where);


$test_agent_id = Patt_Custom_Func::convert_db_contact_email($postvarsemail);

if ($postvarsemail != '' && $test_agent_id != 'error') {

$email = 1;
$data = [];
$requestid = Patt_Custom_Func::convert_request_db_id($postvarsrequest_id);

Patt_Custom_Func::insert_new_notification('email-new-request-created-id',$test_agent_id,$requestid,$data,$email);

}

}

echo "Successfully updated email address for notification.";

} else {
   echo "Please enter an agent email address for notifications.";
}
?>