<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user;
if (!($current_user->ID && $current_user->has_cap('wpsc_agent'))) {
	exit;
}

// Thank you messege
$wpsc_cron_job_schedule_setup = isset($_POST) && isset($_POST['wpsc_cron_job_schedule_setup']) ? intval($_POST['wpsc_cron_job_schedule_setup']) : '1';
update_option('wpsc_cron_job_schedule_setup',$wpsc_cron_job_schedule_setup);

do_action('wpsc_set_gerneral_settings');

echo '{ "sucess_status":"1","messege":"'.__('Settings saved.','supportcandy').'" }';
