<?php 
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

global $wpscfunction,$post,$total_avg;
$custom_filter = isset($_POST['custom_filter']) && is_array($_POST['custom_filter']) ? $wpscfunction->sanitize_array($_POST['custom_filter']) : array();
$date_filter   = isset($_POST['date_filter']) ? sanitize_text_field( $_POST['date_filter'] ) : '';

if($date_filter == 'last7days'){
  
  include_once WPSC_RP_ABSPATH . 'includes/ticket_statistics/recalculate_ticket_stats/recalculate_report_ts_last_7.php';

}else if($date_filter == 'last30days'){
  
  include_once( WPSC_RP_ABSPATH . 'includes/ticket_statistics/recalculate_ticket_stats/recalculate_report_ts_last_30.php' );

}else if($date_filter == 'lastmonth'){
  
  include_once( WPSC_RP_ABSPATH . 'includes/ticket_statistics/recalculate_ticket_stats/recalculate_report_ts_last_month.php' );
  
}else if($date_filter == 'lastquarter'){
  
  include_once( WPSC_RP_ABSPATH . 'includes/ticket_statistics/recalculate_ticket_stats/recalculate_report_ts_last_quarter.php' );
  
}else if($date_filter == 'thisyear') {
  
  include_once( WPSC_RP_ABSPATH . 'includes/ticket_statistics/recalculate_ticket_stats/recalculate_report_ts_this_year.php' );
  
}else if($date_filter == 'customdate') {
  
  include_once( WPSC_RP_ABSPATH . 'includes/ticket_statistics/recalculate_ticket_stats/recalculate_report_ts_custom_date.php' );
  
}
