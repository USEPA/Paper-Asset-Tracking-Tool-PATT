
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

//
// Originals. May be possible to delete.
//
$ticket_id   = isset($_POST['ticket_id']) ? sanitize_text_field($_POST['ticket_id']) : '' ; 
$current_requestor = isset($_POST['requestor']) ? sanitize_text_field($_POST['requestor']) : '' ;
$wpsc_appearance_modal_window = get_option('wpsc_modal_window');
$recall_id = isset($_POST['recall_id']) ? sanitize_text_field($_POST['recall_id']) : '';

//
// Get items
//
$type = isset($_POST['type']) ? sanitize_text_field($_POST['type']) : '';
$item_ids = $_REQUEST['item_ids']; 
$num_of_items = count($item_ids);
//$ticket_id = 1;
$ticket_id = '0000001';
$is_single_item = ( $num_of_items == 1 ) ? true : false;
$alerts_disabled = ( $type == 'view' ) ? true : false;



/*
function get_tax() {
	$box_statuses = get_terms([
		'taxonomy'   => 'wpsc_box_statuses',
		'hide_empty' => false,
		'orderby'    => 'meta_value_num',
		'order'    	 => 'ASC',
		'meta_query' => array('order_clause' => array('key' => 'wpsc_box_status_load_order')),
	]);
	return $box_statuses
}
add_action('init', 'get_tax', 9999);
*/

// Register Box Status Taxonomy
if( !taxonomy_exists('wpsc_box_statuses') ) {
	$args = array(
		'public' => false,
		'rewrite' => false
	);
	register_taxonomy( 'wpsc_box_statuses', 'wpsc_ticket', $args );
}

// $box_statuses = get_tax();

// Get List of Box Statuses
$box_statuses = get_terms([
	'taxonomy'   => 'wpsc_box_statuses',
	'hide_empty' => false,
	'orderby'    => 'meta_value_num',
	'order'    	 => 'ASC',
	'meta_query' => array('order_clause' => array('key' => 'wpsc_box_status_load_order')),
]);

// List of box status that do not need agents assigned.
$ignore_box_status = ['Pending', 'Ingestion', 'Completed', 'Dispositioned'];

$term_id_array = array();
foreach( $box_statuses as $key=>$box ) {
	if( in_array( $box->name, $ignore_box_status ) ) {
		unset($box_statuses[$key]);
		
	} else {
		$term_id_array[] = $box->term_id;
	}
}
array_values($box_statuses);


// Get user array from Recall ID -> Put in meta data format (rather than wp_user)

$where = [ 'recall_id' => $recall_id ]; 

$recall_array = Patt_Custom_Func::get_recall_data($where);

	//Added for servers running < PHP 7.3
	if (!function_exists('array_key_first')) {
	    function array_key_first(array $arr) {
	        foreach($arr as $key => $unused) {
	            return $key;
	        }
	        return NULL;
	    }
	}

$recall_array_key = array_key_first($recall_array);	
$recall_obj = $recall_array[$recall_array_key];

$user_array_wp = $recall_obj->user_id;

// Get current user id & convert to wpsc agent id.
$agent_ids = array();
$agents = get_terms([
	'taxonomy'   => 'wpsc_agents',
	'hide_empty' => false,
	'orderby'    => 'meta_value_num',
	'order'    	 => 'ASC',
]);
foreach ($agents as $agent) {
	$agent_ids[] = [
		'agent_term_id' => $agent->term_id,
		'wp_user_id' => get_term_meta( $agent->term_id, 'user_id', true),
	];
}

$assigned_agents = [];

if( is_array($user_array_wp) ) {
	foreach ( $user_array_wp as $wp_id ) {
		$key = array_search( $wp_id, array_column($agent_ids, 'wp_user_id'));
		$agent_term_id = $agent_ids[$key]['agent_term_id']; //current user agent term id
		$assigned_agents[] = $agent_term_id;
	}
} else {
	$key = array_search( $user_array_wp, array_column($agent_ids, 'wp_user_id'));
	$agent_term_id = $agent_ids[$key]['agent_term_id']; //current user agent term id
	$assigned_agents[] = $agent_term_id;
}

