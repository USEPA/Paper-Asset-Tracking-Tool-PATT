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
      'value'     => '1',
      'compare'   => '='
    )
	),
]);

?>
<h4>
	<?php _e('Agent Only Fields','supportcandy');?>
	<button style="margin-left:10px;" class="btn btn-success btn-sm" onclick="wpsc_get_add_agentonly_field();"><?php _e('+Add New','supportcandy');?></button>
</h4>

<div class="wpsc_padding_space"></div>

<ul class="wpsc-sortable">
	<?php foreach ( $fields as $field ) :
		$label = get_term_meta( $field->term_id, 'wpsc_tf_label', true);
		?>
		<li class="ui-state-default" data-id="<?php echo $field->term_id?>">
			<div class="wpsc-flex-container" style="background-color:#1E90FF;color:#fff;">
				<div class="wpsc-sortable-handle"><i class="fa fa-bars"></i></div>
				<div class="wpsc-sortable-label"><?php echo htmlentities($label)?></div>
				<div class="wpsc-sortable-edit" onclick="wpsc_get_edit_agentonly_field(<?php echo $field->term_id?>);"><i class="fa fa-edit"></i></div>
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
	
	function wpsc_get_add_agentonly_field(){
		wpsc_modal_open(wpsc_admin.add_new_agent_only_field);
		var data = {
			action: 'wpsc_custom_fields',
			setting_action : 'get_add_agentonly_field'
		};
		jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
			var response = JSON.parse(response_str);
			jQuery('#wpsc_popup_body').html(response.body);
			jQuery('#wpsc_popup_footer').html(response.footer);
			jQuery('#wpsc_tf_label').focus();
		});
	}
	
	function wpsc_set_add_agentonly_field(){
		var field_label = jQuery('#wpsc_tf_label').val().trim();
		if (field_label.length == 0) {
			jQuery('#wpsc_tf_label').val('').focus();
			return;
		}
		var extra_info = jQuery('#wpsc_tf_extra_info').val().trim();
		var personal_info = jQuery('#wpsc_tf_personal_info').val().trim();
		var field_type = jQuery('#wpsc_tf_type').val().trim();
		var field_options = jQuery('#wpsc_tf_options').val().trim();
		var limit = jQuery('#wpsc_cf_limit').val();
		var date_range = jQuery('#wpsc_date_range').val().trim();

		if (jQuery('#wpsc_tf_type option:selected').data('options')=='1' && field_options=='') {
			jQuery('#wpsc_tf_options').val('').focus();
			return;
		}
		
		var time_format = jQuery('#wpsc_tf_time_format').val().trim();

		jQuery('.wpsc_popup_action').text('<?php _e('Please wait ...','supportcandy')?>');
		jQuery('.wpsc_popup_action, #wpsc_popup_body input').attr("disabled", "disabled");
		var data = {
			action: 'wpsc_custom_fields',
			setting_action : 'set_add_agentonly_field',
			field_label : field_label,
			extra_info : extra_info,
			field_type : field_type,
			field_options : field_options,
			personal_info : personal_info,
			limit : limit,
			date_range : date_range,
			time_format:time_format
		};
		jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
			wpsc_modal_close();
			var response = JSON.parse(response_str);
			if (response.sucess_status=='1') {
				jQuery('#wpsc_alert_success .wpsc_alert_text').text(response.messege);
				jQuery('#wpsc_alert_success').slideDown('fast',function(){});
				setTimeout(function(){ jQuery('#wpsc_alert_success').slideUp('fast',function(){}); }, 3000);
				wpsc_get_agentonly_fields();
			} else {
				jQuery('#wpsc_alert_error .wpsc_alert_text').text(response.messege);
				jQuery('#wpsc_alert_error').slideDown('fast',function(){});
				setTimeout(function(){ jQuery('#wpsc_alert_error').slideUp('fast',function(){}); }, 3000);
			}
		});
	}
	
	function wpsc_get_edit_agentonly_field(field_id){
		wpsc_modal_open(wpsc_admin.edit_agent_only_field);
		var data = {
			action: 'wpsc_custom_fields',
			setting_action : 'get_edit_agentonly_field',
			field_id : field_id
		};
		jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
			var response = JSON.parse(response_str);
			jQuery('#wpsc_popup_body').html(response.body);
			jQuery('#wpsc_popup_footer').html(response.footer);
			jQuery('#wpsc_tf_label').focus();
		});
	}
	
	function wpsc_set_edit_agentonly_field(field_id){
		var field_label = jQuery('#wpsc_tf_label').val().trim();
		if (field_label.length == 0) {
			jQuery('#wpsc_tf_label').val('').focus();
			return;
		}
		var extra_info = jQuery('#wpsc_tf_extra_info').val().trim();
		var personal_info = jQuery('#wpsc_tf_personal_info').val().trim();
		var field_type = jQuery('#wpsc_tf_type').val().trim();
		var field_options = jQuery('#wpsc_tf_options').val().trim();
		var limit = jQuery('#wpsc_ao_limit').val();
		var date_range = jQuery('#wpsc_date_range').val().trim();
		if (jQuery('#wpsc_tf_type option:selected').data('options')=='1' && field_options=='') {
			jQuery('#wpsc_tf_options').val('').focus();
			return;
		}
		
		var time_format = jQuery('#wpsc_tf_time_format').val().trim();
		
		jQuery('.wpsc_popup_action').text('<?php _e('Please wait ...','supportcandy')?>');
		jQuery('.wpsc_popup_action, #wpsc_popup_body input').attr("disabled", "disabled");
		var data = {
			action: 'wpsc_custom_fields',
			setting_action : 'set_edit_agentonly_field',
			field_id : field_id,
			field_label : field_label,
			extra_info : extra_info,
			field_type : field_type,
			field_options : field_options,
			personal_info : personal_info,
			limit : limit,
			date_range : date_range,
			time_format:time_format
		};
		jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
			wpsc_modal_close();
			var response = JSON.parse(response_str);
			if (response.sucess_status=='1') {
				jQuery('#wpsc_alert_success .wpsc_alert_text').text(response.messege);
				jQuery('#wpsc_alert_success').slideDown('fast',function(){});
				setTimeout(function(){ jQuery('#wpsc_alert_success').slideUp('fast',function(){}); }, 3000);
				wpsc_get_agentonly_fields();
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
					wpsc_get_agentonly_fields();
				} else {
					jQuery('#wpsc_alert_error .wpsc_alert_text').text(response.messege);
					jQuery('#wpsc_alert_error').slideDown('fast',function(){});
					setTimeout(function(){ jQuery('#wpsc_alert_error').slideUp('fast',function(){}); }, 3000);
				}
			});
		}
	}
	
</script>
