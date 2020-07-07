<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

global $wpscfunction,$current_user,$wpdb;

$total = $wpdb->get_var("SELECT COUNT(t.id) FROM {$wpdb->prefix}wpsc_ticket t WHERE DATE(date_created) <> CURDATE() AND t.id NOT IN (SELECT  DISTINCT tm.ticket_id FROM {$wpdb->prefix}wpsc_ticketmeta tm
WHERE  tm.meta_key = 'frt_checked')");

$usermeta = get_user_meta($current_user->ID, 'wpsc_report_filter', true);

?>

 <form id="frm_first_response_time_filters" class="" action="" method="post">

   <?php
   if($total > 0){
   ?>
     <div class="row" style="padding:10px;background-color:#EB9316;margin-bottom:10px;border-radius:4px;" id="upgrade_first_response_data">
       <div style="font-size:18px; color:#fff;">
         <span id="wpsc_upgrade_complete_percentage"><?php echo __('Database upgrade required for existing tickets to use First Response Time  !','wpsc-rp')?></span><br>
         <button style="margin-top:10px;" type="button" id="wpsc_upgrade_frt_btn" onclick="upgrade_first_response_data('1',<?php echo $total?>);" class="btn btn-sm btn-default"><?php _e('Upgrade Now','wpsc-rp');?></button>
       </div>
     </div>
   <?php
   }
   ?>
   <div id="wpsp_report_date_filter" style="border:1px solid #ccc;padding:5px;border-radius:2px;">
    <div class="row">
       <div class="col-md-3">
          <div class="form-group">
            <label for="wpsc_first_response_time_month_filters"><strong><?php _e('Report Duration','wpsc-rp');?></strong></label></br>
            <select id="wpsc_first_response_time_month_filters" class="form-control" name="wpsc_first_response_time_month_filters">
              <?php  
              $selected = $usermeta == 'last7days' ? 'selected="selected"' : '';
              echo '<option ' . $selected . ' value="last7days">' . __('Last 7 Days', 'supportcandy') . '</option>';
              
              $selected = $usermeta == 'last30days' ? 'selected="selected"' : '';
              echo '<option ' . $selected . ' value="last30days">' . __('Last 30 Days', 'supportcandy') . '</option>';
              
              $selected = $usermeta == 'lastmonth' ? 'selected="selected"' : '';
              echo '<option ' . $selected . ' value="lastmonth">' . __('Last Month', 'supportcandy') . '</option>';

              $selected = $usermeta == 'lastquarter' ? 'selected="selected"' : '';
              echo '<option ' . $selected . ' value="lastquarter">' . __('Last Quarter', 'supportcandy') . '</option>';
              
              $selected = $usermeta == 'thisyear' ? 'selected="selected"' : '';
              echo '<option ' . $selected . ' value="thisyear">' . __('This Year', 'supportcandy') . '</option>';

              $selected = $usermeta == 'customdate' ? 'selected="selected"' : '';
              echo '<option ' . $selected . ' value="customdate">' . __('Custom Date', 'supportcandy') . '</option>';
              ?>
             
            </select>
          </div>
        </div>
        <div class="col-md-2 wpsc_first_response_time_custom_date_filter" style="display:none;">
          <div class="form-group">
            <label><strong><?php _e('From Date:','wpsc-rp');?></strong></label><br>
            <input type="text" autocomplete="false" value="" class="form-control" readonly="true" id="wpsc_first_response_time_custom_date_start" name="wpsc_first_response_time_custom_date_start"/>
          </div>
        </div>
        <div class="col-md-2 wpsc_first_response_time_custom_date_filter" style="display:none;">
          <div class="form-group">
            <label><strong><?php _e('To Date:','wpsc-rp');?></strong></label><br>
            <input type="text" autocomplete="false" readonly="true" value="" class="form-control" id="wpsc_first_response_time_custom_date_end" name="wpsc_first_response_time_custom_date_end"/>
          </div>
        </div>
        <div class="col-md-4">
          <button onclick="wpsc_get_first_response_time_reports_graph();return false;" style="margin-top:23px;" class="btn btn-sm btn-default"><?php echo _e('Apply','wpsc-rp');?></button>
          <button onclick="wpsc_get_recalculate_first_response_time();return false;" style="margin-top:23px;" class="btn btn-sm btn-default"><?php echo _e('Recalculate','wpsc-rp');?></button>
          <button id="printChart" onclick="wpsc_print_frt_reprors();return false;" style="margin-top:23px;" class="btn btn-sm btn-default"><?php echo _e('Print','wpsc-rp');?></button>
        </div>
      </div>
    </div>

    <input type="hidden" name="action" value="wpsc_ticket_reports">
    <input type="hidden" name="setting_action" value="first_response_time">
