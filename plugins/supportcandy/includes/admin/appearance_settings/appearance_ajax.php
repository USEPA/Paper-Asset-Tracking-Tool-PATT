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
  
  case 'get_appearance_general_settings': include WPSC_ABSPATH . 'includes/admin/appearance_settings/get_appearance_general_settings.php';
    break;
		
	case 'set_appearance_general_settings': include WPSC_ABSPATH . 'includes/admin/appearance_settings/set_appearance_general_settings.php';
	  break;
		
	case 'get_reset_default_general_settings': include WPSC_ABSPATH . 'includes/admin/appearance_settings/get_reset_default_general_settings.php';
	  break;
		
	case 'get_appearance_ticket_list': include WPSC_ABSPATH . 'includes/admin/appearance_settings/get_appearance_ticket_list.php';
	  break;
		
	case 'set_appearance_ticket_list_settings': include WPSC_ABSPATH . 'includes/admin/appearance_settings/set_appearance_ticket_list_settings.php';
	  break;
		
	case 'get_reset_default_ticket_list_settings': include WPSC_ABSPATH . 'includes/admin/appearance_settings/get_reset_default_ticket_list_settings.php';
	  break;
		
	case 'get_appearance_individual_ticket_page': include WPSC_ABSPATH . 'includes/admin/appearance_settings/get_appearance_individual_ticket_page.php';
	  break;
		
	case 'set_appearance_individual_ticket_settings': include WPSC_ABSPATH . 'includes/admin/appearance_settings/set_appearance_individual_ticket_settings.php';
		break;
		
	case 'get_reset_default_individual_ticket_settings': include WPSC_ABSPATH . 'includes/admin/appearance_settings/get_reset_default_individual_ticket_settings.php';
		break;
		
	case 'get_appearance_create_ticket': include WPSC_ABSPATH . 'includes/admin/appearance_settings/get_appearance_create_ticket.php';
		break;
		
	case 'set_appearance_create_ticket_settings': include WPSC_ABSPATH . 'includes/admin/appearance_settings/set_appearance_create_ticket_settings.php';
		break;
		
	case 'get_reset_default_create_ticket_settings': include WPSC_ABSPATH . 'includes/admin/appearance_settings/get_reset_default_create_ticket_settings.php';
		break;
		
	case 'get_appearance_modal_window': include WPSC_ABSPATH . 'includes/admin/appearance_settings/get_appearance_modal_window.php';
		break;
		
	case 'set_appearance_modal_window_settings': include WPSC_ABSPATH . 'includes/admin/appearance_settings/set_appearance_modal_window_settings.php';
		break;
		
	case 'get_reset_default_modal_window_settings': include WPSC_ABSPATH . 'includes/admin/appearance_settings/get_reset_default_modal_window_settings.php';
		break;
	
	case 'get_appearance_login_form':include WPSC_ABSPATH . 'includes/admin/appearance_settings/get_appearance_login_form.php';
		break;
		
	case 'set_appearance_login_form':include WPSC_ABSPATH . 'includes/admin/appearance_settings/set_appearance_login_form.php';
		break;
		
	case 'get_reset_default_apperance_login_form':include WPSC_ABSPATH . 'includes/admin/appearance_settings/get_reset_default_apperance_login_form.php';
		break;
		
	case 'get_appearance_signup': include WPSC_ABSPATH . 'includes/admin/appearance_settings/get_appearance_signup.php'; 
		break;
		
	case 'set_appearance_signup':include WPSC_ABSPATH . 'includes/admin/appearance_settings/set_appearance_signup.php'; 
		break;
			
	case  'get_reset_default_signup_settings': include WPSC_ABSPATH . 'includes/admin/appearance_settings/get_reset_default_signup_settings.php';
		break;
		
	default:
	  _e('Invalid Action','supportcandy');
	  break;
		
}