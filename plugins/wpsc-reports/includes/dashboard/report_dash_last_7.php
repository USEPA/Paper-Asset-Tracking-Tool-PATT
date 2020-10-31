<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

global $wpscfunction,$current_user,$wpdb;


$first = date('Y-m-d', strtotime('today -6 days'));
$last  = date('Y-m-d', strtotime("today -1 days"));

$counted_ticket = $wpdb->get_var("SELECT SUM(ticket_count) FROM {$wpdb->prefix}wpsc_reports WHERE result_date BETWEEN '".$first."' AND '".$last."' AND report_type ='no_of_tickets' ");

$today = date('Y-m-d');
$todays_count = $wpdb->get_var("SELECT DISTINCT COUNT(id) from {$wpdb->prefix}wpsc_ticket where DATE(date_created)='" . $today . "'");

$no_of_tickets = $counted_ticket + $todays_count;

if(!$no_of_tickets) $no_of_tickets = 0;

$last_week_first = date('Y-m-d', strtotime('-13 days'));
$last_week_last  = date('Y-m-d' ,strtotime('-7 days'));

$last_week_no_tickets  = $wpdb->get_var("SELECT SUM(ticket_count) FROM {$wpdb->prefix}wpsc_reports WHERE result_date  BETWEEN  '".$last_week_first."'  AND  '".$last_week_last."' AND report_type = 'no_of_tickets'");
$last_week_frt_average_in_min = $wpdb->get_var("SELECT AVG(ticket_count) FROM {$wpdb->prefix}wpsc_reports WHERE result_date  BETWEEN  '".$last_week_first."'  AND  '".$last_week_last."' AND report_type = 'first_response' AND ticket_count !=0");

if($no_of_tickets > $last_week_no_tickets){
  if($last_week_no_tickets > 0){
    $no_of_tic_percentage =  $no_of_tickets - $last_week_no_tickets;  
  }else{
    $no_of_tic_percentage = $no_of_tickets;  
  }
  $ticket_graph = 'increasing';
}elseif($last_week_no_tickets == $no_of_tickets ){
  $no_of_tic_percentage = '';
  $ticket_graph =  '';
}else{
  if($last_week_no_tickets > 0  && $no_of_tickets > 0){
    $no_of_tic_percentage = $no_of_tickets - $last_week_no_tickets;
    $ticket_graph =  'decreasing';
  }elseif($last_week_no_tickets == 0 && $no_of_tickets == 0){
    $no_of_tic_percentage = '';
    $ticket_graph =  '';
  }else{
    $no_of_tic_percentage = $last_week_no_tickets;
    $ticket_graph =  'decreasing';
  }
  
}

$last_frt_avg = $wpdb->get_var("SELECT AVG(ticket_count) FROM {$wpdb->prefix}wpsc_reports WHERE result_date BETWEEN '" . $first . "' AND '" . $last . "' AND report_type ='first_response' AND ticket_count !=0 ");
$todays_ticket = $wpdb->get_results("SELECT id FROM {$wpdb->prefix}wpsc_ticket WHERE DATE(date_created)= '" . $today . "'");
$response = array();
if ($todays_ticket) {
    foreach ($todays_ticket as $ticket) {
        $frt = $wpscfunction->get_ticket_meta($ticket->id, 'first_response', true);
        if ($frt) {
            $response[] = round($frt / 60, 2);
        }
    }
}

if (!empty($response)) {
    $today_frt_avg = round(array_sum($response) / count($response),2);
} else {
    $today_frt_avg = 0;
}

$average = $last_frt_avg/60 + $today_frt_avg;

if ($average) {
    $frt_hour = floor($average);
    $fraction = $average - $frt_hour;
    $seconds = $fraction * 3600;
    $frt_minute = floor($seconds / 60);
}

$last_week_frt_average = $last_week_frt_average_in_min/60;

if($average > $last_week_frt_average ){
  if($last_week_frt_average > 0 ){
    $frt_percentage = $average - $last_week_frt_average;
  }else{
    $frt_percentage = $average;
  }
  $frt_graph = 'increasing';
}elseif($average == $last_week_frt_average){
  $frt_percentage = '';
    $frt_graph = '';
}else{
  if($last_week_frt_average > 0 && $average > 0 ){
    $frt_percentage = $average - $last_week_frt_average; 
    $frt_graph = 'decreasing';
  }elseif($last_week_frt_average == 0 && $average == 0){
    $frt_percentage = '';
    $frt_graph = '';
  }else{
    $frt_percentage = $last_week_frt_average;
    $frt_graph = 'decreasing';
  }
  
}

if($frt_percentage){
  $hour = floor($frt_percentage);
  $fraction = $frt_percentage - $hour;
  $seconds = $fraction * 3600;
  $minute = floor($seconds / 60);
}

$category_array = array();

$first = date('Y-m-d', strtotime('today -6 days'));
$today  = date('Y-m-d', strtotime("today"));

$tickets = $wpdb->get_results("SELECT id FROM {$wpdb->prefix}wpsc_ticket WHERE DATE(date_created) BETWEEN '".$first."' AND '".$today."'");

//category bar chart 
$gcategory_data_name = array();
$gcategory_data_count = array();

$categories = get_terms([
  'taxonomy'   => 'wpsc_categories',
  'hide_empty' => false,
  'orderby'    => 'meta_value_num',
  'order'    	 => 'ASC',
  'meta_query' => array('order_clause' => array('key' => 'wpsc_category_load_order')),
]);


$custdata_array = array();
$pie_widgets     = get_option('wpsc_report_dash_widgets',array());
foreach($pie_widgets as $widget_id){

  $cust_vals = array();
  $custom_field = get_term( $widget_id, 'wpsc_ticket_custom_fields' );
  $wpsc_tf_type = get_term_meta( $widget_id, 'wpsc_tf_type',true);
  $term = get_term_by('id',$widget_id,'wpsc_ticket_custom_fields');

  foreach ($tickets as $ticket) {
    if ($wpsc_tf_type == '3') {
      $values = $wpscfunction->get_ticket_meta($ticket->id,$custom_field->slug);
      if(!empty($values)){ 
        foreach ($values as $value) {
          $cust_vals[] = $value;
        }
      }
    }elseif($wpsc_tf_type == '2' || $wpsc_tf_type =='4'){
      $values = $wpscfunction->get_ticket_meta($ticket->id,$custom_field->slug,true);
      if($values){
        $cust_vals[]= $values;
      } 
    }
    if($term->slug == 'ticket_category'){
      $category_array[] =  $wpscfunction->get_ticket_fields($ticket->id,'ticket_category');
    }
  }
  if(!empty($cust_vals))
    $custdata_array[$widget_id] = array_count_values($cust_vals);
}

foreach ($categories as $category) {
  if( !empty($category_array)){
    $gcategory_data_name[] = '"'.$category->name.'"';
    $gcategory_data_count[] = count(array_keys($category_array, $category->term_id));
  }
} 

$days = '7 days';
