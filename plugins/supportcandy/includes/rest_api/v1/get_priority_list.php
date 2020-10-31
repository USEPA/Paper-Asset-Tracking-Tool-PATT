<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

$response = array();

$priorities = get_terms([
	'taxonomy'   => 'wpsc_priorities',
	'hide_empty' => false,
	'orderby'    => 'meta_value_num',
	'order'    	 => 'ASC',
	'meta_query' => array('order_clause' => array('key' => 'wpsc_priority_load_order')),
]);

foreach ( $priorities as $priority ) {
  
    $response[] = array(
      'id'              => $priority->term_id,
      'name'            => $priority->name,
      'textColor'       => get_term_meta( $priority->term_id, 'wpsc_priority_color', true),
      'backgroundColor' => get_term_meta( $priority->term_id, 'wpsc_priority_background_color', true),
    );
  
}
