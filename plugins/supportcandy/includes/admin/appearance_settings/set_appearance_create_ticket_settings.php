<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user, $wpscfunction;
if (!($current_user->ID && $current_user->has_cap('edit_published_posts'))) {
	exit;
}

$wpsc_appearance_create_ticket = isset($_POST) && isset($_POST['appearance_create_ticket']) ? $wpscfunction->sanitize_array($_POST['appearance_create_ticket']) : array();

update_option('wpsc_create_ticket',$wpsc_appearance_create_ticket);

do_action('wpsc_set_appearance_create_ticket_page_settings');

echo '{ "sucess_status":"1","messege":"'.__('Settings saved.','supportcandy').'" }';