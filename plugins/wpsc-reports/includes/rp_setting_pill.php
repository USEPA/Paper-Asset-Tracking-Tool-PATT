<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

add_submenu_page(
 'wpsc-tickets',
 __( 'Reports', 'wpsc-rp' ),
 __( 'Reports', 'wpsc-rp' ),
 'manage_options',
 'wpsc-reports',
  array($this,'load_settings')
);

