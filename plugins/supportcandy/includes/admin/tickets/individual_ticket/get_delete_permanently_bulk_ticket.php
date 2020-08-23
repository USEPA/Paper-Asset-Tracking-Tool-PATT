<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user;

$ticket_id = isset($_POST['ticket_id']) ? sanitize_text_field($_POST['ticket_id']) : '';

ob_start();

?>
<form id="frm_bulk_delete_ticket">
    <div class="form-group">
            <?php if ($ticket_id != '') { ?>
        <p><?php _e('Are you sure to delete these tickets permanently?','supportcandy');?></p>
        <?php } else { ?>
        <p>Please select a request to permanently delete.</p>
        <?php } ?>
    </div>
    
    <input type="hidden" name="action" value="wpsc_tickets" />
    <input type="hidden" name="setting_action" value="set_delete_permanently_bulk_ticket" />
    <input type="hidden" name="ticket_id" value="<?php echo htmlentities($ticket_id) ?>" />
</form>

<?php

$body = ob_get_clean();

ob_start();

?>
<button type="button" class="btn wpsc_popup_close" onclick="wpsc_modal_close();"><?php _e('Cancel','supportcandy');?></button>
        <?php if ($ticket_id != '') { ?>
        <button type="button" class="btn wpsc_popup_action" onclick="wpsc_set_delete_permanently_bulk_ticket();window.location.reload();"><?php _e('Confirm','supportcandy');?></button>
        <?php } ?>
<?php

$footer = ob_get_clean();

$response = array(
    'body'      => $body,
    'footer'    => $footer
);

echo json_encode($response);