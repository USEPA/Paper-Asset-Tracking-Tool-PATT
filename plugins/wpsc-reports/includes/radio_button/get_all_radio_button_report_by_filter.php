<?php 
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

global $wpscfunction, $post, $current_user;


$date_filter = isset($_POST['date_filter']) ? sanitize_text_field($_POST['date_filter']) : '';
update_user_meta($current_user->ID, 'wpsc_report_filter', $date_filter);


if($date_filter == 'last7days'){
    
  include_once WPSC_RP_ABSPATH . 'includes/radio_button/report_trb_last_7.php';

}else if($date_filter == 'last30days'){
  
  include_once( WPSC_RP_ABSPATH . 'includes/radio_button/report_trb_last_30.php' );

}else if($date_filter == 'lastmonth'){
  
  include_once( WPSC_RP_ABSPATH . 'includes/radio_button/report_trb_last_month.php' );
  
}else if($date_filter == 'lastquarter'){
  
  include_once( WPSC_RP_ABSPATH . 'includes/radio_button/report_trb_last_quarter.php' );
  
}else if($date_filter == 'thisyear') {
  
  include_once( WPSC_RP_ABSPATH . 'includes/radio_button/report_trb_this_year.php' );
  
}else if($date_filter == 'customdate') {
  
  include_once( WPSC_RP_ABSPATH . 'includes/radio_button/report_trb_custom_date.php' );
  
}  
?>

<?php 

$sumArray = array();

foreach ($custdata_array as $k => $subArray) {
    foreach ($subArray as $id => $value) {
        $sumArray[] += $value;
    }
}

if(array_sum($sumArray) == 0){
  ?>
  <div style="padding:20px; text-align:center;font-size: 20px; " > <?php _e('No data found!','wpsc-rp') ?> </div>
  <?php
}else{
    foreach ($custdata_array as $custkey => $custom) {
      $cfterm = get_term($custkey);
      $gname  = array();
      $gcount = array();
      
      foreach ($custom as $key => $value) {
        $gname[]= "'".$key."'";
        $gcount[] = $value;
      }
    ?>
      
      <div style="width:100%;">
        <canvas id="ticket_radio_button"></canvas>
      </div>
      
      <script>
      //dropdwon bar graph
      var dynamicColors = function() {
        var r = Math.floor(Math.random() * 255);
        var g = Math.floor(Math.random() * 255);
        var b = Math.floor(Math.random() * 255);
        return "rgb(" + r + "," + g + "," + b + ")";
      };
      
      var count = <?php echo count($gname) ?>;
        var cust_color_array = new Array();
        for(i=0;i<count;i++){
          cust_color_array.push(dynamicColors());
        }

      
      var config = {
        type: 'horizontalBar',
        data: {
          datasets: [{
            data: [<?php echo implode(',', $gcount)?>],
            backgroundColor: cust_color_array,
            label: 'Dataset 1'
          }],
          labels: [<?php echo implode(',', $gname)?>]
        },
        options: {
          responsive: true,
          title: {
            display: true,
            text: ''
          },
          legend: {
            display: false
          },
          tooltips: {
            enabled: true
          },
          scales: {
            xAxes: [{
                ticks: {
                    min:0
                }
            }]
          }
        }
      };
      jQuery(document).ready(function() {
        var radio_button = document.getElementById('ticket_radio_button').getContext('2d');
        window.myHorizontalBar = new Chart(radio_button, config);
      });
      </script>
    <?php
    }
}     