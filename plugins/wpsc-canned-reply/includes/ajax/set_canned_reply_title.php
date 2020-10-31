<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
 
global $wpscfunction, $current_user;
if (!($current_user->ID && $current_user->has_cap('wpsc_agent'))) {exit;}

$description  = isset($_POST['description']) ? wp_kses_post($_POST['description']) : '';
if (!$description) {exit;}

$canned_reply_title  = isset($_POST['canned_reply_title']) ? sanitize_text_field($_POST['canned_reply_title']) : '' ;
if (!$canned_reply_title) {exit;}

$category_id = isset($_POST['category']) ? intval($_POST['category']) : 0 ;

$post_id = wp_insert_post(array(
	'post_title'    => $canned_reply_title,
  'post_content'  => $description,
  'post_status'   => 'publish',
	'post_type'			=> 'wpsc_canned_reply'
));

if($post_id){
	wp_set_post_terms( $post_id, array($category_id),'wpsc_canned_reply_categories');
	add_post_meta( $post_id, 'wpsc_agent_visibility', 0 );
	add_post_meta( $post_id, 'wpsc_author', $current_user->ID );
}
