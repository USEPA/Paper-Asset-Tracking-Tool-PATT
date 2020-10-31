<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user, $wpscfunc,$wpscfunction;

if (!$current_user->ID) die();

$filter         = $wpscfunc->get_current_filter();
$filter['s']    = isset($_POST['s']) ? sanitize_text_field($_POST['s']) : '';
$filter['page'] = isset($_POST['page_no']) ? intval($_POST['page_no']) : 1;

setcookie('wpsc_active_customers_filter',json_encode($filter));
