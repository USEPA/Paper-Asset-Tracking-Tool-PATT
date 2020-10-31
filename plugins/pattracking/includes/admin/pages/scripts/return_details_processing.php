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

$subfolder_path = site_url( '', 'relative'); 

## Read value
$draw = $_POST['draw'];
$row = $_POST['start'];
$rowperpage = $_POST['length']; // Rows display per page
$columnIndex = $_POST['order'][0]['column']; // Column index
$columnName = $_POST['columns'][$columnIndex]['data']; // Column name
$columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
$searchValue = $_POST['search']['value']; // Search value

## Custom Field value
// $searchByBoxID = str_replace(",", "|", $_POST['searchByBoxID']);
$searchByBoxID = str_replace(",", "|", $_POST['searchByID']);
$searchByProgramOffice = $_POST['searchByProgramOffice'];
$searchByDigitizationCenter = $_POST['searchByDigitizationCenter'];
$searchGeneric = $_POST['searchGeneric'];


if($_POST['searchByID']) {
	$searchByID = explode(',',$_POST['searchByID']);
}

//
// Test Data
//

//$searchByID = ['0000001-1','0000002-2-01-13', '0000002-3', '0000001-1-01-1', '0000001-3'];



/*
## Search 
$searchQuery = " ";
if($searchByBoxID != ''){
//    $searchQuery .= " and (a.box_id REGEXP '^(".$searchByBoxID.")$' ) ";
   $searchQuery .= "  (a.box_id REGEXP '^(".$searchByBoxID.")$' ) ";
}

if($searchByProgramOffice != ''){
   $searchQuery .= " and (c.office_acronym='".$searchByProgramOffice."') ";
}

if($searchByDigitizationCenter != ''){
   $searchQuery .= " and (e.name ='".$searchByDigitizationCenter."') ";
}

if($searchGeneric != ''){
   $searchQuery .= " and (a.box_id like '%".$searchGeneric."%' or 
      b.request_id like '%".$searchGeneric."%' or 
      e.name like '%".$searchGeneric."%' or
      c.office_acronym like '%".$searchGeneric."%') ";
}

if($searchValue != ''){
   $searchQuery .= " and (a.box_id like '%".$searchValue."%' or 
      b.request_id like '%".$searchValue."%' or 
      e.name like '%".$searchValue."%' or
      c.office_acronym like '%".$searchValue."%') ";
}

## Total number of records without filtering
$sel = mysqli_query($con,"select count(*) as allcount from wpqa_wpsc_epa_boxinfo WHERE id <> -99999");
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];

## Total number of records with filtering
$sel = mysqli_query($con,"select count(a.box_id) as allcount FROM wpqa_wpsc_epa_boxinfo as a
INNER JOIN wpqa_wpsc_ticket as b ON a.ticket_id = b.id
INNER JOIN wpqa_wpsc_epa_program_office as c ON a.program_office_id = c.office_code
INNER JOIN wpqa_wpsc_epa_storage_location as d ON a.storage_location_id = d.id
INNER JOIN wpqa_terms e ON e.term_id = d.digitization_center
WHERE 1 ".$searchQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

## Fetch records
$boxQuery = "
SELECT 
a.box_id,
CONCAT(

CASE WHEN 
(
SELECT sum(freeze) FROM  wpqa_wpsc_epa_folderdocinfo WHERE a.id = box_id
) <> 0 AND
a.box_destroyed > 0 


THEN CONCAT('<a href=\"admin.php?page=boxdetails&pid=boxsearch&id=',a.box_id,'\" style=\"color: #FF0000 !important;\">',a.box_id,'</a> <span style=\"font-size: 1em; color: #FF0000;\"><i class=\"fas fa-ban\" title=\"Box Destroyed\"></i></span>')

WHEN a.box_destroyed > 0 


THEN CONCAT('<a href=\"admin.php?page=boxdetails&pid=boxsearch&id=',a.box_id,'\" style=\"color: #FF0000 !important; text-decoration: line-through;\">',a.box_id,'</a> <span style=\"font-size: 1em; color: #FF0000;\"><i class=\"fas fa-ban\" title=\"Box Destroyed\"></i></span>')


ELSE CONCAT('<a href=\"admin.php?page=boxdetails&pid=boxsearch&id=',a.box_id,'\">',a.box_id,'</a>')
END,


CASE 
WHEN (SELECT sum(freeze = 1) FROM wpqa_wpsc_epa_folderdocinfo WHERE box_id = a.id) > 0 THEN ' <span style=\"font-size: 1em; color: #009ACD;\"><i class=\"fas fa-snowflake\" title=\"Freeze\"></i></span>'
ELSE '' 
END,
CASE 
WHEN (SELECT sum(unauthorized_destruction = 1) FROM wpqa_wpsc_epa_folderdocinfo WHERE box_id = a.id) > 0 THEN ' <span style=\"font-size: 1em; color: #8b0000;\"><i class=\"fas fa-flag\" title=\"Unauthorized Destruction\"></i></span>'
ELSE '' 
END
) as box_id_flag,
CONCAT('<a href=admin.php?page=wpsc-tickets&id=',b.request_id,'>',b.request_id,'</a>') as request_id, 
e.name as location, 
c.office_acronym as acronym,
CONCAT(
CASE 

WHEN (SELECT sum(validation = 1) FROM wpqa_wpsc_epa_folderdocinfo WHERE box_id = a.id) = 0 AND (SELECT count(id) FROM wpqa_wpsc_epa_folderdocinfo WHERE box_id = a.id) = 0
THEN
''

WHEN (SELECT sum(validation = 1) FROM wpqa_wpsc_epa_folderdocinfo WHERE box_id = a.id) != 0 AND (SELECT sum(validation = 1) FROM wpqa_wpsc_epa_folderdocinfo WHERE box_id = a.id) < (SELECT count(id) FROM wpqa_wpsc_epa_folderdocinfo WHERE box_id = a.id)
THEN 
'<span style=\"font-size: 1.3em; color: #FF8C00;\"><i class=\"fas fa-times-circle\" title=\"Not Validated\"></i></span> '

WHEN (SELECT sum(validation = 1) FROM wpqa_wpsc_epa_folderdocinfo WHERE box_id = a.id) = 0 AND (SELECT sum(validation = 1) FROM wpqa_wpsc_epa_folderdocinfo WHERE box_id = a.id) < (SELECT count(id) FROM wpqa_wpsc_epa_folderdocinfo WHERE box_id = a.id)
THEN 
'<span style=\"font-size: 1.3em; color: #8b0000;\"><i class=\"fas fa-times-circle\" title=\"Not Validated\"></i></span> '

WHEN (SELECT sum(validation = 1) FROM wpqa_wpsc_epa_folderdocinfo WHERE box_id = a.id) = (SELECT count(id) FROM wpqa_wpsc_epa_folderdocinfo WHERE box_id = a.id)
THEN 
'<span style=\"font-size: 1.3em; color: #008000;\"><i class=\"fas fa-check-circle\" title=\"Validated\"></i></span> '

ELSE '' 
END,

CASE 
WHEN (SELECT count(id) FROM wpqa_wpsc_epa_folderdocinfo WHERE box_id = a.id) != 0
THEN
CONCAT((SELECT sum(validation = 1) FROM wpqa_wpsc_epa_folderdocinfo WHERE box_id = a.id), '/', (SELECT count(id) FROM wpqa_wpsc_epa_folderdocinfo WHERE box_id = a.id))
ELSE '-'
END
) as validation
FROM wpqa_wpsc_epa_boxinfo as a
INNER JOIN wpqa_wpsc_ticket as b ON a.ticket_id = b.id
INNER JOIN wpqa_wpsc_epa_program_office as c ON a.program_office_id = c.office_code
INNER JOIN wpqa_wpsc_epa_storage_location as d ON a.storage_location_id = d.id
INNER JOIN wpqa_terms e ON e.term_id = d.digitization_center
WHERE  ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;

$boxRecords = mysqli_query($con, $boxQuery);
$data = array();

while ($row = mysqli_fetch_assoc($boxRecords)) {
   $data[] = array(
     "box_id"=>$row['box_id'], 
//      "box_id"=>$_POST['searchByID'], 
     "box_id_flag"=>$row['box_id_flag'],
     "request_id"=>$row['request_id'],
//      "location"=>$row['location'],
     "location"=>$_POST['searchByID'],
     "acronym"=>$row['acronym'],
     "validation"=>$row['validation'],     
   );
}
## Response
$response = array(
  "draw" => intval($draw),
  "iTotalRecords" => $totalRecords,
  "iTotalDisplayRecords" => $totalRecordwithFilter,
  "aaData" => $data
);
*/

