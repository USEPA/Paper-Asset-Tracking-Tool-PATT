<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user;

if ($current_user->ID) {
  include WPSC_ABSPATH . 'includes/admin/tickets/ticket_list/load_list.php';
} else {
  include WPSC_ABSPATH . 'includes/admin/tickets/sign_in/sign_in.php';
}
