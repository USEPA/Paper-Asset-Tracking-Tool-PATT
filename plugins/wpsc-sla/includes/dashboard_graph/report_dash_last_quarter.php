<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

global $wpscfunction,$current_user,$wpdb;

$first = date("Y-m-d", strtotime("today -3 months"));
$last  = date("Y-m-d", strtotime("today"));

$last_6_month_first = date('Y-m-d', strtotime('first day of -6 months'));
$last_4_month_last  = date('Y-m-d' ,strtotime('last day of -4 month'));


$overdue_tickets = $wpdb->get_var("SELECT SUM(overdue_count) FROM {$wpdb->prefix}wpsc_sla_reports WHERE result_date BETWEEN '".$first."' AND '".$last."' ");

if(!$overdue_tickets) $overdue_tickets = 0;

$last_of_last_quarter_overdue_tickets = $wpdb->get_var("SELECT SUM(overdue_count) FROM {$wpdb->prefix}wpsc_sla_reports WHERE result_date BETWEEN '".$last_6_month_first."' AND '".$last_4_month_last."' ");


if($overdue_tickets > $last_of_last_quarter_overdue_tickets){
  if($last_of_last_quarter_overdue_tickets > 0){
    $overdue_percentage = $overdue_tickets - $last_of_last_quarter_overdue_tickets;  
  }else{
    $overdue_percentage = $overdue_tickets;  
  }
  $overdue_graph = 'increasing';
}else if($overdue_tickets == $last_of_last_quarter_overdue_tickets){
    $overdue_percentage = '';
    $overdue_graph =  '';
}else{
  if($last_of_last_quarter_overdue_tickets > 0  && $overdue_tickets > 0){
    $overdue_percentage = $overdue_tickets - $last_of_last_quarter_overdue_tickets;
    $overdue_graph =  'decreasing';
  }elseif($last_of_last_quarter_overdue_tickets == 0 && $overdue_tickets == 0){
    $overdue_percentage = '';
    $overdue_graph =  '';
  }else{
    $overdue_percentage = $last_of_last_quarter_overdue_tickets;
    $overdue_graph =  'decreasing';
  }
}

$days = 'quarter';
