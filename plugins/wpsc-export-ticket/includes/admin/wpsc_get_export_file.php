<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user, $wpscfunction;
if (!$current_user->ID) exit;

$general_appearance = get_option('wpsc_appearance_general_settings');
$action_default_btn_css = 'background-color:'.$general_appearance['wpsc_default_btn_action_bar_bg_color'].' !important;color:'.$general_appearance['wpsc_default_btn_action_bar_text_color'].' !important;';

$export_role = get_option('wpsc_selected_user_roll_data',array());
$flag_btn    = false;

if($current_user->has_cap('wpsc_agent')){
    
	$current_agent_id      = $wpscfunction->get_current_user_agent_id();
	$current_agent_role_id = get_term_meta($current_agent_id,'role',true);
	
	if(in_array($current_agent_role_id,$export_role)){
		$flag_btn = true;
	}
		
} else if (in_array('customer', $export_role)){
    $flag_btn = true;
} 

if (is_multisite() || is_super_admin($current_user->ID)) {
	$flag_btn = true;
}

if($flag_btn):?>
	<button type="button" class="btn btn-sm wpsc_action_btn" id="wpsc_export_ticket_btn" style="<?php echo $action_default_btn_css ?>" onclick="wpsc_get_export_ticket_field();"><i class="fas fa-cloud-download-alt"></i> <?php _e('Export','wpsc-export-ticket')?></button>
	<script>
		function wpsc_get_export_ticket_field(){
		  wpsc_modal_open(wpsc_admin.export_ticket);
		  var data = {
		    action: 'wpsc_get_export_ticket_field'  
		  };
		  jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
		    var response = JSON.parse(response_str);
		    jQuery('#wpsc_popup_body').html(response.body);
		    jQuery('#wpsc_popup_footer').html(response.footer);
		    jQuery('#wpsc_cat_name').focus();
		  });  
		}
	</script>
	<?php
endif;
