<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $wpdb, $current_user, $wpscfunction,$wpscfunc;

if (!$current_user->ID) {
    die();
}

$filter = $wpscfunc->get_current_filter();;

$upload_dir = wp_upload_dir();
$path_to_export = $upload_dir['basedir'] . '/wpsc_export_active_customers.csv';
$url_to_export = $upload_dir['baseurl'] . '/wpsc_export_active_customers.csv';

$filename = $path_to_export;
$fp = fopen($filename, "w");

$export_colomn_name = array();

$export_colomn_name = array(
    'rank'          =>  'Rank',
    'name'          => 'Name',
    'email'         => 'Email',
    'no_of_tickets' => 'No of Tickets'
) ;

fputcsv($fp, $export_colomn_name);

$filter = $wpscfunc->get_current_filter();
$post_per_page = 10;

$sql = "select SQL_CALC_FOUND_ROWS customer_email, COUNT(customer_email) as cnt from {$wpdb->prefix}wpsc_ticket";
$where = " WHERE active=1";
$group = " group by customer_email order by COUNT(customer_email) desc ";

$sql .= $where . $group;

$results = $wpdb->get_results($sql);

$count = $wpdb->get_var("SELECT FOUND_ROWS()");

$record = 1;

foreach ($results as $result) {

    $export_colomn_value = array();
    foreach($export_colomn_name as $c_name){
       if ($c_name == 'Email') {
          $export_colomn_value[] = $result->customer_email;

        } elseif ($c_name =='Name') {
            $user = get_user_by('email', $result->customer_email);
            if (!empty($user)) {
                $name = $user->display_name;
            } else {
                $name = $wpdb->get_var("SELECT customer_name FROM {$wpdb->prefix}wpsc_ticket WHERE customer_email='" . $result->customer_email . "' ORDER BY id DESC LIMIT 1");
            }

            $export_colomn_value[] = $name;
        } elseif ($c_name == 'No of Tickets') {
            $export_colomn_value[] =$result->cnt;
        } elseif($c_name=='Rank') {
            $export_colomn_value[] = $record;
            $record++;
        }
    }
    
    fputcsv($fp, $export_colomn_value);
}
fclose($fp);
echo '{"url_to_export":"' . $url_to_export . '"}';
