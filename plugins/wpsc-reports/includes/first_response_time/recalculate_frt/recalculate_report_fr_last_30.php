<?php 
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

global $wpscfunction,$wpdb,$wpscfunc;
/*
* Line Graph
*/
$startdate=date('Y-m-d',strtotime( "today -30 days" ));
$average  = array();

for($i=0;$i<30;$i++){
  $date = date('Y-m-d', strtotime($startdate. '+'.$i.' days'));
  
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
