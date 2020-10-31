<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user;
if (!($current_user->ID && $current_user->has_cap('edit_published_posts'))) {exit;}

$agent_id = isset($_POST) && isset($_POST['agent_id']) ? intval($_POST['agent_id']) : 0;
if (!$agent_id) {exit;}

$agent_role = isset($_POST) && isset($_POST['agent_role']) ? intval($_POST['agent_role']) : 0;
if (!$agent_role) {exit;}

update_term_meta($agent_id, 'role', $agent_role);

$user_id = get_term_meta( $agent_id, 'user_id', true);
update_user_option($user_id,'wpsc_agent_role',$agent_role);

do_action('wpsc_set_edit_agent',$agent_id);

echo '{ "sucess_status":"1","messege":"Success" }';

$label_count_history = get_option( 'wpsc_label_count_history' );
update_option('wpsc_label_count_history',++$label_count_history);
