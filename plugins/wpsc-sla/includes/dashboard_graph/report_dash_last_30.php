
<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

$first = date('Y-m-d',strtotime( "today -30 days" ));
$last  = date('Y-m-d', strtotime("today "));

$last_60_31_first = date('Y-m-d', strtotime('-60 days'));
$last_60_31_last  = date('Y-m-d' ,strtotime('-31 days'));

$overdue_tickets = $wpdb->get_var("SELECT SUM(overdue_count) FROM {$wpdb->prefix}wpsc_sla_reports WHERE result_date BETWEEN '".$first."' AND '".$last."' ");

if(!$overdue_tickets) $overdue_tickets = 0;

$last_60_31_overdue_tickets = $wpdb->get_var("SELECT SUM(overdue_count) FROM {$wpdb->prefix}wpsc_sla_reports WHERE result_date BETWEEN '".$last_60_31_first."' AND '".$last_60_31_last."'");

if($overdue_tickets > $last_60_31_overdue_tickets){
  if($last_60_31_overdue_tickets > 0){
    $overdue_percentage = $overdue_tickets - $last_60_31_overdue_tickets;  
  }else{
    $overdue_percentage = $overdue_tickets;  
  }
  $overdue_graph = 'increasing';
}else if($overdue_tickets == $last_60_31_overdue_tickets){
  $overdue_percentage = '';
  $overdue_graph =  '';
}else{
  if($last_60_31_overdue_tickets > 0  && $overdue_tickets > 0){
    $overdue_percentage = $overdue_tickets - $last_60_31_overdue_tickets;
    $overdue_graph =  'decreasing';
  }elseif($last_60_31_overdue_tickets == 0 && $overdue_tickets == 0){
    $overdue_percentage = '';
    $overdue_graph =  '';
  }else{
    $overdue_percentage = $last_60_31_overdue_tickets;
    $overdue_graph =  'decreasing';
  }
}

$days = '30 days';
