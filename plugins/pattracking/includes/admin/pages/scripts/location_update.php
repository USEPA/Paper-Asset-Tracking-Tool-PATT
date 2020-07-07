<?php

global $wpdb, $current_user, $wpscfunction;

$path = preg_replace('/wp-content.*$/','',__DIR__);
include($path.'wp-load.php');

if(isset($_POST['postvarspname']) && isset($_POST['postvaraname']) && isset($_POST['postvarbname']) && isset($_POST['postvarboxname']) && isset($_POST['postvarcentername'])){
   $shelf_position = $_POST['postvarspname'];
 
   $array = explode('_', $shelf_position);
   $shelf = $array[0];
   $position = $array[1];

   $aisle = $_POST['postvaraname'];
   $bay = $_POST['postvarbname'];
   $boxid = $_POST['postvarboxname'];
   $center = $_POST['postvarcentername'];
   $center_term_id = term_exists( $center ); 

   $center_value = '';
   
   if ($center == 62) {
     $center_value = 'E';
   } else if ($center == 2){
     $center_value = 'W';  
   } else if ($center == 663){
     $center_value = 'ECUI';    
   } else if ($center == 664){
     $center_value = 'WCUI';    
   }

			$box_details = $wpdb->get_row(
"SELECT storage_location_id
FROM wpqa_wpsc_epa_boxinfo
WHERE box_id = '" . $boxid . "'"
			);

			$box_storage_location_id = $box_details->storage_location_id;
			
// Update storage status add box back from existing shelf location

			$storage_location_details = $wpdb->get_row(
"SELECT aisle,bay,shelf,position
FROM wpqa_wpsc_epa_storage_location
WHERE id = '" . $box_storage_location_id . "'"
			);

			$existing_aisle = $storage_location_details->aisle;
			$existing_bay = $storage_location_details->bay;
			$existing_shelf = $storage_location_details->shelf;
			$existing_position = $storage_location_details->position;
			$existing_shelf_id = $existing_aisle.'_'.$existing_bay.'_'.$existing_shelf;

$existing_shelf_update = $wpdb->get_row("
SELECT remaining
FROM wpqa_wpsc_epa_storage_status
WHERE
shelf_id = '" . $existing_shelf_id . "' AND
digitization_center = '" . $center . "'
");

$existing_shelf_update_remaining = $existing_shelf_update->remaining + 1;

				$existing_ss_table_name = 'wpqa_wpsc_epa_storage_status';
				
				if ($existing_shelf_update_remaining == 4) {
				$existing_ss_data_update = array('occupied' => 0, 'remaining' => $existing_shelf_update_remaining);
				} else {
				$existing_ss_data_update = array('occupied' => 1, 'remaining' => $existing_shelf_update_remaining);
				}
				
				$existing_ss_data_where = array('shelf_id' => $existing_shelf_id);

				$wpdb->update($existing_ss_table_name, $existing_ss_data_update, $existing_ss_data_where);
				
// Update wpqa_wpsc_epa_storage_location with new location that was selected

$table_name = 'wpqa_wpsc_epa_storage_location';
$data_update = array('aisle' => $aisle ,'bay'=>$bay,'shelf'=>$shelf,'position'=>$position);
$data_where = array('id' => $box_storage_location_id);
$wpdb->update($table_name , $data_update, $data_where);

$new_shelf_id_update = $aisle.'_'.$bay.'_'.$shelf;

// Update storage status remove box from new shelf location
				$new_shelf_update = $wpdb->get_row("
SELECT remaining
FROM wpqa_wpsc_epa_storage_status
WHERE
shelf_id = '" . $new_shelf_id_update . "' AND
digitization_center = '" . $center_term_id . "'
");

				$new_shelf_update_remaining = $new_shelf_update->remaining - 1;

				$new_ss_table_name = 'wpqa_wpsc_epa_storage_status';
				$new_ss_data_update = array('occupied' => 1, 'remaining' => $new_shelf_update_remaining);
				$new_ss_data_where = array('shelf_id' => $new_shelf_id_update);

				$wpdb->update($new_ss_table_name, $new_ss_data_update, $new_ss_data_where);

				$get_ticket_id = $wpdb->get_row("
SELECT ticket_id
FROM wpqa_wpsc_epa_boxinfo
WHERE
box_id = '" . $boxid . "'
");

				$ticket_id = $get_ticket_id->ticket_id;
				
$shelf_info = $aisle. 'A_' .$bay . 'B_' . $shelf .'S_'.$position.'P_'.$center_value;

$shelf_meta_existing = '';

if($existing_aisle == 0 && $existing_bay == 0 && $existing_shelf == 0 && $existing_position == 0){
$shelf_meta_existing = 'Unassigned';
} else {
$shelf_meta_existing = $existing_aisle. 'A_' .$existing_bay . 'B_' . $existing_shelf .'S_'.$existing_position.'P_'.$center_value;
}
$shelf_meta = $shelf_meta_existing.' > '.$aisle. 'A_' .$bay . 'B_' . $shelf .'S_'.$position.'P_'.$center_value;

do_action('wpppatt_after_shelf_location', $ticket_id, $boxid, $shelf_meta);

   echo "Box ID #: " . $boxid . " has been updated. New Location: " .$shelf_info;
} else {
   echo "Update not successful.";
}
?>