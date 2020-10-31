<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user, $wpscfunction,$wpdb;

$sla_policies = get_terms([
  'taxonomy'   => 'wpsc_sla',
  'hide_empty' => false,
  'orderby'    => 'meta_value_num',
  'order'    	 => 'ASC',
  'meta_query' => array('order_clause' => array('key' => 'load_order')),
]);
$sla_flag = false;
$sla_data = $wpscfunction->get_ticket_meta($ticket_id,'sla',true);
$sla_term = $wpscfunction->get_ticket_meta($ticket_id,'sla_term',true);
$sla_email_send = $wpscfunction->get_ticket_meta($ticket_id,'wpsp_out_of_sla_email_send',true);

$wpscfunction->delete_ticket_meta($ticket_id,'sla_term',true);
$wpscfunction->delete_ticket_meta($ticket_id,'wpsp_out_of_sla_email_send',true);
$wpscfunction->delete_ticket_meta($ticket_id,'wpsc_overdue_ticket_checked');

foreach ( $sla_policies as $sla ){
  $conditions = get_term_meta($sla->term_id,'conditions',true);
	$sla_term = $wpscfunction->get_ticket_meta($ticket_id,'sla_term',true);
  if( $wpscfunction->check_ticket_conditions($conditions,$ticket_id) ){
    if($sla_term != $sla->term_id){
      $time      = get_term_meta($sla->term_id,'time',true);
      $time_unit = get_term_meta($sla->term_id,'time_unit',true);
      $sla_date = new DateTime;
      date_add($sla_date,date_interval_create_from_date_string($time." ".$time_unit));
			if($sla_data){
				$meta_value = array(
					'meta_value' => date_format($sla_date,"Y-m-d H:i:s")
				);
				$wpscfunction->update_ticket_meta($ticket_id,'sla',$meta_value);
			}
			else {
				$wpscfunction->add_ticket_meta($ticket_id,'sla',date_format($sla_date,"Y-m-d H:i:s"));
			}
			
			$wpscfunction->add_ticket_meta($ticket_id,'sla_term',$sla->term_id);
			$wpscfunction->add_ticket_meta($ticket_id,'wpsp_out_of_sla_email_send',0);
		}
    $sla_flag = true;
    break;
  }
}

if(!$sla_flag) {
	if ($sla_data) {
		$meta_value = array(
			'meta_value' => '3099-01-01 00:00:00'
		);
		$wpscfunction->update_ticket_meta($ticket_id,'sla',$meta_value);
	}
	else {
		$wpscfunction->add_ticket_meta($ticket_id,'sla','3099-01-01 00:00:00');
	}
	
	$wpscfunction->add_ticket_meta($ticket_id,'sla_term',0);
	$wpscfunction->add_ticket_meta($ticket_id,'wpsp_out_of_sla_email_send',0);
}
