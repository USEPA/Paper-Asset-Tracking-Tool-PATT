<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpscfunction, $current_user,$wpdb;

$params = $request->get_params();

// Get tiket id
$ticket_id   = isset( $params['id'] ) ? sanitize_text_field($params['id']) : 0;
$data 	     = isset( $params['fields_data']) ? sanitize_text_field($params['fields_data']) : '';

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
$ticket_data = $wpscfunction->get_ticket($ticket_id);

if (! $ticket_data) {
	$response = new WP_Error(
		'unauthorized',
		'Ticket not found.',
		array(
				'status' => 403,
		)
  );
  return;
}

// Changed raised by
if ( isset($fields_data->customer_name) || isset($fields_data->customer_email) ){
	$old_customer_name  = $ticket_data['customer_name'];
	$old_customer_email = $ticket_data['customer_email'];
	if ($wpscfunction->has_permission('change_raised_by',$ticket_id) && ($fields_data->customer_name != $old_customer_name || $fields_data->customer_email != $old_customer_email )){
			$wpscfunction->change_raised_by($ticket_id, $customer_name, $customer_email);
	}
}

// Change ticket subject
if ( $wpscfunction->has_permission('change_ticket_fields',$ticket_id) && isset($fields_data->ticket_subject)){
	$vals = array('ticket_subject' => $fields_data->ticket_subject);
	$wpdb->update($wpdb->prefix.'wpsc_ticket', $vals, array('id'=>$ticket_id));
}

// Change ticket status
if( isset($fields_data->ticket_status) && $wpscfunction->has_permission('change_status',$ticket_id) && intval($fields_data->ticket_status) > 0 && $wpscfunction->is_status($fields_data->ticket_status) && $fields_data->ticket_status != $ticket_data['ticket_status'] ){
	$wpscfunction->change_status( $ticket_id, $fields_data->ticket_status);
}

// Change ticket category
if( isset($fields_data->ticket_category) && $wpscfunction->has_permission('change_status',$ticket_id) && intval($fields_data->ticket_category) > 0 && $wpscfunction->is_category($fields_data->ticket_category) && $fields_data->ticket_category != $ticket_data['ticket_category'] ){
	$wpscfunction->change_category( $ticket_id, $fields_data->ticket_category);
}

// Change ticket priority
if( isset($fields_data->ticket_priority) && $wpscfunction->has_permission('change_status',$ticket_id) && intval($fields_data->ticket_priority) > 0 && $wpscfunction->is_priority($fields_data->ticket_priority) && $fields_data->ticket_priority != $ticket_data['ticket_priority'] ){
	$wpscfunction->change_priority( $ticket_id, $fields_data->ticket_priority);
}

// Assign agent to ticket
$agents              = isset($fields_data->assigned_agent) && is_array($fields_data->assigned_agent) ? $fields_data->assigned_agent : array() ;
$old_assigned_agents = $wpscfunction->get_ticket_meta($ticket_id,'assigned_agent');
$assigned_agents     = array();

foreach( $agents as $agent ){
  $agent = intval($agent) ? intval($agent) : 0;
    if ($agent){
      $assigned_agents[] = $agent;
		}
}

$assign_agent = array_unique($assigned_agents);

if( $agents && $wpscfunction->has_permission('assign_agent',$ticket_id) &&  intval($fields_data->assigned_agent) > 0 && $wpscfunction->is_agent($assign_agent) &&($old_assigned_agents != $assign_agent)){
    $wpscfunction->assign_agent( $ticket_id, $assign_agent);
}	


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

