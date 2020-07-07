<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user, $wpscfunction;

if (!$current_user->ID) die();

$search_id = isset($_POST['label']) ? sanitize_text_field($_POST['label']) : '';

$box_file_details = Patt_Custom_Func::get_box_file_details_by_id($search_id);
// $box_file_details = Patt_Custom_Func::get_box_file_details_by_id('0000288-1');
// $box_file_details = Patt_Custom_Func::get_box_file_details_by_id('0000001-2-01-10');
//print_r($box_file_details);

$details_array = json_decode(json_encode($box_file_details), true);

if($search_id == 'false') {
	$search_response = '';
} else {
	$search_response = $details_array;
}

echo json_encode( $search_response );
