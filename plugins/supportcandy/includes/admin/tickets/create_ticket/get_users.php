<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user, $wpdb;
if (!($current_user->ID && $current_user->has_cap('wpsc_agent'))) {exit;}

$term = isset($_REQUEST) && isset($_REQUEST['term']) ? sanitize_text_field($_REQUEST['term']) : '';
if (!$term) {exit;}

$output = array();

$args =  array ( 
    'meta_query' => array(
    'relation' => 'OR',
    array(
        'key'     => 'first_name',
        'value'   => $term,
        'compare' => 'LIKE'
    ),
    array(
        'key'     => 'last_name',
        'value'   => $term,
        'compare' => 'LIKE'
    )
    )
  );
$wp_user_query = new WP_User_Query($args);
$usermeta = $wp_user_query->get_results();

$users = get_users(array('search'=>'*'.$term.'*','number' => 5));
$users_dup = array_merge($users,$usermeta);
$users = array_unique($users_dup, SORT_REGULAR);

foreach ($users as $user) {
	$output[] = array(
    'id' => $user->ID,
    'label' => $user->display_name,
    'value' => $user->display_name,
    'email' => $user->user_email,
  );
}

if (!$output) {
  $output[] = array(
    'id' => '',
    'label' => __('No matching users','supportcandy'),
    'value' => '',
    'email' => '',
  );
}

echo json_encode($output);
