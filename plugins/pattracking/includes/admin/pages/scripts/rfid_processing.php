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
$searchGeneric = $_POST['searchGeneric'];
$searchByReaderID = $_POST['searchByReaderID'];

## Search 
$searchQuery = " ";
if($searchByBoxID != ''){
   $searchQuery .= " and (a.box_id REGEXP '^(".$searchByBoxID.")$' ) ";
}

if($searchByReaderID != ''){
   $searchQuery .= " and (a.Reader_Name ='".$searchByReaderID."') ";
}

if($searchGeneric != ''){
   $searchQuery .= " and (a.box_id like '%".$searchGeneric."%' or 
      a.Reader_Name like '%".$searchGeneric."%' or 
      a.epc like '%".$searchGeneric."%' or
      a.DateAdded like '%".$searchGeneric."%') ";
}

if($searchValue != ''){
   $searchQuery .= " and (a.box_id like '%".$searchValue."%' or 
      a.Reader_Name  like '%".$searchValue."%' or 
      a.epc like '%".$searchValue."%' or
      a.DateAdded like '%".$searchValue."%') ";
}

## Total number of records without filtering
$sel = mysqli_query($con,"select count(*) as allcount from wpqa_wpsc_epa_rfid_data");
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];

## Total number of records with filtering
$sel = mysqli_query($con,"select count(a.box_id) as allcount FROM wpqa_wpsc_epa_rfid_data as a 
INNER JOIN wpqa_wpsc_epa_boxinfo as b ON a.box_id = b.box_id
INNER JOIN wpqa_wpsc_ticket as c ON b.ticket_id = c.id
WHERE 1 ".$searchQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

## Fetch records
$boxQuery = "SELECT a.Reader_Name, CONCAT('<a href=admin.php?page=boxdetails&pid=boxsearch&id=',a.box_id,'>',a.box_id,'</a>') as box_id, CONCAT('<a href=admin.php?page=wpsc-tickets&id=',c.request_id,'>',c.request_id,'</a>') as request_id, a.epc, a.DateAdded FROM wpqa_wpsc_epa_rfid_data as a
INNER JOIN wpqa_wpsc_epa_boxinfo as b ON a.box_id = b.box_id
INNER JOIN wpqa_wpsc_ticket as c ON b.ticket_id = c.id
WHERE (c.active <> 0) AND 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
$boxRecords = mysqli_query($con, $boxQuery);
$data = array();

while ($row = mysqli_fetch_assoc($boxRecords)) {
   $data[] = array(
     "box_id"=>$row['box_id'],
     "request_id"=>$row['request_id'],
     "Reader_Name"=>$row['Reader_Name'],
     "epc"=>$row['epc'],
     "DateAdded"=>$row['DateAdded']
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