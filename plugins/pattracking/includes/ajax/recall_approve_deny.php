<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
// set default filter for agents and customers //not true
global $current_user, $wpscfunction;

if (!$current_user->ID) die();

$setting_action = isset($_POST['setting_action']) ? sanitize_text_field($_POST['setting_action']) : '';
$recall_id = isset($_POST['recall_id']) ? sanitize_text_field($_POST['recall_id']) : '';
$ticket_id = isset($_POST['ticket_id']) ? sanitize_text_field($_POST['ticket_id']) : '';
$type = isset($_POST['type']) ? sanitize_text_field($_POST['type']) : '';

$recall_ids = $_REQUEST['recall_ids']; 
$num_of_recalls = count($recall_ids);

$wpsc_appearance_modal_window = get_option('wpsc_modal_window');

$approve_message = 'Are you sure you want to <b>Approve</b> Recall ID: R-'.$recall_id.'? This action cannot be undone.';
$deny_message = 'Are you sure you want to <b>Deny</b> Recall ID: R-'.$recall_id.'? This action cannot be undone.';
$html_br = '<br>';
$html_hr = '<hr>';
//$html_set_alert = "<div id='alert_status' class=''></div>";

$recall_staff_meets_requirements_message = 'Assigned Staff for this Recall is valid.';
$recall_staff_digi_void = 'No Digitization Staff Member on this Recall. Please add a Digitization Staff Member below.';
$recall_staff_requester_void = 'No Requester on this Recall. Please add a Requester below.';

// Check that there is at least one requester and at least one agent/manager/admin in Recall. 


$where = [
	'recall_id' => $recall_id
];
$recall_data = Patt_Custom_Func::get_recall_data( $where );
$agent_id_array = Patt_Custom_Func::translate_user_id( $recall_data[0]->user_id, 'agent_term_id' );

// Check for
$role_array_digi_staff = [ 'Administrator', 'Manager', 'Agent' ];
$results_digi = Patt_Custom_Func::return_agent_ids_in_role( $agent_id_array, $role_array_digi_staff);

$role_array_requester = [  'Requester' ];
$results_requester = Patt_Custom_Func::return_agent_ids_in_role( $agent_id_array, $role_array_requester);

$recall_staff_meets_requirements = false;
$recall_staff_digi_valid = true;
$recall_staff_requester_valid = true;

if( count( $results_digi ) > 0 && count( $results_requester ) > 0 ) {
	$recall_staff_meets_requirements = true;
} 
if ( count( $results_digi ) == 0 ) {
	$recall_staff_digi_valid = false;
}
if ( count( $results_requester ) == 0 ) {
	$recall_staff_requester_valid = false;
}




ob_start();
// echo 'This has got to be something';
//echo "Recall ID: ".$recall_id."<br>";
//echo "Ticket ID: ".$ticket_id."<br>";
//echo "Type: ".$type."<br>";

if( $type == 'approve_recall' ) {
	echo $approve_message . $html_br;
	if( $recall_staff_meets_requirements == true ) {
		//echo $html_br . $recall_staff_meets_requirements_message . $html_br ;
		//set_alert( 'success', $recall_staff_meets_requirements_message );
	} elseif ( $recall_staff_digi_valid == false ) {
		//echo $html_br . $recall_staff_digi_void . $html_br . $html_hr;
		//set_alert( 'danger', $recall_staff_digi_void );
	} elseif ( $recall_staff_requester_valid == false ) {
		//echo $html_br . $recall_staff_requester_void . $html_br . $html_hr;
		//set_alert( 'danger', $recall_staff_requester_void );
	}
} elseif ( $type == 'deny_recall' ) {
	echo $deny_message . $html_br;		
}





