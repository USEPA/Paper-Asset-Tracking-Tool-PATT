<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user, $wpscfunction,$wpdb;

if (!($current_user->ID && $current_user->has_cap('manage_options'))) {
	exit;
}

// select dashboard filter 
$wpsc_dashboard_report_filters = isset($_POST) && isset($_POST['wpsc_dashboard_report_filters']) ? sanitize_text_field($_POST['wpsc_dashboard_report_filters']) : '';
update_option('wpsc_dashboard_report_filters',$wpsc_dashboard_report_filters);


// select dropdown fields reports
$wpsc_report_dash_widgets = isset($_POST) && isset($_POST['wpsc_report_dash_widgets']) ? $wpscfunction->sanitize_array($_POST['wpsc_report_dash_widgets']) : array();
update_option( 'wpsc_report_dash_widgets',$wpsc_report_dash_widgets );

do_action('wpsc_set_reports_settings');
echo '{ "sucess_status":"1","messege":"'.__('Settings saved.','wpsc-rp').'" }';
