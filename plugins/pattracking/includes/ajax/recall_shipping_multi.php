<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
// set default filter for agents and customers //not true
global $current_user, $wpscfunction;

if (!$current_user->ID) die();

$setting_action = isset($_POST['setting_action']) ? sanitize_text_field($_POST['setting_action']) : '';
//$recall_id = isset($_POST['recall_id']) ? sanitize_text_field($_POST['recall_id']) : '';
$recall_ids = $_REQUEST['recall_ids'];
$return_ids = $_REQUEST['return_ids'];
$shipping_table_ids = $_REQUEST['shipping_table_ids'];
$category = isset($_POST['category']) ? sanitize_text_field($_POST['category']) : '';
$called_on_page = isset($_POST['from_page']) ? sanitize_text_field($_POST['from_page']) : '';
 
//$GLOBALS['recall_ids'] = 'TESTTTT';
$num_of_recalls = count($recall_ids);
$ticket_id = '0000001'; // Not actually used. Passed along via AJAX, but not used there either. 

$subfolder_path = site_url( '', 'relative'); 


ob_start();
//echo 'This has got to be something about shipping, right? RIGHT?!?!';
/*
echo "Number of Recall IDs!: ".$num_of_recalls."<br>";
$encoded_recall_ids = json_encode($recall_ids);
echo $encoded_recall_ids; 
echo "<br>";
print_r($recall_ids);
*/


