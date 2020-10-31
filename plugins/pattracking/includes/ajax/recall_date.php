<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
// set default filter for agents and customers //not true
global $current_user, $wpscfunction;

if (!$current_user->ID) die();

$setting_action = isset($_POST['setting_action']) ? sanitize_text_field($_POST['setting_action']) : '';
$recall_id = isset($_POST['recall_id']) ? sanitize_text_field($_POST['recall_id']) : '';
$date_type = isset($_POST['date_type']) ? sanitize_text_field($_POST['date_type']) : '';
$title = isset($_POST['title']) ? sanitize_text_field($_POST['title']) : '';
$ticket_id = isset($_POST['ticket_id']) ? sanitize_text_field($_POST['ticket_id']) : '';
$old_date = isset($_POST['old_date']) ? sanitize_text_field($_POST['old_date']) : '';
//$current_date = "11/11/1911";
$current_date = $old_date;

/*
switch ($date_type) {
	case 'request_date':
		$title = 'Request';
		break;
	case 'received_date':
		$title = 'Received';
		break;
	case 'returned_date':
		$title = 'Returned';
		break;
	default:
		echo 'something is broken in the switch';
}
*/


ob_start();

//echo "Recall ".$title." Date for: ".$recall_id;
?>

<label class="wpsc_ct_field_label">Current <?php echo $title; ?> Date: </label>
	<span id="modal_current_date" class=""><?php echo $current_date; ?></span>
<br>
<label class="wpsc_ct_field_label">New <?php echo $title; ?> Date: </label>
	<span id="modal_new_date" class=""></span>
	<input type="hidden" name="new_date_input" value="">
<br>
	<div id="modal_date_editor" class="calendar"></div>

<script>
	
	jQuery("#button_date_submit").hide();

	jQuery('#modal_date_editor').datepicker({
// 		dateFormat: 'mm/dd/yy',
		dateFormat: 'yy-mm-dd',
		onSelect: function(date) {
			
			var time = new Date(); // for now
			var newDateTime = date+' '+time.getHours()+':'+time.getMinutes()+':'+time.getSeconds();
			//var newDate = new Date(newDateTime);
			var dateAr = date.split('-');
			var newDate = dateAr[1] + '/' + dateAr[2] + '/' + dateAr[0];
			
			//var now_utc =  Date.UTC(newDate.getUTCFullYear(), newDate.getUTCMonth(), newDate.getUTCDate(), newDate.getUTCHours(), newDate.getUTCMinutes(), newDate.getUTCSeconds());
			
		    jQuery('#modal_new_date').text(newDate);
		    //jQuery('input[name=new_date_input]').val(date); 
		    jQuery('input[name=new_date_input]').val(newDateTime); 
		    
		    //update_database(date, '<?php echo $title; ?>'); //function in recall-details  
			jQuery("#button_date_submit").show();
    	},
	});
	
</script>

<?php

$body = ob_get_clean();


ob_start();
?>
<button type="button" class="btn wpsc_popup_close"  style="background-color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_text_color']?> !important;"   onclick="wpsc_modal_close();"><?php _e('Close','wpsc-export-ticket');?></button>

<button type="button" id="button_date_submit" class="btn wpsc_popup_action" style="background-color:<?php echo $wpsc_appearance_modal_window['wpsc_action_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_modal_window['wpsc_action_button_text_color']?> !important;" onclick="wppatt_set_request_date();"><?php _e('Save','supportcandy');?></button>
<script>
jQuery("#button_date_submit").hide();

				

function wppatt_set_request_date(){	
	
	console.log('setting date for: '+ '<?php echo $recall_id ?>');
	jQuery.post(
	   '<?php echo WPPATT_PLUGIN_URL; ?>includes/admin/pages/scripts/update_recall_details.php',{
	    recall_id: '<?php echo $recall_id ?>',
	    old_date: '<?php echo $current_date ?>',
	    new_date: jQuery('input[name=new_date_input]').val(),
	    type: '<?php echo $date_type; ?>',
		title: '<?php echo $title; ?>',
		ticket_id: '<?php echo $ticket_id; ?>',
	}, 
    function (response) {
		//alert('updated: '+response);
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



//echo "Recall Request Date for: ".$recall_id;
//echo $modal_content;