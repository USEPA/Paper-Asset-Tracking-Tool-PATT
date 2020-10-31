<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user;
if (!($current_user->ID && $current_user->has_cap('edit_published_posts'))) {
	exit;
}

$wpsc_appearance_login_form = array (
	
	'wpsc_signin_button_bg_color'                => '#419641',
	'wpsc_signin_button_text_color'              => '#FFFFFF',
	'wpsc_signin_button_border_color'            => '#C3C3C3',
	'wpsc_register_now_button_bg_color'          => '#2aabd2',
	'wpsc_register_now_text_color'               => '#FFFFFF' ,
	'wpsc_register_now_button_border_color'      => '#28a4c9',
	'wpsc_continue_as_guest_button_bg_color'     => '#2aabd2',
	'wpsc_continue_as_guest_button_text_color'   => '#FFFFFF',
	'wpsc_continue_as_guest_button_border_color' => '#28a4c9',

);

update_option('wpsc_appearance_login_form',$wpsc_appearance_login_form);

do_action('wpsc_reset_default_appperance_login_form_settings');

echo '{ "sucess_status":"1","messege":"'.__('Settings saved.','supportcandy').'" }';