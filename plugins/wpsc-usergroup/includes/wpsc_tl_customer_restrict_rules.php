<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

global $current_user;
$usergroups = get_terms([
	'taxonomy'   => 'wpsc_usergroup_data',
	'hide_empty' => false,
  'meta_query' => array(
    'relation' => 'AND',
      array(
        'key'     => 'wpsc_usergroup_userid',
        'value'   => $current_user->ID,
        'compare' => '='
      ),
  ),
]);

foreach($usergroups as $usergroup){
    $wpsc_usergroup_userid        = get_term_meta( $usergroup->term_id, 'wpsc_usergroup_userid');
    $wpsc_usergroup_supervisor_id = get_term_meta( $usergroup->term_id, 'wpsc_usergroup_supervisor_id');
    foreach($wpsc_usergroup_supervisor_id as $supervisor_id){
        if($supervisor_id == $current_user->ID){
            foreach($wpsc_usergroup_userid as $user_id){
                $user = get_user_by('id',$user_id);
                $restrict_rules[] = array(
              			'key'            => 'customer_email',
              			'value'          =>  $user->user_email,
              			'compare'        => '='
                );
            }
        }
    }
}