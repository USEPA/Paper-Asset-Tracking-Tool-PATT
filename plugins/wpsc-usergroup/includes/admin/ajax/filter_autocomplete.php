<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$output = array();
$term = isset($_REQUEST) && isset($_REQUEST['term']) ? sanitize_text_field($_REQUEST['term']) : '';
if (!$term) {exit;}

$users = get_users(array('search'=>'*'.$term.'*'));

foreach ($users as $user) {
		$check_agents = term_exists('agent_'.$user->ID);
		if($check_agents == ''){
				 $output[] = array(
			     'id' => $user->ID,
			     'label' => $user->display_name,
			   );
		 }
}

if (!$output) {
	  $output[] = array(
	    'id' => '',
	    'label' => __('No matching users','wpsc-usergroup')
	  );
}

echo json_encode($output);