<?php 
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

global $wpscfunc,$wpdb,$wpscfunction;
/*
* Line Graph
*/
$rlabel = array();
$values = array();
if(isset($_POST['custom_date_start']) && $_POST['custom_date_start']!=''){
    $startdate=$_POST['custom_date_start'];
}else{
     $startdate=date('Y-m-d',strtotime( "monday this week" ));
}
if(isset($_POST['custom_date_end']) && $_POST['custom_date_end']!=''){
    $enddate=$_POST['custom_date_end'];
}else{
    $enddate=date('Y-m-d', strtotime($startdate. ' +6 days'));
}
$datetime1 = new DateTime($startdate);
$datetime2 = new DateTime($enddate);
$diff      = $datetime1->diff($datetime2);
$days      = $diff->format('%a');

if($days<=30){
  $average = array();
  for($i=0;$i<=$days;$i++){
    
    $date     = date('Y-m-d', strtotime($startdate. '+'.$i.' days'));
    $rlabel[] = "'".$date."'";
    
    $check_date = strtotime($date);
    $today = date("Y-m-d", strtotime("today"));
    $t_date = strtotime($today);
    if ($check_date == $t_date) {
        $todays_ticket = $wpdb->get_results("SELECT id FROM {$wpdb->prefix}wpsc_ticket WHERE DATE(date_created)= '" . $today . "'");
        $response = array();
        if ($todays_ticket) {
            foreach ($todays_ticket as $ticket) {
                $frt = $wpscfunction->get_ticket_meta($ticket->id, 'first_response', true);
                if ($frt) {
                    $response[] = round($frt / 60, 2);
                }
            }
        }
          if (!empty($response)) {
              $average[] = round(array_sum($response) / count($response), 2);
          } else {
            $average[] = 0;
          }
      } else {
         $frt  = $wpdb->get_var("SELECT ticket_count FROM {$wpdb->prefix}wpsc_reports WHERE result_date= '".$date."' AND report_type='first_response'");
    
        if($frt){
          $average[] = round($frt/60,2);
        }else{
          $average[] = 0;
        }
    }
  }
  
  if(count(array_filter($average))){
    $total_avg = round(array_sum($average) / count(array_filter($average)),2);
    $hour      = floor($total_avg);
    $fraction  = $total_avg - $hour;
    $seconds   = $fraction * 3600;
    $minute    = floor($seconds / 60);

  }
  
  $glabel = implode(',' ,$rlabel);
  $gvalue = implode(',', $average);
  
}
else if($days>30 && $days<93) {
  
  $average   = array();
  $startweek = date("W",strtotime($startdate));
  $days      = $diff->days;
  $endweek   = date("W",strtotime($enddate));
  
  $year      = date("Y");
  $rlabel     = array();
  $values     = array();
  $week_array = array();

  for($i=$startweek;$i<=$endweek;$i++) {
  
      $result = array();
      $dto = new DateTime();
      $result['start'] = $dto->setISODate($year, $i, 0)->format('Y-m-d');
      $result['end'] = $dto->setISODate($year, $i, 6)->format('Y-m-d');
      $result['week'] = $i;
      $week_array[] = $result;
  }
  
  foreach ($week_array as $key => $value) {
      $rlabel[] = "'".date('Y-m-d',strtotime($value['start']))."'";
      $frt      = $wpdb->get_var("SELECT SUM(ticket_count) FROM {$wpdb->prefix}wpsc_reports WHERE result_date BETWEEN '".$value['start']."' AND '".$value['end']."' AND report_type='first_response'");
      
      if($frt){
        $average[] = round($frt/60,2);
      }else{
        $average[] = 0;
      }
  }
  
  if(count(array_filter($average))){
    $total_avg = round(array_sum($average) / count(array_filter($average)),2);
    $hour      = floor($total_avg);
    $fraction  = $total_avg - $hour;
    $seconds   = $fraction * 3600;
    $minute    = floor($seconds / 60);

  }
  
  $glabel = implode(',' ,$rlabel);
  $gvalue = implode(',', $average);
}else if ($days>93) {
  $start       = (new DateTime($startdate))->modify('first day of this month');
  $end         = (new DateTime($enddate))->modify('first day of next month');
  $interval    = DateInterval::createFromDateString('1 month');
  $period      = new DatePeriod($start, $interval, $end);
  $mdate_array = array();
  $average     = array();
  foreach ($period as $dt) {
      $months = array();
      $mstart = $dt->format("Y-m-d");
      $last_date_find = strtotime(date("Y-m-d", strtotime($mstart)) . ", last day of this month");
      $mend = date("Y-m-d",$last_date_find);
      
      $months['start']=$mstart;
      $months['end']=$mend;
      
      $mdate_array[] = $months;
  }
  
  foreach ($mdate_array as $key => $month) {
    
    if($days > 365){
      $rlabel[] = "'".date("M", strtotime($month['start']))."-".date("y", strtotime($month['start']))."'";  
    }else{
      $rlabel[] = "'".date("F", strtotime($month['start']))."'";  
    }
    
    $frt = $wpdb->get_var("SELECT SUM(ticket_count) FROM {$wpdb->prefix}wpsc_reports WHERE result_date BETWEEN '".$month['start']."' AND '".$month['end']."' AND report_type='first_response'");
    
    if($frt){
      $average[] = round($frt/60,2);
    }else{
      $average[] = 0;
    }
  }
  
  if(count(array_filter($average))){
    $total_avg = round(array_sum($average) / count(array_filter($average)),2);
    $hour      = floor($total_avg);
    $fraction  = $total_avg - $hour;
    $seconds   = $fraction * 3600;
    $minute    = floor($seconds / 60);

  }
  
  $glabel = implode(',' ,$rlabel);
  $gvalue = implode(',', $average);

}

?>
<input type="hidden" id="average_frt_hour" value= "<?php echo isset($hour) ? $hour : 0 ?>"/>
<input type="hidden" id="average_frt_min" value= "<?php echo isset($minute) ? $minute : 0 ?>"/>
<input type="hidden" id="start_date" value= "<?php echo $startdate ?>"/>
<input type="hidden" id="end_date" value= "<?php echo $enddate ?>"/>