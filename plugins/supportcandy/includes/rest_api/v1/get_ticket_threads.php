<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

global $wpdb, $current_user, $wpscfunction;

$params        = $request->get_params();
$current_page  = isset($params['current_page']) ? intval($params['current_page']) : 0;
$no_of_threads = isset($params['no_of_threads']) ? intval($params['no_of_threads']) : 20;
$order         = isset($params['order']) ? sanitize_text_field($params['order']) : 'DESC';
$ticket_id     = isset($params['id']) ? intval($params['id']) : 0;

$args = array(
  'post_type'      => 'wpsc_ticket_thread',
  'post_status'    => 'publish',
  'orderby'        => 'ID',
  'order'          => $order,
  'posts_per_page' => $no_of_threads,
  'paged'          => $current_page,
);

$meta_query = array(
  'relation' => 'AND',
  array(
    'key'     => 'ticket_id',
    'value'   => $ticket_id,
    'compare' => '=',
  )
);

if ( $current_user->has_cap('wpsc_agent') ) {
  
    $meta_query[] = array(
      'key'     => 'thread_type',
      'value'   => array('report','reply','note','log'),
      'compare' => 'IN'
    );
  
} else {
  
    $meta_query[] = array(
      'key'     => 'thread_type',
      'value'   => array('report','reply'),
      'compare' => 'IN'
    );
  
}

$args['meta_query'] = $meta_query;

$threads = new WP_Query($args);

$total_items = $threads->found_posts;
$total_pages = ceil($total_items/$no_of_threads);
$is_next     = $current_page < $total_pages ? 1 : 0;

$response = array(
  'total_items'        => $total_items,
  'total_pages'        => $total_pages,
  'current_page'       => $current_page,
  'current_page_items' => $threads->post_count,
  'is_next_page'       => $is_next,
  'next_page'          => $is_next ? $current_page+1 : 0,
);

$threads_response = array();

foreach ( $threads->posts as $thread ) {
  $threads_response[] = SELF::get_thread_response($thread);
}

$response['threads'] = $threads_response;
