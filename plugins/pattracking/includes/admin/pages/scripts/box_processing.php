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

global $current_user;

## Read value
$draw = $_POST['draw'];
$row = $_POST['start'];
$rowperpage = $_POST['length']; // Rows display per page
$columnIndex = $_POST['order'][0]['column']; // Column index
$columnName = $_POST['columns'][$columnIndex]['data']; // Column name
$columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
$searchValue = $_POST['search']['value']; // Search value

## Custom Field value
$searchByBoxID = str_replace(",", "|", $_POST['searchByBoxID']);
$searchByProgramOffice = $_POST['searchByProgramOffice'];
$searchByDigitizationCenter = $_POST['searchByDigitizationCenter'];
$searchByPriority = $_POST['searchByPriority'];
$searchGeneric = $_POST['searchGeneric'];
$searchByStatus = $_POST['searchByStatus'];
$searchByUser = $_POST['searchByUser'];
$searchByUserAAVal = $_REQUEST['searchByUserAAVal'];
$searchByUserAAName = $_REQUEST['searchByUserAAName'];
$is_requester = $_POST['is_requester'];


## Search 
$searchQuery = " ";
if($searchByBoxID != ''){
   $searchQuery .= " and (a.box_id REGEXP '^(".$searchByBoxID.")$' ) ";
}

if($searchByProgramOffice != ''){
   $searchQuery .= " and (c.office_acronym='".$searchByProgramOffice."') ";
}

if($searchByDigitizationCenter != ''){
   $searchQuery .= " and (e.name ='".$searchByDigitizationCenter."') ";
}

if($searchByPriority != ''){
   $searchQuery .= " and (b.ticket_priority='".$searchByPriority."') ";
}

if($searchByStatus != ''){
   $searchQuery .= " and (f.name ='".$searchByStatus."') ";
}

// If a user is a requester, only show the boxes from requests (tickets) they have submitted. 
if( $is_requester == 'true' ){
	$user_name = $current_user->display_name;
	$searchQuery .= " and (b.customer_name ='".$user_name."') ";
}


// Search by User code
if($searchByUser != ''){
	if( $searchByUser == 'mine' ) {
		$box_ids_for_user = '';
		$mini_query = "select distinct box_id from wpqa_wpsc_epa_boxinfo_userstatus where user_id = ".$current_user->ID;
		$mini_records = mysqli_query($con, $mini_query);
		while ($rox = mysqli_fetch_assoc($mini_records)) {
			$box_ids_for_user .= $rox['box_id'].", ";
		}
		$box_ids_for_user = substr($box_ids_for_user, 0, -2);
		
		if( $box_ids_for_user == null ) {
			$searchQuery .= " and (a.id IN (-99999)) ";
		} else {
			$searchQuery .= " and (a.id IN (".$box_ids_for_user.")) ";
		}
		
		
	} elseif( $searchByUser == 'not assigned' ) {
		
		// Register Box Status Taxonomy
		if( !taxonomy_exists('wpsc_box_statuses') ) {
			$args = array(
				'public' => false,
				'rewrite' => false
			);
			register_taxonomy( 'wpsc_box_statuses', 'wpsc_ticket', $args );
		}
		
		// Get List of Box Statuses
		$box_statuses = get_terms([
			'taxonomy'   => 'wpsc_box_statuses',
			'hide_empty' => false,
			'orderby'    => 'meta_value_num',
			'order'    	 => 'ASC',
			'meta_query' => array('order_clause' => array('key' => 'wpsc_box_status_load_order')),
		]);
		
		// List of box status that do not need agents assigned.
		$ignore_box_status = ['Pending', 'Ingestion', 'Completed', 'Dispositioned'];
// 		$ignore_box_status = []; //show all box status
		
		$term_id_array = array();
		foreach( $box_statuses as $key=>$box ) {
			if( in_array( $box->name, $ignore_box_status ) ) {
				unset($box_statuses[$key]);
				
			} else {
				$term_id_array[] = $box->term_id;
			}
		}
		array_values($box_statuses);
		
		$search_in_box_statuses = '';
		foreach( $box_statuses as $status ) {
			$search_in_box_statuses .= $status->term_id.', ';
		}
		$search_in_box_statuses = substr($search_in_box_statuses, 0, -2);
		
		$box_ids_for_user = '';
		// Get all distinct box_id that have been assigned.
		$mini_query = "select box_id 
						from 
							wpqa_wpsc_epa_boxinfo_userstatus 
						where 
							status_id IN (672, 671, 65, 6, 674, 743, 68) 
						group by 
							box_id 
						having count(distinct status_id) = 7 ";
		$mini_records = mysqli_query($con, $mini_query); 
		while ($rox = mysqli_fetch_assoc($mini_records)) {
			$box_ids_for_user .= $rox['box_id'].", ";
		}
		$box_ids_for_user = substr($box_ids_for_user, 0, -2);
		
		$searchQuery .= " and (a.id NOT IN (".$box_ids_for_user.")) ";
	} elseif( $searchByUser == 'search for user' ) {
		$search_true = (isset($searchByUserAAVal) ) ? true : false;
		$array_of_wp_user_id = Patt_Custom_Func::translate_user_id($searchByUserAAVal, 'wp_user_id');
		$user_id_str = '';
 		if( $search_true ) {
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
				if( $box_ids_for_users == null ) {
					$searchQuery .= " and (a.id IN (-99999)) ";
				} else {
					$searchQuery .= " and (a.id IN (".$box_ids_for_users.")) ";
				}
				
				//$searchQuery .= " and (a.id IN (".$box_ids_for_users.")) ";	
			}
		
		}
		

	}
	

}


