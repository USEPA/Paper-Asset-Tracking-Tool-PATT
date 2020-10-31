<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
// set default filter for agents and customers //not true
global $current_user, $wpscfunction;

if (!$current_user->ID) die();

$setting_action = isset($_POST['setting_action']) ? sanitize_text_field($_POST['setting_action']) : '';
$return_id = isset($_POST['return_id']) ? sanitize_text_field($_POST['return_id']) : '';
//$ticket_id = isset($_POST['ticket_id']) ? sanitize_text_field($_POST['ticket_id']) : '';

//$recall_ids = $_REQUEST['recall_ids']; 
//$num_of_recalls = count($recall_ids);


ob_start();
// echo 'This has got to be something';
//echo "Recall ID: ".$recall_id."<br>";
echo 'Are you sure you want to cancel Decline ID: D-'.$return_id.'? This action cannot be undone.';
/*
echo "Return Status for: ";
print_r($recall_ids);
*/
echo "<br>";



$body = ob_get_clean();
ob_start();
?>
<button type="button" class="btn wpsc_popup_close"  style="background-color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_text_color']?> !important;"   onclick="wpsc_modal_close();"><?php _e('No','wpsc-export-ticket');?></button>

<button type="button" id="button_cancel_yes" class="btn wpsc_popup_action" style="background-color:<?php echo $wpsc_appearance_modal_window['wpsc_action_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_modal_window['wpsc_action_button_text_color']?> !important;" onclick="wppatt_cancel_return();"><?php _e('Yes','supportcandy');?></button>

<script>

	//jQuery("#button_status_submit").hide();
	
/*
	jQuery('#status_dropdown').change(function() {	
		if( jQuery('#status_dropdown').val() =='') {
			jQuery("#button_status_submit").hide();						
		} else {
			jQuery("#button_status_submit").show();
		}
	})	
*/

	function wppatt_cancel_return() {
		console.log('cancelling ');
		//var recall_id_array = <?php echo json_encode($recall_ids) ?>;
		var return_id = '<?php echo $return_id ?>';
		//var ticket_id = '<?php echo $ticket_id ?>';

		console.log('return id: '+return_id);
		console.log('ticket id: '+ticket_id);
		
		jQuery.post(
		   '<?php echo WPPATT_PLUGIN_URL; ?>includes/admin/pages/scripts/update_return_details.php',{
		    return_id: return_id,
		    type: 'cancel',
		}, 
	    function (response) {
			//alert('Recall Id: R-'+recall_id+' Cancelled '+response);
			console.log(response);
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