// Display User selection portion of modal if need be.
if( ( $recall_staff_digi_valid == false || $recall_staff_requester_valid == false ) && $type == 'approve_recall' ) {
	
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

	//ob_start();
	//echo "Recall Requestor for: ".$recall_id;
	//echo "<br>Assigned Agents: ";
	//print_r($assigned_agents);
	//echo "<br>ticket id: ".$ticket_id;
	//echo "<br>Fake Agents: ";
	//print_r($fake_array_of_users);
	?>
	<div id='alert_status_modal' class=''></div>
	<label class="wpsc_ct_field_label">Search Digitization Staff: </label>
	<br>
	
	<form id="frm_get_ticket_assign_agent">
		<div id="assigned_agent">
			<div class="form-group wpsc_display_assign_agent ">
			    <input class="form-control  wpsc_assign_agents ui-autocomplete-input" name="assigned_agent"  type="text" autocomplete="off" placeholder="<?php _e('Search agent ...','supportcandy')?>" />
					<ui class="wpsp_filter_display_container"></ui>
			</div>
		</div>
		<div id="assigned_agents" class="form-group col-md-12">
			<?php
			   foreach ( $assigned_agents as $agent ) {
					 $agent_name = get_term_meta( $agent, 'label', true);
					 	
						if($agent && $agent_name):
			 ?>
								<div class="form-group wpsp_filter_display_element wpsc_assign_agents ">
									<div class="flex-container" style="padding:10px;font-size:1.0em;">
										<?php echo htmlentities($agent_name)?><span onclick="wpsc_remove_filter(this);remove_user();"><i class="fa fa-times"></i></span>
										  <input type="hidden" name="assigned_agent[]" value="<?php echo htmlentities($agent) ?>" />
	<!-- 									  <input type="hidden" name="new_requestor" value="<?php echo htmlentities($agent) ?>" /> -->
									</div>
								</div>
			<?php
					endif;
				 }
			?>
	  </div>
			<input type="hidden" name="action" value="wpsc_tickets" />
			<input type="hidden" name="setting_action" value="set_change_assign_agent" />
			<input type="hidden" name="recall_id" value="<?php echo htmlentities($recall_id) ?>" />
	</form>
	
	<script>
	jQuery(document).ready(function(){
		
		let the_type = '<?php echo $type ?>';
		let recall_staff_meets_requirements = <?php echo json_encode( $recall_staff_meets_requirements ); ?>;
		let recall_staff_digi_valid = <?php echo json_encode( $recall_staff_digi_valid ); ?>;
		let recall_staff_requester_valid = <?php echo json_encode( $recall_staff_requester_valid ); ?>;				
		let recall_staff_meets_requirements_message = '<?php echo $recall_staff_meets_requirements_message ?>';
		let recall_staff_digi_void = '<?php echo $recall_staff_digi_void ?>';
		let recall_staff_requester_void = '<?php echo $recall_staff_requester_void ?>';
		
		console.log({the_type:the_type, recall_staff_meets_requirements:recall_staff_meets_requirements, recall_staff_digi_valid:recall_staff_digi_valid, recall_staff_meets_requirements_message:recall_staff_meets_requirements_message, recall_staff_digi_void:recall_staff_digi_void, recall_staff_requester_void:recall_staff_requester_void, recall_staff_requester_valid:recall_staff_requester_valid});
		
		
		// Simple hash function based on java's. Used for set_alert.
		String.prototype.hashCode = function(){
		    var hash = 0;
		    for (var i = 0; i < this.length; i++) {
		        var character = this.charCodeAt(i);
		        hash = ((hash<<5)-hash)+character;
		        hash = hash & hash; // Convert to 32bit integer
		    }
		    return hash;
		}
		
		if( the_type == 'approve_recall' ) {
			if( recall_staff_meets_requirements == true ) {
				set_alert( 'success', recall_staff_meets_requirements_message );
			} else if ( recall_staff_digi_valid == false ) {
				set_alert( 'danger', recall_staff_digi_void );
			} else if ( recall_staff_requester_valid == false ) {
 				set_alert( 'danger', recall_staff_requester_void );
			}
		} 
		
		
		jQuery("input[name='assigned_agent']").keypress(function(e) {
			//Enter key
			if (e.which == 13) {
				return false;
			}
		});
		
		
		jQuery( ".wpsc_assign_agents" ).autocomplete({
				minLength: 0,
				appendTo: jQuery('.wpsc_assign_agents').parent(),
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
													
					html_str = get_display_user_html(ui.item.label, ui.item.flag_val);
					jQuery('#assigned_agents').append(html_str);
					//jQuery("#button_requestor_submit").show();
				    jQuery(this).val(''); 
				    
				    
				    //var recall_id_array = <?php echo json_encode($recall_ids) ?>;
					let recall_id = '<?php echo $recall_id ?>';
					//var ticket_id = '<?php echo $ticket_id ?>';
					//var type = '<?php echo $type ?>';		
					//console.log({type:type,recall_id:recall_id,ticket_id:ticket_id });
					
					
					console.log({name:ui.item.label, agent_id:ui.item.flag_val, recall_id:recall_id });
					
					let requestor_list = jQuery("input[name='assigned_agent[]']").map(function(){return jQuery(this).val();}).get();
					requestor_list = requestor_list.map(Number);
					console.log({requestor_list_Add:requestor_list});
					
					
					jQuery.ajax({
						type: "POST",
						url: '<?php echo WPPATT_PLUGIN_URL; ?>includes/admin/pages/scripts/update_recall_details.php', 
						data: {
						    recall_id: recall_id,
						    type: 'check_assignment_balance',
 						    //new_agent_id: ui.item.flag_val
							agent_id_array: requestor_list
						}, 
						success: function( response ) {
							console.log('AJAX success: Check Assignement Balance')
							
							response = JSON.parse( response );
							
							console.log( response );
							console.log( response.staff_meets_requirements );
							console.log( response.staff_digi_vali );
							console.log( response.staff_requester_valid );
							
							if( response.staff_meets_requirements == true ) {
								set_alert( 'success', '<?php echo $recall_staff_meets_requirements_message; ?>' );
								jQuery('#button_confirm').show();
								
							} else if( response.staff_digi_vali == false ) {
								set_alert( 'danger', '<?php echo $recall_staff_digi_void; ?>' );
							} else if( response.staff_requester_valid == false ) {
								set_alert( 'danger', '<?php echo $recall_staff_requester_void; ?>' );
							} 
							

							
							
						},
						error: function( response ) {
							console.log('FAILED AJAX: Check Assignement Balance')
							console.log( response );		
							set_alert( 'danger', 'Check Balance Error' );
						}
					});
				    
				   
					
					
/*
					jQuery.post(
					   '<?php echo WPPATT_PLUGIN_URL; ?>includes/admin/pages/scripts/update_recall_details.php',{
					    recall_id: recall_id,
					    ticket_id: ticket_id,
					    type: 'check_assignment_balance'
					}, 
				    function (response) {
						alert('Recall ID: R-'+recall_id+' Approved. ');
						//alert(response);
						//window.location.reload();
				
				    });
*/
			    
				    
				    
				    return false;
				}
		}).focus(function() {
				jQuery(this).autocomplete("search", "");
		});
	
	});
	
	function get_display_user_html(user_name, termmeta_user_val) {
		var requestor_list = jQuery("input[name='assigned_agent[]']").map(function(){return jQuery(this).val();}).get();
		
		if( requestor_list.indexOf(termmeta_user_val.toString()) >= 0 ) {
			console.log('termmeta_user_val: '+termmeta_user_val+' is already listed');
			html_str = '';
		} else {
	
			var html_str = '<div class="form-group wpsp_filter_display_element wpsc_assign_agents ">'
							+'<div class="flex-container" style="padding:10px;font-size:1.0em;">'
								+user_name
								+'<span onclick="wpsc_remove_filter(this);remove_user();"><i class="fa fa-times"></i></span>'
							+'<input type="hidden" name="assigned_agent[]" value="'+termmeta_user_val+'" />'
							+'</div>'
						+'</div>';	
		}
				
		return html_str;
	}
	
	function remove_user() {
		//if zero users remove save
		//if more than 1 user show save
		let requestor_list = jQuery("input[name='assigned_agent[]']").map(function(){return jQuery(this).val();}).get();
		requestor_list = requestor_list.map(Number);
		console.log({requestor_list:requestor_list});
		

		jQuery.ajax({
			type: "POST",
			url: '<?php echo WPPATT_PLUGIN_URL; ?>includes/admin/pages/scripts/update_recall_details.php', 
			data: {
			    recall_id: recall_id,
			    type: 'check_assignment_balance',
			    agent_id_array: requestor_list
			}, 
			success: function( response ) {
				console.log('AJAX success: Check Assignement Balance')
				
				response = JSON.parse( response );
				
				console.log( response );
				console.log( response.staff_meets_requirements );
				console.log( response.staff_digi_vali );
				console.log( response.staff_requester_valid );
				
				if( response.staff_meets_requirements == true ) {
					set_alert( 'success', '<?php echo $recall_staff_meets_requirements_message; ?>' );
					jQuery('#button_confirm').show();

				} else if( response.staff_digi_vali == false ) {
					set_alert( 'danger', '<?php echo $recall_staff_digi_void; ?>' );
					jQuery('#button_confirm').hide();
				} else if( response.staff_requester_valid == false ) {
					set_alert( 'danger', '<?php echo $recall_staff_requester_void; ?>' );
					jQuery('#button_confirm').hide();
				} 
				
			},
			error: function( response ) {
				console.log('FAILED AJAX: Check Assignement Balance')
				console.log( response );		
				set_alert( 'danger', 'Check Balance Error' );
			}
		});

		
		
	}
	
	
	// Simple hash function based on java's. Used for set_alert.
	String.prototype.hashCode = function(){
	    var hash = 0;
	    for (var i = 0; i < this.length; i++) {
	        var character = this.charCodeAt(i);
	        hash = ((hash<<5)-hash)+character;
	        hash = hash & hash; // Convert to 32bit integer
	    }
	    return hash;
	}
	
	// Sets an alert
	function set_alert( type, message ) {
		console.log('typeof message: ' + typeof message );
		let alert_style = '';
		let hash = message.hashCode();
		console.log({hash:hash});
		
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
		jQuery('#alert_status_modal').show();
		//jQuery('#alert_status').html('<div class=" alert '+alert_style+'">'+message+'</div>'); //badge badge-danger
		jQuery('#alert_status_modal').html('<div id="alert-' + hash + '" class=" alert '+alert_style+'">'+message+'</div>'); //badge badge-danger
		jQuery('#alert_status_modal').addClass('alert_spacing');
		
		alert_dismiss( hash );
	}
	
	// Sets the time for dismissing the error notification
	function alert_dismiss( hash ) {
		//setTimeout(function(){ jQuery('#alert_status').fadeOut(1000); }, 9000);	
		setTimeout( function(){ jQuery( '#alert-'+hash ).fadeOut( 1000 ); }, 9000 );	
	}
	
	
	</script>
	
	<style>
		.alert_spacing {
			/* 	margin: 15px 0px 25px 15px; */
			margin: 0px 0px 25px 15px;
		}
	</style>

	
