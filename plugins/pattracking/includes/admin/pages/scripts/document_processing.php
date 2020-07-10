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
$searchByProgramOffice = $_POST['searchByProgramOffice'];
$searchByDigitizationCenter = $_POST['searchByDigitizationCenter'];
$searchGeneric = $_POST['searchGeneric'];

## Search 
$searchQuery = " ";
if($searchByDocID != ''){
   $searchQuery .= " and (a.folderdocinfo_id REGEXP '^(".$searchByDocID.")$' ) ";
}

if($searchByProgramOffice != ''){
   $searchQuery .= " and (c.office_acronym='".$searchByProgramOffice."') ";
}

if($searchByDigitizationCenter != ''){
   $searchQuery .= " and (f.name ='".$searchByDigitizationCenter."') ";
}

if($searchGeneric != ''){
   $searchQuery .= " and (a.folderdocinfo_id like '%".$searchGeneric."%' or 
      b.request_id like '%".$searchGeneric."%' or 
      f.name like '%".$searchGeneric."%' or
      c.office_acronym like '%".$searchGeneric."%') ";
}

if($searchValue != ''){
   $searchQuery .= " and (a.folderdocinfo_id like '%".$searchValue."%' or 
      b.request_id like '%".$searchValue."%' or 
      f.name like '%".$searchValue."%' or
      c.office_acronym like '%".$searchValue."%') ";
}

## Total number of records without filtering
$sel = mysqli_query($con,"select count(*) as allcount from wpqa_wpsc_epa_folderdocinfo WHERE id <> -99999");
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];

## Total number of records with filtering
$sel = mysqli_query($con,"select count(a.folderdocinfo_id) as allcount FROM wpqa_wpsc_epa_folderdocinfo as a
INNER JOIN wpqa_wpsc_epa_boxinfo as d ON a.box_id = d.id
INNER JOIN wpqa_wpsc_epa_storage_location as e ON d.storage_location_id = e.id
INNER JOIN wpqa_wpsc_ticket as b ON d.ticket_id = b.id
INNER JOIN wpqa_wpsc_epa_program_office as c ON d.program_office_id = c.office_code
INNER JOIN wpqa_terms f ON f.term_id = e.digitization_center
WHERE 1 ".$searchQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

## Fetch records
$docQuery = "SELECT 
a.folderdocinfo_id as folderdocinfo_id,
CONCAT(

CASE WHEN d.box_destroyed > 0  AND a.freeze <> 1
THEN CONCAT('<a href=\"admin.php?pid=docsearch&page=filedetails&id=',a.folderdocinfo_id,'\" style=\"color: #FF0000 !important; text-decoration: line-through;\">',a.folderdocinfo_id,'</a> <span style=\"font-size: 1em; color: #FF0000;\"><i class=\"fas fa-ban\" title=\"Box Destroyed\"></i></span>')
ELSE CONCAT('<a href=\"admin.php?pid=docsearch&page=filedetails&id=',a.folderdocinfo_id,'\">',a.folderdocinfo_id,'</a>')
END,

CASE 
WHEN (unauthorized_destruction = 1 AND freeze = 1) THEN CONCAT(' <span style=\"font-size: 1em; color: #8b0000;\"><i class=\"fas fa-flag\" title=\"Unauthorized Destruction\"></i></span>', ' <span style=\"font-size: 1em; color: #009ACD;\"><i class=\"fas fa-snowflake\" title=\"Freeze\"></i></span>')
WHEN(freeze = 1)  THEN ' <span style=\"font-size: 1em; color: #009ACD;\"><i class=\"fas fa-snowflake\" title=\"Freeze\"></i></span>'
WHEN (unauthorized_destruction = 1) THEN ' <span style=\"font-size: 1em; color: #8b0000;\"><i class=\"fas fa-flag\" title=\"Unauthorized Destruction\"></i></span>'
ELSE ''
END) as folderdocinfo_id_flag,
CONCAT('<a href=admin.php?page=wpsc-tickets&id=',b.request_id,'>',b.request_id,'</a>') as request_id, f.name as location, c.office_acronym as acronym,

CONCAT(
CASE 
WHEN validation = 1 THEN CONCAT('<span style=\"font-size: 1.3em; color: #008000;\"><i class=\"fas fa-check-circle\" title=\"Validated\"></i></span> ',' (',(user_nicename),')')
WHEN rescan = 1 THEN CONCAT('<span style=\"font-size: 1.3em; color: #8b0000;\"><i class=\"fas fa-times-circle\" title=\"Not Validated\"></i></span> ',' <span style=\"color: #FF0000;\"><strong>[Re-scan]</strong></span>')
ELSE '<span style=\"font-size: 1.3em; color: #8b0000;\"><i class=\"fas fa-times-circle\" title=\"Not Validated\"></i></span> '
END) as validation

FROM wpqa_wpsc_epa_folderdocinfo as a

LEFT JOIN wpqa_users as u ON a.validation_user_id = u.ID
INNER JOIN wpqa_wpsc_epa_boxinfo as d ON a.box_id = d.id
INNER JOIN wpqa_wpsc_epa_storage_location as e ON d.storage_location_id = e.id
INNER JOIN wpqa_wpsc_ticket as b ON d.ticket_id = b.id
INNER JOIN wpqa_wpsc_epa_program_office as c ON d.program_office_id = c.office_code
INNER JOIN wpqa_terms f ON f.term_id = e.digitization_center
WHERE 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
$docRecords = mysqli_query($con, $docQuery);
$data = array();

while ($row = mysqli_fetch_assoc($docRecords)) {
   $data[] = array(
     "folderdocinfo_id"=>$row['folderdocinfo_id'],
     "folderdocinfo_id_flag"=>$row['folderdocinfo_id_flag'],
     "request_id"=>$row['request_id'],
     "location"=>$row['location'],
     "acronym"=>$row['acronym'],
     "validation"=>$row['validation']
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
