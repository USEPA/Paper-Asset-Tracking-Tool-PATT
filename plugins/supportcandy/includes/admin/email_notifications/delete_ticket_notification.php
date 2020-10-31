<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user, $wpscfunction;
if (!($current_user->ID && $current_user->has_cap('edit_published_posts'))) {
	exit;
}

$term_id = isset($_POST) && isset($_POST['term_id']) ? intval($_POST['term_id']) : 0;
if(!$term_id) die();

$email_subject = get_option('wpsc_email_notification_subject');
unset($email_subject['email_subject_' . $term_id]);
update_option('wpsc_email_notification_subject', $email_subject);

$email_body = get_option('wpsc_email_notification_body');
unset($email_body['email_body_' . $term_id]);
update_option('wpsc_email_notification_body', $email_body);

$response = wp_delete_term( $term_id,'wpsc_en');

if(!is_wp_error($response)){
  
  do_action('wpsc_delete_ticket_notification',$term_id);
  echo '{ "sucess_status":"1","messege":"'.__('Email Notification delete successfull.','supportcandy').'" }';
  
} else {
  echo '{ "sucess_status":"0","messege":"'.__('An error occured while deleting email notification.','supportcandy').'" }';
}
