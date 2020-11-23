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

function calculate_time_span($seconds)
{  
 $year = floor($seconds /31556926);
$months = floor($seconds /2629743);
$week=floor($seconds /604800);
$day = floor($seconds /86400); 
$hours = floor($seconds / 3600);
 $mins = floor(($seconds - ($hours*3600)) / 60); 
$secs = floor($seconds % 60);
 if($seconds < 60) $time = $secs." seconds ago";
 else if($seconds < 3600 ) $time =($mins==1)?$mins." minute ago":$mins." minutes ago";
 else if($seconds < 86400) $time = ($hours==1)?$hours." hour ago":$hours." hours ago";
 else if($seconds < 604800) $time = ($day==1)?$day." day ago":$day." days ago";
 else if($seconds < 2629743) $time = ($week==1)?$week." week ago":$week." weeks ago";
 else if($seconds < 31556926) $time =($months==1)? $months." month ago":$months." months ago";
 else $time = ($year==1)? $year." year ago":$year." years ago";
return $time; 
}  

## Read value
$draw = $_POST['draw'];
$row = $_POST['start'];
$rowperpage = $_POST['length']; // Rows display per page
$columnIndex = $_POST['order'][0]['column']; // Column index
$columnName = $_POST['columns'][$columnIndex]['data']; // Column name
$columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
$searchValue = $_POST['search']['value']; // Search value

if($columnName == 'ticket_priority') {
$columnName = 'ticket_priority_order';
} elseif($columnName == 'ticket_status') {
$columnName = 'ticket_status_order';
} else {
$columnName = $_POST['columns'][$columnIndex]['data']; // Column name
}

## Custom Field value
$searchByRequestID = str_replace(",", "|", $_POST['searchByRequestID']);
$searchByDigitizationCenter = $_POST['searchByDigitizationCenter'];
$searchGeneric = $_POST['searchGeneric'];
$searchByStatus = $_POST['searchByStatus'];
$searchByPriority = $_POST['searchByPriority'];
$searchByRecallDecline = $_POST['searchByRecallDecline'];
$currentUser = $_POST['currentUser'];
## User Search
$searchByUser = $_POST['searchByUser'];
$searchByUserAAVal = $_POST['searchByUserAAVal'];
$searchByUserAAName = str_replace(",", "|", $_POST['searchByUserAAName']);
$searchByUserAANameQuoted = str_replace(",", "','", $_POST['searchByUserAAName']);
$searchByUserAANameQuoted = "'".$searchByUserAANameQuoted."'";

## Search 
$searchQuery = " ";
$searchHaving = " ";
$locationarray = array("east", "west", "east cui", "west cui", "not assigned");

if($searchByUser == 'mine') {
   $searchQuery .= " and (a.customer_name ='".$currentUser."') ";    
}

if($searchByUser == 'search for user') {
	
	if( strlen($searchByUserAAName) == 0  ) {
		//$searchQuery .= "";
	} else {
		$searchQuery .= " and (a.customer_name IN (".$searchByUserAANameQuoted.")) ";	
	}
	
		
	
	//OLD search when looking at the agents assigned the box statuses
    //$searchQuery .= " and (a.customer_name REGEXP '^(".$searchByUserAAName.")$' ) ";
/*
   	$array_of_wp_user_id = Patt_Custom_Func::translate_user_id($searchByUserAAVal, 'wp_user_id');
   	$user_id_str = '';
	foreach( $array_of_wp_user_id as $id ) {
		$user_id_str .= $id.', ';
	}
	$user_id_str = substr($user_id_str, 0, -2);
	
	$box_ids_for_users = '';
	$mini_query = "select distinct box_id from wpqa_wpsc_epa_boxinfo_userstatus where user_id IN (".$user_id_str.")";
	$mini_records = mysqli_query($con, $mini_query);
	while ($rox = mysqli_fetch_assoc($mini_records)) {
		$box_ids_for_users .= $rox['box_id'].", ";
	}
	$box_ids_for_users = substr($box_ids_for_users, 0, -2);
	
	if( $user_id_str == '' ) {
	} else {
		$searchQuery .= " and (a.id IN (".$box_ids_for_users.")) ";	
	}      
*/
}


if($searchByRequestID != ''){
   $searchQuery .= " and (a.request_id REGEXP '^(".$searchByRequestID.")$' ) ";
}

if($searchByStatus != ''){
   $searchQuery .= " and (a.ticket_status='".$searchByStatus."') ";
}

