<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user,$wpscfunction;
if (!($current_user->ID && $current_user->has_cap('edit_published_posts'))) {
	exit;
}

// Support page id
$wpsc_support_page_id = isset($_POST) && isset($_POST['wpsc_support_page_id']) ? sanitize_text_field($_POST['wpsc_support_page_id']) : '';
update_option('wpsc_support_page_id',$wpsc_support_page_id);

// Default Status
$wpsc_default_ticket_status = isset($_POST) && isset($_POST['wpsc_default_ticket_status']) ? sanitize_text_field($_POST['wpsc_default_ticket_status']) : '';
update_option('wpsc_default_ticket_status',$wpsc_default_ticket_status);

// Default Categories
$wpsc_default_ticket_category = isset($_POST) && isset($_POST['wpsc_default_ticket_category']) ? sanitize_text_field($_POST['wpsc_default_ticket_category']) : '';
update_option('wpsc_default_ticket_category',$wpsc_default_ticket_category);

// Default Priorities
$wpsc_default_ticket_priority = isset($_POST) && isset($_POST['wpsc_default_ticket_priority']) ? sanitize_text_field($_POST['wpsc_default_ticket_priority']) : '';
update_option('wpsc_default_ticket_priority',$wpsc_default_ticket_priority);

// Ticket status after customer reply
$wpsc_ticket_status_after_customer_reply = isset($_POST) && isset($_POST['wpsc_ticket_status_after_customer_reply']) ? sanitize_text_field($_POST['wpsc_ticket_status_after_customer_reply']) : '';
update_option('wpsc_ticket_status_after_customer_reply',$wpsc_ticket_status_after_customer_reply);

// Ticket status after agent reply
$wpsc_ticket_status_after_agent_reply = isset($_POST) && isset($_POST['wpsc_ticket_status_after_agent_reply']) ? sanitize_text_field($_POST['wpsc_ticket_status_after_agent_reply']) : '';
update_option('wpsc_ticket_status_after_agent_reply',$wpsc_ticket_status_after_agent_reply);

// Close ticket status
$wpsc_close_ticket_status = isset($_POST) && isset($_POST['wpsc_close_ticket_status']) ? sanitize_text_field($_POST['wpsc_close_ticket_status']) : '';
update_option('wpsc_close_ticket_status',$wpsc_close_ticket_status);

// Allow customer to close ticket
$wpsc_allow_customer_close_ticket = isset($_POST) && isset($_POST['wpsc_allow_customer_close_ticket']) ? sanitize_text_field($_POST['wpsc_allow_customer_close_ticket']) : '1';
update_option('wpsc_allow_customer_close_ticket',$wpsc_allow_customer_close_ticket);

// Reply form position
$wpsc_reply_form_position = isset($_POST) && isset($_POST['wpsc_reply_form_position']) ? sanitize_text_field($_POST['wpsc_reply_form_position']) : '1';
update_option('wpsc_reply_form_position',$wpsc_reply_form_position);

// Calender date format
$wpsc_calender_date_format = isset($_POST) && isset($_POST['wpsc_calender_date_format']) ? sanitize_text_field($_POST['wpsc_calender_date_format']) : 'dd-mm-yy';
update_option('wpsc_calender_date_format',$wpsc_calender_date_format);

// Allow guest ticket
$wpsc_allow_guest_ticket = isset($_POST) && isset($_POST['wpsc_allow_guest_ticket']) ? sanitize_text_field($_POST['wpsc_allow_guest_ticket']) : '0';
update_option('wpsc_allow_guest_ticket',$wpsc_allow_guest_ticket);

$wpsc_ticket_alice = isset($_POST) && isset($_POST['wpsc_ticket_alice']) ? sanitize_text_field($_POST['wpsc_ticket_alice']) : 'Ticket';
update_option('wpsc_ticket_alice',$wpsc_ticket_alice);

// Custom Login
$wpsc_custom_login_url = isset($_POST) && isset($_POST['wpsc_custom_login_url']) ? sanitize_text_field($_POST['wpsc_custom_login_url']) : '';
update_option('wpsc_custom_login_url',$wpsc_custom_login_url);

// Reply to close ticket
$wpsc_allow_reply_to_close_ticket = isset($_POST) && isset($_POST['wpsc_allow_rtct']) ? $wpscfunction->sanitize_array($_POST['wpsc_allow_rtct']) : array();
update_option('wpsc_allow_reply_to_close_ticket',$wpsc_allow_reply_to_close_ticket);

$wpsc_enable_default_login = isset($_POST) && isset($_POST['wpsc_default_login_setting']) ? sanitize_text_field($_POST['wpsc_default_login_setting']) : '1';
update_option('wpsc_default_login_setting',$wpsc_enable_default_login);

//User Registration
$wpsc_user_registration=isset($_POST) && isset($_POST['wpsc_user_registration']) ? sanitize_text_field($_POST['wpsc_user_registration']) : '';
update_option('wpsc_user_registration',$wpsc_user_registration);

//User Registration Method

$wpsc_user_registration_method = isset($_POST) && isset($_POST['wpsc_user_registration_method']) ? sanitize_text_field($_POST['wpsc_user_registration_method']) : '';
update_option('wpsc_user_registration_method',$wpsc_user_registration_method);

//Custom user Registration Url
$wpsc_custom_registration_url=isset($_POST) && isset($_POST['wpsc_custom_registration_url']) ? sanitize_text_field($_POST['wpsc_custom_registration_url']) : '';
update_option('wpsc_custom_registration_url',$wpsc_custom_registration_url);


do_action('wpsc_set_gerneral_settings');

echo '{ "sucess_status":"1","messege":"'.__('Settings saved.','supportcandy').'" }';
