<?php 
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

global $wpdb,$wpscfunc;
/*
* Line Graph
*/

$rlabel =  array();
$values = array();

$ldate = date('Y-m-d', strtotime('last day of december this year'));
$sdate = date('Y-m-d', strtotime('first day of january this year'));

for($i=1;$i<=12;$i++){
  $startdate = date('Y-m-d', mktime(0,0,0,$i,1,date('Y')));
  $lastdate  = date('Y-m-d', mktime(0,0,0,$i+1,0,date('Y')));
  
  $rlabel[] = "'".date("F", strtotime($startdate))."'";
  $count    = $wpdb->get_var("SELECT overdue_count FROM {$wpdb->prefix}wpsc_sla_reports WHERE result_date BETWEEN  '".$startdate."' AND '".$lastdate."'");
  
  if($count){
    $values[]   = $count;  
  }else{
    $values[]   = 0;
  }
} 
$glabel = implode(',' ,$rlabel);
$gvalue = implode(',', $values);

$total_tickets = array_sum($values);


?>

<input type="hidden" id="total_overdue_tickets" value="<?php echo $total_tickets?>" >
<input type="hidden" id="start_date" value= "<?php echo $sdate ?>"/>
<input type="hidden" id="end_date" value= "<?php echo $ldate ?>"/>

