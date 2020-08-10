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

$patt_box_id_arr = array();

    $box_patt_id = $wpdb->get_row("SELECT box_id FROM wpqa_wpsc_epa_boxinfo WHERE id = '" . $box_id . "'");
    $patt_box_id = $box_patt_id->box_id;
    array_push($patt_box_id_arr,$patt_box_id);
    
    $box_program_office = $wpdb->get_row("SELECT b.office_acronym as acronym 
    FROM wpqa_wpsc_epa_boxinfo as a INNER JOIN wpqa_wpsc_epa_program_office as b ON a.program_office_id = b.office_code
    WHERE box_id = '" . $box_id . "'");
    $program_office = $box_program_office->acronym;
    
    $box_record_schedule = $wpdb->get_row("SELECT c.Record_Schedule_Number as record_schedule_number 
    FROM wpqa_wpsc_epa_boxinfo as a INNER JOIN wpqa_epa_record_schedule as c ON record_schedule_id = c.id
    WHERE box_id = '" . $box_id . "'");
    $record_schedule = $box_record_schedule->record_schedule_number;
    
    $box_dc = $wpdb->get_row("SELECT a.box_destroyed, SUM(b.validation = 1) as validated, COUNT(b.validation) as validation_total
FROM wpqa_wpsc_epa_boxinfo a
INNER JOIN wpqa_wpsc_epa_folderdocinfo b ON a.id = b.box_id
WHERE a.id = '" . $box_id . "'");
    $dc = $box_dc->box_destroyed;
    $validated = $box_dc->validated;
    $validation_total = $box_dc->validation_total;
    
    $box_status = $wpdb->get_row("SELECT wpqa_terms.term_id as box_status FROM wpqa_terms, wpqa_wpsc_epa_boxinfo WHERE wpqa_terms.term_id = wpqa_wpsc_epa_boxinfo.box_status AND wpqa_wpsc_epa_boxinfo.id = '" . $box_id . "'");
    $status_id = $box_status->box_status;
    
    $box_destruction_approval = $wpdb->get_row("SELECT destruction_approval FROM wpqa_wpsc_ticket, wpqa_wpsc_epa_boxinfo WHERE wpqa_wpsc_ticket.id = wpqa_wpsc_epa_boxinfo.ticket_id AND wpqa_wpsc_epa_boxinfo.id = '" . $box_id . "'");
    $destruction_approval = $box_destruction_approval->destruction_approval;
?>   
<!--converts program office and record schedules into a datalist-->
<form autocomplete='off'>
    
<?php
$status_list = Patt_Custom_Func::get_restricted_box_status_list( $patt_box_id_arr );
$restriction_reason = $status_list['restriction_reason']; // string with warnings (multiple lines)
?>
<div id='alert_status' class=''></div> 
<strong>Box Status:</strong><br />

		<select id="box_status" name="box_status">
			<?php
// Register Box Status Taxonomy
if( !taxonomy_exists('wpsc_box_statuses') ) {
	$args = array(
		'public' => false,
		'rewrite' => false
	);
	register_taxonomy( 'wpsc_box_statuses', 'wpsc_ticket', $args );
}

$box_statuses = $status_list['box_statuses']; // list of acceptable statuses

      foreach ( $box_statuses as $term=>$status ) :

if ($status_id == $term ) {
    $selected = 'selected'; 
} else {
    $selected = ''; 
}

echo '<option '.$selected.' value="'.$term.'">'.$status.'</option>';
			endforeach;
			?>
		</select>
<?php
// TESTING print_r($box_statuses);
?>
<br /><br />
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

<?php
if($validated == $validation_total && $status_id == 68 && $destruction_approval == 1) {
?>
<br></br>

<strong>Destruction Completed:</strong><br />
<select id="dc" name="dc">
  <option value="1" <?php if ($dc == 1 ) echo 'selected' ; ?>>Yes</option>
  <option value="0" <?php if ($dc == 0 ) echo 'selected' ; ?>>No</option>
</select>
<?php 
    
} else { 

?>

<input type="hidden" id="dc" name="dc" value="<?php echo $dc; ?>">
<?php } ?>

<input type="hidden" id="boxid" name="boxid" value="<?php echo $box_id; ?>">
<input type="hidden" id="pattboxid" name="pattboxid" value="<?php echo $patt_box_id; ?>">
</form>
<?php 
$body = ob_get_clean();
ob_start();
?>
<button type="button" class="btn wpsc_popup_close"  style="background-color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_text_color']?> !important;"   onclick="wpsc_modal_close();"><?php _e('Close','wpsc-export-ticket');?></button>
<button type="button" class="btn wpsc_popup_action" style="background-color:<?php echo $wpsc_appearance_modal_window['wpsc_action_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_modal_window['wpsc_action_button_text_color']?> !important;" onclick="wpsc_edit_box_details();"><?php _e('Save','supportcandy');?></button>
<style>
.alert_spacing {
	margin: 0px 0px 0px 0px;
}

</style>

<script>
	jQuery(document).ready(function(){
		let restriction_reason = '<?php echo $restriction_reason ?>';		
		
		if( restriction_reason.length > 0 ) {
			set_alert('warning', restriction_reason);				
		}

	});


// Sets an alert
function set_alert( type, message ) {
	
	let alert_style = '';
	
	switch( type ) {
		case 'success':
			alert_style = 'alert-success';		
			break;
		case 'warning':
			alert_style = 'alert-warning';
			break;
		case 'danger':
			alert_style = 'alert-danger';
			break;		
	}

	jQuery('#alert_status').html('<div class=" alert '+alert_style+'">'+message+'</div>'); //badge badge-danger
	jQuery('#alert_status').addClass('alert_spacing');	
	
}

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
