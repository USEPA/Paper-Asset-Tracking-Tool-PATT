<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

global $wpscfunction;

$params      = $request->get_params();
$agent_id    = isset( $params['id'] ) ? $params['id'] : 0;
$agent_roles = get_option('wpsc_agent_role');

if ( $wpscfunction->is_agent([$agent_id]) ) {
  
  $response = SELF::get_agent_info( $agent_id, $agent_roles );
  
} else {
  
  $response = new WP_Error(
      'not_found',
      'Agent not found for id '.$agent_id,
      array(
          'status' => 404,
      )
  );
}
