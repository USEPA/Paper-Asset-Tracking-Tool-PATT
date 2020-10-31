<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

$response = array();

$statuses = get_terms([
	'taxonomy'   => 'wpsc_statuses',
	'hide_empty' => false,
	'orderby'    => 'meta_value_num',
	'order'    	 => 'ASC',
	'meta_query' => array('order_clause' => array('key' => 'wpsc_status_load_order')),
]);

foreach ( $statuses as $status ) {
  
    $response[] = array(
      'id'              => $status->term_id,
      'name'            => $status->name,
      'textColor'       => get_term_meta( $status->term_id, 'wpsc_status_color', true),
      'backgroundColor' => get_term_meta( $status->term_id, 'wpsc_status_background_color', true),
    );
  
}
