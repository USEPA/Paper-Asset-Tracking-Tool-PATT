<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user,$wpscfunction;
if (!($current_user->ID && $current_user->has_cap('edit_published_posts'))) {
	exit;
}

// Attachment max filesize
$wpsc_attachment_max_filesize = isset($_POST) && isset($_POST['wpsc_attachment_max_filesize']) ? sanitize_text_field($_POST['wpsc_attachment_max_filesize']) : '20';
update_option('wpsc_attachment_max_filesize',$wpsc_attachment_max_filesize);

$wpsc_allow_attachment_type = isset($_POST) && isset($_POST['wpsc_allow_attachment_type']) ? sanitize_text_field($_POST['wpsc_allow_attachment_type']) : '';
update_option('wpsc_allow_attachment_type',$wpsc_allow_attachment_type);

$wpsc_allow_attach_create_ticket = isset($_POST) && isset($_POST['wpsc_allow_attach_create_ticket']) ? $wpscfunction->sanitize_array($_POST['wpsc_allow_attach_create_ticket']) : array();
update_option('wpsc_allow_attach_create_ticket',$wpsc_allow_attach_create_ticket);

$wpsc_allow_attach_reply_form = isset($_POST) && isset($_POST['wpsc_allow_attach_reply_form']) ? $wpscfunction->sanitize_array($_POST['wpsc_allow_attach_reply_form']) : array();
update_option('wpsc_allow_attach_reply_form',$wpsc_allow_attach_reply_form);

$wpsc_show_attachment_notice = isset($_POST) && isset($_POST['wpsc_show_attachment_notice']) ? sanitize_text_field($_POST['wpsc_show_attachment_notice']) : '';
update_option('wpsc_show_attachment_notice',$wpsc_show_attachment_notice);

$wpsc_image_download_method = isset($_POST) && isset($_POST['wpsc_image_download_method']) ? sanitize_text_field($_POST['wpsc_image_download_method']) : '';
update_option('wpsc_image_download_method',$wpsc_image_download_method);

$wpsc_attachment_notice = isset($_POST) && isset($_POST['wpsc_attachment_notice']) ? sanitize_text_field($_POST['wpsc_attachment_notice']) : '';
update_option('wpsc_attachment_notice',$wpsc_attachment_notice);

echo '{ "sucess_status":"1","messege":"'.__('Settings saved.','supportcandy').'" }';

