<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user, $wpdb;
if (!($current_user->ID && $current_user->has_cap('edit_published_posts'))) {
	exit;
}

$fields = get_terms([
	'taxonomy'   => 'wpsc_ticket_custom_fields',
	'hide_empty' => false,
	'orderby'    => 'meta_value_num',
	'meta_key'	 => 'wpsc_tf_load_order',
	'order'    	 => 'ASC',
	'meta_query' => array(
		array(
      'key'       => 'agentonly',
      'value'     => '0',
      'compare'   => '='
    )
	),
]);

?>
<h4>
	<?php _e('Ticket Form Fields','supportcandy');?>
	<button style="margin-left:10px;" class="btn btn-success btn-sm" id="wpsc_add_new_form_field_btn" onclick="wpsc_get_add_form_field();"><?php _e('+Add New','supportcandy');?></button>
</h4>

<div class="wpsc_padding_space"></div>

<ul class="wpsc-sortable">
	<?php foreach ( $fields as $field ) :
		$label = get_term_meta( $field->term_id, 'wpsc_tf_label', true);
		$type = get_term_meta( $field->term_id, 'wpsc_tf_type', true);
		$status = get_term_meta( $field->term_id, 'wpsc_tf_status', true);
		$bg_color = $status == '1' ? '#1E90FF' : '#ff0000';
		?>
		<li class="ui-state-default" data-id="<?php echo $field->term_id?>">
			<div class="wpsc-flex-container" style="background-color:<?php echo $bg_color?>;color:#fff;">
				<div class="wpsc-sortable-handle"><i class="fa fa-bars"></i></div>
				<div class="wpsc-sortable-label"><?php echo htmlentities($label)?></div>
				<?php do_action('wpsc_before_form_fields_edit_option',$field->term_id) ?>
				<div class="wpsc-sortable-edit" onclick="<?php echo $type!='0'? 'wpsc_get_edit_form_field('.$field->term_id.');' : 'wpsc_get_edit_default_form_field('.$field->term_id.');'?>"><i class="fa fa-edit"></i></div>
				<div class="wpsc-sortable-delete" onclick="wpsc_delete_custom_field(<?php echo $field->term_id?>);"><i class="fa fa-trash"></i></div>
			</div>
		</li>
	<?php endforeach;?>
</ul>

