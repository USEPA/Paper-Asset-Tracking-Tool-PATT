<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user, $wpdb, $wpscfunction;
if (!($current_user->ID && $current_user->has_cap('edit_published_posts'))) {exit;}

$field_id = isset($_POST) && isset($_POST['field_id']) ? intval($_POST['field_id']) : 0;
if (!$field_id) {exit;}

$old_status = get_term_meta( $field_id, 'wpsc_agent_ticket_filter_status', true);

if ($old_status) {
  echo '{ "sucess_status":"0","messege":"'.__('Already available in list.','supportcandy').'" }';
} else {
  $load_order = $wpdb->get_var("select max(meta_value) as load_order from {$wpdb->prefix}termmeta WHERE meta_key='wpsc_filter_agent_load_order'");
  update_term_meta ($field_id, 'wpsc_agent_ticket_filter_status', '1');
  update_term_meta ($field_id, 'wpsc_filter_agent_load_order', ++$load_order);
  echo '{ "sucess_status":"1","messege":"'.__('Added successfully.','supportcandy').'" }';
}
