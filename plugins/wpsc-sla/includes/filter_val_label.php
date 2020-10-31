<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if($field_slug=='sla'){
  if($val=='sla_out'){
    $val = 'Out Of SLA';
  }elseif ($val=='sla_in') {
    $val = 'In SLA';
  }
}
?>