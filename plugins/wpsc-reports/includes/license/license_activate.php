<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user;

if( !$current_user->ID || !$current_user->has_cap('manage_options') ) die('You do not have this permission!');

$license_key = isset($_POST['license_key']) ? sanitize_text_field( $_POST['license_key'] ) : '';
if(!$license_key) die();


$api_params = array(
    'edd_action' => 'activate_license',
    'license'    => $license_key,
    'item_id'    => WPSC_RP_STORE_ID,
    'url'        => site_url()
);
$response = wp_remote_post( WPSC_STORE_URL, array( 'body' => $api_params, 'timeout' => 15, 'sslverify' => false ) );
$license_data = json_decode( wp_remote_retrieve_body( $response ), true );

if(isset($license_data['success']) && $license_data['success']){
  update_option('wpsc_rp_license_key',$license_key);
  update_option('wpsc_rp_license_expiry',$license_data['expires']);
  $response = array(
    'success' => 1,
    'messege' => 'License activated successfully!'
  );
} else {
  update_option('wpsc_rp_license_key','');
  update_option('wpsc_rp_license_expiry','');
  $response = array(
    'success' => 0,
    'messege' => 'Error activating license key!'
  );
}

echo json_encode( $response );
