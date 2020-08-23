<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user, $wpdb;
if (!($current_user->ID && $current_user->has_cap('edit_published_posts'))) {
	exit;
}

$priorities = get_terms([
	'taxonomy'   => 'wpsc_priorities',
	'hide_empty' => false,
	'orderby'    => 'meta_value_num',
	'order'    	 => 'ASC',
	'meta_query' => array('order_clause' => array('key' => 'wpsc_priority_load_order')),
]);

?>
<h4>
	<?php _e('Ticket Priorities','supportcandy');?>
	<button style="margin-left:10px;" class="btn btn-success btn-sm" onclick="wpsc_get_add_priority();"><?php _e('+Add New','supportcandy');?></button>
</h4>

<div class="wpsc_padding_space"></div>

<ul class="wpsc-sortable">
	<?php foreach ( $priorities as $priority ) :
    $color = get_term_meta( $priority->term_id, 'wpsc_priority_color', true);
    $backgound_color = get_term_meta( $priority->term_id, 'wpsc_priority_background_color', true);
    ?>
		<li class="ui-state-default" data-id="<?php echo $priority->term_id?>">
			<div class="wpsc-flex-container" style="background-color:<?php echo $backgound_color?>;color:<?php echo $color?>;">
				<div class="wpsc-sortable-handle"><i class="fa fa-bars"></i></div>
				<div class="wpsc-sortable-label"><?php echo $priority->name?></div>
				<div class="wpsc-sortable-edit" onclick="wpsc_get_edit_priority(<?php echo $priority->term_id?>);"><i class="fa fa-edit"></i></div>
				<div class="wpsc-sortable-delete" onclick="wpsc_delete_priority(<?php echo $priority->term_id?>);"><i class="fa fa-trash"></i></div>
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
		    action: 'wpsc_settings',
		    setting_action : 'set_priority_order',
				priority_ids : ids
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
	
	function wpsc_get_add_priority(){
		wpsc_modal_open(wpsc_admin.add_new_priority);
		var data = {
			action: 'wpsc_settings',
			setting_action : 'get_add_priority'
		};
		jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
			var response = JSON.parse(response_str);
			jQuery('#wpsc_popup_body').html(response.body);
			jQuery('#wpsc_popup_footer').html(response.footer);
			jQuery('#wpsc_cat_name').focus();
		});
	}
	
	function wpsc_set_add_priority(){
		var status_name = jQuery('#wpsc_priority_name').val().trim();
		if (status_name.length == 0) {
			jQuery('#wpsc_priority_name').val('').focus();
			return;
		}
		var status_color = jQuery('#wpsc_priority_color').val().trim();
		if (status_color.length == 0) {
			status_color = '#ffffff';
		}
		var status_bg_color = jQuery('#wpsc_priority_bg_color').val().trim();
		if (status_bg_color.length == 0) {
			status_bg_color = '#1E90FF';
		}
		jQuery('.wpsc_popup_action').text('<?php _e('Please wait ...','supportcandy')?>');
		jQuery('.wpsc_popup_action, #wpsc_popup_body input').attr("disabled", "disabled");
		var data = {
			action: 'wpsc_settings',
			setting_action : 'set_add_priority',
			priority_name : status_name,
			priority_color: status_color,
			priority_bg_color: status_bg_color
		};
		jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
			wpsc_modal_close();
			var response = JSON.parse(response_str);
			if (response.sucess_status=='1') {
				jQuery('#wpsc_alert_success .wpsc_alert_text').text(response.messege);
				jQuery('#wpsc_alert_success').slideDown('fast',function(){});
				setTimeout(function(){ jQuery('#wpsc_alert_success').slideUp('fast',function(){}); }, 3000);
				wpsc_get_priority_settings();
			} else {
				jQuery('#wpsc_alert_error .wpsc_alert_text').text(response.messege);
				jQuery('#wpsc_alert_error').slideDown('fast',function(){});
				setTimeout(function(){ jQuery('#wpsc_alert_error').slideUp('fast',function(){}); }, 3000);
			}
		});
	}
	
	function wpsc_get_edit_priority(priority_id){
		wpsc_modal_open(wpsc_admin.edit_priority);
		var data = {
			action: 'wpsc_settings',
			setting_action : 'get_edit_priority',
			priority_id : priority_id
		};
		jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
			var response = JSON.parse(response_str);
			jQuery('#wpsc_popup_body').html(response.body);
			jQuery('#wpsc_popup_footer').html(response.footer);
			jQuery('#wpsc_priority_name').focus();
		});
	}
	
	function wpsc_set_edit_priority(priority_id){
		var priority_name = jQuery('#wpsc_priority_name').val().trim();
		if (priority_name.length == 0) {
			jQuery('#wpsc_priority_name').val('').focus();
			return;
		}
		var priority_color = jQuery('#wpsc_priority_color').val().trim();
		if (priority_color.length == 0) {
			priority_color = '#ffffff';
		}
		var priority_bg_color = jQuery('#wpsc_priority_bg_color').val().trim();
		if (priority_bg_color.length == 0) {
			priority_bg_color = '#1E90FF';
		}
		jQuery('.wpsc_popup_action').text('<?php _e('Please wait ...','supportcandy')?>');
		jQuery('.wpsc_popup_action, #wpsc_popup_body input').attr("disabled", "disabled");
		var data = {
			action: 'wpsc_settings',
			setting_action : 'set_edit_priority',
			priority_id : priority_id,
			priority_name : priority_name,
			priority_color: priority_color,
			priority_bg_color: priority_bg_color
		};
		jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
			wpsc_modal_close();
			var response = JSON.parse(response_str);
			if (response.sucess_status=='1') {
				wpsc_get_priority_settings();
			} else {
				jQuery('#wpsc_alert_error .wpsc_alert_text').text(response.messege);
				jQuery('#wpsc_alert_error').slideDown('fast',function(){});
				setTimeout(function(){ jQuery('#wpsc_alert_error').slideUp('fast',function(){}); }, 3000);
			}
		});
	}
	
	function wpsc_delete_priority(priority_id){
		var flag = confirm(wpsc_admin.are_you_sure);
		if (flag) {
			var data = {
				action: 'wpsc_settings',
				setting_action : 'delete_priority',
				priority_id : priority_id
			};
			jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
				var response = JSON.parse(response_str);
				if (response.sucess_status=='1') {
					jQuery('#wpsc_alert_success .wpsc_alert_text').text(response.messege);
					jQuery('#wpsc_alert_success').slideDown('fast',function(){});
					setTimeout(function(){ jQuery('#wpsc_alert_success').slideUp('fast',function(){}); }, 3000);
					wpsc_get_priority_settings();
				} else {
					jQuery('#wpsc_alert_error .wpsc_alert_text').text(response.messege);
					jQuery('#wpsc_alert_error').slideDown('fast',function(){});
					setTimeout(function(){ jQuery('#wpsc_alert_error').slideUp('fast',function(){}); }, 3000);
				}
			});
		}
	}
	
</script>
