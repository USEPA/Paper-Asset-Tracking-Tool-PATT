<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

global $wpdb, $current_user, $wpscfunction;

$args     = array();
$params   = $request->get_params();
$data     = isset($params['fields_data']) ? wp_kses_post($params['fields_data']) : '';
$response = array();

if (!$data) {
  $response = new WP_Error(
      'invalid_api_parameters',
      'Invalid api parameters provided.',
      array(
          'status' => 403,
      )
  );
  return;
}

$fields_data = json_decode($data); 

// customer name
if ( isset($fields_data->customer_name) ){
  $args['customer_name'] = $fields_data->customer_name;
} else if ( is_user_logged_in() )  {
  $args['customer_name'] = $current_user->display_name;
} 

// customer email
if ( isset($fields_data->customer_email) ){
  $args['customer_email'] = $fields_data->customer_email;
} else if ( is_user_logged_in() )  {
  $args['customer_email'] = $current_user->user_email;
}

// error if there is no user found
if ( !isset($args['customer_name']) || !isset($args['customer_email']) ) {
  $response = new WP_Error(
      'user_not_found',
      'Either user name or email not given.',
      array(
          'status' => 403,
      )
  );
  return;
}

// check agent created
if ( is_user_logged_in() && $current_user->has_cap('wpsc_agent') && $args['customer_email'] != $current_user->user_email ) {
  $args['agent_created'] = $current_user->ID;
}

// Ticket subject
$subject          = get_term_by('slug', 'ticket_subject', 'wpsc_ticket_custom_fields' );
$wpsc_tf_limit    = get_term_meta( $subject->term_id,'wpsc_tf_limit',true);
$ticket_subject = isset($fields_data->ticket_subject) ? $fields_data->ticket_subject : '';

if($wpsc_tf_limit){
  $ticket_subject   = substr($ticket_subject,0,$wpsc_tf_limit);
}

$args['ticket_subject'] = $ticket_subject;

// Ticket description
$ticket_description = isset($fields_data->ticket_description) ? wp_kses_post(htmlspecialchars_decode($fields_data->ticket_description, ENT_QUOTES)) : '';
$ticket_description = str_replace(' ', '&nbsp;', $ticket_description);

$args['ticket_description'] = $ticket_description;
$wpsc_guest_can_upload_files = get_option('wpsc_guest_can_upload_files');
if(is_user_logged_in() || $wpsc_guest_can_upload_files ){
	$description_attachment = isset($fields_data->desc_attachment) ? $wpscfunction->sanitize_array($fields_data->desc_attachment) : array();
	if($description_attachment) $args['desc_attachment'] = $description_attachment;
}

// Ticket category
if( isset($fields_data->ticket_category) && intval($fields_data->ticket_category) > 0 && $wpscfunction->is_category($fields_data->ticket_category) ){
  $args['ticket_category'] = $fields_data->ticket_category;
}

// Ticket priority
if( isset($fields_data->ticket_priority) && intval($fields_data->ticket_priority) > 0 && $wpscfunction->is_priority($fields_data->ticket_priority) ){
  $args['ticket_priority'] = $fields_data->ticket_priority;
}

// Custom fields
$fields = get_terms([
  'taxonomy'   => 'wpsc_ticket_custom_fields',
  'hide_empty' => false,
  'orderby'    => 'meta_value_num',
  'meta_key'	 => 'wpsc_tf_load_order',
  'order'    	 => 'ASC',
  'meta_query' => array(
    'relation' => 'AND',
    array(
      'key'       => 'agentonly',
      'value'     => array(0,1),
      'compare'   => 'IN'
    ),
    array(
      'key'       => 'wpsc_tf_type',
      'value'     => '0',
      'compare'   => '>'
    ),
  ),
]);

foreach ($fields as $field) {
  
    $tf_type = get_term_meta( $field->term_id, 'wpsc_tf_type', true);
    
    switch ($tf_type) {
        
        case '1':	
        case '2':
        case '6':
        case '7':
        case '8':
        case '18':
        case '21':
          $text = isset($fields_data->{$field->slug}) ? sanitize_text_field($fields_data->{$field->slug}) : '';
          if($text) $args[$field->slug] = $text;
          break;

        case '3':
          $arrVal = isset($fields_data->{$field->slug}) ? $fields_data->{$field->slug} : array();
          foreach ( $arrVal as $val) {
            if ( $wpscfunction->is_cf_option($field->term_id,$val) ) {
              $args[$field->slug][] = sanitize_text_field($val);
            }
          }
          break;
          
        case '4':
          $text = isset($fields_data->{$field->slug}) ? sanitize_text_field($fields_data->{$field->slug}) : '';
          $args[$field->slug] = $text && $wpscfunction->is_cf_option($field->term_id,$text) ? $text : '';
          break;
        
        case '10':
          $arrVal = isset($fields_data->{$field->slug}) ? $fields_data->{$field->slug} : array();
          if($arrVal) $args[$field->slug] = $wpscfunction->sanitize_array($arrVal);
          break;

        case '5':
          $text = isset($fields_data->{$field->slug}) ? wp_kses_post(htmlspecialchars_decode($fields_data->{$field->slug}, ENT_QUOTES)) : '';
          if($text) $args[$field->slug] = $text;
          break;

        case '9':
          $number = isset($fields_data->{$field->slug}) ? intval($fields_data->{$field->slug}) : '';
          if($number) $args[$field->slug] = $number;
          break;

        default:	
          $args = apply_filters('wpsc_after_create_ticket_custom_field', $args, $field, $tf_type );
          break;		
    }
}

$args = apply_filters( 'wpsc_before_create_ticket_args', $args);

$ticket_id = $wpscfunction->create_ticket($args);

if ( $ticket_id > 0 ) {
  
    $response = array(
      'status' => 200,
      'ticket_id' =>  $ticket_id
    );
  
} else {
  
    $response = new WP_Error(
      'operation_failed',
      'Ticket not created.',
      array(
        'status' => 403,
      )
    );
  
}
