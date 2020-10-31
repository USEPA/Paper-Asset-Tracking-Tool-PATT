
<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
// set default filter for agents and customers //not true
/*
global $current_user, $wpscfunction;
if (!$current_user->ID) die();
*/

global $current_user, $wpscfunction;
if (!($current_user->ID && $current_user->has_cap('wpsc_agent'))) {
	exit;
}

$ticket_id   = isset($_POST['ticket_id']) ? sanitize_text_field($_POST['ticket_id']) : '' ;
$current_requestor = isset($_POST['requestor']) ? sanitize_text_field($_POST['requestor']) : '' ;
//$ticket_id = 1;
$wpsc_appearance_modal_window = get_option('wpsc_modal_window');

//$setting_action = isset($_POST['setting_action']) ? sanitize_text_field($_POST['setting_action']) : '';
$recall_id = isset($_POST['recall_id']) ? sanitize_text_field($_POST['recall_id']) : '';


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

//$key = array_search($current_user->ID, array_column($agent_ids, 'wp_user_id'));
//$agent_term_id = $agent_ids[$key]['agent_term_id']; //current user agent term id


// OLD from TICKETS
//$assigned_agents = $wpscfunction->get_ticket_meta($ticket_id,'assigned_agent');


//echo "Assigned Agents: ";
//print_r($assigned_agents);

//need function that grabs the requestors from recall_id
//$recall_index = $GLOBALS['recall_id'] - 8;
//$fake_array_of_users = [1, 2, 4, 5, 6, 7];



ob_start();
//echo "Recall Requestor for: ".$recall_id;
//echo "<br>Assigned Agents: ";
//print_r($assigned_agents);
//echo "<br>ticket id: ".$ticket_id;
//echo "<br>Fake Agents: ";
//print_r($fake_array_of_users);
?>

<br>
<!--
<label class="wpsc_ct_field_label">Current Requestor: </label>
	<span id="modal_current_requestor" class=""><?php echo $current_requestor; ?></span>
<br>
-->
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
/*
				var html_str = '<li class="wpsp_filter_display_element">'
												+'<div class="flex-container">'
													+'<div class="wpsp_filter_display_text">'
														+ui.item.label
														+'<input type="hidden" name="assigned_agent[]" value="'+ui.item.flag_val+'">'
// 														+'<input type="hidden" name="new_requestor" value="'+ui.item.flag_val+'">'
													+'</div>'
													+'<div class="wpsp_filter_display_remove" onclick="wpsc_remove_filter(this);"><i class="fa fa-times"></i></div>'
												+'</div>'
											+'</li>';
*/
											
				html_str = get_display_user_html(ui.item.label, ui.item.flag_val);
// 				jQuery('#assigned_agent .wpsp_filter_display_container').append(html_str);
				jQuery('#assigned_agents').append(html_str);
				
				
// 				jQuery('#assigned_agent .wpsp_filter_display_container').replace(html_str);
				//Add code for only single user: https://stackoverflow.com/questions/22971580/jquery-append-element-if-it-doesnt-exist-otherwise-replace
				jQuery("#button_requestor_submit").show();
			    jQuery(this).val(''); return false;
			}
	}).focus(function() {
			jQuery(this).autocomplete("search", "");
	});

});

function get_display_user_html(user_name, termmeta_user_val) {
	//console.log("in display_user");
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
	var requestor_list = jQuery("input[name='assigned_agent[]']").map(function(){return jQuery(this).val();}).get();
	console.log(requestor_list);
	if( requestor_list.length > 0 ) {
		jQuery("#button_requestor_submit").show();
	} else {
		jQuery("#button_requestor_submit").hide();
	}
}

</script>

<?php

$body = ob_get_clean();

ob_start();

?>
<button type="button" class="btn wpsc_popup_close"  style="background-color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_text_color']?> !important;"   onclick="wpsc_modal_close();"><?php _e('Close','wpsc-export-ticket');?></button>
<button type="button" id="button_requestor_submit" class="btn wpsc_popup_action" style="background-color:<?php echo $wpsc_appearance_modal_window['wpsc_action_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_modal_window['wpsc_action_button_text_color']?> !important;" onclick="wppatt_set_requestor();"><?php _e('Save','supportcandy');?></button>

<script>
jQuery("#button_requestor_submit").hide();

function wppatt_set_requestor(){	
	console.log('setting requestor for: '+ '<?php echo $recall_id ?>');
	
	var new_requestors = jQuery("input[name='assigned_agent[]']").map(function(){return jQuery(this).val();}).get();
 	var old_requestors = <?php echo json_encode($old_assigned_agents); ?>;
	console.log(new_requestors);
	
	// Another check to ensure you can't save 0 users
	if( new_requestors.length > 0 ) {
		jQuery.post(
		   '<?php echo WPPATT_PLUGIN_URL; ?>includes/admin/pages/scripts/update_recall_details.php',{
		    recall_id: '<?php echo $recall_id ?>',
		    ticket_id: '<?php echo $ticket_id ?>',
		    new_requestors: new_requestors,
		    old_requestors: old_requestors,
		    type: 'requestor'
		}, 
	    function (response) {
			//alert('updated: '+response);
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

