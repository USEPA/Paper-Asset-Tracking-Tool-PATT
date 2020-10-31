<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user, $wpscfunction;

// Get tiket id
$ticket_id = isset($_POST['ticket_id']) ? intval($_POST['ticket_id']) : 0;
if(!$ticket_id) die();

// Check if auth code is there
$auth_code = isset($_POST['auth_code']) ? sanitize_text_field($_POST['auth_code']) : '';

//  Check ticket permanently deleted 
if(!$ticket_id){
	_e('Ticket not found.','supportcandy');
	die();
}

$ticket_auth_code = $wpscfunction->get_ticket_fields($ticket_id,'ticket_auth_code');

// Check ticket in trash
$ticket_status = $wpscfunction->get_ticket_status($ticket_id);
if(!$ticket_status && $current_user && !$current_user->has_cap('wpsc_agent')){
	_e('Ticket not found.','supportcandy');
	die();
}

$wpsc_ticket_url_permission = get_option('wpsc_ticket_url_permission');
if( !is_user_logged_in() && !$wpsc_ticket_url_permission){
	include WPSC_ABSPATH . 'includes/admin/tickets/sign_in/sign_in.php';
	die();
}

$wpsc_ticket_public_mode = get_option('wpsc_ticket_public_mode');
if( ($wpscfunction->has_permission('view_ticket',$ticket_id) || $wpsc_ticket_public_mode) && is_user_logged_in()){

	include WPSC_ABSPATH . 'includes/admin/tickets/individual_ticket/load_individual_ticket.php';

} else if ( $ticket_auth_code == $auth_code && $wpsc_ticket_url_permission) {

	include WPSC_ABSPATH . 'includes/admin/tickets/individual_ticket/load_guest_individual_ticket.php';

} else  {

	_e('You are not authorized to view this ticket.','supportcandy');

}
