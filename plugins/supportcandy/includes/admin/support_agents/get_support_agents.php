<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user, $wpdb;
if (!($current_user->ID && $current_user->has_cap('edit_published_posts'))) {
	exit;
}

$agents = get_terms([
	'taxonomy'   => 'wpsc_agents',
	'hide_empty' => false,
	'meta_query' => array(
    array(
      'key'       => 'agentgroup',
      'value'     => '0',
      'compare'   => '='
    )
  )
]);

$agent_role = get_option('wpsc_agent_role');

?>
<h4>
	<?php _e('Support Agents','supportcandy');?>
	<button style="margin-left:10px;" class="btn btn-success btn-sm" id="wpsc_add_new_support_agent_btn" onclick="wpsc_get_add_support_agent();"><?php _e('+Add New','supportcandy');?></button>
</h4>

<div class="wpsc_padding_space"></div>

<table class="table table-striped table-hover">
  <tr>
    <th><?php _e('Agent Name','supportcandy')?></th>
    <th><?php _e('Role','supportcandy')?></th>
    <th><?php _e('Actions','supportcandy')?></th>
  </tr>
  <?php foreach ( $agents as $agent ) :
    $agent_name = get_term_meta( $agent->term_id, 'label', true);
    $role_id = get_term_meta( $agent->term_id, 'role', true);
    ?>
    <tr>
      <td><?php echo htmlentities($agent_name)?></td>
      <td><?php echo $agent_role[$role_id]['label']?></td>
      <td>
        <div class="wpsc_flex">
          <div onclick="wpsc_get_edit_support_agent(<?php echo $agent->term_id;?>);" style="cursor:pointer;"><i class="fa fa-edit"></i></div>
          <div onclick="wpsc_delete_support_agent(<?php echo $agent->term_id;?>);" style="cursor:pointer; padding-left: 10px;"><i class="fa fa-trash"></i></div>
		  <?php do_action('wpsc_support_agent_add_action', $agent); ?>
        </div>
      </td>
    </tr>
	<?php endforeach;?>
</table>

<script>
	
	function wpsc_get_add_support_agent(){
		wpsc_modal_open(wpsc_admin.add_new_agent);
		var data = {
			action: 'wpsc_support_agents',
			setting_action : 'get_add_support_agent'
		};
		jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
			var response = JSON.parse(response_str);
			jQuery('#wpsc_popup_body').html(response.body);
			jQuery('#wpsc_popup_footer').html(response.footer);
			jQuery('#wpsc_cat_name').focus();
		});
	}
	
	function wpsc_set_add_support_agent(){
		var agent_name = jQuery('#wpsc_agent_name').val().trim();
		if (agent_name.length == 0) {
			jQuery('#wpsc_agent_name').val('').focus();
			return;
		}
		var agent_id = jQuery('#wpsc_agent_id').val().trim();
		if (!agent_id) {
			jQuery('#wpsc_agent_name').focus();
			return;
		}
		var agent_role = jQuery('#wpsc_agent_role').val();
		
		jQuery('.wpsc_popup_action').text('<?php _e('Please wait ...','supportcandy')?>');
		jQuery('.wpsc_popup_action, #wpsc_popup_body input').attr("disabled", "disabled");
		var data = {
			action: 'wpsc_support_agents',
			setting_action : 'set_add_support_agent',
			agent_name : agent_name,
			agent_id : agent_id,
			agent_role : agent_role
		};
		jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
			wpsc_modal_close();
			var response = JSON.parse(response_str);
			if (response.sucess_status=='1') {
				jQuery('#wpsc_alert_success .wpsc_alert_text').text(response.messege);
				jQuery('#wpsc_alert_success').slideDown('fast',function(){});
				setTimeout(function(){ jQuery('#wpsc_alert_success').slideUp('fast',function(){}); }, 3000);
				wpsc_get_support_agents();
			} else {
				jQuery('#wpsc_alert_error .wpsc_alert_text').text(response.messege);
				jQuery('#wpsc_alert_error').slideDown('fast',function(){});
				setTimeout(function(){ jQuery('#wpsc_alert_error').slideUp('fast',function(){}); }, 3000);
			}
		});
	}
	
	function wpsc_get_edit_support_agent(agent_id){
		wpsc_modal_open(wpsc_admin.edit_agent);
		var data = {
			action: 'wpsc_support_agents',
			setting_action : 'get_edit_support_agent',
			agent_id : agent_id
		};
		jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
			var response = JSON.parse(response_str);
			jQuery('#wpsc_popup_body').html(response.body);
			jQuery('#wpsc_popup_footer').html(response.footer);
			jQuery('#wpsc_cat_name').focus();
		});
	}
	
	function wpsc_set_edit_support_agent(agent_id){
		var agent_role = jQuery('#wpsc_agent_role').val();
		jQuery('.wpsc_popup_action').text('<?php _e('Please wait ...','supportcandy')?>');
		jQuery('.wpsc_popup_action, #wpsc_popup_body input').attr("disabled", "disabled");
		var data = {
			action: 'wpsc_support_agents',
			setting_action : 'set_edit_support_agent',
			agent_id : agent_id,
			agent_role : agent_role
		};
		jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
			wpsc_modal_close();
			wpsc_get_support_agents();
		});
	}
	
	function wpsc_delete_support_agent(agent_id){
		
		var flag = confirm(wpsc_admin.are_you_sure);
		if (flag) {
			var data = {
				action: 'wpsc_support_agents',
				setting_action : 'delete_support_agent',
				agent_id : agent_id
			};
			jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
				var response = JSON.parse(response_str);
				if (response.sucess_status=='1') {
					jQuery('#wpsc_alert_success .wpsc_alert_text').text(response.messege);
					jQuery('#wpsc_alert_success').slideDown('fast',function(){});
					setTimeout(function(){ jQuery('#wpsc_alert_success').slideUp('fast',function(){}); }, 3000);
					wpsc_get_support_agents();
				} else {
					jQuery('#wpsc_alert_error .wpsc_alert_text').text(response.messege);
					jQuery('#wpsc_alert_error').slideDown('fast',function(){});
					setTimeout(function(){ jQuery('#wpsc_alert_error').slideUp('fast',function(){}); }, 3000);
				}
			});
		}
	}

	<?php do_action( "wpsc_add_extra_script_support_agent" ); ?>
</script>
