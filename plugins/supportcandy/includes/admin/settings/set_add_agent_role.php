<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user;
if (!($current_user->ID && $current_user->has_cap('edit_published_posts'))) {
	exit;
}

$agent_role = get_option('wpsc_agent_role');

$agent_role_item = isset($_POST) && isset($_POST['agentrole']) && is_array($_POST['agentrole']) ? $_POST['agentrole'] : array();
foreach($agent_role as $key => $val){
  $agent_role_item[sanitize_key($key)] = sanitize_text_field($val);
}

$agent_role[] = $agent_role_item;

update_option('wpsc_agent_role',$agent_role);

do_action('wpsc_set_agent_role');

echo '{ "sucess_status":"1","messege":"'.__('Settings saved.','supportcandy').'" }';

$label_count_history = get_option( 'wpsc_label_count_history' );
update_option('wpsc_label_count_history',++$label_count_history);
