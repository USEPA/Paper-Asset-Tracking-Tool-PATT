<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user, $wpscfunction,$wpdb;

$wpsc_ticket_id_type = get_option('wpsc_ticket_id_type');

// Ticket Status
$default_status = get_option('wpsc_default_ticket_status');
$ticket_status = isset($args['ticket_status']) ? intval($args['ticket_status']) : $default_status;
$ticket_status = apply_filters('wpsc_create_ticket_status', $ticket_status, $args);

// Customer name
$customer_name = isset($args['customer_name']) ? $args['customer_name'] : '';

// Customer email
$customer_email = isset($args['customer_email']) ? sanitize_text_field($args['customer_email']) : '';

// Subject
$subject          = get_term_by('slug', 'ticket_subject', 'wpsc_ticket_custom_fields' );
$wpsc_default_sub = get_term_meta( $subject->term_id,'wpsc_tf_default_subject',true);
$wpsc_tf_limit    = get_term_meta( $subject->term_id,'wpsc_tf_limit',true);
$ticket_subject   = isset($args['ticket_subject']) ? sanitize_text_field($args['ticket_subject']) : apply_filters( 'wpsc_default_subject_text', $wpsc_default_sub,$args );

if($wpsc_tf_limit){
	$ticket_subject   = substr($args['ticket_subject'],0,$wpsc_tf_limit);
}

// Category
$default_category = get_option('');
$ticket_category = isset($args['ticket_category']) ? intval($args['ticket_category']) : $default_category;
$ticket_category = apply_filters('wpsc_create_ticket_category', $ticket_category, $args);

// Priority
$default_priority = get_option('wpsc_default_ticket_priority');
$ticket_priority = isset($args['ticket_priority']) ? intval($args['ticket_priority']) : $default_priority;
$ticket_priority = apply_filters('wpsc_create_ticket_priority', $ticket_priority, $args);

$reply_source = isset($args['reply_source']) ? sanitize_text_field($args['reply_source']) : 'browser';

$user_data = get_user_by('email' ,$customer_email);
if($user_data){
	$user_type = "user";
}
else {
	$user_type = "guest";
}

$agent_created = 0;

if (  ($current_user->has_cap('wpsc_agent') && $customer_email!=$current_user->user_email && !is_multisite()) || ($current_user->has_cap('wpsc_agent') && is_multisite() && !is_super_admin($current_user->ID))) {
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
	$agent_created = $agents[0]->term_id;
}

$ip_address	= isset($_SERVER) && isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:'';
if (strlen($ip_address)>28 || $ip_address == '::1') {
	$ip_address = '';
}

$values = array(
	'ticket_status' => $ticket_status,
	'customer_name' => $customer_name,
	'customer_email' => $customer_email,
	'ticket_subject' => $ticket_subject,
	'user_type' => $user_type,
	'ticket_category' => $ticket_category,
	'ticket_priority' => $ticket_priority,
	'date_created' => date("Y-m-d H:i:s"),
	'date_updated' => date("Y-m-d H:i:s"),
	'ip_address' => $ip_address,
	'agent_created' => $agent_created,
	'ticket_auth_code' => $this->getRandomString(10),
	'active' => '1'
);

if(!$wpsc_ticket_id_type){
		$id = 0;
		do {
				$id = rand(11111111, 99999999);
				$sql = "select id from {$wpdb->prefix}wpsc_ticket where id=" . $id;
				$result = $wpdb->get_var($sql);
		} while ($result);
		$values['id'] = $id;
}

$ticket_id = $wpscfunction->create_new_ticket($values);

$data['ticket_id'] = $ticket_id;
$data['box_info'] = $args["box_info"];
do_action('patt_process_boxinfo_records', $data);

$wpscfunction->add_ticket_meta($ticket_id,'assigned_agent','0');

$wpscfunction->add_ticket_meta($ticket_id,'prev_assigned_agent','0');

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
      'value'     => '0',
      'compare'   => '='
    ),
		array(
      'key'       => 'wpsc_tf_type',
      'value'     => '0',
      'compare'   => '>'
    ),
	),
]);

if($fields){
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
			case '21':
						$text = isset($args[$field->slug]) ? $args[$field->slug] : '';
						if($text){
							$wpscfunction->add_ticket_meta($ticket_id, $field->slug,$text);
						}
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
				$date = isset($args[$field->slug]) && $args[$field->slug] ? $args[$field->slug] : '';
				if($date) {
					$date = $wpscfunction->calenderDateFormatToDateTime($date);
					$wpscfunction->add_ticket_meta($ticket_id, $field->slug,$date);
				}
				break;

			default:
				do_action('wpsc_add_ticket_meta_custom_field',$ticket_id,$tf_type,$args,$field);
				break;
			}
	}
}

 // Description
$description            = get_term_by('slug', 'ticket_description', 'wpsc_ticket_custom_fields' );
$wpsc_default_desc      = get_term_meta( $description->term_id,'wpsc_tf_default_description',true);
$ticket_description     = isset($args['ticket_description']) ? $args['ticket_description'] : apply_filters( 'wpsc_default_description_text', $wpsc_default_desc );
$description_attachment = isset($args['desc_attachment']) ? $args['desc_attachment'] : array();
$attachments            = array();
foreach ($description_attachment as $key => $value) {
	$attachment_id = intval($value);
	$attachments[] = $attachment_id;
	update_term_meta ($attachment_id, 'active', '1');
}

$signature = get_user_meta($current_user->ID,'wpsc_agent_signature',true);
if($signature && !$agent_created){
	 $signature= stripcslashes(htmlspecialchars_decode($signature, ENT_QUOTES));
	 $ticket_description.= $signature;
}

$os_platform = $wpscfunction->get_os();
$browser		 = $wpscfunction->get_browser();

$user_seen = 'null';
if($current_user->user_email == $customer_email){
	$user_seen = date("Y-m-d H:i:s");
}
// Save thread description
$thread_args = array(
  'ticket_id'      => $ticket_id,
  'reply_body'     => $this->replace_macro($ticket_description,$ticket_id),
  'customer_name'  => $customer_name,
  'customer_email' => $customer_email,
  'attachments'    => $attachments,
  'thread_type'    => 'report',
	'ip_address'		 => $ip_address,
	'reply_source'	 => $reply_source ,
	'os'						 => $os_platform,
	'browser'				 => $browser,
	'user_seen'      => $user_seen
);

$thread_args = apply_filters( 'wpsc_thread_args', $thread_args );
$thread_id = $this->submit_ticket_thread($thread_args);

$wpsc_reg_guest_user_after_create_ticket = get_option('wpsc_reg_guest_user_after_create_ticket');

if($wpsc_reg_guest_user_after_create_ticket && !email_exists($customer_email) ) {
	$random_password = wp_generate_password( $length=12, $include_standard_special_chars=false );
	$user_id = wp_create_user( $customer_name, $random_password, $customer_email );
	$creds = array(
		'user_login'    => $customer_name,
		'user_password' => $random_password,
	);
	wp_new_user_notification($user_id,null,'both');

    if (!$agent_created) {
        wp_signon($creds, false);
    }
}

do_action( 'wpsc_ticket_created', $ticket_id );
