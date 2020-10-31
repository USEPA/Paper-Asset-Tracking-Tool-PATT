<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

$params      = $request->get_params();
$priority_id = isset( $params['id'] ) ? $params['id'] : 0;
$priority    = get_term_by('id', $priority_id, 'wpsc_priorities');
if ($priority) {
  $response  = array(
    'id'              => $priority->term_id,
    'name'            => $priority->name,
    'textColor'       => get_term_meta( $priority->term_id, 'wpsc_priority_color', true),
    'backgroundColor' => get_term_meta( $priority->term_id, 'wpsc_priority_background_color', true),
  );
} else {
  $response = new WP_Error(
      'not_found',
      'Priority not found for id '.$priority_id,
      array(
          'status' => 404,
      )
  );
}
