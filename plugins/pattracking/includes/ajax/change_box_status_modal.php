
<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
// set default filter for agents and customers //not true
/*
global $current_user, $wpscfunction;
if (!$current_user->ID) die();
*/

global $current_user, $wpscfunction, $wpdb;
if (!($current_user->ID && $current_user->has_cap('wpsc_agent'))) {
	exit;
}

$subfolder_path = site_url( '', 'relative'); 
$save_enabled = true;





//
// Get items
//
$type = isset($_POST['type']) ? sanitize_text_field($_POST['type']) : '';
$item_ids = $_REQUEST['item_ids']; 
$is_single_item = isset($_POST['is_single_item']) ? sanitize_text_field($_POST['is_single_item']) : '';
$num_of_items = count($item_ids);
$ticket_id = '0000001';
$is_single_item = ($num_of_items == 1) ? true : false;
$old_status = Patt_Custom_Func::get_box_file_details_by_id($item_ids[0])->box_status;







//
// Checks to determine if status can be saved
//
// 1) IF Nobody assigned to the box THEN all but Pending must be disabled. (672,671,65,6,673,674,743,66,68,67)
// 2) Box is not validated (66,68,67) - Validation is a status in same list, right? Must be in status of Validation?
//                                   - count validated flag is in folder doc - Andrew to send CODE
// 3) Destruction Approval - Check to see if request contains a destruction_approval of 1 in wpqa_wpsc_ticket - IF = 0 then disable
//                         - Disable the ability to select Destruction approval if Not approved. 
// 4) if request status = 3,670,69 THEN 672,671,65,6,673,674,743,66,68,67 Need to be disabled (Only allow Pending) 
// 

//ob_start();

//
// item_ids = array, show data structure. - ["0000001-2", "0000003-2", "0000003-3"] or ["0000001-2"]
// 
//
//

