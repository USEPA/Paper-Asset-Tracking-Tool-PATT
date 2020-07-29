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
    
    $box_status = $wpdb->get_row("SELECT wpqa_terms.term_id as box_status FROM wpqa_terms, wpqa_wpsc_epa_boxinfo WHERE wpqa_terms.term_id = wpqa_wpsc_epa_boxinfo.box_status AND wpqa_wpsc_epa_boxinfo.id = '" . $box_id . "'");
    $status = $box_status->box_status;
?>   
<!--converts program office and record schedules into a datalist-->
<form autocomplete='off'>
<strong>Box Status:</strong><br />
<select id="box_status" name="box_status">
  <option value="748" <?php if ($status == 748 ) echo 'selected' ; ?>>Pending</option>
  <option value="672" <?php if ($status == 672 ) echo 'selected' ; ?>>Scanning Preparation</option>
  <option value="671" <?php if ($status == 671 ) echo 'selected' ; ?>>Scanning/Digitization</option>
  <option value="65" <?php if ($status == 65 ) echo 'selected' ; ?>>QA/QC</option>
  <option value="6" <?php if ($status == 6 ) echo 'selected' ; ?>>Digitized - Not Validated</option>
  <option value="673" <?php if ($status == 673 ) echo 'selected' ; ?>>Ingestion</option>
  <option value="674" <?php if ($status == 674 ) echo 'selected' ; ?>>Validation</option>
  <option value="743" <?php if ($status == 743 ) echo 'selected' ; ?>>Re-Scan</option>
  <option value="66" <?php if ($status == 66 ) echo 'selected' ; ?>>Completed</option>
  <option value="68" <?php if ($status == 68 ) echo 'selected' ; ?>>Destruction Approval</option>
  <option value="67" <?php if ($status == 67 ) echo 'selected' ; ?>>Dispositioned</option>
</select>
<br></br>

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
postvarsbs: jQuery('#box_status').val(),
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
