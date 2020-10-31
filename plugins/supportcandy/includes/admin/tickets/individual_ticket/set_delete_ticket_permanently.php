<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpdb, $current_user, $wpscfunction;

if (!($current_user->ID && $current_user->has_cap('wpsc_agent'))) {
		exit;
}

$ticket_id   		   	= isset($_POST['ticket_id']) ? sanitize_text_field($_POST['ticket_id']) : '' ;

//PATT BEGIN
// Delete associated documents when ticket delete
$get_associated_doc_ticket_meta = $wpdb->get_results("SELECT * FROM wpqa_wpsc_ticketmeta WHERE ticket_id = '" . $ticket_id . "' AND ( meta_key = 'destruction_authorizations_image' OR meta_key = 'litigation_letter_image' OR meta_key = 'congressional_file' OR meta_key = 'foia_file' ) ", ARRAY_A);

if ( is_array( $get_associated_doc_ticket_meta ) ) {
	array_walk( $get_associated_doc_ticket_meta, function( $value, $key ) use(&$wpdb) {

		$wpdb->query( 'DELETE  FROM '.$wpdb->prefix.'wpsc_ticketmeta WHERE id = "'.$value['id'].'"' );
		$attachment_ids = str_replace( '[', "", $value['meta_value'] );
		$attachment_ids = str_replace( ']', "", $attachment_ids );
		$attachment_ids = explode( ',', $attachment_ids );

		if ( is_array( $attachment_ids ) ) {
			array_walk( $attachment_ids, function( $attachment_id, $k ) {
				// delete from folder, post and post meta table
				wp_delete_attachment( $attachment_id );
			});
		}

		// Delete from ticket meta table
		$wpdb->delete( $wpdb->prefix . 'wpsc_ticketmeta', array( 'id' => $value['id'] ) );
	});
}
//PATT END

//PATT BEGIN
$get_associated_boxes = $wpdb->get_results("
SELECT id, storage_location_id FROM wpqa_wpsc_epa_boxinfo 
WHERE ticket_id = '" . $ticket_id . "'
");

foreach ($get_associated_boxes as $info) {
		$associated_box_ids = $info->id;
		$associated_storage_ids = $info->storage_location_id;
		
		$box_details = $wpdb->get_row(
"SELECT 
digitization_center,
aisle,
bay,
shelf,
position
FROM wpqa_wpsc_epa_storage_location
WHERE id = '" . $associated_storage_ids . "'"
			);
			
			$box_storage_digitization_center = $box_details->digitization_center;
			$box_storage_aisle = $box_details->aisle;
			$box_storage_bay = $box_details->bay;
			$box_storage_shelf = $box_details->shelf;
			$box_sotrage_shelf_id = $box_storage_aisle . '_' . $box_storage_bay . '_' . $box_storage_shelf;

$box_storage_status = $wpdb->get_row(
"SELECT 
occupied,
remaining
FROM wpqa_wpsc_epa_storage_status
WHERE shelf_id = '" . $box_sotrage_shelf_id . "'"
			);

$box_storage_status_occupied = $box_storage_status->occupied;
$box_storage_status_remaining = $box_storage_status->remaining;
$box_storage_status_remaining_added = $box_storage_status->remaining + 1;

if ($box_storage_status_remaining <= 4) {
$table_ss = 'wpqa_wpsc_epa_storage_status';
$ssr_update = array('remaining' => $box_storage_status_remaining_added);
$ssr_where = array('shelf_id' => $box_sotrage_shelf_id, 'digitization_center' => $box_storage_digitization_center);
$wpdb->update($table_ss , $ssr_update, $ssr_where);
}

if($box_storage_status_remaining == 4){
$sso_update = array('occupied' => 0);
$sso_where = array('shelf_id' => $box_sotrage_shelf_id, 'digitization_center' => $box_storage_digitization_center);
$wpdb->update($table_ss , $sso_update, $sso_where);
}

		$wpdb->delete($wpdb->prefix.'wpsc_epa_storage_location', array( 'id' => $associated_storage_ids));
		$wpdb->delete($wpdb->prefix.'wpsc_epa_boxinfo', array( 'id' => $associated_box_ids));
	}
//PATT END

$wpdb->delete($wpdb->prefix.'wpsc_ticket', array( 'id' => $ticket_id));
$wpdb->delete($wpdb->prefix.'wpsc_ticketmeta', array('ticket_id' => $ticket_id));

$args = array(
	'post_type'      => 'wpsc_ticket_thread',
	'post_status'    => array('publish','trash'),
	'posts_per_page' => -1,
	'meta_query'     => array(
		 array(
			'key'     => 'ticket_id',
			'value'   => $ticket_id,
			'compare' => '='
		),
	),
);
$ticket_threads = get_posts($args);
if($ticket_threads) {
	foreach ($ticket_threads as $ticket_thread ) {
		wp_delete_post($ticket_thread->ID,true);
	}
}


