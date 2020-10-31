<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user, $wpscfunction;

if (!$current_user->ID) die();

$filter = $wpscfunction->get_current_filter();
$filter['custom_filter'] = isset($_POST['custom_filter']) && is_array($_POST['custom_filter']) ? $wpscfunction->sanitize_array($_POST['custom_filter']) : array();
$filter['page'] = isset($_POST['page_no']) ? intval($_POST['page_no']) : 1;

$orderby = isset($_POST['orderby']) ? sanitize_text_field($_POST['orderby']) : '';
if($orderby){
	$filter['orderby'] = $orderby;
}

$order = isset($_POST['order']) ? sanitize_text_field($_POST['order']) : '';
if($order){
	$filter['order'] = $order;
}

setcookie('wpsc_ticket_filter',json_encode($filter));
