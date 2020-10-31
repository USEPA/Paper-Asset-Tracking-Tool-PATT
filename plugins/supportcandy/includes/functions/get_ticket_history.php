<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $wpscfunction;
$wpsc_thread_limit = get_option('wpsc_thread_limit');

$ticket_history = '';

$threads = get_posts(array(
  'post_type'      => 'wpsc_ticket_thread',
  'post_status'    => 'publish',
  'posts_per_page' => $wpsc_thread_limit+1,
  'orderby'        => 'date',
  'order'          => 'DESC',
  'meta_query'     => array(
    'relation' => 'AND',
    array(
      'key'     => 'ticket_id',
      'value'   => $ticket_id,
      'compare' => '='
    ),
    array(
      'key'     => 'thread_type',
      'value'   => array('reply','report'),
      'compare' => 'IN'
    ),
  ),
));


$auth_id           = $wpscfunction->get_ticket_fields($ticket_id, 'ticket_auth_code');

if($threads){
  $cntr = 0;
  foreach ($threads as $thread) {
    if($cntr==0){
      $cntr ++;
      continue;
    }
    $user_name    = get_post_meta($thread->ID,'customer_name',true);
    $attachments  = get_post_meta($thread->ID, 'attachments', true);
    $thread_type  = get_post_meta($thread->ID, 'thread_type',true);
    $thread_type  = '(' . $thread_type .')';

    $ticket_history .= '<hr><strong>'.$user_name.'</strong> <small><i>'.$thread->post_date.'</i>' . $thread_type  . '</small><br>'.$thread->post_content;
    if($attachments){
      $ticket_history .= 'Attachments :';		
      foreach( $attachments as $attachment ):
        $attach      = array();
        $attach_meta = get_term_meta($attachment);
        foreach ($attach_meta as $key => $value) {
          $attach[$key] = $value[0];
        }
        $download_url = site_url('/').'?wpsc_attachment='.$attachment.'&tid='.$ticket_id.'&tac='.$auth_id;
        $ticket_history.= '<br><a   style="text-decoration:none;" href="'.$download_url.'" target="_blank">'.$attach['filename'].'</a>';
      endforeach;
    }
  }
}
