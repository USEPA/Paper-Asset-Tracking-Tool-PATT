<?php

//if ( ! defined( 'ABSPATH' ) ) {
//	exit; // Exit if accessed directly
//}

$path = preg_replace('/wp-content.*$/','',__DIR__);
include($path.'wp-load.php');
include_once( WPPATT_ABSPATH . 'includes/api_authorization_strings.php' );

global $current_user, $wpscfunction, $wpdb;


$lanid_query = $wpdb->get_results(
"
SELECT 
DISTINCT lan_id, lan_id_details from wpqa_wpsc_epa_boxinfo WHERE lan_id <> ''
"
);

foreach ($lanid_query as $lan_id) {

$lan_id_val = $lan_id->lan_id; 
$lan_id_details_val = $lan_id->lan_id_details; 

$curl = curl_init();

$url = 'https://wamssoprd.epa.gov/iam/governance/scim/v1/Users?filter=userName%20eq%20'.$lan_id_val;

$headers = [
    'Cache-Control: no-cache',
	$eidw_authorization
];

        curl_setopt($curl,CURLOPT_URL, $url);
        curl_setopt($curl,CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl,CURLOPT_MAXREDIRS, 10);
        curl_setopt($curl,CURLOPT_TIMEOUT, 30);
        curl_setopt($curl,CURLOPT_HTTP_VERSION,CURL_HTTP_VERSION_1_1);
        curl_setopt($curl,CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($curl,CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);

$lan_id_details = '';

if ($err) {
$lan_id_details = 'Error';
} else {

$json = json_decode($response, true);

$results = $json['totalResults'];
$full_name = $json['Resources']['0']['name']['givenName'].' '.$json['Resources']['0']['name']['familyName'];
$email = $json['Resources']['0']['emails']['0']['value'];
$phone = $json['Resources']['0']['phoneNumbers']['0']['value'];
$org = $json['Resources']['0']['urn:ietf:params:scim:schemas:extension:enterprise:2.0:User']['department'];

//get LAN ID to compare on the box details page
$lan_id_username = $json['Resources'][0]['userName'];

if ($results >= 1) {

$id_query = $wpdb->get_results("SELECT DISTINCT id from wpqa_wpsc_epa_boxinfo WHERE lan_id = '" . $lan_id_val . "'");



foreach ($id_query as $lan_id_update) {
$db_lan_id = $lan_id_update->id ;

// Declare array  
$lan_id_details_array = array( 
    "name"=>$full_name,
    "email"=>$email,
    "phone"=>$phone,
    "org"=>$org,
    "lan_id"=>$lan_id_username,
); 
   
// Use json_encode() function 
$json = json_encode($lan_id_details_array); 
   
// Display the output 
echo($json); 
   
   
$lan_id_details = $full_name.','.$email.','.$phone.','.$org.','.$lan_id_username;

// Detects update to contact info, if yes then update table
if ($lan_id_details != $lan_id_details_val && $lan_id_details != 'Error')
{
$boxinfo_table = 'wpqa_wpsc_epa_boxinfo';

$data_update = array('lan_id_details' => $json);
$data_where = array('id' => $db_lan_id);
$wpdb->update($boxinfo_table, $data_update, $data_where);
}

}

}

//echo $lan_id_details;
//print_r($response);

}
}

?>