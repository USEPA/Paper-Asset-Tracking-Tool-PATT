<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user;
if (!($current_user->ID && $current_user->has_cap('edit_published_posts'))) {
	exit;
}

// Allow Rest API
$wpsc_rest_api = isset($_POST) && isset($_POST['wpsc_rest_api']) ? intval($_POST['wpsc_rest_api']) : 0;
update_option('wpsc_rest_api',$wpsc_rest_api);

// secret key
$wpsc_rest_api_secret_key = isset($_POST) && isset($_POST['wpsc_rest_api_secret_key']) ? sanitize_text_field($_POST['wpsc_rest_api_secret_key']) : '';
update_option('wpsc_rest_api_secret_key',$wpsc_rest_api_secret_key);

do_action('wpsc_set_rest_api_settings');

echo '{ "sucess_status":"1","messege":"'.__('Settings saved.','supportcandy').'" }';