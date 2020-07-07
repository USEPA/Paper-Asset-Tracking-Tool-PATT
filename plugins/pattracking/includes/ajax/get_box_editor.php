<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user, $wpscfunction, $wpdb;

$subfolder_path = site_url( '', 'relative');
//echo 'subfolder_path';

if (!isset($_SESSION)) {
    session_start();    
}

$box_id = $_POST["box_id"];
        
ob_start();
    
    $box_patt_id = $wpdb->get_row("SELECT box_id FROM wpqa_wpsc_epa_boxinfo WHERE id = '" . $box_id . "'");
    $patt_box_id = $box_patt_id->box_id;
    
    $box_program_office = $wpdb->get_row("SELECT b.office_acronym as acronym 
    FROM wpqa_wpsc_epa_boxinfo as a INNER JOIN wpqa_wpsc_epa_program_office as b ON a.program_office_id = b.office_code
    WHERE box_id = '" . $box_id . "'");
    $program_office = $box_program_office->acronym;
    
    $box_record_schedule = $wpdb->get_row("SELECT c.Record_Schedule_Number as record_schedule_number 
    FROM wpqa_wpsc_epa_boxinfo as a INNER JOIN wpqa_epa_record_schedule as c ON record_schedule_id = c.id
    WHERE box_id = '" . $box_id . "'");
    $record_schedule = $box_record_schedule->record_schedule_number;
    
    $box_dc = $wpdb->get_row("SELECT box_destroyed FROM wpqa_wpsc_epa_boxinfo WHERE id = '" . $box_id . "'");
    $dc = $box_dc->box_destroyed;

?>
<?php  ?>
<!--converts program office and record schedules into a datalist-->
<form autocomplete='off'>
<strong>Program Office:</strong><br />
<?php
    $po_array = Patt_Custom_Func::fetch_program_office_array(); ?>
    <input type="search" list="ProgramOfficeList" placeholder='Enter program office' id='po'/>
    <datalist id = 'ProgramOfficeList'>
     <?php foreach($po_array as $key => $value) { 
     
    $program_office = $wpdb->get_row("SELECT office_code
FROM wpqa_wpsc_epa_program_office 
WHERE office_acronym  = '" . $value . "'");
    
    $program_office_id = $program_office->office_code;
    ?>
        <option data-value='<?php echo $program_office_id; ?>' value='<?php echo preg_replace("/\([^)]+\)/","",$value); ?>'></option>
     <?php } ?>
     </datalist>

<br></br>

<strong>Record Schedule:</strong><br />
<?php
    $rs_array = Patt_Custom_Func::fetch_record_schedule_array(); ?>
    <input type="search" list="RecordScheduleList" placeholder='Enter record schedule' id='rs'/>
    <datalist id = 'RecordScheduleList'>
     <?php foreach($rs_array as $key => $value) { 
     
     $record_schedule = $wpdb->get_row("SELECT id
FROM wpqa_epa_record_schedule 
WHERE Record_Schedule_Number  = '" . $value . "'");
    
    $record_schedule_id = $record_schedule->id;
     ?>
        <option data-value='<?php echo $record_schedule_id; ?>' value='<?php echo $value; ?>'></option>
     <?php } ?>
     </datalist>

<br></br>

<strong>Destruction Completed:</strong><br />
<select id="dc" name="dc">
  <option value="1" <?php if ($dc == 1 ) echo 'selected' ; ?>>Yes</option>
  <option value="0" <?php if ($dc == 0 ) echo 'selected' ; ?>>No</option>
</select></br></br>

<input type="hidden" id="boxid" name="boxid" value="<?php echo $box_id; ?>">
<input type="hidden" id="pattboxid" name="pattboxid" value="<?php echo $patt_box_id; ?>">
</form>
<?php 
$body = ob_get_clean();
ob_start();
?>
<button type="button" class="btn wpsc_popup_close"  style="background-color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_text_color']?> !important;"   onclick="wpsc_modal_close();"><?php _e('Close','wpsc-export-ticket');?></button>
<button type="button" class="btn wpsc_popup_action" style="background-color:<?php echo $wpsc_appearance_modal_window['wpsc_action_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_modal_window['wpsc_action_button_text_color']?> !important;" onclick="wpsc_edit_box_details();"><?php _e('Save','supportcandy');?></button>
<script>
function wpsc_edit_box_details(){	

    var po_value = jQuery('#po').val();
    var rs_value = jQuery('#rs').val();
		   jQuery.post(
   '<?php echo WPPATT_PLUGIN_URL; ?>includes/admin/pages/scripts/update_box_details.php',{
postvarspattboxid: jQuery("#pattboxid").val(),
postvarsboxid: jQuery("#boxid").val(),
postvarsdc: jQuery('#dc').val(),
postvarspo: jQuery('#ProgramOfficeList [value="' + po_value + '"]').data('value'),
postvarsrs: jQuery('#RecordScheduleList [value="' + rs_value + '"]').data('value')
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