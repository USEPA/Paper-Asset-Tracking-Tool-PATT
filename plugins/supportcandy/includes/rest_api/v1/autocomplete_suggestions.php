<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpscfunction;

$source     = 'rest';
$params     = $request->get_params();
$term       = isset( $params['search'] ) ? sanitize_text_field($params['search']) : '';
$field_slug = isset( $params['field_slug'] ) ? sanitize_text_field($params['field_slug']) : '';

if ( !$field_slug || !$wpscfunction->is_cf_slug($field_slug) ) {
  $response = new WP_Error(
      'invalid_api_parameters',
      'Invalid api parameters provided.',
      array(
          'status' => 403,
      )
  );
  return;
}

include WPSC_ABSPATH . 'includes/admin/tickets/ticket_list/filters/filter_autocomplete.php';

$response = array();

foreach ($output as $key => $suggestion) {
  $response[] = array(
    'label' => $suggestion['label'],
    'value' => $suggestion['flag_val'],
  );
}
