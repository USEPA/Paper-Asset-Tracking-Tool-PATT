<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

global $current_user,$wpscfunction;

// Initialie restrictions. Everyone should able to see their own tickets.
$restrict_rules = array(
	'relation' => 'OR',
	array(
		'key'            => 'customer_email',
		'value'          => $current_user->user_email,
		'compare'        => '='
	),
);

if ($current_user->has_cap('wpsc_agent') ) {
	
	$agent_permissions = $wpscfunction->get_current_agent_permissions();
	$current_agent_id  = $wpscfunction->get_current_user_agent_id();
	
	if ($agent_permissions['view_unassigned']) {
		$restrict_rules[] = array(
			'key'            => 'assigned_agent',
			'value'          => 0,
			'compare'        => '='
		);
	}
	
	if ($agent_permissions['view_assigned_me']) {
		$restrict_rules[] = array(
			'key'            => 'assigned_agent',
			'value'          => $current_agent_id,
			'compare'        => '='
		);
	}
	
	if ($agent_permissions['view_assigned_others']) {
		$restrict_rules[] = array(
			'key'            => 'assigned_agent',
			'value'          => array(0,$current_agent_id),
			'compare'        => 'NOT IN'
		);
	}
	
	$restrict_rules = apply_filters('wpsc_tl_agent_restrict_rules',$restrict_rules);
	
} else {
	
	$restrict_rules = apply_filters('wpsc_tl_customer_restrict_rules',$restrict_rules);
	
}

$wpsc_ticket_public_mode = get_option('wpsc_ticket_public_mode');

if( !$current_user->has_cap('wpsc_agent') && $wpsc_ticket_public_mode){
	$restrict_rules[] = array(
		'key'            => 'active',
		'value'          => 1,
		'compare'        => '='
	);
}
