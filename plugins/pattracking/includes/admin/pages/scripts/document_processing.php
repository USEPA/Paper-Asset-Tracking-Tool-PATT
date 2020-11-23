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

if($columnName == 'ticket_priority') {
$columnName = 'ticket_priority_order';
} else {
$columnName = $_POST['columns'][$columnIndex]['data']; // Column name
}

## Custom Field value
$searchByDocID = str_replace(",", "|", $_POST['searchByDocID']);
$searchByProgramOffice = $_POST['searchByProgramOffice'];
$searchByDigitizationCenter = $_POST['searchByDigitizationCenter'];
$searchByPriority = $_POST['searchByPriority'];
$searchByRecallDecline = $_POST['searchByRecallDecline'];
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

if($searchByPriority != ''){
   $searchQuery .= " and (b.ticket_priority='".$searchByPriority."') ";
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
            COALESCE(g.recall_status_id, h.recall_status_id) NOT IN (".$status_recall_denied_term_id.",".$status_recall_cancelled_term_id.",".$status_recall_complete_term_id.")
            )";
        }

        if($searchByRecallDecline == 'Decline') {
            $searchQuery .= "and (
            i.return_id <> '' OR j.return_id <> ''
            )";
        }

}

if($searchGeneric != ''){
   $searchQuery .= " and (a.folderdocinfo_id like '%".$searchGeneric."%' or 
      b.request_id like '%".$searchGeneric."%' or 
      f.name like '%".$searchGeneric."%' or
      c.office_acronym like '%".$searchGeneric."%' or
      b.ticket_priority like '%".$searchGeneric."%') ";

//   $searchQuery .= " and (a.folderdocinfo_id like '%".$searchGeneric."%' ) ";
}

if($searchValue != ''){
   $searchQuery .= " and (a.folderdocinfo_id like '%".$searchValue."%' or 
      b.request_id like '%".$searchValue."%' or 
      f.name like '%".$searchValue."%' or
      c.office_acronym like '%".$searchValue."%' or
      b.ticket_priority like '%".$searchValue."%') ";
}

## Total number of records without filtering
$sel = mysqli_query($con,"select count(*) as allcount from wpqa_wpsc_epa_folderdocinfo as a 
INNER JOIN wpqa_wpsc_epa_boxinfo as d ON a.box_id = d.id
INNER JOIN wpqa_wpsc_epa_storage_location as e ON d.storage_location_id = e.id
INNER JOIN wpqa_wpsc_ticket as b ON d.ticket_id = b.id
INNER JOIN wpqa_wpsc_epa_program_office as c ON d.program_office_id = c.office_code
INNER JOIN wpqa_terms f ON f.term_id = e.digitization_center
LEFT JOIN wpqa_wpsc_epa_recallrequest AS g ON (g.box_id = d.id AND g.folderdoc_id = '-99999')
LEFT JOIN wpqa_wpsc_epa_recallrequest AS h ON (h.folderdoc_id = a.id AND h.folderdoc_id <> '-99999')

LEFT JOIN (   SELECT a.box_id, a.return_id
   FROM   wpqa_wpsc_epa_return_items a
   LEFT JOIN  wpqa_wpsc_epa_return b ON a.return_id = b.id
   WHERE box_id <> '-99999' AND b.return_status_id <> 791
   GROUP  BY box_id ) AS i ON i.box_id = d.id
LEFT JOIN (   SELECT a.folderdoc_id, a.return_id
   FROM   wpqa_wpsc_epa_return_items a
   LEFT JOIN  wpqa_wpsc_epa_return b ON a.return_id = b.id
   WHERE folderdoc_id <> '-99999' AND b.return_status_id <> 791
   GROUP  BY folderdoc_id )  AS j ON j.folderdoc_id = a.id
WHERE a.id <> -99999 AND b.active <> 0 
");

$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];

