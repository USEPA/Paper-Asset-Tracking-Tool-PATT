<?php
global $wpdb, $current_user, $wpscfunction;

$path = preg_replace('/wp-content.*$/', '', __DIR__);
include ($path . 'wp-load.php');
include ($path . 'wp-content/plugins/pattracking/includes/class-wppatt-custom-function.php');

//Grab ticket ID and Selected Digitization Center from Modal
	$tkid = $_POST['postvartktid'];
	$dc_final = $_POST['postvardcname'];
	
//Obtain Ticket Status
	$ticket_details = $wpdb->get_row("
SELECT ticket_status 
FROM wpqa_wpsc_ticket 
WHERE
id = '" . $tkid . "'
");

	$ticket_details_status = $ticket_details->ticket_status;

if (($ticket_details_status == 3 || $ticket_details_status == 4) && isset($_POST['postvartktid']) && isset($_POST['postvardcname']) && $_POST['postvardcname'] != '666') {

// Finds Shelf ID of next available sequence
	$find_sequence = $wpdb->get_row("
WITH 
cte1 AS
(
SELECT id, 
       CASE WHEN     occupied  = LAG(occupied) OVER (ORDER BY id)
                 AND remaining = LAG(remaining) OVER (ORDER BY id)
            THEN 0
            ELSE 1 
            END values_differs
FROM wpqa_wpsc_epa_storage_status
WHERE digitization_center = '" . $dc_final . "'
),
cte2 AS 
(
SELECT id,
       SUM(values_differs) OVER (ORDER BY id) group_num
FROM cte1
ORDER BY id
)
SELECT MIN(id) as id
FROM cte2
GROUP BY group_num
ORDER BY COUNT(*) DESC LIMIT 1;
");

	$sequence_shelfid = $find_sequence->id;

//Get array of unassigned boxes from ticket ID
	$box_id_assignment = Patt_Custom_Func::get_unassigned_boxes($tkid);

//Select count of boxes that have not been auto assigned
	$box_details = $wpdb->get_row("
SELECT wpqa_wpsc_epa_boxinfo.id, count(wpqa_wpsc_epa_boxinfo.id) as count 
FROM wpqa_wpsc_epa_boxinfo 
INNER JOIN wpqa_wpsc_epa_storage_location ON wpqa_wpsc_epa_boxinfo.storage_location_id = wpqa_wpsc_epa_storage_location.id 
WHERE
wpqa_wpsc_epa_storage_location.digitization_center IS NOT NULL AND
wpqa_wpsc_epa_storage_location.aisle = 0 AND 
wpqa_wpsc_epa_storage_location.bay = 0 AND 
wpqa_wpsc_epa_storage_location.shelf = 0 AND 
wpqa_wpsc_epa_storage_location.position = 0 AND
wpqa_wpsc_epa_boxinfo.ticket_id = '" . $tkid . "'
");

	$box_details_count = $box_details->count;

// Check to see if tkid is a comma delimited list
	$ticketid_array = explode(',', $tkid);

	if (sizeof($ticketid_array) > 1) {
// If comma delimited list, parse out into array and run a for-each loop
		echo 'Multiple Tickets selected';
		print_r($ticketid_array);
	} else {
// Is the box count = 1?:: Continuous shelf space not requried. Find first available gap.

// Get count of first available position
$nc_count_single_rows = $wpdb->get_row("
SELECT count(shelf_id) as count
FROM wpqa_wpsc_epa_storage_status
WHERE occupied = 1 AND
remaining = 1 AND
digitization_center = '" . $dc_final . "'
ORDER BY id asc
LIMIT 1
");

$nc_count = $nc_count_single_rows->count;

//echo $nc_count;

if (($box_details_count == 1) && ($nc_count > 0)) {

// Find first available slot for requests with boxes equal to 1
			$nc_shelf = $wpdb->get_row("
SELECT shelf_id
FROM wpqa_wpsc_epa_storage_status
WHERE occupied = 1 AND
remaining = 1 AND
digitization_center = '" . $dc_final . "'
ORDER BY id asc
LIMIT 1
");
			$nc_shelf_id = $nc_shelf->shelf_id;

			[$nc_aisle, $nc_bay, $nc_shelf] = explode("_", $nc_shelf_id);

// Get first available position
			$nc_position_details = $wpdb->get_results("
SELECT position FROM wpqa_wpsc_epa_storage_location 
WHERE aisle = '" . $nc_aisle . "' 
AND bay = '" . $nc_bay . "' 
AND shelf = '" . $nc_shelf . "' 
AND digitization_center = '" . $dc_final . "'
");
			$nc_position_gap_array = array();
			$nc_aisle_bay_shelf_position = array();

			foreach ($nc_position_details as $info) {
				$position_nc_position = $info->position;
				array_push($nc_position_gap_array, $position_nc_position);
			}
				
// Determine missing positions and push to an array         
			$nc_missing = array_diff(range(1, 4), $nc_position_gap_array);
// Only use portion of array that equals the number of boxes that are unassigned
			$nc_missing_final = array_slice($nc_missing, 0, $box_details_count);

			foreach ($nc_missing_final as &$nc_missing_val) {
				$nc_position_id_val = $nc_shelf_id . '_' . $nc_missing_val;
				array_push($nc_aisle_bay_shelf_position, $nc_position_id_val);
			}
				//print_r($nc_aisle_bay_shelf_position);

			foreach ($nc_aisle_bay_shelf_position as $key => $value) {
				[$ncf_aisle, $ncf_bay, $ncf_shelf, $ncf_position] = explode("_", $value);
// Make auto-assignment in database
				$ncsl_table_name = 'wpqa_wpsc_epa_storage_location';
				$ncsl_data_update = array(
					'aisle' => $ncf_aisle, 'bay' => $ncf_bay, 'shelf' => $ncf_shelf, 'position' => $ncf_position, 'digitization_center' => $dc_final
				);
				$ncsl_data_where = array('id' => $box_id_assignment[$key]);

			    $wpdb->update($ncsl_table_name, $ncsl_data_update, $ncsl_data_where);

				$nc_shelf_id_update = $ncf_aisle . '_' . $ncf_bay . '_' . $ncf_shelf;
// Update storage status table
				$nc_shelf_update = $wpdb->get_row("
SELECT remaining
FROM wpqa_wpsc_epa_storage_status
WHERE
shelf_id = '" . $nc_shelf_id_update . "' AND
digitization_center = '" . $dc_final . "'
");

				$nc_shelf_update_remaining = $nc_shelf_update->remaining - 1;

				$ncss_table_name = 'wpqa_wpsc_epa_storage_status';
				$ncss_data_update = array('occupied' => 1, 'remaining' => $nc_shelf_update_remaining);
				$ncss_data_where = array('shelf_id' => $nc_shelf_id_update);

				$wpdb->update($ncss_table_name, $ncss_data_update, $ncss_data_where);
				
// Update physical location

// Get count of first available position
$get_box_id = $wpdb->get_row("
SELECT id
FROM wpqa_wpsc_epa_boxinfo
WHERE
storage_location_id = '" . $box_id_assignment[$key] . "'
");

$bid = $get_box_id->id;

//SET PHYSICAL LOCATION TO IN TRANSIT
$table_pl = 'wpqa_wpsc_epa_boxinfo';
$pl_update = array('location_status_id' => '1');
$pl_where = array('id' => $bid);
$wpdb->update($table_pl , $pl_update, $pl_where);
			}
// When Continuing shelf space space is required

		} else if ($box_details_count <= Patt_Custom_Func::calc_max_gap_val($dc_final)) {
		    
			$find_gaps = $wpdb->get_results("
WITH 
cte1 AS
(
SELECT shelf_id, remaining, SUM(remaining = 0) OVER (ORDER BY id) group_num
FROM wpqa_wpsc_epa_storage_status
WHERE digitization_center = '" . $dc_final . "' AND
id BETWEEN 1 AND '" . $sequence_shelfid . "'
)
SELECT GROUP_CONCAT(shelf_id) as shelf_id,
       GROUP_CONCAT(remaining) as remaining,
       SUM(remaining) as total
FROM cte1
WHERE remaining != 0
GROUP BY group_num
");

			$findgaps_array = array();

			$counter = 0;

// Obtain total from above query and determine next available gap that fits the unassigned box total
			foreach ($find_gaps as $info) {
				$findgaps_shelfid = $info->shelf_id;
				$findgaps_remaining = $info->remaining;
				$findgaps_total = $info->total;
				array_push($findgaps_array, $findgaps_shelfid);
				if ($box_details_count <= $findgaps_total) {
					break;
				}
				$counter++;
			}
            //echo $counter;
            
            //print_r($findgaps_array);
			$shelfid_gaps_array = explode(",", $findgaps_array[$counter]);
            //print_r($shelfid_gaps_array);
            
			$missing_gap_array = array();
			//$position_gap_array = array();
			foreach ($shelfid_gaps_array as &$value) {

				[$gap_aisle, $gap_bay, $gap_shelf] = explode("_", $value);
				//echo $value;
				//echo $gap_aisle;
				//echo $gap_bay;
				//echo $gap_shelf;
				$current_row_details = $wpdb->get_row("
SELECT remaining
FROM wpqa_wpsc_epa_storage_status
WHERE
shelf_id = '" . $value . "' AND
digitization_center = '" . $dc_final . "'
");

				$get_current_row_details_value = $current_row_details->remaining;
				

if($get_current_row_details_value != 4) {
// Get all positions in an array to determine available positions
				$position_gap_details = $wpdb->get_results("
SELECT position FROM wpqa_wpsc_epa_storage_location 
WHERE aisle = '" . $gap_aisle . "' 
AND bay = '" . $gap_bay . "' 
AND shelf = '" . $gap_shelf . "' 
AND digitization_center = '" . $dc_final . "'
");

$position_gap_array = array();

foreach ($position_gap_details as $item) {
    
$array_gap_val_final = $item->position;

//echo $array_gap_val_final;
array_push($position_gap_array, $array_gap_val_final);

}

//echo $get_current_row_details_value;

} else {

$position_gap_array = array();
$array_gap_val_final = '';
array_push($position_gap_array, $array_gap_val_final);

//echo $get_current_row_details_value;
}

//print_r($position_gap_array);
// Determine missing positions and push to an array.         
				$missing = array_diff(range(1, 4), $position_gap_array);
				//print_r($missing);

				foreach ($missing as &$missing_val) {
					$shelf_position_id_val = $value . '_' . $missing_val;
					array_push($missing_gap_array, $shelf_position_id_val);
				}
			}
			
// Only use portion of array that equals the number of boxes that are unassigned
			$gap_aisle_bay_shelf_position = array_slice($missing_gap_array, 0, $box_details_count);
                //print_r($missing_gap_array);
                //echo $box_details_count;
                //print_r($gap_aisle_bay_shelf_position);
                //print_r($box_id_assignment);

			foreach ($gap_aisle_bay_shelf_position as $key => $value) {
				[$gap_aisle, $gap_bay, $gap_shelf, $gap_position] = explode("_", $value);
// Make auto-assignment in database
				$gapsl_table_name = 'wpqa_wpsc_epa_storage_location';
				$gapsl_data_update = array(
					'aisle' => $gap_aisle, 'bay' => $gap_bay, 'shelf' => $gap_shelf, 'position' => $gap_position, 'digitization_center' => $dc_final
				);
				$gapsl_data_where = array('id' => $box_id_assignment[$key]);

				$wpdb->update($gapsl_table_name, $gapsl_data_update, $gapsl_data_where);

				$gap_shelf_id_update = $gap_aisle . '_' . $gap_bay . '_' . $gap_shelf;
// Update storage status table
				$gap_shelf_update = $wpdb->get_row("
SELECT remaining
FROM wpqa_wpsc_epa_storage_status
WHERE
shelf_id = '" . $gap_shelf_id_update . "' AND
digitization_center = '" . $dc_final . "'
");

				$gap_shelf_update_remaining = $gap_shelf_update->remaining - 1;

				$gapss_table_name = 'wpqa_wpsc_epa_storage_status';
				$gapss_data_update = array('occupied' => 1, 'remaining' => $gap_shelf_update_remaining);
				$gapss_data_where = array('shelf_id' => $gap_shelf_id_update);

				$wpdb->update($gapss_table_name, $gapss_data_update, $gapss_data_where);
// Update physical location
// Get count of first available position
$get_box_id = $wpdb->get_row("
SELECT id
FROM wpqa_wpsc_epa_boxinfo
WHERE
storage_location_id = '" . $box_id_assignment[$key] . "'
");

$bid = $get_box_id->id;
//SET PHYSICAL LOCATION TO IN TRANSIT
$table_pl = 'wpqa_wpsc_epa_boxinfo';
$pl_update = array('location_status_id' => '1');
$pl_where = array('id' => $bid);
$wpdb->update($table_pl , $pl_update, $pl_where);
			}
// For every other case assign box to next available slot of available shelfs
		} else {
		    
// Calculate previous remaining value:: Need to fix. Needs to find previous sequence up to where remaining = 0
                $previous_sequence_shelfid = $sequence_shelfid - 1;

				$previous_row_details = $wpdb->get_row("
SELECT remaining
FROM wpqa_wpsc_epa_storage_status
WHERE
id = '" . $previous_sequence_shelfid . "' AND
digitization_center = '" . $dc_final . "'
");

				$previous_sequence_shelfid_value = $previous_row_details->remaining;

if($previous_sequence_shelfid_value > 0 && $previous_sequence_shelfid_value < 4) {
$sequence_shelfid = $sequence_shelfid - 1;
$box_details_count_new = $box_details_count - $previous_sequence_shelfid_value;
$sequence_upperlimit = $sequence_shelfid + ceil($box_details_count_new / 4);
} else {
$sequence_upperlimit = $sequence_shelfid + ceil($box_details_count / 4) - 1;
}

			$find_sequence_details = $wpdb->get_results("
SELECT shelf_id FROM wpqa_wpsc_epa_storage_status 
WHERE ID BETWEEN '" . $sequence_shelfid . "' AND '" . $sequence_upperlimit . "' AND
digitization_center = '" . $dc_final . "'
");

//echo $box_details_count. '>>';
//echo $previous_sequence_shelfid_value. '>>';
//echo $sequence_shelfid. '>>';
//echo $sequence_upperlimit;

if($previous_sequence_shelfid_value != 4) {
            $shelfid_array = array();
			foreach ($find_sequence_details as $info) {
				$find_sequence_shelfid = $info->shelf_id;
				array_push($shelfid_array, $find_sequence_shelfid);
			}

            $sequence_array = array();
			foreach ($shelfid_array as &$value) {

				[$seq_aisle, $seq_bay, $seq_shelf] = explode("_", $value);

				$current_row_details = $wpdb->get_row("
SELECT remaining
FROM wpqa_wpsc_epa_storage_status
WHERE
shelf_id = '" . $value . "' AND
digitization_center = '" . $dc_final . "'
");

				$get_current_row_details_value = $current_row_details->remaining;
				
				
if($get_current_row_details_value != 4) {
// Get all positions in an array to determine available positions
				$position_seq_details = $wpdb->get_results("
SELECT position FROM wpqa_wpsc_epa_storage_location 
WHERE aisle = '" . $seq_aisle . "' 
AND bay = '" . $seq_bay . "' 
AND shelf = '" . $seq_shelf . "' 
AND digitization_center = '" . $dc_final . "'
");


$position_seq_array = array();
foreach ($position_seq_details as $item) {
    
$array_seq_val_final = $item->position;
array_push($position_seq_array, $array_seq_val_final);

}

//echo $get_current_row_details_value;

} else {

$position_seq_array = array();
$array_seq_val_final = '';
array_push($position_seq_array, $array_seq_val_final);

//echo $get_current_row_details_value;
}


				//print_r($position_seq_array);
// Determine missing positions and push to an array.         
				$missing = array_diff(range(1, 4), $position_seq_array);
				//print_r($missing);

				foreach ($missing as &$missing_val) {
					$shelf_position_id_val = $value . '_' . $missing_val;
					array_push($sequence_array, $shelf_position_id_val);
				}
			}
//print_r($sequence_array);

} else {

// Create an array of four positions
			$sequence_array = array();
			$four_array = array();
			foreach (range(1, 4) as $number) {
				array_push($four_array, $number);
			}

			foreach ($find_sequence_details as $info) {
				$find_sequence_shelfid = $info->shelf_id;
				//array_push($sequence_array, $find_sequence_shelfid);
// Assign position to shelf id
				foreach ($four_array as &$seq_position_val) {
					$shelf_position_id_val = $find_sequence_shelfid . '_' . $seq_position_val;
					array_push($sequence_array, $shelf_position_id_val);
				}
			}

//print_r($sequence_array);

}

			$seq_aisle_bay_shelf_position = array_slice($sequence_array, 0, $box_details_count);
			//print_r($seq_aisle_bay_shelf_position);
// Only use portion of array that equals the number of boxes that are unassigned
			foreach ($seq_aisle_bay_shelf_position as $key => $value) {
				[$seq_aisle, $seq_bay, $seq_shelf, $seq_position] = explode("_", $value);
// Make auto-assignment in database
				$seqsl_table_name = 'wpqa_wpsc_epa_storage_location';
				$seqsl_data_update = array(
					'aisle' => $seq_aisle, 'bay' => $seq_bay, 'shelf' => $seq_shelf, 'position' => $seq_position, 'digitization_center' => $dc_final
				);
				$seqsl_data_where = array('id' => $box_id_assignment[$key]);

				$wpdb->update($seqsl_table_name, $seqsl_data_update, $seqsl_data_where);

				$seq_shelf_id_update = $seq_aisle . '_' . $seq_bay . '_' . $seq_shelf;
// Update storage status table
				$seq_shelf_update = $wpdb->get_row("
SELECT remaining
FROM wpqa_wpsc_epa_storage_status
WHERE
shelf_id = '" . $seq_shelf_id_update . "' AND
digitization_center = '" . $dc_final . "'
");

				$seq_shelf_update_remaining = $seq_shelf_update->remaining - 1;

				$seqss_table_name = 'wpqa_wpsc_epa_storage_status';
				$seqss_data_update = array('occupied' => 1, 'remaining' => $seq_shelf_update_remaining);
				$seqss_data_where = array('shelf_id' => $seq_shelf_id_update);

				$wpdb->update($seqss_table_name, $seqss_data_update, $seqss_data_where);
// Update physical location
// Get count of first available position
$get_box_id = $wpdb->get_row("
SELECT id
FROM wpqa_wpsc_epa_boxinfo
WHERE
storage_location_id = '" . $box_id_assignment[$key] . "'
");
//SET PHYSICAL LOCATION TO IN TRANSIT
$table_pl = 'wpqa_wpsc_epa_boxinfo';
$pl_update = array('location_status_id' => '1');
$pl_where = array('id' => $bid);
$wpdb->update($table_pl , $pl_update, $pl_where);
			}
		}
// Display message to end user
		if (($ticket_details_status == 3 || $ticket_details_status == 4) && $box_details_count > 0) {
			echo "Boxes have been automatically assigned to a shelf.";
		} else {
		    echo "No automatic box shelf assignments made.";
		}
		
	}

} else {
	echo "No automatic box shelf assignments made.";
}
