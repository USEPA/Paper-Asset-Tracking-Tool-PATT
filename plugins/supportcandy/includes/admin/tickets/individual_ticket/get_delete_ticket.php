<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user;

$ticket_id = isset($_POST['ticket_id']) ? sanitize_text_field($_POST['ticket_id']) : '';

$wpsc_appearance_modal_window = get_option('wpsc_modal_window');

ob_start();

?>
<form id="frm_delete_ticket">
    <div class="form-group">
        <p><?php _e('Are you sure to delete these tickets?','supportcandy');?></p>
    </div>
    
    <input type="hidden" name="action" value="wpsc_tickets" />
    <input type="hidden" name="setting_action" value="set_delete_ticket" />
    <input type="hidden" name="ticket_id" value="<?php echo htmlentities($ticket_id)?>" />
</form>

<?php

$body = ob_get_clean();

ob_start();

?>
<button type="button" class="btn wpsc_popup_close"  style="background-color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_text_color']?> !important;"   onclick="wpsc_modal_close();"><?php _e('Cancel','supportcandy');?></button>
<button type="button" class="btn wpsc_popup_action" style="background-color:<?php echo $wpsc_appearance_modal_window['wpsc_action_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_modal_window['wpsc_action_button_text_color']?> !important;" onclick="wpsc_set_delete_ticket();"><?php _e('Confirm','supportcandy');?></button>
<?php

$footer = ob_get_clean();

$response = array(
    'body'      => $body,
    'footer'    => $footer
);

echo json_encode($response);