if($searchByPriority != ''){
   $searchQuery .= " and (a.ticket_priority='".$searchByPriority."') ";
}

if($searchByDigitizationCenter != ''){
   $searchHaving = " HAVING location like '%".$searchByDigitizationCenter."%' ";
}

//Get term_ids for Recall status slugs
$status_recall_denied_term_id = Patt_Custom_Func::get_term_by_slug( 'recall-denied' );	 // 878
$status_recall_cancelled_term_id = Patt_Custom_Func::get_term_by_slug( 'recall-cancelled' ); //734
$status_recall_complete_term_id = Patt_Custom_Func::get_term_by_slug( 'recall-complete' ); //733

$status_decline_cancelled_term_id = Patt_Custom_Func::get_term_by_slug( 'decline-cancelled' );	 // 791
$status_decline_completed_term_id = Patt_Custom_Func::get_term_by_slug( 'decline-complete' ); //754
    
if($searchByRecallDecline != ''){

        if($searchByRecallDecline == 'Recall') {
            $searchQuery .= "and (
            COALESCE(x.recall_status_id, h.recall_status_id) NOT IN (".$status_recall_denied_term_id.",".$status_recall_cancelled_term_id.",".$status_recall_complete_term_id.")
            )";
        }

        if($searchByRecallDecline == 'Decline') {
            $searchQuery .= "and (
            i.return_id <> '' OR j.return_id <> ''
            )";
        }

}

if($searchGeneric != ''){
if(in_array(strtolower($searchGeneric), $locationarray)){
   $searchHaving = " HAVING location like '%".$searchGeneric."%' ";
} else {
   $searchQuery .= " and (a.request_id like '%".$searchGeneric."%' or
      a.customer_name like '%".$searchGeneric."%') ";    
}

}

if($searchValue != ''){
if(in_array(strtolower($searchGeneric), $locationarray)){
   $searchHaving = " HAVING location like '%".$searchGeneric."%' ";
} else {
   $searchQuery .= " and (a.request_id like '%".$searchGeneric."%' or
      a.customer_name like '%".$searchGeneric."%') ";    
}
}

## Total number of records without filtering Filter out inactive (initially deleted tickets)
$sel = mysqli_query($con,"select count(*) as allcount FROM (select COUNT(DISTINCT a.request_id) as allcount, GROUP_CONCAT(DISTINCT e.name ORDER BY e.name ASC SEPARATOR ', ') as location
FROM wpqa_wpsc_ticket as a
INNER JOIN wpqa_wpsc_epa_boxinfo as b ON a.id = b.ticket_id
INNER JOIN wpqa_wpsc_epa_storage_location as d ON b.storage_location_id = d.id
INNER JOIN wpqa_terms e ON e.term_id = d.digitization_center
WHERE a.id <> -99999 AND a.active <> 0 group by a.request_id) t");
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];

## Total number of records with filtering
$sel = mysqli_query($con,"select count(*) as allcount FROM (select COUNT(DISTINCT a.request_id) as allcount, GROUP_CONCAT(DISTINCT e.name ORDER BY e.name ASC SEPARATOR ', ') as location
FROM wpqa_wpsc_ticket as a
INNER JOIN wpqa_wpsc_epa_boxinfo as b ON a.id = b.ticket_id
INNER JOIN wpqa_wpsc_epa_storage_location as d ON b.storage_location_id = d.id
INNER JOIN wpqa_terms e ON e.term_id = d.digitization_center
LEFT JOIN wpqa_wpsc_epa_folderdocinfo as z ON z.box_id = b.id
LEFT JOIN wpqa_wpsc_epa_recallrequest AS x ON (x.box_id = b.id AND x.folderdoc_id = '-99999')
LEFT JOIN wpqa_wpsc_epa_recallrequest AS h ON (h.folderdoc_id = z.id AND h.folderdoc_id <> '-99999')

LEFT JOIN (   SELECT a.box_id, a.return_id
   FROM   wpqa_wpsc_epa_return_items a
   LEFT JOIN  wpqa_wpsc_epa_return b ON a.return_id = b.id
   WHERE box_id <> '-99999' AND b.return_status_id NOT IN (".$status_decline_cancelled_term_id.",".$status_decline_completed_term_id.")
   GROUP  BY box_id ) AS i ON i.box_id = b.id
LEFT JOIN (   SELECT a.folderdoc_id, a.return_id
   FROM   wpqa_wpsc_epa_return_items a
   LEFT JOIN  wpqa_wpsc_epa_return b ON a.return_id = b.id
   WHERE folderdoc_id <> '-99999' AND b.return_status_id NOT IN (".$status_decline_cancelled_term_id.",".$status_decline_completed_term_id.")
   GROUP  BY folderdoc_id )  AS j ON j.folderdoc_id = z.id
WHERE 1 ".$searchQuery." AND a.active <> 0 AND a.id <> -99999 group by a.request_id ".$searchHaving.") t");