?>

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
    
    var category = '<?php echo $category ?>';
    var ticket_id = <?php echo $ticket_id; ?>; 
	var subfolder = '<?php echo $subfolder_path; ?>';
	var recall_ids = <?php echo json_encode($recall_ids); ?>;
	var return_ids = <?php echo json_encode($return_ids); ?>;	
	var shipping_table_ids = <?php echo json_encode($shipping_table_ids); ?>;
    var get_link = '';
    var put_link = '';
    var title = '';
    
    console.log('The Category: ');
    console.log(category);
    
    if( category == 'return') {
	    get_link = subfolder+"/wp-content/plugins/pattracking/includes/ajax/fetch_shipping_data_multi.php?ticket_id="+ticket_id+"&return_ids="+return_ids
	    						+"&category=return";
	    put_link = subfolder+"/wp-content/plugins/pattracking/includes/ajax/fetch_shipping_data_multi.php?ticket_id="+ticket_id+"&category=return";
	    title = 'Return ID';
    } else if( category == 'recall') {
	    get_link = subfolder+"/wp-content/plugins/pattracking/includes/ajax/fetch_shipping_data_multi.php?ticket_id="+ticket_id+"&recall_ids="+recall_ids;
	    put_link = subfolder+"/wp-content/plugins/pattracking/includes/ajax/fetch_shipping_data_multi.php?ticket_id="+ticket_id;
	    title = 'Recall ID';
    } else if( category == 'shipping-status-editor') {
	    get_link = subfolder+"/wp-content/plugins/pattracking/includes/ajax/fetch_shipping_data_multi.php?ticket_id="+ticket_id+"&shipping_table_ids="+shipping_table_ids+"&category=shipping-status-editor";
	    put_link = subfolder+"/wp-content/plugins/pattracking/includes/ajax/fetch_shipping_data_multi.php?ticket_id="+ticket_id+"&category=shipping-status-editor";
	    title = 'ID';
    } else {
	    get_link = subfolder+"/wp-content/plugins/pattracking/includes/ajax/fetch_shipping_data_multi.php?ticket_id="+ticket_id+"&recall_ids="+recall_ids;
	    put_link = subfolder+"/wp-content/plugins/pattracking/includes/ajax/fetch_shipping_data_multi.php?ticket_id="+ticket_id;	    
	    title = 'Recall ID';	    
    }
     
           
	jQuery('#grid_table').jsGrid({
		width: "auto",
		height: "auto",
		//filtering: true,
// 		inserting:true,
		editing: true,
		sorting: true,
		deleting: false,
		paging: true,
		autoload: true,
		pageSize: 20,
		pageButtonCount: 5,
		deleteConfirm: "Do you really want to delete this tracking number?",
		controller: {
			loadData: function(filter){
/*
				var ticket_id = <?php echo $ticket_id; ?>; 
				var subfolder = '<?php echo $subfolder_path; ?>';
				var recall_ids = <?php echo json_encode($recall_ids); ?>;
*/
				console.log('recall ids:::');
				console.log(recall_ids);
				console.log('return ids:::');
				console.log(return_ids);
				console.log('Shipping Table ids:::');
				console.log(shipping_table_ids);
				console.log(subfolder);
				console.log('The Category 2: ');
				console.log(category);
				console.log(get_link);
				console.log('filter: ');
				console.log(filter);
				return jQuery.ajax({
					type: "GET",
					category: category,
					url: get_link,
//					url: subfolder+"/wp-content/plugins/pattracking/includes/ajax/fetch_shipping_data_multi.php?ticket_id="+ticket_id+"&recall_ids="+recall_ids,
// 					url: subfolder+"/wp-content/plugins/pattracking/includes/ajax/fetch_shipping_data.php",
					data: filter
				});
		    },
/*
			insertItem: function(item){
				var ticket_id = <?php echo $ticket_id; ?>; 
				var subfolder = '<?php echo $subfolder_path; ?>';
				return jQuery.ajax({
					type: "POST",
					url: subfolder+"/wp-content/plugins/pattracking/includes/ajax/fetch_shipping_data_multi.php?ticket_id="+ticket_id,
					data:item
				});
			},
*/
			updateItem: function(item){
				var ticket_id = <?php echo $ticket_id; ?>; 
				var subfolder = '<?php echo $subfolder_path; ?>';
				//var recall_ids = <?php echo json_encode($recall_ids); ?>;
				console.log('update item');
				console.log(category);
				console.log(put_link);
				console.log(item);				
				return jQuery.ajax({
					type: "PUT",
					category: category,
					url: put_link,
					//url: subfolder+"/wp-content/plugins/pattracking/includes/ajax/fetch_shipping_data_multi.php?ticket_id="+ticket_id,
					data: item,
					success: function(response) {
						console.log('Shipping Update Success');
						console.log(response);
					}
				});
			},
/*
			deleteItem: function(item){
				var ticket_id = <?php echo $ticket_id; ?>; 
				var subfolder = '<?php echo $subfolder_path; ?>';
				return jQuery.ajax({
					type: "DELETE",
					url: subfolder+"/wp-content/plugins/pattracking/includes/ajax/fetch_shipping_data_multi.php?ticket_id="+ticket_id,
					data: item
				});
			},
*/
		},
		fields: [
		{
			name: "id",
 			type: "hidden",
 			css: 'hide'
		},
		{
			name: "recall_id",
// 			title: "Recall ID",
			title: title,
			type: "text",
			width: 50	,
			editing: false,
			inserting: false 
		},
		{
			name: "ticket_id",
			title: "Recall ID",
			type: "text",
			width: 26, 
 			type: "hidden",
 			css: 'hide'			
		},
		{
			name: "tracking_number",
			title: "Tracking Number",
			type: "text", 
			width: 130, 
// 			validate: "required",
			validate: function(value, item) { 
			    
			    var isTrue = '';
			    var string = "DHL:";
			
			
			    if ((/\b(1Z ?[0-9A-Z]{3} ?[0-9A-Z]{3} ?[0-9A-Z]{2} ?[0-9A-Z]{4} ?[0-9A-Z]{3} ?[0-9A-Z]|T\d{3} ?\d{4} ?\d{3})\b/i.test(value)
			    || /\b((420 ?\d{5} ?)?(91|92|93|94|01|03|04|70|23|13)\d{2} ?\d{4} ?\d{4} ?\d{4} ?\d{4}( ?\d{2,6})?)\b/i.test(value)
			    || /\b((M|P[A-Z]?|D[C-Z]|LK|E[A-C]|V[A-Z]|R[A-Z]|CP|CJ|LC|LJ) ?\d{3} ?\d{3} ?\d{3} ?[A-Z]?[A-Z]?)\b/i.test(value)
			    || /\b(82 ?\d{3} ?\d{3} ?\d{2})\b/i.test(value)
			    || /\b(((96\d\d|6\d)\d{3} ?\d{4}|96\d{2}|\d{4}) ?\d{4} ?\d{4}( ?\d{3})?)\b/i.test(value)) && (!value.toUpperCase().includes(string))) {
			    var isTrue = true;
			    } else if((/\b([a-zA-Z0-9]{10,43})$/i.test(value)) && (value.toUpperCase().includes(string))) {
			    var isTrue = true;
			    } else {
			    var isTrue = false;
			    }
			    
			    return isTrue;
			},
/*
			validate: function(value, item) { 
			    
			    var isTrue = '';
			    if (/\b(1Z ?[0-9A-Z]{3} ?[0-9A-Z]{3} ?[0-9A-Z]{2} ?[0-9A-Z]{4} ?[0-9A-Z]{3} ?[0-9A-Z]|T\d{3} ?\d{4} ?\d{3})\b/i.test(value)
			    || /\b((420 ?\d{5} ?)?(91|92|93|94|01|03|04|70|23|13)\d{2} ?\d{4} ?\d{4} ?\d{4} ?\d{4}( ?\d{2,6})?)\b/i.test(value)
			    || /\b((M|P[A-Z]?|D[C-Z]|LK|E[A-C]|V[A-Z]|R[A-Z]|CP|CJ|LC|LJ) ?\d{3} ?\d{3} ?\d{3} ?[A-Z]?[A-Z]?)\b/i.test(value)
			    || /\b(82 ?\d{3} ?\d{3} ?\d{2})\b/i.test(value)
			    || /\b(((96\d\d|6\d)\d{3} ?\d{4}|96\d{2}|\d{4}) ?\d{4} ?\d{4}( ?\d{3})?)\b/i.test(value)
			    || /\b(\d{4}[- ]?\d{4}[- ]?\d{2}|\d{3}[- ]?\d{8}|[A-Z]{3}\d{7})\b/i.test(value)) {
			    	var isTrue = true;
			    } else {
			    	var isTrue = false;
			    }
			    
			    return isTrue;      
        },
*/

			formatter: function (cellvalue, options, rowObject) {
		    	return "<a href='javascript:void(0);' class='anchor usergroup_name link'>" + cellvalue + '</a>';
		    }
		},
		{
			name: "company_name",
			title: "Shipping Company",
			type: "select", 
			width: 36, 
			items: [
				{ Name: "", Id: '' },
				{ Name: "UPS", Id: 'ups' },
				{ Name: "FedEx", Id: 'fedex' },
				{ Name: "USPS", Id: 'usps' },
				{ Name: "DHL", Id: 'dhl' },
			], 
			valueField: "Id", 
			textField: "Name", 
			//validate: "required"
            editing: false,    
            inserting: false,    
            css: "hide"
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
			type: "control" , 
			deleteButton: false
		}
		]
	});
});


