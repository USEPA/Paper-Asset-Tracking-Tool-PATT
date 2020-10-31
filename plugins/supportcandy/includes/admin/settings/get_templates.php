<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user;
if (!($current_user->ID && $current_user->has_cap('wpsc_agent'))) {
		exit;
}

$wpsc_appearance_modal_window = get_option('wpsc_modal_window');

$fields = get_terms([
	'taxonomy'   => 'wpsc_ticket_custom_fields',
	'hide_empty' => false,
]);

ob_start();
?>
<div class="table-responsive">
	<table id="tbl_templates"   class="table table-striped table-bordered"  cellspacing="5" cellpadding="5">
  <thead>
  	<tr>
    <th><?php _e('Tag','supportcandy')?></th>
    <th><?php _e('Field Name','supportcandy')?></th>
		<th></th>
  </tr>
	</thead>
	<tbody>
	<?php
	foreach ($fields as $field) {
		$label = get_term_meta($field->term_id,'wpsc_tf_label',true);
		$wpsc_tf_type = get_term_meta($field->term_id,'wpsc_tf_type',true);
		if( $wpsc_tf_type == '22'  || $field->slug == 'conditional_agent_assign' || $field->slug == 'schedule_tickets'){
			continue;
		}
		?>
		<tr>
	    <td id="wpsc_tag_td_<?php echo $field->slug?>" class="wpsc_tag_td" onclick="wpsc_insert_editor_text('{<?php echo $field->slug?>}')">{<?php echo $field->slug?>}</td>
	    <td><?php echo htmlentities($label)?></td>
			<td><button id="wpsc_tag_td_btn_<?php echo $field->slug?>" onclick="copyToClipboard(this,'<?php echo $field->slug?>')" class="btn btn-sm btn-copy"><?php _e('Copy','supportcandy')?></button></td>
		</tr>
		<?php
	}
	do_action('wpsc_after_macro_templates');
	?>
	</tbody>
</table>
</div>
<link rel="stylesheet" type="text/css" href="<?php echo WPSC_PLUGIN_URL.'asset/lib/DataTables/datatables.min.css';?>"/>
<script type="text/javascript" src="<?php echo WPSC_PLUGIN_URL.'asset/lib/DataTables/datatables.min.js';?>"></script>
<script>
 jQuery(document).ready(function() {
	 jQuery('#tbl_templates').DataTable({
		 "aLengthMenu": [[4, 8, 12, -1], [4, 8, 12, "All"]],
		 "columnDefs": [
   			{ orderable: false, targets: -1 }
			]
		});
} );

function copyToClipboard(element,slug) {
  var element = jQuery("#wpsc_tag_td_"+slug).focus()[0];
  var range, selection;

  if (document.body.createTextRange) {
    range = document.body.createTextRange();
    range.moveToElementText(element);
    range.select();
  } else if (window.getSelection) {
    selection = window.getSelection();        
    range = document.createRange();
    range.selectNodeContents(element);
    selection.removeAllRanges();
    selection.addRange(range);
  }
  try {
    document.execCommand('copy');
	  var val = jQuery('#wpsc_tag_td_btn').text();
	  text = val.replace(val, "Coppied");
		jQuery('#wpsc_tag_td_btn_'+slug).text(text);	
		wpsc_modal_close();
  }
  catch (err) {
    alert('unable to copy text');
  }

}
</script>
<?php 
$body = ob_get_clean();
ob_start();
?>
<button type="button" class="btn wpsc_popup_close" style="background-color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_text_color']?> !important;" onclick="wpsc_modal_close();"><?php _e('Close','supportcandy');?></button>
<?php 
$footer = ob_get_clean();

$output = array(
  'body'   => $body,
  'footer' => $footer
);

echo json_encode($output);
?>
