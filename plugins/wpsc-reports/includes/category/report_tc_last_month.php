<?php 
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

global $wpscfunc,$wpdb;

$first = date("Y-m-d", strtotime("first day of last month"));
$last = date("Y-m-d", strtotime("last day of last month"));

$category_array = array();

$tickets = $wpdb->get_results("SELECT id FROM {$wpdb->prefix}wpsc_ticket WHERE DATE(date_created) BETWEEN '".$first."' AND '".$last."'");

if ($tickets) {
  foreach ($tickets as $ticket) {
    $category_array[] =  $wpscfunction->get_ticket_fields($ticket->id,'ticket_category');
  }
}

//category bar chart 
$gcategory_data_name = array();
$gcategory_data_count = array();

$categories = get_terms([
  'taxonomy'   => 'wpsc_categories',
  'hide_empty' => false,
  'orderby'    => 'meta_value_num',
  'order'    	 => 'ASC',
  'meta_query' => array('order_clause' => array('key' => 'wpsc_category_load_order')),
]);

foreach ($categories as $category) {
  if( !empty($category_array)){
    $gcategory_data_name[] = '"'.$category->name.'"';
    $gcategory_data_count[] = count(array_keys($category_array, $category->term_id));
  }
}  

?>

<input type="hidden" id="start_date" value= "<?php echo $first ?>"/>
<input type="hidden" id="end_date" value= "<?php echo $last ?>"/>
