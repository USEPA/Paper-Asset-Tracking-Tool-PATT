<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user, $wpscfunction ,$wp;
if (!($current_user->ID && $current_user->has_cap('wpsc_agent'))) {
	exit;
}

$ticket_id  = isset($_POST['ticket_id']) ? sanitize_text_field($_POST['ticket_id']) : 0 ;
$thread_id  = isset($_POST['thread_id']) ? sanitize_text_field($_POST['thread_id']) : 0 ;
$thread_body= isset($_POST['body']) ? wp_kses_post(htmlspecialchars_decode($_POST['body'], ENT_QUOTES)) : '';

$update_thread_body = array(
    'ID'           => $thread_id,
    'post_content' => $thread_body,
);

wp_update_post( $update_thread_body );
?>