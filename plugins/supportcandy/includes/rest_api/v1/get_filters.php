<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

global $current_user, $wpscfunction;

$default_filters = array();
$labels = $wpscfunction->get_ticket_filter_labels();

foreach ($labels as $key => $label) {
  if ( ($current_user->has_cap('wpsc_agent') && $label['visibility']=='agent') || (!$current_user->has_cap('wpsc_agent') && $label['visibility']=='customer') || $label['visibility']=='both' ) {
    $default_filters[] = array(
      'filter_key'   => $key,
      'filter_label' => $label['label'],
    );
  }
}

$custom_filters = array();
$blog_id        = get_current_blog_id();
$saved_filters  = get_user_meta($current_user->ID, $blog_id.'_wpsc_filter',true);
$saved_filters  = $saved_filters ? $saved_filters : array();

foreach ($saved_filters as $key => $label) {
  $custom_filters[] = array(
    'filter_key'   => intval($key)+1,
    'filter_label' => $label['save_label'],
  );
}

$response = array(
  'default_filters' => $default_filters,
  'custom_filters'  => $custom_filters,
);
