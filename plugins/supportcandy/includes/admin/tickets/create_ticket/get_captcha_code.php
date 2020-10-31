<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$key = rand();
$code = wp_create_nonce($key);
setcookie('wpsc_secure_code',$key);
echo $code;