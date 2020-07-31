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
$searchByDocID = str_replace(",", "|", $_POST['searchByDocID']);
$searchGeneric = $_POST['searchGeneric'];

## Search 
$searchQuery = " ";
if($searchByDocID != ''){
   $searchQuery .= " and (tracking_number REGEXP '^(".$searchByDocID.")$' ) ";
}

if($searchGeneric != ''){
   $searchQuery .= " and (tracking_number like '%".$searchGeneric."%' or 
      company_name like '%".$searchGeneric."%' or 
      status like '%".$searchGeneric."%') ";
}

if($searchValue != ''){
   $searchQuery .= " and (tracking_number like '%".$searchValue."%' or 
      company_name like '%".$searchValue."%' or 
      status like '%".$searchValue."%') ";
}

## Total number of records without filtering
$sel = mysqli_query($con,"select count(*) as allcount from wpqa_wpsc_epa_shipping_tracking WHERE id <> -99999");
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];

## Total number of records with filtering
$sel = mysqli_query($con,"select count(id) as allcount FROM wpqa_wpsc_epa_shipping_tracking
WHERE 1 ".$searchQuery." and id <> -99999");
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

## Fetch records
$docQuery = "SELECT 
id as id,
CASE 
WHEN company_name = 'ups' THEN CONCAT('<a href=\'https://www.ups.com/track?loc=en_US&tracknum=',tracking_number,'\' target=\'blank\'>',tracking_number,'</a>')
WHEN company_name = 'fedex' THEN CONCAT('<a href=\'https://www.fedex.com/apps/fedextrack/?tracknumbers=',tracking_number,'\' target=\'blank\'>',tracking_number,'</a>')
WHEN company_name = 'usps' THEN CONCAT('<a href=\'https://tools.usps.com/go/TrackConfirmAction?qtc_tLabels1=',tracking_number,'\' target=\'blank\'>',tracking_number,'</a>')
WHEN company_name = 'dhl' THEN CONCAT('<a href=\'https://www.logistics.dhl/global-en/home/tracking.html?tracking-id=',tracking_number,'\' target=\'blank\'>',tracking_number,'</a>')
END as tracking_number,
company_name as company_name,
status as status,

CASE 
WHEN shipped = 1 THEN '<span style=\"font-size: 1.3em; color: #008000;\"><i class=\"fas fa-check-circle\" title=\"Shipped\"></i></span>'
WHEN shipped = 0 THEN '<span style=\"font-size: 1.3em; color: #8b0000;\"><i class=\"fas fa-times-circle\" title=\"Not Shipped\"></i></span>'
END as shipped,

CASE 
WHEN delivered = 1 THEN '<span style=\"font-size: 1.3em; color: #008000;\"><i class=\"fas fa-check-circle\" title=\"Delivered\"></i></span>'
WHEN delivered = 0 THEN '<span style=\"font-size: 1.3em; color: #8b0000;\"><i class=\"fas fa-times-circle\" title=\"Not Delivered\"></i></span>'
END as delivered

FROM wpqa_wpsc_epa_shipping_tracking
WHERE 1 ".$searchQuery." and id <> -99999 order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
$docRecords = mysqli_query($con, $docQuery);
$data = array();

while ($row = mysqli_fetch_assoc($docRecords)) {
   $data[] = array(
     "id"=>$row['id'],
     "tracking_number"=>$row['tracking_number'],
     "company_name"=>$row['company_name'],
     "status"=>$row['status'],
     "shipped"=>$row['shipped'],
     "delivered"=>$row['delivered']
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
