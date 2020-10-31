<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $wpscfunction, $wpdb;
/*
 * Line Graph
 */
$startdate = date('Y-m-d', strtotime("today -7 days"));

$rlabel = array();

$values = array();

for ($i = 0; $i < 7; $i++) {
    $date = date('Y-m-d', strtotime($startdate . '+' . $i . ' days'));
    $rlabel[] = "'" . $date . "'";
    $count = $wpdb->get_var("SELECT overdue_count FROM {$wpdb->prefix}wpsc_sla_reports WHERE result_date = '" . $date . "'");

    if ($count) {
        $values[] = $count;
    } else {
        $values[] = 0;
    }

}

$glabel = implode(',', $rlabel);
$gvalue = implode(',', $values);

$total_tickets = array_sum($values);

$today = date('Y-m-d');

?>

<input type="hidden" id="total_overdue_tickets" value="<?php echo $total_tickets ?>" >
<input type="hidden" id="start_date" value= "<?php echo $startdate ?>"/>
<input type="hidden" id="end_date" value= "<?php echo $today ?>"/>