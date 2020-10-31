<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user, $wpscfunction;
if (!($current_user->ID && $current_user->has_cap('edit_published_posts'))) {
	exit;
}

$ticket_widget_id = isset($_POST) && isset($_POST['ticket_widget_ids']) ? $wpscfunction->sanitize_array($_POST['ticket_widget_ids']) : array();

foreach ($ticket_widget_id as $key => $ticket_widget_ids) {
	update_term_meta(intval($ticket_widget_ids), 'wpsc_ticket_widget_load_order', intval($key));
}

echo '{ "sucess_status":"1","messege":"'.__('Ticket Widget Order  saved.','supportcandy').'" }';