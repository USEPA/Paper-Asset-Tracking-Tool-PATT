<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user, $wpscfunction, $wpdb;

if (!isset($_SESSION)) {
    session_start();    
}

$subfolder_path = site_url( '', 'relative'); 

$folderdocid_string = $_POST['postvarsfolderdocid'];
$page_id = $_POST['postvarpage'];
$pid = $_POST['pid'];
$box_id = $_POST['boxid'];

ob_start();

?>
Confirm unauthorized destruction flag edit.
<?php
$body = ob_get_clean();
ob_start();
?>
<button type="button" class="btn wpsc_popup_close"  style="background-color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_text_color']?> !important;"   onclick="wpsc_modal_close();"><?php _e('Close','wpsc-export-ticket');?></button>
<button type="button" class="btn wpsc_popup_action" style="background-color:<?php echo $wpsc_appearance_modal_window['wpsc_action_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_modal_window['wpsc_action_button_text_color']?> !important;" onclick="wpsc_submit_unauthorized_destruction();">Submit</button>
<script>
function wpsc_submit_unauthorized_destruction(){		
    jQuery.post(
   '<?php echo WPPATT_PLUGIN_URL; ?>includes/admin/pages/scripts/update_unauthorize_destruction.php',{
postvarsfolderdocid : '<?php echo $folderdocid_string;?>',
postvarpage : '<?php echo $page_id;?>',
boxid : '<?php echo $box_id;?>'
}, 
   function (response) {
      if(!alert(response)){window.location.reload();}
<?php 
if ($page_id == 'filedetails') {
?>
      window.location.replace("<?php echo $subfolder_path; ?>/wp-admin/admin.php?page=<?php echo $page_id; ?>&pid=<?php echo $pid;?>&id=<?php echo $folderdocid_string;?>")
<?php
} else if($page_id == 'boxdetails') {
?>
      window.location.replace("<?php echo $subfolder_path; ?>/wp-admin/admin.php?page=<?php echo $page_id; ?>&pid=<?php echo $pid;?>&id=<?php echo $box_id;?>")
<?php
} else {
?>
      window.location.replace("<?php echo $subfolder_path; ?>/wp-admin/admin.php?page=<?php echo $page_id; ?>")
<?php
}
?>
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