<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user;
if (!($current_user->ID && $current_user->has_cap('edit_published_posts'))) {exit;}

$field_id = isset($_POST) && isset($_POST['field_id']) ? intval($_POST['field_id']) : 0;
if (!$field_id) {exit;}

$custom_field =  get_term_by('id', $field_id, 'wpsc_ticket_custom_fields');

$wpsc_tf_label = get_term_meta( $custom_field->term_id, 'wpsc_tf_label', true);
$wpsc_tf_extra_info = get_term_meta( $custom_field->term_id, 'wpsc_tf_extra_info', true);
$wpsc_tf_status = get_term_meta( $custom_field->term_id, 'wpsc_tf_status', true);
$wpsc_tf_width = get_term_meta( $custom_field->term_id, 'wpsc_tf_width', true);
$wpsc_limit = get_term_meta($custom_field->term_id,'wpsc_tf_limit',true);
$wpsc_tf_placeholder  = get_term_meta($custom_field->term_id,'wpsc_tf_placeholder_text',true);

$tf_types = array('ticket_category','ticket_priority');
$style    = '';
if(in_array($custom_field->slug,$tf_types)){
	$style = "display:none";
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
  <label for="wpsc_tf_width"><?php _e('Width','supportcandy');?></label>
  <p class="help-block"><?php _e('Please select width of the field in create ticket form.','supportcandy');?></p>
  <select id="wpsc_tf_width" class="form-control" name="wpsc_tf_width">
		<option <?php echo $wpsc_tf_width == '1/3' ? 'selected="selected"' : ''?> value="1/3"><?php _e('1/3 width of Row','supportcandy');?></option>
		<option <?php echo $wpsc_tf_width == '1/2' ? 'selected="selected"' : ''?> value="1/2"><?php _e('Half width of Row','supportcandy');?></option>
		<option <?php echo $wpsc_tf_width == '1' ? 'selected="selected"' : ''?> value="1"><?php _e('Full width of Row','supportcandy');?></option>
	</select>
</div>
<?php 
if ( !($custom_field->slug == 'customer_name' || $custom_field->slug == 'customer_email') ) {
  ?>
  <div class="form-group">
    <label for="wpsc_tf_status"><?php _e('Status','supportcandy');?></label>
    <p class="help-block"><?php _e('If disabled, will not be available in create ticket form. This will disable ability of customer to insert while creating ticket but agents can edit these values and remian visible in open ticket','supportcandy');?></p>
    <select id="wpsc_tf_status" class="form-control" name="wpsc_tf_status">
  		<option <?php echo $wpsc_tf_status == '1' ? 'selected="selected"' : ''?> value="1"><?php _e('Enable','supportcandy');?></option>
  		<option <?php echo $wpsc_tf_status == '0' ? 'selected="selected"' : ''?> value="0"><?php _e('Disable','supportcandy');?></option>
  	</select>
  </div>
  <?php
} else {
  ?>
  <input id="wpsc_tf_status" type="hidden" name="wpsc_tf_status" value="1">
  <?php
}

if($custom_field->slug == 'ticket_description'){
	$wpsc_default_desc = get_term_meta( $custom_field->term_id,'wpsc_tf_default_description',true);
	?>
		<div class="form-group default_text" id = "default_text_description" style = "<?php echo $wpsc_tf_status == '0'? 'display:block':'display:none';?>">
	    <label for="wpsc_tf_default_value"><?php _e('Default Text','supportcandy');?></label>
	    <p class="help-block"><?php _e('Default text for custom fields. If text is set, this text will appear in ticket after ticket creation. ','supportcandy');?></p>
	    <textarea id = "wpsc_tf_default_description" class = "form-control" name = "wpsc_tf_default_value" style="height:140px !important;"><?php echo $wpsc_default_desc ?></textarea>
	  </div> 
	<?php
}
if($custom_field->slug == 'ticket_subject'){
	$wpsc_default_sub = get_term_meta( $custom_field->term_id,'wpsc_tf_default_subject',true);
	?>
		<div class="form-group default_text" id = "default_text_subject" style = "<?php echo $wpsc_tf_status == '0'? 'display:block':'display:none';?>">
		<label for="wpsc_tf_default_value"><?php _e('Default Text','supportcandy');?></label>
		<p class="help-block"><?php _e('Default text for custom fields. If text is set, this text will appear in ticket after ticket creation. ','supportcandy');?></p>
		<input type = "text" id = "wpsc_tf_default_subject" class = "form-control" name = "wpsc_tf_default_value" value = "<?php echo $wpsc_default_sub ?>"/>
	</div> 
	<?php
}

if($custom_field->slug != 'ticket_description') {?>
	<div class="form-group" style="<?php echo $style ?>">
		<label for="wpsc_tf_limit"><?php _e('Character Limit','supportcandy');?></label>
  	<p class="help-block"><?php _e('Limit number of characters to be accepted for this field. Enter 0 for unlimited characters.','supportcandy');?></p>
  	<input type = "number" id="wpsc_tf_limit" class="form-control" name="wpsc_tf_limit" value="<?php echo htmlentities($wpsc_limit) ?>" />
	</div>
<?php } ?>
<?php  if ( $custom_field->slug == 'customer_name' || $custom_field->slug == 'customer_email' || $custom_field->slug == 'ticket_subject' || $custom_field->slug == 'ticket_description' ) { ?>
<div id= "wpsc_tf_placeholder" class="form-group" >
	<label for="wpsc_tf_placeholder_text"><?php _e('Placeholder Text','supportcandy');?></label>
	<p class="help-block"><?php _e('Enter the placeholder text.','supportcandy');?></p>
	<input id="wpsc_tf_placeholder_text" class="form-control" name="wpsc_tf_placeholder_text" value="<?php echo $wpsc_tf_placeholder; ?>" />
</div>
<?php } ?>
<?php

$body = ob_get_clean();
ob_start();
?>
<script type = "text/javascript">

	jQuery('#wpsc_tf_status').on('change',function(){
		if(jQuery('#wpsc_tf_status').val() == '0'){
			jQuery('.default_text').show();
		}else{
			jQuery('.default_text').hide();
		}
	});
	
</script>
<button type="button" class="btn wpsc_popup_close" onclick="wpsc_modal_close();"><?php _e('Close','supportcandy');?></button>
<button type="button" class="btn wpsc_popup_action" onclick="wpsc_set_edit_default_form_field(<?php echo htmlentities($field_id)?>);"><?php _e('Submit','supportcandy');?></button>
<?php 
$footer = ob_get_clean();

$output = array(
  'body'   => $body,
  'footer' => $footer
);

echo json_encode($output);
