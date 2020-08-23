<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user;
if (!($current_user->ID && $current_user->has_cap('manage_options'))) {exit;}

$status_id = isset($_POST) && isset($_POST['status_id']) ? sanitize_text_field($_POST['status_id']) : '';
if (!$status_id) {exit;}

//$status_name = isset($_POST) && isset($_POST['status_name']) ? sanitize_text_field($_POST['status_name']) : '';
//if (!$status_name) {exit;}

$status_color = isset($_POST) && isset($_POST['status_color']) ? sanitize_text_field($_POST['status_color']) : '';
if (!$status_color) {exit;}

$status_bg_color = isset($_POST) && isset($_POST['status_bg_color']) ? sanitize_text_field($_POST['status_bg_color']) : '';
if (!$status_bg_color) {exit;}

if ($status_color==$status_bg_color) {
  echo '{ "sucess_status":"0","messege":"'.__('Status color and background color should not be same.','supportcandy').'" }';
  die();
}


//wp_update_term($status_id, 'wppatt_recall_statuses', array(
//  'name' => $status_name
//));

update_term_meta($status_id, 'wppatt_recall_status_color', $status_color);
update_term_meta($status_id, 'wppatt_recall_status_background_color', $status_bg_color);

//$wpsc_custom_status_localize =  get_option('wpsc_custom_status_localize');
//$wpsc_custom_status_localize['custom_status_'.$status_id] = $status_name;
//update_option('wpsc_custom_status_localize', $wpsc_custom_status_localize);

do_action('wpsc_set_edit_status',$status_id);

echo '{ "sucess_status":"1","messege":"Success" }';