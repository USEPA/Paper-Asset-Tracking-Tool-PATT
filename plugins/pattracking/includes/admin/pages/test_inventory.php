<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpdb, $current_user, $wpscfunction;

$box_id = $_GET['box_id'];

$box_details = $wpdb->get_row(
"SELECT wpqa_wpsc_epa_storage_location.digitization_center as digitization_center
FROM wpqa_wpsc_epa_boxinfo
INNER JOIN wpqa_wpsc_epa_storage_location ON wpqa_wpsc_epa_boxinfo.storage_location_id = wpqa_wpsc_epa_storage_location.id
WHERE wpqa_wpsc_epa_boxinfo.box_id = '" . $box_id . "'"
			);

$digitization_center = $box_details->digitization_center;

if (preg_match("/^[0-9]{7}-[0-9]{1,3}$/", $box_id)) {
?>
<style>
#dropdown_container {
    left: 50% !important;
    right: auto !important;
    text-align: center !important;
}
</style>
<div id="dropdown_container">
<br /><br />
<strong>Aisle</strong>
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

<div id="shelf_position">
    </div>
		
		<script>

			jQuery(document).ready(function() {
			jQuery("#bay_div").hide();
		  // event called when the aisle select is changed
        jQuery("#aisle_selector").change(function(){
            jQuery("#shelf_position").hide();
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
            jQuery("#shelf_position").hide();
            } else {
            jQuery("#shelf_position").show();
            }
            jQuery("#shelf_position").load("<?php echo WPPATT_PLUGIN_URL; ?>includes/admin/pages/scripts/shelf_position.php?aisle="+jQuery('#aisle_selector').val()+"&bay="+jQuery('#bay_selector').val()+"&center=<?php echo $digitization_center ?>&box_id=<?php echo $_GET['box_id'] ?>"); 
         });
	
});		
		</script>
<?php 
    
} else {
    
echo 'Please pass a valid Box ID.';

}

?>
    
				