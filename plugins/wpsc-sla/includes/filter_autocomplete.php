<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user, $wpdb, $wpscfunction;
if (!($current_user->ID)) {exit;}

if($field_slug=='sla'){
  $sla_item = array('sla_out'=>'Out Of SLA', 'sla_in'=>'In SLA');
  foreach ($sla_item as $key => $opt) {
    $output[] = array(
      'label'    => $opt,
      'value'    => '',
      'flag_val' => $key,
      'slug'     => $field_slug,
    );
  }   
}
  
 
