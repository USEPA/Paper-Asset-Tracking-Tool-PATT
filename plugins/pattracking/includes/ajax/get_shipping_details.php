<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpdb, $current_user, $wpscfunction;

$ticket_id 	 = isset($_POST['ticket_id']) ? intval($_POST['ticket_id']) : 0 ;

$subfolder_path = site_url( '', 'relative'); 

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
        <div class="container>  
   <br />
   <div class="table-responsive">
    <div id="grid_table"></div>
   </div>  
  </div>
<link rel="stylesheet" type="text/css" href="<?php echo WPPATT_PLUGIN_URL.'includes/admin/css/jsgrid.min.css';?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo WPPATT_PLUGIN_URL.'includes/admin/css/jsgrid-theme.min.css';?>"/>
<script type="text/javascript" src="<?php echo WPPATT_PLUGIN_URL.'includes/admin/js/jsgrid.min.js';?>"></script>

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
        url: subfolder+"/wp-content/plugins/pattracking/includes/ajax/fetch_shipping_data.php?ticket_id="+ticket_id,
        data: filter
       });
      },
      insertItem: function(item){
       var ticket_id = <?php echo $ticket_id; ?>; 
       var subfolder = '<?php echo $subfolder_path; ?>';
       return jQuery.ajax({
        type: "POST",
        url: subfolder+"/wp-content/plugins/pattracking/includes/ajax/fetch_shipping_data.php?ticket_id="+ticket_id,
        data:item
       });
      },
      updateItem: function(item){
       var ticket_id = <?php echo $ticket_id; ?>; 
       var subfolder = '<?php echo $subfolder_path; ?>';
       return jQuery.ajax({
        type: "PUT",
        url: subfolder+"/wp-content/plugins/pattracking/includes/ajax/fetch_shipping_data.php?ticket_id="+ticket_id,
        data: item
       });
      },
      deleteItem: function(item){
       var ticket_id = <?php echo $ticket_id; ?>; 
       var subfolder = '<?php echo $subfolder_path; ?>';
       return jQuery.ajax({
        type: "DELETE",
        url: subfolder+"/wp-content/plugins/pattracking/includes/ajax/fetch_shipping_data.php?ticket_id="+ticket_id,
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
                 });
</script>

<?php 
$body = ob_get_clean();
ob_start();
?>
<button type="button" class="btn wpsc_popup_close"  style="background-color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_text_color']?> !important;"   onclick="wpsc_open_ticket(<?php echo htmlentities($ticket_id)?>);wpsc_modal_close();"><?php _e('Close','wpsc-export-ticket');?></button>

<?php 
$footer = ob_get_clean();

$output = array(
  'body'   => $body,
  'footer' => $footer
);
echo json_encode($output);