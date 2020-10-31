<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user;
if (!($current_user->ID && $current_user->has_cap('edit_published_posts'))) {
	exit;
}
// Allow Captcha
$wpsc_captcha = isset($_POST) && isset($_POST['wpsc_captcha']) ? sanitize_text_field($_POST['wpsc_captcha']) : '0';
update_option('wpsc_captcha',$wpsc_captcha);

// Allow Captcha in registration form
$wpsc_registration_captcha = isset($_POST) && isset($_POST['wpsc_registration_captcha']) ? sanitize_text_field($_POST['wpsc_registration_captcha']) : '0';
update_option('wpsc_registration_captcha',$wpsc_registration_captcha);

//login captcha
$wpsc_login_captcha = isset($_POST) && isset($_POST['wpsc_login_captcha']) ? sanitize_text_field($_POST['wpsc_login_captcha']) : '0';
update_option('wpsc_login_captcha',$wpsc_login_captcha);

//Allow  Recaptcha
$wpsc_recaptcha_type=isset($_POST) && isset($_POST['wpsc_recaptcha_type']) ? sanitize_text_field($_POST['wpsc_recaptcha_type']) : '';
update_option('wpsc_recaptcha_type',$wpsc_recaptcha_type);

//Get site key for Google Recaptcha
$wpsc_get_site_key = isset($_POST) && isset($_POST['wpsc_get_site_key']) ? sanitize_text_field($_POST['wpsc_get_site_key']) : '';
update_option('wpsc_get_site_key',$wpsc_get_site_key);

//Get secret key for Google Recaptcha
$wpsc_get_secret_key = isset($_POST) && isset($_POST['wpsc_get_secret_key']) ? sanitize_text_field($_POST['wpsc_get_secret_key']) : '';
update_option('wpsc_get_secret_key',$wpsc_get_secret_key);

do_action('wpsc_set_captcha_settings');

echo '{ "sucess_status":"1","messege":"'.__('Settings saved.','supportcandy').'" }';