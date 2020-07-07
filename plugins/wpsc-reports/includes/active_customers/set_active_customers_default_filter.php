<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user, $wpscfunction, $wpscfunc;

if (!$current_user->ID) die();

$filter = $wpscfunc->get_default_filter();

setcookie('wpsc_active_customers_filter',json_encode($filter));
