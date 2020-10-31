<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user;
if (!($current_user->ID && $current_user->has_cap('edit_published_posts'))) {
	exit;
}

$setting_action =  isset($_POST['setting_action']) ? sanitize_text_field($_POST['setting_action']) : '';

switch ($setting_action) {
  
  case 'get_ticket_form_fields': include WPSC_ABSPATH . 'includes/admin/custom_fields/get_ticket_form_fields.php';
    break;
		
	case 'set_form_field_order': include WPSC_ABSPATH . 'includes/admin/custom_fields/set_form_field_order.php';
    break;
		
	case 'get_add_form_field': include WPSC_ABSPATH . 'includes/admin/custom_fields/get_add_form_field.php';
    break;
		
	case 'set_add_form_field': include WPSC_ABSPATH . 'includes/admin/custom_fields/set_add_form_field.php';
    break;
		
	case 'get_edit_form_field': include WPSC_ABSPATH . 'includes/admin/custom_fields/get_edit_form_field.php';
    break;
		
	case 'set_edit_form_field': include WPSC_ABSPATH . 'includes/admin/custom_fields/set_edit_form_field.php';
    break;
		
	case 'get_edit_default_form_field': include WPSC_ABSPATH . 'includes/admin/custom_fields/get_edit_default_form_field.php';
    break;
		
	case 'set_edit_default_form_field': include WPSC_ABSPATH . 'includes/admin/custom_fields/set_edit_default_form_field.php';
    break;
		
	case 'get_conditional_options': include WPSC_ABSPATH . 'includes/admin/custom_fields/get_conditional_options.php';
    break;
		
	case 'delete_custom_field': include WPSC_ABSPATH . 'includes/admin/custom_fields/delete_custom_field.php';
    break;
		
	case 'get_agentonly_fields': include WPSC_ABSPATH . 'includes/admin/custom_fields/get_agentonly_fields.php';
    break;
		
	case 'get_add_agentonly_field': include WPSC_ABSPATH . 'includes/admin/custom_fields/get_add_agentonly_field.php';
    break;
		
	case 'set_add_agentonly_field': include WPSC_ABSPATH . 'includes/admin/custom_fields/set_add_agentonly_field.php';
    break;
		
	case 'get_edit_agentonly_field': include WPSC_ABSPATH . 'includes/admin/custom_fields/get_edit_agentonly_field.php';
    break;
		
	case 'set_edit_agentonly_field': include WPSC_ABSPATH . 'includes/admin/custom_fields/set_edit_agentonly_field.php';
    break;
  
	case 'get_conditional_options_fields': include WPSC_ABSPATH . 'includes/admin/custom_fields/get_conditional_options_fields.php';
	  break;
			
  default:
    _e('Invalid Action','supportcandy');
    break;
    
}
