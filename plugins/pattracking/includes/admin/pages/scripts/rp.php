<?php
$WP_PATH = implode("/", (explode("/", $_SERVER["PHP_SELF"], -8)));
require_once($_SERVER['DOCUMENT_ROOT'].$WP_PATH.'/wp-config.php');
	
$host = DB_HOST; /* Host name */
$user = DB_USER; /* User */
$password = DB_PASSWORD; /* Password */
$dbname = DB_NAME; /* Database name */

$subfolder_path = site_url( '', 'relative'); 

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

$searchByBoxID = str_replace(",", "|", $_POST['searchByBoxID']);
$searchByProgramOffice = $_POST['searchByProgramOffice'];
$searchByDigitizationCenter = $_POST['searchByDigitizationCenter'];
$searchGeneric = $_POST['searchGeneric'];

## 
$where = [
	// 'id' => 19, 
	// 'id' => [19, 20], 
	// 'recall_id' => 19, 
// 	'recall_id' => ['0000001', '0000002'], 
// 	'recall_id' => '', 
	// 'recall_status_id' => 5,
	// 'program_office_id' => 2,
// 	'digitization_center' => 'East', 
//	'digitization_center' => $searchByDigitizationCenter,
	'filter' => [
		'records_per_page' => $rowperpage,
// 		'paged' => $draw,
		'orderby' => $columnName,
		'order' => $columnSortOrder
	]
]; 


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
$boxQuery = "SELECT 
CONCAT(
'<a href=\"".$url_var."',folderdocinfo_id,'\" id=\"folderdocinfo_link\">',folderdocinfo_id,'</a>',
CASE WHEN unauthorized_destruction = 1 THEN ' <span style=\"font-size: 1em; color: #8b0000;\"><i class=\"fas fa-flag\" title=\"Unauthorized Destruction\"></i></span>'
ELSE '' 
END) as folderdocinfo_id_flag,
folderdocinfo_id,
case when length(title) > 25 
then concat(substring(title, 1, 25), '...')
else title end as title,
date,
epa_contact_email,
(CASE WHEN validation = 1 THEN CONCAT('<span style=\"font-size: 1.3em; color: #008000;\"><i class=\"fas fa-check-circle\" title=\"Validated\"></i></span>',' (',(SELECT user_nicename from wpqa_users WHERE ID = wpqa_wpsc_epa_folderdocinfo.validation_user_id),')')
ELSE '<span style=\"font-size: 1.3em; color: #8b0000;\"><i class=\"fas fa-times-circle\" title=\"Not Validated\"></i></span>'
END) as validation
FROM 
wpqa_wpsc_epa_folderdocinfo
WHERE 1 ".$searchQuery." AND box_id = ".$box_id." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
$boxRecords = mysqli_query($con, $boxQuery);
$data = array();

while ($row = mysqli_fetch_assoc($boxRecords)) {
   $data[] = array(
     "folderdocinfo_id"=>$row['folderdocinfo_id'],
     "folderdocinfo_id_flag"=>$row['folderdocinfo_id_flag'],
     "title"=>$row['title'],
     "date"=>$row['date'],
     "epa_contact_email"=>$row['epa_contact_email'],
     "validation"=>$row['validation']
   );
}

//Podbelski START
$data2 = array();

//$where = [];




$recall_array = Patt_Custom_Func::get_recall_data($where);

foreach($recall_array as $row) {
	
/*
	$status_term_obj = get_term_by('name', $row->recall_status, 'wppatt_recall_statuses');
	
	$status_style = "background-color:"..";color:"..";";
*/

	$status_term_id = $row->recall_status_id;
	$status_background = get_term_meta($status_term_id, 'wppatt_recall_status_background_color', true);
	$status_color = get_term_meta($status_term_id, 'wppatt_recall_status_color', true);
	$status_style = "background-color:".$status_background.";color:".$status_color.";";



	$date_expiration = $row->expiration_date;
	$date_expiration = "90 Days";
	
/*
	$req_uri = $_SERVER['REQUEST_URI'];
	$path = substr($req_uri,0,strrpos($req_uri,'/'));
*/
	
	$data2[] = array(
// 		"recall_id"=>"<a href='/wordpress3/wp-admin/admin.php?page=recalldetails&id=R-".$row->recall_id."' >R-".$row->recall_id."</a>",
		"recall_id"=>"<a href='".$subfolder_path."/wp-admin/admin.php?page=recalldetails&id=R-".$row->recall_id."' >R-".$row->recall_id."</a>", 		
		"folderdocinfo_id_flag"=>$row->recall_id,
		"status"=>"<span class='wpsp_admin_label' style='".$status_style."'>".$row->recall_status."</span>", 
// 		"status"=>"<span class='wpsp_admin_label' style=''>".$row->recall_status."</span>", 
		"date_updated"=>$row->updated_date,
		"date_requested"=>$row->request_date,
		"date_returned"=>$row->return_date, 
		"date_received"=>$row->request_receipt_date, 		
		"date_expiration"=>$date_expiration, 
		"shipping_tracking"=>$row->tracking_number,
   );

/*
		"recall_id"=>$row->id,
		"folderdocinfo_id_flag"=>$row->id,
		"status"=>$row->recall_status_id,
		"date_updated"=>$row->updated_date,
		"date_requested"=>$row->request_date,
		"date_returned"=>$row->return_date, 
		"date_received"=>$row->request_receipt_date, 		
		"date_expiration"=>$row->expiration_date, 
		"shipping_tracking"=>$row->shipping_tracking_id,
*/			

/*	
		"recall_id"=>$row->id,
		"folderdocinfo_id_flag"=>$row->id,
		"status"=>$row->recall_status_id,
		"date_updated"=>$row->updated_date,
		"date_requested"=>$row->request_date,
		"date_received"=>$row->request_receipt_date,
		"date_returned"=>$row->id,
		"date_expiration"=>$row->expiration_date,
		"shipping_tracking"=>$row->shipping_tracking_id
*/

   
   
   

/*
	echo $row->id;			
	echo $row->recall_status_id;			
	echo $row->updated_date;			
	echo $row->request_date;			
	echo $row->request_receipt_date;			
	echo $row->return_date;			
	echo $row->expiration_date;			
	echo $row->recall_status_id;		
	echo "<br>"; 
*/
}


/*
while ($row = mysqli_fetch_assoc($boxRecords)) {
   $data2[] = array(
     "folderdocinfo_id"=>$row['folderdocinfo_id'],
     "folderdocinfo_id_flag"=>$row['folderdocinfo_id_flag'],
     "title"=>$row['title'],
     "date"=>$row['date'],
     "epa_contact_email"=>$row['epa_contact_email'],
     "validation"=>$row['validation']
   );
}
*/

//Podbelski END

## Response
$response = array(
  "draw" => intval($draw),
  "iTotalRecords" => $totalRecords,
  "iTotalDisplayRecords" => $totalRecordwithFilter,
  "aaData" => $data2
);
//dd($response);

echo json_encode($response);