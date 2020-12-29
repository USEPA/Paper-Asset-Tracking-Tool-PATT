<?php
$WP_PATH = implode("/", (explode("/", $_SERVER["PHP_SELF"], -6)));
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
$userID = $_POST['userID'];

## Search 
$searchQuery = " ";

if($searchValue != ''){
   $searchQuery .= " and (a.identifier like '%".$searchValue."%' or 
      a.subject  like '%".$searchValue."%' or 
      a.content like '%".$searchValue."%') ";
}

## Total number of records without filtering
$sel = mysqli_query($con,"select count(*) as allcount FROM wpqa_pm a 
INNER JOIN wpqa_pm_users b ON a.id = b.pm_id WHERE b.deleted != '2' AND b.recipient = ".$userID);
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];

## Total number of records with filtering
$sel = mysqli_query($con,"select count(*) as allcount FROM wpqa_pm a 
INNER JOIN wpqa_pm_users b ON a.id = b.pm_id
WHERE 1 ".$searchQuery." AND b.deleted != '2' AND b.recipient = ".$userID);
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

## Fetch records
if ($rowperpage == '-1') {
$row_limit = '';
} else {
$row_limit = " limit ".$row.",".$rowperpage;    
}
$messageQuery = "SELECT 
a.id as id,
a.identifier as identifier,
CASE WHEN (b.viewed = 0)
THEN CONCAT('<strong><a href=\"#\" class=\"detailsmodal\">',a.subject,'</a></strong>')
ELSE CONCAT('<a href=\"#\" class=\"detailsmodal\">',a.subject,'</a>')
END as subject,
b.viewed as viewed, 
a.date as sent_date,
CASE WHEN (a.content = '')
THEN 'No additional message.'
ELSE a.content
END as content

FROM wpqa_pm a 
INNER JOIN wpqa_pm_users b ON a.id = b.pm_id
WHERE 1 ".$searchQuery." AND b.deleted != '2' AND b.recipient = ".$userID." order by ".$columnName." ".$columnSortOrder.$row_limit;
$messageRecords = mysqli_query($con, $messageQuery);
$data = array();

while ($row = mysqli_fetch_assoc($messageRecords)) {

   $result = str_replace( chr( 160 ), ' ', $row['content'] );
    
   $data[] = array(
     "id"=>$row['id'],
     "identifier"=>$row['identifier'],
     "subject"=>$row['subject'],
     "content"=>htmlspecialchars($result, ENT_QUOTES, 'UTF-8'),
     "sent_date"=>$row['sent_date']
   );
}

## Response
$response = array(
  "draw" => intval($draw),
  "iTotalRecords" => $totalRecords,
  "iTotalDisplayRecords" => $totalRecordwithFilter,
    "test" => $messageQuery,
  "aaData" => $data
);

echo json_encode($response);
