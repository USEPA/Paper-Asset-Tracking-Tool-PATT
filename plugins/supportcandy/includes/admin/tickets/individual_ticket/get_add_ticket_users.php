<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user, $wpscfunction;

$ticket_id 			= isset($_POST['ticket_id']) ? sanitize_text_field($_POST['ticket_id']) : '' ;
$extra_users_emails = $wpscfunction->get_ticket_meta($ticket_id , 'extra_ticket_users');
$wpsc_appearance_modal_window = get_option('wpsc_modal_window');

$wpsc_custom_widget_localize = get_option('wpsc_custom_widget_localize');
$widget = get_term_by('slug', 'additional-recepients','wpsc_ticket_widget');	
$ticket_widget_name = $wpsc_custom_widget_localize['custom_widget_'.$widget->term_id];
ob_start();

?>
<form id="frm_get_add_ticket_users"  method="post">
	<div class="row" style="padding-left:2px;">
		<div class="form-group ">
	    	<label class="wpsc_ct_field_label" for="wpsc_ticket_et_user"><?php echo $ticket_widget_name;?> </label>
				<p class="help-block"><?php _e('(Optional) Enter additional recepient email addresses to whom you want to send email notifications as raised by user. One email per line.','supportcandy');?></p>           
				<textarea  class="wpsc_textarea" name="wpsc_ticket_et_user" id="wpsc_ticket_et_user"><?php echo stripcslashes(implode('\n', $extra_users_emails))?></textarea>
   	</div>
	</div>
		<?php  do_action('wpsc_add_extra_ticket_users',$ticket_id)?>
		<input type="hidden" name="action" value="wpsc_tickets" />
		<input type="hidden" name="setting_action" value="set_add_ticket_users" />
		<input type="hidden" name="ticket_id" value="<?php echo htmlentities($ticket_id) ?>" />
			
</form>
	
<?php 
$body = ob_get_clean();
ob_start();
?>
<button type="button" class="btn wpsc_popup_close"  style="background-color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_text_color']?> !important;"    onclick="wpsc_modal_close();"><?php _e('Close','supportcandy');?></button>
<button type="button" class="btn wpsc_popup_action" style="background-color:<?php echo $wpsc_appearance_modal_window['wpsc_action_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_modal_window['wpsc_action_button_text_color']?> !important;" onclick="wpsc_set_add_ticket_users(<?php echo $ticket_id ?>);"><?php _e('Save','supportcandy');?></button>
<?php 
$footer = ob_get_clean();

$output = array(
  'body'   => $body,
  'footer' => $footer
);

echo json_encode($output);

	