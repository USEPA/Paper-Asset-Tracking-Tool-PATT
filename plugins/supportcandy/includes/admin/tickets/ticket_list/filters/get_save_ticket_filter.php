<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user, $wpscfunction;
if (!($current_user->ID )) {exit;}

ob_start();
?>
<div class="form-group">
  <label for="wpsc_tf_label"><?php _e('Filter label','supportcandy');?></label>
  <input type="text" id="wpsc_filter_label" class="form-control" name="wpsc_filter_label" value="" required />
</div>
<?php 

$body = ob_get_clean();
ob_start();
?>
<button type="button" class="btn wpsc_popup_close" onclick="wpsc_modal_close();"><?php _e('Close','supportcandy');?></button>
<button type="button" class="btn wpsc_popup_action" onclick="wpsc_set_save_ticket_filter();"><?php _e('Submit','supportcandy');?></button>
<?php 
$footer = ob_get_clean();

$output = array(
  'body'   => $body,
  'footer' => $footer
);

echo json_encode($output);