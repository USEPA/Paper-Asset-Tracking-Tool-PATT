<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

global $current_user, $wpscfunction;

$ticket_id = get_post_meta( $thread->ID, 'ticket_id', true);
$auth_id   = $wpscfunction->get_ticket_fields($ticket_id,'ticket_auth_code');

$attachments = get_post_meta( $thread->ID, 'attachments', true);
$attachments = is_array($attachments) ? $attachments : array();
$response_attachments = array();
foreach ($attachments as $attachment) {
  
  $attach      = array();
  $attach_meta = get_term_meta($attachment);
  foreach ($attach_meta as $key => $value) {
    $attach[$key] = $value[0];
  }
  $upload_dir   = wp_upload_dir();
  $file_url     = $upload_dir['baseurl'] . '/wpsc/'.$attach['save_file_name'];
  $download_url = $attach['is_image'] ? $file_url : site_url('/').'?wpsc_attachment='.$attachment.'&tid='.$ticket_id.'&tac='.$auth_id;
  
  $response_attachments[] = array(
    'filename'     => $attach['filename'],
    'is_image'     => $attach['is_image'],
    'download_url' => $download_url,
  );
  
}

$response = array(
  'id'          => $thread->ID,
  'ticket_id'   => $ticket_id,
  'thread_type' => get_post_meta( $thread->ID, 'thread_type', true),
  'user_name'   => get_post_meta( $thread->ID, 'customer_name', true),
  'user_email'  => get_post_meta( $thread->ID, 'customer_email', true),
  'thread_body' => $thread->post_content,
  'attachments' => $response_attachments,
  'date'        => $thread->post_date_gmt,
);
