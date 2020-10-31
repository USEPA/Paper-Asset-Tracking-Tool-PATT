<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpdb;

$get_all_meta_keys = $this->get_all_meta_keys();

$status            = $this->get_statuses();
$priority          = $this->get_priorities();
$category          = $this->get_categories();
$join              = array();

$sql = "SELECT ". $select_str ." FROM ".$wpdb->prefix."wpsc_ticket t";

$layer1_where    = array();
$layer1_relation = isset($meta_query['relation']) ? $meta_query['relation'] : 'AND';

foreach ($meta_query as $outer_key => $inner_query) {
    
    if( $outer_key !== 'relation' ){
    
        $layer2_where    = array();
        $layer2_relation = isset($inner_query['relation']) ? $inner_query['relation'] : '';
        
        if( $layer2_relation ) {
          
            foreach ($inner_query as $innner_key => $query) {
            
                if( $innner_key !== 'relation' ){
                
                    $alice = 't';
                    $field = $query['key'];
                    
                    if( in_array($query['key'],$get_all_meta_keys) ){
                    
                        $join[] = $query['key'];
                        $alice  = str_replace('-','_',$query['key']);
                        $field  = 'meta_value';
                    
                    }
                    
										if (isset($query['type'])) {
											
											if (is_array($query['value'])) {
												
												foreach ($query['value'] as $k => $v) {
													$date_data[] = "'$v'";
												}
											}
											
											$value = is_array($query['value']) ? '(' . implode('AND',$date_data) . ')' : "'" . $query['value'] . "'";

										} else {
                      
											if(is_array($query['value'])){
												foreach ($query['value'] as $val_key => $val) {
													$query['value'][$val_key] = "'".$val."'";
												}
											}
											$value = is_array($query['value']) ? '(' . implode(',',$query['value']) . ')' : "'" . $query['value'] . "'";
                      
                    }
                    
                    $layer2_where[] = $alice . "." . $field. " " . $query['compare']. " " . $value;
                
                }
            
            }
          
        } else {
          
          $alice = 't';
          $field = $inner_query['key'];
          
          if( in_array($inner_query['key'],$get_all_meta_keys) ){
              $join[] = $inner_query['key'];
              $alice  = str_replace('-','_',$inner_query['key']);
              $field  = 'meta_value';
          }
          /**
          * If type is DATE, need to implode it.
          */
          if(isset($inner_query['type'])){
            
            foreach ($inner_query['value'] as $k => $v) {
              $date_data[] = "'$v'";
            }
            $value = implode(' AND ',$date_data) ;
            
          } else {
            
						if(is_array($inner_query['value'])){
							foreach ($inner_query['value'] as $val_key => $val) {
								$inner_query['value'][$val_key] = "'".$val."'";
							}
						}
            $value = is_array($inner_query['value']) ? '(' . implode(',',$inner_query['value']) . ')' : "'" . $inner_query['value'] . "'";
            
          }
          $layer2_where[] = $alice . "." . $field. " " . $inner_query['compare']. " " . $value;
          
        }
        
        if($layer2_where){
        
            $layer1_where[] = count($layer2_where) > 1 ? "(" . implode(" ".$layer2_relation." ",$layer2_where) .")" : $layer2_where[0];
        
        }
    
    }
  
}

if($search){
  $search         = addslashes($search);
  $term           = '%'.$search.'%';
  $layer1_where[] = "( " 
                        ."t.id  LIKE '$term' OR "
                        ."t.customer_name  LIKE '$term' OR "
                        ."t.customer_email  LIKE '$term' OR "
                        ."t.ticket_subject  LIKE '$term' OR "
                        ."tm.meta_value  LIKE '$term' "
                    .")" ;
}

$where_str = implode(' '.$layer1_relation.' ', $layer1_where).' ';

$join     = array_unique($join);
$join_str = '';

if (($orderkey = array_search($orderby, $join)) !== false) {
    unset($join[$orderkey]);
}

foreach ( $join as $slug ) {
  
    $alice     = str_replace('-','_',$slug);
    $join_str .= "JOIN {$wpdb->prefix}wpsc_ticketmeta ".$alice." ON t.id = ".$alice.".ticket_id AND ".$alice.".meta_key = '".$slug."' ";
  
}

if($search){
    $join_str .= "JOIN {$wpdb->prefix}wpsc_ticketmeta tm ON t.id = tm.ticket_id ";
}

if ( $orderby && in_array( $orderby, $get_all_meta_keys ) ) {
  
    $alice        = str_replace('-','_',$orderby);
		$join_str    .= " LEFT JOIN {$wpdb->prefix}wpsc_ticketmeta ".$alice." ON t.id = ".$alice.".ticket_id AND ".$alice.".meta_key = '".$orderby."' ";
		$orderby_str  = " ORDER BY " .$alice.".meta_value";
  
} else if( $orderby ) {
  
    if ( $orderby == 'ticket_status' ) {
      
      $orderby_str = " ORDER BY FIELD( t.ticket_status, ". implode(',',$status)." )";
      
    } elseif ( $orderby == 'ticket_priority' ) {
      
      $orderby_str = " ORDER BY FIELD( t.ticket_priority, ". implode(',',$priority) ." )";
      
    } elseif ( $orderby == 'ticket_category' ) {
      
      $orderby_str = " ORDER BY FIELD( t.ticket_category, ".implode(',',$category)." )";
      
    } else {
      
      $orderby_str = " ORDER BY t.".$orderby;
      
    }
  
} else {
  
    $orderby_str = '';
  
}

if( $current_page && $no_of_tickets ) {
  $offset = ($current_page-1)*$no_of_tickets;
  $limit = ' LIMIT ' . $no_of_tickets . "  OFFSET " . $offset;
} else {
  $limit = '';
}

if($order){
  $order = ' '.$order;
} else {
  $order = '';
}

$where = '';
if(strlen( trim($where_str)) ){
  $where = ' WHERE ';
}

$sql .= ' ' . $join_str . $where . $where_str . $orderby_str . $order . $limit;
