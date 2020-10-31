<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

global $current_user;

$response = array();

$agentonly_fields = get_terms([
	'taxonomy'   => 'wpsc_ticket_custom_fields',
	'hide_empty' => false,
	'orderby'    => 'meta_value_num',
	'meta_key'	 => 'wpsc_tf_load_order',
	'order'    	 => 'ASC',
	'meta_query' => array(
		array(
      'key'       => 'agentonly',
      'value'     => '1',
      'compare'   => '='
    )
	),
]);

foreach ( $agentonly_fields as $ao_field ) {
  
    if ( $current_user->ID && ( $ao_field->slug == 'customer_name' || $ao_field->slug == 'customer_email' ) ) {
      continue;
    }
    
    $options = get_term_meta( $ao_field->term_id, 'wpsc_tf_options', true);
    $options = is_array($options) ? $options : array();
    $options = isset($options[0]) && $options[0] ? $options : array();
    
    $temp_options = array();
    foreach ($options as $value) {
      $temp_options[] = array(
        'name'  => $value,
        'value' => $value
      );
    }
    $options = $temp_options;
    
    $field = array(
      'id'                    => $ao_field->term_id,
      'slug'                  => $ao_field->slug,
      'label'                 => get_term_meta( $ao_field->term_id, 'wpsc_tf_label', true),
      'extra_info'            => get_term_meta( $ao_field->term_id, 'wpsc_tf_extra_info', true),
      'type'                  => get_term_meta( $ao_field->term_id, 'wpsc_tf_type', true),
      'options'               => $options,
    );
    
    $field = apply_filters('wpsc_api_v1_ao_fields',$field);
    
    $response[] = $field;
  
}
