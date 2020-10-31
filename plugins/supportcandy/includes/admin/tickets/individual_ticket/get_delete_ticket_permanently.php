<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpdb, $current_user, $wpscfunction;

if (!($current_user->ID && $current_user->has_cap('wpsc_agent'))) {
		exit;
}

$ticket_id = isset($_POST['ticket_id']) ? sanitize_text_field($_POST['ticket_id']) : '' ;

ob_start();
?>
<form id="frm_delete_ticket">
  
    <div class="form-group">
        <p><?php _e('Are you sure to delete this ticket permanently?','supportcandy');?></p>
    </div>
    
    <input type="hidden" name="action" value="wpsc_tickets" />
    <input type="hidden" name="setting_action" value="set_delete_ticket_permanently" />
    <input type="hidden" name="ticket_id" value="<?php echo htmlentities($ticket_id) ?>" />
    
</form>

<?php

$body = ob_get_clean();

ob_start();

?>
<button type="button" class="btn wpsc_popup_close" onclick="wpsc_modal_close();"><?php _e('Cancel','supportcandy');?></button>
<button type="button" class="btn wpsc_popup_action" onclick="wpsc_set_delete_ticket_permanently();"><?php _e('Confirm','supportcandy');?></button>
<?php

$footer = ob_get_clean();

$response = array(
    'body'      => $body,
    'footer'    => $footer
);

echo json_encode($response);