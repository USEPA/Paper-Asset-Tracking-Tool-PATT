<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

global $current_user,$wpscfunction;

$customer_email = $wpscfunction->get_ticket_fields($ticket_id,'customer_email');

$usergroups = array();
$groups     = $wpscfunction->get_ticket_meta($ticket_id,'usergroups');
$recipients = get_term_meta($email->term_id,'recipients',true);

if($groups){
    foreach ($recipients as $recipient) {
        if($recipient == 'extra_ticket_users'){
            foreach ($groups as $group) {
                $wpsc_usergroup_userid        = get_term_meta( $group, 'wpsc_usergroup_userid');
                foreach ($wpsc_usergroup_userid as $user_id) {
                  $user         = get_user_by('id', $user_id);
                  $usergroups[] = $user->user_email;
                }
            }
        }
    }
}

$email_addresses=array_unique(array_merge($email_addresses,$usergroups));

$user = get_user_by('email',$customer_email);
if(!$user) return $email_addresses;


$users      = array();
$usergroups  = get_terms([
    'taxonomy'   => 'wpsc_usergroup_data',
    'hide_empty' => false,
    'meta_query' => array(
      'relation' => 'AND',
      array(
        'key'     => 'wpsc_usergroup_userid',
        'value'   => $user->ID,
        'compare' => '='
      ),
    ),
]);

if($usergroups){
    foreach($recipients as $recipient){
        if($recipient == 'usergroup_supervisors'){
              foreach($usergroups as $usergroup){
                  $wpsc_usergroup_supervisor_id = get_term_meta( $usergroup->term_id, 'wpsc_usergroup_supervisor_id');  
                  foreach($wpsc_usergroup_supervisor_id as $supervisor_id){
                    $user    = get_user_by('id',$supervisor_id);
                    $users[] = $user->user_email;
                  }
              }
        }elseif ($recipient == 'usergroup_members') {
              foreach($usergroups as $usergroup){
                  $wpsc_usergroup_userid        = get_term_meta( $usergroup->term_id, 'wpsc_usergroup_userid');
                  foreach($wpsc_usergroup_userid as $user_id){
                      $user    = get_user_by('id',$user_id);
                      $users[] = $user->user_email;
                  }
              }
        }
    }
}
$email_addresses=array_unique(array_merge($email_addresses,$users));

