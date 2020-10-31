<?php
$WP_PATH = implode("/", (explode("/", $_SERVER["PHP_SELF"], -6)));
require_once($_SERVER['DOCUMENT_ROOT'].$WP_PATH.'/wp-config.php');

global $current_user, $wpscfunction, $wpdb;
$subfolder_path = site_url( '', 'relative'); 
//$subfolder_path = '/wordpress3/'; 



// OLD
//$search_id = isset($_POST['label']) ? sanitize_text_field($_POST['label']) : '';
// $search_id = '0000020-2';
$search_id = '';
// NEW

if($_POST['searchByID']) {
	$searchByID = explode(',',$_POST['searchByID']);
	$search_id = $searchByID[0];
}

//$nothing = Patt_Custom_Func::get_default_digitization_center(238);
$box_file_details = Patt_Custom_Func::get_box_file_details_by_id($search_id);
//$box_file_details = Patt_Custom_Func::get_box_file_details_by_id('0000288-1');
// $box_file_details = Patt_Custom_Func::get_box_file_details_by_id('0000001-2-01-10');
//print_r($box_file_details);

$details_array = json_decode(json_encode($box_file_details), true);

// DEBUG
$details_array['searchByID'] = $searchByID;

// END

//if ( $details_array == false ) {
if ( $box_file_details == null ) {
	$details_array['search_error'] = true;
} else {
	$details_array['search_error'] = false;
}


// Set variables for search
$is_folder_search = array_key_exists('Folderdoc_Info_id',$details_array);
$details_array['in_recall'] = false;
$details_array['is_folder_search'] = $is_folder_search;
$details_array['error_message'] = '';
$db_null = -99999;





// Check if item is currently in recall database 

