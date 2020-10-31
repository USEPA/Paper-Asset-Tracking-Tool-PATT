<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

if ( ! class_exists( 'WPSC_Functions' ) ) :
  
  final class WPSC_Functions {
    
    // Return custom field types. Developers, please make sure hooked custom field type index should start from 51. 1-50 is reserved for core product.
    function get_custom_field_types(){
      $custom_field_types = array(
        1  => array( 
          'label' => __('Text Field','supportcandy'),
          'has_options' => 0,
          'allow_ticket_list' => 1,
          'allow_ticket_filter' => 1,
          'allow_orderby' => 1,
          'ticket_filter_type' => 'string'
        ),
        2  => array( 
          'label' => __('Drop Down','supportcandy'),
          'has_options' => 1,
          'allow_ticket_list' => 1,
          'allow_ticket_filter' => 1,
          'allow_orderby' => 1,
          'ticket_filter_type' => 'string'
        ),
        3  => array( 
          'label' => __('Checkbox','supportcandy'),
          'has_options' => 1,
          'allow_ticket_list' => 1,
          'allow_ticket_filter' => 1,
          'allow_orderby' => 1,
          'ticket_filter_type' => 'string'
        ),
        4  => array( 
          'label' => __('Radio Button','supportcandy'),
          'has_options' => 1,
          'allow_ticket_list' => 1,
          'allow_ticket_filter' => 1,
          'allow_orderby' => 1,
          'ticket_filter_type' => 'string'
        ),
        5  => array( 
          'label' => __('Textarea','supportcandy'),
          'has_options' => 0,
          'allow_ticket_list' => 0,
          'allow_ticket_filter' => 0,
          'allow_orderby' => 1,
          'ticket_filter_type' => 'string'
        ),
        6  => array( 
          'label' => __('Date','supportcandy'),
          'has_options' => 0,
          'allow_ticket_list' => 1,
          'allow_ticket_filter' => 1,
          'allow_orderby' => 1,
          'ticket_filter_type' => 'date'
        ),
        7  => array( 
          'label' => __('URL','supportcandy'),
          'has_options' => 0,
          'allow_ticket_list' => 0,
          'allow_ticket_filter' => 1,
          'allow_orderby' => 0,
          'ticket_filter_type' => 'string'
        ),
        8  => array( 
          'label' => __('Email','supportcandy'),
          'has_options' => 0,
          'allow_ticket_list' => 1,
          'allow_ticket_filter' => 1,
          'allow_orderby' => 1,
          'ticket_filter_type' => 'string'
        ),
        9  => array( 
          'label' => __('Number Only','supportcandy'),
          'has_options' => 0,
          'allow_ticket_list' => 1,
          'allow_ticket_filter' => 1,
          'allow_orderby' => 1,
          'ticket_filter_type' => 'number'
        ),
        10  => array( 
          'label' => __('File Attachment','supportcandy'),
          'has_options' => 0,
          'allow_ticket_list' => 0,
          'allow_ticket_filter' => 0,
          'allow_orderby' => 0,
          'ticket_filter_type' => 'string'
        ),
        18  => array( 
          'label' => __('Date Time','supportcandy'),
          'has_options' => 0,
          'allow_ticket_list' => 1,
          'allow_ticket_filter' => 1,
          'allow_orderby' => 1,
          'ticket_filter_type' => 'datetime'
        ),
        21 => array(
          'label' => __('Time','supportcandy'),
          'has_options' => 0,
          'allow_ticket_list' => 1,
          'allow_ticket_filter' => 1,
          'allow_orderby' => 1,
          'ticket_filter_type' => 'time'
        ),
        22 => array( 
          'label' => __('HTML','supportcandy'),
          'has_options' => 0,
          'allow_ticket_list' => 0,
          'allow_ticket_filter' => 0,
          'allow_orderby' => 0,
          'ticket_filter_type' => 'string'
        ),
      );
      return apply_filters('wpsc_custom_field_types',$custom_field_types);
    }
    
    // Calender date format to datetime conversion so that it can be compared in query
    function calenderDateFormatToDateTime($date){
      if (!$date) return '';
      $datetime = explode(' ',$date);
      $calender_format = get_option('wpsc_calender_date_format');
      $y = ''; $m = ''; $d = '';
      $calender_format = explode('-',$calender_format);
      $date = explode('-',$date);
      foreach ($calender_format as $key => $value) {
        switch ($value) {
          case 'dd':
            $d = $date[$key];
            break;
            
          case 'mm':
            $m = $date[$key];
            break;
            
          case 'yy':
            $y = $date[$key];
            break;
        }
      }
      if (!$y||!$m||!$d) {
        return '';
      }
      if(array_key_exists('1',$datetime)){
        return $y.'-'.$m.'-'.$d;
      } else{
        return $y.'-'.$m.'-'.$d.' 00:00:00';
      }  
    }
    
    // Calender date format to datetime conversion so that it can be compared in query
    function datetimeToCalenderFormat($datetime){ 
      if(!$datetime) return '';
      $calender_format = get_option('wpsc_calender_date_format');
      $calender_format = explode('-',$calender_format);
      $arr = array();
      foreach ($calender_format as $key => $value) {
        switch ($value) {
          case 'dd':
            $arr[]='d';
            break;
          case 'mm':
            $arr[]='m';
            break;
          case 'yy':
            $arr[]='Y';
            break;
        }
      }
      $d_format = implode('-',$arr);
      $date=date_create($datetime);
      return date_format($date, $d_format);
    }
    
    // Get current agent permissions.
    function get_current_agent_permissions(){
      global $current_user;
      if (is_super_admin($current_user->ID) && is_multisite()) {
        return $this->get_all_permission_array();
      }
      $role_id    = get_user_option('wpsc_agent_role');
      $agent_role = get_option('wpsc_agent_role');
      return $agent_role[$role_id];
    }
    
    // Allow all permission response
    function get_all_permission_array(){
      $agent_role = get_option('wpsc_agent_role');
      $permissions = array();
      foreach ($agent_role[1] as $key => $value) {
        if ($key=='label') continue;
        $permissions[$key] = 1;
      }
      return $permissions;
    }
    
    // Get default ticket filter labels
    function get_ticket_filter_labels(){
      $labels = array(
        'all' => array(
            'label'      => __('All Tickets','supportcandy'),
            'has_badge'  => 0,
            'visibility' => 'both',
        ),
        'unresolved_agent' => array(
            'label'      => __('Unresolved','supportcandy'),
            'has_badge'  => 1,
            'visibility' => 'agent',
        ),
        'unresolved_customer' => array(
            'label'      => __('Unresolved','supportcandy'),
            'has_badge'  => 0,
            'visibility' => 'customer',
        ),
        'unassigned' => array(
            'label'      => __('Unassigned','supportcandy'),
            'has_badge'  => 0,
            'visibility' => 'agent',
        ),
        'mine' => array(
            'label'      => __('Mine','supportcandy'),
            'has_badge'  => 0,
            'visibility' => 'agent',
        ),
        'closed' => array(
            'label'      => __('Closed','supportcandy'),
            'has_badge'  => 0,
            'visibility' => 'both',
        ),
        'deleted' => array(
            'label'      => __('Deleted','supportcandy'),
            'has_badge'  => 0,
            'visibility' => 'agent',
        ),
      );
      
      return apply_filters('ticket_filter_labels',$labels);
    }
    
    // Get default user filter
    function get_default_filter(){
      global $current_user;
      if ($current_user->has_cap('wpsc_agent')) {
        $order_key  = get_option('wpsc_tl_agent_orderby');
        $order      = get_option('wpsc_tl_agent_orderby_order');
      } else {
        $order_key  = get_option('wpsc_tl_customer_orderby');
        $order      = get_option('wpsc_tl_customer_orderby_order');
      }
      // check current user has default filter is set or not 
      $blog_id        = get_current_blog_id();
      $default_filter = get_user_meta($current_user->ID,$blog_id.'_wpsc_user_default_filter',true);
      $default_filter = ( $current_user->has_cap('wpsc_agent') && strlen($default_filter) )? $default_filter: 'all';
      if(is_numeric($default_filter) && ((int)$default_filter) >= 0  ){ 
        $saved_filters  = get_user_meta($current_user->ID, $blog_id.'_wpsc_filter',true);
        $saved_filters  = $saved_filters ? $saved_filters : array();
        $filter         = $saved_filters[$default_filter]; 
      }else {
        $filter = array(
          'label'   => $default_filter,
          'query'   => $this->get_default_filter_query($default_filter),
          'orderby' => $order_key,
          'order'   => $order,
          'page'    => 1,
          'custom_filter' => array(
            's' => '',
          )
        ); 
      }

      return $filter;
    }
    
    // Get current user filter
    function get_current_filter(){
      $filter = isset($_COOKIE['wpsc_ticket_filter']) ? $_COOKIE['wpsc_ticket_filter'] : '';
      if (!$filter) {
        $filter = $this->get_default_filter();
      } else {
        $filter = json_decode(stripslashes($filter),true);
      }
      return $filter;
    }
    
    // Return default filter query
    function get_default_filter_query( $label_key ){
      
      global $current_user;
      
      $query = array();

      switch ($label_key) {
        case 'all':
        case 'deleted':
      		$query = apply_filters('wpsc_filter_label_all', $query, $label_key);
          break;
          
        case 'unresolved_agent':
      		$unresolved_agent = get_option('wpsc_tl_agent_unresolve_statuses');
      		$query[] = array(
      			'key'            => 'ticket_status',
      			'value'          => $unresolved_agent,
      			'compare'        => 'IN'
      		);
      		$query = apply_filters('wpsc_filter_label_unresolved_agent', $query, $label_key);
      	  break;
          
        case 'unresolved_customer':
      		$unresolved_customer = get_option('wpsc_tl_customer_unresolve_statuses');
      		$query[] = array(
      			'key'            => 'ticket_status',
      			'value'          => $unresolved_customer,
      			'compare'        => 'IN'
      		);
      		$query = apply_filters('wpsc_filter_label_unresolved_customer', $query, $label_key);
      	  break;
          
        case 'unassigned':
          $close_status = get_option( 'wpsc_close_ticket_group');
          
          $query[] = array(
            'relation' => 'AND',
            array(
              'key'       => 'ticket_status',
              'value'     => $close_status,
              'compare'   => 'NOT IN',
            ),
            array(
              'key'       => 'assigned_agent',
              'value'     => 0,
              'compare'   => '='
             )
          );

      		$query = apply_filters('wpsc_filter_label_unassigned', $query, $label_key);
          break;
          
        case 'mine':
          $agents = get_terms([
            'taxonomy'   => 'wpsc_agents',
            'hide_empty' => false,
            'meta_query' => array(
              'relation' => 'AND',
              array(
                'key'       => 'user_id',
                'value'     => $current_user->ID,
                'compare'   => '='
              )
            ),
          ]);
         
          $close_status = get_option( 'wpsc_close_ticket_group');
          
      		if($agents){
      			$query[] = array(
              'relation' => 'AND',
              array(
                'key'       => 'ticket_status',
      			    'value'     => $close_status,
                'compare'   => 'NOT IN',
              ),
              array(
                'key'       => 'assigned_agent',
                'value'     => $agents[0]->term_id,
                'compare'   => '='
               )
            );
          }
          
          $query = apply_filters('wpsc_filter_label_mine', $query, $label_key);
          break;

        case 'closed':
          $close_status = get_option( 'wpsc_close_ticket_group');
          $query[] = array(
            'key'            => 'ticket_status',
      			'value'          => $close_status,
            'compare'        => '=',
            'compare'        => 'IN'
          );
      		$query = apply_filters('wpsc_filter_label_closed', $query, $label_key);
          break;
        
        default:
          $query = apply_filters('wpsc_filter_label_default', $query, $label_key );
          break;
      }
      return $query;
      
    }
    
    // Get sort type for current orderby field
    function get_field_sort_type($field_slug){
      $term = get_term_by( 'slug', $field_slug, 'wpsc_ticket_custom_fields');
      $ticket_filter_type = get_term_meta($term->term_id,'wpsc_ticket_filter_type',true);
      switch ($ticket_filter_type) {
        case 'number':
          $sort_type = 'meta_value_num';
          break;
          
        case 'date':
        case 'datetime':
        case 'string':
          $sort_type = 'meta_value';
          break;
      }
      return apply_filters( 'wpsc_field_sort_type', $sort_type, $ticket_filter_type, $term );
    }
    
    // Get post status
    function get_post_status($filter){
      $post_status = $filter['label'] == 'deleted' ? 'trash' : 'publish';
      return apply_filters( 'wpsc_post_status', $post_status, $filter );
    }
    
    // Array sanitization
    function sanitize_array($array) {
      foreach ( $array as $key => $value ) {
          if ( is_array( $value ) ) {
              $value = $this->sanitize_array($value);
          }
          else {
              $value = sanitize_text_field( $value );
          }
      }
      return $array;
    }
    
    // Get label for ticket field in filter using slug & value
    function get_tf_value_filter_label($field_slug,$val){
      switch ($field_slug) {
      	
      	case 'ticket_status':
        
            $term = get_term_by('id',$val,'wpsc_statuses');
            return $term? $term->name : '';
            break;
          
        case 'ticket_category':
        
            $term = get_term_by('id',$val,'wpsc_categories');
            return $term? $term->name : '';
            break;
        
        case 'ticket_priority':
      	
      			$term = get_term_by('id',$val,'wpsc_priorities');
            return $term? $term->name : '';
      			break;
      			
        case 'ticket_widget':
          
            $term = get_term_by('id',$val,'wpsc_ticket_widget');
            return $term? $term->name : '';
            break;
         
      	case 'assigned_agent':
      	case 'agent_created':
      	
      			if($val==0) return __('None');
            $term = get_term_by('id',$val,'wpsc_agents');
            if($term){
              $label = get_term_meta($term->term_id,'label',true);
              return $label;
            } else {
              return '';
            }
      			break;
        
        case 'user_type':
        
          if ($val == "user") {
            $label = __('User','supportcandy');
            return $label;
          } elseif ($val == "guest") {
            $label = __('Guest','supportcandy');
            return $label;
          }
          
          break;
      			
      	default:
      		  
            $val = apply_filters('wpsc_filter_val_label',$val,$field_slug);
            
      			return $val;
      			break;
      }
    }
    
    // get time ago format for datetime
    function time_elapsed_string($datetime) {
      $now = new DateTime;
      $ago = new DateTime($datetime);
      $diff = $now->diff($ago);

      $diff->w = floor($diff->d / 7);
      $diff->d -= $diff->w * 7;

      $string = array(
          'y' => __('years','supportcandy'),
          'm' => __('months','supportcandy'),
          'w' => __('weeks','supportcandy'),
          'd' => __('days','supportcandy'),
          'h' => __('hours','supportcandy'),
          'i' => __('minutes','supportcandy'),
          's' => __('seconds','supportcandy'),
      );
      foreach ($string as $k => &$v) {
          if ($diff->$k) {
              $v = $diff->$k . ' ' . $v;
          } else {
              unset($string[$k]);
          }
      }

      $string = array_slice($string, 0, 1);
      if($string){
        $string = implode(' ', $string);
        $string = explode(' ', $string);
        $digit = $string[0];
        $unit = $string[1];
        return sprintf(__("%d %s ago",'supportcandy'),$digit,$unit);
      }else if(!$datetime){
        return '';
      }else {
        return __('just now','supportcandy');
      }
      
    }
    
    function time_elapsed_timestamp($datetime){
      $timestamp = '';
      if($datetime){
        $wpsc_thread_date_time_format = get_option('wpsc_thread_date_time_format');
        $timestamp = date_i18n( $wpsc_thread_date_time_format, strtotime( get_date_from_gmt($datetime, 'Y-m-d H:i:s') ) );  
      }
      return $timestamp;
    }
    
    function assign_agent($ticket_id, $agents){
      global $wpscfunction,$wpdb;
      $prev_assigned = $wpscfunction->get_ticket_meta($ticket_id,'assigned_agent');
      
      $wpscfunction->delete_ticket_meta($ticket_id,'prev_assigned_agent');

      foreach($prev_assigned as $assign_agent){
        $wpscfunction->add_ticket_meta($ticket_id,'prev_assigned_agent',$assign_agent);
      }

      $wpscfunction->delete_ticket_meta($ticket_id,'assigned_agent');
      if($agents){
       foreach($agents as $agent){
         $wpscfunction->add_ticket_meta($ticket_id,'assigned_agent',$agent);
       }
      }
      else{
        $wpscfunction->add_ticket_meta($ticket_id,'assigned_agent',0);
      }
      do_action('wpsc_set_assign_agent', $ticket_id, $agents, $prev_assigned);
    }
        
    function change_status($ticket_id,$status_id){
      global $wpscfunction,$wpdb;
      $prev_status = $wpscfunction->get_ticket_fields($ticket_id,'ticket_status');
      $values=array(
         'ticket_status'=>$status_id
      );
      $wpdb->update($wpdb->prefix.'wpsc_ticket', $values, array('id'=>$ticket_id));
      
      $wpsc_close_ticket_status = get_option('wpsc_close_ticket_status');
      if ($wpsc_close_ticket_status == $status_id){
        $add_close_date = $wpscfunction->get_ticket_meta($ticket_id, 'date_closed');
        if(!$add_close_date){
          $wpscfunction->add_ticket_meta($ticket_id, 'date_closed', date("Y-m-d H:i:s"));
        }
        else {
          $wpscfunction->update_ticket_meta($ticket_id, 'date_closed',array('meta_value' => date("Y-m-d H:i:s")));
        }
      }else{
        $wpscfunction->delete_ticket_meta($ticket_id ,'date_closed');
      }
      
      do_action('wpsc_set_change_status', $ticket_id, $status_id, $prev_status);
        // PATT BEGIN

$get_customer_name = $wpdb->get_row('SELECT customer_name FROM wpqa_wpsc_ticket WHERE id = "' . $ticket_id . '"');
$get_user_id = $wpdb->get_row('SELECT ID FROM wpqa_users WHERE display_name = "' . $get_customer_name->customer_name . '"');

$user_id_array = [$get_user_id->ID];
$convert_patt_id = Patt_Custom_Func::translate_user_id($user_id_array,'agent_term_id');
$patt_agent_id = implode($convert_patt_id);
$pattagentid_array = [$patt_agent_id];
$data = [];
$requestid = Patt_Custom_Func::convert_request_db_id($ticket_id);

//disabled email notification
$email = 1;

//sends an email/notification if status changes from New to Initial Review Complete
if($status_id == 4 && $prev_status == 3){
Patt_Custom_Func::insert_new_notification('email-prepare-request-shipment',$pattagentid_array,$requestid,$data,$email);
}

//sends an email/notification if status changes to Initial Review Rejected
if($status_id == 670) {
Patt_Custom_Func::insert_new_notification('email-initial-review-rejected',$pattagentid_array,$requestid,$data,$email);
}

//sends an email/notification if status changes to Cancelled
if($status_id == 69) {
Patt_Custom_Func::insert_new_notification('email-cancelled',$pattagentid_array,$requestid,$data,$email);
}

// PATT END
		    
    }
        
    function change_category($ticket_id,$category_id){
      global $wpscfunction,$wpdb;
      $prev_cat = $wpscfunction->get_ticket_fields($ticket_id,'ticket_category');
      $values   = array(
         'ticket_category'=>$category_id
      );
      $wpdb->update($wpdb->prefix.'wpsc_ticket', $values, array('id' =>$ticket_id));
      do_action('wpsc_set_change_category',$ticket_id,$category_id,$prev_cat);
    }
    
    function change_priority($ticket_id,$priority_id){
      global $wpscfunction,$wpdb;
      $prev_priority = $wpscfunction->get_ticket_fields($ticket_id,'ticket_priority');
      $values=array(
         'ticket_priority'=>$priority_id
      );
      $wpdb->update($wpdb->prefix.'wpsc_ticket', $values, array('id' =>$ticket_id));
      do_action('wpsc_set_change_priority', $ticket_id,  $priority_id, $prev_priority);
    }
    
    function change_raised_by($ticket_id,$name,$email){
      global $wpscfunction,$wpdb;
      $prev_name = $wpscfunction->get_ticket_fields($ticket_id,'customer_name');
      $values = array(
         'customer_name'=> $name,
         'customer_email' => $email
      );
      $wpdb->update($wpdb->prefix.'wpsc_ticket', $values, array('customer_name'=>$prev_name,'id'=>$ticket_id));
      do_action('wpsc_set_change_raised_by', $ticket_id, $name, $email,$prev_name);
    }
    
    /**
     * Change ticket custom field
     */
    function change_field( $ticket_id, $field_slug, $field_val ){
      
        global $wpscfunction, $wpdb;
        
        if ( $field_val === '' ) {
          
            $prev_field_val = $wpscfunction->get_ticket_meta($ticket_id,$field_slug);
            $wpscfunction->delete_ticket_meta($ticket_id,$field_slug);
          
        } else if( is_array($field_val) ){
          
            $prev_field_val = $wpscfunction->get_ticket_meta($ticket_id,$field_slug);
            $wpscfunction->delete_ticket_meta($ticket_id,$field_slug);
            foreach ($field_val as $value) {
              $wpscfunction->add_ticket_meta($ticket_id,$field_slug,$value);
            }
          
        } else {
          
            $term           = get_term_by('slug',$field_slug,'wpsc_ticket_custom_fields');
            $wpsc_tf_type   = get_term_meta( $term->term_id, 'wpsc_tf_type',true);
            $prev_field_val = $wpscfunction->get_ticket_meta($ticket_id,$field_slug,true);
            
            $values = array(
              'meta_value' => $field_val,
            );
            
            $ticket_field = $wpdb->get_var("SELECT meta_key FROM {$wpdb->prefix}wpsc_ticketmeta WHERE ticket_id='$ticket_id' AND meta_key = '$field_slug'");
            
            if(!$ticket_field){
              $wpdb->insert( $wpdb->prefix . 'wpsc_ticketmeta', 
                array(
                  'ticket_id'  => $ticket_id,
                  'meta_key'   => $field_slug,
                  'meta_value' => $field_val
                ));
            }
            
            $wpscfunction->update_ticket_meta($ticket_id,$field_slug,$values);
          
        }
        
        do_action( 'wpsc_set_change_fields', $ticket_id, $field_slug, $field_val, $prev_field_val );  
      
    }
    
    function delete_tickets($ticket_id){
      global $wpdb;
      
      $values = array(
        'active' => '0'
      );
      $wpdb->update($wpdb->prefix.'wpsc_ticket',$values,array('id'=>$ticket_id));
      do_action('wpsc_set_delete_ticket',$ticket_id);
    }
    
    // Get post id for ticket id
    function get_ticket_post_id($ticket_id){
      $posts = get_posts(array(
      	'post_type'    => 'wpsc_ticket',
        'post_status'  => array('publish','trash'),
      	'meta_query'   => array(
      		'relation' => 'AND',
      		array(
      			'key'     => 'ticket_id',
      			'value'   => $ticket_id,
      			'compare' => '='
      		),
      	),
      ));
      $ticket_post = count($posts) ? $posts[0] : array();
      $post_id = $ticket_post ? $ticket_post->ID : 0;
      return $post_id;
    }
    
    // Get status name by status id
    function get_status_name($status_id){
      $wpsc_custom_status_localize = get_option('wpsc_custom_status_localize');
      return $wpsc_custom_status_localize['custom_status_'.$status_id];
    }
    
    // Get category name by category id
    function get_category_name($category_id){
      $wpsc_custom_category_localize = get_option('wpsc_custom_category_localize');
      return $wpsc_custom_category_localize['custom_category_'.$category_id];
    }
    
    // Get priority name by category id
    function get_priority_name($priority_id){
      $wpsc_custom_priority_localize = get_option('wpsc_custom_priority_localize');
      return $wpsc_custom_priority_localize['custom_priority_'.$priority_id];
    }
    
    // Get ticket_widget name by ticket_widget id
    function get_ticket_widget_name($ticket_widget_id){
      $ticket_widget = get_term_by('id',$ticket_widget_id,'wpsc_ticket_widget');
      return $ticket_widget->name;
    }
      
    // Get ticket description
    function get_ticket_description($ticket_id){
      $threads = get_posts(array(
        'post_type'   => 'wpsc_ticket_thread',
        'post_status' => 'publish',
        'meta_query'  => array(
          'relation' => 'AND',
          array(
            'key'     => 'thread_type',
            'value'   => 'report',
            'compare' => '='
          ),
          array(
            'key'     => 'ticket_id',
            'value'   => $ticket_id,
            'compare' => '='
          ),
        ),
      ));
      $description   = $threads ? $threads[0]->post_content : '';
      $thread_id     = $threads ? $threads[0]->ID : 0;
      $ticket_report = array();
      
      if ($thread_id) {
        $ticket_report = array(
          'description' => $description,
          'thread_id'   => $thread_id,
        );
      } else {
        $ticket_report = array(
          'description' => $description,
        );
      }
      return $ticket_report;
    }
    
    // Get last reply
    function get_last_reply($ticket_id){
      $threads = get_posts(array(
        'post_type'      => 'wpsc_ticket_thread',
        'post_status'    => 'publish',
        'posts_per_page' => '1',
        'orderby'        => 'date',
        'order'          => 'DESC',
        'meta_query'     => array(
          'relation' => 'AND',
          array(
            'key'     => 'ticket_id',
            'value'   => $ticket_id,
            'compare' => '='
          ),
          array(
            'key'     => 'thread_type',
            'value'   => 'reply',
            'compare' => '='
          ),
        ),
      ));
      $thread_id = $threads ? $threads[0]->ID : 0;
      if ($thread_id) {
        $last_reply = array(
          'user_name'   => get_post_meta($thread_id,'customer_name',true),
          'user_email'  => get_post_meta($thread_id,'customer_email',true),
          'description' => $threads[0]->post_content,
          'thread_id'   => $thread_id,
        );
      } else {
        $last_reply = array(
          'user_name'   => '',
          'user_email'  => '',
          'description' => '',
          'thread_id'   => $thread_id
        );
      }
      return $last_reply;
    }
    
    // Get last note
    function get_last_note($ticket_id){
      $threads = get_posts(array(
        'post_type'      => 'wpsc_ticket_thread',
        'post_status'    => 'publish',
        'posts_per_page' => '1',
        'orderby'        => 'date',
        'order'          => 'DESC',
        'meta_query'     => array(
          'relation' => 'AND',
          array(
            'key'     => 'ticket_id',
            'value'   => $ticket_id,
            'compare' => '='
          ),
          array(
            'key'     => 'thread_type',
            'value'   => 'note',
            'compare' => '='
          ),
        ),
      ));
      $thread_id = $threads ? $threads[0]->ID : 0;
      if ($thread_id) {
        $last_note = array(
          'user_name'   => get_post_meta($thread_id,'customer_name',true),
          'user_email'  => get_post_meta($thread_id,'customer_email',true),
          'description' => $threads[0]->post_content,
          'thread_id'   => $thread_id,
        );
      } else {
        $last_note = array(
          'user_name'   => '',
          'user_email'  => '',
          'description' => '',
          'thread_id'   => $thread_id
        );
      }
      return $last_note;
    }
    
    // Get Ticket History
    function get_ticket_history($ticket_id){
      include WPSC_ABSPATH . 'includes/functions/get_ticket_history.php';
      return $ticket_history;
    }
    
    // Get assigned_agent names
    function get_assigned_agent_names($ticket_id){
      global $wpscfunction;
      $assigned_agents = $wpscfunction->get_ticket_meta($ticket_id, 'assigned_agent'); 
      $agent_names = array();
      foreach ($assigned_agents as $agent_id) {
        $agent_names[] = $this->get_agent_name($agent_id);
      }
      return implode(', ', $agent_names);
    }
    
    // Get assigned_agent names
    function get_assigned_agent_emails($ticket_id){
      global $wpscfunction;
      $assigned_agents = $wpscfunction->get_ticket_meta($ticket_id,'assigned_agent');
      $agent_emails = array();
      foreach ($assigned_agents as $agent_id) {
        $user_id = get_term_meta($agent_id,'user_id',true);
        if($user_id){
          $user = get_user_by('id',$user_id);
          $agent_emails[] = $user->user_email;
        }
      }
      return apply_filters( 'wpsc_get_assigned_agent_emails', $agent_emails ,$ticket_id);
    }
    
    // Get agent name by id
    function get_agent_name($agent_id){
      if($agent_id){
        return get_term_meta($agent_id,'label', true);
      } else {
        return __('None','supportcandy');
      }
    }
    
    // Submit ticket
    function create_ticket($args){
      include WPSC_ABSPATH . 'includes/functions/create_ticket.php';
      return $ticket_id;
    }
    
    // Submit clone ticket ticket
    function create_clone_ticket($args){
      include WPSC_ABSPATH . 'includes/functions/create_clone_ticket.php';
      return $ticket_id;
    }
    
    // Submit ticket thread
    function submit_ticket_thread($args){
      global $wpdb;
      $thread_id = wp_insert_post(
      	array(
      		'post_type'    => 'wpsc_ticket_thread',
      		'post_content' => $args['reply_body'],
      		'post_status'  => 'publish',
      	)
      );
      add_post_meta( $thread_id, 'ticket_id', $args['ticket_id'] );
      add_post_meta( $thread_id, 'thread_type', $args['thread_type'] );
      if(isset($args['reply_source'])){
        add_post_meta( $thread_id, 'reply_source', $args['reply_source'] );
      }
      $customer_name =  isset($args['customer_name']) ? $args['customer_name'] : '';
      add_post_meta( $thread_id, 'customer_name', $customer_name );
      $customer_email =  isset($args['customer_email']) ? $args['customer_email'] : '';
      add_post_meta( $thread_id, 'customer_email', $customer_email );
      $attachments =  isset($args['attachments']) ? $args['attachments'] : array();
      add_post_meta( $thread_id, 'attachments', $attachments );
      
      if(isset($args['ip_address'])){
        add_post_meta( $thread_id, 'ip_address', $args['ip_address'] );
      }
      
      if(isset($args['os'])){
        add_post_meta($thread_id,'os',$args['os']);
      }
      
      if(isset($args['browser'])){
        add_post_meta($thread_id,'browser',$args['browser']);
      }
      
      if (isset($args['user_seen'])) {
        add_post_meta($thread_id,'user_seen',$args['user_seen']);
      }
      
      $ticket_id =  isset($args['ticket_id']) ? $args['ticket_id'] : 0;
      $values= array(
        'date_updated' => date("Y-m-d H:i:s"),
        'historyId'    => $thread_id,
      );
      $wpdb->update($wpdb->prefix.'wpsc_ticket',$values,array('id'=>$ticket_id));
      
      do_action('wpsc_after_submit_thread_request',$args,$thread_id);

      return $thread_id;
    }
    
    // Submit ticket thread
    function submit_cloned_ticket_thread($args){
      global $wpdb;
      $thread_id = wp_insert_post(
      	array(
      		'post_type'    => 'wpsc_ticket_thread',
      		'post_content' => $args['reply_body'],
      		'post_status'  => 'publish',
          'post_date'    => $args['create_time'],
      	)
      );
      add_post_meta( $thread_id, 'ticket_id', $args['ticket_id'] );
      add_post_meta( $thread_id, 'thread_type', $args['thread_type'] );
      $customer_name =  isset($args['customer_name']) ? $args['customer_name'] : '';
      add_post_meta( $thread_id, 'customer_name', $customer_name );
      $customer_email =  isset($args['customer_email']) ? $args['customer_email'] : '';
      add_post_meta( $thread_id, 'customer_email', $customer_email );
      $attachments =  isset($args['attachments']) ? $args['attachments'] : array();
      add_post_meta( $thread_id, 'attachments', $attachments );
      
      if(isset($args['reply_source'])){
        add_post_meta( $thread_id, 'reply_source', $args['reply_source'] );
      }
      
      if(isset($args['ip_address'])){
        add_post_meta( $thread_id, 'ip_address', $args['ip_address'] );
      }
      
      if(isset($args['os'])){
        add_post_meta($thread_id,'os',$args['os']);
      }
      
      if(isset($args['browser'])){
        add_post_meta($thread_id,'browser',$args['browser']);
      }
      
      $ticket_id =  isset($args['ticket_id']) ? $args['ticket_id'] : 0;
      $values= array(
        'date_updated' => date("Y-m-d H:i:s"),
        'historyId'    => $thread_id,
      );
      $wpdb->update($wpdb->prefix.'wpsc_ticket',$values,array('id'=>$ticket_id));
      return $thread_id;
    }
    
    // Replace macros
    function replace_macro($str,$ticket_id){
      include WPSC_ABSPATH . 'includes/functions/replace_macro.php';
      return $str;
    }
    
    // Get user agent id
    function get_current_user_agent_id(){
      global $current_user;
      $agents = get_terms([
    		'taxonomy'   => 'wpsc_agents',
    		'hide_empty' => false,
    		'meta_query' => array(
    			'relation' => 'AND',
    			array(
    				'key'       => 'user_id',
    				'value'     => $current_user->ID,
    				'compare'   => '='
    			)
    		),
    	]);
      if($agents){
        return $agents[0]->term_id;
      } else {
        return 0;
      }
    }
    
    // Check agent permission for wpsc functionality
    function agent_has_permission( $permission, $ticket_id=0 ){
      global $current_user,$wpscfunction;
      $current_agent_id = $this->get_current_user_agent_id();
      if(!$current_agent_id) return false;
      if(!$current_user->has_cap('wpsc_agent')) return false;
      $role_id = get_user_option( 'wpsc_agent_role', $current_user->ID );
      $agent_roles = get_option('wpsc_agent_role');
      $permissions = $agent_roles[$role_id];
      $response       = false;
      if ($ticket_id) {
        $assigned_agents = $wpscfunction->get_ticket_meta($ticket_id,'assigned_agent');
        switch ($permission) {
          case 'view_unassigned':
          case 'assign_unassigned':
          case 'reply_unassigned':
          case 'cng_tkt_sts_unassigned':
          case 'cng_tkt_field_unassigned':
          case 'cng_tkt_ao_unassigned':
          case 'cng_tkt_rb_unassigned':
          case 'delete_unassigned':
          case 'view_unassigned_private_note':
          case 'view_unassigned_log':
          case 'edit_delete_unassigned':
            $response = ($permissions[$permission] && in_array(0,$assigned_agents)) ? true : false;
            break;
            
          case 'view_assigned_me':
          case 'assign_assigned_me':
          case 'reply_assigned_me':
          case 'cng_tkt_sts_assigned_me':
          case 'cng_tkt_field_assigned_me':
          case 'cng_tkt_ao_assigned_me':
          case 'cng_tkt_rb_assigned_me':
          case 'delete_assigned_me':
          case 'view_assigned_me_private_note':
          case 'view_assigned_me_log':
          case 'edit_delete_assigned_me':
            $response = ($permissions[$permission] && in_array($current_agent_id,$assigned_agents)) ? true : false;
            break;
            
          case 'view_assigned_others':
          case 'assign_assigned_others':
          case 'reply_assigned_others':
          case 'cng_tkt_sts_assigned_others':
          case 'cng_tkt_field_assigned_others':
          case 'cng_tkt_ao_assigned_others':
          case 'cng_tkt_rb_assigned_others':
          case 'delete_assigned_others':
          case 'view_assigned_others_private_note':
          case 'view_assigned_others_log':
          case 'edit_delete_assigned_others':
            $response = ($permissions[$permission] && !in_array($current_agent_id,$assigned_agents) && !in_array(0,$assigned_agents)) ? true : false;
            break;
        }
      } else {
        $response = ($permissions[$permission]) ? true : false;
      }
      return apply_filters( 'wpsc_agent_has_permission', $response, $permission, $ticket_id  );
    }
    
    // User permissions
    function has_permission( $permission, $ticket_id=0 ){

      global $wpscfunction, $current_user;
      
      if(!$current_user->ID) return false;
      
      if(is_super_admin($current_user->ID) && is_multisite()) return true;
      
      $ticket         = $wpscfunction->get_ticket($ticket_id);
      $customer_email = $ticket_id ? strtolower($ticket['customer_email']) : '';
      $wpsc_ticket_public_mode = get_option('wpsc_ticket_public_mode');
      $response       = false;
      switch ($permission) {
        case 'view_ticket':
          $response = $customer_email == strtolower($current_user->user_email) || $this->agent_has_permission( 'view_unassigned', $ticket_id ) || $this->agent_has_permission( 'view_assigned_me', $ticket_id ) || $this->agent_has_permission( 'view_assigned_others', $ticket_id ) ? true : false;
          break;
          
        case 'assign_agent':
          $response = $this->agent_has_permission( 'assign_unassigned', $ticket_id ) || $this->agent_has_permission( 'assign_assigned_me', $ticket_id ) || $this->agent_has_permission( 'assign_assigned_others', $ticket_id ) ? true : false;
          break;
          
        case 'reply_ticket':
          $response = $customer_email == strtolower($current_user->user_email) || $this->agent_has_permission( 'reply_unassigned', $ticket_id ) || $this->agent_has_permission( 'reply_assigned_me', $ticket_id ) || $this->agent_has_permission( 'reply_assigned_others', $ticket_id ) || (!$current_user->has_cap('wpsc_agent') && $wpsc_ticket_public_mode) ? true : false;
          break;
					
        case 'add_note':
          $response = $this->agent_has_permission( 'view_unassigned_private_note', $ticket_id ) || $this->agent_has_permission( 'view_assigned_me_private_note', $ticket_id ) || $this->agent_has_permission( 'view_assigned_others_private_note', $ticket_id ) ? true : false;
          break;
          
        case 'change_status':
          $response = $this->agent_has_permission( 'cng_tkt_sts_unassigned', $ticket_id ) || $this->agent_has_permission( 'cng_tkt_sts_assigned_me', $ticket_id ) || $this->agent_has_permission( 'cng_tkt_sts_assigned_others', $ticket_id ) ? true : false;
          break;
          
        case 'change_ticket_fields':
          $response = $this->agent_has_permission( 'cng_tkt_field_unassigned', $ticket_id ) || $this->agent_has_permission( 'cng_tkt_field_assigned_me', $ticket_id ) || $this->agent_has_permission( 'cng_tkt_field_assigned_others', $ticket_id ) ? true : false;
          break;
          
        case 'change_agentonly_fields':
          $response = $this->agent_has_permission( 'cng_tkt_ao_unassigned', $ticket_id ) || $this->agent_has_permission( 'cng_tkt_ao_assigned_me', $ticket_id ) || $this->agent_has_permission( 'cng_tkt_ao_assigned_others', $ticket_id ) ? true : false;
          break;
          
        case 'change_raised_by':
          $response = $this->agent_has_permission( 'cng_tkt_rb_unassigned', $ticket_id ) ||  $this->agent_has_permission( 'cng_tkt_rb_assigned_me', $ticket_id ) || $this->agent_has_permission( 'cng_tkt_rb_assigned_others', $ticket_id ) ? true : false;
          break;
          
        case 'delete_ticket':
          $response = $this->agent_has_permission( 'delete_unassigned', $ticket_id ) ||  $this->agent_has_permission( 'delete_assigned_me', $ticket_id ) || $this->agent_has_permission( 'delete_assigned_others', $ticket_id ) ? true : false;
          break;
        
        case 'view_note':
          $response = $this->agent_has_permission( 'view_unassigned_private_note', $ticket_id ) ||  $this->agent_has_permission( 'view_assigned_me_private_note', $ticket_id ) || $this->agent_has_permission( 'view_assigned_others_private_note', $ticket_id ) ? true : false;
          break;
        
        case 'view_log':
            $response = $this->agent_has_permission( 'view_unassigned_log', $ticket_id ) ||  $this->agent_has_permission( 'view_assigned_me_log', $ticket_id ) || $this->agent_has_permission( 'view_assigned_others_log', $ticket_id ) ? true : false;
            break;  
            
        case 'edit_delete_ticket' :
          $response = $this->agent_has_permission('edit_delete_unassigned' ,$ticket_id) || $this->agent_has_permission('edit_delete_assigned_me', $ticket_id) || $this->agent_has_permission('edit_delete_assigned_others', $ticket_id) ? true : false;  
          break;
      }
      return apply_filters( 'wpsc_has_permission', $response, $ticket_id, $permission );
    }
    
    // Random string
    function getRandomString($length = 8) {
      $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
      $string = '';
      for ($i = 0; $i < $length; $i++) {
        $string .= $characters[mt_rand(0, strlen($characters) - 1)];
      }
      return $string;
    }
    
    // Email Notification types
    function get_email_notification_types(){
      $notification_types = array(
        'new_ticket'       => __('New Ticket','supportcandy'),
        'ticket_reply'     => __('Ticket Reply','supportcandy'),
        'change_status'    => __('Change Ticket Status','supportcandy'),
        'assign_agent'     => __('Assign Agent','supportcandy'),
        'delete_ticket'    => __('Delete Ticket','supportcandy'),
        'private_note'     => __('Private Note','supportcandy'),
        'change_category'  => __('Change Ticket Category','supportcandy'),
        'change_priority'  => __('Change Ticket Priority','supportcandy')
      );
      return apply_filters('wpsc_en_types',$notification_types);
    }
    
    // Get Field value by value id
    function get_field_val_by_field_id($field_id,$val){
      include WPSC_ABSPATH . 'includes/functions/get_field_val_by_valId.php';
      return $val;
    }
    
    // get condition field options
    function get_condition_field_options(){
      include WPSC_ABSPATH . 'includes/functions/get_condition_field_options.php';
      return $fields;
    }
    
    // check conditions
    function check_ticket_conditions($conditions,$ticket_id){
      include WPSC_ABSPATH . 'includes/functions/check_ticket_conditions.php';
      return $flag;
    }
    
    // show ad banner on dashboard interface if no add-on is installed.
    function display_ad_banner(){
      include WPSC_ABSPATH . 'includes/functions/display_ad_banner.php';
    }
    
    function get_ticket($ticket_id){
      global $wpdb;
      $ticket_data = array();
      $ticket = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}wpsc_ticket WHERE id='$ticket_id' ");
      if( $ticket ){
        $ticket_data = json_decode(json_encode($ticket), true);
      }
      return $ticket_data;
    }
    
    function get_ticket_fields($ticket_id,$select_field){
      
      global $wpdb;
      $ticket_field_value = '';
      if (apply_filters('wpsc_get_select_field',true,$ticket_id,$select_field)) {
        $get_ticket_field_value = $wpdb->get_var(" SELECT $select_field FROM {$wpdb->prefix}wpsc_ticket WHERE id='$ticket_id' ");
        $ticket_field_value = $get_ticket_field_value ? $get_ticket_field_value : '';
      }
      return stripslashes($ticket_field_value);
    }
    
    /**
     * Get ticket meta
     */
    function get_ticket_meta( $ticket_id, $meta_key, $flag = false ){
      
      global $wpdb;
      if($flag){
        
        $get_meta = $wpdb->get_var("SELECT meta_value FROM {$wpdb->prefix}wpsc_ticketmeta WHERE ticket_id= '$ticket_id' AND meta_key = '$meta_key' ");
        $ticket_meta = stripslashes($get_meta) ? stripslashes($get_meta) : '';
        
      } else {
        
        $ticket_meta = array();
        $results = $wpdb->get_results("SELECT meta_value FROM {$wpdb->prefix}wpsc_ticketmeta WHERE ticket_id= '$ticket_id' AND meta_key = '$meta_key'");
        if( $results ){
          foreach ($results as $result) {
            $ticket_meta[]= stripslashes($result->meta_value);
          }
        }
      }
      return $ticket_meta;
    }
    
    /**
     * Get assigned agents. If not, return empty array
     */
    function get_assigned_agents($ticket_id){
      $assigned_agents = $this->get_ticket_meta($ticket_id,'assigned_agent');
      if ( isset($assigned_agents[0]) && $assigned_agents[0] == 0 ) {
        $assigned_agents = array();
      }
      return $assigned_agents;
    }
    
    /**
     * Get status of ticket whether it is active or deleted
     */
    function get_ticket_status($ticket_id){
      global $wpdb;
      $ticket_status = $wpdb->get_var("SELECT active FROM {$wpdb->prefix}wpsc_ticket WHERE id='$ticket_id' ");
      return  intval($ticket_status);
    }
    
    /**
     * Adds ticketmeta for ticket
     */
    function add_ticket_meta($ticket_id ,$meta_key ,$meta_value){
      global $wpdb;
      $wpdb->insert( 
        $wpdb->prefix . 'wpsc_ticketmeta', 
        array(
          'ticket_id' => $ticket_id,
          'meta_key' => $meta_key,
          'meta_value' => $meta_value
      ));
    }
    
    /**
     * Update ticket meta for ticket
     */
    function update_ticket_meta($ticket_id ,$meta_key ,$meta_value){
      global $wpdb;
      $wpdb->update($wpdb->prefix.'wpsc_ticketmeta', $meta_value, array('ticket_id'=>$ticket_id,'meta_key' => $meta_key));      
    }
    
    function delete_ticket_meta($ticket_id  ,$meta_key){
      global $wpdb;
      $wpdb->delete( $wpdb->prefix.'wpsc_ticketmeta', array( 'ticket_id' => $ticket_id,'meta_key'=>$meta_key) );
    }
    
    /**
     * Get SQL Query depending on paramenters passed for ticket list
     */
    public function get_sql_query( $select_str , $meta_query, $search = false, $orderby = false, $order = false, $no_of_tickets = false, $current_page = false ){
      include WPSC_ABSPATH . 'includes/functions/get_sql_query.php';
      return $sql;
    }
    
    /**
     * Get all field array which saved in ticketmeta table
     */
    
     function get_all_meta_keys(){
      
      $c_fields = get_terms([
        'taxonomy'   => 'wpsc_ticket_custom_fields',
        'hide_empty' => false,
        'orderby'    => 'meta_value_num',
        'meta_key'	 => 'wpsc_tf_load_order',
        'order'    	 => 'ASC',
        'meta_query' => array(
          array(
            'key'       => 'agentonly',
            'value'     => array(0,1),
            'compare'   => 'IN'
            )
        ),
      ]);
      
      $meta_key = array();
      
      if( $c_fields ) {
        
        foreach ($c_fields as $field) {
          
          $wpsc_tf_type = get_term_meta( $field->term_id, 'wpsc_tf_type',true);
          
          if(!$wpsc_tf_type) continue;
          
          $meta_key[] = $field->slug; 
          
        }
        
      }
      
      $meta_key[] = 'assigned_agent';
      $meta_key[] = 'date_closed';
      return apply_filters( 'wpsc_get_all_meta_keys', $meta_key );
      
    }
    
    /**
     * Create a ticket
     */
    public function create_new_ticket($values){
      global $wpdb;
      $wpdb->insert($wpdb->prefix . 'wpsc_ticket', $values);
      $ticket_id = $wpdb->insert_id;
      return $ticket_id;
    }
    
    /**
     * Get status term ids
     */
    function get_statuses(){
      $status_ids = array();
      $statuses = get_terms([
        'taxonomy'   => 'wpsc_statuses',
        'hide_empty' => false,
        'orderby'    => 'meta_value_num',
        'order'    	 => 'ASC',
        'meta_query' => array('order_clause' => array('key' => 'wpsc_status_load_order')),
      ]);
      foreach ($statuses as $status) {
        $status_ids[] = $status->term_id;
      }
      return $status_ids;
    }
    
    /**
     * Get priority term ids
     */
    function get_priorities(){
      $priority_ids = array();
      $priorities = get_terms([
        'taxonomy'   => 'wpsc_priorities',
        'hide_empty' => false,
        'orderby'    => 'meta_value_num',
        'order'    	 => 'ASC',
        'meta_query' => array('order_clause' => array('key' => 'wpsc_priority_load_order')),
      ]);
      foreach ($priorities as $priority) {
        $priority_ids[] = $priority->term_id;
      }
      return $priority_ids;
    }
    
    /**
     * Get category term ids
     */
    function get_categories(){
      $category_ids = array();
      $categories = get_terms([
        'taxonomy'   => 'wpsc_categories',
        'hide_empty' => false,
        'orderby'    => 'meta_value_num',
        'order'    	 => 'ASC',
        'meta_query' => array('order_clause' => array('key' => 'wpsc_category_load_order')),
      ]);
      foreach ($categories as $category) {
        $category_ids[] = $category->term_id;
      }
      return $category_ids;
    }
    
    /**
     * Get Unresolved Label COUNT
     */
    
    function get_label_count_unresolved(){
  
      global $wpdb, $current_user, $wpscfunction;
      
      $label_counts = array();
      
      // Get user meta history
      $label_count_history      = get_option( 'wpsc_label_count_history' );
      $label_count_last_history = get_user_meta( $current_user->ID, 'wpsc_'.get_current_blog_id().'_label_count_last_history', true );
      
      if( $label_count_history && $label_count_history == $label_count_last_history ){
        $label_counts  = get_user_meta( $current_user->ID, 'wpsc_'.get_current_blog_id().'_label_counts', true );
        $filter_count_flag = false;
      }
      
      $all_labels = $this->get_ticket_filter_labels();
      
      // Initialize meta query
      $meta_query = array(
        'relation' => 'AND',
      );
      
      // Initialie restrictions. Everyone should able to see their own tickets.
      $restrict_rules = array(
        'relation' => 'OR',
        array(
          'key'            => 'customer_email',
          'value'          => $current_user->user_email,
          'compare'        => '='
        ),
      );
      
      if ($current_user->has_cap('wpsc_agent')) {
      
        $agent_permissions = $this->get_current_agent_permissions();
      
        $agents = get_terms([
          'taxonomy'   => 'wpsc_agents',
          'hide_empty' => false,
          'meta_query' => array(
            'relation' => 'AND',
            array(
              'key'       => 'user_id',
              'value'     => $current_user->ID,
              'compare'   => '='
            )
          ),
        ]);
      
        if(!$agents) die();
        $current_agent = $agents[0];
      
        if ($agent_permissions['view_unassigned']) {
          $restrict_rules[] = array(
            'key'            => 'assigned_agent',
            'value'          => 0,
            'compare'        => '='
          );
        }
      
        if ($agent_permissions['view_assigned_me']) {
          $restrict_rules[] = array(
            'key'            => 'assigned_agent',
            'value'          => $current_agent->term_id,
            'compare'        => '='
          );
        }
      
        if ($agent_permissions['view_assigned_others']) {
          $restrict_rules[] = array(
            'key'            => 'assigned_agent',
            'value'          => array(0,$current_agent->term_id),
            'compare'        => 'NOT IN'
          );
        }
      
        $restrict_rules = apply_filters('wpsc_tl_agent_restrict_rules',$restrict_rules);
      
      } else {
        $restrict_rules = apply_filters('wpsc_tl_customer_restrict_rules',$restrict_rules);
      }
      
      $meta_query[] = $restrict_rules;
      
      $labels = array();
      
      foreach ($all_labels as $key => $label) {
      
        if( $label['has_badge'] ){
      
          switch ($key) {
      
            case 'unresolved_agent':
 							$unresolved_agent  = get_option('wpsc_tl_agent_unresolve_statuses');
 							$unresolved_agent_rules = array(
 								'relation' => 'OR',
 							);
 							if($unresolved_agent){
 								$unresolved_agent_rules[] = array(
 									'key'            => 'ticket_status',
 									'value'          => $unresolved_agent,
 									'compare'        => 'IN'
 								);
 							}
 							$meta_query[] = apply_filters('wpsc_unresolved_agent_label_count', $unresolved_agent_rules, $key);
 							break;
      
            default:
            $meta_query = apply_filters('wpsc_filter_after_label_default', $meta_query, $key );
            break;
      
          }
      
          $sql          = $wpscfunction->get_sql_query( 'COUNT(DISTINCT t.id)', $meta_query );
					$ticket_count = $wpdb->get_var($sql);	
					$labels[$key] = $ticket_count;
        }
      }
      
      if(!$label_count_history){
        $label_count_history = 1;
        update_option( 'wpsc_label_count_history', $label_count_history );
      }
      
      update_user_meta( $current_user->ID, 'wpsc_'.get_current_blog_id().'_label_count_last_history', $label_count_history );
      update_user_meta( $current_user->ID, 'wpsc_'.get_current_blog_id().'_label_counts', $labels );
      
      $label_counts = $labels;
      		
    }
    
    /**
     * Check string
     */
    public function check_str_is_non_english($string){
      $flag = true;
      if(preg_match('/[^\x00-\x7F]+/' ,$string)){
        $flag = false;
      }
      return $flag;
    }
    
    /**
     * Load conditions UI. Will be used to show conditions in setting.
     * @param  [String] $id         (required) CSS id for the element. It must be ubique for each element.
     * @param  array  $conditions   (optional) Existing conditions to load
     * @return [type]               Void
     */
    public function load_conditions_ui( $id, $conditions = '' ){
      include WPSC_ABSPATH . 'includes/functions/conditions/load_conditions_ui.php';
    }
    
    /**
     * Print options html for a condition options for a given key
     * @param  Mixed  $key key for getting options. 
     * @return [type] Void
     */
    public function get_condition_options($key){
      include WPSC_ABSPATH . 'includes/functions/conditions/get_condition_options.php';
      return $options;
    }
    
    /**
     * Check whether given category available or not
     * @return boolean
     */
    public function is_category( $cat_id ){
      $term = get_term_by( 'id', $cat_id, 'wpsc_categories' );
      return $term ? true : false;
    }
    
    /**
     * Check whether given priority available or not
     * @return boolean
     */
    public function is_priority( $priority_id ){
      $term = get_term_by( 'id', $priority_id, 'wpsc_priorities' );
      return $term ? true : false;
    }
    
    /**
     * Check whether given status available or not
     * @return boolean
     */
    public function is_status( $status_id ){
      $term = get_term_by( 'id', $status_id, 'wpsc_statuses' );
      return $term ? true : false;
    }
    
    /**
     * Check whether given custom field option available or not
     * @return boolean
     */
    public function is_cf_option( $field_id, $option ){
      $options = get_term_meta( $field_id, 'wpsc_tf_options', true);
      $options = is_array($options) ? $options : array();
      $options = isset($options[0]) && $options[0] ? $options : array();
      return in_array($option,$options) ? true : false;
    }
    
    /**
     * Check whether given slug is custom field slug
     */
    public function is_cf_slug($slug){
      $term = get_term_by( 'slug', $slug, 'wpsc_ticket_custom_fields' );
      return $term ? true : false;
    }
    
    /**
     * Check whether given agent is present or not
     */
    public function is_agent($agents){
      if(!$agents) return false;
      foreach ($agents as $agent) {
        $term = get_term_by( 'id' , $agent , 'wpsc_agents' );
        if(!$term){
          return false;
        }
      }
      return true;
    }
    
    /**
     * Check whether given ticket is present and active  or not 
     */
    public function is_ticket($ticket_id){
      global $wpdb;
      
      $ticket_exists = $wpdb->get_var("SELECT IF( EXISTS( SELECT * FROM {$wpdb->prefix}wpsc_ticket WHERE id = $ticket_id AND active = 1), 1, 0)");
      return $ticket_exists ? true : false;
    }
    
    /**
    * Add extra users for ticket  
    * @param [type] $ticket_id  
    * @param [type] $extra_ticket_users 
    */
   function add_extra_users($ticket_id,$extra_ticket_users){
     global $wpscfunction,$wpdb;
     
     $prev_users = $wpscfunction->get_ticket_meta($ticket_id,'extra_ticket_users');
     $wpscfunction->delete_ticket_meta($ticket_id,'extra_ticket_users');
     if($extra_ticket_users){
       foreach($extra_ticket_users as $user){
          $wpscfunction->add_ticket_meta($ticket_id,'extra_ticket_users',$user);
       }
     }
     do_action('wpsc_set_add_extra_users', $ticket_id, $extra_ticket_users, $prev_users);
   }
   
   function get_os(){
     $user_agent = $_SERVER['HTTP_USER_AGENT'];
     $os_platform =   "Not Found";
     $os_array =   array(
        '/windows nt 10/i'      =>  'Windows 10',
        '/windows nt 6.3/i'     =>  'Windows 8.1',
        '/windows nt 6.2/i'     =>  'Windows 8',
        '/windows nt 6.1/i'     =>  'Windows 7',
        '/windows nt 6.0/i'     =>  'Windows Vista',
        '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
        '/windows nt 5.1/i'     =>  'Windows XP',
        '/windows xp/i'         =>  'Windows XP',
        '/windows nt 5.0/i'     =>  'Windows 2000',
        '/windows me/i'         =>  'Windows ME',
        '/win98/i'              =>  'Windows 98',
        '/win95/i'              =>  'Windows 95',
        '/win16/i'              =>  'Windows 3.11',
        '/macintosh|mac os x/i' =>  'Mac OS X',
        '/mac_powerpc/i'        =>  'Mac OS 9',
        '/linux/i'              =>  'Linux',
        '/ubuntu/i'             =>  'Ubuntu',
        '/iphone/i'             =>  'iPhone',
        '/ipod/i'               =>  'iPod',
        '/ipad/i'               =>  'iPad',
        '/android/i'            =>  'Android',
        '/blackberry/i'         =>  'BlackBerry',
        '/webos/i'              =>  'Mobile'
      );
      foreach ( $os_array as $regex => $value ) { 
        if ( preg_match($regex, $user_agent ) ) {
          $os_platform = $value;
        }
      }
     
     return $os_platform;
   }
   
   function get_browser(){
     $user_agent = $_SERVER['HTTP_USER_AGENT'];
     $browser        = "Not Found";
     $browser_array  = array(
        '/msie/i'       =>  'Internet Explorer',
        '/firefox/i'    =>  'Mozilla Firefox',
        '/safari/i'     =>  'Safari',
        '/chrome/i'     =>  'Google Chrome',
        '/edge/i'       =>  'Edge',
        '/opera/i'      =>  'Opera',
        '/netscape/i'   =>  'Netscape',
        '/maxthon/i'    =>  'Maxthon',
        '/konqueror/i'  =>  'Konqueror',
        '/SamsungBrowser/' => 'Samsung Browser'
      );
      foreach ( $browser_array as $regex => $value ) {

        if ( preg_match( $regex, $user_agent ) ) {
          $browser = $value;
        }
      }
      return $browser;
   }
   
   //Ticket all threads 
   function get_ticket_history_all($ticket_id){
     include WPSC_ABSPATH . 'includes/functions/get_ticket_history_all.php';
     return $ticket_history_all;
   }
   
   // All ticket threads with note
   function get_ticket_history_all_with_notes($ticket_id){
     include WPSC_ABSPATH . 'includes/functions/get_ticket_history_all_with_notes.php';
     return $ticket_history_all_with_notes;
   }
   
   // ticket all notes
   function ticket_notes_history($ticket_id){
     include WPSC_ABSPATH . 'includes/functions/get_ticket_notes_history.php';
     return $ticket_notes_history;
   }

   function get_previously_assigned_agents_names($ticket_id){
    global $wpscfunction;
    $prev_assigned_agents = $wpscfunction->get_ticket_meta($ticket_id, 'prev_assigned_agent'); 
    $agent_names = array();
    foreach ($prev_assigned_agents as $agent_id) {
      $agent_names[] = $this->get_agent_name($agent_id);
    }
    return implode(', ', $agent_names);
  }

  function get_previously_assigned_agents($ticket_id){
    global $wpscfunction;
    $prev_assigned_agents = $wpscfunction->get_ticket_meta($ticket_id,'prev_assigned_agent');
    $agent_emails = array();
    foreach ($prev_assigned_agents as $agent_id) {
      $user_id = get_term_meta($agent_id,'user_id',true);
      if($user_id){
        $user = get_user_by('id',$user_id);
        $agent_emails[] = $user->user_email;
      }
    }
    return apply_filters( 'wpsc_get_prev_assigned_agent_emails',$agent_emails, $prev_assigned_agents, $ticket_id);
   }

  function rich_editing_status($user){
    $status = get_user_meta($user->ID,'rich_editing',true);
    return filter_var($status, FILTER_VALIDATE_BOOLEAN); 
  }
  
  function check_rtl(){
    $rtl_locale = array('ar','ary','ckb','haz','he_IL','fa_IR','ps','ur','ug_CN');
    if(in_array(get_locale(),$rtl_locale)){
      return 'rtl';
    }else{
      return 'ltr';
    } 
  }

  function show_cron_status() {

		$status = $this->test_cron_spawn();

		if ( is_wp_error( $status ) ) {
			if ( 'crontrol_info' === $status->get_error_code() ) {
				?>
				<div id="cron-status-notice" class="notice notice-info">
					<p style="margin:0.5em;"><?php echo esc_html( $status->get_error_message() ); ?></p>
				</div>
				<?php
			} else {
				?>
				<div id="cron-status-error" class="error">
					<p style="margin:0.5em;">
						<?php
						printf(
							/* translators: 1: Error message text. */
							esc_html__( 'There was a problem spawning a call to the WP-Cron system on your site. This means WP-Cron events on your site may not work. The problem was: %s', 'supportcandy' ),
							'<br><strong>' . esc_html( $status->get_error_message() ) . '</strong>'
						);
						?>
					</p>
				</div>
				<?php
			}
		}

  }
  
  function test_cron_spawn( $cache = false ) {
		global $wp_version;

		if ( defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON ) {
			/* translators: 1: The name of the PHP constant that is set. */
			return new WP_Error( 'crontrol_info', sprintf( __( 'The %s constant is set to true. WP-Cron spawning is disabled.', 'supportcandy' ), 'DISABLE_WP_CRON' ) );
		}

		if ( defined( 'ALTERNATE_WP_CRON' ) && ALTERNATE_WP_CRON ) {
			/* translators: 1: The name of the PHP constant that is set. */
			return new WP_Error( 'crontrol_info', sprintf( __( 'The %s constant is set to true.', 'supportcandy' ), 'ALTERNATE_WP_CRON' ) );
		}

		$cached_status = get_transient( 'wp-cron-test-ok' );

		if ( $cache && $cached_status ) {
			return true;
		}

		$sslverify     = version_compare( $wp_version, 4.0, '<' );
		$doing_wp_cron = sprintf( '%.22F', microtime( true ) );

		$cron_request = apply_filters( 'cron_request', array(
			'url'  => site_url( 'wp-cron.php?doing_wp_cron=' . $doing_wp_cron ),
			'key'  => $doing_wp_cron,
			'args' => array(
				'timeout'   => 3,
				'blocking'  => true,
				'sslverify' => apply_filters( 'https_local_ssl_verify', $sslverify ),
			),
		) );

		$cron_request['args']['blocking'] = true;

		$result = wp_remote_post( $cron_request['url'], $cron_request['args'] );

		if ( is_wp_error( $result ) ) {
			return $result;
		} elseif ( wp_remote_retrieve_response_code( $result ) >= 300 ) {
			return new WP_Error( 'unexpected_http_response_code', sprintf(
				/* translators: 1: The HTTP response code. */
				__( 'Unexpected HTTP response code: %s', 'supportcandy' ),
				intval( wp_remote_retrieve_response_code( $result ) )
			) );
		} else {
			set_transient( 'wp-cron-test-ok', 1, 3600 );
			return true;
		}

	}
   
  //All ticket history with log threads 
  function get_ticket_history_all_with_logs($ticket_id){
    include WPSC_ABSPATH . 'includes/functions/get_ticket_history_all_with_logs.php';
    return $ticket_history_all_with_logs;
  }

  //All ticket history with notes and log threads 
  function get_ticket_history_all_with_notes_and_logs($ticket_id){
    include WPSC_ABSPATH . 'includes/functions/get_ticket_history_all_with_notes_and_logs.php';
    return $ticket_history_all_with_notes_and_logs;
  }

  //Ticket history notes with log threads 
  function get_ticket_notes_history_with_logs($ticket_id){
    include WPSC_ABSPATH . 'includes/functions/get_ticket_notes_history_with_logs.php';
    return $ticket_notes_history_with_logs;
  }
}  
endif;

$GLOBALS['wpscfunction'] =  new WPSC_Functions();