$old_assigned_agents = $assigned_agents; // for audit log


//
// Get all users and translate from wp_user id to wpsc agent id
//

if( $is_single_item ) {
	$box_id = Patt_Custom_Func::get_box_file_details_by_id($item_ids[0])->Box_id_FK;	
}

$where = [
			 'box_id' => $box_id,
			// 'user_id' => 2,
			// 'status_id' => 672	
		];
$assigned_agents = Patt_Custom_Func::get_user_status_data($where);

//$assigned_agents = array_values($assigned_agents);
//$assigned_agents = $assigned_agents[0];

// Translate the wp_users in the obj to agent_id
foreach( $assigned_agents['status'] as $key=>$val_array ) {
	//$assigned_agents['status'][$key] = translate_user_id( $val_array, 'agent_term_id' );
	$assigned_agents['status'][$key] = Patt_Custom_Func::translate_user_id( $val_array, 'agent_term_id' );
}

// Gets an array of all the items and their ticket status
$save_enabled = true;
$ticket_id_array = array();
foreach( $item_ids as $key=>$id ) {
	$data = ['box_folder_file_id'=>$id];
	$ticket_id_array[] = Patt_Custom_Func::get_ticket_id_from_box_folder_file( $data );
	
	$data = ['ticket_id'=>$ticket_id_array[$key]['ticket_id']];
	$ticket_status = Patt_Custom_Func::get_ticket_status( $data );
	$ticket_id_array[$key]['ticket_status'] = $ticket_status;
	
	if( $ticket_status == 3 || $ticket_status == 670 || $ticket_status == 69 ) {
		$save_enabled = false;
	}
}


ob_start();
/*
echo "Assign Agents for: ".$type;
echo "<br>";
print_r($item_ids);
echo "<br>Ticket ID Array: ";
print_r($ticket_id_array);
echo "<br>";
echo "Ticket Statuses: ";
//print_r($box_statuses);
print_r($ticket_status);
echo "<br>";
echo "Num of Items: ".$num_of_items;
echo "<br>";
echo "Is single: ".$is_single_item;
echo "<br>";
*/
//echo "<br>Fake Agents: ";
//print_r($fake_array_of_users);
?>
<div id='alert_status' class=''></div> 
<br>
<!--
<label class="wpsc_ct_field_label">Current Requestor: </label>
	<span id="modal_current_requestor" class=""><?php echo $current_requestor; ?></span>
<br>
-->

<div class="row">
	<div class="col-sm-4">
		<label class="wpsc_ct_field_label">Box Status: </label>
	</div>
	
	<div class="col-sm-8">
		<label class="wpsc_ct_field_label">Assign Agents: </label>
	</div>
</div>

<hr class='tight'>

<?php 
	if( $type == 'edit') {
		foreach( $box_statuses as $status) { 	
?>
		<div class="row zebra">
			<div class="col-sm-4">
				<label class="wpsc_ct_field_label"><?php echo $status->name; ?> </label>
			</div>
			
			<div class="col-sm-8">
	<!-- 			<label class="wpsc_ct_field_label">Search Digitization Staff: </label> -->
	
				<form id="frm_get_ticket_assign_agent">
					<div id="assigned_agent">
						<div class="form-group wpsc_display_assign_agent ">
						    <input class="form-control  wpsc_assign_agents ui-autocomplete-input term-<?php echo $status->term_id; ?>" name="assigned_agent"  type="text" autocomplete="off" placeholder="<?php _e('Search agent ...','supportcandy')?>" />
							<ui class="wpsp_filter_display_container"></ui>
						</div>
					</div>
					<div id="assigned_agents" class="form-group col-sm-12 term-<?php echo $status->term_id; ?>">
						<?php
						    if($is_single_item) {
							    foreach ( $assigned_agents['status'] as $term_id=>$agent_list ) {							    
								    if( $term_id == $status->term_id ) :
								    	foreach( $agent_list as $agent ) {
								    	
											$agent_name = get_term_meta( $agent, 'label', true);
											 	
											if($agent && $agent_name):
							?>
													<div class="form-group wpsp_filter_display_element wpsc_assign_agents ">
														<div class="flex-container staff-badge" style="">
															<?php echo htmlentities($agent_name)?><span class="staff-close" onclick="wpsc_remove_filter(this);remove_user(<?php echo $status->term_id; ?>);"><i class="fa fa-times"></i></span>
															  <input type="hidden" name="assigned_agent[<?php echo $status->term_id; ?>]" value="<?php echo htmlentities($agent) ?>" />
						<!-- 									  <input type="hidden" name="new_requestor" value="<?php echo htmlentities($agent) ?>" /> -->
														</div>
													</div>
							<?php
											endif;
										}
									endif;	
								}
							}
						?>
				  </div>
						<input type="hidden" name="action" value="wpsc_tickets" />
						<input type="hidden" name="setting_action" value="set_change_assign_agent" />
						<input type="hidden" name="recall_id" value="<?php echo htmlentities($recall_id) ?>" />
				</form>
			</div>
		</div>
<?php
		} 	
	}
