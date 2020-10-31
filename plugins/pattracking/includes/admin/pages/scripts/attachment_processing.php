<?php
$WP_PATH = implode("/", (explode("/", $_SERVER["PHP_SELF"], -8)));
require_once($_SERVER['DOCUMENT_ROOT'].$WP_PATH.'/wp-config.php');
	
$host = DB_HOST; /* Host name */
$user = DB_USER; /* User */
$password = DB_PASSWORD; /* Password */
$dbname = DB_NAME; /* Database name */

global $wpdb, $current_user, $wpscfunction;
$agent_permissions = $wpscfunction->get_current_agent_permissions();

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
$searchGeneric = $_POST['searchGeneric'];

## Search 
$searchQuery = " ";
if($searchGeneric != ''){
   $searchQuery .= " and (folderdocinfofile_id like '%".$searchGeneric."%' or 
      file_name like '%".$searchGeneric."%' or 
      object_location like '%".$searchGeneric."%' or
      file_object_id like '%".$searchGeneric."%' or
      title like '%".$searchGeneric."%' or
      description like '%".$searchGeneric."%' or
      tags like '%".$searchGeneric."%'
      ) ";
}

if($searchValue != ''){
   $searchQuery .= " and (folderdocinfofile_id like '%".$searchGeneric."%' or 
      file_name like '%".$searchGeneric."%' or 
      object_location like '%".$searchGeneric."%' or
      file_object_id like '%".$searchGeneric."%' or
      title like '%".$searchGeneric."%' or
      description like '%".$searchGeneric."%' or
      tags like '%".$searchGeneric."%') ";
}

## Total number of records without filtering
$sel = mysqli_query($con,"select count(*) as allcount from wpqa_wpsc_epa_folderdocinfo_files
");

$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];

## Total number of records with filtering
//gets all folder/files for a user with the requester role, only shows their own folder/files
$sel = mysqli_query($con,"select count(id) as allcount FROM wpqa_wpsc_epa_folderdocinfo_files
WHERE 1 ".$searchQuery);

$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

## Fetch records
//if user is a requester, only show their own requests
$docQuery = "SELECT 
folderdocinfofile_id,
file_name,
object_location,
file_object_id,
title,
description,
tags
FROM wpqa_wpsc_epa_folderdocinfo_files
WHERE 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;

$docRecords = mysqli_query($con, $docQuery);
$data = array();

while ($row = mysqli_fetch_assoc($docRecords)) {

   $data[] = array(
     "folderdocinfofile_id"=>$row['folderdocinfofile_id'],
     "file_name"=>$row['file_name'],
     "object_location"=>$row['object_location'],
     "file_object_id"=>$row['file_object_id'],
     "title"=>$row['title'],
     "description"=>$row['description'],
     "tags"=>$row['tags']
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