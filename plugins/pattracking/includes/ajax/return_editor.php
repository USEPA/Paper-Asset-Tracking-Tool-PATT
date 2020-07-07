<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
// set default filter for agents and customers //not true
global $current_user, $wpscfunction;

if (!$current_user->ID) die();

$setting_action = isset($_POST['setting_action']) ? sanitize_text_field($_POST['setting_action']) : '';
$return_type = isset($_POST['return_type']) ? sanitize_text_field($_POST['return_type']) : '';
//$recall_ids = isset($_POST['recall_ids']) ? sanitize_text_field($_POST['recall_ids']) : '';
$return_ids = $_REQUEST['return_ids']; 
$num_of_items = count($return_ids);
$subfolder_path = site_url( '', 'relative'); 

//FAKE DATA
$ticket_id = '0000001';




ob_start();
// echo 'This has got to be something';
echo "Return type: ".$return_type."<br>";
echo "Number of Return IDs: ".$num_of_items."<br>";
echo "Return  for: ";
print_r($return_ids);
echo "<br><br>";

/*
foreach($recall_ids as $id) {
	echo '<label class="wpsc_ct_field_label">Recall ID: </label>';
	echo '<span id="modal_recall_id" class="">'.$id.'</span><br>';	
}
*/

?>

<label class="wpsc_ct_field_label">Return Reason: </label>
<select id="return_dropdown" name="return_reason" value="" >
	<option value""></option>
	<option value"damaged">Damaged</option>
	<option value"non-record">Non-record</option>
	<option value"duplicate">Duplicate</option>
	<option value"unscannable">Unscannable</option>
	<option value"copyright-material">Copyright material</option>	
	<option value"request-cancelled-before-arrival">Request cancelled before arrival</option>
	<option value"contents-not-prepared-to-standards">Contents not prepared to standards</option>
	<option value"box-listing-incomplete-missing">Box Listing incomplete/missing</option>
</select>

<br><br>
<div>
	<label class="wpsc_ct_field_label top">Comment: </label>
	<textarea id="comment" name="return_comment" rows="2" cols="60" value="" />
</div>

<!--
<label class="wpsc_ct_field_label">Shipping Tracking Number: </label>
<input type="text" id="stn" name="shipping_tracking_number" value="" />
<label class="wpsc_ct_field_label">Shipping Carrier: </label>
<select id="carrier_dropdown" name="shipping_carrier" value="" >
	<option value""></option>
	<option value"ups">UPS</option>
	<option value"FedEx">FedEx</option>
	<option value"usps">USPS</option>
	<option value"dhl">DHL</option>		
</select>
<br><br>
-->

<style>
	.hide
	{
		display: none;
	}
	
	.jsgrid-header-row>.jsgrid-header-cell {
		text-align: center;
	}
	
	.jsgrid-row>.jsgrid-cell {
		text-align: center;
	}
	
	.jsgrid-alt-row>.jsgrid-cell {
		text-align: center;
	}
	
	#grid_table {
		width: auto !IMPORTANT
	}
	
	.top {
		vertical-align: top;
	}
</style>
  
<link rel="stylesheet" type="text/css" href="<?php echo WPPATT_PLUGIN_URL.'includes/admin/css/jsgrid.min.css';?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo WPPATT_PLUGIN_URL.'includes/admin/css/jsgrid-theme.min.css';?>"/>
<script type="text/javascript" src="<?php echo WPPATT_PLUGIN_URL.'includes/admin/js/jsgrid.min.js';?>"></script>  
  
<!--         <div class="container> -->
<div class="">  
<br>
	<div class="table-responsive">
		<div id="grid_table"></div>
	</div>  
</div>

