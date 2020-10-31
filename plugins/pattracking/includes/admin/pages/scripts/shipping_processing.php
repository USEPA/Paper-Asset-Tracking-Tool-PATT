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
$mask_length = 20;

## Read value
$draw = $_POST['draw'];
$row = $_POST['start'];
$rowperpage = $_POST['length']; // Rows display per page
$columnIndex = $_POST['order'][0]['column']; // Column index
$columnName = $_POST['columns'][$columnIndex]['data']; // Column name
$columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
$searchValue = $_POST['search']['value']; // Search value

## Custom Field value
$searchByTN = str_replace(",", "|", $_POST['searchByTN']);
$searchGeneric = $_POST['searchGeneric'];

$searchShipped = $_POST['searchByShipped'];
$searchDelivered = $_POST['searchByDelivered'];

## Search 

$searchQuery = " ";
if( $searchByTN != '' ){
//    $searchQuery .= " and (tracking_number REGEXP '^(".$searchByTN.")$' ) ";
   $searchQuery .= " and ( tracking_number like '%".$searchByTN."%' ) ";
   
}

if( $searchShipped != '' ){

	if( $searchShipped == 1 ){
	   $searchQuery .= " and (shipped=1) ";
	} else {
	   $searchQuery .= " and (shipped=0) "; 
	}
}

if( $searchDelivered != '' ){
	if( $searchDelivered == 1 ){
	   $searchQuery .= " and (delivered=1) ";
	} else {
	   $searchQuery .= " and (delivered=0) "; 
	}
}



/*
if($searchDelivered == 1){
	if($searchDelivered == 1){
	   $searchQuery .= " and (delivered=1) ";
	} else {
	   $searchQuery .= " and (delivered=0) "; 
	}
}
*/

if($searchGeneric != ''){
/*
   $searchQuery .= " and (tracking_number like '%".$searchGeneric."%' or 
      company_name like '%".$searchGeneric."%' or 
      status like '%".$searchGeneric."%') ";
*/
	$searchQuery .= " and (tracking_number like '%".$searchGeneric."%' or 
		company_name like '%".$searchGeneric."%' or 
		item_id like '%".$searchGeneric."%' or 
		status like '%".$searchGeneric."%') ";
}

if($searchValue != ''){
   $searchQuery .= " and (tracking_number like '%".$searchValue."%' or 
      company_name like '%".$searchValue."%' or 
      status like '%".$searchValue."%') ";
}

## Total number of records without filtering
$sel = mysqli_query($con,"select count(*) as allcount from wpqa_wpsc_epa_shipping_tracking WHERE id <> -99999 AND tracking_number <> ''");
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];

/*
## Total number of records with filtering
$total_filter_rec_query = "select count(id) as allcount FROM wpqa_wpsc_epa_shipping_tracking WHERE 1 ".$searchQuery." and id <> -99999 AND tracking_number <> ''";
$sel = mysqli_query($con,"select count(id) as allcount FROM wpqa_wpsc_epa_shipping_tracking
WHERE 1 ".$searchQuery." and id <> -99999 AND tracking_number <> ''");
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];
*/


//SAVES
//WHEN ReturnX.return_id <> -99999 THEN CONCAT( 'RTN-', ReturnX.return_id)

## Fetch records
$docQuery = "SELECT 
Tracking.id as id,
tracking_number as tracking_number,
company_name as company_name,
status as status,

CASE
WHEN Ticket.request_id <> -99999 THEN Ticket.request_id
WHEN Recall.recall_id <> -99999 THEN CONCAT( 'R-', Recall.recall_id)
WHEN ReturnX.return_id <> -99999 THEN CONCAT( 'D-', ReturnX.return_id)
END as item_id,

CASE 
WHEN shipped = 1 THEN 1
WHEN shipped = 0 THEN 0
END as shipped,

CASE 
WHEN delivered = 1 THEN 1
WHEN delivered = 0 THEN 0
END as delivered

FROM wpqa_wpsc_epa_shipping_tracking Tracking

INNER JOIN wpqa_wpsc_ticket Ticket ON Ticket.id = Tracking.ticket_id
INNER JOIN wpqa_wpsc_epa_recallrequest Recall ON Recall.id = Tracking.recallrequest_id
INNER JOIN wpqa_wpsc_epa_return ReturnX ON ReturnX.id = Tracking.return_id

WHERE 1 ";
// .$searchQuery." and Tracking.id <> -99999 AND tracking_number <> '' order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;

// $docQuerySearch = " and Tracking.id <> -99999 AND tracking_number <> '' order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
$docQuerySearch = " and Tracking.id <> -99999 AND tracking_number <> '' order by ".$columnName." ".$columnSortOrder;

$docQueryWrapped = "SELECT * FROM (".$docQuery.$docQuerySearch.") AS innerTable WHERE 1 ".$searchQuery." limit ".$row.",".$rowperpage;


## Total number of records with filtering
//$total_filter_rec_query = "select count(id) as allcount FROM wpqa_wpsc_epa_shipping_tracking WHERE 1 ".$searchQuery." and id <> -99999 AND tracking_number <> ''";
$total_filter_search = " and Tracking.id <> -99999 AND tracking_number <> '' ";
$total_filter_rec_query = "SELECT count(id) as allcount FROM (".$docQuery.$total_filter_search.") AS innerTable WHERE 1 ".$searchQuery;
$sel = mysqli_query( $con, $total_filter_rec_query );
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

