<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user;
if (!($current_user->ID && $current_user->has_cap('edit_published_posts'))) {exit;}

$cat_id = isset($_POST) && isset($_POST['cat_id']) ? intval($_POST['cat_id']) : 0;
if (!$cat_id) {exit;}

$wpsc_default_ticket_category = get_option('wpsc_default_ticket_category');

if ($wpsc_default_ticket_category!=$cat_id) {
	$wpsc_custom_category_localize =  get_option('wpsc_custom_category_localize');
	unset($wpsc_custom_category_localize['custom_category_'.$cat_id]);
	update_option('wpsc_custom_category_localize', $wpsc_custom_category_localize);
	
	wp_delete_term($cat_id, 'wpsc_categories');
	do_action('wpsc_delete_category',$cat_id);
	echo '{ "sucess_status":"1","messege":"'.__('Category deleted successfully.','supportcandy').'" }';
} else {
	echo '{ "sucess_status":"0","messege":"'.__('Default category can not be deleted.','supportcandy').'" }';
}