foreach ($fields as $field ) {
	
		$wpsc_tf_type = get_term_meta( $field->term_id, 'wpsc_tf_type', true);
		$agentonly    = get_term_meta( $field->term_id, 'agentonly', true);
		
		switch ( $wpsc_tf_type ) {
			
				case '1':
				case '2':
				case '4':
				case '7':
				case '8':
				case '18':
						$oldVal = $wpscfunction->get_ticket_meta( $ticket_id, $field->slug, true);
						$newVal = isset($fields_data->{$field->slug}) ? sanitize_text_field($fields_data->{$field->slug}) : '';
						if ( isset($fields_data->{$field->slug}) && (($agentonly == 0 && $wpscfunction->has_permission('change_ticket_fields',$ticket_id)) || ($agentonly == 1 && $wpscfunction->has_permission('change_agentonly_fields',$ticket_id)) ) && $oldVal != $newVal ) {
							$wpscfunction->change_field( $ticket_id, $field->slug, $newVal );
						}
						break;
						
				case '3':
						$oldVal = $wpscfunction->get_ticket_meta( $ticket_id, $field->slug );
						$newVal = isset($fields_data->{$field->slug}) && $fields_data->{$field->slug} ? $wpscfunction->sanitize_array($fields_data->{$field->slug}) : array();
						if ( isset($fields_data->{$field->slug}) && (($agentonly == 0 && $wpscfunction->has_permission('change_ticket_fields',$ticket_id)) || ($agentonly == 1 && $wpscfunction->has_permission('change_agentonly_fields',$ticket_id)) ) && $oldVal != $newVal ) {
							$wpscfunction->change_field( $ticket_id, $field->slug, $newVal );
						}
						break;
						
				case '5':		
					$oldVal = $wpscfunction->get_ticket_meta( $ticket_id, $field->slug, true);
					$newVal = isset($fields_data->{$field->slug}) ? sanitize_textarea_field($fields_data->{$field->slug}) : '';
					if ( isset($fields_data->{$field->slug}) && (($agentonly == 0 && $wpscfunction->has_permission('change_ticket_fields',$ticket_id)) || ($agentonly == 1 && $wpscfunction->has_permission('change_agentonly_fields',$ticket_id)) ) && $oldVal != $newVal ) {
						$wpscfunction->change_field( $ticket_id, $field->slug, $newVal );
					}
					break;
					
				case '6':
						
						$oldVal = $wpscfunction->datetimeToCalenderFormat($wpscfunction->get_ticket_meta( $ticket_id, $field->slug, true));
						$newVal = isset($fields_data->{$field->slug}) ? sanitize_text_field($fields_data->{$field->slug}) : '';
						$label  = get_term_meta( $field->term_id, 'wpsc_tf_label',true);
						
						if ( isset($fields_data->{$field->slug}) && (($agentonly == 0 && $wpscfunction->has_permission('change_ticket_fields',$ticket_id)) || ($agentonly == 1 && $wpscfunction->has_permission('change_agentonly_fields',$ticket_id)) ) && $oldVal != $newVal ) {
							
								$fields_value_new = $wpscfunction->calenderDateFormatToDateTime($newVal);
								
								if ( $oldVal && !$newVal ) {
									$wpscfunction->delete_ticket_meta($ticket_id,$field->slug);
									$log_str = sprintf( __('%1$s removed %2$s','supportcandy'), '<strong>'.$current_user->display_name.'</strong>','<strong>'.$label.'</strong>' );
								} else if( !$oldVal && $newVal ){
									$wpscfunction->add_ticket_meta( $ticket_id, $field->slug, $fields_value_new );
									$log_str = sprintf( __('%1$s changed %2$s to %3$s','supportcandy'), '<strong>'.$current_user->display_name.'</strong>','<strong>'.$label.'</strong>','<strong>'.$newVal.'</strong>');	
								} else {
									$wpscfunction->update_ticket_meta($ticket_id,$field->slug,array('meta_value' => $fields_value_new));
									$log_str = sprintf( __('%1$s changed %2$s from %3$s to %4$s','supportcandy'), '<strong>'.$current_user->display_name.'</strong>','<strong>'.$label.'</strong>','<strong>'.$oldVal.'</strong>','<strong>'.$newVal.'</strong>');
								}
								
								$args = array(
									'ticket_id'      => $ticket_id,
									'reply_body'     => $log_str,
									'thread_type'    => 'log'
								);
								$args = apply_filters( 'wpsc_thread_args', $args );
								$wpscfunction->submit_ticket_thread($args);
							
						}
						break;
						
				case '9':
						$oldVal = $wpscfunction->get_ticket_meta( $ticket_id, $field->slug, true);
						$newVal = isset($fields_data->{$field->slug}) ? intval($fields_data->{$field->slug}) : '';
						if ( isset($fields_data->{$field->slug}) && (($agentonly == 0 && $wpscfunction->has_permission('change_ticket_fields',$ticket_id)) || ($agentonly == 1 && $wpscfunction->has_permission('change_agentonly_fields',$ticket_id)) ) && $oldVal != $newVal ) {
							$wpscfunction->change_field( $ticket_id, $field->slug, $newVal );
						}
						break;
						
				case '10':
						$oldVal = $wpscfunction->get_ticket_meta( $ticket_id, $field->slug );
						$newVal = isset($fields_data->{$field->slug}) && $fields_data->{$field->slug} ? $wpscfunction->sanitize_array($fields_data->{$field->slug}) : array();
						if ( isset($fields_data->{$field->slug}) && (($agentonly == 0 && $wpscfunction->has_permission('change_ticket_fields',$ticket_id)) || ($agentonly == 1 && $wpscfunction->has_permission('change_agentonly_fields',$ticket_id)) ) && $oldVal != $newVal ) {
							foreach ($newVal as $key => $value) {
							  $attachment_id = intval($value);
							  update_term_meta ($attachment_id, 'active', '1');
							}
							$wpscfunction->change_field( $ticket_id, $field->slug, $newVal );
						}
						break;
				
				case '21':
						$time_format = get_term_meta($field->term_id,'wpsc_time_format',true);
						if($time_format == '12'){
							$oldVal = trim($wpscfunction->get_ticket_meta($ticket_id, $field->slug, true));
							if($oldVal){
								$oldVal = date("h:i:s a", strtotime($wpscfunction->get_ticket_meta( $ticket_id, $field->slug, true)));
							}
							$newVal = isset($fields_data->{$field->slug}) ? trim(sanitize_text_field($fields_data->{$field->slug})) : '';
							if ( isset($fields_data->{$field->slug}) && (($agentonly == 0 && $wpscfunction->has_permission('change_ticket_fields',$ticket_id)) || ($agentonly == 1 && $wpscfunction->has_permission('change_agentonly_fields',$ticket_id)) ) && $oldVal != $newVal) {
								$wpscfunction->change_field( $ticket_id, $field->slug,$newVal );
							}
						}else{
							$oldVal = trim($wpscfunction->get_ticket_meta( $ticket_id, $field->slug, true));
							$newVal = isset($fields_data->{$field->slug}) ? trim(sanitize_text_field($fields_data->{$field->slug})) : '';
							if ( isset($fields_data->{$field->slug}) && (($agentonly == 0 && $wpscfunction->has_permission('change_ticket_fields',$ticket_id)) || ($agentonly == 1 && $wpscfunction->has_permission('change_agentonly_fields',$ticket_id)) ) && $oldVal != $newVal) {
								$wpscfunction->change_field( $ticket_id, $field->slug,$newVal );
							}
						}
						
						break;

				default:
						do_action( 'wpsc_set_change_ticket_field', $fields_data, $field, $ticket_id, $wpsc_tf_type );
				
		}
		
}

$wpdb->update($wpdb->prefix.'wpsc_ticket',array('date_updated'=> date("Y-m-d H:i:s") ),array('id'=>$ticket_id));

$response = array(
	'status' => 200,
	'message' =>  "Field(s) updated successfully!"
);
