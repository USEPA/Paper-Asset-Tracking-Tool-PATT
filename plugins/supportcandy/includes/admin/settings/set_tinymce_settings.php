<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user,$wpscfunction;
if (!($current_user->ID && $current_user->has_cap('edit_published_posts'))) {
	exit;
}

// Allow Tinymce
$wpsc_allow_rich_text_editor = isset($_POST) && isset($_POST['wpsc_allow_rte']) ? $wpscfunction->sanitize_array($_POST['wpsc_allow_rte']) : array();
update_option('wpsc_allow_rich_text_editor',$wpsc_allow_rich_text_editor);

// Toolbar active
$wpsc_tinymce_toolbar_active = isset($_POST) && isset($_POST['wpsc_tinymce_toolbar']) ? $wpscfunction->sanitize_array($_POST['wpsc_tinymce_toolbar']) : array();
update_option('wpsc_tinymce_toolbar_active',$wpsc_tinymce_toolbar_active);

$wpsc_allow_html_pasting = isset($_POST) && isset($_POST['wpsc_html_pasting']) ? sanitize_text_field($_POST['wpsc_html_pasting']) : '0';
update_option('wpsc_allow_html_pasting',$wpsc_allow_html_pasting);

do_action('wpsc_set_tinymce_settings');

echo '{ "sucess_status":"1","messege":"'.__('Settings saved.','supportcandy').'" }';