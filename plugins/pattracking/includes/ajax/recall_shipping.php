
<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpdb, $current_user, $wpscfunction;

if (!$current_user->ID) die();

$ticket_id 	 = isset($_POST['ticket_id']) ? intval($_POST['ticket_id']) : 0 ;
$recall_id = isset($_POST['recall_id']) ? sanitize_text_field($_POST['recall_id']) : '';
$current_shipping_tracking = isset($_POST['shipping_tracking']) ? sanitize_text_field($_POST['shipping_tracking']) : '';
$current_shipping_carrier = isset($_POST['shipping_carrier']) ? sanitize_text_field($_POST['shipping_carrier']) : '';

//$current_shipping_tracking 	 = isset($_POST['shipping_tracking']) ? intval($_POST['shipping_tracking']) : '' ;
// $current_shipping_carrier 	 = isset($_POST['shipping_carrier']) ? intval($_POST['shipping_carrier']) : '' ;
//$current_shipping_carrier 	 = 'FedEx' ;

ob_start();
?>

  <style>
  .hide
  {
     display:none;
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
  </style>


<br>
<label class="wpsc_ct_field_label">Current Shipping Tracking Number: </label>
	<span id="modal_current_shipping_number" class=""><?php echo $current_shipping_tracking; ?></span>
<br>
<label class="wpsc_ct_field_label">Current Shipping Carrier: </label>
	<span id="modal_current_shipping_carrier" class=""><?php echo $current_shipping_carrier; ?></span>
<br>
<label class="wpsc_ct_field_label">New Shipping Tracking Number: </label>
<input type="text" id="new_stn" name="new_shipping_tracking_number" value="" />
<label class="wpsc_ct_field_label">New Shipping Carrier: </label>
<select id="carrier_dropdown" name="new_shipping_carrier" value="" >
	<option value""></option>
	<option value"ups">UPS</option>
	<option value"FedEx">FedEx</option>
	<option value"usps">USPS</option>
	<option value"dhl">DHL</option>		
</select>
<br>

<script>
	
/*
	jQuery(document).ready(function() {


	});
*/
	

		jQuery('#carrier_dropdown').change(function() {	
			if( jQuery('input[name=new_shipping_tracking_number]').val() =='') {
				console.log("No Value");				
			} else {
				jQuery("#button_shipping_submit").show();
			}
			
		})
		
		
		
		jQuery('#new_stn').on('input propertychange paste', function() {
		    console.log("Look ma, no hands");
		    if(jQuery('#carrier_dropdown').val() == '') {
			    console.log("farts");
		    } else {
			    jQuery("#button_shipping_submit").show();
		    }
		})		
		
/* //just commented out
		jQuery('#carrier_dropdown').change(function() {	
			if( jQuery('input[name=new_shipping_tracking_number]').val() =='') {
				console.log("No Value");				
			} else {
				jQuery("#button_shipping_submit").show();
			}
			
		})
*/
  	
/*
jQuery( document ).ready(function() {
// 	console.log('shipping ready');
	jQuery('select[name="new_shipping_carrier"]').change(function(){
		jQuery("#button_shipping_submit").show();

	});​
});
*/

</script>

<!--
<div class="">  
   <br>
    <div class="table-responsive">
    	<div id="grid_table"></div>
    </div>  
</div>

<script>
(function ($) {
           
    $('#grid_table').jsGrid({

     width: "auto",
     height: "auto",

     filtering: true,
     inserting:true,
     editing: true,
     sorting: true,
     paging: true,
     autoload: true,
     pageSize: 10,
     pageButtonCount: 5,
     deleteConfirm: "Do you really want to delete this tracking number?",

     controller: {
      loadData: function(filter){
       var ticket_id = <?php echo $ticket_id; ?>; 
       return $.ajax({
        type: "GET",
        url: "/wordpress2/wp-content/plugins/pattracking/includes/ajax/fetch_shipping_data.php?ticket_id="+ticket_id,
        data: filter
       });
      },
      insertItem: function(item){
       var ticket_id = <?php echo $ticket_id; ?>; 
       return $.ajax({
        type: "POST",
        url: "/wordpress2/wp-content/plugins/pattracking/includes/ajax/fetch_shipping_data.php?ticket_id="+ticket_id,
        data:item
       });
      },
      updateItem: function(item){
       var ticket_id = <?php echo $ticket_id; ?>; 
       return $.ajax({
        type: "PUT",
        url: "/wordpress2/wp-content/plugins/pattracking/includes/ajax/fetch_shipping_data.php?ticket_id="+ticket_id,
        data: item
       });
      },
      deleteItem: function(item){
       return $.ajax({
        type: "DELETE",
        url: "/wordpress2/wp-content/plugins/pattracking/includes/ajax/fetch_shipping_data.php",
        data: item
       });
      },
     },

     fields: [
      {
       name: "id",
    type: "hidden",
    css: 'hide'
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
                    return "<a href='javascript:void(0);' class='anchor usergroup_name link'>" +
                           cellvalue + '</a>';
                }
      },
      {
       name: "company_name",
       title: "Shipping Company",
    type: "select", 
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
    width: 150, 
    editing: false,
    inserting: false
      },
      {
       type: "control"
      }
     ]

    });
                })(jQuery);
</script>
-->

<?php 
$body = ob_get_clean();
ob_start();
?>
<button type="button" class="btn wpsc_popup_close"  style="background-color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_text_color']?> !important;"   onclick="wpsc_modal_close();"><?php _e('Close','wpsc-export-ticket');?></button>
<button type="button" id="button_shipping_submit" class="btn wpsc_popup_action" style="background-color:<?php echo $wpsc_appearance_modal_window['wpsc_action_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_modal_window['wpsc_action_button_text_color']?> !important;" onclick="wppatt_set_shipping();"><?php _e('Save','supportcandy');?></button>

<script>
jQuery("#button_shipping_submit").hide();

function wppatt_set_shipping(){	
	
	console.log('setting shipping for: '+ '<?php echo $recall_id ?>');
	jQuery.post(
	   '<?php echo WPPATT_PLUGIN_URL; ?>includes/admin/pages/scripts/update_recall_details.php',{
	    recall_id: '<?php echo $recall_id ?>',
	    new_shipping_tracking: jQuery('input[name=new_shipping_tracking_number]').val(),
	    new_shipping_carrier: jQuery('#carrier_dropdown').val(),
	    type: 'shipping'
	}, 
    function (response) {
		alert('updated: '+response);
		window.location.reload();
/*
    	if(!alert(response)){window.location.reload();}
		window.location.replace("/wordpress3/wp-admin/admin.php?page=wpsc-tickets&id=<?php echo Patt_Custom_Func::convert_request_db_id($patt_ticket_id); ?>");
*/
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




/* Original working
<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
// set default filter for agents and customers //not true
global $current_user, $wpscfunction;

if (!$current_user->ID) die();

$setting_action = isset($_POST['setting_action']) ? sanitize_text_field($_POST['setting_action']) : '';
$recall_id = isset($_POST['recall_id']) ? sanitize_text_field($_POST['recall_id']) : '';


echo "Recall Shipping for: ".$recall_id;
*/





