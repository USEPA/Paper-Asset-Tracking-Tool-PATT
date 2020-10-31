<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user, $wpscfunction, $wpdb;

$subfolder_path = site_url( '', 'relative'); 

if (!isset($_SESSION)) {
    session_start();    
}

$box_id = $_POST["box_id"];
        
ob_start();

$box_patt_id = $wpdb->get_row("SELECT box_id, lan_id FROM wpqa_wpsc_epa_boxinfo WHERE id = '" . $box_id . "'");
$patt_box_id = $box_patt_id->box_id;
$user_id = $box_patt_id->lan_id;

$pattdocid = $wpdb->get_row("SELECT wpqa_wpsc_epa_folderdocinfo.id as folderfileid 
FROM wpqa_wpsc_epa_folderdocinfo, wpqa_wpsc_epa_boxinfo
WHERE wpqa_wpsc_epa_boxinfo.id = wpqa_wpsc_epa_folderdocinfo.box_id AND wpqa_wpsc_epa_folderdocinfo.box_id = '" . $box_id . "'");
$folderfile_id = $pattdocid->folderfileid;

/*$curl = curl_init();
$url = 'https://wamssoprd.epa.gov/iam/governance/scim/v1/Users?filter=userName%20eq%20'.$box_lan_id;
$headers = [
    'Cache-Control: no-cache',
	'Authorization: Basic c3ZjX3NjaW1fZWNtczpwakNSNjRUSDJkbng='
];
        curl_setopt($curl,CURLOPT_URL, $url);
        curl_setopt($curl,CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl,CURLOPT_MAXREDIRS, 10);
        curl_setopt($curl,CURLOPT_TIMEOUT, 30);
        curl_setopt($curl,CURLOPT_HTTP_VERSION,CURL_HTTP_VERSION_1_1);
        curl_setopt($curl,CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($curl,CURLOPT_HTTPHEADER, $headers);
		//curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
		//curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {

$json = json_decode($response, true);
$results = $json['totalResults'];*/
?>

<form>
<?php
//placeholders with 'Enter...' only appear if that field is empty in the database, otherwise show current data

if(!empty($user_id) && $user_id != 1) {
    echo "<strong>LAN ID:</strong><br /><input type='text' id='lanid' placeholder= '$user_id'>";
}
else {
    echo "<strong>LAN ID:</strong><br /><input type='text' id='lanid' placeholder= 'Enter a LAN ID...'>";
}
?>
<input type="hidden" id="boxid" name="boxid" value="<?php echo $box_id; ?>">
<input type="hidden" id="pattboxid" name="pattboxid" value="<?php echo $patt_box_id; ?>">
<input type="hidden" id="pattdocid" name="pattdocid" value="<?php echo $folderfile_id; ?>">
</form>
<?php 
$body = ob_get_clean();
ob_start();
?>

<button type="button" class="btn wpsc_popup_close"  style="background-color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_text_color']?> !important;"   onclick="wpsc_modal_close();"><?php _e('Close','wpsc-export-ticket');?></button>
<button type="button" class="btn wpsc_popup_action" style="background-color:<?php echo $wpsc_appearance_modal_window['wpsc_action_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_modal_window['wpsc_action_button_text_color']?> !important;" onclick="wpsc_edit_epa_contact();"><?php _e('Save','supportcandy');?></button>

<script>

function wpsc_edit_epa_contact(){
		   jQuery.post(
   '<?php echo WPPATT_PLUGIN_URL; ?>includes/admin/pages/scripts/update_epa_contact.php',{
postvarspattboxid: jQuery("#pattboxid").val(),
postvarsboxid: jQuery("#boxid").val(),
postvarslanid: jQuery("#lanid").val(),
postvarspattdocid: jQuery("#pattdocid").val()
}, 
function (response) {
      if(!alert(response)){window.location.reload();}
       window.location.replace("<?php echo $subfolder_path; ?>/wp-admin/admin.php?pid=boxsearch&page=boxdetails&id=<?php echo $patt_box_id; ?>");
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