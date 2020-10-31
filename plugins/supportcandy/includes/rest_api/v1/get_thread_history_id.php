<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

global $current_user, $wpdb;

$history_id = $wpdb->get_var("SELECT max(ID) from {$wpdb->prefix}posts WHERE post_type='wpsc_ticket_thread'");

$response = array(
  'historyId' => $history_id
);
