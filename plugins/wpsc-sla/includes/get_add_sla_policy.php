<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user, $wpscfunction;
if (!($current_user->ID && $current_user->has_cap('manage_options'))) {exit;}

ob_start();
?>
<form id="wpsc_frm_add_sla" method="post" action="javascript:wpsc_set_add_sla_policy();">
  <div class="form-group">
    <label for="wpsc_sla_policy_title"><?php _e('Title','wpsc-sla');?></label>
    <p class="help-block"><?php _e('Title to show in policy list. It will be easier to know what this policy is for.','wpsc-sla');?></p>
    <input id="wpsc_sla_policy_title" class="form-control" name="wpsc_sla_policy_title" value="" required />
  </div>
  <div class="form-group">
    <label for="wpsc_sla_policy_time"><?php _e('Time','wpsc-sla');?></label>
    <p class="help-block"><?php _e('Insert time.','wpsc-sla');?></p>
    <input type="number" id="wpsc_sla_policy_time" class="" name="wpsc_sla_policy_time" value="" required />
    <select style="margin-top:-3px;" name="wpsc_sla_policy_time_unit">
      <option value="minutes"><?php _e('Minute','wpsc-sla');?></option>
      <option value="hours"><?php _e('Hour','wpsc-sla');?></option>
      <option value="days"><?php _e('Day','wpsc-sla');?></option>
      <option value="months"><?php _e('Month','wpsc-sla');?></option>
      <option value="years"><?php _e('Year','wpsc-sla');?></option>
    </select>
  </div>
  <div class="form-group">
    <label for=""><?php _e('Conditions','wpsc-sla');?></label>
    <p class="help-block"><?php _e('Set conditions to match to apply this policy.','wpsc-sla');?></p>
		<div class="row">
	  	<ul id="wpsc_tf_condition_container" class="wpsp_filter_display_container"></ul>
	  </div>
		<?php $wpscfunction->load_conditions_ui('wpsc_add_sla_conditions');?>
  </div>
  <input type="hidden" name="action" value="wpsc_set_add_sla_policy" />
</form>
<?php 
$body = ob_get_clean();
ob_start();
?>
<button type="button" class="btn wpsc_popup_close" onclick="wpsc_modal_close();"><?php _e('Close','wpsc');?></button>
<button type="button" class="btn wpsc_popup_action" onclick="jQuery('#wpsc_frm_add_sla').submit();"><?php _e('Submit','wpsc');?></button>
<?php 
$footer = ob_get_clean();

$output = array(
  'body'   => $body,
  'footer' => $footer
);

echo json_encode($output);
