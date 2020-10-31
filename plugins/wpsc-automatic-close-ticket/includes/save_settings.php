<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user;
if (!($current_user->ID && $current_user->has_cap('manage_options'))) {
	exit;
}

$statuses = isset($_POST) && isset($_POST['wpsc_tl_statuses']) && is_array($_POST['wpsc_tl_statuses']) ? $_POST['wpsc_tl_statuses'] : array();
foreach ($statuses as $key => $value) {
  $statuses[] = intval($value);
}
update_option('wpsc_tl_statuses',array_unique($statuses));

$age = isset($_POST['wpsc_atc_age']) ? intval($_POST['wpsc_atc_age']) : 0;
update_option('wpsc_atc_age',$age);

echo '{ "sucess_status":"1","messege":"'.__('Settings saved.','wpsc').'" }';
