<?php 
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

global $wpdb,$wpscfunc,$wpscfunction;
/*
* Line Graph
*/
$first = date("Y-m-d", strtotime("first day of last month"));
$last = date("Y-m-d", strtotime("last day of last month"));
$datetime1  = new DateTime($first);
$datetime2  = new DateTime($last);
$difference = $datetime1->diff($datetime2);
$days       = $difference->d;
$rlabel     = array();
$values     = array();
$average    = array();
for($i=0;$i<=$days;$i++){
  $date = date('Y-m-d', strtotime($first. '+'.$i.' days'));
  $rlabel[] = "'".$date."'";
  $frt  = $wpdb->get_var("SELECT ticket_count FROM {$wpdb->prefix}wpsc_reports WHERE result_date= '".$date."' AND report_type='first_response'");
  
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
$gvalue =implode(',',$average);

?>

<input type="hidden" id="average_frt_hour" value= "<?php echo isset($hour) ? $hour : 0 ?>"/>
<input type="hidden" id="average_frt_min" value= "<?php echo  isset($minute) ? $minute : 0 ?>"/>
<input type="hidden" id="start_date" value= "<?php echo $first ?>"/>
<input type="hidden" id="end_date" value= "<?php echo $last ?>"/>
