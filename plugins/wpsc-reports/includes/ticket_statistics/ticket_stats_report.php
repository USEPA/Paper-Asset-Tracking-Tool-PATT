<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
} 

global $wpscfunction,$current_user,$wpdb;

$last_insert_date = get_option('wpsc_ticket_stats_checked_date');

$total = $wpdb->get_var("SELECT COUNT(t.id) FROM {$wpdb->prefix}wpsc_ticket t WHERE DATE(date_created) <> CURDATE() AND t.id NOT IN (SELECT  DISTINCT tm.ticket_id FROM {$wpdb->prefix}wpsc_ticketmeta tm
WHERE  tm.meta_key = 'ticket_counted')");

$usermeta = get_user_meta($current_user->ID, 'wpsc_report_filter', true);

?>

<form id="frm_reports_filters" class="" action="" method="post">
  <?php 
   if ($total > 0 ) {
    ?>
     <div class="row" style="padding:10px;background-color:#EB9316;margin-bottom:10px;border-radius:4px;" id="upgrade_tickets_data">
       <div style="font-size:18px; color:#fff;">
         <span id="wpsc_upgrade_tickets_complete_percentage"><?php echo __('Database upgrade required for existing tickets!', 'wpsc-rp') ?></span><br>
         <button style="margin-top:10px;" type="button" id="upgrade_tickets_btn" onclick="upgrade_tickets_data('1',<?php echo $total ?>);" class="btn btn-sm btn-default"><?php _e('Upgrade Now', 'wpsc-rp');?></button>
       </div>
     </div>
   <?php
   }
  ?>
	 <div style="border:1px solid #ccc;padding:5px;border-radius:2px;"> 
     <div class="row">
       <div class="col-md-3">
  	       <div class="form-group">
  	          <label for="wpsc_ticket_stats_month_filters"><strong><?php _e('Report Duration','wpsc-rp');?></strong></label></br>
  	          
  	          <select id="wpsc_ticket_stats_month_filters" class="form-control" name="wpsc_ticket_stats_month_filters" >
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
  	   <div class="col-md-2 wpsc_ticket_stats_custom_date_filter" style="display:none;">
  	     <div class="form-group">
  	       <label><strong><?php _e('From Date','wpsc-rp');?></strong></label><br>
  	       <input type="text" class="form-control" style="padding:0px;" autocomplete="false" value="" readonly="true" id="wpsc_ticket_stats_custom_date_start" name="wpsc_ticket_stats_custom_date_start"/> 
  	     </div>
  	   </div>
  	   <div class="col-md-2 wpsc_ticket_stats_custom_date_filter" style="display:none;">
  	     <div class="form-group">
  	       <label><strong><?php _e('To Date','wpsc-rp');?></strong></label><br>
  	       <input type="text" class="form-control" style="padding:0px;" autocomplete="false" readonly="true" value="" id="wpsc_ticket_stats_custom_date_end" name="wpsc_ticket_stats_custom_date_end"/>
  	     </div>
  	   </div>
  	   <div class="col-md-4">
          <button onclick="wpsc_ticket_stat_report_graph();return false;" style="margin-top:23px;" class="btn btn-sm btn-default"><?php echo _e('Apply','wpsc-rp');?></button>
          <button onclick="wpsc_get_recalculate_ticket_stats();return false;" style="margin-top:23px;" class="btn btn-sm btn-default"><?php echo _e('Recalculate','wpsc-rp');?></button>
          <button id="printChart" onclick="wpsc_print_ticket_stats();return false;" style="margin-top:23px;" class="btn btn-sm btn-default"><?php echo _e('Print','wpsc-rp');?></button>
        </div>
     </div>
  
     <input type="hidden" name="action" value="wpsc_ticket_reports">
     <input type="hidden" name="setting_action" value="ticket_statistics">
   </div>
</form>

<div id="wpsc_all_graph"></div>
	
<script type="text/javascript">
  jQuery(document).ready(function(){
    
    wpsc_ticket_stat_report_graph();
    
    jQuery( "#wpsc_ticket_stats_custom_date_start" ).datepicker({
        dateFormat : 'yy-mm-dd',
        showAnim : 'slideDown',
        changeMonth: true,
        changeYear: true,
        yearRange: "-50:+50",
    });
    
    jQuery("#wpsc_ticket_stats_custom_date_end").datepicker({
          dateFormat: 'yy-mm-dd',
          showAnim : 'slideDown',
          changeMonth: true,
          changeYear: true,
          yearRange: "-50:+50"
    });
      
    jQuery('#wpsc_ticket_stats_month_filters').change(function (){
      var seloption=jQuery(this).val();
      if(seloption == 'customdate'){
        jQuery(".wpsc_ticket_stats_custom_date_filter").show();
      }else{
        jQuery(".wpsc_ticket_stats_custom_date_filter").hide();
      }
    })

    <?php 
    if($usermeta == 'customdate'){
      ?>
         jQuery(".wpsc_ticket_stats_custom_date_filter").show();
      <?php
    }else{
      ?>
        jQuery(".wpsc_ticket_stats_custom_date_filter").hide();
      <?php
    }
   ?>
    
  });
  
  function wpsc_print_ticket_stats(){
    var canvas        = document.getElementById("ticket_stats");
    var myWindow      = window.open('','','width=500,height=400');
    var duration      = document.getElementById("wpsc_ticket_stats_month_filters").value;;
    var total_tickets = document.getElementById("total_tickets").value;
    var start_date    = document.getElementById("start_date").value;
    var end_date      = document.getElementById("end_date").value;
    
    total_tickets = 'Total tickets : ' + total_tickets ;
    
    var message =  'Report duration : ' + start_date + '  to  ' + end_date;
    setTimeout(function(){ myWindow.document.write("<div style='text-align:center; color:#808080;'>" + "<h4> Ticket Statistics</h4>" +"</div>" ); }, 1000);  
    setTimeout(function(){ myWindow.document.write("<div style='text-align:center; color:#808080;'> <small>" + message +"</small>" + "\n\n" + "</div>" ); }, 1000);  
    setTimeout(function(){ myWindow.document.write("<div style='text-align:center; color:#808080;'> <small>" + total_tickets + "</small>" + "\n\n" +  "</div>" ); }, 1000);  
    setTimeout(function(){ myWindow.document.write("<br><img src='" + canvas.toDataURL() + "'/>"); }, 1000);  
    setTimeout(function(){ myWindow.print(); }, 1000);
    setTimeout(function(){ myWindow.close(); }, 1000);
  } 
  
</script>


<script>
function upgrade_tickets_data(page,total_result) {
  jQuery('#upgrade_tickets_btn').hide();
  wpsc_get_upgrade_tickets(page,total_result);
}

function wpsc_get_upgrade_tickets(page,total_result){
  var data = {
    action: 'get_upgrade_ticket',
    page :  page,
    total_result : total_result
  };
  jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
    var response = JSON.parse(response_str);
    if( response.is_next == 1 ){
          jQuery('#wpsc_upgrade_tickets_complete_percentage').html(response.percentage+'% completed. Please wait!!');
          wpsc_get_upgrade_tickets(response.page,total_result);
    
    } else {
          jQuery('#wpsc_upgrade_tickets_complete_percentage').html('Upgrade successful !');
          setTimeout(function(){ jQuery('#upgrade_tickets_data').slideUp('fast',function(){}); }, 1000);
          wpsc_ticket_stat_report_graph();
      }
    });

}
</script>


