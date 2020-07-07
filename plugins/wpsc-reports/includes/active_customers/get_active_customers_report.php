<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}
 
global $wpscfunction,$current_user,$wpscfunc,$wpdb;

$filter = $wpscfunc->get_current_filter();

?>
<div id="wpsc_active_customers"  class="row table-responsive">
  <form id="frm_active_customers_reports" class="" action="" method="post">
  	<div id="wpsc_report_date_filter" style="border:1px solid #ccc;padding:5px;border-radius:2px;">
      <div class="row">
        <div class="col-md-3">
         <div class="form-group">
             <label for="wpsc_ticket_stats_month_filters"><strong><?php _e('Active Customers','wpsc-rp');?></strong></label></br>
            <input type="text" id="wpsc_active_customer_search" class="form-control" name="s" value="<?php echo $filter['s'] ?>" autocomplete="off" placeholder="<?php _e('Search','wpsc-rp')?>">
         </div>
      </div>
       
       <div class="col-md-4">
           <button onclick="get_active_customers_filter();return false;" style="margin-top:23px;" class="btn btn-sm btn-default"><?php echo _e('Apply','wpsc-rp');?></button>
           <button onclick="set_active_customers_default_filter();return false;" style="margin-top:23px;" class="btn btn-sm btn-default"><?php echo _e('Reset Filter','wpsc-rp');?></button>
           <?php  do_action('wpsc_report_after_active_customers') ?>
         </div>
      </div>
     
    </div>

  	<input type="hidden" name="action" value="wpsc_ticket_reports">
    <input type="hidden" id="wpsc_pg_no" name="page_no" value="<?php echo $filter['page']?>">
    <input type="hidden" name="setting_action" value="active_customers">

  </form></br></br>
  <?php include WPSC_RP_ABSPATH . 'includes/active_customers/get_active_customers_list.php'; ?>
</div>

	

