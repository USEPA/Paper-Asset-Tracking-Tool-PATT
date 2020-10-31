<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


global $current_user, $wpscfunction;
if (!($current_user->ID && $current_user->has_cap('wpsc_agent'))) {
	exit;
}

$ticket_id = isset($_POST['ticket_id']) ? sanitize_text_field($_POST['ticket_id']) : '' ;
$thread_id = isset($_POST['thread_id']) ? sanitize_text_field($_POST['thread_id']) : '' ;

$ticket_subject = $wpscfunction->get_ticket_fields($ticket_id,'ticket_subject');
$wpsc_appearance_modal_window = get_option('wpsc_modal_window');

ob_start();

?>

<form id="create_ticket_thread">
	
	<div class="form-group">
		<label class="wpsc_ct_field_label" for="Subject"><?php _e('Subject','supportcandy');?></label>
		<input type="text" class="form-control" id="subject" name="subject" value="<?php echo htmlentities(stripslashes(htmlspecialchars_decode($ticket_subject, ENT_QUOTES)))?>" />
	</div>

	<input type="hidden" name="action" value="wpsc_tickets" />
	<input type="hidden" name="setting_action" value="set_thread_ticket" />
	<input type="hidden" name="ticket_id" value="<?php echo htmlentities($ticket_id) ?>" />
	<input type="hidden" name="thread_id" value="<?php echo htmlentities($thread_id)?>" />
    
</form>

<?php 
$body = ob_get_clean();

ob_start();

?>
<div class="row">
    <div class="pull-right">
			<button type="button" class="btn wpsc_popup_close"  style="background-color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_text_color']?> !important;"    onclick="wpsc_modal_close();"><?php _e('Close','supportcandy');?></button>
			<button type="button" class="btn wpsc_popup_action" style="width:140px;" onclick="javascript:wpsc_set_new_ticket_thread();"><?php _e('Create New Ticket','supportcandy');?></button>
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