$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

$status_id = 5;

## Fetch records
$boxQuery = "
SELECT
a.id as request_id,
a.request_id as patt_request_id,
CONCAT(
'<a href=\"admin.php?page=wpsc-tickets&id=',a.request_id,'\">',a.request_id,'</a> ',
CASE 
WHEN sum(b.box_destroyed = 1) > 0 THEN CONCAT ('<span style=\"font-size: 1em; color: #FF0000;\"><i class=\"fas fa-ban\" title=\"Box Destroyed\"></i></span>')
ELSE ''
END,
CASE
WHEN sum(f.freeze = 1) > 0 THEN CONCAT (' <span style=\"font-size: 1em; color: #009ACD;\"><i class=\"fas fa-snowflake\" title=\"Freeze\"></i></span>')
ELSE ''
END,
CASE
WHEN sum(f.unauthorized_destruction = 1) > 0 THEN CONCAT(' <span style=\"font-size: 1em; color: #8b0000;\"><i class=\"fas fa-flag\" title=\"Unauthorized Destruction\"></i></span>')
ELSE ''
END
) as request_id_flag,

CONCAT(
'<span class=\"wpsp_admin_label\" style=\"background-color:',
(SELECT meta_value from wpqa_termmeta where meta_key = 'wpsc_priority_background_color' AND term_id = a.ticket_priority),
';color:',
(SELECT meta_value from wpqa_termmeta where meta_key = 'wpsc_priority_color' AND term_id = a.ticket_priority),
';\">',
(SELECT name from wpqa_terms where term_id = a.ticket_priority),
'</span>') as ticket_priority,

CONCAT(
'<span class=\"wpsp_admin_label\" style=\"background-color:',
(SELECT meta_value from wpqa_termmeta where meta_key = 'wpsc_status_background_color' AND term_id = a.ticket_status),
';color:',
(SELECT meta_value from wpqa_termmeta where meta_key = 'wpsc_status_color' AND term_id = a.ticket_status),
';\">',
(SELECT name from wpqa_terms where term_id = a.ticket_status),
'</span>') as ticket_status,

CASE 
WHEN a.ticket_priority = 621
THEN
1
WHEN a.ticket_priority = 9
THEN
2
WHEN a.ticket_priority = 8
THEN
3
WHEN a.ticket_priority = 7
THEN
4
ELSE
999
END
 as ticket_priority_order,

CASE 
WHEN a.ticket_status = 3
THEN
1
WHEN a.ticket_status = 4
THEN
2
WHEN a.ticket_status = 670
THEN
3
WHEN a.ticket_status = 5
THEN
4
WHEN a.ticket_status = 63
THEN
5
WHEN a.ticket_status = 69
THEN
6
ELSE
999
END
 as ticket_status_order,

CONCAT((SELECT meta_value FROM wpqa_usermeta WHERE meta_key = 'first_name' AND user_id = g.ID), ' ', (SELECT meta_value FROM wpqa_usermeta WHERE meta_key = 'last_name' AND user_id = g.ID)) as full_name,
CONCAT (
CASE
WHEN ((SELECT meta_value FROM wpqa_usermeta WHERE meta_key = 'first_name' AND user_id = g.ID) <> '') AND ((SELECT meta_value FROM wpqa_usermeta WHERE meta_key = 'last_name' AND user_id = g.ID) <> '') THEN CONCAT ( '<a href=\"#\" style=\"color: #000000 !important;\" data-toggle=\"tooltip\" data-placement=\"bottom\" data-html=\"true\" aria-label=\"Name\" title=\"',
g.user_login
,'\">',

(
    CASE WHEN length(CONCAT((SELECT meta_value FROM wpqa_usermeta WHERE meta_key = 'first_name' AND user_id = g.ID), ' ', (SELECT meta_value FROM wpqa_usermeta WHERE meta_key = 'last_name' AND user_id = g.ID))) > 15 THEN
        CONCAT(LEFT(CONCAT((SELECT meta_value FROM wpqa_usermeta WHERE meta_key = 'first_name' AND user_id = g.ID), ' ', (SELECT meta_value FROM wpqa_usermeta WHERE meta_key = 'last_name' AND user_id = g.ID)), 15), '...')
    ELSE CONCAT((SELECT meta_value FROM wpqa_usermeta WHERE meta_key = 'first_name' AND user_id = g.ID), ' ', (SELECT meta_value FROM wpqa_usermeta WHERE meta_key = 'last_name' AND user_id = g.ID))
    END
)

,'</a>' )
ELSE g.user_login
END
) as customer_name,

