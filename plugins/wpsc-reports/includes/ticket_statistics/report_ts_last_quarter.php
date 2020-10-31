<?php 
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

global $wpscfunc,$wpdb;
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
    $count    = $wpdb->get_var("SELECT  SUM(ticket_count) FROM {$wpdb->prefix}wpsc_reports WHERE result_date BETWEEN  '".$value['start']."' AND '".$value['end']."' AND report_type = 'no_of_tickets'");
    $values[] = $count;
}

$glabel = implode(',' ,$rlabel);
$gvalue = implode(',', $values);

$total_tickets = array_sum($values);
?>

<input type="hidden" id="total_tickets" value= "<?php echo $total_tickets ?>"/>
<input type="hidden" id="start_date" value= "<?php echo $enddate ?>"/>
<input type="hidden" id="end_date" value= "<?php echo $startdate ?>"/>
