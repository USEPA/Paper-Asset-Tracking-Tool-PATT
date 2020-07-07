<?php 
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

global $wpscfunc,$wpdb;


if(isset($_POST['custom_date_start']) && $_POST['custom_date_start']!=''){
    $startdate = $_POST['custom_date_start'];
}else{
     $startdate = date('Y-m-d',strtotime( "monday this week" ));
}
if(isset($_POST['custom_date_end']) && $_POST['custom_date_end']!=''){
    $enddate  = $_POST['custom_date_end'];
}else{
    $enddate = date('Y-m-d', strtotime($startdate. ' +6 days'));
}


$category_array = array();

$tickets = $wpdb->get_results("SELECT id FROM {$wpdb->prefix}wpsc_ticket WHERE DATE(date_created) BETWEEN '".$startdate."' AND '".$enddate."'");

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

<input type="hidden" id="start_date" value= "<?php echo $startdate ?>"/>
<input type="hidden" id="end_date" value= "<?php echo $enddate ?>"/>