//
// NEW SEARCH & RESPONSE
//

$data2 = array();
$error_array = array();

foreach( $searchByID as $item ) {
	
	$item_details = Patt_Custom_Func::get_box_file_details_by_id($item);
	$details_array = json_decode(json_encode($item_details), true);
	

	if ( $details_array == false ) {
		$error_array[$item]['search_error'] = true;
	} else {
		$error_array[$item]['search_error'] = false;
	}
	
	if( $details_array['type'] == 'Box' ) {
		
		$pieces = explode('-', $details_array['box_id'],2 );
		$ticket_id = $pieces[0];
		$link_str_box = "<a href='".$subfolder_path."/wp-admin/admin.php?page=boxdetails&pid=boxsearch&id=".
							$details_array['box_id']."' target='_blank' >".$details_array['box_id']."</a>";
		$link_str_request = "<a href='".$subfolder_path."/wp-admin/admin.php?page=wpsc-tickets&id=".
								$ticket_id."' target='_blank'>".$ticket_id."</a>";					
							
		
		$data2[] = array(
		     "box_id"=>$details_array['box_id'], 
		     "box_id_flag"=>$link_str_box,
		     "title"=>'[Boxes do not have titles]',
		     "request_id"=>$link_str_request,
		     "program_office"=>$details_array['office_acronym'] . ': ' . $details_array['office_name'],
		     "validation"=>$_POST['searchByID'],     
		   );
	} elseif ($details_array['type'] == 'Folder/Doc') {
		
		$pieces = explode('-', $details_array['Folderdoc_Info_id'],2 );
		$ticket_id = $pieces[0];
		
		$link_str_ff = "<a href='".$subfolder_path."/wp-admin/admin.php?pid=boxsearch&page=filedetails&id=".
							$details_array['Folderdoc_Info_id']."' target='_blank' >".$details_array['Folderdoc_Info_id']."</a>";
		$link_str_request = "<a href='".$subfolder_path."/wp-admin/admin.php?page=wpsc-tickets&id=".
								$ticket_id."' target='_blank'>".$ticket_id."</a>";					
							
		
		$data2[] = array(
		     "box_id"=>$details_array['Folderdoc_Info_id'], 
		     "box_id_flag"=>$link_str_ff,
		     "title"=>$details_array['title'],
		     "request_id"=>$link_str_request,
		     "program_office"=>$details_array['office_acronym'] . ': ' . $details_array['office_name'],
		     "validation"=>$_POST['searchByID'],     
		   );
	}
}




$response2 = array(
  "draw" => intval($draw),
  "iTotalRecords" => count($searchByID),
  "iTotalDisplayRecords" => count($searchByID),
  "aaData" => $data2,
  "errors" => $error_array,
  "alerts" => 'go get it'
);




echo json_encode($response2);