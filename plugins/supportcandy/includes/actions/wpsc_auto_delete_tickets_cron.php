<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

global $wpscfunction,$wpdb;

$check_flag = false;
$last_check = get_option('wpsc_auto_delete_cron_last_check');

if($last_check){
  $now = time();
  $ago = strtotime($last_check);
  $diff = $now - $ago;
  $diff_minutes = round( $diff / 60 );

	if( $diff_minutes >= 60 ){
		$check_flag = true;
	}
}

if(!(!$last_check || $check_flag)){
	return;
} else {
  update_option('wpsc_auto_delete_cron_last_check',date("Y-m-d H:i:s"));
}

$wpsc_auto_delete_ticket_time = get_option('wpsc_auto_delete_ticket_time');
$auto_delete_ticket           = get_option('wpsc_auto_delete_ticket');

if(!$wpsc_auto_delete_ticket_time || !$auto_delete_ticket){
  return;
}

//Close tickets status
$wpsc_close_ticket_status = get_option('wpsc_close_ticket_status');

$ticket_data = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}wpsc_ticket WHERE ticket_status IN ($wpsc_close_ticket_status) AND active=1");

$wpsc_auto_delete_ticket_time_period_unit = get_option('wpsc_auto_delete_ticket_time_period_unit');
if($wpsc_auto_delete_ticket_time_period_unit == 'days'){
  foreach ($ticket_data as $key => $ticket) {
    $ticket_id    = $ticket->id;
    $now          = time();
    $date_updated = $ticket->date_updated;
    $upago        = strtotime($date_updated);
    $diff         = $now - $upago;
    $diff_days    = intval( $diff / (60 * 60 * 24));
    
  	if( $diff_days > $wpsc_auto_delete_ticket_time ){
      $values = array(
        'active' => '0'
      );
      $wpdb->update($wpdb->prefix.'wpsc_ticket',$values,array('id'=>$ticket_id));
    }
  }
}elseif ($wpsc_auto_delete_ticket_time_period_unit == 'months') {
  foreach ($ticket_data as $key => $ticket) {
    $ticket_id    = $ticket->id;
    $now          = new DateTime();
    $date_updated = new DateTime($ticket->date_updated);
  	$diff         = $now->diff($date_updated);
    $months       = ($diff->y*12) + $diff->m;
    
    if( $months > $wpsc_auto_delete_ticket_time ){
      $values = array(
        'active' => '0'
      );
      $wpdb->update($wpdb->prefix.'wpsc_ticket',$values,array('id'=>$ticket_id));
    }
  }  
}elseif ($wpsc_auto_delete_ticket_time_period_unit=='years') {
  foreach ($ticket_data as $key => $ticket) {
    $ticket_id    = $ticket->id;
    $now          = new DateTime();
    $date_updated = new DateTime($ticket->date_updated);
    $diff         = $now->diff($date_updated);
    $years        = $diff->y;
  
  	if( $years > $wpsc_auto_delete_ticket_time ){
      $values = array(
        'active' => '0'
      );
      $wpdb->update($wpdb->prefix.'wpsc_ticket',$values,array('id'=>$ticket_id));
    }
  }
}
