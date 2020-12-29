<?php

global $wpdb, $current_user, $wpscfunction;

$path = preg_replace('/wp-content.*$/','',__DIR__);
include($path.'wp-load.php');

$bulk_action = $_POST['postvaraction'];
$selection = $_POST['postvarselection'];

$selection_arr = explode (",", $selection); 

if(isset($bulk_action) && isset($selection) && ($bulk_action != '')){

$table_pm_users = 'wpqa_pm_users';

if($bulk_action == 'read') {

foreach($selection_arr as $key) {
$pm_users_data_update = array('viewed' => 1);
$pm_users_data_where = array('pm_id' => $key);
$wpdb->update($table_pm_users , $pm_users_data_update, $pm_users_data_where);
}
echo "The selected messages have been marked as read.";
}

if($bulk_action == 'delete') {

foreach($selection_arr as $key) {
$pm_users_data_update = array('deleted' => 2);
$pm_users_data_where = array('pm_id' => $key);
$wpdb->update($table_pm_users , $pm_users_data_update, $pm_users_data_where);
}
echo "The selected messages have been deleted.";
}

} else {
echo "Please make a bulk action selection.";
}

?>
