<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpscfunction, $current_user;

if (!($current_user->ID && $current_user->has_cap('wpsc_agent'))) {exit;}

$ticket_id_data = isset($_POST['ticket_id']) ? sanitize_text_field($_POST['ticket_id']) : '' ;
$ticket_ids     = explode(',', $ticket_id_data);

$category_id = isset($_POST['category']) ? intval($_POST['category']) : 0 ;
$priority_id = isset($_POST['priority']) ? intval($_POST['priority']) : 0 ;
$status_id   = isset($_POST['status']) ? intval($_POST['status']) : 0 ;

foreach ($ticket_ids as $ticket_id){
  if( $status_id && $wpscfunction->has_permission('change_status',$ticket_id)){
		$wpscfunction->change_status( $ticket_id, $status_id);
	}
	
  if( $priority_id && $wpscfunction->has_permission('change_status',$ticket_id)){
  	$wpscfunction->change_priority( $ticket_id, $priority_id);
  }
	
  if( $category_id && $wpscfunction->has_permission('change_status',$ticket_id) ){
  	$wpscfunction->change_category( $ticket_id, $category_id );
  }
	
}

do_action('wpsc_after_set_bulk_change_status');

