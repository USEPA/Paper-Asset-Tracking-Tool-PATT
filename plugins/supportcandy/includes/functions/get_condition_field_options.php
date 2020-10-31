<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpscfunction;

$field_types = $wpscfunction->get_custom_field_types();

$conditional_types = array();
foreach ($field_types as $key => $field) {
	if ($field['has_options']) {
		$conditional_types[]=$key;
	}
}

$fields = get_terms([
  'taxonomy'   => 'wpsc_ticket_custom_fields',
  'hide_empty' => false,
  'meta_query' => array(
    'relation' => 'OR',
    array(
      'key'       => 'wpsc_tf_type',
      'value'     => $conditional_types,
      'compare'   => 'IN'
    ),
    array(
      'key'       => 'wpsc_conditional',
      'value'     => '1',
      'compare'   => '='
    )
  ),
]);
