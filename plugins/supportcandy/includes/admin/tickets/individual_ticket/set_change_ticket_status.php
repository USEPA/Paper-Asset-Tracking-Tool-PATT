<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $current_user,$wpscfunction;

if (!($current_user->ID && $current_user->has_cap('wpsc_agent'))) {exit;}

$ticket_id    = isset($_POST['ticket_id'])  ? sanitize_text_field($_POST['ticket_id']) : '';
$category_id = isset($_POST['category']) ? intval($_POST['category']) : 0 ;
$priority_id = isset($_POST['priority']) ? intval($_POST['priority']) : 0 ;
$status_id   = isset($_POST['status']) ? intval($_POST['status']) : 0 ;

if( !$status_id || !$category_id || !$priority_id ){
  die();
}

$ticket_data = $wpscfunction->get_ticket($ticket_id);
$old_status_id   	= $ticket_data['ticket_status'];
$old_priority_id 	= $ticket_data['ticket_priority'];
$old_category_id  = $ticket_data['ticket_category'];

if($status_id && $wpscfunction->has_permission('change_status',$ticket_id) && $status_id!=$old_status_id){
	$wpscfunction->change_status( $ticket_id, $status_id);
}

if( $priority_id && $wpscfunction->has_permission('change_status',$ticket_id) && $priority_id != $old_priority_id){
	$wpscfunction->change_priority( $ticket_id, $priority_id);
}

if( $category_id && $wpscfunction->has_permission('change_status',$ticket_id) && $category_id != $old_category_id){
	$wpscfunction->change_category( $ticket_id, $category_id );
}

do_action('wpsc_after_set_change_ticket_status');
