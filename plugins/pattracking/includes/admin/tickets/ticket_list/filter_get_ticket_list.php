<?php
// Code to add ID lookup
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly

}

global $wpdb, $current_user, $wpscfunction;

global $meta_query_location_search;
$meta_query_location_search = false;

global $meta_query_location_values;
$meta_query_location_values = array();

//Location Filtering - Adds Labels to Request Dashboard
function add_filters_to_request_dashboard( $labels ) {
   
   $east_label = array(
       'label'      => __('East','supportcandy'),
       'has_badge'  => 0,
       'visibility' => 'agent',
   );
   
   $east_cui_label = array(
       'label'      => __('East CUI','supportcandy'),
       'has_badge'  => 0,
       'visibility' => 'agent',
   );   
   
   $west_label = array(
       'label'      => __('West','supportcandy'),
       'has_badge'  => 0,
       'visibility' => 'agent',
   );
   
   $west_cui_label = array(
       'label'      => __('West CUI','supportcandy'),
       'has_badge'  => 0,
       'visibility' => 'agent',
   );   
   
   $labels['east'] = $east_label;
   $labels['east-cui'] = $east_cui_label;
   $labels['west'] = $west_label;
   $labels['west-cui'] = $west_cui_label;
   		   
   return $labels;
}
add_filter('ticket_filter_labels', 'add_filters_to_request_dashboard' );

/*
* Filter which returns updated SQL query to be used with Support Candy
*
*
*/


function update_location_sql( $sql, $meta_query ) {
    $raw_sql = $sql;
    $raw_meta = $meta_query; //probably not needed
	global $meta_query_location_search;
	global $meta_query_location_values;
	$sql_location_value = '';
	
	// Converts location meta_query into sql compatible code
	foreach ($meta_query_location_values as $index => $loco_solo) {
		$sql_location_value .= "'" .$loco_solo. "',";
	}
	$sql_location_value = rtrim( $sql_location_value, ",");
	
	$category_array = get_the_categories('id');
	$category_string_id = array_to_sql_string( $category_array );
    
    
    //Updates the ordering of when reorder based on location
    $find_order_by_needle = " ORDER BY FIELD( t.ticket_category, ".$category_string_id." )";
    $len = strlen($find_order_by_needle);
	$find_order_by_needle_pos = strpos($sql, $find_order_by_needle);
	
	$order_by_sql = " ORDER BY
    FIELD(
     combo_loco,
    'East',
    'East, East CUI',
    'East, East CUI, West',
    'East, East CUI, West, West CUI',
    'East, East CUI, West CUI',
    'East, West',
    'East, West CUI',
    'East CUI',
    'East CUI, West',
    'East CUI, West, West CUI',
    'East CUI, West CUI',
    'West',
    'West, West CUI',
    'West CUI',    
    'Not Assigned')";
    
	if($find_order_by_needle_pos) {
		$sql = substr_replace($sql, $order_by_sql, $find_order_by_needle_pos-1, $len+1);
	}
	

	//Updates the SQL query for location to include all locations for the request
	$find_sql_1 = "FROM";
	$find_sql_1_pos = strpos($sql, $find_sql_1);
	$find_sql_2 = "WHERE";
	
	//IF Advanced search
	if( $meta_query_location_search ) {
		
		$insert_sql_1 = ", lut.combo_loco ";
		$sql = substr_replace($sql, $insert_sql_1, $find_sql_1_pos-1, 0);
		$find_sql_2_pos = strpos($sql, $find_sql_2);		
								
		$insert_sql_2 = "JOIN ( SELECT 
								    bi.ticket_id AS bid, 
								    GROUP_CONCAT(DISTINCT terms.name ORDER BY terms.name ASC
											SEPARATOR ', ') 
								    AS combo_loco 
								FROM 
								    wpqa_wpsc_epa_boxinfo AS bi 
								JOIN 
								    wpqa_wpsc_epa_storage_location AS esl ON bi.storage_location_id = esl.id 
								JOIN
							        wpqa_terms AS terms
							      ON
							        esl.digitization_center = terms.term_id
								JOIN
								   (
									SELECT 
									    DISTINCT bi.ticket_id AS bidx
									    FROM 
									        wpqa_wpsc_epa_boxinfo AS bi 
									    JOIN 
									        wpqa_wpsc_epa_storage_location AS esl ON bi.storage_location_id = esl.id 
									     AND EXISTS ( SELECT esl.digitization_center
									                  FROM wpqa_wpsc_epa_storage_location as esl
									                 WHERE esl.digitization_center IN (".$sql_location_value.")
									                   AND bi.storage_location_id IN (esl.id )
									                ) 

								    ) 
								    AS ticket_search_result 
								   ON 
								     ticket_search_result.bidx = bi.ticket_id  
								     GROUP BY bi.ticket_id 
									 	HAVING COUNT(bi.ticket_id) > 0  
							  ) 
								AS lut  ON t.id = lut.bid ";

		$sql = substr_replace($sql, $insert_sql_2, $find_sql_2_pos-1, 0);
		$meta_query_location_search = false; //unset location search
		
	} Else {  //ELSE Regular list display with no Meta Query

		$insert_sql_1 = ", lut.combo_loco ";
		$sql = substr_replace($sql, $insert_sql_1, $find_sql_1_pos-1, 0);
		$find_sql_2_pos = strpos($sql, $find_sql_2);		
		
		//Use for digitized loctions as joined wp_terms
		$insert_sql_2 = "JOIN ( SELECT
      bi.ticket_id AS bid,
      GROUP_CONCAT(
        DISTINCT terms.name ORDER BY terms.name ASC
          SEPARATOR ', '
      ) AS combo_loco
    FROM
      wpqa_wpsc_epa_boxinfo AS bi
      JOIN 
        wpqa_wpsc_epa_storage_location AS esl 
        ON 
        bi.storage_location_id = esl.id
      JOIN
        wpqa_terms AS terms
        ON
        esl.digitization_center = terms.term_id
    GROUP BY
      bi.ticket_id
    HAVING
      COUNT(bi.ticket_id) > 0) AS lut ON t.id = lut.bid ";
		
		$sql = substr_replace($sql, $insert_sql_2, $find_sql_2_pos-1, 0);
	}
	   
   return $sql;   
}

