<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user;
if (!($current_user->ID && $current_user->has_cap('edit_published_posts'))) {exit;}

$priority_id = isset($_POST) && isset($_POST['priority_id']) ? intval($_POST['priority_id']) : 0;
if (!$priority_id) {exit;}

$priority = get_term_by('id', $priority_id, 'wpsc_priorities');
$color = get_term_meta( $priority->term_id, 'wpsc_priority_color', true);
$backgound_color = get_term_meta( $priority->term_id, 'wpsc_priority_background_color', true);

ob_start();
?>
<div class="form-group">
  <label for="wpsc_priority_name"><?php _e('Priority Name','supportcandy');?></label>
  <p class="help-block"><?php _e('Insert priority name. Please make sure priority name you are entering should not already exist.','supportcandy');?></p>
  <input id="wpsc_priority_name" class="form-control" name="wpsc_priority_name" value="<?php echo $priority->name?>" />
</div>
<div class="form-group">
  <label for="wpsc_priority_color"><?php _e('Color','supportcandy');?></label>
  <p class="help-block"><?php _e('Text color of priority.','supportcandy');?></p>
  <input id="wpsc_priority_color" class="wpsc_color_picker" name="wpsc_priority_color" value="<?php echo $color?>" />
</div>
<div class="form-group">
  <label for="wpsc_priority_bg_color"><?php _e('Background Color','supportcandy');?></label>
  <p class="help-block"><?php _e('Background color of priority.','supportcandy');?></p>
  <input id="wpsc_priority_bg_color" class="wpsc_color_picker" name="wpsc_priority_bg_color" value="<?php echo $backgound_color?>" />
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
<button type="button" class="btn wpsc_popup_action" onclick="wpsc_set_edit_priority(<?php echo htmlentities($priority_id)?>);"><?php _e('Submit','supportcandy');?></button>
<?php 
$footer = ob_get_clean();

$output = array(
  'body'   => $body,
  'footer' => $footer
);

echo json_encode($output);
