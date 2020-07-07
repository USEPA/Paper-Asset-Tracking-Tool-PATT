<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $wpscfunc, $wpdb;
/*
 * Line Graph
 */
$startdate = date('Y-m-d', strtotime("today -6 days"));

$rlabel = array();
$values = array();
for ($i = 0; $i < 6; $i++) {
    $date = date('Y-m-d', strtotime($startdate . '+' . $i . ' days'));
    $rlabel[] = "'" . $date . "'";
    $count = $wpdb->get_var("SELECT ticket_count FROM {$wpdb->prefix}wpsc_reports WHERE result_date = '" . $date . "' AND report_type = 'no_of_tickets'");
    $values[] = $count;
}

$today = date('Y-m-d');
$todays_count = "SELECT DISTINCT COUNT(id) from {$wpdb->prefix}wpsc_ticket where DATE(date_created)='" . $today . "'";
$tickets_count = $wpdb->get_var($todays_count);
$rlabel[] = "'" . $today . "'";
$values[] = $tickets_count;

$glabel = implode(',', $rlabel);
$gvalue = implode(',', $values);

$total_tickets = array_sum($values);

?>

<input type="hidden" id="total_tickets" value= "<?php echo $total_tickets ?>"/>
<input type="hidden" id="start_date" value= "<?php echo $startdate ?>"/>
<input type="hidden" id="end_date" value= "<?php echo $today ?>"/>