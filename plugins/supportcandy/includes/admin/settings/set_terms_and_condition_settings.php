<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user;
if (!($current_user->ID && $current_user->has_cap('edit_published_posts'))) {
	exit;
}

// Allow terms and condition
$wpsc_terms_and_conditions = isset($_POST) && isset($_POST['wpsc_terms_and_conditions']) ? sanitize_text_field($_POST['wpsc_terms_and_conditions']) : '0';
update_option('wpsc_terms_and_conditions',$wpsc_terms_and_conditions);

//Terms and Condition Message
$wpsc_terms_and_conditions_html = isset($_POST) && isset($_POST['wpsc_terms_and_conditions_html']) ? wp_kses_post(htmlspecialchars_decode($_POST['wpsc_terms_and_conditions_html'], ENT_QUOTES)) : '';
update_option('wpsc_terms_and_conditions_html',$wpsc_terms_and_conditions_html);

// Allow GDPR
$wpsc_gdpr_settings = isset($_POST) && isset($_POST['wpsc_gdpr_settings']) ? sanitize_text_field($_POST['wpsc_gdpr_settings']) : '0';
update_option('wpsc_set_in_gdpr',$wpsc_gdpr_settings);

// GDPR messege
$wpsc_gdpr_html = isset($_POST) && isset($_POST['wpsc_gdpr_html']) ? wp_kses_post(htmlspecialchars_decode($_POST['wpsc_gdpr_html'], ENT_QUOTES)) : '';
update_option('wpsc_gdpr_html',$wpsc_gdpr_html);

// Personal Data Retention Period
$wpsc_personal_data_retention_type = isset($_POST) && isset($_POST['wpsc_personal_data_retention_type']) ? sanitize_text_field($_POST['wpsc_personal_data_retention_type']) : '';
update_option('wpsc_personal_data_retention_type',$wpsc_personal_data_retention_type);

$wpsc_personal_data_retention_period_time = isset($_POST) && isset($_POST['wpsc_personal_data_retention_period_time']) ? intval($_POST['wpsc_personal_data_retention_period_time']) : 0;
update_option('wpsc_personal_data_retention_period_time',$wpsc_personal_data_retention_period_time);

$wpsc_personal_data_retention_period_unit = isset($_POST) && isset($_POST['wpsc_personal_data_retention_period_unit']) ? sanitize_text_field($_POST['wpsc_personal_data_retention_period_unit']) : '';
update_option('wpsc_personal_data_retention_period_unit',$wpsc_personal_data_retention_period_unit);


do_action('wpsc_set_terms_and_condition_settings');

echo '{ "sucess_status":"1","messege":"'.__('Settings saved.','supportcandy').'" }';