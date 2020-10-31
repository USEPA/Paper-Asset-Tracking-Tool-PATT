<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpscfunction, $current_user;

if (!($current_user->ID && $current_user->has_cap('wpsc_agent'))) {exit;}

$ticket_id = isset($_POST['ticket_id']) ? sanitize_text_field($_POST['ticket_id']) : '' ;
$customer_name = isset($_POST['customer_name']) ? sanitize_text_field($_POST['customer_name']) : 0 ;
$customer_email = isset($_POST['customer_email']) ? sanitize_text_field($_POST['customer_email']) : 0 ;


$ticket_data = $wpscfunction->get_ticket($ticket_id);
$old_customer_name  = $ticket_data['customer_name'];
$old_customer_email =$ticket_data['customer_email'];
$customer_name = stripslashes($customer_name);

if ($wpscfunction->has_permission('change_raised_by',$ticket_id) && ($customer_name != $old_customer_name || $customer_email != $old_customer_email )){
	$wpscfunction->change_raised_by($ticket_id, $customer_name, $customer_email);
}