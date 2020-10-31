<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user, $wpscfunction;

$wpsc_appearance_modal_window = get_option('wpsc_modal_window');
$wpsc_export_ticket_list = get_option('wpsc_export_ticket_list');
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
ob_start();
?>
<?php if($wpsc_export_ticket_list){?>
<form id="frm_export_ticket_create_title">
  <div class="table-responsive">
		<table id="tbl_templates" class="table table-striped table-bordered">
			<thead>
				<tr>
					<td><input id="chk_all_export_ticket" onchange="toggle_export_checkboxes(this);"  class="wpsp_export_ticket_check_all" type="checkbox"  value=" "/></td>
					<td><b><?php _e('Select All / Clear All','wpsc-export-ticket');?></b></td>
				</tr>
  	</thead>
  	<tbody>
  	<?php
		foreach ($wpsc_export_ticket_list as $key => $value) {
			$label = get_term_by('slug',$value,'wpsc_ticket_custom_fields');
			$c_name = get_term_meta($label->term_id,'wpsc_tf_label',true); 
			$c_name = apply_filters('wpsc_change_et_field_name',$c_name,$value);

			if($c_name=='Conditional Agent Assign' || $c_name=='Agent Created' || $c_name=='' || $c_name=='Automatic Ticket Close' || $c_name=='SLA'){
				continue;
			}
			?>
			<tr>
  	    <td><input type="checkbox" class="wpsp_export_ticket_colums" value="<?php echo $value;?>"></td>
  	    <td><?php echo $c_name ?></td>
  	  </tr>
			<?php
		}
}
else{
	?>
	<div class="wpsp_sidebar_labels">
		<?php	_e("No Items Found",'wpsc-export-ticket');	?>
	</div>
	<?php
}
?>
</tbody>
</table>
</div>
	
<input type="hidden" name="action" value="wpsc_tickets" />
<input type="hidden" name="setting_action" value="set_export_ticket" />  			
</form>
<script>
function toggle_export_checkboxes(obj){  
	if(jQuery(obj).is(':checked')){
    jQuery('.wpsp_export_ticket_colums:enabled').prop('checked',true);
	}else{
		jQuery('.wpsp_export_ticket_colums:enabled').prop('checked',false);
	}  
}

function wpsc_set_export_ticket(){    
  var values = jQuery('input[class=wpsp_export_ticket_colums]:checked').map(function () {
  return this.value;
  }).get();
  
  var field_id = String(values);    
  if(field_id){
    var data = {
      action: 'wpsc_set_export_ticket',
      field_id: field_id  
    };
    jQuery.post(wpsc_admin.ajax_url, data, function(response) {
      var obj = jQuery.parseJSON( response );
      window.open(obj.url_to_export,'_blank');
      wpsc_modal_close();    
    });
  } else{
    alert(wpsc_admin.please_select_at_least_one_column);
  }              
}
</script>
<?php 
$body = ob_get_clean();
ob_start();
?>
<button type="button" class="btn wpsc_popup_close"  style="background-color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_text_color']?> !important;"   onclick="wpsc_modal_close();"><?php _e('Close','wpsc-export-ticket');?></button>
<button type="button" class="btn wpsc_popup_action" style="background-color:<?php echo $wpsc_appearance_modal_window['wpsc_action_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_modal_window['wpsc_action_button_text_color']?> !important;" onclick="wpsc_set_export_ticket();"><?php _e('Export','wpsc-export-ticket');?></button>
<?php 
$footer = ob_get_clean();

$output = array(
  'body'   => $body,
  'footer' => $footer
);
echo json_encode($output);