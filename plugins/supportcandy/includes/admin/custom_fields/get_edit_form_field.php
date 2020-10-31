<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user, $wpscfunction;
if (!($current_user->ID && $current_user->has_cap('edit_published_posts'))) {exit;}

$field_id              = isset($_POST) && isset($_POST['field_id']) ? intval($_POST['field_id']) : 0;
if (!$field_id) {exit;}

$custom_field =  get_term_by('id', $field_id, 'wpsc_ticket_custom_fields');
$wpsc_tf_label = get_term_meta( $custom_field->term_id, 'wpsc_tf_label', true);
$wpsc_tf_extra_info = get_term_meta( $custom_field->term_id, 'wpsc_tf_extra_info', true);
$agentonly = get_term_meta( $custom_field->term_id, 'agentonly', true);
$wpsc_tf_status = get_term_meta( $custom_field->term_id, 'wpsc_tf_status', true);
$wpsc_tf_type = get_term_meta( $custom_field->term_id, 'wpsc_tf_type', true);
$wpsc_tf_has_options = 0;
$wpsc_tf_options = get_term_meta( $custom_field->term_id, 'wpsc_tf_options', true);
$wpsc_tf_required = get_term_meta( $custom_field->term_id, 'wpsc_tf_required', true);
$wpsc_tf_width = get_term_meta( $custom_field->term_id, 'wpsc_tf_width', true);
$wpsc_date_range= get_term_meta( $custom_field->term_id, 'wpsc_date_range', true);
$wpsc_tf_visibility = get_term_meta( $custom_field->term_id, 'wpsc_tf_visibility', true);
$wpsc_tf_personal_info   = get_term_meta( $custom_field->term_id, 'wpsc_tf_personal_info', true);
$wpsc_tf_limit = get_term_meta( $custom_field->term_id, 'wpsc_tf_limit',true);
$wpsc_tf_placeholder_text = get_term_meta(	$custom_field->term_id, 'wpsc_tf_placeholder_text',true);
$wpsc_html_content = get_term_meta( $custom_field->term_id , 'wpsc_html_content', true);
$wpsc_time_format = get_term_meta($custom_field->term_id,'wpsc_time_format',true);

$field_types           = $wpscfunction->get_custom_field_types();
$conditional_types     = array();
foreach ($field_types as $key => $field) {
	if ($field['has_options']) {
		$conditional_types[]=$key;
	}
}
$placeholder_text = array('1','5','6','7','8','9','18');
$style = '';
if (!(in_array($wpsc_tf_type,$placeholder_text))) {
	$style = 'display:none';
}

$tf_types = array(1,5,7,8,9);
$l_style    = '';
if(!in_array($wpsc_tf_type,$tf_types)){
	$l_style = "display:none";
}
//has option
if(in_array($wpsc_tf_type,$conditional_types)){
	$wpsc_tf_has_options = 1;
}
//html field
$wpsc_html_content_option = 0;
if($wpsc_tf_type == '22'){
	$wpsc_html_content_option =1;
}

//time field
$wpsc_time_field = 0 ;
if($wpsc_tf_type == '21'){
	$wpsc_time_field = 1 ;	
}
$wpsc_date_field = 0;
if($wpsc_tf_type == '6'){
	$wpsc_date_field = 1;
}

$rich_editing = $wpscfunction->rich_editing_status($current_user);
$directionality = $wpscfunction->check_rtl();

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
				$selected = $wpsc_tf_type == $key ? 'selected="selected"' : '';
		?>
			<option <?php echo $selected?> data-options="<?php echo $field['has_options']?>" value="<?php echo $key?>"><?php echo $field['label']?></option>
		<?php endforeach;?>
	</select>
</div>

<div class="form-group" id= "wpsc_placeholder_text"  style="<?php echo $style; ?>">
  <label for="wpsc_tf_placeholder_text"><?php _e('Placeholder text','supportcandy');?></label>
  <p class="help-block"><?php _e('Enter the placeholder text','supportcandy');?></p>
  <input id="wpsc_tf_placeholder_text" class="form-control" name="wpsc_tf_placeholder_text" value="<?php echo str_replace('"', "&quot;", stripcslashes($wpsc_tf_placeholder_text))?>" />
</div>

<div class="form-group" id="wpsc_tf_options_container" style="<?php echo !$wpsc_tf_has_options?'display:none;':'';?>">
  <label for="wpsc_tf_options"><?php _e('Field Options','supportcandy');?></label>
  <p class="help-block"><?php _e('Insert field options. New option should begin on new line.','supportcandy');?></p>
  <textarea style="height:80px !important;" id="wpsc_tf_options" class="form-control" name="wpsc_tf_options"><?php echo stripcslashes(implode('\n', $wpsc_tf_options));?></textarea>
</div>

