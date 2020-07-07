<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $wpscfunction, $current_user;

$customer_email = get_post_meta( $thread_id, 'customer_email', true);
$user           = get_user_by( 'email',$customer_email );

if($user && $user->has_cap('wpsc_agent') ){
	
	$response = $wpscfunction->get_ticket_meta($ticket_id,'first_response',true);
  if(!$response){
		$ticket_data   = $wpscfunction->get_ticket($ticket_id);
		$created       = $ticket_data['date_created'];
		$first_updated = $ticket_data['date_updated'];
		$datetime1     = new DateTime($created);
	  	$datetime2     = new DateTime($first_updated);
		$diff          = $datetime2->diff($datetime1);
	 	$response      = ($diff->d*24*60)+($diff->h*60)+($diff->i);
		$wpscfunction->delete_ticket_meta($ticket_id,'first_response');
		$wpscfunction->add_ticket_meta($ticket_id ,'first_response',$response);
	}
}


