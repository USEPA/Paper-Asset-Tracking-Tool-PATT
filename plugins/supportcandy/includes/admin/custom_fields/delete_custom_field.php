<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user;
if (!($current_user->ID && $current_user->has_cap('edit_published_posts'))) {exit;}

$field_id = isset($_POST) && isset($_POST['field_id']) ? intval($_POST['field_id']) : 0;
if (!$field_id) {exit;}

$wpsc_tf_type = get_term_meta( $field_id, 'wpsc_tf_type', true);

if ($wpsc_tf_type!='0') {
	$custom_fields_localize = get_option('wpsc_custom_fields_localize');
  unset($custom_fields_localize['custom_fields_' .$field_id]);
  update_option('wpsc_custom_fields_localize', $custom_fields_localize);
	
	$custom_fields_extra_info = get_option('wpsc_custom_fields_extra_info');
	unset($custom_fields_extra_info['custom_fields_extra_info_' .$field_id]);
	update_option('wpsc_custom_fields_extra_info', $custom_fields_extra_info);
	
	do_action('wpsc_delete_custom_field',$field_id);
	wp_delete_term($field_id, 'wpsc_ticket_custom_fields');
	
	echo '{ "sucess_status":"1","messege":"'.__('Field deleted successfully.','supportcandy').'" }';
} else {
	echo '{ "sucess_status":"0","messege":"'.__('Default field can not be deleted.','supportcandy').'" }';
}
