<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

global $wpscfunction,$wpdb;

$time = get_option('wpsc_report_last_check');
$check_flag = false;

$now = time();
$ago = strtotime($time);
$diff = $now - $ago;
if($diff >= 86400){
	$check_flag = true;
}

if(!$check_flag){
	return;
}

$yesterday = date('Y-m-d',strtotime("-1 days"));

$yesteday_count = $wpdb->get_var("SELECT ticket_count from {$wpdb->prefix}wpsc_reports where result_date='".$yesterday."'");

if(!$yesteday_count){
  $count = $wpdb->get_var("SELECT DISTINCT COUNT(id) from {$wpdb->prefix}wpsc_ticket where DATE(date_created)='".$yesterday."'");

  $wpdb->insert( $wpdb->prefix . 'wpsc_reports', 
    array(
      'report_type'  => 'no_of_tickets',
      'type'         => 'daily',
      'ticket_count' => $count,
      'result_date'  => $yesterday
    ));
    update_option('wpsc_ticket_stats_checked_date', $yesterday);

  $response = array(); 
  $tickets  = $wpdb->get_results("SELECT id FROM {$wpdb->prefix}wpsc_ticket WHERE DATE(date_created)= '".$yesterday."'");

  if($tickets){
    foreach ($tickets as $ticket) {
      $frt = $wpscfunction->get_ticket_meta($ticket->id,'first_response',true);
      if($frt){
        $response[] = $frt;
      }
    }  
  }
  
  if(!empty($response)){
    $average = round(array_sum($response) / count($response),2);  
  }else{
    $average = 0;
  }

  $wpdb->insert( $wpdb->prefix . 'wpsc_reports', 
    array(
      'report_type'  => 'first_response',
      'type'         => 'daily',
      'ticket_count' => $average,
      'result_date'  => $yesterday
    ));
}

update_option('wpsc_report_last_check', date("Y-m-d H:i:s"));
