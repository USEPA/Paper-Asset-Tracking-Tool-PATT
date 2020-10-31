<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $wpscfunc, $wpdb;


$date1 = date('Y-m-d', strtotime("today"));
$date2 = date('Y-m-d', strtotime("today -7 days"));

$category_array = array();

$tickets = $wpdb->get_results("SELECT id FROM {$wpdb->prefix}wpsc_ticket WHERE DATE(date_created) BETWEEN '" . $date2 . "' AND '" . $date1 . "'");

if ($tickets) {
    foreach ($tickets as $ticket) {
        $category_array[] = $wpscfunction->get_ticket_fields($ticket->id, 'ticket_category');
    }
}

//category bar chart
$gcategory_data_name = array();
$gcategory_data_count = array();

$categories = get_terms([
    'taxonomy' => 'wpsc_categories',
    'hide_empty' => false,
    'orderby' => 'meta_value_num',
    'order' => 'ASC',
    'meta_query' => array('order_clause' => array('key' => 'wpsc_category_load_order')),
]);

foreach ($categories as $category) {
    if (!empty($category_array)) {
        $gcategory_data_name[] = '"'.$category->name.'"';
        $gcategory_data_count[] = count(array_keys($category_array, $category->term_id));
    }
}
?>

<input type="hidden" id="start_date" value= "<?php echo $date2 ?>"/>
<input type="hidden" id="end_date" value= "<?php echo $date1 ?>"/>