a.date_updated as date_updated,

GROUP_CONCAT(DISTINCT e.name ORDER BY e.name ASC SEPARATOR ', ') as location

FROM wpqa_wpsc_ticket as a
INNER JOIN wpqa_wpsc_epa_boxinfo as b ON a.id = b.ticket_id
INNER JOIN wpqa_wpsc_epa_storage_location as d ON b.storage_location_id = d.id
INNER JOIN wpqa_terms e ON e.term_id = d.digitization_center
INNER JOIN wpqa_wpsc_epa_folderdocinfo f ON f.box_id = b.id
LEFT JOIN wpqa_users g ON g.user_email = a.customer_email
LEFT JOIN wpqa_wpsc_epa_folderdocinfo as z ON z.box_id = b.id
LEFT JOIN wpqa_wpsc_epa_recallrequest AS x ON (x.box_id = b.id AND x.folderdoc_id = '-99999')
LEFT JOIN wpqa_wpsc_epa_recallrequest AS h ON (h.folderdoc_id = z.id AND h.folderdoc_id <> '-99999')

LEFT JOIN (   SELECT a.box_id, a.return_id
   FROM   wpqa_wpsc_epa_return_items a
   LEFT JOIN  wpqa_wpsc_epa_return b ON a.return_id = b.id
   WHERE box_id <> '-99999' AND b.return_status_id NOT IN (".$status_decline_cancelled_term_id.",".$status_decline_completed_term_id.")
   GROUP  BY box_id ) AS i ON i.box_id = b.id
LEFT JOIN (   SELECT a.folderdoc_id, a.return_id
   FROM   wpqa_wpsc_epa_return_items a
   LEFT JOIN  wpqa_wpsc_epa_return b ON a.return_id = b.id
   WHERE folderdoc_id <> '-99999' AND b.return_status_id NOT IN (".$status_decline_cancelled_term_id.",".$status_decline_completed_term_id.")
   GROUP  BY folderdoc_id )  AS j ON j.folderdoc_id = z.id
WHERE 1 ".$searchQuery." AND a.active <> 0 AND a.id <> -99999
group by a.request_id ".$searchHaving." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;

$boxRecords = mysqli_query($con, $boxQuery);
$data = array();

while ($row = mysqli_fetch_assoc($boxRecords)) {
$seconds = time() - strtotime($row['date_updated']); 

$decline_icon = '';
$recall_icon = '';
$type = 'request';

if(Patt_Custom_Func::id_in_return($row['patt_request_id'],$type) == 1){
//$decline_icon = '<span style="margin-left:4px;"><img src="'.WPPATT_PLUGIN_URL.'asset/images/decline.gif" alt="Declined"/></span>';
$decline_icon = '<span style="font-size: 1em; color: #FF0000;margin-left:4px;"><i class="fas fa-undo" title="Declined"></i></span>';
}

if(Patt_Custom_Func::id_in_recall($row['patt_request_id'],$type) == 1){
//$recall_icon = '<span style="margin-left:4px;"><img src="'.WPPATT_PLUGIN_URL.'asset/images/recall.gif" alt="Recall"/></span>';
$recall_icon = '<span style="font-size: 1em; color: #000;margin-left:4px;"><i class="far fa-registered" title="Recall"></i></span>';
}
$full_name = '<span style="display: none;" aria-label="'.$row['full_name'].'"></span>';



   $data[] = array(
     "request_id"=>$row['request_id'],
     "request_id_flag"=>$row['request_id_flag'].$decline_icon.$recall_icon,
     "ticket_priority"=>$row['ticket_priority'],
     "ticket_status"=>$row['ticket_status'],
     "customer_name"=>$row['customer_name'].$full_name,
     "location"=>$row['location'],
     //"ticket_priority"=>$row['ticket_priority'],
     "date_updated"=>calculate_time_span($seconds),
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
