<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

global $wpdb, $current_user, $wpscfunction;

$params        = $request->get_params();
$current_page  = isset($params['current_page']) ? intval($params['current_page']) : 0;
$no_of_threads = isset($params['no_of_threads']) ? intval($params['no_of_threads']) : 20;
$historyId     = isset($params['historyId']) ? intval($params['historyId']) : 0;

if (!$historyId) {
  $response = new WP_Error(
      'invalid_api_parameters',
      'Invalid api parameters provided.',
      array(
          'status' => 403,
      )
  );
  return;
}


/**
 * Now we need only tickets that current user is allowed to read
 */

$meta_query = array(
	'relation' => 'AND',
);

if ( !is_multisite() || !is_super_admin($current_user->ID) ) {
  $meta_query[] = SELF::get_tl_meta_query_restrict_rules();
}

$meta_query = array_merge($meta_query,$wpscfunction->get_default_filter_query('all'));

$meta_query[] = array(
	'key'     => 'active',
	'value'   => 1,
	'compare' => '='
);

$meta_query[] = array(
	'key'     => 'historyId',
	'value'   => $historyId,
	'compare' => '>'
);

$select_str  = 'DISTINCT t.id';
$sql         = $wpscfunction->get_sql_query( $select_str, $meta_query );
$tickets     = $wpdb->get_results($sql);

$ticket_ids = array();
foreach ($tickets as $ticket) {
  $ticket_ids[] = $ticket->id;
}


/**
 * Up to this point we have ticekt ids to which user has access to.
 * Now we need to get thread ids greater than $histortId of which ticket_id falls in $ticket_ids
 * This way we get only those threds to which user has access. This includes all threads like report, reply, private notes, logs, etc. no matter which user is logged in.
 */
if ($ticket_ids) {
  
    $args = array(
      'post_type'      => 'wpsc_ticket_thread',
      'post_status'    => 'publish',
      'orderby'        => 'ID',
      'order'          => 'ASC',
      'posts_per_page' => $no_of_threads,
      'paged'          => $current_page,
      'meta_query'     => array(
        'relation' => 'AND',
        array(
          'key'     => 'ticket_id',
          'value'   => $ticket_ids,
          'compare' => 'IN',
        )
      ),
    );

    $filter_handler = function( $where = '' )  use ( $historyId ) {
        
        global $wpdb;
        return $where . " AND {$wpdb->posts}.ID > $historyId";
        
    };

    add_filter( 'posts_where', $filter_handler );
    $threads = new WP_Query($args);
    remove_filter( 'posts_where', $filter_handler );

    $total_items = $threads->found_posts;
    $total_pages = ceil($total_items/$no_of_threads);
    $is_next     = $current_page < $total_pages ? 1 : 0;
    $page_items  = $threads->post_count;
  
} else {
  
    $total_items = 0;
    $total_pages = 1;
    $is_next     = 0;
    $page_items  = 0;
  
}


$response = array(
  'total_items'        => $total_items,
  'total_pages'        => $total_pages,
  'current_page'       => $current_page,
  'current_page_items' => $page_items,
  'is_next_page'       => $is_next,
  'next_page'          => $is_next ? $current_page+1 : 0,
);

$threads_response = array();

foreach ( $threads->posts as $thread ) {
  $threads_response[] = SELF::get_thread_response($thread);
}

$response['threads'] = $threads_response;

