<?php
$WP_PATH = implode("/", (explode("/", $_SERVER["PHP_SELF"], -8)));
require_once($_SERVER['DOCUMENT_ROOT'].$WP_PATH.'/wp-config.php');
	
$host = DB_HOST; /* Host name */
$user = DB_USER; /* User */
$password = DB_PASSWORD; /* Password */
$dbname = DB_NAME; /* Database name */

$con = mysqli_connect($host, $user, $password,$dbname);
// Check connection
if (!$con) {
  die("Connection failed: " . mysqli_connect_error());
}

function calculate_time_span($seconds)
{  
 $year = floor($seconds /31556926);
$months = floor($seconds /2629743);
$week=floor($seconds /604800);
$day = floor($seconds /86400); 
$hours = floor($seconds / 3600);
 $mins = floor(($seconds - ($hours*3600)) / 60); 
$secs = floor($seconds % 60);
 if($seconds < 60) $time = $secs." seconds ago";
 else if($seconds < 3600 ) $time =($mins==1)?$mins." minute ago":$mins." minutes ago";
 else if($seconds < 86400) $time = ($hours==1)?$hours." hour ago":$hours." hours ago";
 else if($seconds < 604800) $time = ($day==1)?$day." day ago":$day." days ago";
 else if($seconds < 2629743) $time = ($week==1)?$week." week ago":$week." weeks ago";
 else if($seconds < 31556926) $time =($months==1)? $months." month ago":$months." months ago";
 else $time = ($year==1)? $year." year ago":$year." years ago";
return $time; 
}  

## Read value
$draw = $_POST['draw'];
$row = $_POST['start'];
$rowperpage = $_POST['length']; // Rows display per page
$columnIndex = $_POST['order'][0]['column']; // Column index
$columnName = $_POST['columns'][$columnIndex]['data']; // Column name
$columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
$searchValue = $_POST['search']['value']; // Search value

## Custom Field value
$searchByRequestID = str_replace(",", "|", $_POST['searchByRequestID']);
$searchByDigitizationCenter = $_POST['searchByDigitizationCenter'];
$searchGeneric = $_POST['searchGeneric'];
$searchByStatus = $_POST['searchByStatus'];
$searchByPriority = $_POST['searchByPriority'];
$currentUser = $_POST['currentUser'];
## User Search
$searchByUser = $_POST['searchByUser'];
$searchByUserAAVal = $_POST['searchByUserAAVal'];
$searchByUserAAName = str_replace(",", "|", $_POST['searchByUserAAName']);

## Search 
$searchQuery = " ";
$searchHaving = " ";
$locationarray = array("east", "west", "east cui", "west cui", "not assigned");

if($searchByUser == 'mine') {
   $searchQuery .= " and (a.customer_name ='".$currentUser."') ";    
}

if($searchByUser == 'search for user') {
   $searchQuery .= " and (a.customer_name REGEXP '^(".$searchByUserAAName.")$' ) ";      
}


if($searchByRequestID != ''){
   $searchQuery .= " and (a.request_id REGEXP '^(".$searchByRequestID.")$' ) ";
}

if($searchByStatus != ''){
   $searchQuery .= " and (a.ticket_status='".$searchByStatus."') ";
}

if($searchByPriority != ''){
   $searchQuery .= " and (a.ticket_priority='".$searchByPriority."') ";
}

if($searchByDigitizationCenter != ''){
   $searchHaving = " HAVING location like '%".$searchByDigitizationCenter."%' ";
}

if($searchGeneric != ''){
if(in_array(strtolower($searchGeneric), $locationarray)){
   $searchHaving = " HAVING location like '%".$searchGeneric."%' ";
} else {
   $searchQuery .= " and (a.request_id like '%".$searchGeneric."%' or
      a.customer_name like '%".$searchGeneric."%') ";    
}

}

if($searchValue != ''){
if(in_array(strtolower($searchGeneric), $locationarray)){
   $searchHaving = " HAVING location like '%".$searchGeneric."%' ";
} else {
   $searchQuery .= " and (a.request_id like '%".$searchGeneric."%' or
      a.customer_name like '%".$searchGeneric."%') ";    
}
}

