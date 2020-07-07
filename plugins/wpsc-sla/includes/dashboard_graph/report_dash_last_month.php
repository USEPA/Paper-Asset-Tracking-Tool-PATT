<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

global $wpscfunction,$current_user,$wpdb;

$first = date("Y-m-d", strtotime("first day of last month"));
$last  = date("Y-m-d", strtotime("last day of last month"));

$last_two_month_first = date('Y-m-d', strtotime('first day of -2 months'));
$last_two_month_last  = date('Y-m-d' ,strtotime('last day of -2 month'));

$overdue_tickets = $wpdb->get_var("SELECT SUM(overdue_count) FROM {$wpdb->prefix}wpsc_sla_reports WHERE result_date BETWEEN '".$first."' AND '".$last."' ");

if(!$overdue_tickets) $overdue_tickets = 0;

$last_two_month_overdue_tickets = $wpdb->get_var("SELECT SUM(overdue_count) FROM {$wpdb->prefix}wpsc_sla_reports WHERE result_date BETWEEN '".$last_two_month_first."' AND '".$last_two_month_last."' ");


if($overdue_tickets > $last_two_month_overdue_tickets){
  if($last_two_month_overdue_tickets > 0){
    $overdue_percentage = $overdue_tickets - $last_two_month_overdue_tickets;  
  }else{
    $overdue_percentage = $overdue_tickets;  
  }
  $overdue_graph = 'increasing';
}elseif($overdue_tickets == $last_two_month_overdue_tickets ){
    $overdue_percentage = '';
    $overdue_graph =  '';
}else{
  if($last_two_month_overdue_tickets > 0  && $overdue_tickets > 0){
    $overdue_percentage = $overdue_tickets - $last_two_month_overdue_tickets;
    $overdue_graph =  'decreasing';
  }elseif($last_two_month_overdue_tickets == 0 && $overdue_tickets == 0){
    $overdue_percentage = '';
    $overdue_graph =  '';
  }else{
    $overdue_percentage = $last_two_month_overdue_tickets;
    $overdue_graph =  'decreasing';
  }
}

$days = 'month';
