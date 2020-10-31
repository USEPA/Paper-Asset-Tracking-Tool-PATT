<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user, $wpscfunction,$wpdb;

$send_mail_count = get_option('wpsc_en_send_mail_count');

$from_name     = get_option('wpsc_en_from_name','');

$emails = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}wpsc_email_notification WHERE attemp=0 ORDER BY id  ASC LIMIT $send_mail_count ");
$fail_emails = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}wpsc_email_notification WHERE attemp <> 0 ORDER BY id  ASC LIMIT 1");
$all_mails = array_merge($emails, $fail_emails);

foreach($all_mails as $email){

    $reply_to = $email->reply_to ? $email->reply_to : $email->from_email;

    $headers  = "From: {$from_name} <{$email->from_email}>\r\n";
    $headers .= "Reply-To: {$reply_to}\r\n";
    $email_addresses = explode(',',$email->bcc_email);
    foreach ($email_addresses as $email_address) {
      $headers .= "BCC: {$email_address}\r\n";
    }

    $headers .= "Content-Type: text/html; charset=utf-8\r\n";

    $mail_status = wp_mail($email->to_email, $email->email_subject, $email->email_body, $headers);
    
    $attemp = $email->attemp+1;
    if(!$mail_status){
      $values=array(
        'send_date'   => date("Y-m-d H:i:s"),
        'mail_status' => 1,
        'attemp'      => $attemp
        );
      $wpdb->update($wpdb->prefix.'wpsc_email_notification', $values, array('id'=>$email->id));
      
      if($attemp >= 3){
        do_action('wpsc_after_failed_attemp_mail_sent',$email);
        $wpdb->delete($wpdb->prefix.'wpsc_email_notification',array('id'=>$email->id));
      }
    }else{
      do_action('wpsc_after_successfully_mail_sent',$email);
      $wpdb->delete($wpdb->prefix.'wpsc_email_notification',array('id'=>$email->id));
    }
}
