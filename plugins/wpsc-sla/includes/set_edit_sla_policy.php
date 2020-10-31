<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpdb, $current_user, $wpscfunction;
if (!($current_user->ID && $current_user->has_cap('manage_options'))) {exit;}

$term_id = isset($_POST) && isset($_POST['term_id']) ? intval($_POST['term_id']) : 0;
if (!$term_id) {exit;}

$title = isset($_POST) && isset($_POST['wpsc_sla_policy_title']) ? sanitize_text_field($_POST['wpsc_sla_policy_title']) : '';
if (!$title) {exit;}

$time = isset($_POST) && isset($_POST['wpsc_sla_policy_time']) ? intval($_POST['wpsc_sla_policy_time']) : 0;
if (!$time) {exit;}

$time_unit = isset($_POST) && isset($_POST['wpsc_sla_policy_time_unit']) ? sanitize_text_field($_POST['wpsc_sla_policy_time_unit']) : '';
if (!$time_unit) {exit;}
$conditions = isset($_POST) && isset($_POST['conditions']) && $_POST['conditions'] != '[]' ? sanitize_text_field($_POST['conditions']) : '';

wp_update_term($term_id, 'wpsc_sla', array(
  'name' => $title
));
update_term_meta ($term_id, 'time', $time);
update_term_meta ($term_id, 'time_unit', $time_unit);
update_term_meta ($term_id, 'conditions', $conditions);

do_action('wpsc_set_edit_sla',$term_id);
echo '{ "sucess_status":"1","messege":"'.__('Policy updated successfully.','wpsc-sla').'" }';
