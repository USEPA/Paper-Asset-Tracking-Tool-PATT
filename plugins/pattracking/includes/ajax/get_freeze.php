<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user, $wpscfunction, $wpdb;

if (!isset($_SESSION)) {
    session_start();    
}


ob_start();

  $rfid_details = $wpdb->get_results("
SELECT DISTINCT
Reader_Name
FROM wpqa_wpsc_epa_rfid_data
");

			$rfid_readerid_array = array();

			foreach ($rfid_details as $info) {
				$readerid = $info->Reader_Name;
				array_push($rfid_readerid_array, $readerid);
			}
			

?>

<form>
<strong>RFID Reader ID:</strong><br />
<select id="readerid" name="readerid">
<?php
foreach($rfid_readerid_array as $key => $value):
echo '<option value="'.$value.'">'.$value.'</option>';
endforeach;
?>
</select>
</form>
<?php 
$body = ob_get_clean();
ob_start();
?>
<button type="button" class="btn wpsc_popup_close"  style="background-color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_text_color']?> !important;"   onclick="wpsc_modal_close();"><?php _e('Close','wpsc-export-ticket');?></button>
<button type="button" class="btn wpsc_popup_action" style="background-color:<?php echo $wpsc_appearance_modal_window['wpsc_action_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_modal_window['wpsc_action_button_text_color']?> !important;" onclick="wpsc_clear_rfid_readerid();">Clear Results</button>
<script>
function wpsc_clear_rfid_readerid(){		
		   jQuery.post(
   '<?php echo WPPATT_PLUGIN_URL; ?>includes/admin/pages/scripts/clear_rfid_readerid.php',{
postvarsreaderid: jQuery("#readerid").val()
}, 
   function (response) {
      if(!alert(response)){window.location.reload();}
      window.location.replace("/wordpress3/wp-admin/admin.php?page=rfid");
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