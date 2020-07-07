<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user;
if (!($current_user->ID && $current_user->has_cap('manage_options'))) {
	exit;
}

$sla_ids = isset($_POST) && isset($_POST['sla_ids']) ? $_POST['sla_ids'] : array();

foreach ($sla_ids as $key => $sla_id) {
	update_term_meta(intval($sla_id), 'load_order', intval($key));
}

echo '{ "sucess_status":"1","messege":"'.__('SLA order saved.','wpsc-sla').'" }';
