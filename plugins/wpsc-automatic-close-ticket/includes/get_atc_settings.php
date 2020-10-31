<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user, $wpdb, $wpscfunction;
if (!($current_user->ID && $current_user->has_cap('manage_options'))) {
	exit;
}

$wpsc_atc_warning_text  = stripslashes(get_option('wpsc_atc_warning_text'));
$wpsc_atc_age           = get_option('wpsc_atc_age','0');
$wpsc_atc_waring_email_age  = get_option('wpsc_atc_waring_email_age','0');

$statuses = get_terms([
  'taxonomy'   => 'wpsc_statuses',
  'hide_empty' => false,
  'orderby'    => 'meta_value_num',
  'order'    	 => 'ASC',
  'meta_query' => array('order_clause' => array('key' => 'wpsc_status_load_order')),
]);

$directionality = $wpscfunction->check_rtl();

?>
<h4><?php _e('Automatic Close Ticket','wpsc-atc');?></h4><br>
<form id="frm_atc_settings" action="javascript:wpsc_set_atc_settings();" method="post">
	
	<div class="form-group">
		<label><?php _e('Select Statuses','wpsc-atc');?></label>
		<p class="help-block"><?php _e('Select statuses to check for automatic close ticket.','wpsc-atc');?></p>
		<?php
		foreach ( $statuses as $status ) :
			$wpsc_tl_statuses = get_option('wpsc_tl_statuses');
			$wpsc_tl_statuses = $wpsc_tl_statuses ? $wpsc_tl_statuses : array();
			$checked = in_array($status->term_id,$wpsc_tl_statuses) ? 'checked="checked"' : '';
			 ?>
			 <div class="col-sm-4" style="margin-bottom:10px; display:flex;">
				 <div style="width:25px;"><input type="checkbox" name="wpsc_tl_statuses[]" <?php echo $checked?> value="<?php echo $status->term_id?>" /></div>
				 <div style="padding-top:3px;"><?php echo $status->name?></div>
			 </div>
			 <?php
		endforeach;
		?>
	</div>
	
	<div class="form-group" style="clear:both;">
		<label><?php _e('Age','wpsc-atc');?></label>
		<p class="help-block"><?php _e("Insert number of days after which ticket should be closed. Set '0' to disable feature.",'wpsc-atc');?></p>
		<input type="number" class="form-control" name="wpsc_atc_age" id="wpsc_atc_age" value="<?php echo $wpsc_atc_age?>" />
	</div>
 
  <button type="submit" class="btn btn-success" id="wpsc_save_changes_atc_btn"><?php _e('Save Changes','wpsc-sf');?></button>
  <img class="wpsc_submit_wait" style="display:none;" src="<?php echo WPSC_PLUGIN_URL.'asset/images/ajax-loader@2x.gif';?>">
  <input type="hidden" name="action" value="wpsc_atc_save_settings" />
  
</form>

<script>

function wpsc_set_atc_settings(){
  jQuery('.wpsc_submit_wait').show();
  var dataform = new FormData(jQuery('#frm_atc_settings')[0]);
  
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