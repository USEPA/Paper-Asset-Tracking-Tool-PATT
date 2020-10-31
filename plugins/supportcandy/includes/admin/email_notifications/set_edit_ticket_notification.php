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

// Title
$title = isset($_POST) && isset($_POST['wpsc_en_title']) ? sanitize_text_field($_POST['wpsc_en_title']) : '';
wp_update_term($term_id, 'wpsc_en', array('name' => $title));

$type = isset($_POST) && isset($_POST['wpsc_en_type']) ? sanitize_text_field($_POST['wpsc_en_type']) : '';
update_term_meta ($term_id, 'type', $type);

$subject = isset($_POST) && isset($_POST['wpsc_en_subject']) ? sanitize_text_field($_POST['wpsc_en_subject']) : '';
update_term_meta ($term_id, 'subject', $subject);

$body = isset($_POST) && isset($_POST['wpsc_en_body']) ? wp_kses_post(htmlspecialchars_decode($_POST['wpsc_en_body'], ENT_QUOTES)) : '';
update_term_meta ($term_id, 'body', $body);

$recipients = isset($_POST) && isset($_POST['wpsc_en_recipients']) ? $wpscfunction->sanitize_array($_POST['wpsc_en_recipients']) : array();
update_term_meta ($term_id, 'recipients', $recipients);

$extra_recipients = isset($_POST) && isset($_POST['wpsc_en_extra_recipients']) ? explode("\n",  $_POST['wpsc_en_extra_recipients']) : array();
$extra_recipients = $wpscfunction->sanitize_array($extra_recipients);
update_term_meta ($term_id, 'extra_recipients', $extra_recipients);

$conditions = isset($_POST) && isset($_POST['conditions']) && $_POST['conditions'] != '[]' ? sanitize_text_field($_POST['conditions']) : '';
update_term_meta ($term_id, 'conditions', $conditions);

do_action('wpsc_set_edit_ticket_notification',$term_id);

$email_subject = get_option('wpsc_email_notification_subject');
if (!$email_subject) {
	 $email_subject = array();
}
$email_subject['email_subject_' . $term_id] = $subject;
update_option('wpsc_email_notification_subject', $email_subject);


$email_body = get_option('wpsc_email_notification_body');
if (!$email_body) {
	 $email_body = array();
}
$email_body['email_body_' . $term_id] = $body;
update_option('wpsc_email_notification_body', $email_body);

echo '{ "sucess_status":"1","messege":"'.__('Email Notification updated successfully.','supportcandy').'" }';