## Total number of records without filtering Filter out inactive (initially deleted tickets)
$sel = mysqli_query($con,"select count(*) as allcount from wpqa_wpsc_ticket WHERE id <> -99999 AND active <> 0");
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];

## Total number of records with filtering
$sel = mysqli_query($con,"select count(*) as allcount FROM (select COUNT(DISTINCT a.request_id) as allcount, GROUP_CONCAT(DISTINCT e.name ORDER BY e.name ASC SEPARATOR ', ') as location
FROM wpqa_wpsc_ticket as a
INNER JOIN wpqa_wpsc_epa_boxinfo as b ON a.id = b.ticket_id
INNER JOIN wpqa_wpsc_epa_program_office as c ON b.program_office_id = c.office_code
INNER JOIN wpqa_wpsc_epa_storage_location as d ON b.storage_location_id = d.id
INNER JOIN wpqa_terms e ON e.term_id = d.digitization_center
WHERE 1 ".$searchQuery." AND active <> 0 group by a.request_id ".$searchHaving.") t");

$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

$status_id = 5;

## Fetch records
$boxQuery = "
SELECT

a.id as request_id,
CONCAT(
'<a href=\"admin.php?page=wpsc-tickets&id=',
a.request_id,
'\">',
a.request_id,
'</a>') as request_id_flag,
CONCAT(
'<span class=\"wpsp_admin_label\" style=\"background-color:',
(SELECT meta_value from wpqa_termmeta where meta_key = 'wpsc_status_background_color' AND term_id = a.ticket_status),
';color:',
(SELECT meta_value from wpqa_termmeta where meta_key = 'wpsc_status_color' AND term_id = a.ticket_status),
';\">',
(SELECT name from wpqa_terms where term_id = a.ticket_status),
'</span>') as ticket_status,

a.customer_name as customer_name,

CONCAT(
'<span class=\"wpsp_admin_label\" style=\"background-color:',
(SELECT meta_value from wpqa_termmeta where meta_key = 'wpsc_priority_background_color' AND term_id = a.ticket_priority),
';color:',
(SELECT meta_value from wpqa_termmeta where meta_key = 'wpsc_priority_color' AND term_id = a.ticket_priority),
';\">',
(SELECT name from wpqa_terms where term_id = a.ticket_priority),
'</span>') as ticket_priority,

a.date_updated as date_updated,

GROUP_CONCAT(DISTINCT e.name ORDER BY e.name ASC SEPARATOR ', ') as location, 
c.office_acronym as acronym
FROM wpqa_wpsc_ticket as a
INNER JOIN wpqa_wpsc_epa_boxinfo as b ON a.id = b.ticket_id
INNER JOIN wpqa_wpsc_epa_program_office as c ON b.program_office_id = c.office_code
INNER JOIN wpqa_wpsc_epa_storage_location as d ON b.storage_location_id = d.id
INNER JOIN wpqa_terms e ON e.term_id = d.digitization_center
WHERE 1 ".$searchQuery." AND active <> 0 group by request_id ".$searchHaving." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;

$boxRecords = mysqli_query($con, $boxQuery);
$data = array();

while ($row = mysqli_fetch_assoc($boxRecords)) {
$seconds = time() - strtotime($row['date_updated']); 

   $data[] = array(
     "request_id"=>$row['request_id'],
     "request_id_flag"=>$row['request_id_flag'],
     "ticket_status"=>$row['ticket_status'],
     "customer_name"=>$row['customer_name'],
     "location"=>$row['location'],
     "ticket_priority"=>$row['ticket_priority'],
     "date_updated"=>calculate_time_span($seconds),
   );
}
## Response
$response = array(
  "draw" => intval($draw),
  "iTotalRecords" => $totalRecords,
  "iTotalDisplayRecords" => $totalRecordwithFilter,
  "aaData" => $data
);

echo json_encode($response);