/*
$restricted_status_list = array();
$restriction_reason = '';
$all_unassigned_x = true;
$condition_c1 = false; 
$condition_c4 = false; 

foreach( $item_ids as $item ) {
	$box_obj = Patt_Custom_Func::get_box_file_details_by_id($item);
	$status_agent_array = Patt_Custom_Func::get_user_status_data( ['box_id' => $box_obj->Box_id_FK ] );
	$ignore_box_status = ['Pending', 'Ingestion', 'Completed', 'Dispositioned'];
	$status_list_assignable = Patt_Custom_Func::get_all_status($ignore_box_status);
 	$where = ['box_folder_file_id' => $box_obj->box_id ];
 	$ticket_id_obj = Patt_Custom_Func::get_ticket_id_from_box_folder_file( $where );



	

	// Condition 1.
	$all_assigned = false;
	$all_unassigned = true;
	foreach( $status_agent_array['status'] as $term_id=>$user_array ) {
		
		if( array_key_exists($term_id, $status_list_assignable) ) {
			if( count($user_array) > 0 && $user_array != 'N/A' ) {
				//users exist
				$all_unassigned = false;
				break;
			}
		}
	}
	
	// Condition 1 SET.
	if( $all_unassigned ) {
		$restriction_reason .= '<p>Box '.$box_obj->box_id.' has no one assigned to Any Status. (C1)<p>';
		$condition_c1 = true;

		
		if( !in_array('Scanning Preparation', $restricted_status_list) ) {
			$restricted_status_list[] = 'Scanning Preparation';
		} 
		if( !in_array('Scanning/Digitization', $restricted_status_list) ) {
			$restricted_status_list[] = 'Scanning/Digitization';
		} 
		if( !in_array('QA/QC', $restricted_status_list) ) {
			$restricted_status_list[] = 'QA/QC';
		} 
		if( !in_array('Digitized - Not Validated', $restricted_status_list) ) {
			$restricted_status_list[] = 'Digitized - Not Validated';
		} 
		if( !in_array('Ingestion', $restricted_status_list) ) {
			$restricted_status_list[] = 'Ingestion';
		} 
		if( !in_array('Validation', $restricted_status_list) ) {
			$restricted_status_list[] = 'Validation';
		} 
		if( !in_array('Re-scan', $restricted_status_list) ) {
			$restricted_status_list[] = 'Re-scan';
		} 
		if( !in_array('Completed', $restricted_status_list) ) {
			$restricted_status_list[] = 'Completed';
		} 
		if( !in_array('Destruction Approval', $restricted_status_list) ) {
			$restricted_status_list[] = 'Destruction Approval';
		} 
		if( !in_array('Dispositioned', $restricted_status_list) ) {
			$restricted_status_list[] = 'Dispositioned';
		} 
	}
	
	
	// Condition 2 
	$get_sum_total = $wpdb->get_row("select sum(a.total_count) as sum_total_count
									from (
										SELECT (
											SELECT count(id) 
												FROM wpqa_wpsc_epa_folderdocinfo as c
											WHERE box_id = a.id 
										) as total_count 
										FROM wpqa_wpsc_epa_boxinfo as a  
										WHERE a.id = '" . $box_obj->Box_id_FK . "'
									) 
								a");
	
	$sum_total_val = $get_sum_total->sum_total_count;
	
	$get_sum_validation = $wpdb->get_row("select sum(a.validation) as sum_validation
											from (
												SELECT (
													SELECT sum(validation = 1) FROM wpqa_wpsc_epa_folderdocinfo WHERE box_id = a.id
												) as validation 
												FROM wpqa_wpsc_epa_boxinfo as a 
												
												WHERE a.id = '" . $box_obj->Box_id_FK . "'	
											) 
										a");									
					
			
	$sum_validation = $get_sum_validation->sum_validation;

	$validated = '';
	
	if($sum_total_val == $sum_validation) {
		$validated = 1;
	} else {
		$validated = 0;
	}
	
	// Condition 2 SET
	if( !$validated ) {
		$restriction_reason .= '<p>Contents of Box '.$box_obj->box_id.' have not been Validated. (C2)</p>';
		
		if( !in_array('Completed', $restricted_status_list) ) {
			$restricted_status_list[] = 'Completed';
		} 
		if( !in_array('Dispositioned', $restricted_status_list) ) {
			$restricted_status_list[] = 'Dispositioned';
		}
		if( !in_array('Destruction Approval', $restricted_status_list) ) {
			$restricted_status_list[] = 'Destruction Approval';
		}
	}
	
	
	// Condition 3 - Destruction Approval
	
 	$box_destruction_approval = $wpdb->get_row("SELECT destruction_approval FROM wpqa_wpsc_ticket WHERE id='".$ticket_id_obj['ticket_id']."'");

	
	// Condition 3 SET - Show destruction approval setting?
	if( $box_destruction_approval->destruction_approval ) {
		//$restriction_reason .= '<p>Contents of Box '.$box_obj->box_id.' have been approved for Destruction.(C3)</p>';
		
		// if Destruction Approval has already been restricted AND C1 has never come up... 
		if( in_array('Destruction Approval', $restricted_status_list) && !$condition_c1 && !$condition_c4 ) {
			$the_key = array_search('Destruction Approval', $restricted_status_list);
			unset($restricted_status_list[$the_key]);
			array_values($restricted_status_list);
			//$restricted_status_list[] = 'Destruction Approval';
		}
	} else {
		$restriction_reason .= '<p>Contents of Box '.$box_obj->box_id.' have not been approved for Destruction. (C3)</p>';
		if( !in_array('Destruction Approval', $restricted_status_list) ) {
			$restricted_status_list[] = 'Destruction Approval';
		}
	}
	
	// Condition 4 - if request status = 3,670,69 - only allow 'Pending'
	$data = [ 'ticket_id'=>$ticket_id_obj['ticket_id'] ];
	$ticket_status = Patt_Custom_Func::get_ticket_status( $data );
	
	
	// Condition 4 SET
	if( $ticket_status == 3 || $ticket_status == 670 || $ticket_status == 69 ) {
		$save_enabled = false;
		$restriction_reason .= '<p>Containing Request of Box '.$box_obj->box_id.' has a status of New, Cancelled, or Initial Review Rejected. (C4)</p>';
		$condition_c4 = true;
		
		if( !in_array('Scanning Preparation', $restricted_status_list) ) {
			$restricted_status_list[] = 'Scanning Preparation';
		} 
		if( !in_array('Scanning/Digitization', $restricted_status_list) ) {
			$restricted_status_list[] = 'Scanning/Digitization';
		} 
		if( !in_array('QA/QC', $restricted_status_list) ) {
			$restricted_status_list[] = 'QA/QC';
		} 
		if( !in_array('Digitized - Not Validated', $restricted_status_list) ) {
			$restricted_status_list[] = 'Digitized - Not Validated';
		} 
		if( !in_array('Ingestion', $restricted_status_list) ) {
			$restricted_status_list[] = 'Ingestion';
		} 
		if( !in_array('Validation', $restricted_status_list) ) {
			$restricted_status_list[] = 'Validation';
		} 
		if( !in_array('Re-scan', $restricted_status_list) ) {
			$restricted_status_list[] = 'Re-scan';
		} 
		if( !in_array('Completed', $restricted_status_list) ) {
			$restricted_status_list[] = 'Completed';
		} 
		if( !in_array('Destruction Approval', $restricted_status_list) ) {
			$restricted_status_list[] = 'Destruction Approval';
		} 
		if( !in_array('Dispositioned', $restricted_status_list) ) {
			$restricted_status_list[] = 'Dispositioned';
		} 
		
	}
	
}

// $ignore_box_status = ['Pending', 'Ingestion', 'Completed', 'Dispositioned'];
$box_statuses = Patt_Custom_Func::get_all_status($restricted_status_list);
*/




