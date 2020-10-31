<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user, $wpscfunction;

if (!$current_user->ID) die();

$key = isset($_POST['key']) ? sanitize_text_field($_POST['key']) : '';
if(!is_numeric($key)) die();

$blog_id = get_current_blog_id();
$saved_filters = get_user_meta($current_user->ID, $blog_id.'_wpsc_filter',true);
$saved_filters = $saved_filters ? $saved_filters : array();
if(!isset($saved_filters[$key])) die(); 

unset($saved_filters[$key]);
update_user_meta( $current_user->ID, $blog_id.'_wpsc_filter', $saved_filters );
