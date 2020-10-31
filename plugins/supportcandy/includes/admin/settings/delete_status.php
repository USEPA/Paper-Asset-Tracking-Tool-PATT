<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user;
if (!($current_user->ID && $current_user->has_cap('edit_published_posts'))) {exit;}

$status_id = isset($_POST) && isset($_POST['status_id']) ? intval($_POST['status_id']) : 0;
if (!$status_id) {exit;}

$wpsc_default_ticket_status = get_option('wpsc_default_ticket_status');
if ($wpsc_default_ticket_status == $status_id){
  echo '{ "sucess_status":"0","messege":"'.__('Default ticket status can not be deleted.','supportcandy').'" }';
  die();
}
$wpsc_ticket_status_after_customer_reply = get_option('wpsc_ticket_status_after_customer_reply');
if ($wpsc_ticket_status_after_customer_reply == $status_id){
  echo '{ "sucess_status":"0","messege":"'.__('Ticket status after customer reply can not be deleted.','supportcandy').'" }';
  die();
}
$wpsc_ticket_status_after_agent_reply = get_option('wpsc_ticket_status_after_agent_reply');
if ($wpsc_ticket_status_after_agent_reply == $status_id){
  echo '{ "sucess_status":"0","messege":"'.__('Ticket status after agent reply can not be deleted.','supportcandy').'" }';
  die();
}
$wpsc_close_ticket_status = get_option('wpsc_close_ticket_status');
if ($wpsc_close_ticket_status == $status_id){
  echo '{ "sucess_status":"0","messege":"'.__('Default ticket close status can not be deleted.','supportcandy').'" }';
  die();
}

$wpsc_custom_status_localize =  get_option('wpsc_custom_status_localize');
unset($wpsc_custom_status_localize['custom_status_' .$status_id]);
update_option('wpsc_custom_status_localize', $wpsc_custom_status_localize);

wp_delete_term($status_id, 'wpsc_statuses');

do_action('wpsc_delete_status',$status_id);

echo '{ "sucess_status":"1","messege":"'.__('Status deleted successfully.','supportcandy').'" }';
