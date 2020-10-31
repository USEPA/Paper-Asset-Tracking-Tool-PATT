<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

global $wpdb,$wpscfunction;

$user = get_user_by('email', $email_address);

$sql  = "SELECT t.* from {$wpdb->prefix}wpsc_ticket t WHERE t.customer_email = '$email_address'";
$tickets = $wpdb->get_results($sql);
$ticket_list = json_decode(json_encode($tickets), true);

$fields = get_terms([
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

$export_ticket = array();
foreach ($ticket_list as $export_post) {
  $ticket_id          =  $export_post['id'];
  $ticket_subject     = $wpscfunction->get_ticket_fields($export_post['id'],'ticket_subject');
  
  $cust_field = array();
  foreach ($fields as $key => $field) {
    $personal_info = get_term_meta($field->term_id, 'wpsc_tf_personal_info', true);
    if($personal_info){
      $tf_type = get_term_meta( $field->term_id, 'wpsc_tf_type', true);
      $cvalue = '';
      if($tf_type==3){
        $cust_value = $wpscfunction->get_ticket_meta($export_post['id'] ,$field->slug);
        if(!empty($cust_value)){
          $cvalue = implode(',',$cust_value);
        }
      }elseif($tf_type==10){
        $cust_value = $wpscfunction->get_ticket_meta($export_post['id'] ,$field->slug);
        if(!empty($cust_value)){
          $files = array();
          foreach ($cust_value as $attachment) {
            $attach      = array();
            $attach_meta = get_term_meta($attachment);
          
            foreach ($attach_meta as $key => $value) {
              $attach[$key] = $value[0];
            }
            $files[] = $attach['filename'];
          }
          $cvalue = implode(', ',$files);
        }
      }else{
        $cvalue = $wpscfunction->get_ticket_meta($export_post['id'] ,$field->slug,true);
      }
      $cust_field[] = array(
         'name'  => $field->name,
         'value' => $cvalue ? $cvalue : '',
      );
    }
  }
  
  $ticket_info = array();
  $args = array(
    'post_type'      => 'wpsc_ticket_thread',
    'post_status'    => 'publish',
    'orderby'        => 'ID',
    'order'          => 'ASC',
    'posts_per_page' => -1,
    'meta_query'     => array(
      'relation' => 'AND',
      array(
        'key'     => 'ticket_id',
        'value'   => $ticket_id,
        'compare' => '='
      ),
      array(
        'key'     => 'thread_type',
        'value'   => 'report',
        'compare' => '='
      ),
    )
  );
  $threads = get_posts($args);
  foreach ($threads as $thread) {
    
    $ip_address = get_post_meta( $thread->ID, 'ip_address',  true );
    $ip_address = $ip_address ? $ip_address : __('Not Found','supportcandy');

    $os         = get_post_meta( $thread->ID, 'os' ,true );
    $os         = $os ? $os : __('Not Found','supportcandy');
     
    $browser    = get_post_meta( $thread->ID, 'browser' ,true );
    $browser    = $browser ? $browser : __('Not Found','supportcandy');

    $reply_source = get_post_meta( $thread->ID, 'reply_source', true );
    
    $reply_type = '';
    if ($reply_source == 'browser'){
    	$reply_type = "Browser"; 
    }
    $reply_type = $reply_type ? $reply_type : __('Not Found','supportcandy');
    
    $ticket_info = array(
      array(
        'name'  => __( 'IP Address', 'supportcandy' ),
        'value' => $ip_address,
      ),
      array(
        'name'  => __( 'Ticket Source', 'supportcandy' ),
        'value' => $reply_type,
      ),
      array(
        'name'  => __( 'Operating System', 'supportcandy' ),
        'value' => $os,
      ),
      array(
        'name'  => __( 'Browser', 'supportcandy' ),
        'value' => $browser,
      ),
    );  
  }
  
  $data_points = array(
    array(
      'name'  => __( 'Ticket ID', 'supportcandy' ),
      'value' => $ticket_id,
    ),
    array(
      'name'  => __( 'Email Address', 'supportcandy' ),
      'value' => $email_address,
    ),
    array(
      'name'  => __( 'Subject', 'supportcandy' ),
      'value' => $ticket_subject,
    ),
  );
  $data_points = array_merge($data_points,$cust_field);
  $data_points = array_merge($data_points,$ticket_info);
  $data_points = apply_filters( 'wpsc_privacy_tickets_information', $data_points, $export_post['id'] );
  
  $export_ticket[] = array(
    'group_id'    => 'wpsc_tickets',
    'group_label' => __( 'User Tickets', 'supportcandy' ),
    'item_id'     => "wpsc_ticket_id-{$export_post['id']}",
    'data'        => $data_points,
  );  
}
