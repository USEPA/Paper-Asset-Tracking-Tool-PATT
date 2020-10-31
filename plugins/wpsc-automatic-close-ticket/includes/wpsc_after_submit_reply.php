<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $wpscfunction;
$thread_email = get_post_meta($thread_id,'customer_email',true);
$post_email = $wpscfunction->get_ticket_fields($ticket_id,'customer_email');
$warn = $wpscfunction->get_ticket_meta($ticket_id,'wpsp_warning_email_send',true);
$user = get_user_by('email',$thread_email);

if((!user_can( $user, 'wpsc_agent' ) || $thread_email == $post_email) && $warn){
	$wpscfunction->delete_ticket_meta($ticket_id,'wpsp_warning_email_send');
}