<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$field = get_term_by('id',$field_id,'wpsc_ticket_custom_fields');
$wpsc_tf_type = get_term_meta($field_id,'wpsc_tf_type',true);

if(!$wpsc_tf_type){
  
  switch($field->slug){
    
    case 'ticket_status':
      $status = get_term_by('id',$val,'wpsc_statuses');
      $val    = $status->name;
      break;
    
    case 'ticket_priority':
      $priority = get_term_by('id',$val,'wpsc_priorities');
      $val    = $priority->name;
      break;
    
    case 'ticket_category':
      $category = get_term_by('id',$val,'wpsc_categories');
      $val    = $category->name;
      break;
    
  }
  
}