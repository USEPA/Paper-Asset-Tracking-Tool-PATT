<?php

global $wpdb, $current_user, $wpscfunction;

$path = preg_replace('/wp-content.*$/','',__DIR__);
include($path.'wp-load.php');
include_once( WPPATT_ABSPATH . 'includes/api_authorization_strings.php' );

if(!empty($_POST['postvarslanid'])){
   //id in box table (e.g. 1)
   $box_id = $_POST['postvarsboxid'];
   //box_id in box table (e.g. 0000001-1)
   $pattboxid = $_POST['postvarspattboxid'];
   $lanid = $_POST['postvarslanid'];
   $pattdocid = $_POST['postvarspattdocid'];

$curl = curl_init();

$url = 'https://wamssoprd.epa.gov/iam/governance/scim/v1/Users?filter=userName%20eq%20'.$lanid;

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
		//curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
		//curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {

$json = json_decode($response, true);

$results = $json['totalResults'];

if ($results >= 1) {
$get_ticket_id = $wpdb->get_row("SELECT ticket_id FROM wpqa_wpsc_epa_boxinfo WHERE box_id = '" . $pattboxid . "'");
$ticket_id = $get_ticket_id->ticket_id;

$metadata_array = array();
$table_name = 'wpqa_wpsc_epa_boxinfo';

$old_box_lanid = $wpdb->get_row("SELECT lan_id FROM wpqa_wpsc_epa_boxinfo WHERE box_id = '" . $pattboxid . "'");
$old_lanid = $old_box_lanid->lan_id;

$folderfile_table = 'wpqa_wpsc_epa_folderdocinfo';

//updates the epa contact by entering a LANID
if(!empty($lanid)) {
$data_update = array('lan_id' => $lanid);
$data_where = array('id' => $box_id);
array_push($metadata_array,'EPA Contact: '.$old_lanid.' > '.$lanid);
$wpdb->update($table_name, $data_update, $data_where);

//updates all associated documents with new epa contact LANID
$data_update = array('epa_contact_email' => $lanid);
$data_where = array('box_id' => $box_id);
$wpdb->update($folderfile_table, $data_update, $data_where);
}

$metadata = implode (", ", $metadata_array);

if($old_lanid != $lanid) {
do_action('wpppatt_after_box_metadata', $ticket_id, $metadata, $pattboxid);

//sends email/notification to user when epa contact is updated
$get_customer_name = $wpdb->get_row('SELECT customer_name FROM wpqa_wpsc_ticket WHERE id = "' . $ticket_id . '"');
$get_user_id = $wpdb->get_row('SELECT ID FROM wpqa_users WHERE display_name = "' . $get_customer_name->customer_name . '"');
$user_id_array = [$get_user_id->ID];
$convert_patt_id = Patt_Custom_Func::translate_user_id($user_id_array,'agent_term_id');
$patt_agent_id = implode($convert_patt_id);
$pattagentid_array = [$patt_agent_id];
$data = [];

$email = 1;

Patt_Custom_Func::insert_new_notification('email-epa-contact-changed',$pattagentid_array,$pattboxid,$data,$email);
}


echo "Box ID #: " . $pattboxid . " has been updated.";
} else {
echo 'LAN ID is not valid';
}
}

} else {
    echo $pattboxid;
   echo "Please make an edit.";
}
?>