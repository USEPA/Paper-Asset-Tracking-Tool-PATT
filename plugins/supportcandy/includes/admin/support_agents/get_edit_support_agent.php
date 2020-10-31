<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user;
if (!($current_user->ID && $current_user->has_cap('edit_published_posts'))) {exit;}

$agent_id = isset($_POST) && isset($_POST['agent_id']) ? intval($_POST['agent_id']) : 0;
if (!$agent_id) {exit;}

$agent_role_id = get_term_meta( $agent_id, 'role', true);

$agent_role = get_option('wpsc_agent_role');

ob_start();
?>
<div class="form-group">
  <label for="wpsc_priority_color"><?php _e('Select Role','supportcandy');?></label>
  <p class="help-block"><?php _e('Select agent role. You can create new role in Agent Roles section if needed.','supportcandy');?></p>
  <select class="form-control" id="wpsc_agent_role" name="wpsc_agent_role">
    <?php foreach ($agent_role as $key => $value):?>
    	<option <?php echo $agent_role_id == $key ? 'selected="selected"':''?> value="<?php echo $key?>"><?php echo $value['label']?></option>
    <?php endforeach;?>
  </select>
</div>
<?php 
$body = ob_get_clean();
ob_start();
?>
<button type="button" class="btn wpsc_popup_close" onclick="wpsc_modal_close();"><?php _e('Close','supportcandy');?></button>
<button type="button" class="btn wpsc_popup_action" onclick="wpsc_set_edit_support_agent(<?php echo htmlentities($agent_id)?>);"><?php _e('Submit','supportcandy');?></button>
<?php 
$footer = ob_get_clean();

$output = array(
  'body'   => $body,
  'footer' => $footer
);

echo json_encode($output);
