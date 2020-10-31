<?php 
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

global $wpscfunction,$wpdb,$wpscfunc;
/*
* Line Graph
*/
$startdate=date('Y-m-d',strtotime( "today -30 days" ));
$rlabel   = array();
$values   = array();
$average  = array();

for($i=0;$i<30;$i++){
  $date = date('Y-m-d', strtotime($startdate. '+'.$i.' days'));
  $rlabel[] = "'".$date."'";
  
  $frt  = $wpdb->get_var("SELECT ticket_count FROM {$wpdb->prefix}wpsc_reports WHERE result_date= '".$date."' AND report_type = 'first_response'");
  
  if(!empty($frt)){
    $average[] = round($frt/60,2);  
  }else{
    $average[] = 0;
  }
  
}
$today = date('Y-m-d');
$rlabel[] = "'".$today."'";
$todays_ticket  = $wpdb->get_results("SELECT id FROM {$wpdb->prefix}wpsc_ticket WHERE DATE(date_created)= '".$today."'");
$response = array();
if ($todays_ticket) {
  foreach ($todays_ticket as $ticket) {
    $frt = $wpscfunction->get_ticket_meta($ticket->id,'first_response',true);
    if($frt){
        $response[] = round($frt/60,2);
    }
  }
}

if(!empty($response)){
  $average[] = round(array_sum($response) / count($response),2);  
}else{
  $average[] = 0;
}

if(count(array_filter($average))){
  $total_avg = round(array_sum($average) / count(array_filter($average)),2);
  $hour      = floor($total_avg);
  $fraction  = $total_avg - $hour;
  $seconds   = $fraction*3600;
  $minute    = floor($seconds/60);
}

$glabel = implode(',' ,$rlabel);
$gvalue = implode(',' ,$average);

?>
<input type="hidden" id="average_frt_hour" value= "<?php  echo isset($hour) ? $hour : 0 ?>"/>
<input type="hidden" id="average_frt_min" value= "<?php echo  isset($minute) ? $minute : 0  ?>"/>
<input type="hidden" id="start_date" value= "<?php echo $startdate ?>"/>
<input type="hidden" id="end_date" value= "<?php echo $today ?>"/>