?>




<?php 
	if( $type == 'view') {
		foreach( $box_statuses as $status) { 	
?>
		<div class="row zebra">
			<div class="col-sm-4">
				<label class="wpsc_ct_field_label label_center"><?php echo $status->name; ?> </label>
			</div>
			
			<div class="col-sm-8">
					<div id="assigned_agents" class="  term-<?php echo $status->term_id; ?>">
						<?php
						    if($is_single_item) {
							    foreach ( $assigned_agents['status'] as $term_id=>$agent_list ) {							    
								    if( $term_id == $status->term_id ) :
								    	foreach( $agent_list as $agent ) {
								    	
											$agent_name = get_term_meta( $agent, 'label', true);
											 	
											if($agent && $agent_name):
							?>
													<div class=" wpsp_filter_display_element wpsc_assign_agents ">
														<div class="flex-container staff-badge" style="">
															<?php echo htmlentities($agent_name)?>
														</div>
													</div>
							<?php
											endif;
										}
									endif;	
								}
							}
						?>

				  </div>

			</div>
		</div>
<?php
		} 	
	}
?>








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
	margin: 25px 0px 5px 0px;
}
</style>

<script>
jQuery(document).ready(function(){


	
	jQuery("input[name='assigned_agent']").keypress(function(e) {
		//Enter key
		if (e.which == 13) {
			return false;
		}
	});
	
	jQuery( ".wpsc_assign_agents" ).autocomplete({
			minLength: 0,
// 			appendTo: jQuery('.wpsc_assign_agents').parent(),
			appendTo: jQuery('.wpsc_assign_agents.term-68').parent(), //targeting any .term-xxx fixes type ahead issue. 
			source: function( request, response ) {
				var term = request.term;
				request = {
					action: 'wpsc_tickets',
					setting_action : 'filter_autocomplete',
					term : term,
					field : 'assigned_agent',
				}
				jQuery.getJSON( wpsc_admin.ajax_url, request, function( data, status, xhr ) {
					response(data);
				});
			},
			select: function (event, ui) {

				console.log('Focus: ');
				console.log( jQuery(':focus').prop("classList") );
				
				let list = jQuery(':focus').prop("classList");
				let the_term = '';
				list.forEach( function(y) {
					console.log(y);
					if ( y.startsWith('term-') ) {
						the_term = y.replace('term-','');
					}
				});
				 							
				html_str = get_display_user_html(ui.item.label, ui.item.flag_val, the_term);
// 				jQuery('#assigned_agent .wpsp_filter_display_container').append(html_str);
// 				jQuery('#assigned_agents').append(html_str);
 				jQuery('#assigned_agents.term-'+the_term+'').append(html_str);		
 				
 				// Enable / Disable Save based on PHP save enabled which denotes if the Box is savable.
 				var save_enabled = '<?php echo $save_enabled ?>';
				if( !save_enabled ) {
					console.log('save disabled');
					jQuery("#button_agent_submit").hide();
				} else {
					jQuery("#button_agent_submit").show();	
				}
				
				// Enable / Disable Save based on js save enabled which denotes if all of the fields are filled in
				var save_enabled_js = false;
				let term_id_array = <?php echo json_encode($term_id_array); ?>;
				let is_single = <?php echo json_encode($is_single_item); ?>;
				
				console.log('is single? ');
				console.log(is_single);
				term_id_array.forEach( function(x) {
					if( is_single ) {
						//console.log('is single is FALSE');
						let entries_per_term = jQuery("input[name='assigned_agent["+x+"]']").map(function(){return jQuery(this).val();}).get();	
						if( entries_per_term < 1) {
							save_enabled_js = false;
							//jQuery("#button_agent_submit").hide();
							return false;
						} else {
							save_enabled_js = true;
						}
					}
				});
				
				
			    jQuery(this).val(''); return false;
			}
	}).focus(function() {
			jQuery(this).autocomplete("search", "");
	});

	// Checks if ticket status of any item inhibits it's editing
	var items_and_status = <?php echo json_encode($ticket_id_array, true); ?>;
	var alerts_disabled = '<?php echo $alerts_disabled ?>';
	
	
	let error_message = '';
	let is_error = false;
	let error_count = 0;
	
	var subfolder_path = '<?php echo $subfolder_path ?>';
	let box_link_start = '<a href="'+subfolder_path+'/wp-admin/admin.php?page=boxdetails&pid=requestdetails&id=';
	let request_link_start = '<a href="'+subfolder_path+'/wp-admin/admin.php?page=wpsc-tickets&id=';	
	let link_mid = '" >';
	let link_end = '</a>';
	
	items_and_status.forEach( function(x) {
		
		if( x.ticket_status == 3 || x.ticket_status == 670 || x.ticket_status == 69 ) {
			is_error = true;
			error_count++;
			let ticket_id = get_containing_ticket(x.item_id);
// 			error_message += box_link_start+x.item_id+link_mid+x.item_id+link_end+', '; //for box link
			error_message += request_link_start+ticket_id+link_mid+x.item_id+link_end+', ';
		}
	});
	error_message = error_message.slice(0, -2); 
	
	if( is_error == true && !alerts_disabled ){
		if( error_count > 1 ) {
			var error_start = 'Boxes: ';
			var error_mid = '. The containing Request statuses are not editable.';
		} else {
			var error_start = 'Box: ';
			var error_mid = '. The containing Request status is not editable.';
		}
		const error_end = ' Saving Disabled.';
		message = error_start+error_message+error_mid+error_end;
		set_alert( 'danger', message );
	}
	
	var save_enabled = '<?php echo $save_enabled ?>';
	console.log('save enabled');
	if( !save_enabled ) {
		console.log('save disabled');
		jQuery("#button_agent_submit").hide();
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

	jQuery('#alert_status').html('<span class=" alert '+alert_style+'">'+message+'</span>'); //badge badge-danger
	jQuery('#alert_status').addClass('alert_spacing');	
	
}

function get_containing_ticket( box_folder_id ) {
	let num = box_folder_id.split("-").length - 1;
	console.log('The Num: '+num);
	
	if( num == 1 ) {
		let type = 'Box';
		let arr = box_folder_id.split("-");
// 		var ticket_id = parseInt(arr[0]);
		var ticket_id = arr[0];
	} else if( num == 3 ) {
		let type = 'Folder/File';
		let arr = box_folder_id.split("-");
// 		var ticket_id = parseInt(arr[0]);
		var ticket_id = arr[0];
	} else {
		let type = 'Error';
		let ticket_id = null;
	}
	
	return ticket_id;
}


function get_display_user_html(user_name, termmeta_user_val, term_id) {
	//console.log("in display_user");
// 	var requestor_list = jQuery("input[name='assigned_agent[]']").map(function(){return jQuery(this).val();}).get();
	var requestor_list = jQuery("input[name='assigned_agent["+term_id+"]']").map(function(){return jQuery(this).val();}).get();
	
	if( requestor_list.indexOf(termmeta_user_val.toString()) >= 0 ) {
		console.log('termmeta_user_val: '+termmeta_user_val+' is already listed');
		html_str = '';
	} else {

		var html_str = '<div class="form-group wpsp_filter_display_element wpsc_assign_agents ">'
						+'<div class="flex-container staff-badge" style="">'
							+user_name
							+'<span class="staff-close" onclick="wpsc_remove_filter(this);remove_user('+term_id+');"><i class="fa fa-times"></i></span>'
						+'<input type="hidden" name="assigned_agent['+term_id+']" value="'+termmeta_user_val+'" />'
						+'</div>'
					+'</div>';	

	}
			
	return html_str;		

}

function remove_user(term_id) {
	//if zero users remove save
	//if more than 1 user show save
	var requestor_list = jQuery("input[name='assigned_agent["+term_id+"]']").map(function(){return jQuery(this).val();}).get();
	let is_single_item = <?php echo json_encode($is_single_item); ?>;
	console.log('Remove user');
	console.log(requestor_list);
	console.log('length: '+requestor_list.length);
	console.log('single item? '+is_single_item);
	console.log('term_id: '+term_id);
	
	
	var save_enabled = '<?php echo $save_enabled ?>';
	console.log('Save Enabled? '+save_enabled);
	if( is_single_item ) {
		console.log('doing single item stuff');
// 		if( requestor_list.length > 0 && save_enabled) {
		if( save_enabled ) {
			console.log('show');
			jQuery("#button_agent_submit").show();
		} else {
			jQuery("#button_agent_submit").hide();
		}
	}
}



</script>

<?php

$body = ob_get_clean();

ob_start();

?>
<button type="button" class="btn wpsc_popup_close"  style="background-color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_text_color']?> !important;"   onclick="wpsc_modal_close();"><?php _e('Close','wpsc-export-ticket');?></button>
<button type="button" id="button_agent_submit" class="btn wpsc_popup_action" style="background-color:<?php echo $wpsc_appearance_modal_window['wpsc_action_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_modal_window['wpsc_action_button_text_color']?> !important;" onclick="wppatt_set_agents();"><?php _e('Save','supportcandy');?></button>

<script>
jQuery("#button_agent_submit").hide();

function wppatt_set_agents(){
	let item_ids = <?php echo json_encode($item_ids); ?>;	
	let term_id_array = <?php echo json_encode($term_id_array); ?>;
	let is_single_item = <?php echo json_encode($is_single_item); ?>;
	
	console.log('setting agents for items: ');
	console.log(item_ids);
	
	
	//OLD - start
	var new_requestors = jQuery("input[name='assigned_agent[]']").map(function(){return jQuery(this).val();}).get();
 	var old_requestors = <?php echo json_encode($old_assigned_agents); ?>;
 	//OLD -end
 	
 	let new_agents_array = [];
 	term_id_array.forEach( function(x) {
// 	 	console.log('x= ');
// 	 	console.log(x);
// 	 	console.log(jQuery("input[name='assigned_agent["+x+"]']"));
// 	 	new_agents_array.push(  jQuery("input[name='assigned_agent["+x+"]']").map(function(){return jQuery(this).val();}).get() );
	 	new_agents_array.push( {term:x, agents:jQuery("input[name='assigned_agent["+x+"]']").map(function(){return jQuery(this).val();}).get()} );	 	
 	});	
 
	//console.log(new_requestors);
	console.log('term id array: ');
	console.log(term_id_array);
	console.log('new agents array');
	console.log(new_agents_array);
	console.log('item_ids ');
	console.log(item_ids);
	
	// Another check to ensure you can't save 0 users
// 	if( new_requestors.length > 0 ) {
		jQuery.post(
		   '<?php echo WPPATT_PLUGIN_URL; ?>includes/admin/pages/scripts/update_box_status_agents.php',{
		    type: 'box_status_agents',
		    new_agents_array: new_agents_array,
		    item_ids: item_ids,
		    is_single_item: is_single_item,
		    recall_id: '<?php echo $recall_id ?>',
		    ticket_id: '<?php echo $ticket_id ?>',
		    new_requestors: new_requestors,
		    old_requestors: old_requestors
		}, 
	    function (response) {
			//alert('updated: '+response);
			console.log('The Response:');
			console.log(response);
			//window.location.reload();
	
	    });
//     }
	wpsc_modal_close();
} 
</script>



<?php 
$footer = ob_get_clean();

$output = array(
  'body'   => $body,
  'footer' => $footer
);
echo json_encode($output);

