<?php

global $wpdb, $current_user, $wpscfunction;

$path = preg_replace('/wp-content.*$/','',__DIR__);
include($path.'wp-load.php');

if(
!empty($_POST['postvarsmessageid'])
){
$table_pm_users = 'wpqa_pm_users';
$pm_users_data_update = array('viewed' => 1);
$pm_users_data_where = array('pm_id' => $_POST['postvarsmessageid']);
$wpdb->update($table_pm_users , $pm_users_data_update, $pm_users_data_where);

}

?>