$recall_rows = $wpdb->get_results(
'SELECT 
	wpqa_wpsc_epa_recallrequest.id as id, 
    wpqa_wpsc_epa_recallrequest.recall_id as recall_id,	
	wpqa_wpsc_epa_recallrequest.box_id as box_id, 
	boxinfo.box_id as display_box_id,
	boxinfo.box_destroyed as box_destroyed,
    folderinfo.folderdocinfo_id as dispay_folder_id,
	wpqa_wpsc_epa_recallrequest.folderdoc_id as folderdoc_id,
	wpqa_wpsc_epa_recallrequest.recall_status_id as status_id
FROM 
	wpqa_wpsc_epa_recallrequest 
	INNER JOIN 
		wpqa_wpsc_epa_boxinfo AS boxinfo 
	ON (
                wpqa_wpsc_epa_recallrequest.box_id = boxinfo.id
	)
        INNER JOIN 
		wpqa_wpsc_epa_folderdocinfo AS folderinfo 
	ON (
                wpqa_wpsc_epa_recallrequest.folderdoc_id = folderinfo.id
	)
 ORDER BY id ASC' );
 

// Box Search  
if( !$is_folder_search ) {
	
	
	

	
	// if Box Destroyed, No recall allowed
	if( $details_array['box_destroyed'] == true ) {
		$details_array['error_message'] = 'Box Destroyed';
	} else { // if box not destroyed, check if it's been recalled
		
		// Search through all Recalls to determine if box has been recalled.
		foreach ($recall_rows as $item) {
		
			// Is Box Recalled?
			if( $details_array['box_id'] == $item->display_box_id && $item->folderdoc_id == $db_null && ($item->status_id != 733 && $item->status_id != 734 && $item->status_id != 878) ) {
				$details_array['error'] = 'Found: '.$item->status_id.' - '.$details_array['error'];
				$details_array['in_recall'] = true;
				$details_array['in_recall_where'] = $item->recall_id;
				$details_array['error_message'] = 'Box Already Recalled';
				break;
			}
		}
		

		
		
		// if not recalled, check all folder/files inside of box for Destroyed Files
		if( $details_array['in_recall'] == false ) {
			$folder_rows = $wpdb->get_results(
				'SELECT 
					folderinfo.id as id, 
				    folderinfo.folderdocinfo_id as display_folderdocinfo_id,
				    folderinfo.unauthorized_destruction as unauthorized_destruction
				FROM 
					wpqa_wpsc_epa_folderdocinfo as folderinfo
				WHERE
				    folderinfo.box_id = '. $details_array['Box_id_FK'] .'
				   AND
				    unauthorized_destruction = 1
				ORDER BY id ASC'
			);
			
			if( $folder_rows ) {
				$list_of_destroyed_files = [];
		
				foreach( $folder_rows as $folder ) {
					$list_of_destroyed_files[] = $folder->display_folderdocinfo_id;
				}
				
				$details_array['error_message'] = 'Box Contains Destroyed Files';
				$details_array['error'] = 'Box Contains Destroyed Files';
				$details_array['destroyed_files'] = $list_of_destroyed_files;	
			}	
		}
		
		
		// Check the box status to determine if box is recallable 
		switch( $details_array['box_status'] ) {
			
			case 748: // Box Status: Pending
				$details_array['error'] = 'Box Status Not Recallable';
				$details_array['error_message'] = 'Recalls are not allowed until the Box status enters Scanning/Digitization.';
				$details_array['box_status_name'] = 'Pending';
				break;
			case 672: // Box Status: Scanning Preperation
				$details_array['error'] = 'Box Status Not Recallable';
				$details_array['error_message'] = 'Recalls are not allowed until the Box status enters Scanning/Digitization.';
				$details_array['box_status_name'] = 'Scanning Preperation';
				break;
			case 671: // Box Status: Scanning/Digitization
				//$details_array['error'] = '';
				//$details_array['error_message'] = '';
				$details_array['box_status_name'] = 'Scanning/Digitization';
				break;
			case 65: // Box Status: QA/QC
				//$details_array['error'] = '';
				//$details_array['error_message'] = '';
				$details_array['box_status_name'] = 'QA/QC';
				break;
			case 6: // Box Status: Digitized - Not Validated
				//$details_array['error'] = '';
				//$details_array['error_message'] = '';
				$details_array['box_status_name'] = 'Digitized - Not Validated';
				break;
			case 673: // Box Status: Ingestion
				//$details_array['error'] = '';
				//$details_array['error_message'] = '';
				$details_array['box_status_name'] = 'Ingestion';
				break;
			case 674: // Box Status: Validation
				$details_array['error'] = 'Box Status Not Recallable';
				$details_array['error_message'] = 'Recalls are not allowed for Boxes in Validation to Re-Scan statuses.';
				$details_array['box_status_name'] = 'Validation';
				break;
			case 743: // Box Status: Re-scan
				$details_array['error'] = 'Box Status Not Recallable';
				$details_array['error_message'] = 'Recalls are not allowed for Boxes in Validation to Re-Scan statuses.';
				$details_array['box_status_name'] = 'Re-scan';
				break;
			case 66: // Box Status: Completed
				//$details_array['error'] = '';
				//$details_array['error_message'] = '';
				$details_array['box_status_name'] = 'Completed';
				break;
			case 68: // Box Status: Destruction Approval
				$details_array['error'] = 'Box Status Not Recallable';
				$details_array['error_message'] = 'Recalls are not allowed in the Destruction Approval status.';
				$details_array['box_status_name'] = 'Destruction Approval';
				break;
			case 67: // Box Status: Dispositioned
				$details_array['error'] = 'Box Status Not Recallable';
				$details_array['error_message'] = 'Recalls are not allowed in the Dispositioned status.';
				$details_array['box_status_name'] = 'Dispositioned';
				break;
			
		}
		
		// Check if item is currently in Return 
		$ret = Patt_Custom_Func::item_in_return($search_id, 'Box', $subfolder_path);
		if( $ret['return_id'] != null ) {
			$details_array['error'] = 'Item in Return';
			$details_array['error_message'] = $ret['item_error'];
			$details_array['return_id'] = $ret['return_id'];
			//$type = 'Box' or 'Folder/Doc';	
		}
	
	}
} else { // Folder/File Search
	
	// if Folder / File  Unauthorized Destruction, No recall allowed
	if( $details_array['unauthorized_destruction'] == true ) {
		$details_array['error_message'] = 'Folder/File Unauthorized Destruction';
	} // if Folder/File not destroyed, check if it's been recalled 
	elseif ( $details_array['in_recall'] == false ) {
		foreach( $recall_rows as $item ) {
			if ($details_array['Folderdoc_Info_id'] == $item->dispay_folder_id && ($item->status_id != 733 && $item->status_id != 734 && $item->status_id != 878)) {
				$details_array['error'] = 'Found: '.$item->dispay_folder_id.' - '.$details_array['error'];
				$details_array['in_recall'] = true;
				$details_array['in_recall_where'] = $item->recall_id;
				$details_array['error_message'] = 'Folder/File already Recalled';
			}
		}
	} 
	
	// if not destoryed && not recalled, check if containing box has been recalled
	if ( $details_array['in_recall'] == false && $details_array['error_message'] != 'Folder/File Unauthorized Destruction' ) { 
		// Search through all Recalls to determine if box has been recalled.
		foreach ($recall_rows as $item) {
			$details_array['Test'] = $item;
			// Is Box Recalled?
			if( $details_array['Box_id_FK'] == $item->box_id && $item->folderdoc_id == $db_null && ($item->status_id != 733 && $item->status_id != 734 && $item->status_id != 878)) {
				$details_array['error'] = 'Found: '.$item->status_id.' - '.$details_array['error'];
				$details_array['in_recall'] = true;
				$details_array['in_recall_where'] = $item->recall_id;
				$details_array['error_message'] = 'Folder/File in Recalled Box';
				break;
			}
		}
		
	}
	
	// Check the status of the containing box to determine if it's recallable
	switch( $details_array['box_status'] ) {
		
		case 748: // Box Status: Pending
			$details_array['error'] = 'Containing Box Status Not Recallable';
			$details_array['error_message'] = 'Recalls are not allowed until the Box status enters Scanning/Digitization.';
			$details_array['box_status_name'] = 'Pending';
			break;
		case 672: // Box Status: Scanning Preperation
			$details_array['error'] = 'Containing Box Status Not Recallable';
			$details_array['error_message'] = 'Recalls are not allowed until the Box status enters Scanning/Digitization.';
			$details_array['box_status_name'] = 'Scanning Preperation';
			break;
		case 671: // Box Status: Scanning/Digitization
			//$details_array['error'] = '';
			//$details_array['error_message'] = '';
			$details_array['box_status_name'] = 'Scanning/Digitization';
			break;
		case 65: // Box Status: QA/QC
			//$details_array['error'] = '';
			//$details_array['error_message'] = '';
			$details_array['box_status_name'] = 'QA/QC';
			break;
		case 6: // Box Status: Digitized - Not Validated
			//$details_array['error'] = '';
			//$details_array['error_message'] = '';
			$details_array['box_status_name'] = 'Digitized - Not Validated';
			break;
		case 673: // Box Status: Ingestion
			//$details_array['error'] = '';
			//$details_array['error_message'] = '';
			$details_array['box_status_name'] = 'Ingestion';
			break;
		case 674: // Box Status: Validation
			$details_array['error'] = 'Containing Box Status Not Recallable';
			$details_array['error_message'] = 'Recalls are not allowed for Boxes in Validation to Re-Scan statuses.';
			$details_array['box_status_name'] = 'Validation';
			break;
		case 743: // Box Status: Re-scan
			$details_array['error'] = 'Containing Box Status Not Recallable';
			$details_array['error_message'] = 'Recalls are not allowed for Boxes in Validation to Re-Scan statuses.';
			$details_array['box_status_name'] = 'Re-scan';
			break;
		case 66: // Box Status: Completed
			//$details_array['error'] = '';
			//$details_array['error_message'] = '';
			$details_array['box_status_name'] = 'Completed';
			break;
		case 68: // Box Status: Destruction Approval
			$details_array['error'] = 'Containing Box Status Not Recallable';
			$details_array['error_message'] = 'Recalls are not allowed in the Destruction approval status.';
			$details_array['box_status_name'] = 'Destruction Approval';
			break;
		case 67: // Box Status: Dispositioned
			$details_array['error'] = 'Containing Box Status Not Recallable';
			$details_array['error_message'] = 'Recalls are not allowed in the Dispositioned status.';
			$details_array['box_status_name'] = 'Dispositioned';
			break;			
		
	}

	// Check if item is currently in Return 
	$ret = Patt_Custom_Func::item_in_return($search_id, 'Folder/Doc', $subfolder_path);
	//$details_array['return_id'] = $ret['return_id'];
	if( $ret['return_id'] != null ) {
		$details_array['error'] = 'Item in Return';
		$details_array['error_message'] = $ret['item_error'];
		$details_array['return_id'] = $ret['return_id'];
	}

	
}




// OLD METHOD


// NEW METHOD
// Set variables

$data2 = array();
$num_of_records = 0;

if( $details_array['search_error'] == false ) {
	if($is_folder_search) {
		$the_id = $details_array['Folderdoc_Info_id'];
		$link_str_ff = "<a href='".$subfolder_path."/wp-admin/admin.php?pid=boxsearch&page=filedetails&id=".
								$details_array['Folderdoc_Info_id']."' target='_blank' >".$details_array['Folderdoc_Info_id']."</a>";
		$title = $details_array['title'];
	} else {
		$the_id = $details_array['box_id'];
		$link_str_ff = "<a href='".$subfolder_path."/wp-admin/admin.php?page=boxdetails&pid=requestdetails&id=".
								$details_array['box_id']."' target='_blank' >".$details_array['box_id']."</a>";
		$title = '[Boxes do not have Titles]';						
	}
	
	$num_of_records = count($searchByID);
	
	$data2[] = array(
			"box_id"=>$the_id, 
			"box_id_flag"=>$link_str_ff,
			"title"=>$title,
			"request_id"=>$details_array['Record_Schedule_Number'], 
			"program_office"=>$details_array['office_acronym'].': '.$details_array['office_name']
// 			"validation"=>'another thing'	      
		);		
}


	



//$data2 = [];



	

$response2 = array(
	"draw" => intval($draw),
	"iTotalRecords" => $num_of_records,
	"iTotalDisplayRecords" => $num_of_records,
	"aaData" => $data2,
	"errors" => 'errors',
	"alerts" => $nothing,
	"details" => $details_array,
	"test" => '',
	"search_id" => $search_id,
	"searchByID" => $searchByID
);
	
/*
$response2 = array(
  "draw" => intval($draw),
  "iTotalRecords" => count($searchByID),
  "iTotalDisplayRecords" => count($searchByID),
  "aaData" => $data2,
  "errors" => $error_array,
  "alerts" => $return_check,
  "details" => $details_array
);
*/

/*
$data2 = array();

$response2 = array(
  "aaData" => $data2
);
*/

echo json_encode($response2);