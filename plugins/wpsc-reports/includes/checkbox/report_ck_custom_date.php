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

$term_id = isset($_POST['term_id']) ? sanitize_text_field($_POST['term_id']) : '' ;
$term    = get_term_by('id',$term_id ,'wpsc_ticket_custom_fields');

$custdata_array = array();

$tickets = $wpdb->get_results("SELECT id FROM {$wpdb->prefix}wpsc_ticket WHERE DATE(date_created) BETWEEN '".$startdate."' AND '".$enddate."'");

$cust_vals = array();

if ($tickets) {
  foreach ($tickets as $ticket) {
    $temp = $wpscfunction->get_ticket_meta($ticket->id,$term->slug);
    if(!empty($temp)){ 
      foreach ($temp as $value) {
        $cust_vals[] = $value;
      }
    }  
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

<input type="hidden" id="start_date" value= "<?php echo $startdate ?>"/>
<input type="hidden" id="end_date" value= "<?php echo $enddate ?>"/>

  