<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user, $wpdb;
if (!($current_user->ID && $current_user->has_cap('edit_published_posts'))) {
	exit;
}

$agent_role = get_option('wpsc_agent_role');

?>
<h4>
	<?php _e('Agent Roles','supportcandy');?>
	<button style="margin-left:10px;" class="btn btn-success btn-sm" onclick="wpsc_get_add_agent_role();"><?php _e('+Add New','supportcandy');?></button>
</h4>

<div class="wpsc_padding_space"></div>

<ul class="wpsc-sortable">
	<?php foreach ( $agent_role as $key => $val ) :?>
		<li class="ui-state-default">
			<div class="wpsc-flex-container" style="background-color:#1E90FF;color:#fff;">
				<div class="wpsc-sortable-label"><?php echo $val['label']?></div>
				<div class="wpsc-sortable-edit" onclick="wpsc_get_edit_agent_role(<?php echo $key?>);" style="margin-left:40px;"><i class="fa fa-edit"></i></div>
				<div class="wpsc-sortable-delete" onclick="wpsc_delete_agent_role(<?php echo $key?>);"><i class="fa fa-trash"></i></div>
			</div>
		</li>
	<?php endforeach;?>
</ul>

<script>
	
	function wpsc_get_add_agent_role(){
		wpsc_modal_open(wpsc_admin.add_new_role);
		var data = {
			action: 'wpsc_settings',
			setting_action : 'get_add_agent_role'
		};
		jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
			var response = JSON.parse(response_str);
			jQuery('#wpsc_popup_body').html(response.body);
			jQuery('#wpsc_popup_footer').html(response.footer);
			jQuery('#wpsc_cat_name').focus();
		});
	}
	
	function wpsc_set_add_agent_role(){
		var label = jQuery('#wpsc_role_label').val().trim();
		if (label.length == 0) {
			jQuery('#wpsc_role_label').val('').focus();
			return;
		}
		var dataform = new FormData(jQuery('#wpsc_frm_agent_role')[0]);
	  jQuery.ajax({
	    url: wpsc_admin.ajax_url,
	    type: 'POST',
	    data: dataform,
	    processData: false,
	    contentType: false
	  })
	  .done(function (response_str) {
			wpsc_modal_close();
			var response = JSON.parse(response_str);
			if (response.sucess_status=='1') {
				wpsc_get_agent_roles();
			} else {
				jQuery('#wpsc_alert_error .wpsc_alert_text').text(response.messege);
				jQuery('#wpsc_alert_error').slideDown('fast',function(){});
				setTimeout(function(){ jQuery('#wpsc_alert_error').slideUp('fast',function(){}); }, 3000);
			}
	  });
	}
	
	function wpsc_get_edit_agent_role(role_id){
		wpsc_modal_open(wpsc_admin.edit_agent_role);
		var data = {
			action: 'wpsc_settings',
			setting_action : 'get_edit_agent_role',
			role_id : role_id
		};
		jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
			var response = JSON.parse(response_str);
			jQuery('#wpsc_popup_body').html(response.body);
			jQuery('#wpsc_popup_footer').html(response.footer);
			jQuery('#wpsc_priority_name').focus();
		});
	}
	
	function wpsc_set_edit_agent_role(){
		var label = jQuery('#wpsc_role_label').val().trim();
		if (label.length == 0) {
			jQuery('#wpsc_role_label').val('').focus();
			return;
		}
		var dataform = new FormData(jQuery('#wpsc_frm_agent_role')[0]);
	  jQuery.ajax({
	    url: wpsc_admin.ajax_url,
	    type: 'POST',
	    data: dataform,
	    processData: false,
	    contentType: false
	  })
	  .done(function (response_str) {
			wpsc_modal_close();
			var response = JSON.parse(response_str);
			if (response.sucess_status=='1') {
				jQuery('#wpsc_alert_success .wpsc_alert_text').text(response.messege);
				jQuery('#wpsc_alert_success').slideDown('fast',function(){});
				setTimeout(function(){ jQuery('#wpsc_alert_success').slideUp('fast',function(){}); }, 3000);
				wpsc_get_agent_roles();
			} else {
				jQuery('#wpsc_alert_error .wpsc_alert_text').text(response.messege);
				jQuery('#wpsc_alert_error').slideDown('fast',function(){});
				setTimeout(function(){ jQuery('#wpsc_alert_error').slideUp('fast',function(){}); }, 3000);
			}
	  });
	}
	
	function wpsc_delete_agent_role(role_id){
		var flag = confirm(wpsc_admin.are_you_sure);
		if (flag) {
			var data = {
				action: 'wpsc_settings',
				setting_action : 'delete_agent_role',
				role_id : role_id
			};
			jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
				var response = JSON.parse(response_str);
				if (response.sucess_status=='1') {
					jQuery('#wpsc_alert_success .wpsc_alert_text').text(response.messege);
					jQuery('#wpsc_alert_success').slideDown('fast',function(){});
					setTimeout(function(){ jQuery('#wpsc_alert_success').slideUp('fast',function(){}); }, 3000);
					wpsc_get_agent_roles();
				} else {
					jQuery('#wpsc_alert_error .wpsc_alert_text').text(response.messege);
					jQuery('#wpsc_alert_error').slideDown('fast',function(){});
					setTimeout(function(){ jQuery('#wpsc_alert_error').slideUp('fast',function(){}); }, 3000);
				}
			});
		}
	}
	
</script>