$status_list = Patt_Custom_Func::get_restricted_box_status_list( $item_ids );

$box_statuses = $status_list['box_statuses'];
$restriction_reason = $status_list['restriction_reason'];



ob_start();
// Debug
/*
echo '<br>box obj<br><pre>';	
print_r($box_obj);
echo '</pre><br>';
*/
/*
echo 'Condition 1: <br>';
echo 'All Unassigned? ';
echo ($all_unassigned) ? 'true' : 'false';
echo '<br> Restricted Reason: '.$restriction_reason.'<br>';
echo 'Restricted Status List: <pre>';
print_r($restricted_status_list);
echo '</pre>';

echo 'Condition 2: <br>';
echo 'Validated? ';
echo ($validated) ? 'true' : 'false';
echo '<br> Restricted Reason: '.$restriction_reason.'<br>';
echo 'Restricted Status List: <pre>';
print_r($restricted_status_list);
echo '</pre>';

echo 'Condition 3: <br>';
echo 'Box Destruction Approval? ';
echo ($box_destruction_approval->destruction_approval) ? 'true' : 'false';
echo '<br>Second thoughts: '.$box_destruction_approval->destruction_approval;
echo '<br> Restricted Reason: '.$restriction_reason.'<br>';
echo 'Restricted Status List: <pre>';
print_r($restricted_status_list);
echo '</pre>';

echo 'Condition 4: <br>';
echo 'Ticket Status? ';
print_r( $ticket_status);
echo '<br> Restricted Reason: '.$restriction_reason.'<br>';
echo 'Restricted Status List: <pre>';
print_r($restricted_status_list);
echo '</pre>';


echo "<br>Assign Agents for: ".$type;
echo "<br>";
print_r($item_ids);
echo "<br>Box Statuses: <pre>";
print_r($box_statuses);
echo "</pre><br>";
echo "current status: ";
print_r( Patt_Custom_Func::get_box_file_details_by_id($item_ids[0]));
//print_r($term_id_array);
echo "<br>";
//echo "Get user status data: <br>";
//print_r($get_user_status_data);
echo "<br>Actual status data: <br>";
print_r($status_agent_array);
echo "<br>";
echo "Is single: ".$is_single_item;
echo "<br>";
echo "<br>Status list Assignable: <br>";
print_r($status_list_assignable);
*/
?>



<div id='alert_status' class=''></div> 

<div class="row">
	<div class="col-lg-2">
		<label class="wpsc_ct_field_label">Box Status: </label>
	</div>
	
	<div class="col-lg-10">
		<select id='new_box_status'> 
					<option value=''>-- Select Status --</option>
					<?php 
//						foreach( $box_statuses as $status ) {
//							echo "<option value='".$status->term_id."'>".$status->name."</option>";
//						}
						foreach( $box_statuses as $term=>$status ) {
							echo "<option value='".$term."'>".$status."</option>";
						}

					?>
				</select>
	</div>
</div>














<style>
#wpsc_popup_body {
	max-height: 450px;
}	

.zebra {
	padding: 7px 0px 7px 0px;
}

.zebra:nth-of-type(even) {
/* 	background: #e0e0e0; */
	background: #f3f3f3;
	border-radius: 4px;
}

#assigned_agents {
	margin-bottom: 0px !important;
}

.wpsc_display_assign_agent {
	margin-bottom: 5px !important;
}

.tight {
	margin-top: 3px !important;
	margin-bottom: 10px !important;
}

