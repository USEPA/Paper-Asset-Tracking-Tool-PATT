<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

global $wpdb, $current_user, $wpscfunction;

$params        = $request->get_params();
$current_page  = isset($params['current_page']) ? intval($params['current_page']) : 0;
$no_of_tickets = isset($params['number_of_tickets']) ? intval($params['number_of_tickets']) : 20;
$orderby       = isset($params['orderby']) ? sanitize_text_field($params['orderby']) : '';
$order         = isset($params['order']) ? sanitize_text_field($params['order']) : '';
$filter_key    = isset($params['filter_key']) ? sanitize_text_field($params['filter_key']) : '';
$search        = isset($params['search']) ? sanitize_text_field($params['search']) : '';

$filter = $wpscfunction->get_default_filter();

if ( $filter_key && is_numeric($filter_key) ) {
    
    $filter_key    = $filter_key-1;
    $blog_id       = get_current_blog_id();
    $saved_filters = get_user_meta($current_user->ID, $blog_id.'_wpsc_filter',true);
    if(isset($saved_filters[$filter_key])){
      $filter = $saved_filters[$filter_key];
    }
  
} else if( $filter_key && !is_numeric($filter_key) ){
    
    $filter['label'] = $filter_key;
    $filter['query'] = $wpscfunction->get_default_filter_query($filter_key);
    
} else {
  
    $filter['query'] = $wpscfunction->get_default_filter_query($filter['label']);
  
}

if($orderby) $filter['orderby'] = $orderby;
if($order) $filter['order'] = $order;
if($current_page) $filter['page'] = $current_page;
if($search) $filter['custom_filter']['s'] = $search;

// Initialize meta query
$meta_query = array(
	'relation' => 'AND',
);

if ( !is_multisite() || !is_super_admin($current_user->ID) ) {
  $meta_query[] = SELF::get_tl_meta_query_restrict_rules();
}

// Merge default filter label
if($filter['query']){
	$meta_query = array_merge($meta_query, $filter['query']);
}

// Custom Filter
foreach ($filter['custom_filter'] as $slug => $filterVal) {
  
    if ( $slug == 's' ) continue;
    if ( isset($filterVal['from']) && $filterVal['from']=='' ) continue;
    
    if ( isset($filterVal['from']) ) {
      
        $meta_query[] = array(
          'key'     => $slug,
          'value'   => array( 
            get_date_from_gmt($wpscfunction->calenderDateFormatToDateTime($filterVal['from'])),
            get_date_from_gmt($wpscfunction->calenderDateFormatToDateTime($filterVal['to'])),
          ),
          'compare' => 'BETWEEN',
          'type' => 'DATE',
        );
      
    } else {
      
      $meta_query[] = array(
        'key'     => $slug,
        'value'   => $filterVal,
        'compare' => 'IN',
      );
      
    }
  
}

// Select offset for page number
$offset = ($filter['page']-1)*$no_of_tickets;

if($filter['orderby'] == 'ticket_id'){
	$filter['orderby'] = 'id';
}

$active = 1;
if ($filter['label'] == 'deleted') {
	$active = 0;
}

$meta_query[] = array(
	'key'     => 'active',
	'value'   => $active,
	'compare' => '='
);

$select_str  = 'SQL_CALC_FOUND_ROWS DISTINCT t.id';
$sql         = $wpscfunction->get_sql_query( $select_str, $meta_query, $filter['custom_filter']['s'], $filter['orderby'], $filter['order'], $no_of_tickets, $filter['page'] );
$tickets     = $wpdb->get_results($sql);
$total_items = intval($wpdb->get_var("SELECT FOUND_ROWS()"));
$total_pages = ceil($total_items/$no_of_tickets);
$is_next     = $filter['page'] < $total_pages ? 1 : 0;

$response = array(
  'total_items'        => $total_items,
  'total_pages'        => $total_pages,
  'current_page'       => $filter['page'],
  'current_page_items' => count($tickets),
  'is_next_page'       => $is_next,
  'next_page'          => $is_next ? $filter['page']+1 : 0,
);

$ticket_response = array();

foreach ($tickets as $ticket) {
  $ticket_response[] = SELF::get_ticket_response($ticket->id);
}

$response['tickets'] = $ticket_response;
