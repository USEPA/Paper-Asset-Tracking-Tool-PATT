<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user,$wpscfunction,$wpdb;

if (!($current_user->ID && $current_user->has_cap('wpsc_agent'))) {exit;}

$ticket_id  = isset($_POST['ticket_id']) ? sanitize_text_field($_POST['ticket_id']) : '' ;
$ticket_data = $wpscfunction->get_ticket($ticket_id);
$status_id   	= $ticket_data['ticket_status'];
$priority_id 	= $ticket_data['ticket_priority'];
$category_id  = $ticket_data['ticket_category'];
$wpsc_appearance_modal_window = get_option('wpsc_modal_window');
$wpsc_custom_status_localize   = get_option('wpsc_custom_status_localize');
$wpsc_custom_category_localize = get_option('wpsc_custom_category_localize');
$wpsc_custom_priority_localize = get_option('wpsc_custom_priority_localize');
//PATT BEGIN
$customer_name   	= $ticket_data['customer_name'];

$agent_permissions = $wpscfunction->get_current_agent_permissions();
$agent_permissions['label'];
//PATT END
ob_start();
?>
<form id="frm_get_ticket_change_status" method="post">
    <?php
    //PATT BEGIN
    $shipping_array = array(5,63);
    //PATT END
    ?>

<div class="form-group">
		<label for="wpsc_default_ticket_status"><?php _e('Ticket Status','supportcandy');?></label>
		<select class="form-control" name="status">
			<?php
			$statuses = get_terms([
				'taxonomy'   => 'wpsc_statuses',
				'hide_empty' => false,
				'orderby'    => 'meta_value_num',
				'order'    	 => 'ASC',
				'meta_query' => array('order_clause' => array('key' => 'wpsc_status_load_order')),
			]);
      foreach ( $statuses as $status ) :
				$selected = $status_id == $status->term_id ? 'selected="selected"' : '';
				//PATT BEGIN
                $disabled = '';
                if (in_array($status->term_id, $shipping_array)) {
                    $disabled = 'disabled';
                }
                
                echo '<option '.$selected.' value="'.$status->term_id.'" '.$disabled.'>'.$wpsc_custom_status_localize['custom_status_'.$status->term_id].'</option>';
                //PATT END
			endforeach;
			?>
		</select>
	</div>

<?php
//PATT BEGIN
$box_details = $wpdb->get_results(
"SELECT wpqa_terms.term_id as digitization_center, location_status_id as location
FROM wpqa_wpsc_epa_boxinfo
INNER JOIN wpqa_wpsc_epa_storage_location ON wpqa_wpsc_epa_boxinfo.storage_location_id = wpqa_wpsc_epa_storage_location.id
INNER JOIN wpqa_terms ON  wpqa_terms.term_id = wpqa_wpsc_epa_storage_location.digitization_center
WHERE wpqa_wpsc_epa_boxinfo.ticket_id = '" . $ticket_id . "'"
			);
$dc_array = array();
$pl_array = array();
foreach ($box_details as $info) {
$dc_details = $info->digitization_center;
$physical_location = $info->location;
array_push($dc_array, $dc_details);
array_push($pl_array, $physical_location);
}

