<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user;
if (!($current_user->ID && $current_user->has_cap('edit_published_posts'))) {
	exit;
}

$priority_ids = isset($_POST) && isset($_POST['priority_ids']) ? $_POST['priority_ids'] : array();

foreach ($priority_ids as $key => $priority_id) {
	update_term_meta(intval($priority_id), 'wpsc_priority_load_order', intval($key));
}

echo '{ "sucess_status":"1","messege":"'.__('Priority order saved.','supportcandy').'" }';