</form>

<div id="wpsc_first_response_time_graph"></div>

<script type="text/javascript">
  jQuery(document).ready(function(){
    wpsc_get_first_response_time_reports_graph();
    jQuery( "#wpsc_first_response_time_custom_date_start" ).datepicker({
        dateFormat : 'yy-mm-dd',
        showAnim : 'slideDown',
        changeMonth: true,
        changeYear: true,
        yearRange: "-50:+50",
    });
    jQuery("#wpsc_first_response_time_custom_date_end").datepicker({
          dateFormat: 'yy-mm-dd',
          showAnim : 'slideDown',
          changeMonth: true,
          changeYear: true,
          yearRange: "-50:+50",
      });


    jQuery('#wpsc_first_response_time_month_filters').change(function (){
      var seloption=jQuery(this).val();
      if(seloption=='customdate'){
        jQuery('.wpsc_first_response_time_custom_date_filter').show();
      }else{
        jQuery('.wpsc_first_response_time_custom_date_filter').hide();
      }
    })

    <?php 
    if($usermeta == 'customdate'){
      ?>
         jQuery(".wpsc_first_response_time_custom_date_filter").show();
      <?php
    }else{
      ?>
        jQuery(".wpsc_first_response_time_custom_date_filter").hide();
      <?php
    }
   ?>
  });
  
  function wpsc_print_frt_reprors(){
    var canvas      = document.getElementById("frt_reports");
    var myWindow    = window.open('','','width=500,height=400');
    var duration    = document.getElementById("wpsc_first_response_time_month_filters").value;;
    
    var average_frt_hour = document.getElementById("average_frt_hour").value;
    var average_frt_min = document.getElementById("average_frt_min").value;

    var start_date  = document.getElementById("start_date").value;
    var end_date    = document.getElementById("end_date").value;
    
    var average_frt = 'Average response time :' + average_frt_hour + ' Hrs  ' + average_frt_min + ' mins.';
    
    var message =  'Report duration : ' + start_date + '  to  ' + end_date;
    setTimeout(function(){ myWindow.document.write("<div style='text-align:center; color:#808080;'>" + "<h4>First Response Time</h4>" +"</div>" ); }, 1000);  
    setTimeout(function(){ myWindow.document.write("<div style='text-align:center; color:#808080;'> <small>" + message +"</small>" + "\n\n" + "</div>" ); }, 1000);  
    setTimeout(function(){ myWindow.document.write("<div style='text-align:center; color:#808080;'> <small>" + average_frt + "</small>" + "\n\n" +  "</div>" ); }, 1000);  
    setTimeout(function(){ myWindow.document.write("<br><img src='" + canvas.toDataURL() + "'/>"); }, 1000);  
    
    setTimeout(function(){ myWindow.print(); }, 1000);
    setTimeout(function(){ myWindow.close(); }, 1000);
  } 
</script>

<script>
function upgrade_first_response_data(page,total_result) {
  jQuery('#wpsc_upgrade_frt_btn').hide();
  wpsc_get_upgrade_frt(page,total_result);
}

function wpsc_get_upgrade_frt(page,total_result){
  var data = {
    action: 'get_upgrade_frt',
    page :  page,
    total_result : total_result
  };
  jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
    var response = JSON.parse(response_str);
    if( response.is_next == 1 ){
          jQuery('#wpsc_upgrade_complete_percentage').html(response.percentage+'% completed. Please wait!!');
          wpsc_get_upgrade_frt(response.page,total_result);
    
    } else {
          jQuery('#wpsc_upgrade_complete_percentage').html('Upgrade successful !');
          jQuery('#wpsc_upgrade_sla_btn').hide();
          setTimeout(function(){ jQuery('#upgrade_first_response_data').slideUp('fast',function(){}); }, 1000);
          wpsc_get_first_response_time_reports_graph();
      }
    });

}
</script>