if (count(array_keys($dc_array, '666')) == count($dc_array) && !in_array('-99999', $pl_array) && !in_array(6, $pl_array)) {
//PATT END
?>

<!--
	<div class="form-group">
		<label for="wpsc_default_ticket_category"><?php _e('Ticket Category','supportcandy');?></label>
		<select class="form-control" name="category" >
			<?php
			$categories = get_terms([
				'taxonomy'   => 'wpsc_categories',
				'hide_empty' => false,
				'orderby'    => 'meta_value_num',
				'order'    	 => 'ASC',
				'meta_query' => array('order_clause' => array('key' => 'wpsc_category_load_order')),
			]);
			foreach ( $categories as $category ) :
				//PATT
				$selected = Patt_Custom_Func::get_default_digitization_center($ticket_id) == $category->term_id ? 'selected="selected"' : '';
				echo '<option '.$selected.' value="'.$category->term_id.'">'.$wpsc_custom_category_localize['custom_category_'.$category->term_id].'</option>';
			endforeach;
			?>
		</select>
	</div>
-->
	<div class="form-group">
		<label for="wpsc_default_ticket_category"><?php _e('Ticket Category','supportcandy');?></label>
		<select class="form-control" name="category" >
<option value="666">Not Assigned</option>
<option selected="selected" value="62">East</option>
<option value="663" disabled>East CUI</option>
<option value="2" disabled>West</option>
<option value="664" disabled>West CUI</option>
		</select>
	</div>

<?php
//PATT BEGIN
} else {
//PATT END

echo '<input type="hidden" name="category" value="'.Patt_Custom_Func::get_default_digitization_center($ticket_id).'">';

//PATT BEGIN
}
//PATT END
?>

	<div class="form-group">
		<label for="wpsc_default_ticket_priority"><?php _e('Ticket priority','supportcandy');?></label>
		<select class="form-control" name="priority">
			<?php
			$priorities = get_terms([
				'taxonomy'   => 'wpsc_priorities',
				'hide_empty' => false,
				'orderby'    => 'meta_value_num',
				'order'    	 => 'ASC',
				'meta_query' => array('order_clause' => array('key' => 'wpsc_priority_load_order')),
			]);
			
			//PATT BEGIN
			$key = array_search('Critical', array_column($priorities, 'name'));
			if ($agent_permissions['label'] == 'Agent')
            {
                 unset($priorities[$key]);
            }
			//PATT END
			
			foreach ( $priorities as $priority ) :
				$selected = $priority_id == $priority->term_id ? 'selected="selected"' : '';
				//PATT BEGIN
				if ($priority->term_id == 9) {
				echo '<option '.$selected.' style="background: #FFD0C2; font-weight: bold;" value="'.$priority->term_id.'">'.$wpsc_custom_priority_localize['custom_priority_'.$priority->term_id].'</option>';
				} else {
				echo '<option '.$selected.' value="'.$priority->term_id.'">'.$wpsc_custom_priority_localize['custom_priority_'.$priority->term_id].'</option>';				
				}
				//PATT END
			endforeach;
			?>
		</select>
	</div>
	<?php do_action('wpsc_after_edit_change_ticket_status',$ticket_id);?>
  <input type="hidden" name="action" value="wpsc_tickets" />
	<input type="hidden" name="setting_action" value="set_change_ticket_status" />
  <input type="hidden" id="wpsc_post_id" name="ticket_id" value="<?php echo htmlentities($ticket_id) ?>" />
	

</form>
<?php
$body = ob_get_clean();

ob_start();
?>
<button type="button" class="btn wpsc_popup_close"  style="background-color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_text_color']?> !important;"    onclick="wpsc_modal_close();"><?php _e('Close','supportcandy');?></button>
<button type="button" class="btn wpsc_popup_action" style="background-color:<?php echo $wpsc_appearance_modal_window['wpsc_action_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_modal_window['wpsc_action_button_text_color']?> !important;" onclick="wpsc_set_change_ticket_status(<?php echo htmlentities($ticket_id)?>);wpsc_open_ticket(<?php echo htmlentities($ticket_id)?>);"><?php _e('Save','supportcandy');?></button>
<script>
// PATT BEGIN
jQuery(document).ready(function() {


var request_status = jQuery('[name=status]').val();

jQuery('[name=status]').on('change', function() {
request_status = jQuery('[name=status]').val();
});


jQuery(".wpsc_popup_action").click(function () {

if(request_status == 3 || request_status == 670 || request_status == 69) {
    alert('No automatic shelf assignments made.');
} else {
jQuery.post(
'<?php echo WPPATT_PLUGIN_URL; ?>includes/admin/pages/scripts/auto_assignment.php',{
postvartktid: '<?php echo $ticket_id ?>',
postvardcname: jQuery("[name=category]").val()
},
function (response) {
if(jQuery("select[name='category']").val()) {
alert(response);
}
});
}

});


});
// PATT END
</script>

<?php
$footer = ob_get_clean();

$output = array(
  'body'   => $body,
  'footer' => $footer
);

echo json_encode($output);
