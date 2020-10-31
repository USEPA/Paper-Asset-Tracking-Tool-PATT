<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user, $wpscfunction, $wpdb;

$subfolder_path = site_url( '', 'relative'); 

if (!isset($_SESSION)) {
    session_start();    
}

$dc_id = $_POST["dc_id"];
$_SESSION["dc_id"] = $dc_id;


$box_id  = isset($_POST['box_id']) ? sanitize_text_field($_POST['box_id']) : '' ;
        
ob_start();

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

?>

<h4>Switch Digitization Center Location</h4>

  <label for="dc">Choose a Location:</label>
 
      <select class="form-control" name="dc" id="dc">
        <?php
        /*
				$categories = get_terms([
				  'taxonomy'   => 'wpsc_categories',
				  'hide_empty' => false,
					'orderby'    => 'meta_value_num',
				  'order'    	 => 'ASC',
					'meta_query' => array('order_clause' => array('key' => 'wpsc_category_load_order')),
				]);
				//$wpsc_default_ticket_category = get_option('wpsc_default_ticket_category');
        foreach ( $categories as $category ) :
          $selected = $category->term_id == 666 ? 'selected="selected" disabled' : '';
          $disabled = $digitization_center == $category->term_id ? 'disabled' : '';
          echo '<option '.$selected.' '.$disabled.' value="'.$category->term_id.'">'.$category->name.'</option>';
        endforeach;
        */
        ?>
         <option selected="selected" value="666" disabled>Not Assigned</option>
         <option value="62">East</option>
         <option value="663" disabled>East CUI</option>
         <option value="2" disabled>West</option>
         <option value="664" disabled>West CUI</option> 
      </select>
      

<?php 
$body = ob_get_clean();
ob_start();
?>
<button type="button" class="btn wpsc_popup_close"  style="background-color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_text_color']?> !important;"   onclick="wpsc_modal_close();"><?php _e('Close','wpsc-export-ticket');?></button>
<button type="button" id="digitization_submit" class="btn wpsc_popup_action" style="background-color:<?php echo $wpsc_appearance_modal_window['wpsc_action_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_modal_window['wpsc_action_button_text_color']?> !important;" onclick="wpsc_get_digitization_editor(<?php echo htmlentities($box_id)?>);"><?php _e('Save','supportcandy');?></button>
<script>
jQuery("#digitization_submit").hide();

jQuery('#dc').on('click', function () {
if (jQuery("#dc").val() != '') {
jQuery("#digitization_submit").show();
}
});
				

function wpsc_get_digitization_editor(box_id){		
		   jQuery.post(
   '<?php echo WPPATT_PLUGIN_URL; ?>includes/admin/pages/scripts/update_digitization_center.php',{
    postvarsboxidname: box_id,
    postvarsdc: jQuery("#dc").val()
}, 
   function (response) {
      if(!alert(response)){window.location.reload();}
      window.location.replace("<?php echo $subfolder_path; ?>/wp-admin/admin.php?page=wpsc-tickets&id=<?php echo Patt_Custom_Func::convert_request_db_id($patt_ticket_id); ?>");
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
