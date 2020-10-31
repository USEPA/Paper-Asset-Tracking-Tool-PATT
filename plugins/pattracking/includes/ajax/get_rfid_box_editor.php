<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user, $wpscfunction, $wpdb;

if (!isset($_SESSION)) {
    session_start();    
}


$box_ids = strip_tags($_POST["postvarsboxid"]);

//$box_id  = isset($_POST['box_id']) ? sanitize_text_field($_POST['box_id']) : '' ;
        
ob_start();

/*
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
*/

?>

<button type="button" class="btn btn-sm wpsc_action_btn" id="wpsc_individual_refresh_btn" onclick="window.open('<?php echo WPPATT_PLUGIN_URL; ?>includes/ajax/pdf/box_label.php?id=<?php echo $box_ids; ?>');" style="background-color:#337ab7 !important;color:#FFFFFF !important;"><i class="fas fa-tags"></i> Regenerate Box Labels</button>
<button type="button" class="btn btn-sm wpsc_action_btn" id="wpsc_individual_destruction_completed_btn" onclick="window.open('<?php echo WPPATT_PLUGIN_URL; ?>includes/admin/pages/scripts/update_destruction.php?id=<?php echo $box_ids; ?>');" style="background-color:#337ab7 !important;color:#FFFFFF !important;"><i class="fas fa-ban"></i> Destruction Completed</button>

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