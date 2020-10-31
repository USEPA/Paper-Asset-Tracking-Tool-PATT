<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user;
if (!($current_user->ID)) {
	exit;
}

$setting_action =  isset($_REQUEST['setting_action']) ? sanitize_text_field($_REQUEST['setting_action']) : '';

switch ($setting_action) {
  
  case 'get_support_agents': include WPSC_ABSPATH . 'includes/admin/support_agents/get_support_agents.php';
    break;
		
	case 'get_add_support_agent': include WPSC_ABSPATH . 'includes/admin/support_agents/get_add_support_agent.php';
    break;
		
	case 'get_users_add_agent': include WPSC_ABSPATH . 'includes/admin/support_agents/get_users_add_agent.php';
    break;
		
	case 'set_add_support_agent': include WPSC_ABSPATH . 'includes/admin/support_agents/set_add_support_agent.php';
    break;
		
	case 'get_edit_support_agent': include WPSC_ABSPATH . 'includes/admin/support_agents/get_edit_support_agent.php';
    break;
		
	case 'set_edit_support_agent': include WPSC_ABSPATH . 'includes/admin/support_agents/set_edit_support_agent.php';
    break;
		
	case 'delete_support_agent': include WPSC_ABSPATH . 'includes/admin/support_agents/delete_support_agent.php';
    break;
		
	case 'get_agent_setting': include WPSC_ABSPATH . 'includes/admin/support_agents/get_agent_setting.php';
		break;
		
	case 'set_agent_setting': include WPSC_ABSPATH . 'includes/admin/support_agents/set_agent_setting.php';
		break;
	
  default:
    _e('Invalid Action','supportcandy');
    break;
    
}
