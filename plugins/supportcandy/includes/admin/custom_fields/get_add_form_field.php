<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user, $wpscfunction;
if (!($current_user->ID && $current_user->has_cap('edit_published_posts'))) {exit;}

$field_types = $wpscfunction->get_custom_field_types();
$conditional_types = array();
foreach ($field_types as $key => $field) {
	if ($field['has_options']) {
		$conditional_types[]=$key;
	}
}

$rich_editing = $wpscfunction->rich_editing_status($current_user);
$directionality = $wpscfunction->check_rtl();

ob_start();
?>
<div class="form-group">
  <label for="wpsc_tf_label"><?php _e('Field Label','supportcandy');?></label>
  <p class="help-block"><?php _e('Insert field label. Please make sure label you are entering should not already exist.','supportcandy');?></p>
  <input id="wpsc_tf_label" class="form-control" name="wpsc_tf_label" value="" />
</div>
<div class="form-group">
  <label for="wpsc_tf_extra_info"><?php _e('Extra Information','supportcandy');?></label>
  <p class="help-block"><?php _e('Extra information about the field. Useful if you want to give instructions or information about the field in create ticket from. Keep this empty if not needed.','supportcandy');?></p>
  <input id="wpsc_tf_extra_info" class="form-control" name="wpsc_tf_extra_info" value="" />
</div>
<div class="form-group">
  <label for="wpsc_tf_personal_info"><?php _e('Personal Information','supportcandy');?></label>
  <p class="help-block"><?php _e('Enable or disable personal information in ticket form.','supportcandy');?></p>
  <select id="wpsc_tf_personal_info" class="form-control" name="wpsc_tf_personal_info">
		<option value="0"><?php _e('No','supportcandy');?></option>
		<option value="1"><?php _e('Yes','supportcandy');?></option>
	</select>
</div>
<div class="form-group">
  <label for="wpsc_tf_type"><?php _e('Field Type','supportcandy');?></label>
  <p class="help-block"><?php _e('Select field type.','supportcandy');?></p>
  <select id="wpsc_tf_type" class="form-control" name="wpsc_tf_type">
		<?php foreach ($field_types as $key => $field) :?>
			<option data-options="<?php echo $field['has_options']?>" value="<?php echo $key?>"><?php echo $field['label']?></option>
		<?php endforeach;?>
	</select>
</div>
<div id= "wpsc_placeholder_text" class="form-group" >
  <label for="wpsc_tf_type"><?php _e('Placeholder Text','supportcandy');?></label>
  <p class="help-block"><?php _e('Enter the placeholder text.','supportcandy');?></p>
	<input id="wpsc_tf_placeholder_text" class="form-control" name="wpsc_tf_placeholder_text" value="" />
</div>
<div class="form-group" id="wpsc_tf_options_container" style="display:none;">
  <label for="wpsc_tf_options"><?php _e('Field Options','supportcandy');?></label>
  <p class="help-block"><?php _e('Insert field options. New option should begin on new line.','supportcandy');?></p>
  <textarea style="height:80px !important;" id="wpsc_tf_options" class="form-control" name="wpsc_tf_options"></textarea>
</div>

<div class="form-group" id="wpsc_html_fields_container" style="display:none;">
  <label for="wpsc_html_content"><?php _e('Content','supportcandy');?></label>
  <p class="help-block"></p>
  <?php if($rich_editing){?>
  	<div class="text-right">
		<button id="visual" class="wpsc-switch-editor wpsc-switch-editor-active " type="button" onclick="wpsc_get_tinymce('wpsc_html_content','html_body');"><?php _e('Visual','supportcandy');?></button>
		<button id="text" class="wpsc-switch-editor" type="button" onclick="wpsc_get_textarea('wpsc_html_content')"><?php _e('Text','supportcandy');?></button>
 	</div>
  <?php } ?>

  <textarea type="text" style="height:80px !important;" id="wpsc_html_content" class="form-control" name="wpsc_html_content"></textarea>
</div>

