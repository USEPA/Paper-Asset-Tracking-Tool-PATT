<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user;
if (!($current_user->ID && $current_user->has_cap('edit_published_posts'))) {
	exit;
}

$wpsc_appearance_ticket_list = array (
	
	'wpsc_filter_widgets_bg_color'          => '#FFFFFF',
	'wpsc_filter_widgets_text_color'        => '#2C3E50',
	'wpsc_filter_widgets_border_color'      => '#C3C3C3',
	'wpsc_ticket_list_header_bg_color'      => '#424949',
	'wpsc_ticket_list_header_text_color'    => '#FFFFFF',
	'wpsc_ticket_list_item_mo_bg_color'     => '#FFFFFF',
	'wpsc_ticket_list_item_mo_text_color'   => '#2C3E50',
	'wpsc_apply_filter_btn_bg_color'        => '#419641',
	'wpsc_apply_filter_btn_text_color'      => '#FFFFFF',
	'wpsc_apply_filter_btn_border_color'    => '#C3C3C3',
	'wpsc_save_filter_btn_bg_color'         => '#FFFFFF',
	'wpsc_save_filter_btn_text_color'       => '#000000',
	'wpsc_save_filter_btn_border_color'     => '#C3C3C3',
	'wpsc_close_filter_btn_bg_color'        => '#FFFFFF',
	'wpsc_close_filter_btn_text_color'      => '#000000',
	'wpsc_close_filter_btn_border_color'    => '#C3C3C3',
);

update_option('wpsc_appearance_ticket_list',$wpsc_appearance_ticket_list);

do_action('wpsc_reset_default_create_ticket_settings');

echo '{ "sucess_status":"1","messege":"'.__('Settings saved.','supportcandy').'" }';