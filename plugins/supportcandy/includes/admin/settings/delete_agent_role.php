<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user;
if (!($current_user->ID && $current_user->has_cap('edit_published_posts'))) {exit;}

$role_id = isset($_POST) && isset($_POST['role_id']) ? intval($_POST['role_id']) : 0;
if (!$role_id) {
  exit;
}

$agent_role = get_option('wpsc_agent_role');

if ($role_id > 2) {
	unset($agent_role[$role_id]);
  update_option('wpsc_agent_role',$agent_role);
	do_action('wpsc_delete_agent_role');
	echo '{ "sucess_status":"1","messege":"'.__('Deleted successfully.','supportcandy').'" }';
} else {
	echo '{ "sucess_status":"0","messege":"'.__('Default role can not be deleted.','supportcandy').'" }';
}