<script>
	
	jQuery(function(){
    jQuery( ".wpsc-sortable" ).sortable({ handle: '.wpsc-sortable-handle' });
		jQuery( ".wpsc-sortable" ).on("sortupdate",function(event,ui){
			var ids = jQuery(this).sortable( "toArray", {attribute: 'data-id'} );
			var data = {
		    action: 'wpsc_custom_fields',
		    setting_action : 'set_form_field_order',
				field_ids : ids
		  };
			jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
				var response = JSON.parse(response_str);
		    if (response.sucess_status=='1') {
		      jQuery('#wpsc_alert_success .wpsc_alert_text').text(response.messege);
		    }
		    jQuery('#wpsc_alert_success').slideDown('fast',function(){});
		    setTimeout(function(){ jQuery('#wpsc_alert_success').slideUp('fast',function(){}); }, 3000);
		  });
		});
	});
	
	function wpsc_get_add_form_field(){
		wpsc_modal_open(wpsc_admin.add_new_form_field);
		var data = {
			action: 'wpsc_custom_fields',
			setting_action : 'get_add_form_field'
		};
		jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
			var response = JSON.parse(response_str);
			jQuery('#wpsc_popup_body').html(response.body);
			jQuery('#wpsc_popup_footer').html(response.footer);
			jQuery('#wpsc_tf_label').focus();
		});
	}
	
	function wpsc_set_add_form_field(){
		var field_label = jQuery('#wpsc_tf_label').val().trim();
		if (field_label.length == 0) {
			jQuery('#wpsc_tf_label').val('').focus();
			return;
		}
		var extra_info = jQuery('#wpsc_tf_extra_info').val().trim();
		var personal_info = jQuery('#wpsc_tf_personal_info').val().trim();
		var field_type = jQuery('#wpsc_tf_type').val().trim();
		var wpsc_tf_placeholder_text = jQuery('#wpsc_tf_placeholder_text').val().trim();
		var field_options = jQuery('#wpsc_tf_options').val().trim();
		if (jQuery('#wpsc_tf_type option:selected').data('options')=='1' && field_options=='') {
			jQuery('#wpsc_tf_options').val('').focus();
			return;
		}
		
		if(field_type=='22'){
			if ((typeof tinyMCE != "undefined") && tinyMCE.activeEditor && !tinyMCE.activeEditor.isHidden()) {
				var html_content = tinyMCE.activeEditor.getContent();
				if(html_content.trim().length==0){
					jQuery('#wpsc_html_content').val('').focus();
					return;
				}
  			}else{
				var html_content = jQuery('#wpsc_html_content').val().trim();
				if (jQuery('#wpsc_tf_type option:selected').data('options')=='2' && html_content=='') {
				jQuery('#wpsc_html_content').val('').focus();
				return;
				}
			}
		}
		
		var time_format = jQuery('#wpsc_tf_time_format').val().trim();
		var required = jQuery('#wpsc_tf_required').val().trim();
		var width = jQuery('#wpsc_tf_width').val().trim();
		var visibility = new Array();
		var limit = jQuery('#wpsc_tf_add_limit').val().trim();
		var date_range = jQuery('#wpsc_date_range').val().trim();
		jQuery('#wpsc_tf_condition_container').find('input').each(function(){
	    visibility.push(jQuery(this).val());
	  });
		
		jQuery('.wpsc_popup_action').text('<?php _e('Please wait ...','supportcandy')?>');
		jQuery('.wpsc_popup_action, #wpsc_popup_body input').attr("disabled", "disabled");
		var data = {
			action: 'wpsc_custom_fields',
			setting_action : 'set_add_form_field',
			field_label : field_label,
			extra_info : extra_info,
			personal_info : personal_info,
			field_type : field_type,
			wpsc_tf_placeholder_text:wpsc_tf_placeholder_text,
			field_options : field_options,
			required : required,
			width : width,
			limit : limit,
			visibility : visibility,
			html_content : html_content,
			time_format :time_format,
			date_range : date_range
		};
		jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
			wpsc_modal_close();
			var response = JSON.parse(response_str);
			if (response.sucess_status=='1') {
				jQuery('#wpsc_alert_success .wpsc_alert_text').text(response.messege);
				jQuery('#wpsc_alert_success').slideDown('fast',function(){});
				setTimeout(function(){ jQuery('#wpsc_alert_success').slideUp('fast',function(){}); }, 3000);
				wpsc_get_ticket_form_fields();
			} else {
				jQuery('#wpsc_alert_error .wpsc_alert_text').text(response.messege);
				jQuery('#wpsc_alert_error').slideDown('fast',function(){});
				setTimeout(function(){ jQuery('#wpsc_alert_error').slideUp('fast',function(){}); }, 3000);
			}
		});
	}
	
	function wpsc_get_edit_form_field(field_id){
		wpsc_modal_open(wpsc_admin.edit_form_field);
		var data = {
			action: 'wpsc_custom_fields',
			setting_action : 'get_edit_form_field',
			field_id : field_id
		};
		jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
			var response = JSON.parse(response_str);
			jQuery('#wpsc_popup_body').html(response.body);
			jQuery('#wpsc_popup_footer').html(response.footer);
			jQuery('#wpsc_tf_label').focus();
		});
	}
	
	function wpsc_set_edit_form_field(field_id){
		var field_label = jQuery('#wpsc_tf_label').val().trim();
		if (field_label.length == 0) {
			jQuery('#wpsc_tf_label').val('').focus();
			return;
		}
		var extra_info = jQuery('#wpsc_tf_extra_info').val().trim();
		var personal_info = jQuery('#wpsc_tf_personal_info').val().trim();
		var field_type = jQuery('#wpsc_tf_type').val().trim();
		var wpsc_tf_placeholder_text = jQuery('#wpsc_tf_placeholder_text').val().trim();
		var field_options = jQuery('#wpsc_tf_options').val().trim();
		if (jQuery('#wpsc_tf_type option:selected').data('options')=='1' && field_options=='') {
			jQuery('#wpsc_tf_options').val('').focus();
			return;
		}
		
		if(field_type == '22'){
			if ((typeof tinyMCE != "undefined") && tinyMCE.activeEditor && !tinyMCE.activeEditor.isHidden()) {
			var html_content = tinyMCE.activeEditor.getContent();
			if(html_content.trim().length==0){
				jQuery('#wpsc_html_content') . val('') . focus();
				return;
			}
			}else{
				var html_content = jQuery('#wpsc_html_content').val().trim();
				if (jQuery('#wpsc_tf_type option:selected').data('options')=='2' && html_content=='') {
				jQuery('#wpsc_html_content').val('').focus();
				return;
				}
			}
		}
		
		var time_format = jQuery('#wpsc_tf_time_format').val().trim();
		var required = jQuery('#wpsc_tf_required').val().trim();
		var width = jQuery('#wpsc_tf_width').val().trim();
		var visibility = new Array();
		var limit = jQuery('#wpsc_tf_limit').val();
		var date_range = jQuery('#wpsc_date_range').val().trim();
		jQuery('#wpsc_tf_condition_container').find('input').each(function(){
		  visibility.push(jQuery(this).val());
	  });
		
		jQuery('.wpsc_popup_action').text('<?php _e('Please wait ...','supportcandy')?>');
		jQuery('.wpsc_popup_action, #wpsc_popup_body input').attr("disabled", "disabled");
		var data = {
			action: 'wpsc_custom_fields',
			setting_action : 'set_edit_form_field',
			field_id : field_id,
			field_label : field_label,
			extra_info : extra_info,
			personal_info : personal_info,
			field_type : field_type,
			wpsc_tf_placeholder_text : wpsc_tf_placeholder_text,
			field_options : field_options,
			required : required,
			width	: width,
			limit	: limit,
			visibility : visibility,
			html_content : html_content,
			time_format :time_format,
			date_range : date_range
		};
		jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
			wpsc_modal_close();
			var response = JSON.parse(response_str);
			if (response.sucess_status=='1') {
				jQuery('#wpsc_alert_success .wpsc_alert_text').text(response.messege);
				jQuery('#wpsc_alert_success').slideDown('fast',function(){});
				setTimeout(function(){ jQuery('#wpsc_alert_success').slideUp('fast',function(){}); }, 3000);
				wpsc_get_ticket_form_fields();
			} else {
				jQuery('#wpsc_alert_error .wpsc_alert_text').text(response.messege);
				jQuery('#wpsc_alert_error').slideDown('fast',function(){});
				setTimeout(function(){ jQuery('#wpsc_alert_error').slideUp('fast',function(){}); }, 3000);
			}
		});
	}
	
	function wpsc_get_edit_default_form_field(field_id){
		wpsc_modal_open(wpsc_admin.edit_form_field);
		var data = {
			action: 'wpsc_custom_fields',
			setting_action : 'get_edit_default_form_field',
			field_id : field_id
		};
		jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
			var response = JSON.parse(response_str);
			jQuery('#wpsc_popup_body').html(response.body);
			jQuery('#wpsc_popup_footer').html(response.footer);
			jQuery('#wpsc_tf_label').focus();
		});
	}
	
	function wpsc_set_edit_default_form_field(field_id){
		var field_label = jQuery('#wpsc_tf_label').val().trim();
		if (field_label.length == 0) {
			jQuery('#wpsc_tf_label').val('').focus();
			return;
		}
		var extra_info = jQuery('#wpsc_tf_extra_info').val().trim();
		var width  = jQuery('#wpsc_tf_width').val().trim();
		var status = jQuery('#wpsc_tf_status').val();
		var default_sub  = jQuery('#wpsc_tf_default_subject').val();
		var default_desc = jQuery('#wpsc_tf_default_description').val();
		var limit  = jQuery('#wpsc_tf_limit').val();
		var wpsc_tf_placeholder_text = jQuery('#wpsc_tf_placeholder_text').val();
		var visibility = new Array();
		jQuery('#wpsc_tf_condition_container').find('input').each(function(){
			visibility.push(jQuery(this).val());
		});

		jQuery('.wpsc_popup_action').text('<?php _e('Please wait ...','supportcandy')?>');
		jQuery('.wpsc_popup_action, #wpsc_popup_body input').attr("disabled", "disabled");
		var data = {
			action: 'wpsc_custom_fields',
			setting_action : 'set_edit_default_form_field',
			field_id : field_id,
			field_label : field_label,
			extra_info : extra_info,
			width : width,
			status : status,
			default_sub : default_sub,
			default_desc : default_desc,
			limit	 : limit,
			visibility : visibility,
			wpsc_tf_placeholder_text : wpsc_tf_placeholder_text
		};
		jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
			wpsc_modal_close();
			var response = JSON.parse(response_str);
			if (response.sucess_status=='1') {
				jQuery('#wpsc_alert_success .wpsc_alert_text').text(response.messege);
				jQuery('#wpsc_alert_success').slideDown('fast',function(){});
				setTimeout(function(){ jQuery('#wpsc_alert_success').slideUp('fast',function(){}); }, 3000);
				wpsc_get_ticket_form_fields();
			} else {
				jQuery('#wpsc_alert_error .wpsc_alert_text').text(response.messege);
				jQuery('#wpsc_alert_error').slideDown('fast',function(){});
				setTimeout(function(){ jQuery('#wpsc_alert_error').slideUp('fast',function(){}); }, 3000);
			}
		});
	}
	
	function wpsc_delete_custom_field(field_id){
		
		var flag = confirm(wpsc_admin.are_you_sure);
		if (flag) {
			var data = {
				action: 'wpsc_custom_fields',
				setting_action : 'delete_custom_field',
				field_id : field_id
			};
			jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
				var response = JSON.parse(response_str);
				if (response.sucess_status=='1') {
					jQuery('#wpsc_alert_success .wpsc_alert_text').text(response.messege);
					jQuery('#wpsc_alert_success').slideDown('fast',function(){});
					setTimeout(function(){ jQuery('#wpsc_alert_success').slideUp('fast',function(){}); }, 3000);
					wpsc_get_ticket_form_fields();
				} else {
					jQuery('#wpsc_alert_error .wpsc_alert_text').text(response.messege);
					jQuery('#wpsc_alert_error').slideDown('fast',function(){});
					setTimeout(function(){ jQuery('#wpsc_alert_error').slideUp('fast',function(){}); }, 3000);
				}
			});
		}
	}
	
</script>
