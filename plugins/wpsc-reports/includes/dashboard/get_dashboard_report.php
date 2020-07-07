<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

global $wpscfunction,$current_user,$wpdb;

$wpsc_dashboard_report_filters = get_option('wpsc_dashboard_report_filters ' );

if($wpsc_dashboard_report_filters == 'last7days'){
  
  include_once( WPSC_RP_ABSPATH . 'includes/dashboard/report_dash_last_7.php' );
  
}elseif ($wpsc_dashboard_report_filters == 'last30days') {
  
  include_once( WPSC_RP_ABSPATH . 'includes/dashboard/report_dash_last_30.php' );
  
}elseif ($wpsc_dashboard_report_filters == 'lastmonth') {

include_once( WPSC_RP_ABSPATH . 'includes/dashboard/report_dash_last_month.php' );

}elseif ($wpsc_dashboard_report_filters == 'lastquarter') {
  
  include_once( WPSC_RP_ABSPATH . 'includes/dashboard/report_dash_last_quarter.php' );
}

?>
<div id="wpsc_dashboard_report">
<div class="col-md-4">
    <div class="dashboard ">
      <div class="wpsc_dashboard_count">
        <div class="wpsc_dashboard_ticket_count" ><?php echo $no_of_tickets ?></div>
        <div class="wpsc_dashboard_ticket_percentage" >
        <?php 
          if($ticket_graph == 'increasing'){
            echo '<i class="fas fa-arrow-up"></i>';
          }elseif($ticket_graph=='decreasing'){
            echo '<i class="fas fa-arrow-down"></i>';
          }else{
            echo '';
          }  
        ?>  
        <?php echo $no_of_tic_percentage ? abs($no_of_tic_percentage).' Tickets':'';?>
      </div>
    </div>
      <div  style="height:50px;" class="wpsc_dashboard_ticket_text" ><?php _e('New tickets','wpsc-rp') ?>
        <div class="wpsc_dashboard_days_text" ><?php _e('in last '.$days, 'wpsc-rp') ?></div>
      </div>
      <div class="wpsc_dashboard_button wpsc_no_tickets_wid_btn" >
        <button type="button" id="" onclick="get_ticket_stats_report();" class="btn btn-sm"><?php  _e('View More','wpsc-rp' )?></button>
      </div>
    </div>
  </div>


  <div class="col-md-4">
    <div class="dashboard">
      <div class="wpsc_dashboard_count">
        <div class="wpsc_dashboard_ticket_count" ><?php echo $average  ?  (abs($frt_hour) .'  <span style="font-size:10px;">Hrs<span>  ' . abs($frt_minute) . ' <span style="font-size:10px;">Mins<span>' ):''?></div>
        <div class="wpsc_dashboard_ticket_percentage" >
          <?php 
            if($frt_graph == 'increasing'){
              echo '<i class="fas fa-arrow-up"></i>';
            }elseif($frt_graph=='decreasing'){
              echo '<i class="fas fa-arrow-down"></i>';
            }else{
              echo '';
            }  
          ?> 
          <?php echo $frt_percentage ? (abs($hour) .' Hrs ' . abs($minute) . ' Min')  :'';?>
        </div>
      </div>
      <div style="height:50px;" class="wpsc_dashboard_ticket_text" ><?php _e('First response time','wpsc-rp') ?>
          <div class="wpsc_dashboard_days_text" ><?php _e('in last '.$days, 'wpsc-rp') ?></div>
      </div>
      <div class="wpsc_dashboard_button wpsc_no_tickets_wid_btn" >
        <button type="button" id="" onclick="get_first_response_time_reports();" class="btn btn-sm"><?php  _e('View More','wpsc-rp' )?></button>
      </div>
    </div>
  </div>
   <?php do_action('wpsc_after_dashboard_report') ?>
   <div style="clear:both"></div>
</div>

<?php 
if( !empty($gcategory_data_count)){
  ?>
  <div id="category_pie_chart" class="col-md-4" style="margin-top:10px">
    <div class="wpsc_report_dash_wid wpsc_pie_chart_widgets">
    <canvas id="category-area" width="300px" height="300px"></canvas>
    </div>
  </div>
  <script>
  //ticket category pie chart js
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
    type: 'pie',
    data: {
      datasets: [{
        data: [<?php echo implode(',', $gcategory_data_count)?>],
        backgroundColor: color_array,
        label: ''
      }],
      labels: [<?php echo implode(',', $gcategory_data_name)?>]
    },
    options: {
      responsive: true,
      title: {
        display: true,
        text: 'Tickets Category'
      },
      legend: {
        display: false
      },
      tooltips: {
        enabled: true
      }
    }
  };
  jQuery(document).ready(function() {
    var category = document.getElementById('category-area').getContext('2d');
    window.myPie = new Chart(category, config_category);
  });
  </script>
<?php 
}  
?>

<?php 
if($custdata_array){
  foreach ($custdata_array as $custkey => $custom) {
    
    $cfterm = get_term($custkey);
    $gname = array();
    $gcount = array();
    foreach ($custom as $key => $value) {
      $gname[]= "'".$key."'";
      $gcount[] = $value;
    }
    ?>
    <div class="col-md-4" style="margin-top:10px" >
      <div class="wpsc_report_dash_wid wpsc_pie_chart_widgets">
         <?php echo "<canvas id='wpsp_cust_graph_".$custkey."' width='300px' height='300px'></canvas>"; ?>
       </div>
    </div>
    <script>
    var count = <?php echo count($gname) ?>;
    var cust_color_array = new Array();
    for(i=0;i<count;i++){
      cust_color_array.push(dynamicColors());
    }
    var cust_graph = {
      type: 'pie',
      data: {
        datasets: [{
          data: [<?php echo implode(',', $gcount)?>],
          backgroundColor:cust_color_array,
          label: ''
        }],
        labels: [<?php echo implode(',', $gname)?>]
      },
      options: {
        responsive: true,
        title: {
          display: true,
          text: '<?php echo $cfterm->name; ?>'
        },
        legend: {
          display: false
        },
        tooltips: {
          enabled: true
        }
      }
    };
    jQuery(document).ready(function() {
      var graph = document.getElementById('<?php echo 'wpsp_cust_graph_'.$custkey ?>').getContext('2d');
      window.myPie = new Chart(graph, cust_graph);
    });
    </script>
    <?php
  }
}

?> 
 
 <?php do_action('wpsc_after_custom_fields_pie_chart') ?>






  