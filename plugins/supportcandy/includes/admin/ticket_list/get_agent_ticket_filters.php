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
	'meta_key'	 => 'wpsc_filter_agent_load_order',
	'order'    	 => 'ASC',
	'meta_query' => array(
		'relation' => 'AND',
    array(
      'key'       => 'wpsc_allow_ticket_filter',
      'value'     => '1',
      'compare'   => '='
    ),
    array(
      'key'       => 'wpsc_agent_ticket_filter_status',
      'value'     => '1',
      'compare'   => '='
    )
	),
]);

?>
<h4>
	<?php _e('Agent Ticket Filters','supportcandy');?>
	<button style="margin-left:10px;" class="btn btn-success btn-sm" id="wpsc_add_new_agent_ticket_filter_btn" onclick="wpsc_get_add_agent_ticket_filters();"><?php _e('+Add New','supportcandy');?></button>
</h4>

<div class="wpsc_padding_space"></div>

<ul class="wpsc-sortable">
	<?php foreach ( $fields as $field ) :
		$label = get_term_meta( $field->term_id, 'wpsc_tf_label', true);
		?>
		<li class="ui-state-default" data-id="<?php echo $field->term_id?>">
			<div class="wpsc-flex-container" style="background-color:#1E90FF;color:#fff;">
				<div class="wpsc-sortable-handle"><i class="fa fa-bars"></i></div>
				<div class="wpsc-sortable-label"><?php echo $label?></div>
				<div class="wpsc-sortable-delete" onclick="wpsc_delete_agent_filter_item(<?php echo $field->term_id?>);" style="padding-left:30px;"><i class="fa fa-trash"></i></div>
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
		    action: 'wpsc_ticket_list',
		    setting_action : 'set_agent_filter_order',
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
	
	function wpsc_get_add_agent_ticket_filters(){
		wpsc_modal_open(wpsc_admin.add_filter_item);
		var data = {
			action: 'wpsc_ticket_list',
			setting_action : 'get_add_agent_ticket_filters'
		};
		jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
			var response = JSON.parse(response_str);
			jQuery('#wpsc_popup_body').html(response.body);
			jQuery('#wpsc_popup_footer').html(response.footer);
		});
	}
	
	function wpsc_set_add_agent_ticket_filters(){
		var field_id = jQuery('#wpsc_al_item').val();
		jQuery('.wpsc_popup_action').text('<?php _e('Please wait ...','supportcandy')?>');
		jQuery('.wpsc_popup_action, #wpsc_popup_body input').attr("disabled", "disabled");
		var data = {
			action: 'wpsc_ticket_list',
			setting_action : 'set_add_agent_ticket_filters',
			field_id : field_id
		};
		jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
			wpsc_modal_close();
			var response = JSON.parse(response_str);
			if (response.sucess_status=='1') {
				jQuery('#wpsc_alert_success .wpsc_alert_text').text(response.messege);
				jQuery('#wpsc_alert_success').slideDown('fast',function(){});
				setTimeout(function(){ jQuery('#wpsc_alert_success').slideUp('fast',function(){}); }, 3000);
				wpsc_get_agent_ticket_filters();
			} else {
				jQuery('#wpsc_alert_error .wpsc_alert_text').text(response.messege);
				jQuery('#wpsc_alert_error').slideDown('fast',function(){});
				setTimeout(function(){ jQuery('#wpsc_alert_error').slideUp('fast',function(){}); }, 3000);
			}
		});
	}
	
  function wpsc_delete_agent_filter_item(field_id){
		
		var flag = confirm(wpsc_admin.are_you_sure);
		if (flag) {
			var data = {
				action: 'wpsc_ticket_list',
				setting_action : 'delete_agent_filter_item',
				field_id : field_id
			};
			jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
				var response = JSON.parse(response_str);
				if (response.sucess_status=='1') {
					jQuery('#wpsc_alert_success .wpsc_alert_text').text(response.messege);
					jQuery('#wpsc_alert_success').slideDown('fast',function(){});
					setTimeout(function(){ jQuery('#wpsc_alert_success').slideUp('fast',function(){}); }, 3000);
					wpsc_get_agent_ticket_filters();
				} else {
					jQuery('#wpsc_alert_error .wpsc_alert_text').text(response.messege);
					jQuery('#wpsc_alert_error').slideDown('fast',function(){});
					setTimeout(function(){ jQuery('#wpsc_alert_error').slideUp('fast',function(){}); }, 3000);
				}
			});
		}
	}
	
</script>
