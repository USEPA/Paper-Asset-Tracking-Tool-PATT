<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post,$wpdb;
$sla_field    = get_term_by('slug','sla','wpsc_ticket_custom_fields');
$sla_label    = get_term_meta($sla_field->term_id,'wpsc_tf_label',true);
$in_sla_color = get_option('wpsc_in_sla_color','');
$out_sla_color = get_option('wpsc_out_sla_color','');

$total = $wpdb->get_var("SELECT COUNT(t.id) FROM {$wpdb->prefix}wpsc_ticket t WHERE  t.id NOT IN (SELECT  DISTINCT tm.ticket_id FROM {$wpdb->prefix}wpsc_ticketmeta tm
WHERE  tm.meta_key = 'sla')");

if($total) {?>
	 <div class="row" style="padding:10px;background-color:#EB9316;margin-bottom:10px;border-radius:4px;" id="upgrade_sla_data">
			<div style="font-size:18px; color:#fff;">
				<span id="wpsc_upgrade_complete_percentage"><?php echo __('Database upgrade required for existing tickets to use SLA !','wpsc-sla')?></span><br>
				<button style="margin-top:10px;" type="button" id="wpsc_upgrade_sla_btn" onclick="upgrade_sla_data('1',<?php echo $total?>);" class="btn btn-sm btn-default"><?php _e('Upgrade Now','wpsc-sla');?></button>
	 		</div>
	 </div>
<?php 
}
?>
<ul class="nav nav-tabs">
  <li role="presentation" class="tab active" id="wpsc_sla_chan_settings_tab" onclick="wpsc_change_tab(this,'sla_settings');"><a href="#"><?php _e('SLA Settings')?></a></li>
  <li role="presentation" class="tab" id="wpsc_sla_chan_policy_tab" onclick="wpsc_change_tab(this,'sla_policy');"><a href="#"><?php _e('SLA Policy')?></a></li>
</ul>

<div id="sla_settings" class="tab_content visible">
	<h4 style="margin-bottom:20px;margin-top:20px;"><?php _e('SLA Settings','wpsc-sla')?></h4>
	<form id="wpsc_frm_general_settings" method="post" action="javascript:wpsc_set_sla_settings();">
		  
			<div class="form-group">
	      <label for="wpsc_sla_label"><?php _e('Label','wpsc-sla');?></label>
	      <p class="help-block"><?php _e('Edit label for SLA field you will see in ticket list.','wpsc-sla');?></p>
	      <input type="text" class="form-control" name="wpsc_sla_label" id="wpsc_sla_label" value="<?php echo $sla_label?>" />
	    </div>
			<div class="form-group">
	      <label for=""><?php _e('IN-SLA Color','wpsc-sla');?></label>
	      <p class="help-block"><?php _e('Backgoround color of SLA time in ticket list when ticket is within SLA.','wpsc-sla');?></p>
	      <input type="text" class="wpsc_color_picker" name="wpsc_in_sla_color" id="wpsc_in_sla_color" value="<?php echo $in_sla_color?>" />
	    </div>
			<div class="form-group">
	      <label for=""><?php _e('OUT-SLA Color','wpsc-sla');?></label>
	      <p class="help-block"><?php _e('Backgoround color of SLA time in ticket list when ticket is out of SLA.','wpsc-sla');?></p>
	      <input type="text" class="wpsc_color_picker" name="wpsc_out_sla_color" id="wpsc_out_sla_color" value="<?php echo $out_sla_color?>" />
	    </div>
			<?php do_action('wpsc_get_sla_settings');?>
			<button type="submit" class="btn btn-success" id="wpsc_save_sla_settings_btn"><?php _e('Save Changes','wpsc-sla');?></button>
	    <img class="wpsc_submit_wait" style="display:none;" src="<?php echo WPSC_PLUGIN_URL.'asset/images/ajax-loader@2x.gif';?>">
	    <input type="hidden" name="action" value="wpsc_set_sla_settings" />
	</form>
</div>

