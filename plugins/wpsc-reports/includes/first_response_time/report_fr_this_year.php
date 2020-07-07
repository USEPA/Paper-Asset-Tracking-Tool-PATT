<?php 
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

global $wpscfunc,$wpdb,$wpscfunction ;
/*
* Line Graph
*/

$rlabel  = array();
$values  = array();
$average = array();

$ldate = date('Y-m-d', strtotime('last day of december this year'));
$sdate = date('Y-m-d', strtotime('first day of january this year'));

for($i=1;$i<=12;$i++){
  $startdate = date('Y-m-d', mktime(0,0,0,$i,1,date('Y')));
  $lastdate = date('Y-m-d', mktime(0,0,0,$i+1,0,date('Y')));
  
  $rlabel[] = "'".date("F", strtotime($startdate))."'";
  $frt      = $wpdb->get_var("SELECT AVG(ticket_count) FROM {$wpdb->prefix}wpsc_reports WHERE result_date BETWEEN '".$startdate."' AND '".$lastdate."' AND report_type='first_response'");
  
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

?>

<input type="hidden" id="average_frt_hour" value= "<?php echo isset($hour) ? $hour : 0 ?>"/>
<input type="hidden" id="average_frt_min" value= "<?php echo  isset($minute) ? $minute : 0 ?>"/>
<input type="hidden" id="start_date" value= "<?php echo $sdate ?>"/>
<input type="hidden" id="end_date" value= "<?php echo $ldate ?>"/>