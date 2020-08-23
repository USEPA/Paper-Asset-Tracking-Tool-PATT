<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user, $wpscfunction;
if (!($current_user->ID && $current_user->has_cap('edit_published_posts'))) {exit;}

$field_id = isset($_POST) && isset($_POST['field_id']) ? intval($_POST['field_id']) : 0;
if (!$field_id) {exit;}

$custom_field =  get_term_by('id', $field_id, 'wpsc_ticket_custom_fields');

$wpsc_tf_label = get_term_meta( $custom_field->term_id, 'wpsc_tf_label', true);
$wpsc_tf_extra_info = get_term_meta( $custom_field->term_id, 'wpsc_tf_extra_info', true);
$wpsc_tf_type = get_term_meta( $custom_field->term_id, 'wpsc_tf_type', true);
$wpsc_tf_has_options = 0;
$wpsc_tf_options = get_term_meta( $custom_field->term_id, 'wpsc_tf_options', true);
$wpsc_tf_personal_info   = get_term_meta( $custom_field->term_id, 'wpsc_tf_personal_info', true);
$wpsc_tf_limit = get_term_meta( $custom_field->term_id, 'wpsc_tf_limit',true);
$wpsc_date_range = get_term_meta($custom_field->term_id, 'wpsc_date_range', true);
$wpsc_time_format = get_term_meta($custom_field->term_id, 'wpsc_time_format', true);
$tf_types = array(1,5,7,8,9);
$style    = '';
if(!in_array($wpsc_tf_type,$tf_types)){
	$style = "display:none";
}

// date range
$wpsc_date_field = 0 ;
if($wpsc_tf_type == '6' ){
	$wpsc_date_field = 1;
}

$field_types = $wpscfunction->get_custom_field_types();

//time field
$wpsc_time_field = 0;
if ($wpsc_tf_type == '21') {
	$wpsc_time_field = 1;
}

ob_start();
?>
<div class="form-group">
  <label for="wpsc_tf_label"><?php _e('Field Label','supportcandy');?></label>
  <p class="help-block"><?php _e('Insert field label. Please make sure label you are entering should not already exist.','supportcandy');?></p>
  <input id="wpsc_tf_label" class="form-control" name="wpsc_tf_label" value="<?php echo str_replace('"', "&quot;", stripcslashes($wpsc_tf_label))?>" />
</div>
<div class="form-group">
  <label for="wpsc_tf_extra_info"><?php _e('Extra Information','supportcandy');?></label>
  <p class="help-block"><?php _e('Extra information about the field. Useful if you want to give instructions or information about the field in create ticket from. Keep this empty if not needed.','supportcandy');?></p>
  <input id="wpsc_tf_extra_info" class="form-control" name="wpsc_tf_extra_info" value="<?php echo str_replace('"', "&quot;", stripcslashes($wpsc_tf_extra_info))?>" />
</div>
<div class="form-group">
  <label for="wpsc_tf_personal_info"><?php _e('Personal Information','supportcandy');?></label>
  <p class="help-block"><?php _e('Enable or disable personal information in ticket form.','supportcandy');?></p>
  <select id="wpsc_tf_personal_info" class="form-control" name="wpsc_tf_personal_info">
		<option <?php echo $wpsc_tf_personal_info == '1' ? 'selected="selected"' : ''?> value="1"><?php _e('Yes','supportcandy');?></option>
		<option <?php echo $wpsc_tf_personal_info == '0' ? 'selected="selected"' : ''?> value="0"><?php _e('No','supportcandy');?></option>
	</select>
</div>
<div class="form-group">
  <label for="wpsc_tf_type"><?php _e('Field Type','supportcandy');?></label>
  <p class="help-block"><?php _e('Select field type.','supportcandy');?></p>
  <select id="wpsc_tf_type" class="form-control" name="wpsc_tf_type">
    <?php foreach ($field_types as $key => $field) :
      if($key == 22) continue;
      $selected = $wpsc_tf_type == $key ? 'selected="selected"' : '';
      if ($wpsc_tf_type == $key && $field['has_options']) {
        $wpsc_tf_has_options = 1;
      }
      ?>
			<option <?php echo $selected?> data-options="<?php echo $field['has_options']?>" value="<?php echo $key?>"><?php echo $field['label']?></option>
		<?php endforeach;?>
	</select>
