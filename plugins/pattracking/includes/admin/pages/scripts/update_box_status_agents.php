<?php

global $wpdb, $current_user, $wpscfunction;

$path = preg_replace('/wp-content.*$/','',__DIR__);
include($path.'wp-load.php');

$recall_id = isset($_POST['recall_id']) ? sanitize_text_field($_POST['recall_id']) : '';
$type = isset($_POST['type']) ? sanitize_text_field($_POST['type']) : '';
$title = isset($_POST['title']) ? sanitize_text_field($_POST['title']) : '';
$recall_ids = $_REQUEST['recall_ids']; 
//$ticket_id = isset($_POST['ticket_id']) ? sanitize_text_field($_POST['ticket_id']) : '';
//$recall_ids = json_decode($recall_ids);
//$num_of_recalls = count($recall_ids);


if( $type == 'box_status_agents' ) {

	//
	// Old Requests
	//
	$recall_requestors = $_REQUEST['new_requestors']; 
	$old_recall_requestors = $_REQUEST['old_requestors'];
	
	//
	// New Requests
	//
	$new_agents_array = $_REQUEST['new_agents_array'];
	$item_ids = $_REQUEST['item_ids'];
	$is_single_item = (count($item_ids) > 1 ) ? false : true;


	//$new_agents_array = translate_user_id($new_agents_array, 'wp_user_id' );

	// Debug Area
	echo 'is single: ';
	echo $is_single_item;
	echo 'New Agent Array: ';
	print_r($new_agents_array);
	
	
	if( $is_single_item ) {
		$single_box_id = Patt_Custom_Func::get_box_file_details_by_id($item_ids[0])->Box_id_FK;
		
		foreach( $new_agents_array as $status_users ) {			
			$user_status_array = array();
			$user_status_array[ $status_users['term'] ] = Patt_Custom_Func::translate_user_id( $status_users['agents'], 'wp_user_id' );
			
			$data = [
				'box_id' => $single_box_id,
				'status' => $user_status_array
			]; 
			
			$update_status_by_id = Patt_Custom_Func::update_status_by_id( $data );	
			
			// Debug Area
			echo 'User Status Array: ';
			print_r($user_status_array);
			echo 'Data: ';
			print_r($data);
			echo '<br>Update Status by ID: ';
			echo $update_status_by_id;
			
			
		}
		
	} else {
		foreach( $item_ids as $box_id ) {
			foreach( $new_agents_array as $status_users ) {			
				if( is_array($status_users['agents']) && count($status_users['agents']) > 0 ) {
					$user_status_array = array();
					$user_status_array[ $status_users['term'] ] = Patt_Custom_Func::translate_user_id( $status_users['agents'], 'wp_user_id' );
					
					$data = [
						'box_id' => Patt_Custom_Func::get_box_file_details_by_id($box_id)->Box_id_FK,
						'status' => $user_status_array
					]; 
					
					$update_status_by_id = Patt_Custom_Func::update_status_by_id( $data );	
				}
			}
		}
	}
	
	
		
	// Debug info
	//$test_item_1 = $new_agents_array[0]['term']; 
	//$test_item_2 = $new_agents_array[1]['agents'];	
	
	// NEW
	// Compiles the object for insertion
	
	
	
	// OLD
	// Audit Log string for previously assigned agents
	$old_assigned_agents_string = '';
	$old_assigned_agents_array = array();
	foreach ( $old_recall_requestors as $agent ) {
		$old_assigned_agents_string .= get_term_meta( $agent, 'label', true);
		array_push($old_assigned_agents_array, get_term_meta( $agent, 'user_id', true));
		$old_assigned_agents_string .= ', ';
	}
	$old_assigned_agents_string = substr($old_assigned_agents_string, 0, -2);
	
	// OLD
	// Audit Log string for newly assigned agents
/*
	$new_assigned_agents_string = '';
	$new_assigned_agents_array = array();
	foreach ( $recall_requestors as $agent ) {
		$new_assigned_agents_string .= get_term_meta( $agent, 'label', true);
		array_push($new_assigned_agents_array, get_term_meta( $agent, 'user_id', true));
		$new_assigned_agents_string .= ', ';
	}
	$new_assigned_agents_string = substr($new_assigned_agents_string, 0, -2);
	$recall_requestors_string = $old_assigned_agents_string.' -> '.$new_assigned_agents_string;	
*/
	
	
	// DEBUG INFO
/*
	echo 'Test 1: '.PHP_EOL;
	print_r($test_item_1);	
	echo PHP_EOL.'Test 2: '.PHP_EOL;
	print_r($test_item_2);
	echo 'Type: '.$type.PHP_EOL;
	echo 'New Agents Array: '.PHP_EOL;
	print_r($new_agents_array);
	echo 'Items Array: '.PHP_EOL;
	print_r($item_ids);
	echo 'User Status Array: '.PHP_EOL;
	print_r($user_status_array);
	echo 'Data Array: '.PHP_EOL;
	print_r($data);
	echo 'New Data Array: '.PHP_EOL;
	print_r($data2);
	echo 'User LUT: '.PHP_EOL;
*/
	print_r($user_lut);
//	echo 'Old: '.$old_assigned_agents_string;
//	echo 'New: '.$new_assigned_agents_string;	
	echo 'Combo: '.$recall_requestors_string.PHP_EOL;
	echo 'ticket id: '.$ticket_id.PHP_EOL;
	//print_r($old_recall_requestors);	
	//echo 'Requestor String: '.$recall_requestors_string.PHP_EOL;
// 	echo 'Requestor Value: '.$new_requestor_value.PHP_EOL;
	
	// Update the Users associated with the Recall. 
/*
	$data = [
			'recall_id' => $recall_id,			
			'user_id' => $new_assigned_agents_array
		];
*/
	//Patt_Custom_Func::update_recall_user_by_id($data);
	
	
	//This will be it. 
// 	$update_status_by_id = Patt_Custom_Func::update_status_by_id($data2);
	
	//Update the Updated Date
	$current_datetime = date("yy-m-d H:i:s");
	$update = [	'updated_date' => $current_datetime ];
	$where = [ 'id' => $recall_id ];
	//Patt_Custom_Func::update_recall_dates($update, $where);
	
	//
	// Audit Log
	//
	
	// Register Box Status Taxonomy
	if( !taxonomy_exists('wpsc_box_statuses') ) {
		$args = array(
			'public' => false,
			'rewrite' => false
		);
		register_taxonomy( 'wpsc_box_statuses', 'wpsc_ticket', $args );
	}
	
	
	$status_and_users_str = '';
	foreach( $item_ids as $id ) {

		$where = ['box_folder_file_id' => $id ];
		$ticket_id = Patt_Custom_Func::get_ticket_id_from_box_folder_file( $where );
		
		foreach($new_agents_array as $status_and_user_array ) {
			$status_id = $status_and_user_array['term'];
			$status_obj = get_term_by( 'id', $status_id, 'wpsc_box_statuses');
// 			echo 'Status obj: ';
// 			print_r($status_obj);
			$status_str = $status_obj->name;
			
			$agent_users = $status_and_user_array['agents'];
			$wp_users = Patt_Custom_Func::translate_user_id( $agent_users, 'wp_user_id' );
			echo 'agent users:';
			print_r($agent_users);
			echo 'wp users:';
			print_r($wp_users);
			
			$user_str = '';
			if( $agent_users != null ) {
				foreach( $wp_users as $user_id ) {
					$user_obj = get_userdata($user_id);
					$user_str .= $user_obj->user_login.', ';
				}
				$user_str = substr($user_str, 0, -2);
			}
			
			$prefix = '<li>';
			$postfix = '</li>';
			
			if( $user_str == '' ) {

			} else {

				$status_and_users_str .= $prefix.$status_str.': '.$user_str.$postfix;
			}
			
		}
		
		$status_and_users_str = '<ul>'.$status_and_users_str.'</ul>';
		
		do_action('wpppatt_after_box_status_agents', $ticket_id['ticket_id'], $status_and_users_str, $id);
		
		$status_and_users_str = ''; //clear string for next audit
		
		
		// Set PM Notifications 
		$notifications = '';
		$notification_post = 'email-user-added-to-box-status';

		$unique_agent_id_array = [];
		
		foreach($new_agents_array as $status_and_user_array ) {
			
			echo 'unique_agent_id_array: ' . PHP_EOL;
			print_r($unique_agent_id_array);
			echo 'status_and_user_array: ' . PHP_EOL;
			print_r( $status_and_user_array );
			if( $status_and_user_array['agents'] != null ) {
				$unique_agent_id_array = array_merge( $unique_agent_id_array, $status_and_user_array['agents'] );
			}
		}

		// remove duplicate agent_ids.
		$unique_agent_id_array = array_unique( $unique_agent_id_array );
		
		// remove any agent_ids that were previously assigned to this box.
		$new_unique_agent_array = array_diff( $unique_agent_id_array, $old_recall_requestors );
		
		//$pattagentid_admin_array = Patt_Custom_Func::agent_from_group( 'Administrator' );
		//$pattagentid_array = array_merge( $pattagentid_admin_array, $assigned_agent_ids );
		
		$data = [
	        'item_id' => $id,
	        'action_initiated_by' => $current_user->display_name
	    ];
		$email = 0;
		
		$new_notification = Patt_Custom_Func::insert_new_notification( $notification_post, $unique_agent_id_array, $id, $data, $email );
		
		// D E B U G 
/*
		echo 'T h e    N o t i f i c a i t o n: ';
		echo 'notification_post: ' . $notification_post ;
		echo 'agent array: ';
		print_r( $new_agents_array );
		print_r($unique_agent_id_array);
		echo 'id' . $id ;
		echo 'data: ';
		print_r( $data );
		echo PHP_EOL . PHP_EOL;
		echo 'old requestors: ' . PHP_EOL;
		print_r( $old_recall_requestors );
		echo 'new_unique_agent_array: ' . PHP_EOL;
		print_r( $new_unique_agent_array );
*/
		
	}
	
	
	
	
	
} elseif( $type == 'cancel' ) {
	
	
	
} 


?>