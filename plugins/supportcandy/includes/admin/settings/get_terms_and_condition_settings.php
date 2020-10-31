<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user, $wpdb,$wpscfunction;
if (!($current_user->ID && $current_user->has_cap('edit_published_posts'))) {
	exit;
}
$wpsc_gdpr_html= get_option('wpsc_gdpr_html');
$wpsc_terms_and_conditions_html=get_option('wpsc_terms_and_conditions_html');
$wpsc_personal_data_retention_period_time = get_option('wpsc_personal_data_retention_period_time');

$directionality = $wpscfunction->check_rtl();
?>

<form id="wpsc_terms_and_cond_settings" method="post" action="javascript:wpsc_set_terms_and_condition_settings();">
  
  
  <div class="form-group">
    <label for="wpsc_terms_and_conditions"><?php _e('Terms & Conditions','supportcandy');?></label>
    <p class="help-block"><?php _e("Enable or disable Terms and Condition ticket form.","supportcandy");?></p>
    <select class="form-control" name="wpsc_terms_and_conditions" id="wpsc_terms_and_conditions">
      <?php
      $wpsc_terms_and_conditions = get_option('wpsc_terms_and_conditions');
      $selected = $wpsc_terms_and_conditions == '1' ? 'selected="selected"' : '';
      echo '<option '.$selected.' value="1">'.__('Enable','supportcandy').'</option>';
      $selected = $wpsc_terms_and_conditions == '0' ? 'selected="selected"' : '';
      echo '<option '.$selected.' value="0">'.__('Disable','supportcandy').'</option>';
      ?>
    </select>
  </div>
	
	<div class="form-group">
		<label for="wpsc_terms_and_conditions_html"><?php _e('Terms and Conditions Text','supportcandy');?></label>
		<p class="help-block"><?php _e("Text to show on create ticket page.","supportcandy");?></p>
      <div class="text-right">
				 <button id="visual" class="wpsc-switch-editor wpsc-switch-editor-active visual term" type="button" onclick="wpsc_get_tinymce_term('wpsc_terms_and_conditions_html','term_body');"><?php _e('Visual', 'supportcandy');?></button>
				 <button id="text" class="wpsc-switch-editor text term" type="button" onclick="wpsc_get_textarea_term('wpsc_terms_and_conditions_html')"><?php _e('Text', 'supportcandy');?></button>
      </div>
		  <textarea class="form-control" style="height:100px !important;"  name="wpsc_terms_and_conditions_html" id="wpsc_terms_and_conditions_html"><?php echo html_entity_decode(stripcslashes(get_option('wpsc_terms_and_conditions_html')));?></textarea>		
	</div>
	
	<div class="form-group">
    <label for="wpsc_gdpr_settings"><?php _e('GDPR','supportcandy');?></label>
    <p class="help-block"><?php _e('Enable or disable GDPR condition on ticket form.','supportcandy');?></p>
    <select class="form-control" name="wpsc_gdpr_settings" id="wpsc_gdpr_settings">
      <?php
      $wpsc_set_in_gdpr = get_option('wpsc_set_in_gdpr');			
      $selected_gdpr = $wpsc_set_in_gdpr == '1' ? 'selected="selected"' : '';		
      echo '<option '.$selected_gdpr.' value="1">'.__('Enable','supportcandy').'</option>';
      $selected_gdpr = $wpsc_set_in_gdpr == '0' ? 'selected="selected"' : '';
      echo '<option '.$selected_gdpr.' value="0">'.__('Disable','supportcandy').'</option>';
      ?>
    </select>
  </div>
	
	<div class="form-group">
		<label for="wpsc_gdpr_html"><?php _e('GDPR Text','supportcandy');?></label>
		<p class="help-block"><?php _e("Text to show on create ticket page.","supportcandy");?></p>
      <div class="text-right">
			  	<button id="visual1" class="wpsc-switch-editor wpsc-switch-editor-active visual gdpr" type="button" onclick="wpsc_get_tinymce_gdpr('wpsc_gdpr_html','gdpr_body');"><?php _e('Visual', 'supportcandy');?></button>
				  <button id="text2" class="wpsc-switch-editor text gdpr" type="button" onclick="wpsc_get_textarea_gdpr('wpsc_gdpr_html')"><?php _e('Text', 'supportcandy');?></button>
      </div>
		  <textarea style="height:100px !important;" class="form-control" name="wpsc_gdpr_html" id="wpsc_gdpr_html"><?php echo html_entity_decode(stripcslashes(get_option('wpsc_gdpr_html')));?></textarea>		
	</div>
	
	<div class="form-group">
    <label for="wpsc_personal_data_retention_period"><?php _e('Personal Data Retention','supportcandy');?></label>
    <p class="help-block"><?php _e('Enable or disable personal data retention policy .','supportcandy');?></p>
		<select class="form-control" name="wpsc_personal_data_retention_type" id="wpsc_personal_data_retention_type">
			<?php
			$wpsc_personal_data_retention_type = get_option('wpsc_personal_data_retention_type');
			$selected = $wpsc_personal_data_retention_type == 'disable' ? 'selected="selected"' : '';
			echo '<option '.$selected.' value="disable">'.__('Disable','supportcandy').'</option>';
			$selected = $wpsc_personal_data_retention_type == 'enable' ? 'selected="selected"' : '';
			echo '<option '.$selected.' value="enable">'.__('Enable','supportcandy').'</option>';
			?>
    </select>
  </div>
	
	<div class="form-group" id="wpsc_personal_data_retention_period_div" style="display:none;" >
    <label for="wpsc_personal_data_retention_period"><?php _e('Set retention period','supportcandy');?></label>
    <p class="help-block"><?php _e('Set time for which personal data should be retained.','supportcandy');?></p>
		<div class="row">
			<div class="col-sm-6" style="padding-left:0px !important;">
					<input type="number" id="wpsc_personal_data_retention_period_time" class="form-control" name="wpsc_personal_data_retention_period_time" value="<?php echo $wpsc_personal_data_retention_period_time?>" />
			</div>
			
			<div class="col-sm-6" style="padding-right: 0px !important;">
				 <select class="form-control" name="wpsc_personal_data_retention_period_unit">
					 <?php
					 $wpsc_personal_data_retention_period_unit = get_option('wpsc_personal_data_retention_period_unit');
					 $selected = $wpsc_personal_data_retention_period_unit == 'days' ? 'selected="selected"' : '';
					 echo '<option '.$selected.' value="days">Days</option>';
					 $selected = $wpsc_personal_data_retention_period_unit == 'months' ? 'selected="selected"' : '';
					 echo '<option '.$selected.' value="months">Months</option>';
					 $selected = $wpsc_personal_data_retention_period_unit == 'years' ? 'selected="selected"' : '';
					 echo '<option '.$selected.' value="years">Years</option>';
					 ?>
				 </select>
		  </div>
   </div>
 </div>

  <button type="submit" class="btn btn-success"><?php _e('Save Changes','supportcandy');?></button>
  <img class="wpsc_submit_wait" style="display:none;" src="<?php echo WPSC_PLUGIN_URL.'asset/images/ajax-loader@2x.gif';?>">
  <input type="hidden" name="action" value="wpsc_settings" />
  <input type="hidden" name="setting_action" value="set_terms_and_condition_settings" />
