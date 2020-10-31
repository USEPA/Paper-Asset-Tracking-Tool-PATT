<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

$params    = $request->get_params();
$status_id = isset( $params['id'] ) ? $params['id'] : 0;
$status    = get_term_by('id', $status_id, 'wpsc_statuses');
if ($status) {
  $response  = array(
    'id'              => $status->term_id,
    'name'            => $status->name,
    'textColor'       => get_term_meta( $status->term_id, 'wpsc_status_color', true),
    'backgroundColor' => get_term_meta( $status->term_id, 'wpsc_status_background_color', true),
  );
} else {
  $response = new WP_Error(
      'not_found',
      'Status not found for id '.$status_id,
      array(
          'status' => 404,
      )
  );
}