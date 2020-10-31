<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user;

if( apply_filters('wpsc_allow_get_change_ticket_fields',true) ){
	if (!($current_user->ID && $current_user->has_cap('wpsc_agent'))) {exit;}
}

$ticket_id 	 = isset($_POST['ticket_id']) ? intval($_POST['ticket_id']) : 0 ;
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


include WPSC_ABSPATH . 'includes/admin/tickets/create_ticket/class-ticket-list-format.php';

$ticket_fields = new WPSC_Ticket_List();
ob_start();
?>
<form id="frm_get_ticket_fields" method="post">
	<div class="form-group col-sm-12" style="padding-left:2px;">
		
				<?php
					if($fields){
						foreach ($fields as $field) {
								$wpsc_tf_type = get_term_meta( $field->term_id, 'wpsc_tf_type',true);
								if(!$wpsc_tf_type)continue;
							  $ticket_fields->print_field($field);
						}
					}
					else{
						_e('No Ticket Fields','supportcandy');
					}
				?>
		</div>
	<input type="hidden" name="action" value="wpsc_tickets" />
	<input type="hidden" name="setting_action" value="set_change_ticket_fields" />
	<input type="hidden" name="ticket_id" value="<?php echo htmlentities($ticket_id) ?>" />
</form>

<?php
$body = ob_get_clean();
ob_start();
?>
<button type="button" class="btn wpsc_popup_close"  style="background-color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_modal_window['wpsc_close_button_text_color']?> !important;"    onclick="wpsc_modal_close();"><?php _e('Close','supportcandy');?></button>
<button type="button" class="btn wpsc_popup_action" style="background-color:<?php echo $wpsc_appearance_modal_window['wpsc_action_button_bg_color']?> !important;color:<?php echo $wpsc_appearance_modal_window['wpsc_action_button_text_color']?> !important;" onclick="wpsc_set_change_ticket_fields(<?php echo $ticket_id ?>);"><?php _e('Save','supportcandy');?></button>
<?php
$footer = ob_get_clean();

$output = array(
  'body'   => $body,
  'footer' => $footer
);

echo json_encode($output);
