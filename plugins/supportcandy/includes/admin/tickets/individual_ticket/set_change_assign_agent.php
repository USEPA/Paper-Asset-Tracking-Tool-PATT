<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
 
global $wpscfunction, $current_user;
if (!($current_user->ID && $current_user->has_cap('wpsc_agent'))) {exit;}

$ticket_id = isset($_POST['ticket_id']) ? sanitize_text_field($_POST['ticket_id']) : '' ;
$agents  = isset($_POST['assigned_agent']) && is_array($_POST['assigned_agent']) ? $_POST['assigned_agent'] : array() ;
$old_assigned_agents = $wpscfunction->get_ticket_meta($ticket_id,'assigned_agent');
$assigned_agents = array();

foreach( $agents as $agent ){
  $agent = intval($agent) ? intval($agent) : 0;
    if ($agent){
      $assigned_agents[] = $agent;
		}
}
$assign_agent = array_unique($assigned_agents);

if( $wpscfunction->has_permission('assign_agent',$ticket_id) && ($old_assigned_agents != $assign_agent)){
    $wpscfunction->assign_agent( $ticket_id, $assign_agent);
}	