## Total number of records with filtering
//gets all folder/files for a user with the requester role, only shows their own folder/files
if($agent_permissions['label'] == 'Requester') {
$sel = mysqli_query($con,"select count(a.folderdocinfo_id) as allcount FROM wpqa_wpsc_epa_folderdocinfo as a
INNER JOIN wpqa_wpsc_epa_boxinfo as d ON a.box_id = d.id
INNER JOIN wpqa_wpsc_epa_storage_location as e ON d.storage_location_id = e.id
INNER JOIN wpqa_wpsc_ticket as b ON d.ticket_id = b.id
INNER JOIN wpqa_wpsc_epa_program_office as c ON d.program_office_id = c.office_code
INNER JOIN wpqa_terms f ON f.term_id = e.digitization_center
LEFT JOIN wpqa_wpsc_epa_recallrequest AS g ON (g.box_id = d.id AND g.folderdoc_id = '-99999')
LEFT JOIN wpqa_wpsc_epa_recallrequest AS h ON (h.folderdoc_id = a.id AND h.folderdoc_id <> '-99999')

LEFT JOIN (   SELECT a.box_id, a.return_id
   FROM   wpqa_wpsc_epa_return_items a
   LEFT JOIN  wpqa_wpsc_epa_return b ON a.return_id = b.id
   WHERE a.box_id <> '-99999' AND b.return_status_id NOT IN (".$status_decline_cancelled_term_id.",".$status_decline_completed_term_id.")
   GROUP  BY a.box_id ) AS i ON i.box_id = d.id
LEFT JOIN (   SELECT a.folderdoc_id, a.return_id
   FROM   wpqa_wpsc_epa_return_items a
   LEFT JOIN  wpqa_wpsc_epa_return b ON a.return_id = b.id
   WHERE a.folderdoc_id <> '-99999' AND b.return_status_id NOT IN (".$status_decline_cancelled_term_id.",".$status_decline_completed_term_id.")
   GROUP  BY a.folderdoc_id )  AS j ON j.folderdoc_id = a.id
   
WHERE (b.active <> 0) AND (a.id <> -99999) AND b.customer_name = '" .  $current_user->display_name . "' 
AND 1 ".$searchQuery);
}
//gets all folder/files for every user
else {
$sel = mysqli_query($con,"select count(a.folderdocinfo_id) as allcount FROM wpqa_wpsc_epa_folderdocinfo as a
INNER JOIN wpqa_wpsc_epa_boxinfo as d ON a.box_id = d.id
INNER JOIN wpqa_wpsc_epa_storage_location as e ON d.storage_location_id = e.id
INNER JOIN wpqa_wpsc_ticket as b ON d.ticket_id = b.id
INNER JOIN wpqa_wpsc_epa_program_office as c ON d.program_office_id = c.office_code
INNER JOIN wpqa_terms f ON f.term_id = e.digitization_center
LEFT JOIN wpqa_wpsc_epa_recallrequest AS g ON (g.box_id = d.id AND g.folderdoc_id = '-99999')
LEFT JOIN wpqa_wpsc_epa_recallrequest AS h ON (h.folderdoc_id = a.id AND h.folderdoc_id <> '-99999')

LEFT JOIN (   SELECT a.box_id, a.return_id
   FROM   wpqa_wpsc_epa_return_items a
   LEFT JOIN  wpqa_wpsc_epa_return b ON a.return_id = b.id
   WHERE a.box_id <> '-99999' AND b.return_status_id NOT IN (".$status_decline_cancelled_term_id.",".$status_decline_completed_term_id.")
   GROUP  BY a.box_id ) AS i ON i.box_id = d.id
LEFT JOIN (   SELECT a.folderdoc_id, a.return_id
   FROM   wpqa_wpsc_epa_return_items a
   LEFT JOIN  wpqa_wpsc_epa_return b ON a.return_id = b.id
   WHERE a.folderdoc_id <> '-99999' AND b.return_status_id NOT IN (".$status_decline_cancelled_term_id.",".$status_decline_completed_term_id.")
   GROUP  BY a.folderdoc_id )  AS j ON j.folderdoc_id = a.id
   
WHERE (b.active <> 0) AND (a.id <> -99999) AND 1 ".$searchQuery);
}
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

## Fetch records
//if user is a requester, only show their own requests
if($agent_permissions['label'] == 'Requester') {
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

CONCAT(
'<span class=\"wpsp_admin_label\" style=\"background-color:',
(SELECT meta_value from wpqa_termmeta where meta_key = 'wpsc_priority_background_color' AND term_id = b.ticket_priority),
';color:',
(SELECT meta_value from wpqa_termmeta where meta_key = 'wpsc_priority_color' AND term_id = b.ticket_priority),
';\">',
(SELECT name from wpqa_terms where term_id = b.ticket_priority),
'</span>') as ticket_priority,

CASE 
WHEN b.ticket_priority = 621
THEN
1
WHEN b.ticket_priority = 9
THEN
2
WHEN b.ticket_priority = 8
THEN
3
WHEN b.ticket_priority = 7
THEN
4
ELSE
999
END
 as ticket_priority_order,

CONCAT('<a href=admin.php?page=wpsc-tickets&id=',b.request_id,'>',b.request_id,'</a>') as request_id, f.name as location, c.office_acronym as acronym,

CONCAT(
CASE 
WHEN validation = 1 THEN CONCAT('<span style=\"font-size: 1.3em; color: #008000;\"><i class=\"fas fa-check-circle\" title=\"Validated\"></i></span> ',' [',

CONCAT (
CASE
WHEN ((SELECT meta_value FROM wpqa_usermeta WHERE meta_key = 'first_name' AND user_id = u.ID) <> '') AND ((SELECT meta_value FROM wpqa_usermeta WHERE meta_key = 'last_name' AND user_id = u.ID) <> '') THEN CONCAT ( '<a href=\"#\" style=\"color: #000000 !important;\" data-toggle=\"tooltip\" data-placement=\"bottom\" data-html=\"true\" aria-label=\"Name\" title=\"',
u.user_login
,'\">',

(
    CASE WHEN length(CONCAT((SELECT meta_value FROM wpqa_usermeta WHERE meta_key = 'first_name' AND user_id = u.ID), ' ', (SELECT meta_value FROM wpqa_usermeta WHERE meta_key = 'last_name' AND user_id = u.ID))) > 15 THEN
        CONCAT(LEFT(CONCAT((SELECT meta_value FROM wpqa_usermeta WHERE meta_key = 'first_name' AND user_id = u.ID), ' ', (SELECT meta_value FROM wpqa_usermeta WHERE meta_key = 'last_name' AND user_id = u.ID)), 15), '...')
    ELSE CONCAT((SELECT meta_value FROM wpqa_usermeta WHERE meta_key = 'first_name' AND user_id = u.ID), ' ', (SELECT meta_value FROM wpqa_usermeta WHERE meta_key = 'last_name' AND user_id = u.ID))
    END
)

,'</a>' )
ELSE u.user_login
END
)

,']')
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
LEFT JOIN wpqa_wpsc_epa_recallrequest AS g ON (g.box_id = d.id AND g.folderdoc_id = '-99999')
LEFT JOIN wpqa_wpsc_epa_recallrequest AS h ON (h.folderdoc_id = a.id AND h.folderdoc_id <> '-99999')

LEFT JOIN (   SELECT a.box_id, a.return_id
   FROM   wpqa_wpsc_epa_return_items a
   LEFT JOIN  wpqa_wpsc_epa_return b ON a.return_id = b.id
   WHERE a.box_id <> '-99999' AND b.return_status_id NOT IN (".$status_decline_cancelled_term_id.",".$status_decline_completed_term_id.")
   GROUP  BY a.box_id ) AS i ON i.box_id = d.id
LEFT JOIN (   SELECT a.folderdoc_id, a.return_id
   FROM   wpqa_wpsc_epa_return_items a
   LEFT JOIN  wpqa_wpsc_epa_return b ON a.return_id = b.id
   WHERE a.folderdoc_id <> '-99999' AND b.return_status_id NOT IN (".$status_decline_cancelled_term_id.",".$status_decline_completed_term_id.")
   GROUP  BY a.folderdoc_id )  AS j ON j.folderdoc_id = a.id
WHERE (b.active <> 0) AND (a.id <> -99999) AND b.customer_name =  '" .  $current_user->display_name . "'
AND 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
}
//shows all folder/files
else {
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

CONCAT(
'<span class=\"wpsp_admin_label\" style=\"background-color:',
(SELECT meta_value from wpqa_termmeta where meta_key = 'wpsc_priority_background_color' AND term_id = b.ticket_priority),
';color:',
(SELECT meta_value from wpqa_termmeta where meta_key = 'wpsc_priority_color' AND term_id = b.ticket_priority),
';\">',
(SELECT name from wpqa_terms where term_id = b.ticket_priority),
'</span>') as ticket_priority,

CASE 
WHEN b.ticket_priority = 621
THEN
1
WHEN b.ticket_priority = 9
THEN
2
WHEN b.ticket_priority = 8
THEN
3
WHEN b.ticket_priority = 7
THEN
4
ELSE
999
END
 as ticket_priority_order,

CONCAT('<a href=admin.php?page=wpsc-tickets&id=',b.request_id,'>',b.request_id,'</a>') as request_id, f.name as location, c.office_acronym as acronym,
CONCAT(
CASE 
WHEN validation = 1 THEN CONCAT('<span style=\"font-size: 1.3em; color: #008000;\"><i class=\"fas fa-check-circle\" title=\"Validated\"></i></span> ',' [',

CONCAT (
CASE
WHEN ((SELECT meta_value FROM wpqa_usermeta WHERE meta_key = 'first_name' AND user_id = u.ID) <> '') AND ((SELECT meta_value FROM wpqa_usermeta WHERE meta_key = 'last_name' AND user_id = u.ID) <> '') THEN CONCAT ( '<a href=\"#\" style=\"color: #000000 !important;\" data-toggle=\"tooltip\" data-placement=\"bottom\" data-html=\"true\" aria-label=\"Name\" title=\"',
u.user_login
,'\">',

(
    CASE WHEN length(CONCAT((SELECT meta_value FROM wpqa_usermeta WHERE meta_key = 'first_name' AND user_id = u.ID), ' ', (SELECT meta_value FROM wpqa_usermeta WHERE meta_key = 'last_name' AND user_id = u.ID))) > 15 THEN
        CONCAT(LEFT(CONCAT((SELECT meta_value FROM wpqa_usermeta WHERE meta_key = 'first_name' AND user_id = u.ID), ' ', (SELECT meta_value FROM wpqa_usermeta WHERE meta_key = 'last_name' AND user_id = u.ID)), 15), '...')
    ELSE CONCAT((SELECT meta_value FROM wpqa_usermeta WHERE meta_key = 'first_name' AND user_id = u.ID), ' ', (SELECT meta_value FROM wpqa_usermeta WHERE meta_key = 'last_name' AND user_id = u.ID))
    END
)

,'</a>' )
ELSE u.user_login
END
)

,']')
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
LEFT JOIN wpqa_wpsc_epa_recallrequest AS g ON (g.box_id = d.id AND g.folderdoc_id = '-99999')
LEFT JOIN wpqa_wpsc_epa_recallrequest AS h ON (h.folderdoc_id = a.id AND h.folderdoc_id <> '-99999')

LEFT JOIN (   SELECT a.box_id, a.return_id
   FROM   wpqa_wpsc_epa_return_items a
   LEFT JOIN  wpqa_wpsc_epa_return b ON a.return_id = b.id
   WHERE a.box_id <> '-99999' AND b.return_status_id NOT IN (".$status_decline_cancelled_term_id.",".$status_decline_completed_term_id.")
   GROUP  BY a.box_id ) AS i ON i.box_id = d.id
LEFT JOIN (   SELECT a.folderdoc_id, a.return_id
   FROM   wpqa_wpsc_epa_return_items a
   LEFT JOIN  wpqa_wpsc_epa_return b ON a.return_id = b.id
   WHERE a.folderdoc_id <> '-99999' AND b.return_status_id NOT IN (".$status_decline_cancelled_term_id.",".$status_decline_completed_term_id.")
   GROUP  BY a.folderdoc_id )  AS j ON j.folderdoc_id = a.id
   
WHERE (b.active <> 0) AND (a.id <> -99999) AND 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
}
$docRecords = mysqli_query($con, $docQuery);
$data = array();

while ($row = mysqli_fetch_assoc($docRecords)) {

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
     "ticket_priority"=>$row['ticket_priority'],
//      "folderdocinfo_id_flag"=>$row['folderdocinfo_id_flag'],
     "request_id"=>$row['request_id'],
     "location"=>$row['location'],
     "acronym"=>$row['acronym'],
     "validation"=>$row['validation']
     //"ticket_priority"=>$row['ticket_priority']
   );
}

## Response
$response = array(
  "draw" => intval($draw),
  "iTotalRecords" => $totalRecords,
  "iTotalDisplayRecords" => $totalRecordwithFilter,
  "aaData" => $data,
  "sqlQuery" => $docQuery
);

echo json_encode($response);
