<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}
 
global $wpscfunction,$current_user,$wpdb;

$installed_db_version = get_option( 'wpsc_rp_db_version', 1 );

if($installed_db_version < '2.0'){
  return;
}

$check_flag = false;
$time = get_option('wpsc_overdue_ticket_time');

if($time){
  $now = time();
  $ago = strtotime($time);
  $diff = $now - $ago;
  $diff_minutes = round( $diff / 60 );
  if($diff_minutes >= 10){
    $check_flag = true;
  }
}
 
if(!(!$time || $check_flag)){
	return;
}


$ticket_data = $wpdb->get_results("SELECT ticket_id  FROM {$wpdb->prefix}wpsc_ticketmeta  WHERE meta_key ='sla_term' AND meta_value != '0'");

foreach($ticket_data as $key => $ticket){
  
  $ticket_id = $ticket->ticket_id;

  $sla = $wpscfunction->get_ticket_meta($ticket_id,'sla',true);

  $now  = new DateTime;
  $ago  = new DateTime($sla);
  $diff = $now->diff($ago);
  
  $out_of_sla = $wpscfunction->get_ticket_meta($ticket_id,'wpsc_overdue_ticket_checked',true); 
  if( $diff->invert && !$out_of_sla){
    $date  = date('Y-m-d',strtotime("today"));
    $sla   = $wpdb->get_row("SELECT result_date, overdue_count FROM {$wpdb->prefix}wpsc_sla_reports WHERE result_date ='".$date."'");
    if($sla){
      if(!$sla->overdue_count){
        $count = 1;
      }else{
        $count = $sla->overdue_count;
        $count++;
      }
      $wpdb->update($wpdb->prefix.'wpsc_sla_reports',array('overdue_count' => $count),array('result_date'=>$date));
        
    }else{
        $count = 1;
        $wpdb->insert( $wpdb->prefix . 'wpsc_sla_reports', 
            array(
                'overdue_count' => $count,
                'result_date'   => $date
            ));	
    }

    $wpscfunction->add_ticket_meta($ticket_id,'wpsc_overdue_ticket_checked',1);

  }

  
}

update_option('wpsc_overdue_ticket_time', date("Y-m-d H:i:s"));	


