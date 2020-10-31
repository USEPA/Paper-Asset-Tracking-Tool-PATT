<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

global $wpscfunction,$current_user,$wpdb;

$check_flag = false;
$time = get_option('wpsc_out_of_sla_mail_time');

if($time){
  $now = time();
  $ago = strtotime($time);
  $diff = $now - $ago;
  $diff_minutes = round( $diff / 60 );
  if($diff_minutes >= 60){
    $check_flag = true;
  }
}

if(!(!$time || $check_flag)){
	return;
}

$ticket_data = $wpdb->get_results("SELECT *  FROM {$wpdb->prefix}wpsc_ticketmeta  WHERE meta_key ='sla_term' AND meta_value != '0'"
);


foreach ($ticket_data as $key => $ticket) {
  $from_name     = get_option('wpsc_en_from_name','');
  $from_email    = get_option('wpsc_en_from_email','');
  $reply_to      = get_option('wpsc_en_reply_to','');
  $ignore_emails = get_option('wpsc_en_ignore_emails','');
  $ticket_id     = $ticket->ticket_id;

  if ( !$from_name || !$from_email ) {
     return;
  }

  $sla = $wpscfunction->get_ticket_meta($ticket_id,'sla',true);

  $email_templates = get_terms([
    'taxonomy'   => 'wpsc_en',
    'hide_empty' => false,
    'orderby'    => 'ID',
    'order'      => 'ASC',
    'meta_query' => array(
      'relation' => 'AND',
      array(
        'key'     => 'type',
        'value'   => 'out_of_sla',
        'compare' => '='
      ),
    ),
  ]);

  $now  = new DateTime;
  $ago  = new DateTime($sla);
  $diff = $now->diff($ago);
  $out_of_sla = $wpscfunction->get_ticket_meta($ticket_id,'wpsp_out_of_sla_email_send',true);
  foreach ($email_templates as $email) :
    if( $diff->invert && !$out_of_sla){
      $conditions = get_term_meta($email->term_id,'conditions',true);
      if( $wpscfunction->check_ticket_conditions($conditions,$ticket_id) ) :
        $subject          = $wpscfunction->replace_macro(get_term_meta($email->term_id,'subject',true),$ticket_id);
        $subject          = '['.get_option('wpsc_ticket_alice','').$ticket_id.'] '.$subject;
        $body             = $wpscfunction->replace_macro(get_term_meta($email->term_id,'body',true),$ticket_id);
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
                $get_ticket = $wpscfunction->get_ticket($ticket_id);
                $email_addresses[] = $get_ticket['customer_email'];
                break;
              case 'assigned_agent':
                $email_addresses = array_merge($email_addresses,$wpscfunction->get_assigned_agent_emails($ticket_id));
                break;
            }
          }
        }
        if($extra_recipients) {
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

        $bcc = implode(',',$email_addresses);

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
          'email_type'    => 'out_of_sla',

        );
        
        $wpdb->insert( $wpdb->prefix . 'wpsc_email_notification',$args );

        do_action('wpsc_after_out_of_sla_mail',$ticket_id,$args);
          

      endif;
    }
  endforeach;
}

update_option('wpsc_out_of_sla_mail_time', date("Y-m-d H:i:s"));