<div class="form-group" id="wpsc_html_fields_container" style="<?php echo !$wpsc_html_content_option?'display:none;':'';?>">
  <label for="wpsc_html_content"><?php _e('Content','supportcandy');?></label>
  <p class="help-block"></p>
  <?php if($rich_editing){?>
	  <div class="text-right">
		<button id="visual" class="wpsc-switch-editor wpsc-switch-editor-active " type="button" onclick="wpsc_get_tinymce('wpsc_html_content','html_edit_body');"><?php _e('Visual','supportcandy');?></button>
		<button id="text" class="wpsc-switch-editor" type="button" onclick="wpsc_get_textarea('wpsc_html_content')"><?php _e('Text','supportcandy');?></button>
 	  </div>
  <?php }?>
  <textarea style="height:80px !important;" id="wpsc_html_content" class="form-control" name="wpsc_html_content"><?php echo stripcslashes($wpsc_html_content);?></textarea>
</div>

<div class="form-group" id="wpsc_time_fields_container" style="<?php echo !$wpsc_time_field?'display:none;':'';?>">
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

<div class="form-group" id="wpsc_required_field_container" style="<?php echo $wpsc_html_content_option?'display:none;':'';?>" >
  <label for="wpsc_tf_required"><?php _e('Required','supportcandy');?></label>
  <p class="help-block"><?php _e('Whether this field is mandatory or optional. Yes indicates mandatory whereas no indicates optional.','supportcandy');?></p>
  <select id="wpsc_tf_required" class="form-control" name="wpsc_tf_required">
		<option <?php echo $wpsc_tf_required == '1' ? 'selected="selected"' : ''?> value="1"><?php _e('Yes','supportcandy');?></option>
		<option <?php echo $wpsc_tf_required == '0' ? 'selected="selected"' : ''?> value="0"><?php _e('No','supportcandy');?></option>
	</select>
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

<div class="form-group wpsc_edit_limit" id = "wpsc_add_limit" style = "<?php echo $l_style;?>">
  <label for="wpsc_tf_limit"><?php _e('Character Limit','supportcandy');?></label>
  <p class="help-block"><?php _e('Limit number of characters to be accepted for this field. Enter 0 for unlimited characters.','supportcandy');?></p>
  <input type = "number" id="wpsc_tf_limit" class="form-control" name="wpsc_tf_limit" value="<?php echo($wpsc_tf_limit) ?>" />
</div>
<div class="form-group">
  <label for="wpsc_tf_form_visibility"><?php _e('Visibility','supportcandy');?></label>
  <p class="help-block"><?php _e('Select conditions to show this field in create ticket form. Visible if no conditions set. Visible if any one of added conditions matches while creating ticket.','supportcandy');?></p>
  <div class="row">
  	<ul id="wpsc_tf_condition_container" class="wpsp_filter_display_container">
			<?php
			foreach ($wpsc_tf_visibility as $key => $value) {
				$condition = explode('--', stripslashes($value));
				$tf =  get_term_by('id', $condition[0], 'wpsc_ticket_custom_fields');
				$label = get_term_meta( $condition[0], 'wpsc_tf_label', true);
				$value_label = $condition[1];
				if ($tf->slug=='ticket_category') {
					$category =  get_term_by('id', $value_label, 'wpsc_categories');
					$value_label = $category->name;
				}
				if ($tf->slug=='ticket_priority') {
					$priority =  get_term_by('id', $value_label, 'wpsc_priorities');
					$value_label = $priority->name;
				}
				?>
				<li class="wpsp_filter_display_element">
					<div class="flex-container">
						<div class="wpsp_filter_display_text">
							<?php echo htmlentities($label)?>: <?php echo htmlentities($value_label)?>
							<input type="hidden" name="wpsp_tf_condition[]" value="<?php echo htmlentities($value) ?>">
						</div>
						<div class="wpsp_filter_display_remove" onclick="wpsc_remove_filter(this);"><i class="fa fa-times"></i></div>
					</div>
				</li>
				<?php
			}
			?>
		</ul>
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
					'exclude'    => array($custom_field->term_id),
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
					<option value="<?php echo $field->term_id?>"><?php echo htmlentities($label)?></option>
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

<?php if($rich_editing){?>
	<script>
		tinymce.remove();
		tinymce.init({ 
			selector:'#wpsc_html_content',
			body_id: 'html_edit_body',
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
<?php } ?>
<?php 
$body = ob_get_clean();
ob_start();
?>
<button type="button" class="btn wpsc_popup_close" onclick="wpsc_modal_close();"><?php _e('Close','supportcandy');?></button>
<button type="button" class="btn wpsc_popup_action" onclick="wpsc_set_edit_form_field(<?php echo htmlentities($field_id)?>);"><?php _e('Submit','supportcandy');?></button>
<?php 
$footer = ob_get_clean();

$output = array(
  'body'   => $body,
  'footer' => $footer
);

echo json_encode($output);
