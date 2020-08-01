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

foreach($dbid_arr as $key) {

$get_tn = $wpdb->get_row("SELECT tracking_number FROM wpqa_wpsc_epa_shipping_tracking WHERE id = '".$key."'");
$get_tn_val = $get_tn->tracking_number;

if ($type == 1){
$get_shipped = $wpdb->get_row("SELECT shipped FROM wpqa_wpsc_epa_shipping_tracking WHERE id = '".$key."'");
$get_shipped_val = $get_shipped->shipped;

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

if ($type == 2){
$get_delievered = $wpdb->get_row("SELECT delivered FROM wpqa_wpsc_epa_shipping_tracking WHERE id = '".$key."'");
$get_delievered_val = $get_delievered->delivered;

if ($get_delievered_val == 1) {
$data_update = array('delivered' => 0);
echo "<strong>Tracking # " . $get_tn_val . "</strong> - Delivered flag removed.<br />";
} else {
$data_update = array('delivered' => 1);
echo "<strong>Tracking # " . $get_tn_val . "</strong> - Delivered flag updated.<br />";
}

$data_where = array('id' => $key);
$wpdb->update($table_name , $data_update, $data_where);
}

}

} else {
   echo "Please select one or more items to change shipping status.";
}
?>
