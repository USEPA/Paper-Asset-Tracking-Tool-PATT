<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user, $wpdb;
if (!($current_user->ID && $current_user->has_cap('edit_published_posts'))) {exit;}

$cat_name = isset($_POST) && isset($_POST['cat_name']) ? sanitize_text_field($_POST['cat_name']) : '';
if (!$cat_name) {exit;}

$flag = 0;

$term = wp_insert_term( $cat_name, 'wpsc_categories' );
if (!is_wp_error($term) && isset($term['term_id'])) {
  $load_order = $wpdb->get_var("select max(meta_value) as load_order from {$wpdb->prefix}termmeta WHERE meta_key='wpsc_category_load_order'");
  add_term_meta ($term['term_id'], 'wpsc_category_load_order', ++$load_order);
	
	$wpsc_custom_category_localize =  get_option('wpsc_custom_category_localize');
	if(!$wpsc_custom_category_localize){
		$wpsc_custom_category_localize = array();
	}
	$wpsc_custom_category_localize['custom_category_'.$term['term_id']] = $cat_name;
	update_option('wpsc_custom_category_localize', $wpsc_custom_category_localize);
	
	do_action('wpsc_set_add_category',$term['term_id']);
	echo '{ "sucess_status":"1","messege":"'.__('Category added successfully.','supportcandy').'" }';
} else {
	echo '{ "sucess_status":"0","messege":"'.__('An error occured while creating category.','supportcandy').'" }';
}
