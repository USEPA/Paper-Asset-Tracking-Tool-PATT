<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user, $wpscfunction, $wpdb;

if (!isset($_SESSION)) {
    session_start();    
}

$dc_id = $_POST["dc_id"];
$_SESSION["dc_id"] = $dc_id;


$box_id  = isset($_POST['box_id']) ? sanitize_text_field($_POST['box_id']) : '' ;
        
ob_start();

$box_details = $wpdb->get_row(
"SELECT wpqa_wpsc_epa_storage_location.digitization_center as digitization_center, wpqa_wpsc_epa_boxinfo.box_id as patt_box_id, wpqa_wpsc_ticket.request_id as request_id
FROM wpqa_wpsc_epa_boxinfo
INNER JOIN wpqa_wpsc_epa_storage_location ON wpqa_wpsc_epa_boxinfo.storage_location_id = wpqa_wpsc_epa_storage_location.id
INNER JOIN wpqa_wpsc_ticket ON wpqa_wpsc_epa_boxinfo.ticket_id = wpqa_wpsc_ticket.id
WHERE wpqa_wpsc_epa_boxinfo.id = '" . $box_id . "'"
			);

$digitization_center = $box_details->digitization_center;
$patt_box_id = $box_details->patt_box_id;
$patt_ticket_id = $box_details->request_id;

?>

<style>
#wrapper {
  margin-right: 500px;
}
#content {
  float: left;
  width: 100%;
}
#sidebar {
  float: right;
  width: 500px;
  margin-right: -500px;
}
#cleared {
  clear: both;
}
</style>

<div id="wrapper">
  <div id="content">
<span id="aisle_tag"><strong>Aisle</strong></span>
<select id="aisle_selector" name="aisle_selector" class="form-control">    
<option value="0">--Select Aisle--</option>
<?php
$digitization_center_aisle_total = 50;

$aisle_array = range(1, $digitization_center_aisle_total);

foreach ($aisle_array as $value) {
    $get_available_aisle = $wpdb->get_row(
				"SELECT count(id) as count
FROM wpqa_wpsc_epa_storage_location
WHERE aisle = '" . $value . "' AND digitization_center = '" . $digitization_center . "'"
			);
			
$remaining_boxes = 100 - $get_available_aisle->count;
$disabled = $remaining_boxes != 0 ? "" : "disabled";

  echo '<option value="'.$value.'" '.$disabled .'>Aisle #' . $value . ' [' . (100 - $get_available_aisle->count) . ' boxes remain]'.'</option>';

}

?>
</select>
<br /><br />
<div id="bay_div">
<strong>Bay</strong>
<select name="bay_selector" class="form-control" id="bay_selector">
</select>
<br /><br />
  </div>
  <div id="sidebar">
  </div>
  <div id="cleared"></div>
</div>
		
		<script>

			jQuery(document).ready(function() {
			    
			jQuery("#bay_div").hide();
		  // event called when the aisle select is changed
        jQuery("#aisle_selector").change(function(){
            jQuery("#shelf_position").hide();
            jQuery("#sidebar").hide();
            // get the currently selected aisle selector ID
            var aisleId = jQuery(this).val();
            
            jQuery.ajax({
                // make the ajax call to server and pass the aisle ID as a GET variable
                url: "<?php echo WPPATT_PLUGIN_URL; ?>includes/admin/pages/scripts/bay_query.php?aisle_id=" + aisleId + "&center=<?php echo $digitization_center ?>",
            }).done(function(data) {
                // our ajax call is finished, we have the data returned from the server in a var called data
                data = JSON.parse(data);
            if (aisleId == '0') {
            jQuery("#bay_div").hide();
            } else {
            jQuery("#bay_div").show();
            }
                    jQuery("#bay_selector").empty();
                // loop through our returned data and add an option to the select for each bay returned
                        jQuery('#bay_selector').append(jQuery('<option>', {value:0, text:'---Select Bay---'}).attr("disabled", false));
                jQuery.each(data, function(i, item) {

var matches = /\[.*?(\d+).*?\]/g.exec(item);
//alert(matches[1]); 

        if (matches[1] = 0) {
        jQuery('#bay_selector').append(jQuery('<option>', {value:i, text:item}).attr("disabled", true));
        } else {
        jQuery('#bay_selector').append(jQuery('<option>', {value:i, text:item}).attr("disabled", false));
        }
                });

            });
        });
        
         jQuery("#bay_selector").change(function(){ 
            var bayId = jQuery(this).val();
            if (bayId == '0') {
            jQuery("#sidebar").hide();
            } else {
            jQuery("#sidebar").show();
            }
            jQuery("#sidebar").load("<?php echo WPPATT_PLUGIN_URL; ?>includes/admin/pages/scripts/shelf_position.php?aisle="+jQuery('#aisle_selector').val()+"&bay="+jQuery('#bay_selector').val()+"&center=<?php echo $digitization_center ?>&box_id=<?php echo $patt_box_id ?>&ticket_id=<?php echo $patt_ticket_id ?>"); 
         });
	
});		
		</script>

<?php 
$body = ob_get_clean();
ob_start();
?>
<button type="button" class="btn wpsc_popup_close"  style="background-color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_text_color']?> !important;"   onclick="wpsc_modal_close();"><?php _e('Close','wpsc-export-ticket');?></button>
<?php 
$footer = ob_get_clean();

$output = array(
  'body'   => $body,
  'footer' => $footer
);
echo json_encode($output);
