<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user;
if (!($current_user->ID && $current_user->has_cap('edit_published_posts'))) {
	exit;
}

$wpsc_appearance_modal_window = array (
  
  'wpsc_header_bg_color'          => '#0473AA',
  'wpsc_header_text_color'        => '#FFFFFF',
  'wpsc_footer_bg_color'          => '#F6F6F6',
  'wpsc_close_button_bg_color'    => '#AFAFAF',
  'wpsc_close_button_text_color'  => '#FFFFFF',
  'wpsc_action_button_bg_color'   => '#0473AA',
  'wpsc_action_button_text_color' => '#FFFFFF',

);

update_option('wpsc_modal_window',$wpsc_appearance_modal_window);

do_action('wpsc_reset_default_modal_window_settings');

echo '{ "sucess_status":"1","messege":"'.__('Settings saved.','supportcandy').'" }';