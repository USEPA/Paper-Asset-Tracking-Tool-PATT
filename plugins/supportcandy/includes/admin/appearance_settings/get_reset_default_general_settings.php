<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user;
if (!($current_user->ID && $current_user->has_cap('edit_published_posts'))) {
	exit;
}

$wpsc_appearance_general_settings = array (
	
	'wpsc_bg_color'                               => '#FFFFFF',
	'wpsc_text_color'                             => '#000000',
	'wpsc_action_bar_color'                       => '#1C5D8A',
	'wpsc_crt_ticket_btn_action_bar_bg_color'     => '#FF5733',
	'wpsc_crt_ticket_btn_action_bar_text_color'   => '#FFFFFF',
	'wpsc_default_btn_action_bar_bg_color'        => '#FFFFFF',
	'wpsc_default_btn_action_bar_text_color'      => '#000000',
	'wpsc_sign_out_bg_color'                       => '#FF5733',
	'wpsc_sign_out_text_color'                     => '#FFFFFF',
);

update_option('wpsc_appearance_general_settings',$wpsc_appearance_general_settings);

do_action('wpsc_reset_default_general_settings');

echo '{ "sucess_status":"1","messege":"'.__('Settings saved.','supportcandy').'" }';