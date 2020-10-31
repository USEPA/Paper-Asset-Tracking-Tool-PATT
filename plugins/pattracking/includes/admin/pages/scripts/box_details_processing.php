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
$box_id = str_replace(",", "|", $_POST['BoxID']);
$page_id = $_POST['page'];
$p_id = $_POST['PID'];
$searchGeneric = $_POST['searchGeneric'];

## Search 
$searchQuery = " ";
if($searchGeneric != ''){
   $searchQuery .= " and (folderdocinfo_id like '%".$searchGeneric."%' or 
      title like '%".$searchGeneric."%' or 
      date like '%".$searchGeneric."%' or
      epa_contact_email like '%".$searchGeneric."%') ";
}

if($searchValue != ''){
   $searchQuery .= " and (folderdocinfo_id like '%".$searchValue."%' or 
      title  like '%".$searchValue."%' or 
      date like '%".$searchValue."%' or
      epa_contact_email like '%".$searchValue."%') ";
}

## Total number of records without filtering
$sel = mysqli_query($con,"select count(*) as allcount from wpqa_wpsc_epa_folderdocinfo WHERE box_id = ".$box_id);
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];

## Total number of records with filtering
$sel = mysqli_query($con,"select count(*) as allcount FROM wpqa_wpsc_epa_folderdocinfo
WHERE 1 ".$searchQuery." AND box_id = ".$box_id);
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

$url_var = '';
if ($p_id == 'requestdetails') {
$url_var = 'admin.php?pid=requestdetails&page=filedetails&id=';
}
if ($p_id == 'boxsearch') {
$url_var = 'admin.php?pid=boxsearch&page=filedetails&id=';
}
if ($p_id == 'docsearch') {
$url_var = 'admin.php?pid=docsearch&page=filedetails&id=';
}

## Fetch records

if ($rowperpage == '-1') {
$row_limit = '';
} else {
$row_limit = " limit ".$row.",".$rowperpage;    
}
$boxQuery = "SELECT 
CONCAT(

CASE WHEN (
SELECT wpqa_wpsc_epa_boxinfo.box_destroyed FROM wpqa_wpsc_epa_boxinfo WHERE wpqa_wpsc_epa_boxinfo.id = wpqa_wpsc_epa_folderdocinfo.box_id) > 0 AND
freeze <> 1

THEN CONCAT('<a href=\"".$url_var."',folderdocinfo_id,'\" id=\"folderdocinfo_link\" style=\"color: #FF0000 !important; text-decoration: line-through;\">',folderdocinfo_id,'</a> <span style=\"font-size: 1em; color: #FF0000;\"><i class=\"fas fa-ban\" title=\"Box Destroyed\"></i></span>')
ELSE CONCAT('<a href=\"".$url_var."',folderdocinfo_id,'\" id=\"folderdocinfo_link\">',folderdocinfo_id,'</a>')
END,

CASE 
WHEN (unauthorized_destruction = 1 AND freeze = 1) THEN CONCAT(' <span style=\"font-size: 1em; color: #8b0000;\"><i class=\"fas fa-flag\" title=\"Unauthorized Destruction\"></i></span>', ' <span style=\"font-size: 1em; color: #009ACD;\"><i class=\"fas fa-snowflake\" title=\"Freeze\"></i></span>')
WHEN(freeze = 1)  THEN ' <span style=\"font-size: 1em; color: #009ACD;\"><i class=\"fas fa-snowflake\" title=\"Freeze\"></i></span>'
WHEN (unauthorized_destruction = 1) THEN ' <span style=\"font-size: 1em; color: #8b0000;\"><i class=\"fas fa-flag\" title=\"Unauthorized Destruction\"></i></span>'
ELSE ''
END) as folderdocinfo_id_flag,
folderdocinfo_id,
case when length(title) > 25 
then concat(substring(title, 1, 25), '...')
else title end as title,
date,
(SELECT wpqa_wpsc_epa_boxinfo.lan_id FROM wpqa_wpsc_epa_boxinfo WHERE wpqa_wpsc_epa_boxinfo.id = wpqa_wpsc_epa_folderdocinfo.box_id) as epa_contact_email,
(CASE WHEN validation = 1 THEN CONCAT('<span style=\"font-size: 1.3em; color: #008000;\"><i class=\"fas fa-check-circle\" title=\"Validated\"></i></span>',' [',(SELECT user_nicename from wpqa_users WHERE ID = wpqa_wpsc_epa_folderdocinfo.validation_user_id),']')
WHEN rescan = 1 THEN CONCAT('<span style=\"font-size: 1.3em; color: #8b0000;\"><i class=\"fas fa-times-circle\" title=\"Not Validated\"></i></span> ',' <br /><span style=\"color: #FF0000;\"><strong>[Re-scan]</strong></span>')
ELSE '<span style=\"font-size: 1.3em; color: #8b0000;\"><i class=\"fas fa-times-circle\" title=\"Not Validated\"></i></span>'
END) as validation
FROM 
wpqa_wpsc_epa_folderdocinfo
WHERE 1 ".$searchQuery." AND box_id = ".$box_id." order by ".$columnName." ".$columnSortOrder.$row_limit;
$boxRecords = mysqli_query($con, $boxQuery);
$data = array();

while ($row = mysqli_fetch_assoc($boxRecords)) {
    
$decline_icon = '';
$recall_icon = '';
$type = 'folderfile';

if(Patt_Custom_Func::id_in_return($row['folderdocinfo_id'],$type) == 1){
$decline_icon = '<span style="font-size: 1em; color: #FF0000;margin-left:4px;"><i class="fas fa-undo" title="Declined"></i></span>';
}

if(Patt_Custom_Func::id_in_recall($row['folderdocinfo_id'],$type) == 1){
$recall_icon = '<span style="font-size: 1em; color: #000;margin-left:4px;"><i class="far fa-registered" title="Recall"></i></span>';
}

   $data[] = array(
     "folderdocinfo_id"=>$row['folderdocinfo_id'],
     "folderdocinfo_id_flag"=>$row['folderdocinfo_id_flag'].$decline_icon.$recall_icon,
     "title"=>$row['title'],
     "date"=>$row['date'],
     "epa_contact_email"=>$row['epa_contact_email'],
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