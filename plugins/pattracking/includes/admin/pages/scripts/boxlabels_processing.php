<?php

global $wpdb, $current_user, $wpscfunction;

$path = preg_replace('/wp-content.*$/','',__DIR__);
include($path.'wp-load.php');

if(isset($_POST['postvarsboxid'])){
    
$box_ids = $_POST['postvarsboxid'];

$box_arr = explode(",", $box_ids);

$box_count = count($box_arr);

$count = 0;

$boxidarray = array();

foreach($box_arr as $key => $value) { 
    
$get_destroy_status = $wpdb->get_row("
SELECT box_destroyed, box_id FROM wpqa_wpsc_epa_boxinfo 
WHERE box_id = '" . $value . "'
");
$destroy_status = $get_destroy_status->box_destroyed;
$box_id = $get_destroy_status->box_id;

if ($destroy_status == 0) {
array_push($boxidarray, $box_id);
}

if ($destroy_status == 1) {
$count++;
}

}

$boxidarray_val = implode(',', $boxidarray);

if ($box_count == $count) {
echo 'false'.'|'.$boxidarray_val;
}

if ($count < $box_count && $count != 0) {
echo 'warn'.'|'.$boxidarray_val;
}

if ($count < $box_count && $count == 0) {
echo 'true'.'|'.$boxidarray_val;
}

} else {
   echo "Update not successful.";
}
?>