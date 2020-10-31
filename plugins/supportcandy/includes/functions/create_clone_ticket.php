<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user, $wpscfunction,$wpdb;
// Ticket Status
$default_status = get_option('wpsc_default_ticket_status');

// Customer name
$customer_name = isset($args['customer_name']) ? sanitize_text_field($args['customer_name']) : '';

// Customer email
$customer_email = isset($args['customer_email']) ? sanitize_text_field($args['customer_email']) : '';

// Subject
$ticket_subject = isset($args['ticket_subject']) ? sanitize_text_field($args['ticket_subject']) : apply_filters( 'wpsc_default_subject_text', __('NA','supportcandy') );

// Category
$default_category = get_option('wpsc_default_ticket_category');
$ticket_category = isset($args['ticket_category']) ? intval($args['ticket_category']) : $default_category;

// Priority
$default_priority = get_option('wpsc_default_ticket_priority');

$user_data = get_user_by('email' ,$customer_email);
if($user_data){
	$user_type = "user";
}
else {
	$user_type = "guest";
}

// Agent created
$agent_created_value = 0;
if ( !is_multisite() || !is_super_admin($current_user->ID)) {
	if ($current_user->has_cap('wpsc_agent') && $customer_email!=$current_user->user_email) {
	  $agents = get_terms([
			'taxonomy'   => 'wpsc_agents',
			'hide_empty' => false,
			'meta_query' => array(
				'relation' => 'AND',
				array(
					'key'       => 'user_id',
					'value'     => $current_user->ID,
					'compare'   => '='
				)
			),
		]);
	  if(!$agents) die();
		$agent_created_value = $agents[0]->term_id;
	} else {
		$agent_created_value = 0;
	}	
}

$wpdb->insert( $wpdb->prefix . 'wpsc_ticket', 
		array(
			'ticket_status' => $default_status,
			'customer_name' => $customer_name,
			'customer_email' => $customer_email,
			'ticket_subject' => $ticket_subject,
			'user_type' => $user_type,
			'ticket_category' => $default_category,
			'ticket_priority' => $default_priority,
			'date_created' => date("Y-m-d H:i:s"),
			'date_updated' => date("Y-m-d H:i:s"),
			'ip_address' => ' ',
			'agent_created' => $agent_created_value,
			'ticket_auth_code' => $this->getRandomString(10),
			'active' => '1'
		));

$ticket_id = $wpdb->insert_id;

$wpscfunction->add_ticket_meta($ticket_id,'assigned_agent',0);

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
		case '4':
    case '5':
		case '7':
		case '8':
    case '9':
			$text = isset($args[$field->slug]) ? $args[$field->slug] : '';
			$wpscfunction->add_ticket_meta($ticket_id, $field->slug,$text);
			break;

		case '3':
		case '10':
			$arrVal = isset($args[$field->slug]) && is_array($args[$field->slug]) ? $args[$field->slug] : array();
			if($arrVal){
				foreach ($arrVal as $key => $value) {
					$wpscfunction->add_ticket_meta($ticket_id, $field->slug,$value);
					update_term_meta ($value, 'active', '1');
				}
			}
			break;
		case '6':
		case '18':
		case '21':
		$date = isset($args[$field->slug]) && $args[$field->slug] ? $args[$field->slug] : '';
		if($date) {
			$wpscfunction->add_ticket_meta($ticket_id, $field->slug,$date);
		}
		break;
		
		default:
			do_action('wpsc_add_ticket_meta_custom_field',$ticket_id,$tf_type,$args,$field);
			break;
	}
}

// Description
$ticket_description = isset($args['ticket_description']) ? $args['ticket_description'] : apply_filters( 'wpsc_default_description_text', __('NA','supportcandy') );
$description_attachment = isset($args['desc_attachment']) ? $args['desc_attachment'] : array();
$attachments = array();
if($description_attachment){
	foreach ($description_attachment as $key => $value) {
		$attachment_id = intval($value);
		$attachments[] = $attachment_id;
		update_term_meta ($attachment_id, 'active', '1');
	}
}
do_action( 'wpsc_ticket_created', $ticket_id );