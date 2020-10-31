<?php 
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

global $wpscfunc,$wpdb,$wpscfunction;
/*
* Line Graph
*/
$rlabel = array();
$values = array();
if(isset($_POST['custom_date_start']) && $_POST['custom_date_start']!=''){
    $startdate=$_POST['custom_date_start'];
}else{
     $startdate=date('Y-m-d',strtotime( "monday this week" ));
}
if(isset($_POST['custom_date_end']) && $_POST['custom_date_end']!=''){
    $enddate=$_POST['custom_date_end'];
}else{
    $enddate=date('Y-m-d', strtotime($startdate. ' +6 days'));
}
$datetime1 = new DateTime($startdate);
$datetime2 = new DateTime($enddate);
$diff      = $datetime1->diff($datetime2);
$days      = $diff->format('%a');
$average   = array();

for ($i=0; $i<=$days ; $i++) {
  
  $date = date('Y-m-d', strtotime($startdate. '+'.$i.' days'));
  
  $wpdb->query("DELETE FROM {$wpdb->prefix}wpsc_reports WHERE report_type='no_of_tickets' AND result_date='".$date."'");
  
  $tickets  = $wpdb->get_var("SELECT DISTINCT COUNT(id) FROM {$wpdb->prefix}wpsc_ticket WHERE DATE(date_created)= '".$date."'");

  $wpdb->insert( $wpdb->prefix . 'wpsc_reports', 
    array(
      'report_type'  => 'no_of_tickets',
      'type'         => 'daily',
      'ticket_count' => $tickets,
      'result_date'  => $date
    ));  
}
