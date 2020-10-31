<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$widget = get_term_by( 'slug', 'usergroup', 'wpsc_ticket_custom_fields' );

if($widget){
	update_term_meta ($widget->term_id, 'wpsc_allow_ticket_list', '0');
	update_term_meta ($widget->term_id, 'wpsc_allow_ticket_filter', '0');
}