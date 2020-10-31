<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpdb, $current_user, $wpscfunction;
if (!($current_user->ID && $current_user->has_cap('wpsc_agent'))) {
		exit;
}
$ticket_id 	 = isset($_POST['ticket_id']) ? intval($_POST['ticket_id']) : 0 ;
$raisedby_email = $wpscfunction->get_ticket_fields($ticket_id, 'customer_email');
$wpsc_appearance_modal_window = get_option('wpsc_modal_window');
$wpsc_appearance_ticket_list = get_option('wpsc_appearance_ticket_list');

$agent_permissions = $wpscfunction->get_current_agent_permissions();
$current_agent_id  = $wpscfunction->get_current_user_agent_id();

$restrict_rules = array(
	'relation' => 'AND',
	array(
		'key'            => 'customer_email',
		'value'          => $raisedby_email,
		'compare'        => '='
	),
	array(
		'key'            => 'active',
		'value'          => 1,
		'compare'        => '='
	)
);
$ticket_permission = array(
	'relation' => 'OR'
);
if ($agent_permissions['view_unassigned']) {
	$ticket_permission[] = array(
		'key'            => 'assigned_agent',
		'value'          => 0,
		'compare'        => '='
	);
}

if ($agent_permissions['view_assigned_me']) {
	$ticket_permission[] = array(
		'key'            => 'assigned_agent',
		'value'          => $current_agent_id,
		'compare'        => '='
	);
}

if ($agent_permissions['view_assigned_others']) {
	$ticket_permission[] = array(
		'key'            => 'assigned_agent',
		'value'          => array(0,$current_agent_id),
		'compare'        => 'NOT IN'
	);
}

$restrict_rules [] = $ticket_permission;
$select_str        = 'DISTINCT t.*';
$sql               = $wpscfunction->get_sql_query( $select_str, $restrict_rules);
$tickets           = $wpdb->get_results($sql);
$ticket_list       = json_decode(json_encode($tickets), true);

$ticket_list_items = get_terms([
  'taxonomy'   => 'wpsc_ticket_custom_fields',
  'hide_empty' => false,
  'orderby'    => 'meta_value_num',
  'meta_key'	 => 'wpsc_tl_agent_load_order',
  'order'    	 => 'ASC',
  'meta_query' => array(
    'relation' => 'AND',
    array(
      'key'       => 'wpsc_allow_ticket_list',
      'value'     => '1',
      'compare'   => '='
    ),
    array(
      'key'       => 'wpsc_agent_ticket_list_status',
      'value'     => '1',
      'compare'   => '='
    ),
  ),
]);
ob_start();
?>
<div class="table-responsive" style="overflow-x:auto;">
	<table id="tbl_templates" class="table table-striped table-bordered" cellspacing="5" cellpadding="5">
  <thead>
    <tr>
      <?php
  		foreach ($ticket_list_items as $list_item) {
				if($list_item->slug != 'customer_name'){
					$label = get_term_meta( $list_item->term_id, 'wpsc_tf_label', true);?>
	  			<th class="wpsc_th_<?php echo $list_item->slug ?>" style="background-color:<?php echo $wpsc_appearance_ticket_list['wpsc_ticket_list_header_bg_color']?> !important;color:<?php echo $wpsc_appearance_ticket_list['wpsc_ticket_list_header_text_color']?> !important;">
	  				<?php _e($label,'supportcandy'); ?>
	  			</th>
  				<?php
  			}
			}
  		?>
    </tr>
	</thead>
	<tbody>
    <?php
  	include_once WPSC_ABSPATH . 'includes/admin/tickets/ticket_list/class-ticket-list-field-format.php';
  	$format = new WPSC_Ticket_List_Field();
  		
			foreach($ticket_list as $ticket){
				?>
				<tr class="wpsc_tl_row_item" data-id="<?php echo $ticket['id']?>" onclick="if(link){wpsc_modal_close(); wpsc_get_individual_ticket(this);}" style= "cursor:pointer">
					<?php
					foreach ($ticket_list_items as $list_item) {
						if($list_item->slug != 'customer_name'){
						?>
							<td style="<?php echo $list_item->slug == 'ticket_subject' || $list_item->slug == 'assigned_agent' ? 'white-space: normal;' : ''?>"><?php echo $format->print_field($list_item,$ticket);?></td>
						<?php
						}
					}
					?>
				</tr>
				<?php
			}
  		?>
  </tbody>
</table>
</div>
<link rel="stylesheet" type="text/css" href="<?php echo WPSC_PLUGIN_URL.'asset/lib/DataTables/datatables.min.css';?>"/>
<script type="text/javascript" src="<?php echo WPSC_PLUGIN_URL.'asset/lib/DataTables/datatables.min.js';?>"></script>
<script>
 jQuery(document).ready(function() {
	 jQuery('#tbl_templates').DataTable({
		 "aLengthMenu": [[10, 20, 30, -1], [10, 20, 30, "All"]]
		});
} );

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