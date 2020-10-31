<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $current_user,$post,$wpscfunction;

if (!($current_user->ID && $current_user->has_cap('wpsc_agent'))) {exit;}

$rich_editing = $wpscfunction->rich_editing_status($current_user);

if ( $rich_editing ) {
	$wpsc_agent_signature = isset($_POST['wpsc_agent_signature']) ? wp_kses_post(htmlspecialchars_decode($_POST['wpsc_agent_signature'], ENT_QUOTES)) : '';
} else {
	$wpsc_agent_signature = isset($_POST['wpsc_agent_signature']) ? sanitize_textarea_field($_POST['wpsc_agent_signature']) : '';
}
update_user_meta($current_user->ID,'wpsc_agent_signature', $wpsc_agent_signature);

//setting to save agent's default filter
$blog_id 				   = get_current_blog_id();
$wpsc_agent_default_filter = isset($_POST) && isset($_POST['wpsc_agent_default_filter'])  ? sanitize_text_field($_POST['wpsc_agent_default_filter']) : 'all';
update_user_meta($current_user->ID, $blog_id . '_wpsc_user_default_filter', $wpsc_agent_default_filter);