if($searchGeneric != ''){
   $searchQuery .= " and (a.box_id like '%".$searchGeneric."%' or 
      b.request_id like '%".$searchGeneric."%' or 
      e.name like '%".$searchGeneric."%' or
      c.office_acronym like '%".$searchGeneric."%' or
      b.ticket_priority like '%".$searchGeneric."%') ";
}

if($searchValue != ''){
   $searchQuery .= " and (a.box_id like '%".$searchValue."%' or 
      b.request_id like '%".$searchValue."%' or 
      e.name like '%".$searchValue."%' or
      c.office_acronym like '%".$searchValue."%' or
      b.ticket_priority like '%".$searchValue."%') ";
}

## Total number of records without filtering
$sel = mysqli_query($con,"select count(*) as allcount from wpqa_wpsc_epa_boxinfo as a INNER JOIN wpqa_wpsc_ticket as b ON a.ticket_id = b.id WHERE a.id <> -99999 AND b.active <> 0");
//$sel = mysqli_query($con,"select count(*) as allcount from wpqa_wpsc_epa_boxinfo WHERE id <> -99999");
//$sel = mysqli_query($con,"select count(*) as allcount from wpqa_wpsc_ticket WHERE id <> -99999 AND active <> 0");
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];

## Total number of records with filtering
$sel = mysqli_query($con,"select count(a.box_id) as allcount FROM wpqa_wpsc_epa_boxinfo as a
INNER JOIN wpqa_terms f ON f.term_id = a.box_status
INNER JOIN wpqa_wpsc_ticket as b ON a.ticket_id = b.id
INNER JOIN wpqa_wpsc_epa_program_office as c ON a.program_office_id = c.office_code
INNER JOIN wpqa_wpsc_epa_storage_location as d ON a.storage_location_id = d.id
INNER JOIN wpqa_terms e ON e.term_id = d.digitization_center
WHERE (b.active <> 0) AND (a.id <> -99999) AND 1 ".$searchQuery); //(b.active <> 0) AND
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

## Fetch records
$boxQuery = "
SELECT 
a.box_id, a.id, f.name as box_status, f.term_id as term,
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

CONCAT(
'<span class=\"wpsp_admin_label\" style=\"background-color:',
(SELECT meta_value from wpqa_termmeta where meta_key = 'wpsc_priority_background_color' AND term_id = b.ticket_priority),
';color:',
(SELECT meta_value from wpqa_termmeta where meta_key = 'wpsc_priority_color' AND term_id = b.ticket_priority),
';\">',
(SELECT name from wpqa_terms where term_id = b.ticket_priority),
'</span>') as ticket_priority,

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

INNER JOIN wpqa_terms f ON f.term_id = a.box_status
INNER JOIN wpqa_wpsc_ticket as b ON a.ticket_id = b.id
INNER JOIN wpqa_wpsc_epa_program_office as c ON a.program_office_id = c.office_code
INNER JOIN wpqa_wpsc_epa_storage_location as d ON a.storage_location_id = d.id
INNER JOIN wpqa_terms e ON e.term_id = d.digitization_center
WHERE (b.active <> 0) AND (a.id <> -99999) AND 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;

//INNER JOIN wpqa_wpsc_epa_boxinfo_userstatus g ON g.box_id = a.id
//INNER JOIN wpqa_wpsc_epa_boxinfo_userstatus h ON h.box_id = a.id 

$boxRecords = mysqli_query($con, $boxQuery);
$data = array();
// $assigned_agents_icon = '<span style="font-size: 1.0em; color: #1d1f1d;margin-left:4px;" onclick="view_assigned_agents()" class="assign_agents_icon"><i class="fas fa-user-friends" title="Assigned Agents"></i></span>';

while ($row = mysqli_fetch_assoc($boxRecords)) {
	
	$status_term_id = $row['term'];
	$status_background = get_term_meta($status_term_id, 'wpsc_box_status_background_color', true);
	$status_color = get_term_meta($status_term_id, 'wpsc_box_status_color', true);
	$status_style = "background-color:".$status_background.";color:".$status_color.";";
	$box_status = "<span class='wpsp_admin_label' style='".$status_style."'>".$row['box_status']."</span>";
	
	//$assigned_agents_icon = '<span style="font-size: 1.0em; color: #1d1f1d;margin-left:4px;" onclick="view_assigned_agents(666)" class="assign_agents_icon"><i class="fas fa-user-friends" title="Assigned Agents"></i></span>';
	
	$assigned_agents_icon = '<span style="font-size: 1.0em; color: #1d1f1d;margin-left:4px;" onclick="view_assigned_agents(\''.$row['box_id'].'\')" class="assign_agents_icon"><i class="fas fa-user-friends" title="Assigned Agents"></i></span>';

$decline_icon = '';
$recall_icon = '';
$type = 'box';

if(Patt_Custom_Func::id_in_return($row['box_id'],$type) == 1){
$decline_icon = '<span style="font-size: 1em; color: #FF0000;margin-left:4px;"><i class="fas fa-undo" title="Declined"></i></span>';
}

if(Patt_Custom_Func::id_in_recall($row['box_id'],$type) == 1){
$recall_icon = '<span style="font-size: 1em; color: #000;margin-left:4px;"><i class="far fa-registered" title="Recall"></i></span>';
}	
	$data[] = array(
		"box_id"=>$row['box_id'],
		"box_id_flag"=>$row['box_id_flag'].$decline_icon.$recall_icon.$assigned_agents_icon,
		"ticket_priority"=>$row['ticket_priority'],
		"status"=>$box_status,
		"request_id"=>$row['request_id'],
		"location"=>$row['location'],
		"acronym"=>$row['acronym'],
// 		"acronym"=>$searchQuery,
// 		"acronym"=>$searchByUserAAVal,
		"validation"=>$row['validation'],
	);
}
## Response
$response = array(
  "draw" => intval($draw),
  "iTotalRecords" => $totalRecords,
  "iTotalDisplayRecords" => $totalRecordwithFilter,
  "aaData" => $data,
  "test" => $boxQuery,
  "box_ids_for_user" => $box_ids_for_user,
  "box_ids_for_users" => $box_ids_for_users,
  "searchByUser" => $searchByUser,
  "box_ids_for_user" => $box_ids_for_user,
  "is_requester" => $is_requester
//     "test" => $data[0]['box_id'] $searchByStatus
);

echo json_encode($response);