add_filter('get_ticket_list_sql', 'update_location_sql', 10, 2 );


/*
*  Looks for mentions of OLD ticket_category and removes it.
*  Sets global vaariable to allow other functions to know that the meta query existed and has been removed. 
*/
function update_meta_query($meta_query) {
	
	global $meta_query_location_search;
	global $meta_query_location_values;
	
	foreach ($meta_query as $index => $sub_array) {
		$key = array_search('ticket_category', $sub_array, true);
		if($key) {
			$meta_query_location_values = $sub_array['value'];
			unset($meta_query[$index]);
			$meta_query_location_search = true;
		}
	}
		
	return $meta_query;
}
add_filter('get_ticket_list_meta_query', 'update_meta_query');



function print_the_ticket_category( $object ) {
	
	$codes = explode(',',$object->ticket['combo_loco']);
	$location_array = array();
	foreach ($codes as $location_id) {
		$term = get_term_by('id', $location_id, 'wpsc_categories');
		$location_array[] = $term->name;
	}
	
	$location_string = implode(', ', $location_array);
	
	echo $object->ticket['combo_loco']; //Outputs name in Request Dashboard Location Column
}

add_action('pattracking_print_ticket_category', 'print_the_ticket_category');


/**
 * Accepts an array of term ids or names as an array
 * and returns a comma separated string with quote around each value
 * Ex: Input Args: Array([0]=>22 [1]=> 33 [2]=>44
 * Returns: string "'22','33','44'"
 */
function array_to_sql_string( $array ) {
	
	$string_of_array = '';
	foreach ($array as $index => $item) {
			$string_of_array .= $item. ",";
		}
	$string_of_array = rtrim( $string_of_array, ","); 

	return $string_of_array;
}

/**
 * Get category term ids or names as an array
 * Accepted args: 'id' or 'name'
 */
function get_the_categories($type) {
  $category_array = array();
  $categories = get_terms([
    'taxonomy'   => 'wpsc_categories',
    'hide_empty' => false,
    'orderby'    => 'meta_value_num',
    'order'    	 => 'ASC',
    'meta_query' => array('order_clause' => array('key' => 'wpsc_category_load_order')),
  ]);
  
  if ($type == 'id') {
	  foreach ($categories as $category) {
	    $category_array[] = $category->term_id;
	  }
  } elseif ($type == 'name') {
	  foreach ($categories as $category) {
	    $category_array[] = $category->name;
	  }
  }
  
  return $category_array;
}


/*
*  Updates the filter used on the Recall Dashboard.
*  These set the Sidebar filters to the corresponding locations. 
*/
function filter_the_filter( $filter, $order_key, $order ) {

    if ( $filter['label'] == 'east') {
	    $filter = array(
	      'label'   => $filter["label"],
	      'query'   => array(),
	      'orderby' => $order_key,
	      'order'   => $order,
	      'page'    => 1,
	      'custom_filter' => array(
	        's' => '',
	        'ticket_category' => array( '0' => '62' ),
	      )
	    );
	} elseif ( $filter['label'] == 'east-cui') {
	    $filter = array(
	      'label'   => $filter["label"],
	      'query'   => array(),
	      'orderby' => $order_key,
	      'order'   => $order,
	      'page'    => 1,
	      'custom_filter' => array(
	        's' => '',
	        'ticket_category' => array( '0' => '663' ),
	      )
	    );
	} elseif ( $filter['label'] == 'west') {
	    $filter = array(
	      'label'   => $filter["label"],
	      'query'   => array(),
	      'orderby' => $order_key,
	      'order'   => $order,
	      'page'    => 1,
	      'custom_filter' => array(
	        's' => '',
	        'ticket_category' => array( '0' => '2' ),
	      )
	    );
	} elseif ( $filter['label'] == 'west-cui') {
	    $filter = array(
	      'label'   => $filter["label"],
	      'query'   => array(),
	      'orderby' => $order_key,
	      'order'   => $order,
	      'page'    => 1,
	      'custom_filter' => array(
	        's' => '',
	        'ticket_category' => array( '0' => '664' ),
	      )
	    );
	}
	
	return $filter;
}

add_filter('ticket_filter_the_filter', 'filter_the_filter', 10, 3);




function powerSet($arr) {
    if (!$arr) return array([]);
    $firstElement = array_shift($arr);
    $recursionCombination = powerSet($arr);
    $currentResult = [];
    foreach($recursionCombination as $comb) {
        $currentResult[] = array_merge($comb, [$firstElement]);
    }
    return array_merge($currentResult, $recursionCombination );
}
