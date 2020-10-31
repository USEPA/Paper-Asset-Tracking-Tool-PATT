<?php 
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

global $wpdb,$wpscfunc;
/*
* Line Graph
*/

$ldate = date('Y-m-d', strtotime('last day of december this year'));
$sdate = date('Y-m-d', strtotime('first day of january this year'));
$rlabel =  array();
$values = array();
for($i=1;$i<=12;$i++){
  $startdate = date('Y-m-d', mktime(0,0,0,$i,1,date('Y')));
  $lastdate = date('Y-m-d', mktime(0,0,0,$i+1,0,date('Y')));
  
  $rlabel[] = "'".date("F", strtotime($startdate))."'";
  $count    = $wpdb->get_var("SELECT SUM(ticket_count) FROM {$wpdb->prefix}wpsc_reports WHERE result_date BETWEEN  '".$startdate."' AND '".$lastdate."' AND report_type = 'no_of_tickets'");
  
  if($count){
    $values[] = $count;  
  }else{
    $values[] = 0;
  }
  
} 
$glabel = implode(',' ,$rlabel);
$gvalue = implode(',', $values);

$total_tickets = array_sum($values);

?>

<input type="hidden" id="total_tickets" value= "<?php echo $total_tickets ?>"/>
<input type="hidden" id="start_date" value= "<?php echo $sdate ?>"/>
<input type="hidden" id="end_date" value= "<?php echo $ldate ?>"/>