<?php 
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

global $wpdb,$wpscfunc,$wpscfunction;
/*
* Line Graph
*/

$first = date("Y-m-d", strtotime("first day of last month"));
$last = date("Y-m-d", strtotime("last day of last month"));
$datetime1  = new DateTime($first);
$datetime2  = new DateTime($last);
$difference = $datetime1->diff($datetime2);
$days       = $difference->d;
$average    = array();
for($i=0;$i<=$days;$i++){
  $date = date('Y-m-d', strtotime($first. '+'.$i.' days'));
  
  $wpdb->query("DELETE FROM {$wpdb->prefix}wpsc_reports WHERE report_type='first_response' AND result_date='".$date."'");
  
  $response = array(); 
  $tickets  = $wpdb->get_results("SELECT id FROM {$wpdb->prefix}wpsc_ticket WHERE DATE(date_created)= '".$date."'");

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
      'result_date'  => $date
    ));

}