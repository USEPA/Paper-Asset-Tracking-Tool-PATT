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
  
  $wpdb->query("DELETE FROM {$wpdb->prefix}wpsc_reports WHERE report_type='no_of_tickets' AND result_date='" . $date . "'");

  $tickets = $wpdb->get_var("SELECT DISTINCT COUNT(id) FROM {$wpdb->prefix}wpsc_ticket WHERE DATE(date_created)= '" . $date . "'");

  $wpdb->insert($wpdb->prefix . 'wpsc_reports',
        array(
            'report_type'  => 'no_of_tickets',
            'type'         => 'daily',
            'ticket_count' => $tickets,
            'result_date'  => $date,
      ));


}