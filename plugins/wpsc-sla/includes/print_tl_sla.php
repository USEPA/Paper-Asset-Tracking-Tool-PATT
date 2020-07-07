<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user, $wpscfunction;

if ($ticket_list->list_item->slug != 'sla') return;

$sla = $wpscfunction->get_ticket_meta($ticket_list->ticket['id'],'sla',true);

if( $sla=='' || $sla=='3099-01-01 00:00:00' ) {
	echo '';
	return;
}

$now  = new DateTime;
$ago  = new DateTime($sla);
$diff = $now->diff($ago);

$str = '';

if($diff->y){
	$str = sprintf(__('%1$s year %2$s month','wpsc-sla'),$diff->y,$diff->m);
} else if($diff->m){
	$str = sprintf(__('%1$s month %2$s day','wpsc-sla'),$diff->m,$diff->d);
} else if($diff->d){
	$str = sprintf(__('%1$s day %2$s hour','wpsc-sla'),$diff->d,$diff->h);
} else if($diff->h){
	$str = sprintf(__('%1$s hour %2$s min','wpsc-sla'),$diff->h,$diff->i);
} else if($diff->i){
	$str = sprintf(__('%1$s min %2$s sec','wpsc-sla'),$diff->i,$diff->s);
} else if($diff->s){
	$str = sprintf(__('%1$s sec','wpsc-sla'),$diff->s);
}

if( $diff->invert ){
	$background_color = get_option('wpsc_out_sla_color','');
	do_action('wpsc_after_ticket_out_of_sla');
} else {
	$background_color = get_option('wpsc_in_sla_color','');
}

echo '<span class="wpsp_admin_label" style="background-color:'.$background_color.';color:#ffffff;">'.$str.'</span>';