<script>
jQuery(document).ready(function() {
           
	jQuery('#grid_table').jsGrid({
		width: "auto",
		height: "auto",
		//filtering: true,
		inserting:true,
		editing: true,
		sorting: true,
		paging: true,
		autoload: true,
		pageSize: 20,
		pageButtonCount: 5,
		deleteConfirm: "Do you really want to delete this tracking number?",
		controller: {
			loadData: function(filter){
				var ticket_id = <?php echo $ticket_id; ?>; 
				var subfolder = '<?php echo $subfolder_path; ?>';
				return jQuery.ajax({
					type: "GET",
					url: subfolder+"/wp-content/plugins/pattracking/includes/ajax/fetch_shipping_data_multi.php?ticket_id="+ticket_id,
// 					url: subfolder+"/wp-content/plugins/pattracking/includes/ajax/fetch_shipping_data.php",
					data: filter
				});
		    },
			insertItem: function(item){
				var ticket_id = <?php echo $ticket_id; ?>; 
				var subfolder = '<?php echo $subfolder_path; ?>';
				return jQuery.ajax({
					type: "POST",
					url: subfolder+"/wp-content/plugins/pattracking/includes/ajax/fetch_shipping_data_multi.php?ticket_id="+ticket_id,
					data:item
				});
			},
			updateItem: function(item){
				var ticket_id = <?php echo $ticket_id; ?>; 
				var subfolder = '<?php echo $subfolder_path; ?>';
				return jQuery.ajax({
					type: "PUT",
					url: subfolder+"/wp-content/plugins/pattracking/includes/ajax/fetch_shipping_data_multi.php?ticket_id="+ticket_id,
					data: item
				});
			},
			deleteItem: function(item){
				var ticket_id = <?php echo $ticket_id; ?>; 
				var subfolder = '<?php echo $subfolder_path; ?>';
				return jQuery.ajax({
					type: "DELETE",
					url: subfolder+"/wp-content/plugins/pattracking/includes/ajax/fetch_shipping_data_multi.php?ticket_id="+ticket_id,
					data: item
				});
			},
		},
		fields: [
		{
			name: "id",
			type: "text",
			width: 10, 
// 			type: "hidden",
// 			css: 'hide'
		},
		{
			name: "ticket-id",
			type: "hidden",
			css: 'hide'
		},
		{
			name: "tracking_number",
			title: "Tracking Number",
			type: "text", 
			width: 150, 
			validate: "required",
			formatter: function (cellvalue, options, rowObject) {
		    	return "<a href='javascript:void(0);' class='anchor usergroup_name link'>" + cellvalue + '</a>';
		    }
		},
		{
			name: "company_name",
			title: "Shipping Company",
			type: "select", 
			width: 50, 
			items: [
				{ Name: "", Id: '' },
				{ Name: "UPS", Id: 'ups' },
				{ Name: "FedEx", Id: 'fedex' },
				{ Name: "USPS", Id: 'usps' },
				{ Name: "DHL", Id: 'dhl' },
			], 
			valueField: "Id", 
			textField: "Name", 
			validate: "required"
		},
		{
			name: "status",
			title: "Shipping Status",
			type: "text", 
			width: 100, 
			editing: false,
			inserting: false
		},
		{
			type: "control"
		}
		]
	});
});
</script>


<?php

$body = ob_get_clean();
ob_start();
?>
<!-- <button type="button" class="btn wpsc_popup_close"  style="background-color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_text_color']?> !important;"   onclick="wpsc_open_ticket(<?php echo htmlentities($ticket_id)?>);wpsc_modal_close();"><?php _e('Close','wpsc-export-ticket');?></button> -->
<button type="button" class="btn wpsc_popup_close"  style="background-color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_text_color']?> !important;"   onclick="wpsc_modal_close();"><?php _e('Close','wpsc-export-ticket');?></button>

<button type="button" id="button_return_submit" class="btn wpsc_popup_action" style="background-color:<?php echo $wpsc_appearance_modal_window['wpsc_action_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_modal_window['wpsc_action_button_text_color']?> !important;" onclick="wppatt_set_return();wpsc_modal_close();"><?php _e('Save','supportcandy');?></button>

<script>

	jQuery("#button_return_submit").hide();
	
	jQuery('#return_dropdown').change(function() {	
		if( jQuery('#return_dropdown').val() =='') {
			jQuery("#button_return_submit").hide();						
		} else {
			jQuery("#button_return_submit").show();
		}
	})	

	function wppatt_set_return() {
		console.log('return');
		//var recall_id_array = <?php echo json_encode($recall_ids) ?>;

		//console.log(recall_id_array);
		
		jQuery.post(
		   '<?php echo WPPATT_PLUGIN_URL; ?>includes/admin/pages/scripts/return_processing.php',{
		    return_ids: <?php echo json_encode($return_ids) ?>,
		    return_reason: jQuery('#return_dropdown').val(),
// 		    type: 'box',
			type: '<?php echo $return_type ?>',
		    shipping_tracking_number: jQuery('#stn').val(),
		    shipping_carrier: jQuery('#carrier_dropdown').val(),
		    comment: jQuery('#comment').val()
		}, 
	    function (response) {
			alert('Updated: '+response);
			//window.location.reload();
	
	    });
	}
	
</script>


<?php 
$footer = ob_get_clean();

$output = array(
  'body'   => $body,
  'footer' => $footer
);
echo json_encode($output);	