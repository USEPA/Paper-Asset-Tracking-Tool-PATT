<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user, $wpscfunction;
if (!($current_user->ID && $current_user->has_cap('wpsc_agent'))) {
	exit;
}

$ticket_id  = isset($_POST['ticket_id']) ? sanitize_text_field($_POST['ticket_id']) : '' ;

$wpsc_appearance_modal_window = get_option('wpsc_modal_window');
ob_start();

?>
<form id="frm_bulk_assigned_agents">
	<div id="assigned_agent">
	  <div class="form-group wpsc_display_assign_agent ">
		    <input class="form-control form-control wpsc_assign_agents ui-autocomplete-input" name="assigned_agents"  type="text" autocomplete="off" placeholder="<?php _e('Search agent ...','supportcandy')?>" />
				<ui class="wpsp_filter_display_container"></ui>
		</div>
	</div> 
	<input type="hidden" name="action" value="wpsc_tickets" />
	<input type="hidden" name="setting_action" value="set_bulk_assign_agent" />
	<input type="hidden" name="ticket_id" value="<?php echo htmlentities($ticket_id) ?>" />		
</form>

<script>
jQuery(document).ready(function(){
	
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
				var html_str = '<li class="wpsp_filter_display_element">'
												+'<div class="flex-container">'
													+'<div class="wpsp_filter_display_text">'
														+ui.item.label
														+'<input type="hidden" name="assigned_agent[]" value="'+ui.item.flag_val+'">'
													+'</div>'
													+'<div class="wpsp_filter_display_remove" onclick="wpsc_remove_filter(this);"><i class="fa fa-times"></i></div>'
												+'</div>'
											+'</li>';
				jQuery('#assigned_agent .wpsp_filter_display_container').append(html_str);
			  jQuery(this).val(''); return false;		
			}		
	}).focus(function() {
			jQuery(this).autocomplete("search", "");
	});
		
});
</script>

<?php

$body = ob_get_clean();

ob_start();

?>
<button type="button" class="btn wpsc_popup_close" style="background-color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_text_color']?> !important;"    onclick="wpsc_modal_close();"><?php _e('Close','supportcandy');?></button>
<button type="button" class="btn wpsc_popup_action" style="background-color:<?php echo $wpsc_appearance_modal_window['wpsc_action_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_modal_window['wpsc_action_button_text_color']?> !important;" onclick="wpsc_set_bulk_assign_agent();"><?php _e('Save','supportcandy');?></button>

<?php
$footer = ob_get_clean();

$output = array(
    'body'      => $body,
    'footer'    => $footer
);

echo json_encode($output);
