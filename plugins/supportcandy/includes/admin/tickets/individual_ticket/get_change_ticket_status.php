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
ob_start();
?>
<form id="frm_get_ticket_change_status" method="post">
<?php
//PATT BEGIN
$get_assigned = $wpdb->get_row("SELECT sum(meta_value) as assigned_val FROM wpqa_wpsc_ticketmeta WHERE ticket_id = '" . $ticket_id . "' AND meta_key = 'assigned_agent' ORDER BY ticket_id DESC");
$assigned_val = $get_assigned->assigned_val.',';

$get_sum_total = $wpdb->get_row("select sum(a.total_count) as sum_total_count
    from (
SELECT (SELECT count(id) FROM wpqa_wpsc_epa_folderdocinfo WHERE box_id = a.id) as total_count FROM wpqa_wpsc_epa_boxinfo as a INNER JOIN wpqa_wpsc_ticket as b ON a.ticket_id = b.id WHERE b.id = '" . $ticket_id . "'
    ) a");
$sum_total_val = $get_sum_total->sum_total_count;

$get_sum_validation = $wpdb->get_row("select sum(a.validation) as sum_validation
    from (
SELECT (SELECT sum(validation = 1) FROM wpqa_wpsc_epa_folderdocinfo WHERE box_id = a.id) as validation FROM wpqa_wpsc_epa_boxinfo as a INNER JOIN wpqa_wpsc_ticket as b ON a.ticket_id = b.id WHERE b.id = '" . $ticket_id . "'
    ) a");
$sum_validation = $get_sum_validation->sum_validation;

$get_sum_destruction = $wpdb->get_row("select count(id) as count_destruction
    from wpqa_wpsc_epa_boxinfo where ticket_id = '" . $ticket_id . "' and box_destroyed = 1");
$count_destruction = $get_sum_destruction->count_destruction;

$get_sum_boxes = $wpdb->get_row("select count(id) as box_count
    from wpqa_wpsc_epa_boxinfo where ticket_id = '" . $ticket_id . "'");
$count_boxes = $get_sum_boxes->box_count;

$validated = '';
$assigned = '';
$destruction = '';

if($sum_total_val == $sum_validation) {
$validated = 1;
} else {
$validated = 0;
}

$validated_array = array(66,68,67,69);

if($assigned_val > 0) {
$assigned = 1;
} else {
$assigned = 0;
}

$assigned_array = array(3,4,670,5,63);

if($count_boxes == $count_destruction) {
$destruction = 1;
} else {
$destruction = 0;
}

$destruction_array = array(3,4,670,5,63,64,672,671,65,6,673,674,66);
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
if (in_array($status->term_id, $validated_array) && $validated == 0) {
    $disabled = 'disabled';
}
if (in_array($status->term_id, $assigned_array) && $assigned == 1) {
    $disabled = 'disabled';
}
if (in_array($status->term_id, $destruction_array) && $destruction == 1) {
    $disabled = 'disabled';
}
//PATT END

echo '<option '.$selected.' value="'.$status->term_id.'" '.$disabled.'>'.$wpsc_custom_status_localize['custom_status_'.$status->term_id].'</option>';
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
			foreach ( $priorities as $priority ) :
				$selected = $priority_id == $priority->term_id ? 'selected="selected"' : '';
				echo '<option '.$selected.' value="'.$priority->term_id.'">'.$wpsc_custom_priority_localize['custom_priority_'.$priority->term_id].'</option>';
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
jQuery(".wpsc_popup_action").click(function () {
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