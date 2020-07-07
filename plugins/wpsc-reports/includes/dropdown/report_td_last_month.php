<?php 
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

global $wpscfunc,$wpdb;

$first = date("Y-m-d", strtotime("first day of last month"));
$last  = date("Y-m-d", strtotime("last day of last month"));

$term_id = isset($_POST['term_id']) ? sanitize_text_field($_POST['term_id']) : '' ;
$term    = get_term_by('id',$term_id ,'wpsc_ticket_custom_fields');

$custdata_array = array();

$tickets = $wpdb->get_results("SELECT id FROM {$wpdb->prefix}wpsc_ticket WHERE DATE(date_created) BETWEEN '".$first."' AND '".$last."'");


$cust_vals = array();
foreach ($tickets as $ticket) {
  $temp = $wpscfunction->get_ticket_meta($ticket->id,$term->slug,true);
  if($temp){
    $cust_vals[]= $temp;
  }  
}

if(!empty($cust_vals))
  $custdata_array[$term->term_id] = array_count_values($cust_vals);
else
  $custdata_array[$term->term_id] = array();    

$wpsc_tf_options = get_term_meta($term->term_id,'wpsc_tf_options',true);
foreach($wpsc_tf_options as $key => $opt){
  if(!array_key_exists($opt, $custdata_array[$term->term_id])){
    $custdata_array[$term->term_id][$opt] = 0;
  }
}  

$label = get_term_meta($term->term_id,'wpsc_tf_label', true);
?>

<input type="hidden" id="start_date" value= "<?php echo $first ?>"/>
<input type="hidden" id="end_date" value= "<?php echo $last ?>"/>
  
