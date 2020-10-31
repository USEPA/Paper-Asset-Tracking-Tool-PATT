<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

global $current_user;

$response = array();

$agents = get_terms([
	'taxonomy'   => 'wpsc_agents',
	'hide_empty' => false,
	'meta_query' => array(
    array(
      'key'       => 'agentgroup',
      'value'     => '0',
      'compare'   => '='
    )
  )
]);

$agent_roles = get_option('wpsc_agent_role');

foreach ( $agents as $agent ) :
  
  $response[] = SELF::get_agent_info( $agent->term_id, $agent_roles );

endforeach;
