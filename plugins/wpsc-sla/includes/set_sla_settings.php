<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user;
if (!($current_user->ID && $current_user->has_cap('manage_options'))) {
	exit;
}

// SLA label
$label     = isset($_POST) && isset($_POST['wpsc_sla_label']) ? sanitize_text_field($_POST['wpsc_sla_label']) : __('SLA','wpsc-sla');
$sla_field = get_term_by('slug','sla','wpsc_ticket_custom_fields');
update_term_meta($sla_field->term_id,'wpsc_tf_label',$label);

// IN-SLA color
$in_sla_color = isset($_POST) && isset($_POST['wpsc_in_sla_color']) ? sanitize_text_field($_POST['wpsc_in_sla_color']) : '#5cb85c';
update_option('wpsc_in_sla_color',$in_sla_color);

// OUT-SLA color
$out_sla_color = isset($_POST) && isset($_POST['wpsc_out_sla_color']) ? sanitize_text_field($_POST['wpsc_out_sla_color']) : '#d9534f';
update_option('wpsc_out_sla_color',$out_sla_color);

do_action('wpsc_set_sla_settings');

echo '{ "sucess_status":"1","messege":"'.__('Settings saved.','wpsc').'" }';
