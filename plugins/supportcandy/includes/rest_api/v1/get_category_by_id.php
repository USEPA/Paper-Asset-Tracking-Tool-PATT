<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

$params   = $request->get_params();
$cat_id   = isset( $params['id'] ) ? $params['id'] : 0;
$category = get_term_by('id', $cat_id, 'wpsc_categories');
if ($category) {
  $response = array(
    'id'   => $category->term_id,
    'name' => $category->name,
  );
} else {
  $response = new WP_Error(
      'not_found',
      'Categoty not found for id '.$cat_id,
      array(
          'status' => 404,
      )
  );
}
