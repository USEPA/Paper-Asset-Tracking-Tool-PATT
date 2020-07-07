<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$setting_action =  isset($_REQUEST['setting_action']) ? sanitize_text_field($_REQUEST['setting_action']) : '';

switch ($setting_action) {
  
  case 'ticket_statistics': include WPSC_RP_ABSPATH . 'includes/ticket_statistics/ticket_stat_report_by_filter.php';
    break;		  
  
	case 'first_response_time' : include WPSC_RP_ABSPATH . 'includes/first_response_time/first_response_time_report_filter.php';
		break;
  
	case 'ticket_category' : include WPSC_RP_ABSPATH .  'includes/category/ticket_category_report_by_filter.php';
		break;
	
	case 'custom_field_dropdowm' : 	include WPSC_RP_ABSPATH .  'includes/dropdown/get_all_dropdown_report_by_filter.php';
		break;
	
	case 'custom_field_checkbox' : 	include WPSC_RP_ABSPATH .  'includes/checkbox/get_all_checkbox_report_by_filter.php';
		break;
	
	case 'custom_field_radio_button' : 	include WPSC_RP_ABSPATH .  'includes/radio_button/get_all_radio_button_report_by_filter.php';
	  break;
	
	case 'active_customers' : include WPSC_RP_ABSPATH .  'includes/active_customers/get_all_active_customers_report_by_filter.php';
	  break;	
	
	default:
    _e('Invalid Action','wpsc-rp');
    break;
}