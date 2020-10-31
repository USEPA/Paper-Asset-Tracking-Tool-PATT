<?php 
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

global $wpdb,$wpscfunc,$wpscfunction;
/*
* Line Graph
*/
$rlabel = array();
$values = array();
if(isset($_POST['custom_date_start']) && $_POST['custom_date_start']!=''){
    $startdate=$_POST['custom_date_start'];
}
else{
     $startdate=date('Y-m-d',strtotime( "monday this week" ));
}
if(isset($_POST['custom_date_end']) && $_POST['custom_date_end']!=''){
    $enddate=$_POST['custom_date_end'];
}
else{
    $enddate=date('Y-m-d', strtotime($startdate. ' +6 days'));
}
$datetime1=new DateTime($startdate);
$datetime2=new DateTime($enddate);
$diff = $datetime1->diff($datetime2);
$days =$diff->format('%a');

if($days<=30){
  
  for($i=0;$i<=$days;$i++){
    $date = date('Y-m-d', strtotime($startdate. '+'.$i.' days'));
    $check_date = strtotime($date);
    $today = date("Y-m-d", strtotime("today"));;
    $t_date = strtotime($today);
    if($check_date == $t_date){
      $todays_count = "SELECT DISTINCT COUNT(id) from {$wpdb->prefix}wpsc_ticket where DATE(date_created)='" . $today . "'";
      $tickets_count = $wpdb->get_var($todays_count);
      $rlabel[] = "'" . $today . "'";
      $values[] = $tickets_count;
    }else{
      $rlabel[] = "'" . $date . "'";
      $count = $wpdb->get_var("SELECT ticket_count FROM {$wpdb->prefix}wpsc_reports WHERE result_date = '" . $date . "' AND report_type = 'no_of_tickets'");
      $values[] = $count;
    }
    
  }
  
  $glabel = implode(',' ,$rlabel);
  $gvalue = implode(',', $values);
  
  $total_tickets = array_sum($values);
  
}else if($days>30 && $days<93) {
  
  $startweek=date("W",strtotime($startdate));
  $days=$diff->days;
  $endweek = date("W",strtotime($enddate));
  $year   = date("Y");
  
  $rlabel =  array();
  $values = array();
  $week_array=array();
  
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
      $count    = $wpdb->get_var("SELECT SUM(ticket_count) FROM {$wpdb->prefix}wpsc_reports WHERE result_date BETWEEN  '".$value['start']."' AND '".$value['end']."' AND report_type = 'no_of_tickets'");
      $values[] = $count;
    }

  $glabel = implode(',' ,$rlabel);
  $gvalue = implode(',', $values);
  
  $total_tickets = array_sum($values);
  
}else if ($days>93) {
  $start    = (new DateTime($startdate))->modify('first day of this month');
  $end      = (new DateTime($enddate))->modify('first day of next month');
  $interval = DateInterval::createFromDateString('1 month');
  $period   = new DatePeriod($start, $interval, $end);
  $mdate_array = array();
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
    
    $count    = $wpdb->get_var("SELECT SUM(ticket_count) FROM {$wpdb->prefix}wpsc_reports WHERE result_date BETWEEN  '".$month['start']."' AND '".$month['end']."' AND report_type = 'no_of_tickets'");
    $values[] = $count;
  }
  
  $glabel = implode(',' ,$rlabel);
  $gvalue = implode(',', $values);
  $total_tickets = array_sum($values);

}
?>

<input type="hidden" id="total_tickets" value= "<?php echo $total_tickets ?>"/>
<input type="hidden" id="start_date" value= "<?php echo $startdate ?>"/>
<input type="hidden" id="end_date" value= "<?php echo $enddate ?>"/>