<div class="form-group" id="wpsc_time_fields_container" style="display:none;">
  <label for="wpsc_time_content"><?php _e('Time Format','supportcandy');?></label>
  <p class="help-block"><?php _e('Please select the time format','supportcandy'); ?></p>
  <select id="wpsc_tf_time_format" class="form-control" name="wpsc_tf_time_format">
		<option value="12"><?php _e('12 Hrs','supportcandy');?></option>
		<option value="24"><?php _e('24 Hrs','supportcandy');?></option>
  </select>
</div>

<div class="form-group" id="wpsc_required_field_container">
  <label for="wpsc_tf_required"><?php _e('Required','supportcandy');?></label>
  <p class="help-block"><?php _e('Whether this field is mandatory or optional. Yes indicates mandatory whereas no indicates optional.','supportcandy');?></p>
  <select id="wpsc_tf_required" class="form-control" name="wpsc_tf_required">
		<option value="1"><?php _e('Yes','supportcandy');?></option>
		<option value="0"><?php _e('No','supportcandy');?></option>
	</select>
</div>
<div class="form-group">
  <label for="wpsc_tf_width"><?php _e('Width','supportcandy');?></label>
  <p class="help-block"><?php _e('Please select width of the field in create ticket form.','supportcandy');?></p>
  <select id="wpsc_tf_width" class="form-control" name="wpsc_tf_width">
		<option value="1/3"><?php _e('1/3 width of Row','supportcandy');?></option>
		<option value="1/2"><?php _e('Half width of Row','supportcandy');?></option>
		<option value="1"><?php _e('Full width of Row','supportcandy');?></option>
	</select>
</div>
<div class="form-group" id="wpsc_date_range_container" style="display:none;">
  <label for="wpsc_date_range"><?php _e('Allowed Dates','supportcandy');?></label>
  <p class="help-block"><?php _e('Please select Date range.','supportcandy');?></p>
  <select id="wpsc_date_range" class="form-control" name="wpsc_date_range">
		<option value="all"><?php _e('All','supportcandy');?></option>
		<option value="past"><?php _e('Past','supportcandy');?></option>
		<option value="future"><?php _e('Future','supportcandy');?></option>
	</select>
</div>

<div class="form-group" id = "wpsc_add_limit">
  <label for="wpsc_tf_add_limit"><?php _e('Character Limit','supportcandy');?></label>
  <p class="help-block"><?php _e('Limit number of characters to be accepted for this field. Enter 0 for unlimited characters.','supportcandy');?></p>
  <input type = "number" id="wpsc_tf_add_limit" class="form-control" name="wpsc_tf_add_limit" value="" />
</div>
<div class="form-group">
  <label for="wpsc_tf_form_visibility"><?php _e('Visibility','supportcandy');?></label>
  <p class="help-block"><?php _e('Select conditions to show this field in create ticket form. Visible if no conditions set. Visible if any one of added conditions matches while creating ticket.','supportcandy');?></p>
  <div class="row">
  	<ul id="wpsc_tf_condition_container" class="wpsp_filter_display_container"></ul>
  </div>
	<div class="row" style="background-color:#CCD1D1;padding-top:15px;padding-bottom:15px;">
		<div class="col-sm-5">
			<select id="wpsc_tf_vcf" class="form-control" onchange="wpsc_get_conditional_options(this);" name="wpsc_tf_vcf">
				<option value=""><?php _e('Select field','supportcandy');?></option>
				<?php
				$fields = get_terms([
					'taxonomy'   => 'wpsc_ticket_custom_fields',
					'hide_empty' => false,
					'orderby'    => 'meta_value_num',
					'meta_key'	 => 'wpsc_tf_load_order',
					'order'    	 => 'ASC',
					'meta_query' => array(
						'relation' => 'AND',
						array(
				      'key'       => 'agentonly',
				      'value'     => '0',
				      'compare'   => '='
				    ),
						array(
							'relation' => 'OR',
							array(
					      'key'       => 'wpsc_tf_type',
					      'value'     => $conditional_types,
					      'compare'   => 'IN'
					    ),
							array(
					      'key'       => 'wpsc_tf_conditional',
					      'value'     => '1',
					      'compare'   => '='
					    )
						)
					),
				]);
				foreach ($fields as $field) {
					$label = get_term_meta( $field->term_id, 'wpsc_tf_label', true);
					?>
					<option value="<?php echo $field->term_id?>"><?php echo $label?></option>
					<?php
				}
				?>
			</select>
		</div>
		<div class="col-sm-5">
			<select id="wpsc_tf_vco" class="form-control" name="wpsc_tf_vco">
				<option value=""><?php _e('Select option','supportcandy');?></option>
			</select>
		</div>
		<div class="col-sm-2">
			<button type="button" class="form-control btn btn-success" onclick="wpsc_add_field_condition();"><?php _e('Add','supportcandy');?></button>
		</div>
  </div>
