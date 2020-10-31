<?php 
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

global $wpscfunc,$wpdb;
/*
* Line Graph
*/
$first = date("Y-m-d", strtotime("first day of last month"));
$last  = date("Y-m-d", strtotime("last day of last month"));
$datetime1 = new DateTime($first);
$datetime2 = new DateTime($last);
$difference = $datetime1->diff($datetime2);
$days       = $difference->d;
$rlabel = array();
$values = array();
for($i=0;$i<=$days;$i++){
  $date       = date('Y-m-d', strtotime($first. '+'.$i.' days'));
  $rlabel[]   = "'".$date."'";
  $count      = $wpdb->get_var("SELECT ticket_count FROM {$wpdb->prefix}wpsc_reports WHERE result_date = '".$date."' AND report_type = 'no_of_tickets'");
  $values[]   = $count;
}
$glabel = implode(',' ,$rlabel);
$gvalue = implode(',', $values);

$total_tickets = array_sum($values);
?>

<input type="hidden" id="total_tickets" value= "<?php echo $total_tickets ?>"/>
<input type="hidden" id="start_date" value= "<?php echo $first ?>"/>
<input type="hidden" id="end_date" value= "<?php echo $last ?>"/>
