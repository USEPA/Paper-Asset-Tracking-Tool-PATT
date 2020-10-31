<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// UPDATE to update database based on list of items that are listed as shipped '1'.

global $current_user, $wpscfunction, $wpdb;

// For Return Status to change from Return Initiated [752] to Return Shipped [753]
$shipped_return_status_query = $wpdb->get_results(
	"SELECT 
      shipping.id,
      shipping.tracking_number,
      shipping.shipped,
      shipping.delivered,
      shipping.return_id,
      ret.id,
      ret.return_id as return_id,
      ret.return_status_id as return_status
    FROM 
	    wpqa_wpsc_epa_shipping_tracking AS shipping
    INNER JOIN 
		wpqa_wpsc_epa_return AS ret 
	ON (
        shipping.return_id = ret.id
	   )
	WHERE 
        shipping.return_id <> -99999
      AND 
        shipping.company_name <> ''
      AND
        shipping.shipped = 1
      AND 
        ret.return_status_id = 752
      ORDER BY shipping.id ASC"
	);
	
// For Return Status to change from Return Initiated [752] to Return Shipped [753]
foreach ($shipped_return_status_query as $item) {
	
	// update return status to Return Shipped [753]
	$return_id = $item->return_id;	
	$where = [ 'id' => $return_id ];
	$data_status = [ 'return_status_id' => 753 ]; //change status from Return Initiated [752] to Return Shipped [753] 
	$obj = Patt_Custom_Func::update_return_data( $data_status, $where );
	
	// No need to clear shipped status as all shipping data will need to be preserved for Delivered column
	
}




// For Return Status to change from Return Shipped [753] to Return Complete [754]
$return_complete_return_status_query = $wpdb->get_results(
	"SELECT 
      shipping.id,
      shipping.tracking_number,
      shipping.shipped,
      shipping.delivered,
      shipping.return_id,
      ret.id,
      ret.return_id as return_id,
      ret.return_status_id as return_status
    FROM 
	    wpqa_wpsc_epa_shipping_tracking AS shipping
    INNER JOIN 
		wpqa_wpsc_epa_return AS ret 
	ON (
        shipping.return_id = ret.id
	   )
	WHERE 
        shipping.return_id <> -99999
      AND 
        shipping.company_name <> ''
      AND
        shipping.delivered = 1
      AND 
        ret.return_status_id = 753
      ORDER BY shipping.id ASC"
	);

	
// For Return Status to change from Return Shipped [753] to Return Complete [754]
foreach ($return_complete_return_status_query as $item) {
	
	// update return status to Return Shipped [753]
	$return_id = $item->return_id;	
	$where = [ 'id' => $return_id ];
	$data_status = [ 'return_status_id' => 754 ]; //change status from Return Shipped [753] to Return Complete [754] 
	$obj = Patt_Custom_Func::update_return_data( $data_status, $where );
	
	// Update Return (Decline) db when it is delivered.
	$where = [ 'id' => $return_id ];
	$current_datetime = date("yy-m-d H:i:s");
 	$data = [ 'return_receipt_date' => $current_datetime ]; 
	Patt_Custom_Func::update_return_data( $data, $where );
	
	// No need to clear shipping status as shipping data may need to be periodically purged. 
	// Reset the shipping details as the same id is used for shipping to requestor and back to digitization center.
/*
	$data = [
		'company_name' => '',
		'tracking_number' => '',
		'shipped' => 0,
		'delivered' => 0,		
		'status' => ''
	];
	$where = [
		'return_id' => $return_id
	];

	$recall_array = Patt_Custom_Func::update_return_shipping( $data, $where );	
*/
	
}





?>
