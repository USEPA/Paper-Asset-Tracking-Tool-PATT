<?php 
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

global $wpscfunction, $post, $current_user;

$date_filter = isset($_POST['date_filter']) ? sanitize_text_field($_POST['date_filter']) : '';
update_user_meta($current_user->ID, 'wpsc_report_filter', $date_filter);

if($date_filter == 'last7days'){
  
  include_once WPSC_RP_ABSPATH . 'includes/ticket_statistics/report_ts_last_7.php';

}else if($date_filter == 'last30days'){
  
  include_once( WPSC_RP_ABSPATH . 'includes/ticket_statistics/report_ts_last_30.php' );

}else if($date_filter == 'lastmonth'){
  
  include_once( WPSC_RP_ABSPATH . 'includes/ticket_statistics/report_ts_last_month.php' );
  
}else if($date_filter == 'lastquarter'){
  
  include_once( WPSC_RP_ABSPATH . 'includes/ticket_statistics/report_ts_last_quarter.php' );
  
}else if($date_filter == 'thisyear') {
  
  include_once( WPSC_RP_ABSPATH . 'includes/ticket_statistics/report_ts_this_year.php' );
  
}else if($date_filter == 'customdate') {
  
  include_once( WPSC_RP_ABSPATH . 'includes/ticket_statistics/report_ts_custom_date.php' );
  
}   
?>
<div style="width:100%;">
  <canvas id="ticket_stats"></canvas>
</div>
<div style="padding: 10px;border: 1px solid #ccc;text-align: center;margin: 20px 10px 20px 20px">
 <strong><?php echo sprintf(__('Total number of tickets for period shown: %1$s','wpsc-rp'),$total_tickets)?></strong>
</div>
<script>

var config = {
  type: 'line',
  data: {
    labels: [<?php echo $glabel?>],
    fill: false,
    
    datasets: [{
      label: 'New Ticket',
      backgroundColor: window.chartColors.blue,
      borderColor: window.chartColors.blue,
      data: [<?php echo $gvalue ?>],
      fill: false,
    }]
  },
  options: {
    responsive: true,
    title: {
      display: true,
      text: ''
    },
    tooltips: {
      mode: 'index',
      intersect: false,
    },
    legend: {
      display: false
    },
    tooltips: {
      enabled: true
    },
    hover: {
      mode: 'nearest',
      intersect: true
    },
    scales: {
      xAxes: [{
        display: true,
        scaleLabel: {
          display: true,
          labelString: 'Date'
        }
      }],
      yAxes: [{
        display: true,
        scaleLabel: {
          display: true,
          labelString: 'No of Tickets'
        },
        ticks: {
          beginAtZero: true
        },
      }]
    }
  }
};

var ctx = document.getElementById('ticket_stats').getContext('2d');
window.myLine = new Chart(ctx, config);
</script>

