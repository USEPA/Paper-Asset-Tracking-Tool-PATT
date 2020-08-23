<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user,$wpscfunction;

if (!($current_user->ID && $current_user->has_cap('edit_published_posts'))) {exit;}

$ticket_widget_id = isset($_POST) && isset($_POST['ticket_widget_id']) ? sanitize_text_field($_POST['ticket_widget_id']) : '';
if (!$ticket_widget_id) {exit;}

$ticket_widget_name = isset($_POST) && isset($_POST['ticket_widget_name']) ? sanitize_text_field($_POST['ticket_widget_name']) : '';
if (!$ticket_widget_name) {exit;}

update_term_meta ($ticket_widget_id, 'wpsc_label',$ticket_widget_name);

update_term_meta($ticket_widget_id, 'wpsc_ticket_widget_type', sanitize_text_field($_POST['ticket_widget_type']));

$wpsc_ticket_widget_role = isset($_POST) && isset($_POST['ticket_widget_role']) ? $wpscfunction->sanitize_array($_POST['ticket_widget_role']) : array();
update_term_meta ($ticket_widget_id, 'wpsc_ticket_widget_role',$wpsc_ticket_widget_role);

$wpsc_custom_widget_localize = get_option('wpsc_custom_widget_localize');
$wpsc_custom_widget_localize['custom_widget_'.$ticket_widget_id] = $ticket_widget_name;
update_option('wpsc_custom_widget_localize', $wpsc_custom_widget_localize);

do_action('wpsc_set_edit_ticket_widget',$ticket_widget_id);

echo '{ "success_status":"1","messege":"'.__('Settings saved.','supportcandy').'" }';