<div id="sla_policy" class="tab_content hidden">
	<h4 style="margin-bottom:20px;margin-top:20px;">
		<?php _e('SLA Policy','wpsc-sla')?>
		<button type="button" onclick="wpsc_get_add_sla_policy();" class="btn btn-sm btn-success"><?php _e('Add New','wpsc-sla');?></button>
	</h4>
	<?php
	$sla_policies = get_terms([
		'taxonomy'   => 'wpsc_sla',
		'hide_empty' => false,
		'orderby'    => 'meta_value_num',
		'order'    	 => 'ASC',
		'meta_query' => array('order_clause' => array('key' => 'load_order')),
	]);
	?>
	<ul class="wpsc-sortable">
		<?php foreach ( $sla_policies as $sla ) : ?>
			<li class="ui-state-default" data-id="<?php echo $sla->term_id?>">
				<div class="wpsc-flex-container" style="background-color:#1E90FF;color:#fff;">
					<div class="wpsc-sortable-handle"><i class="fa fa-bars"></i></div>
					<div class="wpsc-sortable-label"><?php echo $sla->name?></div>
					<div class="wpsc-sortable-edit" onclick="wpsc_get_edit_sla_policy(<?php echo $sla->term_id?>);"><i class="fa fa-edit"></i></div>
					<div class="wpsc-sortable-delete" onclick="wpsc_delete_sla_policy(<?php echo $sla->term_id?>);"><i class="fa fa-trash"></i></div>
				</div>
			</li>
		<?php endforeach;?>
	</ul>
</div>

