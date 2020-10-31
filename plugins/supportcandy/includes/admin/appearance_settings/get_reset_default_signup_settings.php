<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user;
if (!($current_user->ID && $current_user->has_cap('edit_published_posts'))) {
	exit;
}

$wpsc_appearance_signup = array (
	
	'wpsc_appearance_signup_button_bg_color'      => '#419641',
	'wpsc_appearance_signup_button_text_color'    => '#FFFFFF',
	'wpsc_appearance_signup_button_border_color'  => '#C3C3C3',
	'wpsc_appearance_cancel_button_bg_color'      => '#FFFFFF',
	'wpsc_appearance_cancel_button_text_color'    => '#000000',
	'wpsc_appearance_cancel_button_border_color'  => '#C3C3C3',

);

update_option('wpsc_appearance_signup',$wpsc_appearance_signup);

do_action('wpsc_reset_appearance_signup_form');

echo '{ "sucess_status":"1","messege":"'.__('Settings saved.','supportcandy').'" }';