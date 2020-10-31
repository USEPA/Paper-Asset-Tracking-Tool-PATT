<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user;

if ( $current_user->ID || get_option('wpsc_allow_guest_ticket')) {
  include WPSC_ABSPATH . 'includes/admin/tickets/create_ticket/load_create_ticket.php';
	
} else {
  include WPSC_ABSPATH . 'includes/admin/tickets/sign_in/sign_in.php';
}
