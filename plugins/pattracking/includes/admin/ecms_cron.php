<?php

//if ( ! defined( 'ABSPATH' ) ) {
//	exit; // Exit if accessed directly
//}

$path = preg_replace('/wp-content.*$/','',__DIR__);
include($path.'wp-load.php');

global $current_user, $wpscfunction, $wpdb;

// Obtain files to be transferred
//ini_set('memory_limit','512M');

$error_log_table = 'wpqa_epa_error_log';

$folderfile_query = $wpdb->get_results(
"SELECT 
wpqa_wpsc_ticket.id as ticket_id, 
wpqa_wpsc_epa_folderdocinfo.id as folderdocid, 
wpqa_wpsc_epa_folderdocinfo.folderdocinfo_id, 
wpqa_wpsc_epa_folderdocinfo.file_object_id, 
wpqa_wpsc_epa_folderdocinfo.id as fdid,
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
INNER JOIN wpqa_users ON wpqa_wpsc_epa_boxinfo.lan_id = wpqa_users.ID
WHERE wpqa_wpsc_epa_folderdocinfo.file_object_id = '' AND wpqa_wpsc_epa_folderdocinfo.file_name IS NOT NULL AND wpqa_wpsc_epa_folderdocinfo.file_location LIKE '%uploads%' AND wpqa_wpsc_epa_boxinfo.box_status = 673"
);



foreach ($folderfile_query as $item) {

$rs_num = $item->fnum . '_' .$item->snum . '_' . $item->dnum;

echo '<strong>Filename:</strong> '. $item->file_name . '<br />';
echo '<strong>Title:</strong> '. $item->title . '<br />';
echo '<strong>Folder/Document DB ID:</strong> '. $item->folderdocid . '<br />';
$date = strtotime( $item->date );
$date_formated = date( 'Y-m-d\\TH:i:s', $date );
echo '<strong>Date:</strong> '. $date_formated . '<br />';
echo '<strong>Record Schedule:</strong> '. $rs_num. '<br />';
$event_date = strtotime( $item->close_date );
$event_date_formated = date( 'Y-m-d\\TH:i:s', $event_date );
if ($event_date_formated == '-0001-11-30T00:00:00') {
$event_date_formated = '';
} else {
$event_date_formated = date( 'Y-m-d\\TH:i:s', $event_date );
}
echo '<strong>Event Date:</strong> '. $event_date_formated . '<br />';
echo '<strong>Sensitivity:</strong> 0<br />';
echo '<strong>Custodian:</strong> '. $item->user_login . '<br />';
echo '<strong>Folder:</strong> '. $item->file_location . '<br />';
echo '------------------------------------------------------------<br />';
echo '<strong>Ticket DB ID:</strong> '. $item->ticket_id . '<br />';
echo '<strong>Ticket Status (should always be 66 or completed):</strong> '. $item->ticket_status . '<br />';
echo '<strong>Temp Storage Location & Filename:</strong> http://086.info/wordpress3'. $item->file_location . $item->file_name  . '<br />';
echo '<strong>PATT Folder/Document ID:</strong> '. $item->folderdocinfo_id;
echo '<hr />';

//POST Request to Content Ingestion Endpoint

// Determine if there are one or more files

$searchForValue = ',';

if( strpos($item->file_name, $searchForValue) !== false ) {

$filename_array  = explode(",", $item->file_name);

$r_object_id_array = array();

foreach ($filename_array as $file) {
$file_name_with_full_path = $_SERVER['DOCUMENT_ROOT'] . '/wp-content' . $item->file_location . $file;

$curlFile = curl_file_create($file_name_with_full_path,mime_content_type($file_name_with_full_path));

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
// $temp_user = 'ayuen';

$metadata = '
{ 
"properties":{ 
"r_object_type":"erma_content",
"object_name":"' .$file.'",
"a_application_type":"PATT",
"erma_content_title":"'.$item->title.'",
"erma_content_unid":"' . $folderdocid.'",
"erma_content_date":"'.$date_formated.'",
"erma_content_schedule":"'.$rs_num.'",
"erma_content_eventdate":"'.$event_date_formated.'",
"erma_sensitivity_id":"",
"erma_custodian":"'.$item->user_login.'",
"erma_folder_path":"'.$item->file_location.'"
}
}
';

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

$r_object_id = $json['properties']['r_object_id'];
array_push($r_object_id_array, $r_object_id);

if (empty($r_object_id_array)) {
$r_object_id_array = '';
} else {
$r_object_id_update = implode(',', $r_object_id_array);
}

if (count($r_object_id_array) == count($filename_array)) {
print_r($json);
$table_name = 'wpqa_wpsc_epa_folderdocinfo';
$data_update = array('file_object_id' => $r_object_id_update);
$data_where = array('id' => $item->fdid);
$wpdb->update($table_name , $data_update, $data_where);
}

curl_close($curl);
}

} else {

$file_name_with_full_path = $_SERVER['DOCUMENT_ROOT'] . '/wp-content' . $item->file_location . $item->file_name;

$curlFile = curl_file_create($file_name_with_full_path,mime_content_type($file_name_with_full_path));

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
//$temp_user = 'ayuen';

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
"erma_custodian":"'.$item->user_login.'",
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

$r_object_id = $json['properties']['r_object_id'];

//Write to Audit Log

$table_name = 'wpqa_wpsc_epa_folderdocinfo';
$data_update = array('file_object_id' => $r_object_id);
$data_where = array('id' => $item->fdid);
$wpdb->update($table_name , $data_update, $data_where);

echo $r_object_id;

curl_close($curl);

}


}

?>