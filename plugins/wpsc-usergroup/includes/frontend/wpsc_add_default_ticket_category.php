<?php
if ( ! defined( 'ABSPATH' ) )
    exit; // Exit if accessed directly
?>
<?php
$user             = get_user_by('email',$customer_email);
$usergroups       = get_terms([
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

if($usergroups && isset($args['is_reply']) && $args['is_reply']==0){
	foreach($usergroups as $usergroup){
		$wpsc_usergroup_category = get_term_meta( $usergroup->term_id, 'wpsc_usergroup_category',true);
		if($wpsc_usergroup_category != 0){
			$ticket_category = isset($wpsc_usergroup_category) ? intval($wpsc_usergroup_category) : $default_category;
		}else{
			$ticket_category = isset($args['ticket_category']) ? intval($args['ticket_category']) : $default_category;
		}
	}
}
