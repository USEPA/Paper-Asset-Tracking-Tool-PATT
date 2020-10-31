<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user, $wpdb;
if (!($current_user->ID && $current_user->has_cap('edit_published_posts'))) {exit;}

$priority_name = isset($_POST) && isset($_POST['priority_name']) ? sanitize_text_field($_POST['priority_name']) : '';
if (!$priority_name) {exit;}

$priority_color = isset($_POST) && isset($_POST['priority_color']) ? sanitize_text_field($_POST['priority_color']) : '';
if (!$priority_color) {exit;}

$priority_bg_color = isset($_POST) && isset($_POST['priority_bg_color']) ? sanitize_text_field($_POST['priority_bg_color']) : '';
if (!$priority_bg_color) {exit;}

if ($priority_color==$priority_bg_color) {
  echo '{ "sucess_status":"0","messege":"'.__('Priority color and background color should not be same.','supportcandy').'" }';
  die();
}

$term = wp_insert_term( $priority_name, 'wpsc_priorities' );
if (!is_wp_error($term) && isset($term['term_id'])) {
  $load_order = $wpdb->get_var("select max(meta_value) as load_order from {$wpdb->prefix}termmeta WHERE meta_key='wpsc_priority_load_order'");
  add_term_meta ($term['term_id'], 'wpsc_priority_load_order', ++$load_order);
  add_term_meta ($term['term_id'], 'wpsc_priority_color', $priority_color);
  add_term_meta ($term['term_id'], 'wpsc_priority_background_color', $priority_bg_color);
	
	$wpsc_custom_priority_localize =  get_option('wpsc_custom_priority_localize');
	if(!$wpsc_custom_priority_localize){
		$wpsc_custom_priority_localize = array();
	}
	$wpsc_custom_priority_localize['custom_priority_'.$term['term_id']] = $priority_name;
	update_option('wpsc_custom_priority_localize', $wpsc_custom_priority_localize);
	
	do_action('wpsc_set_add_priority',$term['term_id']);
	echo '{ "sucess_status":"1","messege":"'.__('Priority added successfully.','supportcandy').'" }';
} else {
	echo '{ "sucess_status":"0","messege":"'.__('An error occured while creating priority.','supportcandy').'" }';
}
