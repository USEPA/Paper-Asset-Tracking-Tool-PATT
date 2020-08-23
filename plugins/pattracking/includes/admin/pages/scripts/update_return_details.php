<?php

global $wpdb, $current_user, $wpscfunction;

$path = preg_replace('/wp-content.*$/','',__DIR__);
include($path.'wp-load.php');

$return_id = isset($_POST['return_id']) ? sanitize_text_field($_POST['return_id']) : '';
$type = isset($_POST['type']) ? sanitize_text_field($_POST['type']) : '';
//$title = isset($_POST['title']) ? sanitize_text_field($_POST['title']) : '';
//$recall_ids = $_REQUEST['recall_ids']; 
//$ticket_id = isset($_POST['ticket_id']) ? sanitize_text_field($_POST['ticket_id']) : '';
//$recall_ids = json_decode($recall_ids);
//$num_of_recalls = count($recall_ids);


if( $type == 'cancel' ) {
	
	//echo '!Recall ID: '.$recall_id.PHP_EOL;
	//echo 'POST recall id: '.$_POST['recall_id'].PHP_EOL;
	//echo 'Type: '.$type.PHP_EOL;
	//echo 'Recall status before: '.$recall_obj->recall_status_id.PHP_EOL;
	//print_r($recall_array);

	
	
	$where = [
		'return_id' => $return_id
	];
	$return_array = Patt_Custom_Func::get_return_data($where);
	
	//Added for servers running < PHP 7.3
	if (!function_exists('array_key_first')) {
	    function array_key_first(array $arr) {
	        foreach($arr as $key => $unused) {
	            return $key;
	        }
	        return NULL;
	    }
	}
	
	$return_array_key = array_key_first($return_array);	
	$return_obj = $return_array[$return_array_key];
	
// 	echo 'current status: '.$return_obj->return_status_id;
	
	echo 'Return Object: ';
	print_r($return_obj);
	
	// Only cancel if Return is in status: Return Initiated
	if ( $return_obj->return_status_id == 752 ) {
		$data_status = [ 'return_status_id' => 791 ]; //change status to Cancelled old 785, now 791
		$obj = Patt_Custom_Func::update_return_data( $data_status, $where );
		$obj = $obj[0];
		echo 'THE OBJ: ';
		print_r($obj);
		
		// Get array of items to get ticket_id for Audit Log
		$box_list = ($obj->box_id) ? $obj->box_id : []; 
		$folderfile_list = ($obj->folderdoc_id) ? $obj->folderdoc_id : [];
		
		// Create single array with box and folderdocs
		$collated_box_folderfile_list = [];
		
		foreach( $box_list as $key=>$box ) {
			if( $box != $db_null ) {
				$collated_box_folderfile_list[] = $box;
			} else {
				$collated_box_folderfile_list[] = $folderfile_list[$key];
			}
		}
		
		foreach( $collated_box_folderfile_list as $item ) {
			
			$where = ['box_folder_file_id' => $item ];
			$ticket_array = Patt_Custom_Func::get_ticket_id_from_box_folder_file($where);
			
			echo ' where: ';
			print_r($where);
			echo ' ticket array: ';
			print_r($ticket_array);
			
			do_action('wpppatt_after_return_cancelled', $ticket_array['ticket_id'], 'RTN-'.$return_id);
		}
			
		
		//Update the Updated Date
/*
		$current_datetime = date("yy-m-d H:i:s");
		$update = [	'updated_date' => $current_datetime ];
		$where = [ 'id' => $recall_id ];
		Patt_Custom_Func::update_return_dates($update, $where);
*/
		
	}
	
	
	

//	do_action('wpppatt_after_recall_cancelled', $ticket_id, 'R-'.$recall_id);
	
} 




?>