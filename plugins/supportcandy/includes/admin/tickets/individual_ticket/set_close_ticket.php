<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user,$wpscfunction;

$ticket_id  = isset($_POST['ticket_id']) ? sanitize_text_field($_POST['ticket_id']) : '' ;

$wpsc_close_ticket_status = get_option('wpsc_close_ticket_status');

if($wpsc_close_ticket_status!=''){
  $wpscfunction->change_status( $ticket_id, $wpsc_close_ticket_status);
}

?>