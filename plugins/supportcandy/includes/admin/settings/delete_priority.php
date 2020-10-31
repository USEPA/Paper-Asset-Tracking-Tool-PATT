<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user;
if (!($current_user->ID && $current_user->has_cap('edit_published_posts'))) {exit;}

$priority_id = isset($_POST) && isset($_POST['priority_id']) ? intval($_POST['priority_id']) : 0;
if (!$priority_id) {exit;}

$wpsc_default_ticket_priority = get_option('wpsc_default_ticket_priority');
if ($wpsc_default_ticket_priority == $priority_id){
  echo '{ "sucess_status":"0","messege":"'.__('Default ticket priority can not be deleted.','supportcandy').'" }';
  die();
}

$wpsc_custom_priority_localize =  get_option('wpsc_custom_priority_localize');
unset($wpsc_custom_priority_localize['custom_priority_'.$priority_id]);
update_option('wpsc_custom_priority_localize', $wpsc_custom_priority_localize);

wp_delete_term($priority_id, 'wpsc_priorities');

do_action('wpsc_delete_priority',$priority_id);

echo '{ "sucess_status":"1","messege":"'.__('Priority deleted successfully.','supportcandy').'" }';
