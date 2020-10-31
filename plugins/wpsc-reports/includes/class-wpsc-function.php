<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

if ( ! class_exists( 'WPSC_Function' ) ) :
  
  final class WPSC_Function {
    
    function get_meta_query_custom_date($meta_query){
      
      global $wpdb,$wpscfunction;
      
      $get_all_meta_keys = $wpscfunction->get_all_meta_keys();

      $sql = "SELECT DISTINCT count(t.id) from {$wpdb->prefix}wpsc_ticket t  ";
      $join = array();
      $join_str = '';
      $where = "WHERE  ";
      
      foreach ($meta_query as $key => $value) {
        
        if(is_array($value) && isset($value['key'])){
          
          if(is_array($value['value'])){
            
            $output_val = implode(',', $value['value']);
            
          }else {
        
            $output_val = $value['value'];
          }
          
          if(in_array($value['key'] , $get_all_meta_keys)){
            
            $join[] = $value['key'];
            $alice  = str_replace('-','_',$value['key']);
            $where .= $alice.".meta_value ".$value['compare']." ( '". $output_val ."' )" .$relation. " " ;
          
          }else {
        
            $where .= "t." .$value['key']. " " .$value['compare']." ( '".$output_val."' ) "  .$relation." " ;
          }
        
        } else if(is_array($value)) {
          
          $where .= '(';
        
          foreach ($value as $k => $val) {
            
            $where .= $val['type']."( t." .$val['key']. ") " .$val['compare']."'".$val['value']."'" ;
            if(next($value)){
              $where .=  $relation." ";
            }
            
          }
            
          $where .= ')';
        
        }else {
          $relation = $value;
        }
      }
        
      if($join){
          
        $join     = array_unique($join);
        foreach ( $join as $slug ) {
          
          $alice     = str_replace('-','_',$slug);
          $join_str .= "JOIN {$wpdb->prefix}wpsc_ticketmeta ".$alice." ON t.id = ".$alice.".ticket_id AND ".$alice.".meta_key = '".$slug."' ";
            
        }
      }
          
      return $sql .$join_str .$where ;
    }
      
    function get_where_query_date($meta_query){
    
      global $wpdb,$wpscfunction;
      
      $get_all_meta_keys = $wpscfunction->get_all_meta_keys();
      
      $sql = "SELECT DISTINCT count(t.id) from {$wpdb->prefix}wpsc_ticket t ";
      $join = array();
      $join_str = '';
      $where = " WHERE " ;
      
      foreach ($meta_query as $key => $value) {
        
        if(is_array($value) && isset($value['key'])){
          
          if(is_array($value['value'])){
            $output_val = implode(',', $value['value']);
          }
          
          else {
            $output_val = $value['value'];
          }
          
          if(in_array($value['key'] , $get_all_meta_keys)){
            
            $join[] = $value['key'];
            $alice  = str_replace('-','_',$value['key']);
            $where .=  $alice.".meta_value ".$value['compare']." ('". $output_val ."')" .$relation. " " ;
            
          }else {
             
            $where .= "t." .$value['key']. " " .$value['compare']." ( '".$output_val."' ) "  .$relation." " ;
             
          }
         
        } elseif (is_array($value)) {
           
          $where .= '(';
          foreach ($value as $k => $val) {
             
            $where .=    $val['type']."( t." .$val['key']. ") " .$val['compare']."'".$val['value']."' ";
            if(next($value)){
               
              $where .=  $relation." ";
               
            }
             
          }
          $where .= ')';
            
        } else {
            
          $relation = $value;
            
        }
        
      }
        
      if($join){
          
        $join     = array_unique($join);
        $join_str = '';

        foreach ( $join as $slug ) {
        
          $alice     = str_replace('-','_',$slug);
          $join_str .= "JOIN {$wpdb->prefix}wpsc_ticketmeta ".$alice." ON t.id = ".$alice.".ticket_id AND ".$alice.".meta_key = '".$slug."' ";
        }
      }
      
      return $sql .$join_str .$where ;
    
    }
    
    function get_ticket_data($meta_query){
      
      global $wpdb,$wpscfunction;
      
      $get_all_meta_keys = $wpscfunction->get_all_meta_keys();
  
      $sql = "SELECT t.* from {$wpdb->prefix}wpsc_ticket t   ";
      $join = array();
      $join_str = '';
      $where = " WHERE " ;
    
      foreach ($meta_query as $key => $value) {
          
        if(is_array($value) && isset($value['key'])){
            
          if(is_array($value['value'])){
            $output_val = implode(',', $value['value']);
          }
          else {
            $output_val = $value['value'];
          }
          
          if(in_array($value['key'] , $get_all_meta_keys)){
              
            $join[] = $value['key'];
            $alice  = str_replace('-','_',$value['key']);
            $where .= $alice.".meta_value ".$value['compare']." ('". $output_val ."')" .$relation. " " ;
              
          }
          else {
            
            $where .= "t." .$value['key']. " " .$value['compare']." ( '".$output_val."' ) "  .$relation." " ;
            
          }
          
        }
          
        else if (is_array($value)) {
            
          $where .= '(';
          foreach ($value as $k => $val) {
              
            $where .=   $val['type']."( t." .$val['key']. ") " .$val['compare']."'".$val['value']."' ";
            
            if(next($value)){
                
              $where .=  $relation." ";
            }
            
          }
          $where .= ')';
          
        }
          
        else {
            
          $relation = $value;
          
        }
        
      }
      
      if($join){  
        
        $join     = array_unique($join);
        $join_str = '';
        
        foreach ( $join as $slug ) {
        
          $alice    = str_replace('-','_',$slug);
          $join_str .= "JOIN {$wpdb->prefix}wpsc_ticketmeta ".$alice." ON t.id = ".$alice.".ticket_id AND ".$alice.".meta_key = '".$slug."' ";
          
        }
      
      }
      
      return $sql .$join_str .$where ;
      
    }
    
    // active customers default filter
    function get_default_filter(){
      
      $filter = array(
        'page' => 1,
        's'    => '' 
      ); 
      return $filter; 
    }
    
    // active customers current filter
    function get_current_filter(){
      $filter = isset($_COOKIE['wpsc_active_customers_filter']) ? $_COOKIE['wpsc_active_customers_filter'] : '';
      if (!$filter) {
        $filter = $this->get_default_filter();
      } else {
        $filter = json_decode(stripslashes($filter),true);
      }
      return $filter;
    }
  }  
  endif;

  $GLOBALS['wpscfunc'] =  new WPSC_Function();