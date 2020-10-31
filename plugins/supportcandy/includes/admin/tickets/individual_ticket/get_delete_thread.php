<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user, $wpscfunction;
if (!($current_user->ID && $current_user->has_cap('wpsc_agent'))) {
	exit;
}

$ticket_id    = isset($_POST['ticket_id']) ? sanitize_text_field($_POST['ticket_id']) : 0 ;
$thread_id  = isset($_POST['thread_id']) ? sanitize_text_field($_POST['thread_id']) : 0 ;
$wpsc_appearance_modal_window = get_option('wpsc_modal_window');

ob_start();

?>

<form id="frm_delete_thread">
    
    <p><?php _e('Are you sure to delete this thread?','supportcandy');?></p>
    
    <input type="hidden" name="action" value="wpsc_tickets" />
    <input type="hidden" name="setting_action" value="set_delete_thread" />
    <input type="hidden" name="ticket_id" value="<?php echo htmlentities($ticket_id)?>" />
    <input type="hidden" name="thread_id" value="<?php echo htmlentities($thread_id)?>" />
    
</form>

<?php 
$body = ob_get_clean();

ob_start();

?>
<div class="row">
    <div class="col-md-12" style="text-align: right;">
			<button type="button" class="btn wpsc_popup_close"  style="background-color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_text_color']?> !important;"    onclick="wpsc_modal_close();"><?php _e('Cancel','supportcandy');?></button>
			<button type="button" class="btn wpsc_popup_action" style="background-color:<?php echo $wpsc_appearance_modal_window['wpsc_action_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_modal_window['wpsc_action_button_text_color']?> !important;"  onclick="wpsc_set_delete_thread(<?php echo htmlentities($ticket_id) ?>);"><?php _e('Confirm','supportcandy');?></button>
  </div>
</div>

<?php
$footer = ob_get_clean();

$output = array(
  'body'   => $body,
  'footer' => $footer
);

echo json_encode($output);
?>