</script>


<?php

$body = ob_get_clean();
ob_start();
?>
<!-- <button type="button" class="btn wpsc_popup_close"  style="background-color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_text_color']?> !important;"   onclick="wpsc_modal_close();window.location.reload();"><?php _e('Close','wpsc-export-ticket');?></button> -->

<button type="button" class="btn wpsc_popup_close"  style="background-color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_text_color']?> !important;"   onclick="reset_datatable();wpsc_modal_close();<?php if($called_on_page == 'recall-details' || $called_on_page == 'return-details' ) { ?>window.location.reload();<?php } ?>"><?php _e('Close','wpsc-export-ticket');?></button>


<script>

	jQuery("#button_status_submit").hide();
// 	jQuery('.jsgrid-delete-button').hide(); //not working

	
	jQuery('#status_dropdown').change(function() {	
		if( jQuery('#status_dropdown').val() =='') {
			jQuery("#button_status_submit").hide();						
		} else {
			jQuery("#button_status_submit").show();
		}
	})	

	function wppatt_set_recall_shipping_multi() {
		console.log('setting shipping for: ');
		var recall_id_array = <?php echo json_encode($recall_ids) ?>;

		console.log(recall_id_array);
		
/*
		jQuery.post(
		   '<?php echo WPPATT_PLUGIN_URL; ?>includes/admin/pages/scripts/update_recall_details.php',{
		    recall_ids: '<?php echo json_encode($recall_ids) ?>',
		    new_status: jQuery('#status_dropdown').val(),
		    type: 'status',
		}, 
	    function (response) {
			alert('Updated: '+response);
			window.location.reload();
	
	    });
*/
	}
	
	function reset_datatable() {
		let the_page = '<?php echo $called_on_page ?>';
		
		if( the_page == 'return-dashboard') {
			jQuery('#tbl_templates_return').DataTable().ajax.reload();
		}
		
		if( the_page == 'recall-dashboard') {
			jQuery('#tbl_templates_recall').DataTable().ajax.reload();
		}
		
		if( the_page == 'shipping-dashboard') {
			jQuery('#tbl_templates_shipping').DataTable().ajax.reload();
		}
		
	}
	
</script>



<?php 
$footer = ob_get_clean();

$output = array(
  'body'   => $body,
  'footer' => $footer
);
echo json_encode($output);