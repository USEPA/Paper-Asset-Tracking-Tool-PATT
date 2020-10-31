<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user, $wpscfunction;
if (!($current_user->ID && $current_user->has_cap('wpsc_agent'))) {
	exit;
}
$ticket_id 			= isset($_POST['ticket_id']) ? sanitize_text_field($_POST['ticket_id']) : '' ;
$ticket_data = $wpscfunction->get_ticket($ticket_id);

$customer_name  = $ticket_data['customer_name'];
$customer_email = $ticket_data['customer_email']; 
$wpsc_appearance_modal_window = get_option('wpsc_modal_window');

$fields = get_terms([
	'taxonomy'   => 'wpsc_ticket_custom_fields',
	'hide_empty' => false,
	'orderby'    => 'meta_value_num',
	'meta_key'	 => 'wpsc_tf_load_order',
	'order'    	 => 'ASC',
	'meta_query' => array(
		array(
      'key'       => 'agentonly',
      'value'     => '0',
      'compare'   => '='
    )
	),
]);
ob_start();
$cust_name_term = get_term_by('slug', 'customer_name', 'wpsc_ticket_custom_fields' );
$cust_name = get_term_meta( $cust_name_term->term_id, 'wpsc_tf_label', true);
$cust_name_extra_info = get_term_meta($cust_name_term->term_id, 'wpsc_tf_extra_info', true);


$cust_email_term = get_term_by('slug', 'customer_email', 'wpsc_ticket_custom_fields' );
$cust_email = get_term_meta( $cust_email_term->term_id, 'wpsc_tf_label', true);
$cust_email_extra_info = get_term_meta($cust_email_term->term_id, 'wpsc_tf_extra_info', true);

$wpsc_custom_fields_localize = get_option('wpsc_custom_fields_localize');
$wpsc_custom_fields_extra_info = get_option('wpsc_custom_fields_extra_info');

?>
<form id="frm_get_ticket_raised_by"  method="post">
	<div class="row" style="padding-left:2px;">
		<div data-fieldtype="text" data-visibility="" class="col-sm-6 visible wpsc_required form-group wpsc_form_field field">
    	<label class="wpsc_ct_field_label" for="customer_name"><?php echo $wpsc_custom_fields_localize['custom_fields_'.$cust_name_term->term_id]?> </label>
			<p class="help-block"><?php echo $wpsc_custom_fields_extra_info['custom_fields_extra_info_'.$cust_name_term->term_id] ?></p>           
			<input type="text" id="customer_name" class="form-control regi_user_autocomplete ui-autocomplete-input" name="customer_name" autocomplete="off" value="<?php echo htmlentities(stripcslashes($customer_name))?>">
   	</div>
	 <div data-fieldtype="email" data-visibility="" class="col-sm-6 visible wpsc_required form-group wpsc_form_field field">
      <label class="wpsc_ct_field_label" for="customer_email"><?php echo $wpsc_custom_fields_localize['custom_fields_'.$cust_email_term->term_id]?></label>
      <p class="help-block"><?php echo $wpsc_custom_fields_extra_info['custom_fields_extra_info_'.$cust_email_term->term_id]?></p>            
			<input type="text" id="customer_email" class="form-control" name="customer_email" autocomplete="off" value="<?php echo $customer_email ?>">
    </div>
	</div>
		
		<input type="hidden" name="action" value="wpsc_tickets" />
		<input type="hidden" name="setting_action" value="set_change_raised_by" />
		<input type="hidden" name="ticket_id" value="<?php echo htmlentities($ticket_id) ?>" />
		
</form>
<script>
	jQuery(document).ready(function(){
  	jQuery( ".regi_user_autocomplete" ).autocomplete({
	
			minLength: 1,
			appendTo: jQuery('.regi_user_autocomplete').parent(),
			source: function( request, response ) {
				var term = request.term;
				request = {
					action: 'wpsc_tickets',
					setting_action : 'get_users',
					term : term
				}
				jQuery.getJSON( wpsc_admin.ajax_url, request, function( data, status, xhr ) {
					response(data);
				});	
			},
        minLength: 2,
        select: function (event, ui) {
					jQuery('#customer_name').val(ui.item.value);
					jQuery('#customer_email').val(ui.item.email);
				}
		});
	
	});
</script>
<?php 
$body = ob_get_clean();
ob_start();
?>
<button type="button" class="btn wpsc_popup_close"  style="background-color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_text_color']?> !important;"    onclick="wpsc_modal_close();"><?php _e('Close','supportcandy');?></button>
<button type="button" class="btn wpsc_popup_action" style="background-color:<?php echo $wpsc_appearance_modal_window['wpsc_action_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_modal_window['wpsc_action_button_text_color']?> !important;" onclick="wpsc_set_change_raised_by(<?php echo $ticket_id ?>);"><?php _e('Save','supportcandy');?></button>
<?php 
$footer = ob_get_clean();

$output = array(
  'body'   => $body,
  'footer' => $footer
);

echo json_encode($output);

	