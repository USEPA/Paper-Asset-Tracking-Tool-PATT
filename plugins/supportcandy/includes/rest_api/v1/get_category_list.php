<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

$response = array();

$categories = get_terms([
	'taxonomy'   => 'wpsc_categories',
	'hide_empty' => false,
	'orderby'    => 'meta_value_num',
	'order'    	 => 'ASC',
	'meta_query' => array('order_clause' => array('key' => 'wpsc_category_load_order')),
]);

foreach ( $categories as $category ) {
  
    $response[] = array(
      'id'   => $category->term_id,
      'name' => $category->name,
    );
  
}
