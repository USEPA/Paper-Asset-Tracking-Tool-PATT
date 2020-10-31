<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

//$path = preg_replace('/wp-content.*$/','',__DIR__);
//include($path.'wp-load.php');

//ini_set('memory_limit','512M');

$error_log_table = 'wpqa_epa_error_log';

global $current_user, $wpscfunction, $wpdb;
$GLOBALS['num'] = 1;

function curlRequest()
{
global $current_user, $wpscfunction, $wpdb;
$sql = 'select * from dm_dbo.VW_ECMS_ORGANIZATION';
$items = 100;
$page = $GLOBALS['num'];
$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => "http://lippizzan3.rtpnc.epa.gov/ecms/find/1.0?apiKey=031a8c90-f025-4e80-ab47-e2bd577410d7&dql=".urlencode($sql)."&items-per-page=".urlencode($items)."&page=".urlencode($page),
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_FAILONERROR => true,
  //CURLOPT_POSTFIELDS => array('metadata' => $metadata,'contents'=> $curlFile),
  CURLOPT_HTTPHEADER => array(
    "Authorization: Basic cGF0dF9hZG1pbjplY21zUGF0dDEyMw=="
  ),
));

$result = curl_exec($curl);

if (curl_errno($curl)) {
    $error_msg = curl_error($curl);
	echo $error_msg;
	// Send notification write to error log
}

$json = json_decode($result, true);

$program_office_table  = $wpdb->prefix . 'wpsc_epa_program_office';

$count = 0;

if (!empty($json['entries'])) {
foreach ($json['entries'] as $data) {
if ($data['content']['properties']['officecode'] <> '' &&  $data['content']['properties']['parent_org_code'] <> '') {
$count++;
$oid_org = $data['content']['properties']['oid_organization'];
$org_description = $data['content']['properties']['organization_description'];
$acroynm = $data['content']['properties']['acronym'];
$officecode = $data['content']['properties']['officecode'];
$office_acronym = $data['content']['properties']['office_acronym'];
$office_name = $data['content']['properties']['office_name'];
$parent_org_code = $data['content']['properties']['parent_org_code'];
echo $oid_org;
echo '<br />';
echo $org_description;
echo '<br />';
echo $acroynm;
echo '<br />';
echo $officecode;
echo '<br />';
echo $office_acronym;
echo '<br />';
echo $office_name;
echo '<br />';
echo $parent_org_code;
echo '<hr />';
$wpdb->insert($program_office_table, array('organization' => $oid_org, 'organization_description' => $org_description, 'organization_acronym' => $acroynm, 'office_code' => $officecode, 'office_acronym' => $office_acronym, 'office_name' => $office_name, 'parent_office_code' => $parent_org_code) ); 
}
}
}
//echo $count;

    if (empty($json['entries'])) {
        // Exit from function in case there is no output
        return;
    } else {
        $GLOBALS['num']++;
        // Call the function to fetch NEXT page
        curlRequest();
    }
	
curl_close($curl);
}

// Call the function for first time
$program_office_table  = $wpdb->prefix . 'wpsc_epa_program_office';
$wpdb->query("TRUNCATE TABLE $program_office_table");
$wpdb->insert($program_office_table, array('organization' => '-99999', 'organization_description' => '-99999', 'organization_acronym' => '-99999', 'office_code' => '-99999', 'office_acronym' => '-99999', 'office_name' => '-99999', 'parent_office_code' => '-99999') ); 

curlRequest();

?>