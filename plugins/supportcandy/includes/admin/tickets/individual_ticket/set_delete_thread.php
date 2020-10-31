<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user, $wpscfunction ,$wp;
if (!($current_user->ID && $current_user->has_cap('wpsc_agent'))) {
	exit;
}

$thread_id  = isset($_POST['thread_id']) ? sanitize_text_field($_POST['thread_id']) : 0 ;
wp_delete_post($thread_id);
?>