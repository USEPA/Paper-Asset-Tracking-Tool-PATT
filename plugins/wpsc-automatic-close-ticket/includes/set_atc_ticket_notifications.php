<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user;
if (!($current_user->ID && $current_user->has_cap('manage_options'))) {
	exit;
}

$wpsc_atc_waring_email_age = isset($_POST['wpsc_atc_waring_email_age']) ? sanitize_text_field($_POST['wpsc_atc_waring_email_age']) : 'd';
update_option('wpsc_atc_waring_email_age',$wpsc_atc_waring_email_age);

$subject = isset($_POST['wpsc_atc_subject']) ? sanitize_text_field($_POST['wpsc_atc_subject']) : '';
update_option('wpsc_atc_subject',$subject);

$body = isset($_POST['wpsc_atc_email_body']) ? wp_kses_post($_POST['wpsc_atc_email_body']) : '';
update_option('wpsc_atc_email_body',$body);

echo '{ "sucess_status":"1","messege":"'.__('Settings saved.','wpsc-atc').'" }';
