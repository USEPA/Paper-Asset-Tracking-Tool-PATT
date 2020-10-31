<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

global $wpscfunction, $current_user,$wpdb;

$from_name     = get_option('wpsc_en_from_name','');
$from_email    = get_option('wpsc_en_from_email','');
$reply_to      = get_option('wpsc_en_reply_to','');
$ignore_emails = get_option('wpsc_en_ignore_emails','');

$wpsc_email_sending_method = get_option('wpsc_email_sending_method') ; 

if ( !$from_name || !$from_email ) {
  return;
}

$email_templates = get_terms([
  'taxonomy'   => 'wpsc_en',
  'hide_empty' => false,
  'orderby'    => 'ID',
  'order'      => 'ASC',
  'meta_query' => array(
    'relation' => 'AND',
      array(
        'key'     => 'type',
        'value'   => 'assign_agent',
        'compare' => '='
      ),
    ),
  ]);

$email_subject = get_option('wpsc_email_notification_subject');
$email_body    = get_option('wpsc_email_notification_body');

foreach ($email_templates as $email) :

  $conditions = get_term_meta($email->term_id,'conditions',true);
  if( $wpscfunction->check_ticket_conditions($conditions,$ticket_id) ) :

    $subject          = $wpscfunction->replace_macro($email_subject['email_subject_' . $email->term_id],$ticket_id);
    $subject          = '['.get_option('wpsc_ticket_alice','').$ticket_id.'] '.stripslashes($subject);
    $body             = $wpscfunction->replace_macro(stripslashes($email_body['email_body_' . $email->term_id]) ,$ticket_id);
    $recipients       = get_term_meta($email->term_id,'recipients',true);
    $extra_recipients = get_term_meta($email->term_id,'extra_recipients',true);

    $email_addresses = array();
    foreach ($recipients as $recipient) {
      if(is_numeric($recipient)){
        $agents = get_terms([
          'taxonomy'   => 'wpsc_agents',
          'hide_empty' => false,
          'meta_query' => array(
            'relation' => 'AND',
            array(
              'key'     => 'role',
              'value'   => $recipient,
              'compare' => '='
            ),
          ),
        ]);
        foreach ($agents as $agent) {
          $user_id = get_term_meta($agent->term_id,'user_id',true);
          if($user_id){
            $user = get_user_by('id',$user_id);
            $email_addresses[] = $user->user_email;
          }
        }
      } else {
        switch ($recipient) {
          case 'customer':
            $customer_email    = $wpscfunction->get_ticket_fields($ticket_id,'customer_email');
            $email_addresses[] = $customer_email;
            break;
          case 'assigned_agent':
            $email_addresses = array_merge($email_addresses,$wpscfunction->get_assigned_agent_emails($ticket_id));
            break;
          case 'previously_assigned_agent':
            $email_addresses = array_merge($email_addresses,$wpscfunction->get_previously_assigned_agents($ticket_id));
            break;
          case 'extra_ticket_users' : 
            $extra_users     = $wpscfunction->get_ticket_meta($ticket_id,'extra_ticket_users');
            $email_addresses = array_merge($email_addresses, $extra_users);
            break;
        }
      }
    }
    if(isset($extra_recipients[0]) && $extra_recipients[0] ) {
      $email_addresses = array_merge($email_addresses,$extra_recipients);
    }
    $email_addresses = array_unique($email_addresses);
    $email_addresses = array_diff($email_addresses,$ignore_emails);
    $email_addresses = array_diff($email_addresses,array($current_user->user_email));
    $email_addresses = apply_filters('wpsc_en_assign_agent_email_addresses',$email_addresses,$email,$ticket_id);
    $email_addresses = array_values($email_addresses);

    $to =  isset($email_addresses[0])? $email_addresses[0] : '';
    if($to){
      unset($email_addresses[0]);
    } else {
      continue; // no email address found to send. So go to next foreach iteration.
    }

    $from_email = apply_filters('wpsc_assign_agent_from_email_headers',$from_email,$ticket_id);
    $reply_to   = apply_filters('wpsc_assign_agent_replyto_headers',$reply_to,$ticket_id);

    $bcc = implode(',',$email_addresses);

    $to = apply_filters('wpsc_assign_agent_to_email',$to,$email_addresses,$ticket_id);
    $bcc = apply_filters('wpsc_assign_agent_bcc_emails',$bcc,$email_addresses,$ticket_id);

    $args = array(
      'ticket_id'     => $ticket_id,
      'from_email'    => $from_email,
      'reply_to'      => $reply_to,
      'email_subject' => $subject,
      'email_body'    => $body,
      'to_email'      => $to,
      'bcc_email'     => $bcc,
      'date_created'  => date("Y-m-d H:i:s"),
      'mail_status'   => 0,
      'email_type'    => 'assign_agent',

    );
    
    if($wpsc_email_sending_method){
      
      $wpdb->insert( $wpdb->prefix . 'wpsc_email_notification',$args);
      
    }else{

      $headers  = "From: {$from_name} <{$from_email}>\r\n";
      $headers .= "Reply-To: {$reply_to}\r\n";
      $email_addresses = explode(',',$bcc);
      foreach ($email_addresses as $email_address) {
        $headers .= "BCC: {$email_address}\r\n";
      }
  
      $headers .= "Content-Type: text/html; charset=utf-8\r\n";
  
      wp_mail($to, $subject, $body, $headers);    
    }

    do_action('wpsc_after_assign_agent_mail',$ticket_id,$args);  
    
  endif;

endforeach;
