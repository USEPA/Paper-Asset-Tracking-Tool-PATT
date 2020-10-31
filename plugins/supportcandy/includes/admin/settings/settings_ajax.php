<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user;
if (!($current_user->ID)) {
	exit;
}

$setting_action =  isset($_POST['setting_action']) ? sanitize_text_field($_POST['setting_action']) : '';

switch ($setting_action) {
  case 'get_general_settings': include WPSC_ABSPATH . 'includes/admin/settings/get_general_settings.php';
    break;
		
	case 'set_general_settings': include WPSC_ABSPATH . 'includes/admin/settings/set_general_settings.php';
    break;
		
	case 'get_category_settings': include WPSC_ABSPATH . 'includes/admin/settings/get_category_settings.php';
    break;
		
	case 'get_add_category': include WPSC_ABSPATH . 'includes/admin/settings/get_add_category.php';
    break;
		
	case 'set_add_category': include WPSC_ABSPATH . 'includes/admin/settings/set_add_category.php';
    break;
		
	case 'set_category_order': include WPSC_ABSPATH . 'includes/admin/settings/set_category_order.php';
    break;
		
	case 'get_edit_category': include WPSC_ABSPATH . 'includes/admin/settings/get_edit_category.php';
    break;
		
	case 'set_edit_category': include WPSC_ABSPATH . 'includes/admin/settings/set_edit_category.php';
    break;
	
	case 'delete_category': include WPSC_ABSPATH . 'includes/admin/settings/delete_category.php';
    break;
		
	case 'get_status_settings': include WPSC_ABSPATH . 'includes/admin/settings/get_status_settings.php';
    break;
		
	case 'get_add_status': include WPSC_ABSPATH . 'includes/admin/settings/get_add_status.php';
    break;
		
	case 'set_add_status': include WPSC_ABSPATH . 'includes/admin/settings/set_add_status.php';
    break;
		
	case 'get_edit_status': include WPSC_ABSPATH . 'includes/admin/settings/get_edit_status.php';
    break;
		
	case 'set_edit_status': include WPSC_ABSPATH . 'includes/admin/settings/set_edit_status.php';
    break;
		
	case 'delete_status': include WPSC_ABSPATH . 'includes/admin/settings/delete_status.php';
    break;
		
	case 'set_status_order': include WPSC_ABSPATH . 'includes/admin/settings/set_status_order.php';
    break;
		
	case 'get_priority_settings': include WPSC_ABSPATH . 'includes/admin/settings/get_priority_settings.php';
    break;
		
	case 'get_add_priority': include WPSC_ABSPATH . 'includes/admin/settings/get_add_priority.php';
    break;
		
	case 'set_add_priority': include WPSC_ABSPATH . 'includes/admin/settings/set_add_priority.php';
    break;
		
	case 'get_edit_priority': include WPSC_ABSPATH . 'includes/admin/settings/get_edit_priority.php';
    break;
		
	case 'set_edit_priority': include WPSC_ABSPATH . 'includes/admin/settings/set_edit_priority.php';
    break;
		
	case 'delete_priority': include WPSC_ABSPATH . 'includes/admin/settings/delete_priority.php';
    break;
		
	case 'set_priority_order': include WPSC_ABSPATH . 'includes/admin/settings/set_priority_order.php';
    break;
		
	case 'get_ticket_widget_settings': include WPSC_ABSPATH . 'includes/admin/settings/get_ticket_widget_settings.php';
		break;
		
	case 'set_ticket_widgets':include WPSC_ABSPATH . 'includes/admin/settings/set_ticket_widgets.php';
		break;
		
	case 'get_edit_ticket_widget': include WPSC_ABSPATH . 'includes/admin/settings/get_edit_ticket_widget.php';
		break;
		
	case 'set_edit_ticket_widgets':include WPSC_ABSPATH . 'includes/admin/settings/set_edit_ticket_widgets.php';
		break;
		
	case 'get_thankyou_settings': include WPSC_ABSPATH . 'includes/admin/settings/get_thankyou_settings.php';
    break;
		
	case 'set_thankyou_settings': include WPSC_ABSPATH . 'includes/admin/settings/set_thankyou_settings.php';
    break;
		
	case 'get_templates': include WPSC_ABSPATH . 'includes/admin/settings/get_templates.php';
    break;
		
	case 'get_agent_roles': include WPSC_ABSPATH . 'includes/admin/settings/get_agent_roles.php';
    break;
		
	case 'get_add_agent_role': include WPSC_ABSPATH . 'includes/admin/settings/get_add_agent_role.php';
    break;
		
	case 'set_add_agent_role': include WPSC_ABSPATH . 'includes/admin/settings/set_add_agent_role.php';
    break;
		
	case 'get_edit_agent_role': include WPSC_ABSPATH . 'includes/admin/settings/get_edit_agent_role.php';
    break;
		
	case 'set_edit_agent_role': include WPSC_ABSPATH . 'includes/admin/settings/set_edit_agent_role.php';
    break;
		
	case 'delete_agent_role': include WPSC_ABSPATH . 'includes/admin/settings/delete_agent_role.php';
    break;
	
	case 'get_cron_setup_settings': include WPSC_ABSPATH . 'includes/admin/settings/get_cron_setup_settings.php';
    break;
		
	case 'get_terms_and_condition_settings': include WPSC_ABSPATH . 'includes/admin/settings/get_terms_and_condition_settings.php';
    break;
		
	case 'set_terms_and_condition_settings': include WPSC_ABSPATH . 'includes/admin/settings/set_terms_and_condition_settings.php';
    break;
		
	case 'set_cron_setup_settings': include WPSC_ABSPATH . 'includes/admin/settings/set_cron_setup_settings.php';
    break;
		
	case 'get_advanced_settings': include WPSC_ABSPATH . 'includes/admin/settings/get_advanced_settings.php';
    break;
		
	case 'set_advanced_settings': include WPSC_ABSPATH . 'includes/admin/settings/set_advanced_settings.php';
    break;
	
	case 'get_captcha_settings': include WPSC_ABSPATH . 'includes/admin/settings/get_captcha_settings.php';
	    break;
	
	case 'set_captcha_settings': include WPSC_ABSPATH . 'includes/admin/settings/set_captcha_settings.php';
			break;
			
	case 'get_rest_api_settings': include WPSC_ABSPATH . 'includes/admin/settings/get_rest_api_settings.php';
	    break;
			
	case 'set_rest_api_settings': include WPSC_ABSPATH . 'includes/admin/settings/set_rest_api_settings.php';
	    break;
			
	case 'custom_start_ticket_number' : 	include WPSC_ABSPATH . 'includes/admin/settings/custom_start_ticket_number.php';
		break;
	
	case 'get_tinymce_settings'	: include WPSC_ABSPATH . 'includes/admin/settings/get_tinymce_settings.php';
		break;

	case 'set_tinymce_settings'	 : include WPSC_ABSPATH . 'includes/admin/settings/set_tinymce_settings.php';
		break;

	case 'get_attachment_settings': include WPSC_ABSPATH . 'includes/admin/settings/get_attachment_settings.php';
		break;
			
	case 'set_attachment_settings': include WPSC_ABSPATH . 'includes/admin/settings/set_attachment_settings.php';
		break;
				
	default:
    _e('Invalid Action','supportcandy');
    break;
}