<?php	
	
} // End if display user additions






$body = ob_get_clean();
ob_start();
?>
<button type="button" class="btn wpsc_popup_close"  style="background-color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_text_color']?> !important;"   onclick="wpsc_modal_close();"><?php _e('No','wpsc-export-ticket');?></button>

<button type="button" id="button_confirm" class="btn wpsc_popup_action" style="background-color:<?php echo $wpsc_appearance_modal_window['wpsc_action_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_modal_window['wpsc_action_button_text_color']?> !important;" onclick="wppatt_approve_deny_recall( );"><?php _e('Yes','supportcandy');?></button>

<script>
	//id="button_cancel_yes"
	//jQuery("#button_status_submit").hide();
	//jQuery('#button_confirm').hide();
	var type = '<?php echo $type ?>';
	
	
	// turn off
	var old_requestor_list = jQuery("input[name='assigned_agent[]']").map(function(){return jQuery(this).val();}).get();
	old_requestor_list = old_requestor_list.map(Number);
	console.log({old_requestor_list:old_requestor_list});
	
	let all_staff_valid = <?php echo json_encode( $recall_staff_meets_requirements ); ?>;
	console.log({all_staff_valid:all_staff_valid});
	
	if( !all_staff_valid && type == 'approve_recall' ) {
		jQuery('#button_confirm').hide();
	} 
	
	jQuery('#status_dropdown').change(function() {	
		if( jQuery('#status_dropdown').val() =='') {
			jQuery("#button_status_submit").hide();						
		} else {
			jQuery("#button_status_submit").show();
		}
	})	

	function wppatt_approve_deny_recall(  ) {
		console.log('approve / deny ');
		

		//var recall_id_array = <?php echo json_encode($recall_ids) ?>;
		var recall_id = '<?php echo $recall_id ?>';
		var ticket_id = '<?php echo $ticket_id ?>';
		//var recall_staff_meets_req = <?php echo $recall_staff_meets_requirements ?>;
// 		var type = '<?php echo $type ?>';		
		type = '<?php echo $type ?>';		
		console.log({type:type,recall_id:recall_id,ticket_id:ticket_id });

//		console.log('recall id: '+recall_id);
//		console.log('ticket id: '+ticket_id);
		
		if( type == 'approve_recall' ) {
			console.log('approve_recall approve_recall');
			
			let requestor_list = jQuery("input[name='assigned_agent[]']").map(function(){return jQuery(this).val();}).get();
			requestor_list = requestor_list.map(Number);
			
			// If all_staff_valid is false, this means that a user needed to add users to get here. Therefore save these users before saving the approval. 
			// Submit the new list of requestors. On Success, submit the approval. 
			if( !all_staff_valid ) {
				jQuery.ajax({
					type: "POST",
					url: '<?php echo WPPATT_PLUGIN_URL; ?>includes/admin/pages/scripts/update_recall_details.php', 
					data: {
					    recall_id: recall_id,
					    ticket_id: ticket_id,
					    new_requestors: requestor_list,
					    old_requestors: old_requestor_list,
	   				    type: 'requestor'
					}, 
					success: function( response ) {
						console.log('AJAX success: Submit new Requestors')
						console.log({response:response});
						//response = JSON.parse( response );
						
						// Submit the approval. 
						jQuery.post(
						   '<?php echo WPPATT_PLUGIN_URL; ?>includes/admin/pages/scripts/update_recall_details.php',{
						    recall_id: recall_id,
						    ticket_id: ticket_id,
						    type: type
						}, 
					    function (response) {
							alert('Recall ID: R-'+recall_id+' Approved. ');
							//alert(response);
							window.location.reload();
					
					    });
	
						
						
					},
					error: function( response ) {
						console.log('FAILED AJAX: Check Assignement Balance')
						console.log( response );		
						set_alert( 'danger', 'Check Balance Error' );
					}
				});
			} else {
				// If all_staff_valid is true, this means that a user did not need to add users, therefore just save the approval. 
				// Submit the approval. 
				jQuery.post(
				   '<?php echo WPPATT_PLUGIN_URL; ?>includes/admin/pages/scripts/update_recall_details.php',{
				    recall_id: recall_id,
				    ticket_id: ticket_id,
				    type: type
				}, 
			    function (response) {
					alert('Recall ID: R-'+recall_id+' Approved. ');
					//alert(response);
					window.location.reload();
			
			    });
				
			}
			
			
			
			
			
						
		} else if ( type == 'deny_recall' ) {
			console.log('deny_recall deny_recall');	
			jQuery.post(
			   '<?php echo WPPATT_PLUGIN_URL; ?>includes/admin/pages/scripts/update_recall_details.php',{
			    recall_id: recall_id,
			    ticket_id: ticket_id,
			    type: type
			}, 
		    function (response) {
				alert('Recall ID: R-'+recall_id+' Denied. ');
				//alert(response);
				window.location.reload();
		
		    });		
		}
	
	    
	}
	
</script>


<?php 
$footer = ob_get_clean();

$output = array(
  'body'   => $body,
  'footer' => $footer
);
echo json_encode($output);	