<?php 
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

global $wpscfunc,$wpdb,$wpscfunction;

/*
* Line Graph
*/
$startdate  = date('Y-m-d', strtotime("today "));
$enddate    = date('Y-m-d', strtotime("today -3 months"));
$datetime1  = new DateTime($startdate);
$datetime2  = new DateTime($enddate);
$difference = $datetime1->diff($datetime2);
$days       = $difference->days;

for($i=0;$i<=$days;$i++) {
    $date = date('Y-m-d', strtotime($enddate. '+'.$i.' days'));
    
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
