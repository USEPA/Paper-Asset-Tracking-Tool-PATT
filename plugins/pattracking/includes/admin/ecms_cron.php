<?php

//if ( ! defined( 'ABSPATH' ) ) {
	//exit; // Exit if accessed directly
//}

$path = preg_replace('/wp-content.*$/','',__DIR__);
include($path.'wp-load.php');

global $current_user, $wpscfunction, $wpdb;

// Obtain files to be transferred
//ini_set('memory_limit','512M');


$folderfile_query = $wpdb->get_results(
"SELECT 
wpqa_wpsc_ticket.id as ticket_id, 
wpqa_wpsc_epa_folderdocinfo.id as folderdocid, 
wpqa_wpsc_epa_folderdocinfo.folderdocinfo_id, 
wpqa_wpsc_epa_folderdocinfo.title, 
wpqa_wpsc_epa_folderdocinfo.date,
wpqa_wpsc_epa_folderdocinfo.close_date,
wpqa_epa_record_schedule.Function_Number as fnum,
wpqa_epa_record_schedule.Schedule_Number as snum,
wpqa_epa_record_schedule.Disposition_Number as dnum,
wpqa_wpsc_ticket.ticket_status,
wpqa_users.user_login,
wpqa_wpsc_epa_folderdocinfo.file_name, 
wpqa_wpsc_epa_folderdocinfo.file_location
FROM wpqa_wpsc_epa_folderdocinfo
INNER JOIN wpqa_wpsc_epa_boxinfo ON  wpqa_wpsc_epa_folderdocinfo.box_id = wpqa_wpsc_epa_boxinfo.id
INNER JOIN wpqa_epa_record_schedule ON wpqa_wpsc_epa_boxinfo.record_schedule_id = wpqa_epa_record_schedule.id
INNER JOIN wpqa_wpsc_ticket ON  wpqa_wpsc_epa_boxinfo.box_id = wpqa_wpsc_ticket.id
INNER JOIN wpqa_users ON wpqa_wpsc_epa_boxinfo.user_id = wpqa_users.ID
WHERE wpqa_wpsc_epa_folderdocinfo.file_name IS NOT NULL AND wpqa_wpsc_epa_folderdocinfo.file_location LIKE '%uploads%' AND wpqa_wpsc_epa_boxinfo.box_status = 673"
);