<script>
  
	jQuery(function(){
		jQuery('.wpsc_color_picker').wpColorPicker();
    jQuery( ".wpsc-sortable" ).sortable({ handle: '.wpsc-sortable-handle' });
		jQuery( ".wpsc-sortable" ).on("sortupdate",function(event,ui){
			var ids = jQuery(this).sortable( "toArray", {attribute: 'data-id'} );
			var data = {
		    action: 'wpsc_set_sla_order',
		    sla_ids : ids
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
	
	function wpsc_change_tab(e,content_id){
		jQuery('.tab').removeClass('active');
		jQuery(e).addClass('active');
		jQuery('.tab_content').removeClass('visible').addClass('hidden');
		jQuery('#'+content_id).removeClass('hidden').addClass('visible');
	}
	
	function wpsc_set_sla_settings(){
		jQuery('.wpsc_submit_wait').show();
	  var dataform = new FormData(jQuery('#wpsc_frm_general_settings')[0]);
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
	
	function upgrade_sla_data(page,total_result) {
			jQuery('#wpsc_upgrade_sla_btn').hide();
			wpsc_get_upgrade_sla(page,total_result);
	}
	
	function wpsc_get_upgrade_sla(page,total_result){

		var data = {
			action: 'wpsc_get_upgrade_sla',
			page :  page,
			total_result : total_result
		};
		jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
			var response = JSON.parse(response_str);
			if( response.is_next == 1 ){
						jQuery('#wpsc_upgrade_complete_percentage').html(response.percentage+'% completed. Please wait!!');
						wpsc_get_upgrade_sla(response.page,total_result);
						
      } else {
					  jQuery('#wpsc_upgrade_complete_percentage').html('Upgrade successful !');
						jQuery('#wpsc_upgrade_sla_btn').hide();
						setTimeout(function(){ jQuery('#upgrade_sla_data').slideUp('fast',function(){}); }, 1000);
        }
		});
	}
	
	function wpsc_get_add_sla_policy(){
		wpsc_modal_open('<?php _e('Add New SLA Policy','wpsc-sla')?>');
		var data = {
			action: 'wpsc_get_add_sla_policy',
		};
		jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
			var response = JSON.parse(response_str);
			jQuery('#wpsc_popup_body').html(response.body);
			jQuery('#wpsc_popup_footer').html(response.footer);
			jQuery('#wpsc_sla_policy_title').focus();
		});
	}
	
	function wpsc_set_add_sla_policy(){
		var title = jQuery('#wpsc_sla_policy_title').val().trim();
		if(title.length==0){
			alert("<?php _e('Title should not be empty.','wpsc-sla')?>");
			return;
		}
		var time = jQuery('#wpsc_sla_policy_time').val().trim();
		if(time.length==0){
			alert("<?php _e('Time should not be empty.','wpsc-sla')?>");
			return;
		}
		var conditions = wpsc_condition_parse('wpsc_add_sla_conditions');
	  if(!wpsc_condition_validate(conditions)) {
	    alert('<?php _e('At least one condition required.','wpsc-sla')?>');
	    return;
	  }
		
		var dataform = new FormData(jQuery('#wpsc_frm_add_sla')[0]);
		dataform.append('conditions',JSON.stringify(conditions));
		jQuery('.wpsc_popup_action').text('<?php _e('Please wait ...','wpsc')?>');
		jQuery('.wpsc_popup_action, #wpsc_popup_body input').attr("disabled", "disabled");
	  jQuery.ajax({
	    url: wpsc_admin.ajax_url,
	    type: 'POST',
	    data: dataform,
	    processData: false,
	    contentType: false
	  })
	  .done(function (response_str) {
			wpsc_modal_close();
			var response = JSON.parse(response_str);
			if (response.sucess_status=='1') {
				jQuery('#wpsc_alert_success .wpsc_alert_text').text(response.messege);
				jQuery('#wpsc_alert_success').slideDown('fast',function(){});
				setTimeout(function(){ jQuery('#wpsc_alert_success').slideUp('fast',function(){}); }, 3000);
				wpsc_get_sla_settings();
			} else {
				jQuery('#wpsc_alert_error .wpsc_alert_text').text(response.messege);
				jQuery('#wpsc_alert_error').slideDown('fast',function(){});
				setTimeout(function(){ jQuery('#wpsc_alert_error').slideUp('fast',function(){}); }, 3000);
			}
	  });
	}
	
	function wpsc_get_edit_sla_policy(term_id){
		wpsc_modal_open('<?php _e('Edit SLA Policy','wpsc-sla')?>');
		var data = {
			action: 'wpsc_get_edit_sla_policy',
			term_id: term_id
		};
		jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
			var response = JSON.parse(response_str);
			jQuery('#wpsc_popup_body').html(response.body);
			jQuery('#wpsc_popup_footer').html(response.footer);
			jQuery('#wpsc_sla_policy_title').focus();
		});
	}
	
	function wpsc_set_edit_sla_policy(){
		var title = jQuery('#wpsc_sla_policy_title').val().trim();
		if(title.length==0){
			alert("<?php _e('Title should not be empty.','wpsc-sla')?>");
			return;
		}
		var time = jQuery('#wpsc_sla_policy_time').val().trim();
		if(time.length==0){
			alert("<?php _e('Time should not be empty.','wpsc-sla')?>");
			return;
		}
		var conditions = wpsc_condition_parse('wpsc_edit_sla_conditions');
	  if(!wpsc_condition_validate(conditions)) {
	    alert('<?php _e('At least one condition required.','wpsc-sla')?>');
	    return;
	  }
	  
		var dataform = new FormData(jQuery('#wpsc_frm_add_sla')[0]);
		dataform.append('conditions',JSON.stringify(conditions));
		jQuery('.wpsc_popup_action').text('<?php _e('Please wait ...','wpsc')?>');
		jQuery('.wpsc_popup_action, #wpsc_popup_body input').attr("disabled", "disabled");
	  jQuery.ajax({
	    url: wpsc_admin.ajax_url,
	    type: 'POST',
	    data: dataform,
	    processData: false,
	    contentType: false
	  })
	  .done(function (response_str) {
			wpsc_modal_close();
			var response = JSON.parse(response_str);
			if (response.sucess_status=='1') {
				jQuery('#wpsc_alert_success .wpsc_alert_text').text(response.messege);
				jQuery('#wpsc_alert_success').slideDown('fast',function(){});
				setTimeout(function(){ jQuery('#wpsc_alert_success').slideUp('fast',function(){}); }, 3000);
				wpsc_get_sla_settings();
			} else {
				jQuery('#wpsc_alert_error .wpsc_alert_text').text(response.messege);
				jQuery('#wpsc_alert_error').slideDown('fast',function(){});
				setTimeout(function(){ jQuery('#wpsc_alert_error').slideUp('fast',function(){}); }, 3000);
			}
	  });
	}
	
	function wpsc_delete_sla_policy(term_id){
		if(!confirm('<?php _e('Are you sure?','wpsc-sla')?>')) return;
		var data = {
			action: 'wpsc_delete_sla_policy',
			term_id: term_id
		};
		jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
			var response = JSON.parse(response_str);
			if (response.sucess_status=='1') {
				jQuery('#wpsc_alert_success .wpsc_alert_text').text(response.messege);
				jQuery('#wpsc_alert_success').slideDown('fast',function(){});
				setTimeout(function(){ jQuery('#wpsc_alert_success').slideUp('fast',function(){}); }, 3000);
				wpsc_get_sla_settings();
			}
		});
	}
	

</script>
