<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user;
if (!($current_user->ID && $current_user->has_cap('edit_published_posts'))) {
	exit;
}


$wpsc_appearance_create_ticket = array (
  
  'wpsc_submit_button_bg_color'      => '#419641',
  'wpsc_submit_button_text_color'    => '#FFFFFF',
  'wpsc_submit_button_border_color'  => '#C3C3C3',
  'wpsc_reset_button_bg_color'       => '#FFFFFF',
  'wpsc_reset_button_text_color'     => '#000000',
  'wpsc_reset_button_border_color'   => '#C3C3C3',
  'wpsc_captcha_bg_color'            => '#B2BABB ',
  'wpsc_captcha_text_color'          => '#000000',
	'wpsc_extra_info_text_color'       => '#000000',
  
);

update_option('wpsc_create_ticket',$wpsc_appearance_create_ticket);

do_action('wpsc_reset_default_create_ticket_settings');

echo '{ "sucess_status":"1","messege":"'.__('Settings saved.','supportcandy').'" }';