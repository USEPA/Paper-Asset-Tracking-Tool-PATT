<?php
// Code to inject label button

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user, $wpscfunction;

$flag_btn = false;

$current_agent_id      = $wpscfunction->get_current_user_agent_id();

$ticket_id = isset($_POST['ticket_id']) ? sanitize_text_field($_POST['ticket_id']) : '' ;
$ticket_data = $wpscfunction->get_ticket($ticket_id);
$status_id   	= $ticket_data['ticket_status'];

// Change Status ID when going to production to reflect the term_id of the "New" status

$status_array = array(3, 67, 68, 69);
if (!in_array($status_id, $status_array)) {
    $flag_btn = true;
}

if($flag_btn):

?>

<?php //echo $status_id ?>

	<button type="button" class="btn btn-sm wpsc_action_btn" id="wpsc_export_ticket_btn" style="<?php echo $action_default_btn_css ?>" onclick="wpsc_get_pdf_label_field(<?php echo $ticket_id?>)"><i class="fas fa-tags"></i> Print Label</button>
		<script>
		function wpsc_get_pdf_label_field(ticket_id){
		  wpsc_modal_open('Labels');
		  var data = {
		    action: 'wpsc_get_pdf_label_field',
		    ticket_id: ticket_id
		  };
		  jQuery.post(wpsc_admin.ajax_url, data, function(response_str) {
		    var response = JSON.parse(response_str);
		    jQuery('#wpsc_popup_body').html(response.body);
		    jQuery('#wpsc_popup_footer').html(response.footer);
		    jQuery('#wpsc_cat_name').focus();
		  });  
		}
	</script>
	<?php
endif;
