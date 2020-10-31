<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

global $wpscfunction,$wpdb;

$check_flag = false;
$last_check = get_option('wpsc_atc_cron_last_check');

if($last_check){
  $now = time();
  $ago = strtotime($last_check);
  $diff = $now - $ago;
  $diff_minutes = round( $diff / 60 );

	if( $diff_minutes >= 60 ){
		$check_flag = true;
	}
}

if(!(!$last_check || $check_flag)){
	return;
} else {
  update_option('wpsc_atc_cron_last_check',date("Y-m-d H:i:s"));
}

$wpsc_atc_age = get_option('wpsc_atc_age','0');

if(!$wpsc_atc_age){
  return;
}

//Close tickets
$wpsc_tl_statuses  = get_option('wpsc_tl_statuses');
$wpsc_close_ticket_status = get_option('wpsc_close_ticket_status');
$wpsc_atc_waring_email_age = get_option('wpsc_atc_waring_email_age');

$from_name     = get_option('wpsc_en_from_name','');
$from_email    = get_option('wpsc_en_from_email','');
$reply_to      = get_option('wpsc_en_reply_to','');

$reply_to = $reply_to ? $reply_to : $from_email;

$output_val = implode(',',$wpsc_tl_statuses);
$ticket_data = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}wpsc_ticket WHERE ticket_status IN ($output_val) AND active = 1");

foreach ($ticket_data as $key => $ticket) {

  $ticket_id    = $ticket->id;
  $now          = time();
  $date_updated = $ticket->date_updated;
  $upago        = strtotime($date_updated);
  $diff         = $now - $upago;
  $diff_days    = intval( $diff / (60 * 60 * 24));

	if( $diff_days > $wpsc_atc_age ){
    $wpscfunction->change_status($ticket_id, $wpsc_close_ticket_status);
    continue;
  }

  //send warning email
  $d = $wpsc_atc_age - $wpsc_atc_waring_email_age;

  $mail_status = $wpscfunction->get_ticket_meta($ticket_id,'wpsp_warning_email_send',true);
  // get_post_meta($post->ID,'wpsp_warning_email_send',true);
  if( $diff_days > $d && !$mail_status){
    $subject  = get_option('wpsc_atc_subject');
    $body     = stripslashes(get_option('wpsc_atc_email_body'));

    $subject = $wpscfunction->replace_macro($subject,$ticket_id);
    $subject = '['.get_option('wpsc_ticket_alice','').$ticket_id.'] '.$subject;
    $body    = $wpscfunction->replace_macro($body,$ticket_id);
    $customer_email = $ticket->customer_email;

    $args = array(
      'ticket_id'     => $ticket_id,
      'from_email'    => $from_email,
      'reply_to'      => $reply_to,
      'email_subject' => $subject,
      'email_body'    => $body,
      'to_email'      => $customer_email,
      'bcc_email'     => '',
      'date_created'  => date("Y-m-d H:i:s"),
      'mail_status'   => 0,
      'email_type'    => 'atc',

    ); 

    $wpdb->insert( $wpdb->prefix . 'wpsc_email_notification',$args );

    do_action('wpsc_after_automatic_ticket_close_mail',$ticket_id,$args);    
    
  }
}

