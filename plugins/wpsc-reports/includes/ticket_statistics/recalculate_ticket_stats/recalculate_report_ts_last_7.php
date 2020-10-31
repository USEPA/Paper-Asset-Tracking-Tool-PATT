<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $wpscfunction, $wpdb, $wpscfunc;
/*
 * Line Graph
 */
$startdate = date('Y-m-d', strtotime("today -6 days"));
$average = array();

for ($i = 0; $i < 6; $i++) {
    $date = date('Y-m-d', strtotime($startdate . '+' . $i . ' days'));
    
    $wpdb->query("DELETE FROM {$wpdb->prefix}wpsc_reports WHERE report_type='no_of_tickets' AND result_date='" . $date . "'");

    $tickets = $wpdb->get_var("SELECT DISTINCT COUNT(id) FROM {$wpdb->prefix}wpsc_ticket WHERE DATE(date_created)= '" . $date . "'");

    $wpdb->insert($wpdb->prefix . 'wpsc_reports',
        array(
            'report_type'   => 'no_of_tickets',
            'type'          => 'daily',
            'ticket_count'  => $tickets,
            'result_date'   => $date,
        ));

}
