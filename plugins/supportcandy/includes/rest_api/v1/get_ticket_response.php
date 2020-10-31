<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

global $current_user, $wpscfunction;

$response = array(
  'ticket_id' => $ticket_id
);

$ticket   = $wpscfunction->get_ticket($ticket_id);
$response = array_merge($response,$ticket);

// Remove id element as slug for ticket id is ticket_id
unset($response['id']);

// Assigned agents
$assigned_agents = $wpscfunction->get_assigned_agents($ticket_id);
$temp_data = array();
foreach ($assigned_agents as $val) {
  $temp_data[] = array(
    'val' => $val,
  );
}
$response['assigned_agents'] = $temp_data;

$custom_fields = get_terms([
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

foreach ( $custom_fields as $custom_field ) {
  
    $tf_type = get_term_meta($custom_field->term_id,'wpsc_tf_type',true);
    
    switch ($tf_type) {
			case '1':	
			case '2':
			case '4':
			case '5':
      case '6':
			case '7':
			case '8':
		case '9':
		case '18':
		case '21':
						$response[$custom_field->slug] = $wpscfunction->get_ticket_meta($ticket_id,$custom_field->slug,true);
						break;
			case '3':
			case '10':
				$arrVal = $wpscfunction->get_ticket_meta($ticket_id,$custom_field->slug);
        $temp_data = array();
        foreach ($arrVal as $val) {
          $temp_data[] = array(
            'val' => $val,
          );
        }
        $response[$custom_field->slug] = $temp_data;
				break;
      
			default:
				$response = apply_filters('wpsc_api_v1_custom_field_val',$response, $ticket_id, $custom_field, $tf_type);
				break;
		}
  
}

$ticket_description = $wpscfunction->get_ticket_description($ticket_id);
$ticket_description['description'] = str_replace('$','\$', $ticket_description['description']);

$response['ticket_description'] = $ticket_description['description'];
