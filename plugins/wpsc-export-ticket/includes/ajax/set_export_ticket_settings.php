<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user, $wpscfunction;
if (!($current_user->ID && $current_user->has_cap('manage_options'))) {
	exit;
}

// Support page id
$export_role = isset($_POST) && isset($_POST['export_role']) ? $wpscfunction->sanitize_array($_POST['export_role']) : array();
update_option( 'wpsc_selected_user_roll_data',$export_role );

//Export ticket list
$wpsc_export_ticket_list = isset($_POST) && isset($_POST['wpsc_export_ticket_list']) ? $wpscfunction->sanitize_array($_POST['wpsc_export_ticket_list']) : array();
update_option( 'wpsc_export_ticket_list',$wpsc_export_ticket_list );

do_action('wpsc_set_export_settings');
echo '{ "sucess_status":"1","messege":"'.__('Settings saved.','wpsc-export-ticket').'" }';
