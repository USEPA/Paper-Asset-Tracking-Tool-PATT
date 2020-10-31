<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user, $wpdb;
if (!($current_user->ID && $current_user->has_cap('edit_published_posts'))) {exit;}

$agent_name = isset($_POST) && isset($_POST['agent_name']) ? sanitize_text_field($_POST['agent_name']) : '';
if (!$agent_name) {exit;}

$agent_id = isset($_POST) && isset($_POST['agent_id']) ? intval($_POST['agent_id']) : 0;
if (!$agent_id) {exit;}

$agent_role = isset($_POST) && isset($_POST['agent_role']) ? intval($_POST['agent_role']) : 0;
if (!$agent_role) {exit;}

$user_info  = get_userdata($agent_id);

$term = wp_insert_term( 'agent_'.$agent_id, 'wpsc_agents' );
if (!is_wp_error($term) && isset($term['term_id'])) {
  
	add_term_meta ($term['term_id'], 'user_id', $agent_id);
  add_term_meta ($term['term_id'], 'label', $agent_name);
  add_term_meta ($term['term_id'], 'role', $agent_role);
  add_term_meta ($term['term_id'], 'agentgroup', '0');
	add_term_meta ($term['term_id'], 'first_name', $user_info->first_name);
	add_term_meta ($term['term_id'], 'last_name', $user_info->last_name);
	add_term_meta ($term['term_id'], 'nicename', $user_info->user_nicename);
	add_term_meta ($term['term_id'], 'email', $user_info->user_email);
	
	$user = get_user_by('id',$agent_id);
	$user->add_cap('wpsc_agent');
	
	update_user_option($agent_id,'wpsc_agent_role',$agent_role);
	
	do_action('wpsc_set_add_agent',$term['term_id']);
	echo '{ "sucess_status":"1","messege":"'.__('Agent added successfully.','supportcandy').'" }';
	
} else {
	echo '{ "sucess_status":"0","messege":"'.__('Agent already exist.','supportcandy').'" }';
}

$label_count_history = get_option( 'wpsc_label_count_history' );
update_option('wpsc_label_count_history',++$label_count_history);
