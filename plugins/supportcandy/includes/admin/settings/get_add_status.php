<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user;
if (!($current_user->ID && $current_user->has_cap('edit_published_posts'))) {exit;}

ob_start();
?>
<div class="form-group">
  <label for="wpsc_status_name"><?php _e('Status Name','supportcandy');?></label>
  <p class="help-block"><?php _e('Insert status name. Please make sure status name you are entering should not already exist.','supportcandy');?></p>
  <input id="wpsc_status_name" class="form-control" name="wpsc_status_name" value="" />
</div>
<div class="form-group">
  <label for="wpsc_status_color"><?php _e('Color','supportcandy');?></label>
  <p class="help-block"><?php _e('Text color of status.','supportcandy');?></p>
  <input id="wpsc_status_color" class="wpsc_color_picker" name="wpsc_status_color" value="#ffffff" />
</div>
<div class="form-group">
  <label for="wpsc_status_bg_color"><?php _e('Background Color','supportcandy');?></label>
  <p class="help-block"><?php _e('Background color of status.','supportcandy');?></p>
  <input id="wpsc_status_bg_color" class="wpsc_color_picker" name="wpsc_status_bg_color" value="#1E90FF" />
</div>
<script>
  jQuery(document).ready(function(){
      jQuery('.wpsc_color_picker').wpColorPicker();
  });
</script>
<?php 
$body = ob_get_clean();
ob_start();
?>
<button type="button" class="btn wpsc_popup_close" onclick="wpsc_modal_close();"><?php _e('Close','supportcandy');?></button>
<button type="button" class="btn wpsc_popup_action" onclick="wpsc_set_add_status();"><?php _e('Submit','supportcandy');?></button>
<?php 
$footer = ob_get_clean();

$output = array(
  'body'   => $body,
  'footer' => $footer
);

echo json_encode($output);
