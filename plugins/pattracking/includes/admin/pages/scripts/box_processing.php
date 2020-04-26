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

## Read value
$draw = $_POST['draw'];
$row = $_POST['start'];
$rowperpage = $_POST['length']; // Rows display per page
$columnIndex = $_POST['order'][0]['column']; // Column index
$columnName = $_POST['columns'][$columnIndex]['data']; // Column name
$columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
$searchValue = $_POST['search']['value']; // Search value

## Custom Field value
$searchByBoxID = str_replace(",", "|", $_POST['searchByBoxID']);
$searchByProgramOffice = $_POST['searchByProgramOffice'];
$searchByDigitizationCenter = $_POST['searchByDigitizationCenter'];
$searchGeneric = $_POST['searchGeneric'];

## Search 
$searchQuery = " ";
if($searchByBoxID != ''){
   $searchQuery .= " and (a.box_id REGEXP '".$searchByBoxID."' ) ";
}

if($searchByProgramOffice != ''){
   $searchQuery .= " and (c.acronym='".$searchByProgramOffice."') ";
}

if($searchByDigitizationCenter != ''){
   $searchQuery .= " and (a.location='".$searchByDigitizationCenter."') ";
}

if($searchGeneric != ''){
   $searchQuery .= " and (a.box_id like '%".$searchGeneric."%' or 
      b.request_id like '%".$searchGeneric."%' or 
      a.location like '%".$searchGeneric."%' or
      c.acronym like '%".$searchGeneric."%') ";
}

if($searchValue != ''){
   $searchQuery .= " and (a.box_id like '%".$searchValue."%' or 
      b.request_id like '%".$searchValue."%' or 
      a.location like '%".$searchValue."%' or
      c.acronym like '%".$searchValue."%') ";
}

## Total number of records without filtering
$sel = mysqli_query($con,"select count(*) as allcount from wpqa_wpsc_epa_boxinfo");
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];

## Total number of records with filtering
$sel = mysqli_query($con,"select count(a.box_id) as allcount FROM wpqa_wpsc_epa_boxinfo as a
INNER JOIN wpqa_wpsc_ticket as b ON a.ticket_id = b.id
INNER JOIN wpqa_wpsc_epa_program_office as c ON a.program_office_id = c.id WHERE 1 ".$searchQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

## Fetch records
$boxQuery = "SELECT CONCAT('<a href=admin.php?page=boxdetails&id=',a.box_id,'>',a.box_id,'</a>') as box_id, b.request_id as request_id, a.location as location, c.acronym as acronym FROM wpqa_wpsc_epa_boxinfo as a
INNER JOIN wpqa_wpsc_ticket as b ON a.ticket_id = b.id
INNER JOIN wpqa_wpsc_epa_program_office as c ON a.program_office_id = c.id
WHERE 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
$boxRecords = mysqli_query($con, $boxQuery);
$data = array();

while ($row = mysqli_fetch_assoc($boxRecords)) {
   $data[] = array(
     "box_id"=>$row['box_id'],
     "request_id"=>$row['request_id'],
     "location"=>$row['location'],
     "acronym"=>$row['acronym']
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