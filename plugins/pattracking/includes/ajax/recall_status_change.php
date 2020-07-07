<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
// set default filter for agents and customers //not true
global $current_user, $wpscfunction;

if (!$current_user->ID) die();

$setting_action = isset($_POST['setting_action']) ? sanitize_text_field($_POST['setting_action']) : '';
//$recall_ids = isset($_POST['recall_ids']) ? sanitize_text_field($_POST['recall_ids']) : '';
$recall_ids = $_REQUEST['recall_ids']; 
$num_of_recalls = count($recall_ids);


ob_start();
// echo 'This has got to be something';
echo "Number of Recall IDs: ".$num_of_recalls."<br>";
/*
echo "Recall Status for: ";
print_r($recall_ids);
*/
echo "<br>";

foreach($recall_ids as $id) {
	echo '<label class="wpsc_ct_field_label">Recall ID: </label>';
	echo '<span id="modal_recall_id" class="">'.$id.'</span><br>';	
}

?>

<label class="wpsc_ct_field_label">New Status: </label>
<select id="status_dropdown" name="new_recall_status" value="" >
	<option value""></option>
	<option value"recalled">Recalled</option>
	<option value"shipped">Shipped</option>
	<option value"received">Received</option>
	<option value"on loan">On Loan</option>
	<option value"freeze">Freeze</option>	
	<option value"shipped back">Shipped Back</option>
	<option value"back at digitization center">Back at Digitization Center</option>
</select>

<script>

</script>

<?php

$body = ob_get_clean();
ob_start();
?>
<button type="button" class="btn wpsc_popup_close"  style="background-color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_text_color']?> !important;"   onclick="wpsc_open_ticket(<?php echo htmlentities($ticket_id)?>);wpsc_modal_close();"><?php _e('Close','wpsc-export-ticket');?></button>

<button type="button" id="button_status_submit" class="btn wpsc_popup_action" style="background-color:<?php echo $wpsc_appearance_modal_window['wpsc_action_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_modal_window['wpsc_action_button_text_color']?> !important;" onclick="wppatt_set_recall_status();"><?php _e('Save','supportcandy');?></button>

<script>

	jQuery("#button_status_submit").hide();
	
	jQuery('#status_dropdown').change(function() {	
		if( jQuery('#status_dropdown').val() =='') {
			jQuery("#button_status_submit").hide();						
		} else {
			jQuery("#button_status_submit").show();
		}
	})	

	function wppatt_set_recall_status() {
		console.log('setting status for: ');
		var recall_id_array = <?php echo json_encode($recall_ids) ?>;

		console.log(recall_id_array);
		
		jQuery.post(
		   '<?php echo WPPATT_PLUGIN_URL; ?>includes/admin/pages/scripts/update_recall_details.php',{
		    recall_ids: <?php echo json_encode($recall_ids) ?>,
		    new_status: jQuery('#status_dropdown').val(),
		    type: 'status',
		}, 
	    function (response) {
			alert('Updated: '+response);
			window.location.reload();
	
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