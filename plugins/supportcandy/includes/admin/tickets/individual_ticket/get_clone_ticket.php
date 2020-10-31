<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $current_user,$wpscfunction;

if (!($current_user->ID && $current_user->has_cap('wpsc_agent'))) {
 exit;
}

$ticket_id     = isset($_POST['ticket_id']) ? sanitize_text_field($_POST['ticket_id']) : '' ;
$ticket_subject     = $wpscfunction->get_ticket_fields($ticket_id,'ticket_subject');
$wpsc_appearance_modal_window = get_option('wpsc_modal_window');
ob_start();

?>
<form id="frm_edit_clone_subject">
  
    <div class="form-group">
      <input type="text" id="subject"class="form-control" name="subject" value="<?php echo stripslashes($ticket_subject); ?>"/>
    </div>
    <input type="hidden" name="action" value="wpsc_tickets" />
    <input type="hidden" name="setting_action" value="set_clone_ticket" />
    <input type="hidden" name="ticket_id" value="<?php echo htmlentities($ticket_id) ?>" />
</form>

<?php

$body = ob_get_clean();

ob_start();

?>
<button type="button" class="btn wpsc_popup_close"  style="background-color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_text_color']?> !important;"     onclick="wpsc_modal_close();"><?php _e('Cancel','supportcandy');?></button>
<button type="button" class="btn wpsc_popup_action" style="background-color:<?php echo $wpsc_appearance_modal_window['wpsc_action_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_modal_window['wpsc_action_button_text_color']?> !important;"  onclick="wpsc_set_clone_ticket();"><?php _e('Save Changes','supportcandy');?></button>
<?php

$footer = ob_get_clean();

$response = array(
    'body'      => $body,
    'footer'    => $footer
);

echo json_encode($response);
?>