<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user,$wpscfunction;
if (!($current_user->ID && $current_user->has_cap('wpsc_agent'))) {
 exit;
}
$ticket_id  = isset($_POST['ticket_id']) ? sanitize_text_field($_POST['ticket_id']) : '' ;
$subject    = isset($_POST['subject']) ? stripslashes(sanitize_text_field($_POST['subject'])) : '';
$ticket     = $wpscfunction->get_ticket($ticket_id);

$tickets =array();
foreach ($ticket as $key => $value) {
  	$tickets[$key] =$value;
}

$ip_address	= isset($_SERVER) && isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:'';
if (strlen($ip_address)>28 || $ip_address == '::1') {
	$ip_address = '';
}

$values = array(
	
    'ticket_subject'  => $subject,
    'customer_name'   => $tickets['customer_name'],
    'customer_email'  => $tickets['customer_email'],
    'ticket_status'   => $tickets['ticket_status'],
    'ticket_category' => $tickets['ticket_category'],
    'ticket_priority' => $tickets['ticket_priority'],
    'date_created'    => $tickets['date_created'],
    'date_updated'    => $tickets['date_updated'],	
		'agent_created'   => $tickets['agent_created'],
		'ip_address' 			=> $ip_address,
);

$custom_fields = get_terms([
	'taxonomy'   => 'wpsc_ticket_custom_fields',
	'hide_empty' => false,
	'orderby'    => 'meta_value_num',
	'meta_key'	 => 'wpsc_tf_load_order',
	'order'    	 => 'ASC',
	'meta_query' => array(
		 array(
      'key'       => 'agentonly',
      'value'     => array(0,1),
      'compare'   => 'IN'
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
		 if($value){
			 $values[$field->slug]=$value;
		 }
	 }
}

$args = array(
  'post_type'      => 'wpsc_ticket_thread',
  'post_status'    => 'publish',
	'order'    	     => 'ASC',
  'meta_query'     => array(
     array(
      'key'     => 'ticket_id',
      'value'   => $ticket_id,
      'compare' => '='
    ),
  ),
);
$ticket_threads = get_posts($args);
$clone_ticket_id = $wpscfunction->create_clone_ticket($values);


foreach ($ticket_threads as $ticket_thread) {
  $thread_type    = get_post_meta( $ticket_thread->ID, 'thread_type', true);
	if($thread_type=='log'){
		continue;
	}
  $customer_name  = get_post_meta( $ticket_thread->ID, 'customer_name', true);
  $customer_email = get_post_meta( $ticket_thread->ID, 'customer_email', true);
  $attachments    = get_post_meta( $ticket_thread->ID, 'attachments', true);
	if(!$attachments){
		$attachments = '';
	}
  $thread_body    = $ticket_thread->post_content;
	$create_time    = $ticket_thread->post_date;
	
	$os_platform = $wpscfunction->get_os();
	$browser		 = $wpscfunction->get_browser();
	
	$thread_args = array(
	  'ticket_id'      => $clone_ticket_id,
	  'reply_body'     => $thread_body,
	  'customer_name'  => $customer_name,
	  'customer_email' => $customer_email,
	  'attachments'    => $attachments,
	  'thread_type'    => $thread_type,
		'create_time'    => $create_time,
		'ip_address'		 => $ip_address,
		'reply_source'	 => 'browser' ,
		'os'						 => $os_platform,
		'browser'				 => $browser
	);
	$thread_id = $wpscfunction->submit_cloned_ticket_thread($thread_args);
}

echo json_encode($clone_ticket_id);
?>