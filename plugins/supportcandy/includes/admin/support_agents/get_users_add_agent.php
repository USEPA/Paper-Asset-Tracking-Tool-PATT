<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user, $wpdb;
if (!($current_user->ID && $current_user->has_cap('edit_published_posts'))) {exit;}

$term = isset($_REQUEST) && isset($_REQUEST['term']) ? sanitize_text_field($_REQUEST['term']) : '';

$output = array();

$users = get_users(array('search'=>'*'.$term.'*','number' => 5));
foreach ($users as $user) {
  $output[] = array(
    'id' => $user->ID,
    'label' => $user->display_name,
    'value' => $user->display_name,
  );
}

if (!$output) {
  $output[] = array(
    'id' => '',
    'label' => __('No matching users','supportcandy'),
    'value' => '',
  );
}

echo json_encode($output);