/*
$sel = mysqli_query($con,"select count(id) as allcount FROM wpqa_wpsc_epa_shipping_tracking
WHERE 1 ".$searchQuery." and id <> -99999 AND tracking_number <> ''");
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];
*/




//$docRecords = mysqli_query($con, $docQuery);
$docRecords = mysqli_query($con, $docQueryWrapped);
$data = array();

//$shipped_html_yes = '<span style=\"font-size: 1.3em; color: #008000;\"><i class=\"fas fa-check-circle\" title=\"Shipped\"></i></span>';
$shipped_html_yes = '<span style="font-size: 1.3em; color: #008000;"><i class="fas fa-check-circle" title="Shipped"></i></span>';
$shipped_html_no = '<span style="font-size: 1.3em; color: #8b0000;"><i class="fas fa-times-circle" title="Not Shipped"></i></span>';
$delivered_html_yes = '<span style="font-size: 1.3em; color: #008000;"><i class="fas fa-check-circle" title="Delivered"></i></span>';
$delivered_html_no = '<span style="font-size: 1.3em; color: #8b0000;"><i class="fas fa-times-circle" title="Not Delivered"></i></span>';

while ($row = mysqli_fetch_assoc($docRecords)) {

	$shipping_link_start = "<a href='".Patt_Custom_Func::get_tracking_url($row['tracking_number'])."' target='_blank' />";
	//$shipping_link_end = "</a> <span class='edit_shipping_icon' onclick=\"edit_shipping_info('".$row['id']."')\"><i class='fas fa-edit'></i></span>";
	$shipping_link_end = "</a>";
	

if (substr( strtoupper($row['tracking_number']), 0, 4 ) === "DHL:") {
$tracking_num = substr($row['tracking_number'], 4);
} else {
$tracking_num = $row['tracking_number'];
}
	if( strlen($row['tracking_number']) > $mask_length ) {
		$tracking_num = substr($tracking_num, 0, $mask_length);
		$tracking_num .= '...';
	}
	
	$track = $shipping_link_start.$tracking_num.$shipping_link_end;
	
	$company_name = $row['company_name'];
	
	if ($company_name == 'fedex') {
	    $company_name = 'FedEx';
	} else {
	    $company_name = strtoupper($row['company_name']);
	}
	
	$item_id = $row['item_id'];
	$item_id_link = '';
	if (strpos($item_id, 'R-') !== false) {
	    $item_id_link = '<a href="'.$subfolder_path.'/wp-admin/admin.php?page=recalldetails&id='.$item_id.'" >'.$item_id.'</a>';
// 	} elseif (strpos($item_id, 'RTN-') !== false) {
	} elseif (strpos($item_id, 'D-') !== false) {	
// 	    $item_id_link = '<a href="'.$subfolder_path.'/wp-admin/admin.php?page=returndetails&id='.$item_id.'" >'.$item_id.'</a>';
	    $item_id_link = '<a href="'.$subfolder_path.'/wp-admin/admin.php?page=declinedetails&id='.$item_id.'" >'.$item_id.'</a>';
	} else {
		$item_id_link = '<a href="'.$subfolder_path.'/wp-admin/admin.php?page=wpsc-tickets&id='.$item_id.'" >'.$item_id.'</a>';
	}
	
	if( $row['shipped'] == 1 ) {
		$shipped_icon = $shipped_html_yes;
	} else {
		$shipped_icon = $shipped_html_no;
	}
	
	if( $row['delivered'] == 1 ) {
		$delivered_icon = $delivered_html_yes;
	} else {
		$delivered_icon = $delivered_html_no;
	}
	
	

	$data[] = array(
		"id"=>$row['id'],
// 		"item_id"=>$item_id,
// 		"item_id"=>$row['item_id'], 
		"item_id"=>$item_id_link, 
//		"tracking_number"=>$shipping_link_start.$row['tracking_number'].$shipping_link_end,
		"tracking_number"=>$track,	
		"company_name"=>$company_name,
		"status"=>$row['status'],
// 		"shipped"=>$row['shipped'],
		"shipped"=>$shipped_icon,		
// 		"delivered"=>$row['delivered']
		"delivered"=>$delivered_icon
	);
}

## Response
$response = array(
  "draw" => intval($draw),
  "iTotalRecords" => $totalRecords,
  "iTotalDisplayRecords" => $totalRecordwithFilter,
  "aaData" => $data,
  "Query" => $docQueryWrapped,
  "searchByTN" => $searchByTN,
  "searchGeneric" => $searchGeneric,
  "sel" => $sel,
  "searchValue" => $searchValue,
  "total_filter_rec_query" => $total_filter_rec_query
);

function check_nonce($nonce, $optional_salt='')
{
    $lasthour = date("G")-1<0 ? date('Ymd').'23' : date("YmdG")-1;
    if (hash_hmac('sha256', session_id().$optional_salt, date("YmdG").'someSalt'.$_SERVER['REMOTE_ADDR']) == $nonce || 
        hash_hmac('sha256', session_id().$optional_salt, $lasthour.'someSalt'.$_SERVER['REMOTE_ADDR']) == $nonce)
    {
        return true;
    } else {
        return false;
    }
}

$ret = array();
header('Content-Type: application/json');
if (check_nonce($_POST['nonce'], $_SESSION['current_page']))
{
echo json_encode($response);
} else {
echo 'failed check';
}

exit;

