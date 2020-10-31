<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
 
global $wpscfunction, $current_user;
if (!($current_user->ID && $current_user->has_cap('wpsc_agent'))) {exit;}

$ticket_id_data  = isset($_POST['ticket_id']) ? sanitize_text_field($_POST['ticket_id']) : '' ;
$ticket_ids = explode(',', $ticket_id_data);

$agents  = isset($_POST['assigned_agent']) && is_array($_POST['assigned_agent']) ? $_POST['assigned_agent'] : array() ;

$assigned_agents = array();

foreach( $agents as $agent ){
  $agent = intval($agent) ? intval($agent) : 0;
    if ($agent){
      $assigned_agents[] = $agent;
    }
}

foreach ($ticket_ids as $ticket_id){
   if( $wpscfunction->has_permission('assign_agent',$ticket_id)){
       $wpscfunction->assign_agent( $ticket_id, $assigned_agents);
  	}	
}

