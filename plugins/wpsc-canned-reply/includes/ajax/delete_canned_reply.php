<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user, $wpscfunction;

$post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;

if ( !$post_id || !$current_user->ID || !$current_user->has_cap('wpsc_agent') ) die();

$author = get_post_meta($post_id,'wpsc_author',true);

if($current_user->ID == $author){
	wp_delete_post( $post_id );
}
