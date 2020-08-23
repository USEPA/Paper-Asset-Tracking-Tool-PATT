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
  
  case 'get_en_general_setting': include WPSC_ABSPATH . 'includes/admin/email_notifications/get_en_general_setting.php';
    break;
		
	case 'set_en_general_settings': include WPSC_ABSPATH . 'includes/admin/email_notifications/set_en_general_settings.php';
    break;
		
	case 'get_en_ticket_notifications': include WPSC_ABSPATH . 'includes/admin/email_notifications/get_en_ticket_notifications.php';
    break;
		
	case 'get_add_ticket_notification': include WPSC_ABSPATH . 'includes/admin/email_notifications/get_add_ticket_notification.php';
    break;
		
	case 'set_add_ticket_notification': include WPSC_ABSPATH . 'includes/admin/email_notifications/set_add_ticket_notification.php';
    break;
		
	case 'get_edit_ticket_notification': include WPSC_ABSPATH . 'includes/admin/email_notifications/get_edit_ticket_notification.php';
    break;
		
	case 'set_edit_ticket_notification': include WPSC_ABSPATH . 'includes/admin/email_notifications/set_edit_ticket_notification.php';
    break;
		
	case 'clone_ticket_notification': include WPSC_ABSPATH . 'includes/admin/email_notifications/clone_ticket_notification.php';
    break;
		
	case 'delete_ticket_notification': include WPSC_ABSPATH . 'includes/admin/email_notifications/delete_ticket_notification.php';
    break;
    
  default:
    _e('Invalid Action','supportcandy');
    break;
    
}