</div>
<div class="form-group" id="wpsc_tf_options_container" style="<?php echo !$wpsc_tf_has_options?'display:none;':'';?>">
  <label for="wpsc_tf_options"><?php _e('Field Options','supportcandy');?></label>
  <p class="help-block"><?php _e('Insert field options. New option should begin on new line.','supportcandy');?></p>
  <textarea style="height:80px !important;" id="wpsc_tf_options" class="form-control" name="wpsc_tf_options"><?php echo stripcslashes(implode('\n', $wpsc_tf_options));?></textarea>
</div>

<div class="form-group wpsc_edit_limit" id = "wpsc_add_limit" style = "<?php echo $style;?>">
  <label for="wpsc_tf_limit"><?php _e('Character Limit','supportcandy');?></label>
  <p class="help-block"><?php _e('Limit number of characters to be accepted for this field. Enter 0 for unlimited characters.','supportcandy');?></p>
  <input type = "number" id="wpsc_ao_limit" class="form-control" name="wpsc_ao_limit" value="<?php echo $wpsc_tf_limit ; ?>" />
</div>

<div class="form-group" id="wpsc_time_fields_container" style="<?php echo !$wpsc_time_field ? 'display:none;':'';?>">
  <label for="wpsc_time_content"><?php _e('Time Format','supportcandy');?></label>
  <p class="help-block"><?php _e('Please select the time format','supportcandy'); ?></p>
  <select id="wpsc_tf_time_format" class="form-control" name="wpsc_tf_time_format">
		<option <?php echo $wpsc_time_format == '12' ? 'selected="selected"' : ''?> value="12hrs"><?php _e('12 Hrs','supportcandy');?></option>
		<option <?php echo $wpsc_time_format == '24' ? 'selected="selected"' : ''?> value="24hrs"><?php _e('24 Hrs','supportcandy');?></option>
  </select>
</div>

<div class="form-group" id="wpsc_date_range_container" style="<?php echo !$wpsc_date_field ? 'display:none;':'';?>">
  <label for="wpsc_date_range"><?php _e('Allowed Dates','supportcandy');?></label>
  <p class="help-block"><?php _e('Please select date range.','supportcandy');?></p>
  <select id="wpsc_date_range" class="form-control" name="wpsc_date_range">
		<option <?php echo $wpsc_date_range == 'all' ? 'selected="selected"' : ''?> value="all"><?php _e('All','supportcandy');?></option>
		<option <?php echo $wpsc_date_range == 'past' ? 'selected="selected"' : ''?> value="past"><?php _e('Past','supportcandy');?></option>
		<option <?php echo $wpsc_date_range == 'future' ? 'selected="selected"' : ''?> value="future"><?php _e('Future','supportcandy');?></option>
	</select>
</div>

<script>
	jQuery('#wpsc_tf_type').change(function(){
		var has_options = jQuery('option:selected',this).data('options');
		if(has_options=='1'){
			jQuery('#wpsc_tf_options_container').show();
		} else {
			jQuery('#wpsc_tf_options_container').hide();
		}
		
		var option = Number(jQuery(this).val());
		var opt_arr = [1,5,7,8,9];
		if ( jQuery.inArray( option, opt_arr) >= 0) {
			jQuery('#wpsc_ao_limit').show();
		}else{
			jQuery('#wpsc_ao_limit').hide();
		}

  	var poption = Number(jQuery(this).val());
		var popt_arr = [6];
		if ( jQuery.inArray( poption, popt_arr) >= 0) {
			jQuery('#wpsc_date_range_container').show();
		}else{
			jQuery('#wpsc_date_range_container').hide();
		}

		if(option == 21){
			jQuery('#wpsc_time_fields_container').show();
		}else{
			jQuery('#wpsc_time_fields_container').hide();
		}
	});
</script>
<?php 
$body = ob_get_clean();
ob_start();
?>
<button type="button" class="btn wpsc_popup_close" onclick="wpsc_modal_close();"><?php _e('Close','supportcandy');?></button>
<button type="button" class="btn wpsc_popup_action" onclick="wpsc_set_edit_agentonly_field(<?php echo htmlentities($field_id)?>);"><?php _e('Submit','supportcandy');?></button>
<?php 
$footer = ob_get_clean();

$output = array(
  'body'   => $body,
  'footer' => $footer
);

echo json_encode($output);
