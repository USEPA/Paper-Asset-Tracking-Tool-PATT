<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $current_user, $wpscfunction;

if (!$current_user->ID) die();

$blog_id = get_current_blog_id();

$filter_lable = isset($_POST['filter_name']) ? sanitize_text_field($_POST['filter_name']) : '';
if (!$filter_lable) die();

$filter = $wpscfunction->get_current_filter();
$filter['custom_filter'] = isset($_POST['custom_filter']) && is_array($_POST['custom_filter']) ? $wpscfunction->sanitize_array($_POST['custom_filter']) : array();
$filter['page'] = 1;

$orderby = isset($_POST['orderby']) ? sanitize_text_field($_POST['orderby']) : '';
if($orderby){
	$filter['orderby'] = $orderby;
}

$order = isset($_POST['order']) ? sanitize_text_field($_POST['order']) : '';
if($order){
	$filter['order'] = $order;	
}

setcookie('wpsc_ticket_filter',json_encode($filter));

$filter['save_label']= $filter_lable;

$saved_filters = get_user_meta($current_user->ID, $blog_id.'_wpsc_filter',true);
$saved_filters = $saved_filters ? $saved_filters : array();
$saved_filters[] = $filter;

update_user_meta( $current_user->ID, $blog_id.'_wpsc_filter', $saved_filters );
