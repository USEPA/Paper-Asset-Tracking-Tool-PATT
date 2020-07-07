<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user, $wpscfunction;

if ($ticket_list->list_item->slug != 'usergroup') return;

$usergroups = get_terms([
	'taxonomy'   => 'wpsc_usergroup_data',
	'hide_empty' => false,
  'orderby'    => 'meta_value_num',
	'order'    	 => 'ASC'
]);

$user_group_name = array();
$user_obj = get_user_by('email',$ticket_list->ticket['customer_email']);
foreach ($usergroups as $usergroup) {
  $wpsc_user_id = get_term_meta( $usergroup->term_id, 'wpsc_usergroup_userid');
  if( $user_obj && in_array($user_obj->ID,$wpsc_user_id)){
		$user_group_name[] = $usergroup->name; 
	}
}

echo implode(', ',$user_group_name);