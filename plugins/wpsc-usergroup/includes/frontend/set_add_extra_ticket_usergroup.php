<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user, $wpdb, $wpscfunction,$wpscugfunction;
if (!($current_user->ID)) {exit;}


$new_usergroup  = isset($_POST['usergroups']) && is_array($_POST['usergroups']) ? $_POST['usergroups'] : array() ;
$old_usergroups = $wpscfunction->get_ticket_meta($ticket_id,'usergroups');
$usergroups = array();

foreach( $new_usergroup as $groups ){
  $groups = intval($groups) ? intval($groups) : 0;
    if ($groups){
      $usergroups[] = $groups;
		}
}
$new_usergroups = array_unique($usergroups);

if( ($old_usergroups != $new_usergroups)){
    $wpscugfunction->add_usergroups( $ticket_id, $new_usergroups);
}	
