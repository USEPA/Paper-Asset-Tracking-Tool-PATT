<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user,$wpscfunction;
if (!($current_user->ID && $current_user->has_cap('edit_published_posts'))) {
	exit;
}

// Allow ticket url Permissions
$wpsc_ticket_url_permission = isset($_POST) && isset($_POST['wpsc_ticket_url_permission']) ? sanitize_text_field($_POST['wpsc_ticket_url_permission']) : '0';
update_option('wpsc_ticket_url_permission',$wpsc_ticket_url_permission);

$wpsc_allow_sign_out = isset($_POST) && isset($_POST['wpsc_sign_out']) ? sanitize_text_field($_POST['wpsc_sign_out']) : '';
update_option('wpsc_sign_out',$wpsc_allow_sign_out);

// Public MOde
$wpsc_ticket_public_mode = isset($_POST) && isset($_POST['wpsc_ticket_public_mode']) ? sanitize_text_field($_POST['wpsc_ticket_public_mode']) : '0';
update_option('wpsc_ticket_public_mode',$wpsc_ticket_public_mode);

$wpsc_allow_reply_to_public_tickets = isset($_POST) && isset($_POST['wpsc_allow_reply_to_public_tickets']) ? sanitize_text_field($_POST['wpsc_allow_reply_to_public_tickets']) : '0';
update_option('wpsc_allow_reply_to_public_tickets',$wpsc_allow_reply_to_public_tickets);

$wpsc_show_and_hide_filters = isset($_POST) && isset($_POST['wpsc_show_and_hide_filters']) ? sanitize_text_field($_POST['wpsc_show_and_hide_filters']) : '';
update_option('wpsc_show_and_hide_filters',$wpsc_show_and_hide_filters);

$wpsc_allow_reply_confirmation = isset($_POST) && isset($_POST['wpsc_allow_reply_confirmation']) ? sanitize_text_field($_POST['wpsc_allow_reply_confirmation']) : '0';
update_option('wpsc_allow_reply_confirmation',$wpsc_allow_reply_confirmation) ;

$wpsc_reg_guest_user_after_create_ticket = isset($_POST) && isset($_POST['wpsc_reg_guest_user_after_create_ticket']) ? sanitize_text_field($_POST['wpsc_reg_guest_user_after_create_ticket']) : '0';
update_option('wpsc_reg_guest_user_after_create_ticket',$wpsc_reg_guest_user_after_create_ticket) ;

$wpsc_thread_date_format = isset($_POST) && isset($_POST['wpsc_thread_date_format']) ? sanitize_text_field($_POST['wpsc_thread_date_format']) : 'string';
update_option('wpsc_thread_date_format',$wpsc_thread_date_format) ;

$wpsc_thread_date_time_format = isset($_POST) && isset($_POST['wpsc_thread_date_time_format']) ? sanitize_text_field($_POST['wpsc_thread_date_time_format']) : 'string';
update_option('wpsc_thread_date_time_format',$wpsc_thread_date_time_format) ;

$wpsc_notify = isset($_POST) && isset($_POST['wpsc_notify']) ? sanitize_text_field($_POST['wpsc_notify']) : '1';
update_option('wpsc_do_not_notify_setting',$wpsc_notify) ;

$wpsc_notify_checkbox = isset($_POST) && isset($_POST['wpsc_notify_checkbox']) ? sanitize_text_field($_POST['wpsc_notify_checkbox']) : '1';
update_option('wpsc_default_do_not_notify_option',$wpsc_notify_checkbox) ;

$wpsc_hide_show_priority =  isset($_POST) && isset($_POST['wpsc_hide_show_priority']) ? sanitize_text_field($_POST['wpsc_hide_show_priority']) : '';
update_option('wpsc_hide_show_priority',$wpsc_hide_show_priority);

$wpsc_ticket_id_type = isset($_POST) && isset($_POST['wpsc_ticket_id_type']) ? sanitize_text_field($_POST['wpsc_ticket_id_type']) : '0';
update_option('wpsc_ticket_id_type',$wpsc_ticket_id_type);

$wpsc_view_more = isset($_POST) && isset($_POST['wpsc_view_more']) ? sanitize_text_field($_POST['wpsc_view_more']) : '';
update_option('wpsc_view_more',$wpsc_view_more);

$wpsc_thread_limit = isset($_POST) && isset($_POST['wpsc_thread_limit']) ? sanitize_text_field($_POST['wpsc_thread_limit']) : '';
update_option('wpsc_thread_limit',$wpsc_thread_limit);

$wpsc_on_and_off_auto_refresh = isset($_POST) && isset($_POST['wpsc_on_and_off_auto_refresh']) ? sanitize_text_field($_POST['wpsc_on_and_off_auto_refresh']) : '';
update_option('wpsc_on_and_off_auto_refresh',$wpsc_on_and_off_auto_refresh);

$wpsc_redirect_to_ticket_list = isset($_POST) && isset($_POST['wpsc_redirect_to_ticket_list']) ? sanitize_text_field($_POST['wpsc_redirect_to_ticket_list']) : '';
update_option('wpsc_redirect_to_ticket_list',$wpsc_redirect_to_ticket_list);

$wpsc_auto_delete_ticket      = isset($_POST) && isset($_POST['wpsc_auto_delete_ticket']) ? sanitize_text_field($_POST['wpsc_auto_delete_ticket']) : '';
$wpsc_auto_delete_ticket_time = isset($_POST) && isset($_POST['wpsc_auto_delete_ticket_time']) ? sanitize_text_field($_POST['wpsc_auto_delete_ticket_time']) : '';

if($wpsc_auto_delete_ticket_time){
	update_option('wpsc_auto_delete_ticket',$wpsc_auto_delete_ticket);
}

if( $wpsc_auto_delete_ticket_time ){
	update_option('wpsc_auto_delete_ticket_time',$wpsc_auto_delete_ticket_time);
	$wpsc_auto_delete_ticket_time_period_unit = isset($_POST) && isset($_POST['wpsc_auto_delete_ticket_time_period_unit']) ? sanitize_text_field($_POST['wpsc_auto_delete_ticket_time_period_unit']) : '';
	update_option('wpsc_auto_delete_ticket_time_period_unit',$wpsc_auto_delete_ticket_time_period_unit);
}

$wpsc_allow_reply_bcc = isset($_POST) && isset($_POST['wpsc_reply_bcc']) ? sanitize_text_field($_POST['wpsc_reply_bcc']) : '';
update_option('wpsc_reply_bcc_visibility',$wpsc_allow_reply_bcc);

$wpsc_allow_datepickerjs_files = isset($_POST) && isset($_POST['wpsc_allow_datepickerjs_files']) ? sanitize_text_field($_POST['wpsc_allow_datepickerjs_files']) : '0';
update_option('wpsc_allow_datepickerjs_files',$wpsc_allow_datepickerjs_files);

do_action('wpsc_set_advanced_settings');

if( $wpsc_auto_delete_ticket_time <= 0 && $wpsc_auto_delete_ticket ){
	
	echo '{ "sucess_status":"0","messege":"'.__('Auto delete ticket time must be greater than zero.','supportcandy').'" }';
}elseif ($wpsc_thread_limit <= 0) {
	echo '{ "sucess_status":"0","messege":"'.__('Ticket history limit greater than zero.','supportcandy').'" }';
}else{
	echo '{ "sucess_status":"1","messege":"'.__('Settings saved.','supportcandy').'" }';	
}

