<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

global $current_user;

$response = array();

$ticket_form_fields = get_terms([
	'taxonomy'   => 'wpsc_ticket_custom_fields',
	'hide_empty' => false,
	'orderby'    => 'meta_value_num',
	'meta_key'	 => 'wpsc_tf_load_order',
	'order'    	 => 'ASC',
	'meta_query' => array(
		array(
      'key'       => 'agentonly',
      'value'     => '0',
      'compare'   => '='
    )
	),
]);

foreach ( $ticket_form_fields as $form_field ) {
  
    if ( $current_user->ID && ( $form_field->slug == 'customer_name' || $form_field->slug == 'customer_email' ) ) {
      continue;
    }
    
    $visibility            = get_term_meta( $form_field->term_id, 'wpsc_tf_visibility', true);
    $visibility            = is_array($visibility) ? $visibility : array();
    $visibility_conditions = array();
    foreach ($visibility as $condition) {
      $condition = explode('--', $condition);
      $visibility_conditions[] = array(
        'field_id' => $condition[0],
        'value'    => $condition[1],
      );
    }
    
    $options = get_term_meta( $form_field->term_id, 'wpsc_tf_options', true);
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
    
    if( $form_field->slug == 'ticket_priority' ) {
      $priorities = get_terms([
      	'taxonomy'   => 'wpsc_priorities',
      	'hide_empty' => false,
      	'orderby'    => 'meta_value_num',
      	'order'    	 => 'ASC',
      	'meta_query' => array('order_clause' => array('key' => 'wpsc_priority_load_order')),
      ]);
      foreach ( $priorities as $priority ) {
        $options[] = array(
          'name'  => $priority->name,
          'value' => $priority->term_id
        );
      }
    }
    
    if( $form_field->slug == 'ticket_category' ) {
      $categories = get_terms([
      	'taxonomy'   => 'wpsc_categories',
      	'hide_empty' => false,
      	'orderby'    => 'meta_value_num',
      	'order'    	 => 'ASC',
      	'meta_query' => array('order_clause' => array('key' => 'wpsc_category_load_order')),
      ]);
      foreach ( $categories as $category ) {
        $options[] = array(
          'name'  => $category->name,
          'value' => $category->term_id
        );
      }
    }
    
    $field = array(
      'id'                    => $form_field->term_id,
      'slug'                  => $form_field->slug,
      'label'                 => get_term_meta( $form_field->term_id, 'wpsc_tf_label', true),
      'extra_info'            => get_term_meta( $form_field->term_id, 'wpsc_tf_extra_info', true),
      'type'                  => get_term_meta( $form_field->term_id, 'wpsc_tf_type', true),
      'options'               => $options,
      'default_visibility'    => get_term_meta( $form_field->term_id, 'wpsc_tf_status', true),
      'required'              => get_term_meta( $form_field->term_id, 'wpsc_tf_required', true),
      'width'                 => get_term_meta( $form_field->term_id, 'wpsc_tf_width', true),
      'visibility_conditions' => $visibility_conditions,
    );
    
    $field = apply_filters('wpsc_api_v1_ticket_form_fields',$field);
    
    $response[] = $field;
  
}
