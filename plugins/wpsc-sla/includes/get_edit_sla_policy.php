<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user, $wpscfunction;
if (!($current_user->ID && $current_user->has_cap('manage_options'))) {exit;}

$term_id = isset($_POST) && isset($_POST['term_id']) ? intval($_POST['term_id']) : 0;
if(!$term_id) exit;

$policy = get_term_by('id',$term_id,'wpsc_sla');

ob_start();
?>
<form id="wpsc_frm_add_sla" method="post" action="javascript:wpsc_set_edit_sla_policy();">
  <div class="form-group">
    <label for="wpsc_sla_policy_title"><?php _e('Title','wpsc-sla');?></label>
    <p class="help-block"><?php _e('Title to show in policy list. It will be easier to know what this policy is for.','wpsc-sla');?></p>
    <input id="wpsc_sla_policy_title" class="form-control" name="wpsc_sla_policy_title" value="<?php echo $policy->name?>" required />
  </div>
  <div class="form-group">
    <label for="wpsc_sla_policy_time"><?php _e('Time','wpsc-sla');?></label>
    <p class="help-block"><?php _e('Insert time.','wpsc-sla');?></p>
    <input type="number" id="wpsc_sla_policy_time" class="" name="wpsc_sla_policy_time" value="<?php echo get_term_meta($term_id,'time',true)?>" required />
    <?php $time_unit = get_term_meta($term_id,'time_unit',true)?>
    <select style="margin-top:-3px;" name="wpsc_sla_policy_time_unit">
      <option <?php echo $time_unit=='minutes'?'selected="selected"':''?> value="minutes"><?php _e('Minute','wpsc-sla');?></option>
      <option <?php echo $time_unit=='hours'?'selected="selected"':''?> value="hours"><?php _e('Hour','wpsc-sla');?></option>
      <option <?php echo $time_unit=='days'?'selected="selected"':''?> value="days"><?php _e('Day','wpsc-sla');?></option>
      <option <?php echo $time_unit=='months'?'selected="selected"':''?> value="months"><?php _e('Month','wpsc-sla');?></option>
      <option <?php echo $time_unit=='years'?'selected="selected"':''?> value="years"><?php _e('Year','wpsc-sla');?></option>
    </select>
  </div>
	
	<?php $conditions = get_term_meta($term_id,'conditions',true)?>
  <div class="form-group">
    <label for=""><?php _e('Conditions','wpsc-sla');?></label>
    <p class="help-block"><?php _e('Set conditions to match to apply this policy.','wpsc-sla');?></p>
		<?php $wpscfunction->load_conditions_ui('wpsc_edit_sla_conditions',$conditions);?>
  </div>
  <input type="hidden" name="action" value="wpsc_set_edit_sla_policy" />
  <input type="hidden" name="term_id" value="<?php echo $term_id?>" />
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
