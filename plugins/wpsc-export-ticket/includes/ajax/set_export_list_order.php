<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user, $wpscfunction;
if (!($current_user->ID && $current_user->has_cap('manage_options'))) {
	exit;
}

$export_list_ids = isset($_POST) && isset($_POST['export_list_ids']) ? $wpscfunction->sanitize_array($_POST['export_list_ids']) : array();

foreach ($export_list_ids as $key => $export_list_id) {
	update_term_meta(intval($export_list_id), 'wpsc_export_ticket_list_order', intval($key));
}

$ticket_list_items = get_terms([
	'taxonomy'   => 'wpsc_ticket_custom_fields',
	'hide_empty' => false,
	'orderby' => 'meta_value_num',
	'meta_key'	 => 'wpsc_export_ticket_list_order',
  'order' => 'ASC',
	'meta_query' => array(
		'relation' => 'AND',
		array(
			'key'     => 'wpsc_allow_ticket_list',
			'value'   => '1',
			'compare' => '=',
		),
	),
]);
foreach ($ticket_list_items as $key=>$val) {
	$export_column[]=($key = $val->slug);
}
update_option('wpsc_export_ticket_list',$export_column);

$wpsc_export_ticket_list = isset($_POST) && isset($_POST['ticket_list_items']) ? $wpscfunction->sanitize_array($_POST['ticket_list_items']) : array();
update_option( 'wpsc_export_ticket_list',$wpsc_export_ticket_list );

echo '{ "sucess_status":"1","messege":"'.__('Export List Order Saved.','wpsc-export-ticket').'" }';