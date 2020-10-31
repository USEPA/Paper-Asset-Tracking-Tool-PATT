<?php

global $wpdb, $current_user, $wpscfunction;

$path = preg_replace('/wp-content.*$/','',__DIR__);
include($path.'wp-load.php');

if(
!empty($_POST['postvarsreaderid'])
){
   $reader_id = $_POST['postvarsreaderid'];

$table_name = 'wpqa_wpsc_epa_rfid_data';

$wpdb->delete($table_name, array( 'Reader_Name' => $reader_id ) );
  
  
 echo "All entries with the following RFID Reader ID: " . $reader_id . " have been removed.";
 
} else {
   echo "No RFID Reader ID Passed.";
}
?>