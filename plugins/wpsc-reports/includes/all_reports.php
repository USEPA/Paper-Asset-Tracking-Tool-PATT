<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

global $wpscfunction,$current_user,$wpdb;

$cust_fields = get_terms([
	'taxonomy'   => 'wpsc_ticket_custom_fields',
	'hide_empty' => false,
	'orderby'    => 'meta_value_num',
	'meta_key'	 => 'wpsc_tf_load_order',
	'order'    	 => 'ASC',
	'meta_query' => array(
    'relation' =>'AND',
		array(
      'key'       => 'agentonly',
      'value'     => array(0,1),
      'compare'   => 'IN'
    ),
    array(
        'relation' => 'OR',
        array(
          'key'       => 'wpsc_tf_type',
          'value'     => '2',
          'compare'   => '='
        ),
        array(
          'key'       => 'wpsc_tf_type',
          'value'     => '3',
          'compare'   => '='
        ),
        array(
          'key'       => 'wpsc_tf_type',
          'value'     => '4',
          'compare'   => '='
        ),
      )
	),
]);
 ?>

 <div class="bootstrap-iso">

   <h3><?php _e('Reports','wpsc-rp');?>
   <a href="https://supportcandy.net/support/" class="btn btn-info" style="float:right;margin-right:1% !important;margin-top:-9px !important;"><?php _e('Need Help? Click Here!','wpsc-rp');?></a>
   </h3>

   <div class="wpsc_padding_space"></div>
   <div class="row">
     <div class="col-sm-3 wpsc_setting_col1">
        <input type="text" id="wpsc_ticket_report_search" onkeyup="SearchFunction()" class="form-control" name="" value="" autocomplete="off" placeholder="<?php _e('Search....','wpsc-rp')?>"></br>
        <ul id="wpsc_ul_filter" class="nav nav-pills nav-stacked   wpsc_setting_pills">
         <li id="wpsc_rp_dashboard_reports" role="presentation" class="active"><a href="javascript:get_dashboard_report();"><?php _e('Dashboard','wpsc-rp');?></a></li>
         <li id="wpsc_rp_new_ticket_reports" role="presentation"><a href="javascript:get_ticket_stats_report();"><?php _e('Ticket Statistics','wpsc-rp');?></a></li>
         <li id="wpsc_rp_first_response_time_reports" role="presentation" ><a href="javascript:get_first_response_time_reports();"><?php _e('First Response Time','wpsc-rp');?></a></li>
         <li id="wpsc_rp_category_reports" role="presentation"><a href="javascript:get_category_report();"><?php _e('Category','wpsc-rp');?></a></li>
         <?php 
         foreach ($cust_fields as $field) {
           $wpsc_tf_type  = get_term_meta($field->term_id, 'wpsc_tf_type', true);
           if($wpsc_tf_type == '2'){
             $label = get_term_meta($field->term_id,'wpsc_tf_label',true );
            ?>
              <li id="<?php echo $field->term_id ?>" role="presentation"><a href="javascript:get_all_dropdown_report(<?php echo $field->term_id ?>);"><?php _e($label,'wpsc-rp');?></a></li>
            <?php  
          }elseif ($wpsc_tf_type == '3') {
            $label = get_term_meta($field->term_id,'wpsc_tf_label', true);
            ?>
              <li id="<?php echo $field->term_id ?>" role="presentation"><a href="javascript:get_all_checkbox_report(<?php echo $field->term_id ?>);"><?php _e($label,'wpsc-rp');?></a></li>
            <?php
          }elseif ($wpsc_tf_type =='4') {
            $label = get_term_meta($field->term_id,'wpsc_tf_label', true);
            ?>
              <li id="<?php echo $field->term_id ?>" role="presentation"><a href="javascript:get_all_radio_button_report(<?php echo $field->term_id ?>);"><?php _e($label,'wpsc-rp');?></a></li>
            <?php
          }
          } 
         ?>
         <?php do_action('wpsc_report_sub_menu') ?>
         <li id="wpsc_rp_active_customers_reports" role="presentation"><a href="javascript:get_active_customers_settings();"><?php _e('Active Customers','wpsc-rp');?></a></li>
        </ul>
      </div>
      <div  class="col-md-12 wpsc_report_setting_pill"></div>
    </div>  
 </div>

 
<script>
  jQuery(document).ready(function() {
    get_dashboard_report();
  });
  
  function SearchFunction() {
    var input, filter, ul, li, a, i, txtValue;
    input = document.getElementById("wpsc_ticket_report_search");
    filter = input.value.toUpperCase();
    ul = document.getElementById("wpsc_ul_filter");
    li = ul.getElementsByTagName("li");
    for (i = 0; i < li.length; i++) {
        a = li[i].getElementsByTagName("a")[0];
        txtValue = a.textContent || a.innerText;
        if (txtValue.toUpperCase().indexOf(filter) > -1) {
            li[i].style.display = "";
        } else {
            li[i].style.display = "none";
        }
    }
  }
</script>
