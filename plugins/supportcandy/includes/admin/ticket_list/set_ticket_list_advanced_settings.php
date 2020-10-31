<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user,$wpscfunction;
if (!($current_user->ID && $current_user->has_cap('edit_published_posts'))) {
	exit;
}
$wpsc_close_ticket_status = get_option('wpsc_close_ticket_status');
$wpsc_close_ticket_group = isset($_POST) && isset($_POST['wpsc_close_ticket_group']) && is_array($_POST['wpsc_close_ticket_group']) ? $wpscfunction->sanitize_array($_POST['wpsc_close_ticket_group']) : array();
foreach ($wpsc_close_ticket_group as $key => $value) {
  $wpsc_close_ticket_group[] = intval($value);
}
update_option('wpsc_close_ticket_group',$wpsc_close_ticket_group);

$label_count_history = get_option( 'wpsc_label_count_history' );
update_option('wpsc_label_count_history',++$label_count_history);
 
do_action('wpsc_tl_advanced_settings');
echo '{ "sucess_status":"1","messege":"'.__('Settings saved.','supportcandy').'" }';
?>