</form>  
<script>

jQuery(document).ready(function(){
	
	<?php
	if($wpsc_personal_data_retention_type == 'enable'){
		?>
		jQuery('#wpsc_personal_data_retention_period_div').show();
		<?php
	}
	?>
	jQuery('#wpsc_personal_data_retention_type').on('change', function() {
		if(this.value=='enable'){			 
			 jQuery('#wpsc_personal_data_retention_period_div').show();
		}else {
			 jQuery('#wpsc_personal_data_retention_period_div').hide(); 
		}
 });
});

jQuery( document ).ready(function() {
  tinymce.remove();
  wpsc_get_tinymce_term('wpsc_terms_and_conditions_html','term_body');
  wpsc_get_tinymce_gdpr('wpsc_gdpr_html','gdpr_body');
});

function wpsc_get_tinymce_term(selector,body_id){
  
  jQuery('#visual_header').removeClass('btn btn-primary visual_header');
  jQuery('#text_header').addClass('btn btn-primary text_header');
  jQuery('#text_header').removeClass('btn btn-default text_header');
  jQuery('#text').removeClass('wpsc-switch-editor-active');
  jQuery('#visual').addClass('wpsc-switch-editor-active');
  tinymce.init({ 
    selector:'#'+selector,
    body_id: body_id,
    directionality : '<?php echo $directionality; ?>',
    menubar: false,
    statusbar: false,
    height : '100',
    plugins: [
      'lists link image directionality'
    ],
    image_advtab: true,
    toolbar: 'bold italic underline blockquote | alignleft aligncenter alignright | bullist numlist | rtl | link image',
    branding: false,
    autoresize_bottom_margin: 20,
    browser_spellcheck : true,
    relative_urls : false,
    remove_script_host : false,
    convert_urls : true,
    setup: function (editor) {
    }
  });
}

function wpsc_get_textarea_term(selector){
  jQuery('#visual_body').addClass('btn btn-primary visual_body');
  jQuery('#visual_body').removeClass('btn btn-default visual_body');
  jQuery('#text_body').removeClass('btn btn-primary text_body');
  tinymce.remove('#'+selector);
  jQuery('#text').addClass('wpsc-switch-editor-active');
  jQuery('#visual').removeClass('wpsc-switch-editor-active');
}

function wpsc_get_tinymce_gdpr(selector,body_id){
  
  jQuery('#visual_header').removeClass('btn btn-primary visual_header');
  jQuery('#text_header').addClass('btn btn-primary text_header');
  jQuery('#text_header').removeClass('btn btn-default text_header');
  jQuery('#text2').removeClass('wpsc-switch-editor-active');
  jQuery('#visual1').addClass('wpsc-switch-editor-active');
  tinymce.init({ 
    selector:'#'+selector,
    body_id: body_id,
    directionality : '<?php echo $directionality; ?>',
    menubar: false,
    statusbar: false,
    height : '100',
    plugins: [
      'lists link image directionality'
    ],
    image_advtab: true,
    toolbar: 'bold italic underline blockquote | alignleft aligncenter alignright | bullist numlist | rtl | link image',
    branding: false,
    autoresize_bottom_margin: 20,
    browser_spellcheck : true,
    relative_urls : false,
    remove_script_host : false,
    convert_urls : true,
    setup: function (editor) {
    }
  });
}

function wpsc_get_textarea_gdpr(selector){
  jQuery('#visual_body').addClass('btn btn-primary visual_body');
  jQuery('#visual_body').removeClass('btn btn-default visual_body');
  jQuery('#text_body').removeClass('btn btn-primary text_body');
  tinymce.remove('#'+selector);
  jQuery('#text2').addClass('wpsc-switch-editor-active');
  jQuery('#visual1').removeClass('wpsc-switch-editor-active');
}

</script>