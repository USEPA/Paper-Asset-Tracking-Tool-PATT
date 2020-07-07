<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

global $wpscfunction, $current_user, $wpdb;

/**
 * Exit if logged in user do not have administrator capabilities
 */
if( !$current_user->has_cap('manage_options') ) exit();

$startdate = $wpdb->get_var("SELECT DATE(date_created) FROM {$wpdb->prefix}wpsc_ticket ORDER BY date_created LIMIT 1");
$s_date = strtotime($startdate);
$today = date("Y-m-d", strtotime("today"));
$t_date = strtotime($today);
 
if( $s_date == $t_date  || !$startdate){
  update_option( 'wpsc_rp_db_version', '2.0' );
  $response = array(
    'completed' => 100, 
    'is_next'   => 0,
   );
  
  echo json_encode($response);
  exit;
}

$date      = isset($_POST['date']) ? intval($_POST['date']) : $startdate;

$checked_date = get_option('wpsc_rp_db_checked_date');
/**
 * Begin importing ticket table entries
 */

if(!$checked_date){
  $checked_date = $startdate;
}

for ($i=0; $i <5 ; $i++) {
  $result_date = date('Y-m-d', strtotime($checked_date. '+'.$i.' days'));
  $lastdate     = date("Y-m-d", strtotime("yesterday"));
  $check_date   = strtotime($result_date);
  $last_date    = strtotime($lastdate);
  if($check_date <= $last_date){
    $is_imported = $wpdb->get_results("SELECT result_date FROM {$wpdb->prefix}wpsc_reports WHERE  result_date ='".$result_date."'");
    if(!$is_imported) :
      $count = $wpdb->get_var("SELECT DISTINCT COUNT(id) from {$wpdb->prefix}wpsc_ticket where DATE(date_created)='".$result_date."'");
      
      $wpdb->insert( $wpdb->prefix . 'wpsc_reports', 
        array(
          'report_type'  => 'no_of_tickets',
          'type'         => 'daily',
          'ticket_count' => $count,
          'result_date'  => $result_date
        ));
       
      update_option('wpsc_ticket_stats_checked_date',$result_date);
      $response = array(); 
      $average  =  array();
      $tickets  = $wpdb->get_results("SELECT id FROM {$wpdb->prefix}wpsc_ticket WHERE DATE(date_created)= '".$result_date."'");

      if($tickets){
        foreach ($tickets as $ticket) {
          $wpscfunction->delete_ticket_meta($ticket->id,'frt_checked');
          $wpscfunction->add_ticket_meta($ticket->id, 'frt_checked',1);

          $wpscfunction->add_ticket_meta($ticket->id, 'ticket_counted',1);
          $wpscfunction->delete_ticket_meta($ticket->id,'first_response');
          
          $sla_term = (int) $wpscfunction->get_ticket_meta($ticket->id,'sla_term',true);
          if($sla_term){
            $sla = $wpscfunction->get_ticket_meta($ticket->id,'sla',true);
            $now  = new DateTime;
            $ago  = new DateTime($sla);
            $diff = $now->diff($ago);
            if($diff->invert){
              $wpscfunction->add_ticket_meta($ticket->id,'wpsc_overdue_ticket_checked',1); 
            }
          }
          
          $args_thread = array(
            'post_type'      => 'wpsc_ticket_thread',
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'ASC',
            'posts_per_page' => -1,
            'meta_query'     => array(
              'relation' => 'AND',
              array(
                'key'     => 'ticket_id',
                'value'   => $ticket->id,
                'compare' => '='
              ),
              array(
                'key'     => 'thread_type',
                'value'   => 'reply',
                'compare' => '='
              ),
            ),
          );
          $threads = get_posts($args_thread);
          foreach ($threads as $thread):
            $customer_email = get_post_meta( $thread->ID, 'customer_email', true);
            $user = get_user_by( 'email',$customer_email );
            
            if( $user && $user->has_cap('wpsc_agent') ){
              $post_created = $wpscfunction->get_ticket_fields($ticket->id,'date_created');
              $thread_created = $thread->post_date_gmt;
              $datetime1      = new DateTime($post_created);
              $datetime2      = new DateTime($thread_created);
              $diff           = $datetime2->diff($datetime1);
              $frt_response   = ($diff->d*24*60)+($diff->h*60)+($diff->i);
              $response[]     = $frt_response;
              $wpscfunction->add_ticket_meta($ticket->id, 'first_response',$frt_response);
              
              break;
            }
          endforeach;
        }  
      }
      
      if(!empty($response)){
        $average = round(array_sum($response) / count($response),2);  
      }else{
        $average= 0;
      }
      
      $wpdb->insert( $wpdb->prefix . 'wpsc_reports', 
        array(
          'report_type'  => 'first_response',
          'type'         => 'daily',
          'ticket_count' => $average,
          'result_date'  => $result_date
        ));
    
    endif;
    update_option('wpsc_rp_db_checked_date',$result_date);
  }
}

$last_checked_date = get_option('wpsc_rp_db_checked_date');

$prog = (strtotime($last_checked_date) - strtotime($startdate) ) / (strtotime($lastdate) - strtotime($startdate));

$lastdate   = date("Y-m-d", strtotime("yesterday"));
$check_date = strtotime($last_checked_date);
$last_date  = strtotime($lastdate);
$completed  = ceil($prog*100);
$is_next    = $check_date < $last_date ? 1 : 0;

if(!$is_next){
  update_option( 'wpsc_rp_db_version', '2.0' );
}

$response = array(
  'completed' => $completed, 
  'is_next'   => $is_next,
  'date'      => date('m-d-Y',strtotime($last_checked_date . "+1 days")),
);

echo json_encode($response);