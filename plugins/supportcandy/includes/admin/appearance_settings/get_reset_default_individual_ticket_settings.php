<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user;
if (!($current_user->ID && $current_user->has_cap('edit_published_posts'))) {
	exit;
}

$wpsc_appearance_individual_ticket_page = array (
  
    'wpsc_ticket_widgets_bg_color'                   => '#FFFFFF',
    'wpsc_ticket_widgets_text_color'                 => '#000000',
    'wpsc_ticket_widgets_border_color'               => '#C3C3C3',
    'wpsc_report_thread_bg_color'                    => '#FFFFFF',
    'wpsc_report_thread_text_color'                  => '#000000',
    'wpsc_report_thread_border_color'                => '#C3C3C3',
		'wpsc_reply_thread_customer_bg_color'						 => '#FFFFFF',
    'wpsc_reply_thread_customer_text_color'		       => '#000000',
    'wpsc_reply_thread_customer_border_color'	       => '#C3C3C3',
    'wpsc_reply_thread_bg_color'                     => '#FFFFFF',
    'wpsc_reply_thread_text_color'                   => '#000000',
    'wpsc_reply_thread_border_color'                 => '#C3C3C3',
    'wpsc_private_note_bg_color'                     => '#FEF9E7',
    'wpsc_private_note_text_color'                   => '#000000',
    'wpsc_private_note_border_color'                 => '#C3C3C3',
    'wpsc_ticket_logs_bg_color'                      => '#D6EAF8',
    'wpsc_ticket_logs_text_color'                    => '#000000',
    'wpsc_ticket_logs_border_color'                  => '#C3C3C3',
    'wpsc_submit_reply_btn_bg_color'                 => '#419641',
    'wpsc_submit_reply_btn_text_color'               => '#FFFFFF',
    'wpsc_submit_reply_btn_border_color'             => '#C3C3C3',
    'wpsc_other_reply_form_btn_bg_color'             => '#FFFFFF',
    'wpsc_other_reply_form_btn_text_color'           => '#000000',
    'wpsc_other_reply_form_btn_border_color'         => '#C3C3C3',
		'wpsc_edit_btn_bg_color'                         => '#FFFFFF',
    'wpsc_edit_btn_text_color'                       => '#000000',
    'wpsc_edit_btn_border_color'                     => '#C3C3C3',
);

update_option('wpsc_individual_ticket_page',$wpsc_appearance_individual_ticket_page);

do_action('wpsc_reset_default_individual_ticket_settings');

echo '{ "sucess_status":"1","messege":"'.__('Settings saved.','supportcandy').'" }';