<?php
$nothing = explode("/", $_SERVER["PHP_SELF"], -8);
$WP_PATH = implode("/", (explode("/", $_SERVER["PHP_SELF"], -8)));
require_once($_SERVER['DOCUMENT_ROOT'].$WP_PATH.'/wp-config.php');

global $wpdb, $current_user, $wpscfunction;
	
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

if($_POST['searchByID']) {
	$searchByID = explode(',',$_POST['searchByID']);
}


//
// NEW SEARCH & RESPONSE
//

$data2 = array();
$error_array = array();

foreach( $searchByID as $item ) {
	
	$item_details = Patt_Custom_Func::get_box_file_details_by_id($item);
	$details_array = json_decode(json_encode($item_details), true);
	
	// Search Error
	if( $details_array == false ) {
		$error_array[$item]['search_error'] = true;
	} else {
		$error_array[$item]['search_error'] = false;
	}
	


	
	// Error Checking.
	// Note: later errors will overwrite ealier errors. 
	
	// Check if item is currently in a Recall.
	$recall_info = Patt_Custom_Func::item_in_recall( $item );
	if ( $recall_info['in_recall'] ) {
		$recall_id = 'R-'.$recall_info['in_recall_where'];
		$recall_link = '<a href="'.$subfolder_path.'/wp-admin/admin.php?page=recalldetails&id='.$recall_id.'" >'.$recall_id.'</a>';
		$error_array[$item]['item_error'] = $recall_info['error_message'].' in '.$recall_link.' '; //error: Folder/File already Recalled
	}
	
	// Check Box status to determine if returnable. 
	$box_statuses_returnable = [ 672, 748 ]; // 671: Scanning/Digitization | 672: Scanning Preparation | 748: Pending
	if( !in_array($details_array['box_status'], $box_statuses_returnable) ) {
		
		$box_statuses = Patt_Custom_Func::get_all_status();
		
		$box_link = '<a href="'.$subfolder_path.'/wp-admin/admin.php?page=boxdetails&pid=boxsearch&id='.$details_array['box_id'].'" >'.$details_array['box_id'].'</a>';
		$status = $details_array['box_status'];
		//$details_array['status_name'] = $box_statuses;
		$error_array[$item]['item_error'] = 'Box '.$box_link.' is in status <b>'.$box_statuses[$status].'</b> which is not Declinable.';
		// update to include Box link, and list the status that is not returnable. 
		
		
	}
	
	// If item is a Folder/Doc then it is not allowed to be Returned.
	if( $details_array['type'] == 'Folder/Doc' ) {
		$error_array[$item]['item_error'] = 'Folder/Doc IDs are not Declinable. Please enter a Box ID.';
	}
	
	// Check if item is currently in a Return (Decline)
	if( $details_array['type'] == 'Box' ) {
		
		$return_check = $wpdb->get_row(
										"SELECT return_id
										FROM wpqa_wpsc_epa_return_items
										WHERE box_id = '" .  $details_array['Box_id_FK'] . "'");
		
		if( $return_check->return_id != null ) {
			
			$return_status = $wpdb->get_row( "SELECT return_status_id
												FROM wpqa_wpsc_epa_return
												WHERE id = '" .  $return_check->return_id . "'");
			
			// If Decline not Cancelled // Decline Complete cannot be Declined again (no phyiscal path for this to be possible - 754)
			if( $return_status->return_status_id != 791  )	{						
			
			
				$num = $return_check->return_id;	
	            $str_length = 7;	
	            $return_id = substr("000000{$num}", -$str_length);	
	            
	            $box_link = '<a href="'.$subfolder_path.'/wp-admin/admin.php?page=boxdetails&pid=boxsearch&id='.$details_array['box_id'].'" >'.$details_array['box_id'].'</a>';
	            
	// 			$error_array[$item]['item_error'] = 'Box '.$box_link.' already in Return ';
				$error_array[$item]['item_error'] = 'Box '.$box_link.' already in Decline ';
				$error_array[$item]['return_id'] = $return_id;
			}	
		}
		
	} elseif ($details_array['type'] == 'Folder/Doc') {
		
		$return_check = $wpdb->get_row(
										"SELECT return_id
										FROM wpqa_wpsc_epa_return_items
										WHERE folderdoc_id = '" .  $details_array['Folderdoc_Info_id_FK'] . "'");
		
		if( $return_check->return_id != null ) {
			
			$num = $return_check->return_id;	
            $str_length = 7;	
            $return_id = substr("000000{$num}", -$str_length);
            
            $folder_link = '<a href="'.$subfolder_path.'/wp-admin/admin.php?pid=docsearch&page=filedetails&id='.$details_array['Folderdoc_Info_id'].'" >'.$details_array['Folderdoc_Info_id'].'</a>';
            $box_link = '<a href="'.$subfolder_path.'/wp-admin/admin.php?page=boxdetails&pid=boxsearch&id='.$details_array['box_id'].'" >'.$details_array['box_id'].'</a>';
            
// 			$error_array[$item]['item_error'] = 'Containing Box '.$box_link.' for Folder/File '.$folder_link.' already in Return ';
			$error_array[$item]['item_error'] = 'Containing Box '.$box_link.' for Folder/File '.$folder_link.' already in Decline ';
			$error_array[$item]['return_id'] = $return_id;
		}	
	}
	

	
	// Place data into data structure
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
// 		     "validation"=>$_POST['searchByID'],     
		     "validation"=>$details_array['box_status'],     
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
// 		     "validation"=>$_POST['searchByID'],    
			"validation"=>$details_array['box_status'], 		      
		);
	}
}




$response2 = array(
	"draw" => intval($draw),
	"iTotalRecords" => count($searchByID),
	"iTotalDisplayRecords" => count($searchByID),
	"aaData" => $data2,
	"errors" => $error_array,
	"alerts" => $return_check,
	"details" => $details_array,
	"test" => $nothing,
	"WP_PATH" => $WP_PATH
);
/*
$data2 = array();

$response2 = array(
	"draw" => intval($draw),
	"iTotalRecords" => 1,
	"iTotalDisplayRecords" => 1,
	"aaData" => $data2,
	"errors" => 'nothing',
	"alerts" => 'no alerts',
	"details" => 'some deets'
);
*/



echo json_encode($response2);