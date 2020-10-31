<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user, $wpscfunction;
if (!($current_user->ID && $current_user->has_cap('edit_published_posts'))) {
	exit;
}

$wpsc_appearance_individual_ticket_page = isset($_POST) && isset($_POST['individual_ticket_page']) ? $wpscfunction->sanitize_array($_POST['individual_ticket_page']) : array();

update_option('wpsc_individual_ticket_page',$wpsc_appearance_individual_ticket_page);
	
do_action('wpsc_set_appearance_individual_ticket_page_settings');

echo '{ "sucess_status":"1","messege":"'.__('Settings saved.','supportcandy').'" }';