</div>
<script>
	jQuery('#wpsc_tf_type').change(function(){
		var has_options = jQuery('option:selected',this).data('options');
		var option = Number(jQuery(this).val());

		if(has_options=='1'){
			jQuery('#wpsc_tf_options_container').show();
		} else {
			jQuery('#wpsc_tf_options_container').hide();
		}

		if(option == 22){
			jQuery('#wpsc_html_fields_container').show();	
			jQuery('#wpsc_required_field_container').hide();
		}else{
			jQuery('#wpsc_html_fields_container').hide();
		}
		
		if(option == 21){
			jQuery('#wpsc_time_fields_container').show();
		}else{
			jQuery('#wpsc_time_fields_container').hide();
		}
		
		var opt_arr = [1,5,7,8,9];
		if ( jQuery.inArray( option, opt_arr) >= 0) {
			jQuery('#wpsc_add_limit').show();
		}else{
			jQuery('#wpsc_add_limit').hide();
		}
		
		var poption = Number(jQuery(this).val());
		var popt_arr = [1,5,6,7,8,9,18];
		if ( jQuery.inArray( poption, popt_arr) >= 0) {
			jQuery('#wpsc_placeholder_text').show();
		}else{
			jQuery('#wpsc_placeholder_text').hide();
		}
		var poption = Number(jQuery(this).val());
		var popt_arr = [6];
		if ( jQuery.inArray( poption, popt_arr) >= 0) {
			jQuery('#wpsc_date_range_container').show();
		}else{
			jQuery('#wpsc_date_range_container').hide();
		}


	});
</script>

<?php if($rich_editing){ ?>
	<script>
		tinymce.remove();
		tinymce.init({ 
			selector:'#wpsc_html_content',
			body_id: 'html_body',
			menubar: false,
			statusbar: false,
			height : '200',
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
function wpsc_get_tinymce(selector,body_id){
	
	jQuery('#visual_header').addClass('btn btn-primary visual_header');
	jQuery('#text_header').removeClass('btn btn-primary text_header');
	jQuery('#text_header').addClass('btn btn-default text_header');
	jQuery('#text').removeClass('wpsc-switch-editor-active');
	jQuery('#visual').addClass('wpsc-switch-editor-active');
	tinymce.init({ 
		selector:'#'+selector,
		body_id: body_id,
		menubar: false,
		statusbar: false,
		height : '200',
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

function wpsc_get_textarea(selector){

	jQuery('#visual_body').removeClass('btn btn-primary visual_body');
	jQuery('#visual_body').addClass('btn btn-default visual_body');
	jQuery('#text_body').addClass('btn btn-primary text_body');
	tinymce.remove('#'+selector);
	jQuery('#text').addClass('wpsc-switch-editor-active');
	jQuery('#visual').removeClass('wpsc-switch-editor-active');
}
	</script>
<?php }?>
<?php 
$body = ob_get_clean();
ob_start();
?>
<button type="button" class="btn wpsc_popup_close" onclick="wpsc_modal_close();"><?php _e('Close','supportcandy');?></button>
<button type="button" class="btn wpsc_popup_action" onclick="wpsc_set_add_form_field();"><?php _e('Submit','supportcandy');?></button>
<?php 
$footer = ob_get_clean();

$output = array(
  'body'   => $body,
  'footer' => $footer
);

echo json_encode($output);
