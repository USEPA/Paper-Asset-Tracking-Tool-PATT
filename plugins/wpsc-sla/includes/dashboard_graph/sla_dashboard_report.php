<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

global $wpscfunction,$current_user,$wpdb;

$wpsc_dashboard_report_filters = get_option('wpsc_dashboard_report_filters ' );

if($wpsc_dashboard_report_filters == 'last7days'){
  
  include_once( WPSC_SLA_ABSPATH . 'includes/dashboard_graph/report_dash_last_7.php' );
  
}elseif ($wpsc_dashboard_report_filters == 'last30days') {
  
  include_once( WPSC_SLA_ABSPATH . 'includes/dashboard_graph/report_dash_last_30.php' );
  
}elseif ($wpsc_dashboard_report_filters == 'lastmonth') {

include_once( WPSC_SLA_ABSPATH . 'includes/dashboard_graph/report_dash_last_month.php' );

}elseif ($wpsc_dashboard_report_filters == 'lastquarter') {
  
  include_once( WPSC_SLA_ABSPATH . 'includes/dashboard_graph/report_dash_last_quarter.php' );
}

?>

<div class="col-md-4">
    <div class="dashboard">
      <div class="wpsc_dashboard_count">
        <div class="wpsc_dashboard_ticket_count" ><?php echo $overdue_tickets ?></div>
        <div class="wpsc_dashboard_ticket_percentage" >
        <?php 
          if ($overdue_graph == 'increasing') {
            echo '<i class="fas fa-arrow-up"></i>';
          } elseif ($overdue_graph == 'decreasing') {
            echo '<i class="fas fa-arrow-down"></i>';
          } else {
            echo '';
          }
        ?>
        <?php echo $overdue_percentage? abs($overdue_percentage) . ' Tickets ':'';?>
      </div>
      </div>
      <div style="height:50px" class="wpsc_dashboard_ticket_text" ><?php _e('Overdue tickets','wpsc-rp') ?>
        <div class="wpsc_dashboard_days_text" ><?php _e('in last '.$days , 'wpsc-rp') ?></div>
      </div>
      <div class="wpsc_dashboard_button wpsc_no_tickets_wid_btn" >
        <button type="button" id="" onclick="get_sla_overdue_report();" class="btn btn-sm"><?php  _e('View More','wpsc-rp' )?></button>
      </div>
    </div>
</div>
