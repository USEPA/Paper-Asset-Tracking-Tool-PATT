<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if(in_array('sla_in',$custom_filter)) {
	$meta_query[] = array(
		'relation' => 'AND',
			array(
				'key' => $field_slug,
				'value' => date('Y-m-d H:i:s'), 
				'compare' => '>=', 
				'type' => 'datetime' 
			),
			array(
				'key' => 'sla_term',
				'value' => '0', 
				'compare' => '!='
			),	
			array(
				'key' => $field_slug,
				'value' => '3099-01-01 00:00:00', 
				'compare' => '!=', 
				'type' => 'datetime' 
			),
		);

} else if(in_array('sla_out',$custom_filter)) {

	$meta_query[] = array(
		'relation' => 'AND',
			array(
				'key' => $field_slug,
				'value' => date('Y-m-d H:i:s'), 
				'compare' => '<=', 
				'type' => 'datetime' 
			),
			array(
				'key' => 'sla_term',
				'value' => '0', 
				'compare' => '!='
			),
			array(
				'key' => $field_slug,
				'value' => '3099-01-01 00:00:00', 
				'compare' => '!=', 
				'type' => 'datetime' 
			),
	);
}