<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user, $wpscfunction;

if (!$current_user->ID) die();

$blog_id     = get_current_blog_id();
$user_filter = get_user_meta($current_user->ID, $blog_id.'_wpsc_filter',true);
$filter      = $wpscfunction->get_current_filter();

$filter_key              = isset($_POST['wpsc_edit_filter']) ? sanitize_text_field($_POST['wpsc_edit_filter']) : '';
$filter['custom_filter'] = isset($_POST['custom_filter']) && is_array($_POST['custom_filter']) ? $wpscfunction->sanitize_array($_POST['custom_filter']) : array();
$filter['page']          = isset($_POST['page_no']) ? intval($_POST['page_no']) : 1;

$orderby = isset($_POST['orderby']) ? sanitize_text_field($_POST['orderby']) : '';

if($orderby){
	$filter['orderby'] = $orderby;
}

$order         = isset($_POST['order']) ? sanitize_text_field($_POST['order']) : '';
$filter_label  = isset($_POST['filter_save_label']) ? sanitize_text_field($_POST['filter_save_label']) : '';
$filter_search = isset($_POST['wpsc_custom_filter']) ? sanitize_text_field($_POST['wpsc_custom_filter']) : '';

if($order){
	$filter['order'] = $order;
}

if($filter_label){
	$filter['save_label'] = $filter_label;
}

$filter['custom_filter']['s'] = $filter_search;

foreach($user_filter as $key_ =>$val){
	
	if($key_ == $filter_key){
		$user_filter[$key_] = $filter;
	}
}

update_user_meta( $current_user->ID, $blog_id.'_wpsc_filter', $user_filter );

setcookie('wpsc_ticket_filter',json_encode($filter));


