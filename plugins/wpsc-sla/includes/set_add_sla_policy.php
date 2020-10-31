<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpdb, $current_user, $wpscfunction;
if (!($current_user->ID && $current_user->has_cap('manage_options'))) {exit;}

$title = isset($_POST) && isset($_POST['wpsc_sla_policy_title']) ? sanitize_text_field($_POST['wpsc_sla_policy_title']) : '';
if (!$title) {exit;}

$time = isset($_POST) && isset($_POST['wpsc_sla_policy_time']) ? intval($_POST['wpsc_sla_policy_time']) : 0;
if (!$time) {exit;}

$time_unit = isset($_POST) && isset($_POST['wpsc_sla_policy_time_unit']) ? sanitize_text_field($_POST['wpsc_sla_policy_time_unit']) : '';
if (!$time_unit) {exit;}

$conditions = isset($_POST) && isset($_POST['conditions']) && $_POST['conditions'] != '[]' ? sanitize_text_field($_POST['conditions']) : '';

$term = wp_insert_term( $title, 'wpsc_sla' );
if (!is_wp_error($term) && isset($term['term_id'])) {
  $load_order = $wpdb->get_var("select max(meta_value) as load_order from {$wpdb->prefix}termmeta WHERE meta_key='wpsc_sla_load_order'");
  $load_order = $load_order ? $load_order : 0;
  add_term_meta ($term['term_id'], 'load_order', ++$load_order);
  add_term_meta ($term['term_id'], 'time', $time);
  add_term_meta ($term['term_id'], 'time_unit', $time_unit);
  add_term_meta ($term['term_id'], 'conditions', $conditions);
	do_action('wpsc_set_add_sla',$term['term_id']);
	echo '{ "sucess_status":"1","messege":"'.__('Policy added successfully.','wpsc-sla').'" }';
} else {
	echo '{ "sucess_status":"0","messege":"'.__('An error occured while creating policy.','wpsc-sla').'" }';
}
