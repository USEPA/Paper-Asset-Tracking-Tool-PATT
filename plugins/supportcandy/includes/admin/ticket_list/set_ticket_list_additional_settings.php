<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user;
if (!($current_user->ID && $current_user->has_cap('edit_published_posts'))) {
	exit;
}

$wpsc_tl_agent_orderby = isset($_POST) && isset($_POST['wpsc_tl_agent_orderby']) ? sanitize_text_field($_POST['wpsc_tl_agent_orderby']) : '';
update_option('wpsc_tl_agent_orderby',$wpsc_tl_agent_orderby);

$wpsc_tl_agent_orderby_order = isset($_POST) && isset($_POST['wpsc_tl_agent_orderby_order']) ? sanitize_text_field($_POST['wpsc_tl_agent_orderby_order']) : '';
update_option('wpsc_tl_agent_orderby_order',$wpsc_tl_agent_orderby_order);

$wpsc_tl_agent_no_of_tickets = isset($_POST) && isset($_POST['agent_no_of_tickets']) ? sanitize_text_field($_POST['agent_no_of_tickets']) : '';
update_option('wpsc_tl_agent_no_of_tickets',$wpsc_tl_agent_no_of_tickets);

$wpsc_tl_agent_unresolve_statuses = isset($_POST) && isset($_POST['wpsc_tl_agent_unresolve_statuses']) && is_array($_POST['wpsc_tl_agent_unresolve_statuses']) ? $_POST['wpsc_tl_agent_unresolve_statuses'] : array();
foreach ($wpsc_tl_agent_unresolve_statuses as $key => $value) {
  $wpsc_tl_agent_unresolve_statuses[] = intval($value);
}
update_option('wpsc_tl_agent_unresolve_statuses',$wpsc_tl_agent_unresolve_statuses);

$wpsc_tl_customer_orderby = isset($_POST) && isset($_POST['wpsc_tl_customer_orderby']) ? sanitize_text_field($_POST['wpsc_tl_customer_orderby']) : '';
update_option('wpsc_tl_customer_orderby',$wpsc_tl_customer_orderby);

$wpsc_tl_customer_orderby_order = isset($_POST) && isset($_POST['customer_orderby_order']) ? sanitize_text_field($_POST['customer_orderby_order']) : '';
update_option('wpsc_tl_customer_orderby_order',$wpsc_tl_customer_orderby_order);

$wpsc_tl_customer_no_of_tickets = isset($_POST) && isset($_POST['customer_no_of_tickets']) ? sanitize_text_field($_POST['customer_no_of_tickets']) : '';
update_option('wpsc_tl_customer_no_of_tickets',$wpsc_tl_customer_no_of_tickets);

$wpsc_tl_customer_unresolve_statuses = isset($_POST) && isset($_POST['wpsc_tl_customer_unresolve_statuses']) && is_array($_POST['wpsc_tl_customer_unresolve_statuses']) ? $_POST['wpsc_tl_customer_unresolve_statuses'] : array();
foreach ($wpsc_tl_customer_unresolve_statuses as $key => $value) {
  $wpsc_tl_customer_unresolve_statuses[] = intval($value);
}
update_option('wpsc_tl_customer_unresolve_statuses',$wpsc_tl_customer_unresolve_statuses);

$label_count_history = get_option( 'wpsc_label_count_history' );
update_option('wpsc_label_count_history',++$label_count_history);
 
do_action('wpsc_tl_additional_settings');

echo '{ "sucess_status":"1","messege":"'.__('Settings saved.','supportcandy').'" }';
