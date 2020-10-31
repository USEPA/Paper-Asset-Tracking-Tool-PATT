<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user;
if (!($current_user->ID && $current_user->has_cap('edit_published_posts'))) {
	exit;
}

$status_ids = isset($_POST) && isset($_POST['status_ids']) ? $_POST['status_ids'] : array();

foreach ($status_ids as $key => $status_id) {
	update_term_meta(intval($status_id), 'wpsc_status_load_order', intval($key));
}

echo '{ "sucess_status":"1","messege":"'.__('Status order saved.','supportcandy').'" }';
