<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpdb,$wpscfunction;

$time = get_option('wpsc_garbage_collection_data_time' ,'');
$wpsc_personal_data_retention_type = get_option( 'wpsc_personal_data_retention_type');
$wpsc_personal_data_retention_period_time = get_option( 'wpsc_personal_data_retention_period_time');
$check_flag = false;

$now = time();
$ago = strtotime($time);
$diff = $now - $ago;
if($diff >= 86400){
	$check_flag = true;
}

if( !$check_flag || $wpsc_personal_data_retention_type=='disable' || !$wpsc_personal_data_retention_period_time){
	return;
}

$sql = "SELECT t.* from {$wpdb->prefix}wpsc_ticket  t  WHERE t.id NOT IN (SELECT DISTINCT tm.ticket_id  FROM {$wpdb->prefix}wpsc_ticketmeta tm 
				WHERE tm.meta_key='wpsc_privacy_data_erase' AND t.id = tm.ticket_id ) ";
$tickets = $wpdb->get_results($sql);
$ticket_list = json_decode(json_encode($tickets), true);

$fields = get_terms([
 'taxonomy'   => 'wpsc_ticket_custom_fields',
 'hide_empty' => false,
 'orderby'    => 'meta_value_num',
 'meta_key'	 => 'wpsc_tf_load_order',
 'order'    	 => 'ASC',
 'meta_query' => array(
	 array(
			'key'       => 'agentonly',
			'value'     => array(0,1),
			'compare'   => 'IN'
		)
 ),
]);

$wpsc_personal_data_retention_period_unit = get_option('wpsc_personal_data_retention_period_unit');

if($wpsc_personal_data_retention_period_unit == 'days') {
	foreach ($ticket_list as $post) {
		$ticket_id = $post['id'];
		$args = array(
			'post_type'      => 'wpsc_ticket_thread',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
		  'order'    	     => 'ASC',
			'meta_query'     => array(
				 array(
					'key'     => 'ticket_id',
					'value'   => $ticket_id,
					'compare' => '='
				),
			),
		);
		$ticket_threads = get_posts($args);
		
  	$now = time();
  	$date_created = strtotime($wpscfunction->get_ticket_fields($post['id'],'date_created'));
		if($date_created <= $now) {
			
			$diff = $now - $date_created;
			$diff_days = intval( $diff/(60*60*24) );
			
			if($diff_days > $wpsc_personal_data_retention_period_time ) {
				
				$wpdb->update($wpdb->prefix.'wpsc_ticket', array('customer_name'=>'Anonymized User', 'customer_email'=>'anonymous@anonymous.anonymous '), array('id'=>$post['id']));
					foreach ($ticket_threads as $ticket_thread) {
          update_post_meta($ticket_thread->ID, 'customer_name', 'Anonymized User');
          update_post_meta($ticket_thread->ID, 'customer_email', 'anonymous@anonymous.anonymous');
        }
				foreach ($fields as $key => $field) {
					$personal_info = get_term_meta($field->term_id, 'wpsc_tf_personal_info', true);
          if($personal_info){
						$wpscfunction->delete_ticket_meta($post['id'],$field->slug);
					}
        }
				$wpscfunction->update_ticket_meta($post['id'],'wpsc_privacy_data_erase',array('meta_value' => 1));
			}
		}
	}
}elseif ($wpsc_personal_data_retention_period_unit == 'months') {
	foreach ($ticket_list as $post) {
		$ticket_id = $post['id'];
		
		$args = array(
			'post_type'      => 'wpsc_ticket_thread',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
		  'order'    	     => 'ASC',
			'meta_query'     => array(
				 array(
					'key'     => 'ticket_id',
					'value'   => $ticket_id,
					'compare' => '='
				),
			),
		);
		$ticket_threads = get_posts($args);
		
		$now          = new DateTime();
		$date_created = $wpscfunction->get_ticket_fields($post['id'],'date_created');
		$date_created = new DateTime($date_created);
		if($date_created <= $now) {
			
			$diff = $now->diff($date_created);
			$months = ($diff->y*12) + $diff->m;
			
			if($months >= $wpsc_personal_data_retention_period_time) {
				$wpdb->update($wpdb->prefix.'wpsc_ticket', array('customer_name'=>'Anonymized User', 'customer_email'=>'anonymous@anonymous.anonymous '), array('id'=>$post['id']));

				foreach ($ticket_threads as $ticket_thread) {
          update_post_meta($ticket_thread->ID, 'customer_name', 'Anonymized User');
          update_post_meta($ticket_thread->ID, 'customer_email', 'anonymous@anonymous.anonymous');
        }
			  foreach ($fields as $key => $field) {
					$personal_info = get_term_meta($field->term_id, 'wpsc_tf_personal_info', true);
          if($personal_info){
						$wpscfunction->delete_ticket_meta($post['id'],$field->slug);
					}
				}
				$wpscfunction->update_ticket_meta($post['id'],'wpsc_privacy_data_erase',array('meta_value' => 1));
			}
		}
	}
}elseif ($wpsc_personal_data_retention_period_unit == 'years') {
	foreach ($ticket_list as $post) {
		$ticket_id = $post['id'];
		
		$args = array(
			'post_type'      => 'wpsc_ticket_thread',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
		  'order'    	     => 'ASC',
			'meta_query'     => array(
				 array(
					'key'     => 'ticket_id',
					'value'   => $ticket_id,
					'compare' => '='
				),
			),
		);
		$ticket_threads = get_posts($args);
		
		$now  = new DateTime();
		$date_created = $wpscfunction->get_ticket_fields($post['id'],'date_created');
		$date_created = new DateTime($date_created);
		
		if($date_created <= $now) {
			$diff = $now->diff($date_created);
			$years = $diff->y;
			
			if($years >= $wpsc_personal_data_retention_period_time) {
				
				$wpdb->update($wpdb->prefix.'wpsc_ticket', array('customer_name'=>'Anonymized User', 'customer_email'=>'anonymous@anonymous.anonymous '), array('id'=>$post['id']));
				foreach ($ticket_threads as $ticket_thread) {
					update_post_meta($ticket_thread->ID, 'customer_name', 'Anonymized User');
					update_post_meta($ticket_thread->ID, 'customer_email', 'anonymous@anonymous.anonymous');
				}
				foreach ($fields as $key => $field) {
					$personal_info = get_term_meta($field->term_id, 'wpsc_tf_personal_info', true);
					if($personal_info){
						$wpscfunction->delete_ticket_meta($post['id'],$field->slug);
					}
				}
				$wpscfunction->update_ticket_meta($post['id'],'wpsc_privacy_data_erase',array('meta_value' => 1));
			}
		}
	}
}
update_option('wpsc_garbage_collection_data_time', date("Y-m-d H:i:s"));
