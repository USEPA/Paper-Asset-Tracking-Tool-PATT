<?php 
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

global $wpscfunc,$wpdb,$wpscfunction;

/*
* Line Graph
*/
$startdate = date('Y-m-d', strtotime("today "));
$enddate = date('Y-m-d', strtotime("today -3 months"));
$datetime1=new DateTime($startdate);
$datetime2=new DateTime($enddate);
$difference = $datetime1->diff($datetime2);
$startweek=date("W",strtotime($enddate));
$days=$difference->days;
$currentweek = date("W");
$year   = date("Y");

$rlabel =  array();
$values = array();
$week_array=array();


$dto   = new DateTime();
$first = $dto->setISODate($year, $startweek, 0)->format('Y-m-d');

$average = array();
for($i=$startweek;$i<=$currentweek;$i++) {
    $result = array();
    $dto = new DateTime();
    $result['start'] = $dto->setISODate($year, $i, 0)->format('Y-m-d');
    $result['end'] = $dto->setISODate($year, $i, 6)->format('Y-m-d');
    $result['week'] = $i;
    $week_array[] = $result;
}

foreach ($week_array as $key => $value) {
    $rlabel[] = "'".date('Y-m-d',strtotime($value['start']))."'";
    $frt      = $wpdb->get_var("SELECT AVG(ticket_count) FROM {$wpdb->prefix}wpsc_reports WHERE result_date BETWEEN '".$value['start']."' AND '".$value['end']."' AND report_type='first_response' AND ticket_count !=0");
    if($frt) {
      $average[] = round($frt/60,2);
    }else{
      $average[] = 0;
    } 
}

if(count(array_filter($average))){
  $last_quarter_avg_in_min = $wpdb->get_var("SELECT AVG(ticket_count) FROM {$wpdb->prefix}wpsc_reports WHERE result_date BETWEEN '" . $first . "' AND '" . $startdate . "' AND report_type ='first_response' AND ticket_count !=0 ");
  $total_avg = $last_quarter_avg_in_min / 60;
  $hour      = floor($total_avg);
  $fraction  = $total_avg - $hour;
  $seconds   = $fraction * 3600;
  $minute    = floor($seconds / 60);
}

$glabel = implode(',' ,$rlabel);
$gvalue =implode(',',$average);

?>

<input type="hidden" id="average_frt_hour" value= "<?php echo isset($hour) ? $hour : 0 ?>"/>
<input type="hidden" id="average_frt_min" value= "<?php echo isset($minute) ? $minute : 0 ?>"/>
<input type="hidden" id="start_date" value= "<?php echo $enddate ?>"/>
<input type="hidden" id="end_date" value= "<?php echo $startdate ?>"/>