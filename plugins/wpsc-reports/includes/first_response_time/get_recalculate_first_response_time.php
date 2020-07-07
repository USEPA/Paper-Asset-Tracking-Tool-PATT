<?php 
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

global $wpscfunction,$post,$total_avg;
$custom_filter = isset($_POST['custom_filter']) && is_array($_POST['custom_filter']) ? $wpscfunction->sanitize_array($_POST['custom_filter']) : array();
$date_filter   = isset($_POST['date_filter']) ? sanitize_text_field( $_POST['date_filter'] ) : '';

if($date_filter == 'last7days'){
  
  include_once WPSC_RP_ABSPATH . 'includes/first_response_time/recalculate_frt/recalculate_report_fr_last_7.php';

}else if($date_filter == 'last30days'){
  
  include_once( WPSC_RP_ABSPATH . 'includes/first_response_time/recalculate_frt/recalculate_report_fr_last_30.php' );

}else if($date_filter == 'lastmonth'){
  
  include_once( WPSC_RP_ABSPATH . 'includes/first_response_time/recalculate_frt/recalculate_report_fr_last_month.php' );
  
}else if($date_filter == 'lastquarter'){
  
  include_once( WPSC_RP_ABSPATH . 'includes/first_response_time/recalculate_frt/recalculate_report_fr_last_quarter.php' );
  
}else if($date_filter == 'thisyear') {
  
  include_once( WPSC_RP_ABSPATH . 'includes/first_response_time/recalculate_frt/recalculate_report_fr_this_year.php' );
  
}else if($date_filter == 'customdate') {
  
  include_once( WPSC_RP_ABSPATH . 'includes/first_response_time/recalculate_frt/recalculate_report_fr_custom_date.php' );
  
}