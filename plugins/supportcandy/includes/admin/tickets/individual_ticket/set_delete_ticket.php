<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpscfunction;

$ticket_id  = isset($_POST['ticket_id']) ? (sanitize_text_field($_POST['ticket_id'])) : '' ;

if($wpscfunction->has_permission('delete_ticket',$ticket_id)){
	 $wpscfunction->delete_tickets($ticket_id);
}

