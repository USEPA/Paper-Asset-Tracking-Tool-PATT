<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user,$wpscfunction;
if (!($current_user->ID && $current_user->has_cap('edit_published_posts'))) {
	exit;
}

$wpsc_appearance_general_settings = isset($_POST) && isset($_POST['general_settings']) ? $wpscfunction->sanitize_array($_POST['general_settings']) : array();

update_option('wpsc_appearance_general_settings',$wpsc_appearance_general_settings);

do_action('wpsc_set_appearance_general_settings');

echo '{ "sucess_status":"1","messege":"'.__('Settings saved.','supportcandy').'" }';
