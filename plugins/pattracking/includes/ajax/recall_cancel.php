<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
// set default filter for agents and customers //not true
global $current_user, $wpscfunction;

if (!$current_user->ID) die();

$setting_action = isset($_POST['setting_action']) ? sanitize_text_field($_POST['setting_action']) : '';
$recall_id = isset($_POST['recall_id']) ? sanitize_text_field($_POST['recall_id']) : '';
$ticket_id = isset($_POST['ticket_id']) ? sanitize_text_field($_POST['ticket_id']) : '';

$recall_ids = $_REQUEST['recall_ids']; 
$num_of_recalls = count($recall_ids);


ob_start();
// echo 'This has got to be something';
//echo "Recall ID: ".$recall_id."<br>";
echo 'Are you sure you want to cancel Recall ID: R-'.$recall_id.'? This action cannot be undone.';
/*
echo "Recall Status for: ";
print_r($recall_ids);
*/
echo "<br>";

/*
foreach($recall_ids as $id) {
	echo '<label class="wpsc_ct_field_label">Recall ID: </label>';
	echo '<span id="modal_recall_id" class="">'.$id.'</span><br>';	
}
*/

?>

<!--
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
-->

<?php

$body = ob_get_clean();
ob_start();
?>
<button type="button" class="btn wpsc_popup_close"  style="background-color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_text_color']?> !important;"   onclick="wpsc_modal_close();"><?php _e('No','wpsc-export-ticket');?></button>

<button type="button" id="button_cancel_yes" class="btn wpsc_popup_action" style="background-color:<?php echo $wpsc_appearance_modal_window['wpsc_action_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_modal_window['wpsc_action_button_text_color']?> !important;" onclick="wppatt_cancel_recall();"><?php _e('Yes','supportcandy');?></button>

<script>

	//jQuery("#button_status_submit").hide();
	
	jQuery('#status_dropdown').change(function() {	
		if( jQuery('#status_dropdown').val() =='') {
			jQuery("#button_status_submit").hide();						
		} else {
			jQuery("#button_status_submit").show();
		}
	})	

	function wppatt_cancel_recall() {
		console.log('cancelling ');
		//var recall_id_array = <?php echo json_encode($recall_ids) ?>;
		var recall_id = '<?php echo $recall_id ?>';
		var ticket_id = '<?php echo $ticket_id ?>';

		console.log('recall id: '+recall_id);
		console.log('ticket id: '+ticket_id);
		
		jQuery.post(
		   '<?php echo WPPATT_PLUGIN_URL; ?>includes/admin/pages/scripts/update_recall_details.php',{
		    recall_id: recall_id,
		    ticket_id: ticket_id,
		    type: 'cancel',
		}, 
	    function (response) {
			//alert('Recall Id: R-'+recall_id+' Cancelled '+response);
			alert(response);
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