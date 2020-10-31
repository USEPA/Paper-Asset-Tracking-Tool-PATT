<?php 
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

global $wpscfunction, $post, $current_user;

$date_filter = isset($_POST['date_filter']) ? sanitize_text_field($_POST['date_filter']) : '';
update_user_meta($current_user->ID, 'wpsc_report_filter', $date_filter);



if($date_filter == 'last7days' ){

  include_once( WPSC_SLA_ABSPATH . 'includes/sla_graph/report_sla_last_7.php' );

}else if($date_filter == 'last30days'){
  
  include_once( WPSC_SLA_ABSPATH . 'includes/sla_graph/report_sla_last_30.php' );

}else if($date_filter == 'lastmonth'){
  
  include_once( WPSC_SLA_ABSPATH . 'includes/sla_graph/report_sla_last_month.php' );
  
}else if($date_filter == 'lastquarter'){
  
  include_once( WPSC_SLA_ABSPATH . 'includes/sla_graph/report_sla_last_quarter.php' );
  
}else if($date_filter == 'thisyear') {
  
  include_once( WPSC_SLA_ABSPATH . 'includes/sla_graph/report_sla_this_year.php' );
  
}else if($date_filter == 'customdate') {
  
  include_once( WPSC_SLA_ABSPATH . 'includes/sla_graph/report_sla_custom_date.php' );
  
} 
  //require_once(WPSC_RP_ABSPATH .  ) 
?>
<div style="width:100%;">
  <canvas id="overdue_tickets"></canvas>
</div>

<div style="padding: 10px;border: 1px solid #ccc;text-align: center;margin: 20px 10px 20px 20px">
 <strong><?php echo sprintf(__('Total number of overdue tickets for period shown: %1$s','wpsc-sla'),$total_tickets)?></strong>
</div>

<script>

var config = {
  type: 'line',
  data: {
    labels: [<?php echo $glabel?>],
    fill: false,
    
    datasets: [{
      label: 'Sla Overdue Tickets',
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
          labelString: 'Overdue Tickets'
        }
      }]
    }
  }
};

var ctx = document.getElementById('overdue_tickets').getContext('2d');
window.myLine = new Chart(ctx, config);
</script>
