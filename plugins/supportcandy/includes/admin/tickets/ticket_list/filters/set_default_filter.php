<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
// set default filter for agents and customers
global $current_user, $wpscfunction;

if (!$current_user->ID) die();

$label_key = isset($_POST['label']) ? sanitize_text_field($_POST['label']) : '';
//if (!$label_key) die();

$labels = $wpscfunction->get_ticket_filter_labels();

$filter = $wpscfunction->get_default_filter();

// check current user is agent and it has default filter is set or not 
if (is_numeric($label_key) || $label_key == 0 && !is_string($label_key) ) {
    $blog_id       = get_current_blog_id();
    $saved_filters = get_user_meta($current_user->ID, $blog_id . '_wpsc_filter', true);
    $saved_filters = $saved_filters ? $saved_filters : array();
    $filter        = $saved_filters[$label_key];
} else {
    $filter['label'] = $label_key;
    $filter['query'] = $wpscfunction->get_default_filter_query($label_key);
}

$filter = apply_filters('ticket_filter_the_filter',$filter, $order_key, $order); // PATT BEGIN - Location Filtering, allows sidebar filters - PATT END

setcookie('wpsc_ticket_filter',json_encode($filter));
