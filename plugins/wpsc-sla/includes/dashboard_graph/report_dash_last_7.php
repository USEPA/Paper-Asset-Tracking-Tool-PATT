<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

$first = date('Y-m-d', strtotime('-7 days'));
$last  = date('Y-m-d', strtotime("today "));

$last_week_first = date('Y-m-d', strtotime('-14 days'));
$last_week_last  = date('Y-m-d' ,strtotime('-8 days'));

$overdue_tickets = $wpdb->get_var("SELECT SUM(overdue_count) FROM {$wpdb->prefix}wpsc_sla_reports WHERE result_date BETWEEN '".$first."' AND '".$last."' ");

if(!$overdue_tickets) $overdue_tickets = 0;

$last_week_overdue_tickets = $wpdb->get_var("SELECT SUM(overdue_count) FROM {$wpdb->prefix}wpsc_sla_reports WHERE result_date BETWEEN '".$last_week_first."' AND '".$last_week_last."' ");

if($overdue_tickets > $last_week_overdue_tickets){
  if($last_week_overdue_tickets > 0){
    $overdue_percentage = $overdue_tickets - $last_week_overdue_tickets;  
  }else{
    $overdue_percentage = $overdue_tickets;  
  }
  $overdue_graph = 'increasing';
}elseif($overdue_tickets == $last_week_overdue_tickets){
  $overdue_percentage = '';
  $overdue_graph =  '';
}else{
  if($last_week_overdue_tickets > 0  && $overdue_tickets > 0){
    $overdue_percentage = $overdue_tickets - $last_week_no_tickets;
    $overdue_graph =  'decreasing';
  }elseif($last_week_overdue_tickets == 0 && $overdue_tickets == 0){
    $overdue_percentage = '';
    $overdue_graph =  '';
  }else{
    $overdue_percentage = $last_week_overdue_tickets;
    $overdue_graph =  'decreasing';
  }
}

$days = '7 days';
