<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user;
if (!($current_user->ID && $current_user->has_cap('edit_published_posts'))) {
	exit;
}

$field_ids = isset($_POST) && isset($_POST['field_ids']) ? $_POST['field_ids'] : array();

foreach ($field_ids as $key => $field_id) {
	update_term_meta(intval($field_id), 'wpsc_tl_customer_load_order', intval($key));
}

echo '{ "sucess_status":"1","messege":"'.__('List order saved.','supportcandy').'" }';
