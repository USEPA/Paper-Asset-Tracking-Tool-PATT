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
  
  case 'get_agent_ticket_list': include WPSC_ABSPATH . 'includes/admin/ticket_list/get_agent_ticket_list.php';
    break;
    
  case 'set_agent_list_order': include WPSC_ABSPATH . 'includes/admin/ticket_list/set_agent_list_order.php';
    break;
    
  case 'get_add_agent_list_field': include WPSC_ABSPATH . 'includes/admin/ticket_list/get_add_agent_list_field.php';
    break;
    
  case 'set_add_agent_list_field': include WPSC_ABSPATH . 'includes/admin/ticket_list/set_add_agent_list_field.php';
    break;
		
	case 'delete_agent_list_field': include WPSC_ABSPATH . 'includes/admin/ticket_list/delete_agent_list_field.php';
    break;
		
	case 'get_customer_ticket_list': include WPSC_ABSPATH . 'includes/admin/ticket_list/get_customer_ticket_list.php';
    break;
		
	case 'set_customer_list_order': include WPSC_ABSPATH . 'includes/admin/ticket_list/set_customer_list_order.php';
    break;
		
	case 'get_add_customer_list_field': include WPSC_ABSPATH . 'includes/admin/ticket_list/get_add_customer_list_field.php';
    break;
		
	case 'set_add_customer_list_field': include WPSC_ABSPATH . 'includes/admin/ticket_list/set_add_customer_list_field.php';
    break;
		
	case 'delete_customer_list_field': include WPSC_ABSPATH . 'includes/admin/ticket_list/delete_customer_list_field.php';
    break;
		
	case 'get_agent_ticket_filters': include WPSC_ABSPATH . 'includes/admin/ticket_list/get_agent_ticket_filters.php';
    break;
		
	case 'set_agent_filter_order': include WPSC_ABSPATH . 'includes/admin/ticket_list/set_agent_filter_order.php';
    break;
		
	case 'get_add_agent_ticket_filters': include WPSC_ABSPATH . 'includes/admin/ticket_list/get_add_agent_ticket_filters.php';
    break;
		
	case 'set_add_agent_ticket_filters': include WPSC_ABSPATH . 'includes/admin/ticket_list/set_add_agent_ticket_filters.php';
    break;
		
	case 'delete_agent_filter_item': include WPSC_ABSPATH . 'includes/admin/ticket_list/delete_agent_filter_item.php';
    break;
		
	case 'get_customer_ticket_filters': include WPSC_ABSPATH . 'includes/admin/ticket_list/get_customer_ticket_filters.php';
    break;
		
	case 'set_customer_filter_order': include WPSC_ABSPATH . 'includes/admin/ticket_list/set_customer_filter_order.php';
    break;
		
	case 'get_add_customer_ticket_filters': include WPSC_ABSPATH . 'includes/admin/ticket_list/get_add_customer_ticket_filters.php';
    break;
		
	case 'set_add_customer_ticket_filters': include WPSC_ABSPATH . 'includes/admin/ticket_list/set_add_customer_ticket_filters.php';
    break;
		
	case 'delete_customer_filter_item': include WPSC_ABSPATH . 'includes/admin/ticket_list/delete_customer_filter_item.php';
    break;
		
	case 'get_ticket_list_additional_settings': include WPSC_ABSPATH . 'includes/admin/ticket_list/get_ticket_list_additional_settings.php';
    break;
		
	case 'set_ticket_list_additional_settings': include WPSC_ABSPATH . 'includes/admin/ticket_list/set_ticket_list_additional_settings.php';
    break;

    case 'get_ticket_list_advanced_settings': include WPSC_ABSPATH . 'includes/admin/ticket_list/get_ticket_list_advanced_settings.php';
    break;
  
    case 'set_ticket_list_advanced_settings': include WPSC_ABSPATH . 'includes/admin/ticket_list/set_ticket_list_advanced_settings.php';
    break;

  default:
    _e('Invalid Action','supportcandy');
    break;
    
}
