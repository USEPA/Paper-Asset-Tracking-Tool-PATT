<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $current_user;
if (!($current_user->ID && $current_user->has_cap('manage_options'))) {
	exit;
}
$export_role = get_option('wpsc_selected_user_roll_data',array());
$agent_role  = get_option('wpsc_agent_role');
$ticket_list_items = get_terms([
	'taxonomy'   => 'wpsc_ticket_custom_fields',
	'hide_empty' => false,
	'orderby'    => 'meta_value_num',
	'meta_key'	 => 'wpsc_export_ticket_list_order',
	'meta_query' => array(
		'relation' => 'AND',
		array(
			'key'     => 'wpsc_allow_ticket_list',
			'value'   => '1',
			'compare' => '=',
		),
	),
]);
?>
<h4 style="margin-bottom:20px;"><?php _e('Export Ticket','wpsc-export-ticket');?></h4>
<form id="wpsc_frm_export_ticket_settings" method="post" action="javascript:wpsc_set_export_ticket_settings();">
	<div class="form-group">
		<label for="wpsc_thankyou_html"><?php _e('Role Capability','wpsc-export-ticket');?></label>
		<p class="help-block"><?php _e('Select roles who has capability to export tickets. User can only export tickets accessible to them in ticket list for all role capabilities.','wpsc-export-ticket');?></p>
		<div class="row">
			<?php
			foreach ( $agent_role as $key => $val ) :
				$checked = in_array($key,$export_role)?'checked="checked"':'';
				?>
				<div class="col-sm-12" style="margin-bottom:10px; display:flex;">
					<div style="width:25px;"><input name="export_role[]" type="checkbox" <?php echo $checked?> value="<?php echo $key?>"/></div>
					<div style="padding-top:3px;"><?php echo $val['label']?></div>
				</div>
				<?php
			endforeach;
			?>
			<div class="col-sm-12" style="margin-bottom:10px; display:flex;">
				<div style="width:25px;"><input name="export_role[]" type="checkbox" <?php echo in_array('customer',$export_role)?'checked="checked"':''?> value="<?php echo 'customer'?>"/></div>
				<div style="padding-top:3px;"><?php _e('All Users (Any registered user)','wpsc-export-ticket')?></div>
			</div>
		</div>
	</div>
	<div class="form-group">
	    <label for="wpsc_export_ticket_list"><?php _e('Export Ticket List','wpsc-export-ticket');?></label>
	    <p class="help-block"></p>
			<div class="row">
				<div class="col-sm-12" style="margin-bottom:10px; display:flex;padding:0px;">
          <div style="width:30px;">	<input id="chk_all_export_ticket" onchange="toggle_export_checkboxes(this);"  class="wpsc_select_all_option_value" name="wpsc_select_all_option_value" type="checkbox"  value="0"></div>
          <div style="padding-top:3px;"><?php _e('Select All / Clear All','wpsc-export-ticket');?></div>
        </div>
			</div>
			<div class="row">
				<ul class="wpsc-sortable">
					<?php 
					$wpsc_export_ticket_list = get_option('wpsc_export_ticket_list');
	        foreach ($ticket_list_items as $key => $value) {
		        $checked = in_array($value->slug,$wpsc_export_ticket_list)?'checked="checked"':'';	
				$label = get_term_meta($value->term_id,'wpsc_tf_label',true);
				$label = apply_filters('wpsc_change_et_label_name',$label,$value->slug);

						if($label=='Conditional Agent Assign' || $label=='Agent Created' || $label=='' || $label=='Automatic Ticket Close' || $label=='SLA'){
							continue;
						}
					?>
					<li class="ui-state-default" data-id="<?php echo $value->term_id?>">
						<div class="export-flex-container" style="margin-bottom:5px; background-color:#1E90FF;color:#fff;">
							<div>
								<input type="checkbox" class="wpsc_export_ticket_list" name="wpsc_export_ticket_list[]" <?php echo $checked?> value="<?php echo $value->slug?>" />
							</div>
							<div class="wpsc-sortable-handle" style="padding-top:5px;"><i class="fa fa-bars"></i></div>
							<div class="wpsc-sortable-label" style="padding-top:5px;"><?php echo $label;?></div>
		        </div>
					</li>	
						<?php
					}
					?>
			</ul>
  	</div>
	</div>
	<?php do_action('wpsc_get_export_settings')?>
	
	<button type="submit" style="margin-top:20px;" class="btn btn-success" id="wpsc_save_export_settings_btn"><?php _e('Save Changes','wpsc-export-ticket');?></button>
	<img class="wpsc_submit_wait" style="display:none;" src="<?php echo WPSC_PLUGIN_URL.'asset/images/ajax-loader@2x.gif';?>">
	<input type="hidden" name="action" value="wpsc_set_export_ticket_settings" />
</form>

<script>
  function wpsc_set_export_ticket_settings(){ 
		jQuery('.wpsc_submit_wait').show();
		var dataform = new FormData(jQuery('#wpsc_frm_export_ticket_settings')[0]);
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
	
	function toggle_export_checkboxes(obj){
		if(jQuery(obj).is(':checked')){
	    jQuery('.wpsc_export_ticket_list:enabled').prop('checked',true);
		}else{
			jQuery('.wpsc_export_ticket_list:enabled').prop('checked',false);
		}  
	}
	
	jQuery(function(){
		jQuery( ".wpsc-sortable" ).sortable({ handle: '.wpsc-sortable-handle' });
		jQuery( ".wpsc-sortable" ).on("sortupdate",function(event,ui){
			var ids = jQuery(this).sortable( "toArray", {attribute: 'data-id'} );
			var values = jQuery('.wpsc_export_ticket_list:checked').map(function(){return this.value;}).get();
	    var data = {
		    action: 'wpsc_set_export_list_order',
		    export_list_ids : ids,
				ticket_list_items : values ,
		  };
			jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
				var response = JSON.parse(response_str);
		    if (response.sucess_status=='1') {
		      jQuery('#wpsc_alert_success .wpsc_alert_text').text(response.messege);
		    }
		    jQuery('#wpsc_alert_success').slideDown('fast',function(){});
		    setTimeout(function(){ jQuery('#wpsc_alert_success').slideUp('fast',function(){}); }, 3000);
		  });
		});
});
</script>