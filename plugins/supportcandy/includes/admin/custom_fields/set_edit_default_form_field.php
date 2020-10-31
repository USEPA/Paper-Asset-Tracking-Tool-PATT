<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user, $wpdb, $wpscfunction;
if (!($current_user->ID && $current_user->has_cap('edit_published_posts'))) {exit;}

$field_id = isset($_POST) && isset($_POST['field_id']) ? intval($_POST['field_id']) : 0;
if (!$field_id) {exit;}

$field_label = isset($_POST) && isset($_POST['field_label']) ? sanitize_text_field($_POST['field_label']) : '';
if (!$field_label) {exit;}

$extra_info = isset($_POST) && isset($_POST['extra_info']) ? sanitize_text_field($_POST['extra_info']) : '';
$width = isset($_POST) && isset($_POST['width']) ? sanitize_text_field($_POST['width']) : '1';
$status = isset($_POST) && isset($_POST['status']) ? sanitize_text_field($_POST['status']) : '1';
$wpsc_limit = isset($_POST) && isset($_POST['limit'])  && $_POST['limit'] ? sanitize_text_field($_POST['limit']) : '0';
$wpsc_tf_placeholder = isset($_POST) && isset($_POST['wpsc_tf_placeholder_text']) ? sanitize_text_field($_POST['wpsc_tf_placeholder_text']) : '';
$visibility = isset($_POST) && isset($_POST['visibility']) ? $_POST['visibility'] : array();
foreach ($visibility as $key => $value) {
	$visibility[$key] = sanitize_text_field($value);
}
$field_label = stripslashes($field_label);

update_term_meta ($field_id, 'wpsc_tf_label', $field_label);
update_term_meta ($field_id, 'wpsc_tf_extra_info', $extra_info);
update_term_meta ($field_id, 'wpsc_tf_width', $width);
update_term_meta ($field_id, 'wpsc_tf_status', $status);
update_term_meta ($field_id, 'wpsc_tf_limit', $wpsc_limit);
update_term_meta($field_id,  'wpsc_tf_placeholder_text',$wpsc_tf_placeholder);
update_term_meta ($field_id, 'wpsc_tf_visibility', $visibility);
if(isset($_POST) && isset($_POST['default_sub']) && $_POST['default_sub']){
	$default_subject = isset($_POST) && isset($_POST['default_sub']) ? sanitize_text_field($_POST['default_sub']) : '';
	update_term_meta ($field_id, 'wpsc_tf_default_subject', $default_subject);
}else{
	update_term_meta ($field_id, 'wpsc_tf_default_subject', 'NA');
}
if(isset($_POST) && isset($_POST['default_desc']) && $_POST['default_desc']){
	$default_desc = isset($_POST) && isset($_POST['default_desc']) ? sanitize_text_field($_POST['default_desc']) : '';
	update_term_meta ($field_id, 'wpsc_tf_default_description', $default_desc);
}else{
	update_term_meta ($field_id, 'wpsc_tf_default_description', 'NA');
}

$custom_fields_localize = get_option('wpsc_custom_fields_localize');
if (!$custom_fields_localize) {
    $custom_fields_localize = array();
}
$custom_fields_localize['custom_fields_'.$field_id] = $field_label;
update_option('wpsc_custom_fields_localize', $custom_fields_localize);

$custom_fields_extra_info = get_option('wpsc_custom_fields_extra_info');
if (!$custom_fields_extra_info) {
   $custom_fields_extra_info = array();
}
$custom_fields_extra_info['custom_fields_extra_info_' . $field_id] = $extra_info;
update_option('wpsc_custom_fields_extra_info', $custom_fields_extra_info);

do_action('set_edit_default_form_field',$field_id);

echo '{ "sucess_status":"1","messege":"'.__('updated successfully.','supportcandy').'" }';
