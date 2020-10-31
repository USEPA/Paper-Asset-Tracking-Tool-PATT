<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user,$wpscfunction,$wpdb;
if (!($current_user->ID && $current_user->has_cap('wpsc_agent'))) {
 exit;
}

$ticket_id     = isset($_POST['ticket_id']) ? sanitize_text_field($_POST['ticket_id']) : '' ;
$subject       = get_term_by('slug', 'ticket_subject', 'wpsc_ticket_custom_fields' );
$wpsc_tf_limit = get_term_meta( $subject->term_id,'wpsc_tf_limit',true);

$ticket_subject  = isset($_POST['subject']) ? sanitize_text_field($_POST['subject']) : '';
if($wpsc_tf_limit){
	$ticket_subject  = substr($ticket_subject,0,$wpsc_tf_limit);
}

$values = array(
	'ticket_subject' => $ticket_subject
);

$wpdb->update($wpdb->prefix.'wpsc_ticket', $values, array('id'=>$ticket_id));
