<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user;
if (!($current_user->ID && $current_user->has_cap('edit_published_posts'))) {
	exit;
}

$cat_ids = isset($_POST) && isset($_POST['cat_ids']) ? $_POST['cat_ids'] : array();

foreach ($cat_ids as $key => $cat_id) {
	update_term_meta(intval($cat_id), 'wpsc_category_load_order', intval($key));
}

echo '{ "sucess_status":"1","messege":"'.__('Category order saved.','supportcandy').'" }';
