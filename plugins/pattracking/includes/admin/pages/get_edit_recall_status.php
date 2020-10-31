<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user;
if (!($current_user->ID && $current_user->has_cap('manage_options'))) {exit;}

$status_id = isset($_POST) && isset($_POST['status_id']) ? intval($_POST['status_id']) : 0;
if (!$status_id) {exit;}

// $status = get_term_by('id', $status_id, 'wppatt_recall_statuses');
// $status = get_term( $status_id, 'wppatt_recall_statuses');
//$color = get_term_meta( $status->term_id, 'wppatt_recall_status_color', true);
$color = get_term_meta( $status_id, 'wppatt_recall_status_color', true);
//$backgound_color = get_term_meta( $status->term_id, 'wppatt_recall_status_background_color', true);
$backgound_color = get_term_meta( $status_id, 'wppatt_recall_status_background_color', true);


ob_start();
/*
echo 'Debug:<br>';
echo 'status_id: '.$status_id.'<br>';
print_r($status);
echo '<br>status: '.$status.'<br>';
echo 'color: '.$color.'<br>';
echo 'background: '.$backgound_color.'<br>';
*/

?>
<!--
<div class="form-group">
  <label for="wpsc_status_name">Recall Status Name</label>
  <p class="help-block"><?php _e('Insert status name. Please make sure status name you are entering should not already exist.','supportcandy');?></p>
  <input id="wpsc_status_name" class="form-control" name="wpsc_status_name" value="" />
</div>
-->
<div class="form-group">
  <label for="wpsc_status_color"><?php _e('Color','supportcandy');?></label>
  <p class="help-block"><?php _e('Text color of status.','supportcandy');?></p>
  <input id="wpsc_status_color" class="wpsc_color_picker" name="wpsc_status_color" value="<?php echo $color?>" />
</div>
<div class="form-group">
  <label for="wpsc_status_bg_color"><?php _e('Background Color','supportcandy');?></label>
  <p class="help-block"><?php _e('Background color of status.','supportcandy');?></p>
  <input id="wpsc_status_bg_color" class="wpsc_color_picker" name="wpsc_status_bg_color" value="<?php echo $backgound_color?>" />
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
<button type="button" class="btn wpsc_popup_action" onclick="wpsc_set_edit_status(<?php echo htmlentities($status_id)?>);"><?php _e('Submit','supportcandy');?></button>
<?php 
$footer = ob_get_clean();

$output = array(
  'body'   => $body,
  'footer' => $footer
);

echo json_encode($output);
