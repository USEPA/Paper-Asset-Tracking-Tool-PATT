<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user, $wpscfunction,$post,$wpdb;
if (!($current_user->ID && $current_user->has_cap('manage_options'))) {exit;}

$page = $_POST['page'];
$total_result = $_POST['total_result'];

$the_query = $wpdb->get_results("SELECT t.id FROM {$wpdb->prefix}wpsc_ticket t WHERE  t.id NOT IN (SELECT  DISTINCT tm.ticket_id FROM {$wpdb->prefix}wpsc_ticketmeta tm
WHERE  tm.meta_key = 'sla')");
$query = json_decode(json_encode($the_query), true);

if($query){
	foreach ($query as $key => $obj) {
		$wpdb->insert( $wpdb->prefix . 'wpsc_ticketmeta', 
			array(
				'ticket_id' => $obj['id'],
				'meta_key' => 'sla',
				'meta_value' => '3099-01-01 00:00:00'
			));
	}
}

 $percentage = (($page*2)/$total_result)*100;
 $is_next =  $page < ($total_result/5) ? 1 : 0;
  
 $response=array(
    'is_next'       => $is_next,
    'percentage'    => ceil($percentage),
		'page'					=> $page + 1
);

echo json_encode($response);