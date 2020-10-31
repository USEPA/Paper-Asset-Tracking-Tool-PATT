<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user,$wpscfunction;
if (!($current_user->ID && $current_user->has_cap('wpsc_agent'))) {
 exit;
}

$ticket_id = isset($_POST['ticket_id']) ? sanitize_text_field($_POST['ticket_id']) : '' ;
$subject   = isset($_POST['subject']) ? sanitize_text_field($_POST['subject']) : '';
$thread_id = isset($_POST['thread_id']) ? sanitize_text_field($_POST['thread_id']) : '' ;

// Ticket Status
$default_status = get_option('wpsc_default_ticket_status');

// Category
$default_category = get_option('wpsc_default_ticket_category');

// Priority
$default_priority = get_option('wpsc_default_ticket_priority');

$ticket = $wpscfunction->get_ticket($ticket_id);

$tickets = array();
foreach ($ticket as $key => $value) {
  	$tickets[$key] = $value;
}

$content_post = get_post($thread_id);
$thread_body = $content_post->post_content;

$values = array(
	'ticket_status' => $default_status,
	'customer_name' => $tickets['customer_name'],
	'customer_email' => $tickets['customer_email'],
	'ticket_subject' => $subject,
	'user_type' => $tickets['user_type'],
	'ticket_category' => $default_category,
	'ticket_priority' => $default_priority,
	'date_created' => date("Y-m-d H:i:s"),
	'date_updated' => date("Y-m-d H:i:s"),
	'ip_address' => $tickets['ip_address'],
	'agent_created' => 1,
	'ticket_auth_code' => $wpscfunction->getRandomString(10),
	'active' => '1',
	'ticket_description'=> $thread_body,
);

$custom_fields = get_terms([
	'taxonomy'   => 'wpsc_ticket_custom_fields',
	'hide_empty' => false,
	'orderby'    => 'meta_value_num',
	'meta_key'	 => 'wpsc_tf_visibility',
	'order'    	 => 'ASC',
	'meta_query' => array(
		 array(
      'key'       => 'agentonly',
      'value'     => '0',
      'compare'   => '='
	     )
		 ),
	 ]);

if($custom_fields){
	foreach ($custom_fields as $field) {
		$wpsc_tf_type = get_term_meta( $field->term_id, 'wpsc_tf_type',true);
		
		if($wpsc_tf_type == 3 || $wpsc_tf_type==10){
			$value = $wpscfunction->get_ticket_meta($ticket_id,$field->slug);
		}
		else {
			$value = $wpscfunction->get_ticket_meta($ticket_id,$field->slug,true);
		}
		if($wpsc_tf_type == 6){
			$date  = $wpscfunction->get_ticket_meta($ticket_id,$field->slug,true);
			$value =  $date ? date('Y-m-d', strtotime($date)):'';
		}
		if($value){
			$values[$field->slug]=$value;
		}
	}
}

$new_ticket_id = $wpscfunction->create_ticket($values);

// Assigned agents
$agents = $wpscfunction->get_ticket_meta($ticket_id,'assigned_agent');

if($agents[0]){
	$wpscfunction->assign_agent( $new_ticket_id, $agents);
}

//Agent fields
$fields = get_terms([
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

foreach ($fields as $field) {
	$tf_type = get_term_meta( $field->term_id, 'wpsc_tf_type', true);
	switch ($tf_type) {
		case '1':	
		case '2':
		case '4':
    case '5':
		case '6':
		case '7':
		case '8':
    case '9':
		case '11':
		case '12':
		case '13':
		case '14':
			$text = $wpscfunction->get_ticket_meta($ticket_id,$field->slug,true);
			$wpscfunction->add_ticket_meta($new_ticket_id, $field->slug,$text);
			break;
			
		case '3':
		case '10':
			$arrVal = $wpscfunction->get_ticket_meta($ticket_id,$field->slug);
			if($arrVal){
        foreach ($arrVal as $key => $value) {
  				$wpscfunction->add_ticket_meta( $new_ticket_id, $field->slug, $value );
					update_term_meta ($value, 'active', '1');
  			}
      }else {
        $wpscfunction->add_ticket_meta( $new_ticket_id, $field->slug, '' );
      }
		break;
	}
}

	echo json_encode($new_ticket_id);
	?>