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

$source_term = get_term_by('id',$term_id,'wpsc_en');

// Title
$title = $source_term->name.' clone';
$term = wp_insert_term( $title, 'wpsc_en' );
if($term && isset($term['term_id'])){
  
  $type = get_term_meta($term_id,'type',true);
  add_term_meta ($term['term_id'], 'type', $type);
  
  $subject = get_term_meta($term_id,'subject',true);
  add_term_meta ($term['term_id'], 'subject', $subject);
  
  $body = get_term_meta($term_id,'body',true);
  add_term_meta ($term['term_id'], 'body', $body);
  
  $recipients = get_term_meta($term_id,'recipients',true);
  add_term_meta ($term['term_id'], 'recipients', $recipients);
  
  $extra_recipients = get_term_meta($term_id,'extra_recipients',true);
  add_term_meta ($term['term_id'], 'extra_recipients', $extra_recipients);
  
  $arr = get_term_meta($term_id,'conditions',true);
	add_term_meta ($term['term_id'], 'conditions', $arr);
  
  do_action('wpsc_clone_ticket_notification',$term,$source_term);
  echo '{ "sucess_status":"1","messege":"'.__('Email Notification clone successfull.','supportcandy').'" }';
  
} else {
  echo '{ "sucess_status":"0","messege":"'.__('An error occured while creating email notification.','supportcandy').'" }';
}