.staff-badge {
	padding: 3px 5px 3px 5px;
	font-size:1.0em !important;
	vertical-align: middle;
}

.staff-close {
	margin-left: 3px;
	margin-right: 3px;
}

.label_center {
	margin-top: 5px !important;
	margin-bottom: 0px !important;
}

.alert_spacing {
	margin: 0px 0px 0px 0px;
}

</style>

<script>
	jQuery(document).ready(function(){
		
// 		let current_status = '<?php echo Patt_Custom_Func::get_box_file_details_by_id($item_ids[0])->box_status ?>';
		let current_status = '<?php echo $old_status ?>';		
		let is_single_item = <?php echo json_encode($is_single_item); ?>;
		let restriction_reason = '<?php echo $restriction_reason ?>';		

		// Set the value of the select to the current value. 
		if( is_single_item ) {
			jQuery('#new_box_status').val(current_status);
		}
		
		// Only allow saving after change
		jQuery('#new_box_status').change( function() {
			console.log('New Status: '+jQuery('#new_box_status').val());
			if( jQuery('#new_box_status').val() == '' ) {
				jQuery("#button_box_status_submit").hide();
			} else {
				jQuery("#button_box_status_submit").show();
			}
				
		});
			
		
		if( restriction_reason.length > 0 ) {
			set_alert('warning', restriction_reason);				
		}

		
		
	});


// Sets an alert
function set_alert( type, message ) {
	
	let alert_style = '';
	
	switch( type ) {
		case 'success':
			alert_style = 'alert-success';		
			break;
		case 'warning':
			alert_style = 'alert-warning';
			break;
		case 'danger':
			alert_style = 'alert-danger';
			break;		
	}

	jQuery('#alert_status').html('<div class=" alert '+alert_style+'">'+message+'</div>'); //badge badge-danger
	jQuery('#alert_status').addClass('alert_spacing');	
	
}

</script>

<?php

$body = ob_get_clean();

ob_start();

?>
<button type="button" class="btn wpsc_popup_close"  style="background-color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_text_color']?> !important;"   onclick="wpsc_modal_close();"><?php _e('Close','wpsc-export-ticket');?></button>
<button type="button" id="button_box_status_submit" class="btn wpsc_popup_action" style="background-color:<?php echo $wpsc_appearance_modal_window['wpsc_action_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_modal_window['wpsc_action_button_text_color']?> !important;" onclick="wppatt_set_box_status();"><?php _e('Save','supportcandy');?></button>

<script>
jQuery("#button_box_status_submit").hide();

function wppatt_set_box_status(){
	let item_ids = <?php echo json_encode($item_ids); ?>;	
	let term_id_array = <?php echo json_encode($term_id_array); ?>;
	let is_single_item = <?php echo json_encode($is_single_item); ?>;
	var new_status = jQuery('#new_box_status').val();
	let old_status = '<?php echo $old_status ?>';
	
	console.log('setting box status for items: ');
	console.log(item_ids);
	
	
	// Audit Log
	// Get list of all old box statuses
 	// 
 	

 
	//console.log(new_requestors);
	console.log('term id array: ');
	console.log(term_id_array);
	console.log('new status ');
	console.log(new_status);
	console.log('item_ids ');
	console.log(item_ids);
	

	jQuery.post(
	   '<?php echo WPPATT_PLUGIN_URL; ?>includes/admin/pages/scripts/update_box_status.php',{
	    type: 'box_status',
	    new_status: new_status,
	    old_status: old_status,
	    item_ids: item_ids,
	    is_single_item: is_single_item 
	}, 
    function (response) {
		//alert('updated: '+response);
		console.log('The Response:');
		console.log(response);
		window.location.reload(); 

    });

	wpsc_modal_close();
} 

// Sets an alert
function set_alert( type, message ) {
	
	let alert_style = '';
	
	switch( type ) {
		case 'success':
			alert_style = 'alert-success';		
			break;
		case 'warning':
			alert_style = 'alert-warning';
			break;
		case 'danger':
			alert_style = 'alert-danger';
			break;		
	}

	jQuery('#alert_status').html('<span class=" alert '+alert_style+'">'+message+'</span>'); //badge badge-danger
	jQuery('#alert_status').addClass('alert_spacing');	
	
}
</script>



<?php 
$footer = ob_get_clean();

$output = array(
  'body'   => $body,
  'footer' => $footer
);
echo json_encode($output);

