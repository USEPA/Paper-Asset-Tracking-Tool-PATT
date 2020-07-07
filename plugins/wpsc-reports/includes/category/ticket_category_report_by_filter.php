<?php 
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

global $wpscfunction,$post,$current_user;

$date_filter = isset($_POST['date_filter']) ? sanitize_text_field($_POST['date_filter']) : '';
update_user_meta($current_user->ID, 'wpsc_report_filter', $date_filter);

if($date_filter == 'last7days'){

  include_once WPSC_RP_ABSPATH . 'includes/category/report_tc_last_7.php';


}else if($date_filter == 'last30days'){
  
  include_once( WPSC_RP_ABSPATH . 'includes/category/report_tc_last_30.php' );

}else if($date_filter == 'lastmonth'){
  
  include_once( WPSC_RP_ABSPATH . 'includes/category/report_tc_last_month.php' );
  
}else if($date_filter == 'lastquarter'){
  
  include_once( WPSC_RP_ABSPATH . 'includes/category/report_tc_last_quarter.php' );
  
}else if($date_filter == 'thisyear') {
  
  include_once( WPSC_RP_ABSPATH . 'includes/category/report_tc_this_year.php' );
  
}else if($date_filter == 'customdate') {
  
  include_once( WPSC_RP_ABSPATH . 'includes/category/report_tc_custom_date.php' );
  
}  
?>
<?php 
if(array_sum($gcategory_data_count)==0){
  ?>
  <div style="padding:20px; text-align:center;font-size: 20px; " > <?php _e('No data found!', 'wpsc-rp')?> </div>
  <?php
}else{
?>
  <div style="width:100%;">
  <canvas id="category"></canvas>
</div>

<script>
  //ticket category bar graph js
  var dynamicColors = function() {
    var r = Math.floor(Math.random() * 255);
    var g = Math.floor(Math.random() * 255);
    var b = Math.floor(Math.random() * 255);
    return "rgb(" + r + "," + g + "," + b + ")";
  };
  
  var count = <?php echo count($gcategory_data_name) ?>;
  var color_array = new Array();
  for(i=0;i<count;i++){
    color_array.push(dynamicColors());
  }
  
  var config_category = {
    type: 'horizontalBar',
    data: {
      datasets: [{
        data: [<?php echo implode(',', $gcategory_data_count)?>],
        backgroundColor: color_array,
        label: 'Dataset 1'
      }],
      labels: [<?php echo implode(',', $gcategory_data_name)?>]
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
    var category = document.getElementById('category').getContext('2d');
    window.myHorizontalBar = new Chart(category, config_category);
  });
  </script>
<?php  
}



  