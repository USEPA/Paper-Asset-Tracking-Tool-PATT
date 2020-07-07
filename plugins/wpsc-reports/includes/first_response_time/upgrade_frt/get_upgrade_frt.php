<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user, $wpscfunction,$wpdb,$post;
if (!($current_user->ID && $current_user->has_cap('manage_options'))) {exit;}

$page	 = isset($_POST['page']) ? sanitize_text_field( $_POST['page'] ) : '';
$total_result = isset($_POST['total_result']) ? sanitize_text_field( $_POST['total_result'] ) : '';

$sql = " SELECT SQL_CALC_FOUND_ROWS t.id FROM {$wpdb->prefix}wpsc_ticket t WHERE  t.id NOT IN (SELECT DISTINCT tm.ticket_id FROM {$wpdb->prefix}wpsc_ticketmeta tm
WHERE  tm.meta_key = 'frt_checked') LIMIT 10";

$the_query = $wpdb->get_results($sql);

if ($the_query) {
	foreach ($the_query as $ticket) {
		$frt_checked = $wpscfunction->get_ticket_meta($ticket->id,'frt_checked',true);
		if ($frt_checked) {
			$wpscfunction->update_ticket_meta($ticket->id,'frt_checked',array('meta_value'=> 1));
		}
		else {
			$wpscfunction->add_ticket_meta($ticket->id, 'frt_checked',1);
		}
		
		$post_created = $wpscfunction->get_ticket_fields($ticket->id,'date_created');
		$ticket_id	= $ticket->id;
		$args_thread = array(
			'post_type'      => 'wpsc_ticket_thread',
			'post_status'    => 'publish',
			'orderby'        => 'date',
			'order'          => 'ASC',
			'posts_per_page' => -1,
			'meta_query'     => array(
				'relation' => 'AND',
				array(
					'key'     => 'ticket_id',
					'value'   => $ticket_id,
					'compare' => '='
				),
				array(
					'key'     => 'thread_type',
					'value'   => 'reply',
					'compare' => '='
				),
			),
		);
		$threads = get_posts($args_thread);
		foreach ($threads as $thread):
			$customer_email = get_post_meta( $thread->ID, 'customer_email', true);
			$user = get_user_by( 'email',$customer_email );
			
			if( $user && $user->has_cap('wpsc_agent') ){
				$thread_created = $thread->post_date_gmt;
				$datetime1      = new DateTime($post_created);
				$datetime2      = new DateTime($thread_created);
				$diff           = $datetime2->diff($datetime1);
				$response       = ($diff->d*24*60)+($diff->h*60)+($diff->i);
				$get_first_response = $wpscfunction->get_ticket_meta($ticket->id,'first_response',true);
				if ($get_first_response) {
					$wpscfunction->update_ticket_meta($ticket->id, 'first_response',array('meta_value'=>$response));
				}
				else {
					$wpscfunction->add_ticket_meta($ticket->id, 'first_response',$response);
				}
				break;
			}
		endforeach;
	}
}

$percentage        = (($page*2)/$total_result)*100;
$is_next           = $page < ($total_result/5) ? 1 : 0;

$response=array(
  'is_next'       => $is_next,
  'percentage'    => ceil($percentage),
  'page'		  => $page + 1
);

echo json_encode($response);
