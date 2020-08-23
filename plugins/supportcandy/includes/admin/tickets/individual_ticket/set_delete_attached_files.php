<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user,$wpscfunction;
if (!($current_user->ID && $current_user->has_cap('edit_published_posts'))) {exit;}

$ticket_id = isset($_POST['ticket_id']) ? intval($_POST['ticket_id']) : 0 ;
if(!$ticket_id) die();

$attachment_slug = isset($_POST['attachment_slug']) ? sanitize_text_field($_POST['attachment_slug']) : 0 ;

$field_id  = isset($_POST['attachment_id']) ? sanitize_text_field($_POST['attachment_id']) : '' ;
if (!$field_id) {exit;}

$term_meta = get_term_meta($field_id);

$attachments = $wpscfunction->get_ticket_meta($ticket_id,$attachment_slug);

update_term_meta ($field_id, 'active', '0');
update_term_meta ($field_id, 'time_uploaded', date("Y-m-d H:i:s"));

echo '{ "sucess_status":"'.__('File deleted successfully.','supportcandy').'" }';


