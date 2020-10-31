<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user;
if (!($current_user->ID && $current_user->has_cap('edit_published_posts'))) {
	exit;
}

// Thank you messege
$wpsc_thankyou_html = isset($_POST) && isset($_POST['wpsc_thankyou_html']) ? wp_kses_post(stripslashes(htmlspecialchars_decode($_POST['wpsc_thankyou_html'], ENT_QUOTES))) : '';
update_option('wpsc_thankyou_html',$wpsc_thankyou_html);

// Thank you URL
$wpsc_thankyou_url = isset($_POST) && isset($_POST['wpsc_thankyou_url']) ? sanitize_text_field($_POST['wpsc_thankyou_url']) : '';
update_option('wpsc_thankyou_url',$wpsc_thankyou_url);

do_action('wpsc_set_gerneral_settings');

echo '{ "sucess_status":"1","messege":"'.__('Settings saved.','supportcandy').'" }';
