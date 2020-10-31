<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user;
if (!($current_user->ID && $current_user->has_cap('edit_published_posts'))) {exit;}

$priority_id = isset($_POST) && isset($_POST['priority_id']) ? sanitize_text_field($_POST['priority_id']) : '';
if (!$priority_id) {exit;}

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

$wpsc_custom_priority_localize =  get_option('wpsc_custom_priority_localize');
$wpsc_custom_priority_localize['custom_priority_'.$priority_id] = $priority_name;
update_option('wpsc_custom_priority_localize', $wpsc_custom_priority_localize);

wp_update_term($priority_id, 'wpsc_priorities', array(
  'name' => $priority_name
));
update_term_meta($priority_id, 'wpsc_priority_color', $priority_color);
update_term_meta($priority_id, 'wpsc_priority_background_color', $priority_bg_color);

do_action('wpsc_set_edit_priority',$priority_id);

echo '{ "sucess_status":"1","messege":"Success" }';
