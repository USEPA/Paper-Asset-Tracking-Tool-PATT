<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user;
if (!($current_user->ID && $current_user->has_cap('edit_published_posts'))) {exit;}

$cat_id = isset($_POST) && isset($_POST['cat_id']) ? intval($_POST['cat_id']) : 0;
if (!$cat_id) {exit;}

$cat_name = isset($_POST) && isset($_POST['cat_name']) ? sanitize_text_field($_POST['cat_name']) : '';
if (!$cat_name) {exit;}

wp_update_term($cat_id, 'wpsc_categories', array(
  'name' => $cat_name
));

do_action('wpsc_set_edit_category',$cat_id);
$wpsc_custom_category_localize =  get_option('wpsc_custom_category_localize');
$wpsc_custom_category_localize['custom_category_'.$cat_id] = $cat_name;
update_option('wpsc_custom_category_localize', $wpsc_custom_category_localize);