foreach ($folderfile_query as $item) {

/*
// Preliminary Test - to be commented out
echo '<strong>Filename:</strong> '. $item->file_name . '<br />';
echo '<strong>Title:</strong> '. $item->title . '<br />';
echo '<strong>Folder/Document DB ID:</strong> '. $item->folderdocid . '<br />';
$date = strtotime( $item->date );
$date_formated = date( 'Y-m-d\\TH:i:s', $date );
echo '<strong>Date:</strong> '. $date_formated . '<br />';
echo '<strong>Record Schedule:</strong> '. $item->rsnum . '<br />';
$event_date = strtotime( $item->close_date );
$event_date_formated = date( 'Y-m-d\\TH:i:s', $event_date );

if ($event_date_formated == '-0001-11-30T00:00:00') {
$event_date_formated = '';
} else {
$event_date_formated = date( 'Y-m-d\\TH:i:s', $event_date );
}

echo '<strong>Event Date:</strong> '. $event_date_formated . '<br />';
echo '<strong>Sensativity:</strong> 0<br />';
echo '<strong>Custodian:</strong> '. $item->user_login . '<br />';
echo '<strong>Folder:</strong> '. $item->file_location . '<br />';

echo '------------------------------------------------------------<br />';
echo '<strong>Ticket DB ID:</strong> '. $item->ticket_id . '<br />';
echo '<strong>Ticket Status (should always be 66 or completed):</strong> '. $item->ticket_status . '<br />';
echo '<strong>Temp Storage Location & Filename:</strong> http://086.info/wordpress3'. $item->file_location . $item->file_name  . '<br />';
echo '<strong>PATT Folder/Document ID:</strong> '. $item->folderdocinfo_id;
echo '<hr />';
*/

//POST Request to Content Ingestion Endpoint
$file_name_with_full_path = $_SERVER['DOCUMENT_ROOT'] . '/wordpress3/wp-content' . $item->file_location . $item->file_name;

$curlFile = curl_file_create($file_name_with_full_path,mime_content_type($file_name_with_full_path));

//$fileHandler = fopen($file_name_with_full_path, 'r');
//$fileData = fread($fileHandler, filesize($file_name_with_full_path));


//$file = fopen($file_name_with_full_path, 'r');
//$len = 1024;
//$fileData = fread( $file, $len );

//while (!feof($file)) {
//   $fileData .= fread( $file, $len );
//}

//fclose($file);

$date = strtotime( $item->date );
$date_formated = date( 'Y-m-d\\TH:i:s', $date );

$event_date = strtotime( $item->close_date );
$event_date_formated = date( 'Y-m-d\\TH:i:s', $event_date );

if ($event_date_formated == '-0001-11-30T00:00:00') {
$event_date_formated = '';
} else {
$event_date_formated = date( 'Y-m-d\\TH:i:s', $event_date );
}

$rs_num = $item->fnum . '_' .$item->snum . '_' . $item->dnum;

$folderdocid = 'PATT_' . $item->folderdocinfo_id;

// Temp switch of user for testing purposes switch to $item->user_login for production
$temp_user = 'ayuen';

$metadata = '
{ 
"properties":{ 
"r_object_type":"erma_content",
"object_name":"' .$item->file_name.'",
"a_application_type":"PATT",
"erma_content_title":"'.$item->title.'",
"erma_content_unid":"' . $folderdocid.'",
"erma_content_date":"'.$date_formated.'",
"erma_content_schedule":"'.$rs_num.'",
"erma_content_eventdate":"'.$event_date_formated.'",
"erma_sensitivity_id":"",
"erma_custodian":"'.$temp_user.'",
"erma_folder_path":"'.$item->file_location.'"
}
}
';

//echo '<br />' . $file_name_with_full_path .'<br />';
//echo $metadata;


$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => "http://lippizzan3.rtpnc.epa.gov/ecms/save/1.2?apiKey=031a8c90-f025-4e80-ab47-e2bd577410d7",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLINFO_HEADER_OUT => true,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => array('metadata' => $metadata,'contents'=> $curlFile),
  CURLOPT_HTTPHEADER => array(
    "Authorization: Basic cGF0dF9hZG1pbjplY21zUGF0dDEyMw=="
  ),
));

$result = curl_exec($curl);
$retry = 0;
$json = json_decode($result, true);

date_default_timezone_set("America/New_York");
$date = date('m/d/Y h:i:s a', time());

//$information = curl_getinfo($curl);
//print_r( $information);

print_r($json);

/*
// Error Handling
if (array_key_exists("status",$json) || array_key_exists("code",$json))
  {
while($json['status'] == 401 && $json['code'] == 'E_BAD_CREDENTIALS_ERROR' && $retry < 2){
    $result = curl_exec($curl);
    $retry++;
}

while($json['status'] == 401 || $json['status'] == 500){
	$wpdb->insert('wpqa_epa_error_log', array(
    'Status_Code' => $json['status'],
    'Error_Message' => $json['code'],
    'Service_Type' => 'PATT_ECMS_API',
	'Timestamp' => $date,
));

}
  }

if(!$result){
    die('Error: "' . curl_error($curl) . '" - Code: ' . curl_errno($curl));
	
	$wpdb->insert('wpqa_epa_error_log', array(
    'Status_Code' => curl_errno($curl),
    'Error_Message' => curl_error($curl),
    'Service_Type' => 'PATT_ECMS_CURL',
	'Timestamp' => $date,
));
}
*/
curl_close($curl);
//var_dump($result);

/*
// Check Response success 
if ($response = '123') {
// Update Ticket Status
$wpscfunction->change_status($item->ticket_id, 67);

// Update Filelocation to ECMS URL
$table_name = 'wpqa_wpsc_epa_folderdocinfo';
$ecms_location = '/ecms/...';
$wpdb->update( $table_name, array( 'file_location' => $ecms_location),array('ID'=>$item->folderdocid));


// Delete record from temp storage
    if (file_exists($file_name_with_full_path)) {
        unlink($file_name_with_full_path);
    } else {
        // File not found.
    }

}
*/
}

?>
