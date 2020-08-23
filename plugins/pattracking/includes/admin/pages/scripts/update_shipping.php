<?php

global $wpdb, $current_user, $wpscfunction;

$path = preg_replace('/wp-content.*$/','',__DIR__);
include($path.'wp-load.php');

if(
!empty($_POST['postvartype'])
){

$type = $_POST['postvartype'];
$dbid_string = $_POST['postvarsdbid'];
$dbid_arr = explode (",", $dbid_string);

$table_name = 'wpqa_wpsc_epa_shipping_tracking';

$ticket_arr = array();

foreach($dbid_arr as $key) {

$get_tn = $wpdb->get_row("SELECT tracking_number, ticket_id FROM wpqa_wpsc_epa_shipping_tracking WHERE id = '".$key."'");
$get_tn_val = $get_tn->tracking_number;
$get_ticket_id_val = $get_tn->ticket_id;

if ($get_ticket_id_val != '-99999') {
array_push($ticket_arr, $get_ticket_id_val);
}

$get_shipped = $wpdb->get_row("SELECT shipped FROM wpqa_wpsc_epa_shipping_tracking WHERE id = '".$key."'");
$get_shipped_val = $get_shipped->shipped;

$get_delievered = $wpdb->get_row("SELECT delivered FROM wpqa_wpsc_epa_shipping_tracking WHERE id = '".$key."'");
$get_delievered_val = $get_delievered->delivered;

if (($type == 1) && ($get_delievered_val != 1)){

if ($get_shipped_val == 1) {
$data_update = array('shipped' => 0);
echo "<strong>Tracking # " . $get_tn_val . "</strong> - Shipped flag removed.<br />";
} else {
$data_update = array('shipped' => 1);
echo "<strong>Tracking # " . $get_tn_val . "</strong> - Shipped flag updated.<br />";
}
$data_where = array('id' => $key);
$wpdb->update($table_name , $data_update, $data_where);

} 

if (($type == 1) && ($get_delievered_val == 1)) {
echo "<strong>Tracking # " . $get_tn_val . "</strong> - Shipped flag cannot be updated. Already marked as received.<br />";
}

if (($type == 2) && ($get_shipped_val == 1)){

if ($get_delievered_val == 1) {
$data_update = array('delivered' => 0);
echo "<strong>Tracking # " . $get_tn_val . "</strong> - Received flag removed.<br />";
} else {
$data_update = array('delivered' => 1);
echo "<strong>Tracking # " . $get_tn_val . "</strong> - Received flag updated.<br />";
}

$data_where = array('id' => $key);
$wpdb->update($table_name , $data_update, $data_where);
}

if (($type == 2) && ($get_shipped_val != 1)) {
echo "<strong>Tracking # " . $get_tn_val . "</strong> - Received flag cannot be updated. Item has not been marked as shipped.<br />";
}

}

if (count($ticket_arr) > 0) {
    echo '<hr>';
}
// Update Status

foreach($ticket_arr as $key) {

// Update status for shipped
$ticket_data = $wpscfunction->get_ticket($key);
$status_id   	= $ticket_data['ticket_status'];
$shipped_array = array();

$num = $key;
$str_length = 7;
$padded_request_id = substr("000000{$num}", -$str_length);

$get_shipped_status = $wpdb->get_results(
 	"SELECT shipped
 FROM wpqa_wpsc_epa_shipping_tracking
 WHERE ticket_id <> '-99999' AND ticket_id = " . $key
 );

foreach ($get_shipped_status as $shipped) {
	array_push($shipped_array, $shipped->shipped);
	}

if (($status_id == 4) && (!in_array(0, $shipped_array))) {
$wpscfunction->change_status($key, 5);
echo "<strong>Ticket # " . $padded_request_id . "</strong> - Shipping status updated.<br />";
}


// Update status for delivered
$delivered_array = array();

$get_delivered_status = $wpdb->get_results(
 	"SELECT delivered
 FROM wpqa_wpsc_epa_shipping_tracking
 WHERE ticket_id <> '-99999' AND ticket_id = " . $key
 );

foreach ($get_delivered_status as $delivered) {
	array_push($delivered_array, $delivered->delivered);
	}

if (($status_id == 5) && (!in_array(0, $delivered_array))) {
$wpscfunction->change_status($key, 63);
echo "<strong>Ticket # " . $padded_request_id . "</strong> - Received status updated.<br />";
}

if (($status_id == 63) && (!in_array(0, $shipped_array)) && (!in_array(1, $delivered_array))) {
$wpscfunction->change_status($key, 5);
echo "<strong>Ticket # " . $padded_request_id . "</strong> - Status Reset to Shipped.<br />";
}

if (($status_id == 5) && (!in_array(1, $shipped_array)) && (!in_array(1, $delivered_array))) {
$wpscfunction->change_status($key, 4);
echo "<strong>Ticket # " . $padded_request_id . "</strong> - Status Reset.<br />";
}

}

} else {
   echo "Please select one or more items to change shipping status.";
}
?>