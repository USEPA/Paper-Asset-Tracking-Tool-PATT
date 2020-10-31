<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpscfunction,$wpdb;

$fields = get_terms([
	'taxonomy'   => 'wpsc_ticket_custom_fields',
	'hide_empty' => false,
	'orderby'    => 'meta_value_num',
	'meta_key'	 => 'wpsc_tf_load_order',
	'order'    	 => 'ASC',
	'meta_query' => array(
    'relation' => 'AND',
    array(
      'key'       => 'agentonly',
      'value'     => array(0,1),
      'compare'   => 'IN'
    ),
  )
]);

$pie_widgets     = get_option('wpsc_report_dash_widgets',array());
?> 
<div class="row">
  <form id="frm_report_settings"  action="javascript:wpsc_reports_save_settings();" method="post">
    <div class="form-group">
         <label for="wpsc_dashboard_report_filters"><strong><?php _e('Dashboard duration','wpsc-rp');?></strong></label></br>
         <p class="help-block"> <?php _e('Select duration for dashboard reports.','wpsc-rp')?></p>
         <select id="wpsc_dashboard_report_filters" class="form-control" name="wpsc_dashboard_report_filters" >
           <?php
           $wpsc_dashboard_report_filters = get_option('wpsc_dashboard_report_filters ' );
           
           $selected = $wpsc_dashboard_report_filters == 'last7days' ? 'selected="selected"' : '';
           echo '<option '.$selected.' value="last7days">Last 7 Days</option>';
           
           $selected = $wpsc_dashboard_report_filters == 'last30days' ? 'selected="selected"' : '';
           echo '<option '.$selected.' value="last30days">Last 30 Days</option>';
           
           $selected = $wpsc_dashboard_report_filters == 'lastmonth' ? 'selected="selected"' : '';
           echo '<option '.$selected.' value="lastmonth">Last Month</option>';
           
           $selected = $wpsc_dashboard_report_filters == 'lastquarter' ? 'selected="selected"' : '';
           echo '<option '.$selected.' value="lastquarter">Last Quarter</option>';
           ?>
        </select>
        
    </div>
  
    
 		<div class="form-group">
			<label for="wpsc_thankyou_html"><?php _e('Dashboard widgets','wpsc-rp');?></label>
			<p class="help-block"><?php _e('Select which reports you want to show on dashboard.','wpsc-rp');?></p>
			<div class="row">
        <?php 
       if($fields):
        foreach ($fields as $key => $field) {
          $cust_field =  get_term_by('id', $field->term_id, 'wpsc_ticket_custom_fields');
          $wpsc_tf_type = get_term_meta($field->term_id ,'wpsc_tf_type',true);
          if($wpsc_tf_type == 2 || $wpsc_tf_type == 3 || $wpsc_tf_type == 4 ||  $cust_field->slug =='ticket_category' ) :
           $checked = in_array($field->term_id,$pie_widgets)?'checked="checked"':'';	
            $label = get_term_meta($field->term_id,'wpsc_tf_label',true);
            
            ?>
            <div class="col-sm-4" style="margin-bottom:10px; display:flex;">
              <div style="width:25px;"><input type="checkbox" class="wpsc_reports_data" name="wpsc_report_dash_widgets[]" <?php echo $checked?> value="<?php echo $field->term_id?>" /></div>
              <div style="padding-top:3px;"><?php echo $label?></div>
            </div>
            <?php
          endif;  
        }
        do_action('wpsc_allowed_report_on_dash_settings');
			?>
		  </div>
		</div>
	<?php 
  endif;
	?>	
		 
	
  
	<?php do_action('wpsc_get_report_settings')?>
  
	<button type="submit" style="margin-top:20px;" class="btn btn-success" id="wpsc_save_report_settings_btn"><?php _e('Save Changes','wpsc-rp');?></button>
	<img class="wpsc_submit_wait" style="display:none;" src="<?php echo WPSC_PLUGIN_URL.'asset/images/ajax-loader@2x.gif';?>">
	<input type="hidden" name="action" value="wpsc_reports_save_settings" />
</form>


<script type="text/javascript">
  function wpsc_reports_save_settings(){
    jQuery('.wpsc_submit_wait').show();
    var dataform = new FormData(jQuery('#frm_report_settings')[0]);
    
    jQuery.ajax({
      url: wpsc_admin.ajax_url,
      type: 'POST',
      data: dataform,
      processData: false,
      contentType: false
    })
    .done(function (response_str) {
      var response = JSON.parse(response_str);
      jQuery('.wpsc_submit_wait').hide();
      if (response.sucess_status=='1') {
        jQuery('#wpsc_alert_success .wpsc_alert_text').text(response.messege);
      }
      jQuery('#wpsc_alert_success').slideDown('fast',function(){});
      setTimeout(function(){ jQuery('#wpsc_alert_success').slideUp('fast',function(){}); }, 3000);
    });
  }
</script>