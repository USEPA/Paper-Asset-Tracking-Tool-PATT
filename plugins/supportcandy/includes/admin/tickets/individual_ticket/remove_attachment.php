<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user, $wpscfunction;
if (!($current_user->ID && $current_user->has_cap('edit_published_posts'))) {
	exit;
}

$post_id     = isset($_POST['post_id']) ? sanitize_text_field($_POST['post_id']) : '' ;

$attachment_id = isset($_POST['attachment']) ? sanitize_text_field($_POST['attachment']) : '' ;

$ticket_id  = isset($_POST['ticket_id']) ? sanitize_text_field($_POST['ticket_id']) : '' ;

$attachments = get_post_meta($post_id,'attachments',true);

$array_pos = array_search($attachment_id, $attachments);

unset($attachments[$array_pos]);

update_post_meta($post_id,'attachments',$attachments);

update_term_meta($attachment_id,'active',0);
update_term_meta($attachment_id,'time_uploaded', date("Y-m-d H:i:s"));

do_action('wpsc_after_thread_attachment_delete', $attachment_id, $ticket_id );