<?php
/**
 * Template Name: Debug File
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WordPress
 * @subpackage Twenty_Nineteen
 * @since 1.0.0
 */

include WPPATT_PLUGIN_URL.'asset/lib/qr/qrcode.php';

get_header();
global $wpdb, $current_user;
?>

<p><img src="<?php echo WPPATT_PLUGIN_URL; ?>asset/lib/qr/qrcode.php?s=qrl&d=https://www.google.com"></p>

	<div id="primary" class="content-area">
		<main id="main" class="site-main">
			This is a test template in child theme.
			<br/>

<?php
	
	//global $wpdb;
								
	$table_name = $wpdb->prefix.'wpsc_epa_folderdocinfo_files';
	
	$data_array = [
		'post_id' => 999,
		'folderdocinfo_id'  => '111',
		'folderdocinfofile_id'   => '222',
		'attachment' => '1',
		'file_name'  => 'filename',
// 			'file_location'   => '/uploads/mdocs/',
		'object_location'   => '/uploads/mdocs/',
		'title'  => 'amazing title',
		'description'   => 'x',
		'tags' => 'second,third',
	];
	
/*
	$wpdb->insert(
		$table_name,
		$data_array
	);
*/
	
/*
	$wpdb->insert(
		$table_name,
		array(
			'post_id' => $upload['parent_id'],
			'folderdocinfo_id'  => $post_vars['mdocs-cat'],
			'folderdocinfofile_id'   => $folderdocinfofile_id,
			'attachment' => '1',
			'file_name'  => $upload['filename'],
// 			'file_location'   => '/uploads/mdocs/',
			'object_location'   => '/uploads/mdocs/',
			'title'  => $upload['name'],
			'description'   => $upload['desc'],
			'tags' => $post_vars['mdocs-tags'],
		)
	);
*/

	$folderdocfiles_info = [
		'post_id' => 999,
		'folderdocinfo_id'  => '111',
		'folderdocinfofile_id'   => '222',
		'attachment' => '1',
		'file_name'  => 'filename',
		'object_location'   => '/uploads/mdocs/',
		'title'  => 'amazing title',
		'description'   => 'x',
		'tags' => 'second,third',
	];
	
/*
	$folderdocfiles_info = [
		'post_id' => 888, 
		'folderdocinfo_id' => '111', // Use last set counter
		'folderdocinfofile_id' => '222',
		'attachment' => '1',
		'file_name' => 'test name',
		'file_location' => '/uploads/mdocs/',
		'source_file_location' => 'random location',
		'file_object_id' => '333',
		'file_size' => '0',
		'title' => 'test title',
		'description' => '-',
		'tags' => 'none, kidding',
		'object_key' => 'x',
		'object_location' => 'x',
	];
*/

 	//$wpdb->insert( $wpdb->prefix . 'wpsc_epa_folderdocinfo_files', $folderdocfiles_info );
	//$diditwork = $wpdb->insert( 'wpqa_wpsc_epa_folderdocinfo_files', $folderdocfiles_info ); // failing. 
	$folderdocfiles_info_id = $wpdb->insert_id;
	
	echo 'Insert Data: <br>';
	//print_r($agent_id_array);

	//print_r($user_roles_array);	
	echo 'insert: ' . $diditwork ;
	echo '<br>';
	echo 'insert id: ' . $folderdocfiles_info_id;
	echo '<br>';
	echo '<pre>';
	print_r($folderdocfiles_info);
	echo '</pre>';
	echo '<hr>';
	
	
	$where = [
		'recall_id' => '0000013'
	];
	$recall_data = Patt_Custom_Func::get_recall_data( $where );
	$agent_id_array = Patt_Custom_Func::translate_user_id( $recall_data[0]->user_id, 'agent_term_id' );
	
	$user_roles_array = Patt_Custom_Func::get_agent_user_role_lut();
	
	$agent_id_array_2 = [ 32, 60, 623, 626, 632, 883 ]; // 324
// 	$role_array = [ 'Administrator', 'Manager', 'Agent', 'Requester' ];
	$role_array = [  'Agent' ];
	
	$results = Patt_Custom_Func::return_agent_ids_in_role( $agent_id_array_2, $role_array);
	
/*
	$where = [
		'ticket_id' => '0000005'
	];
	$ticket_owner_id_array = Patt_Custom_Func::get_ticket_owner_agent_id( $where );
	$pattagentid_array = array_unique(array_merge( $agent_id_array, $ticket_owner_id_array ));
*/
	
    echo 'Recall Data: <pre>';
	print_r($agent_id_array);

	//print_r($user_roles_array);	
	print_r( $results );
	echo '</pre><br>';
	echo '<hr>';

/*
	$ticket_id = '0000001';	
	$where = [ 'ticket_id' => $ticket_id ];
	$agent_id = Patt_Custom_Func::get_ticket_owner_agent_id( $where );

	echo 'Ticket ID: '.$ticket_id.'<br>';	
	echo 'where: '.'<br>';
	print_r($where);
	//echo '<br>agent_id: '.$agent_id.'<br>';
	echo '<pre>';
	print_r($agent_id);
	echo '</pre>';	
	echo '<hr>';


	$agent_group_name = 'Administrator';
	$agent_group_array = Patt_Custom_Func::agent_from_group($agent_group_name);
	print_r($agent_group_array);
	echo '<br>';
	
	$return_id = '0000001';	
	$where = [ 'id' => $return_id ];
	$current_datetime = date("yy-m-d H:i:s");
 	$data = [ 'return_receipt_date' => $current_datetime ]; 
	$obj = Patt_Custom_Func::update_return_data( $data, $where );

	echo 'Return ID: '.$return_id.'<br>';	
	echo 'where: '.$where.'<br>';
	echo 'current_datetime: '.$current_datetime.'<br><pre>';		
	echo $obj;
	echo '</pre><br>';
	echo '<hr>';
*/

/*
function IsResourceLocal($url){
    if( empty( $url ) ){ return false; }
    $urlParsed = parse_url( $url );
    $host = $urlParsed['host'];
    if( empty( $host ) ){ 
    // maybe we have a relative link like: /wp-content/uploads/image.jpg 
    // add absolute path to begin and check if file exists 
    $doc_root = $_SERVER['DOCUMENT_ROOT'];
    $maybefile = $doc_root.$url;
    // Check if file exists 
    $fileexists = file_exists ( $maybefile );
    if( $fileexists ){
        // maybe you want to convert to full url? 
        return true;        
        }
     }
    // strip www. if exists 
    $host = str_replace('www.','',$host);
    $thishost = $_SERVER['HTTP_HOST'];
    // strip www. if exists 
    $thishost = str_replace('www.','',$thishost);
    if( $host == $thishost ){
        return true;
        }
    return false;
}
*/

/*
$external_url = 'http://086.info';
echo IsResourceLocal($external_url); echo '<br />'.$external_url;
$internal_url = 'http://yahoo.com';
echo IsResourceLocal($internal_url); echo '<br />'.$internal_url;
*/


        $ticket_id = '6';
		$padded_request_id = Patt_Custom_Func::ticket_to_request_id($ticket_id);
        echo 'Request ID: <pre>';
		echo $padded_request_id;
		echo '</pre><br>';
		echo '<hr>';
		

        $patt_id = 'D-0000006';
		$patt_id_type = Patt_Custom_Func::patt_id_type($patt_id);
        echo 'PATT ID Type: <pre>';
		//print_r($new_notification);
		echo $patt_id_type;
		echo '</pre><br>';
		echo '<hr>';
		
		$notification_post = 'email-new-items-have-been-declined-in-id';
        $pattagentid_array = [626];
        $requestid = 'D-0000018';
        
        
        $data = [
	        'item_id' => ['0000003-2'],
	        'action_initiated_by' => 'Aaron Podbelski'
        ];
        $email = 0;
        
        //$new_notification = Patt_Custom_Func::insert_new_notification( $notification_post, $pattagentid_array, $requestid, $data, $email );
		
        echo 'Add notifications [Decline]: <pre>';

		echo $new_notification;
		echo '</pre><br>';
		echo '<hr>';
		
		$notification_post = 'email-id-has-been-recalled';
        $pattagentid_array = [626];
        $requestid = 'R-0000013';
        
        
        $data = [
	        'item_type' => 'Box', 
	        'item_id' => '0000001-1',
	        'action_initiated_by' => $current_user->display_name
	    ];
		$email = 0;
        
        //$new_notification = Patt_Custom_Func::insert_new_notification( $notification_post, $pattagentid_array, $requestid, $data, $email );
        echo 'Add notifications [Recall]: <pre>';

		echo $new_notification;
		echo '</pre><br>';
		echo '<hr>';
		
		
		
		

        $user_id_array = [1,2,4];
		$covert_patt_id = Patt_Custom_Func::translate_user_id($user_id_array,'agent_term_id');
        echo 'User ID Conversion: <pre>';
		//print_r($new_notification);
		print_r($covert_patt_id);
		echo '</pre><br>';
		echo '<pre>';
		print_r($user_id_array);
		echo '</pre>';
		echo '<hr>';
		
        $identifier = '0000001-1-02-1';
        $type = 'folderfile';
		$request_return_check = Patt_Custom_Func::id_in_return($identifier,$type);
		echo $identifier.'<br />';
		echo 'Request contains decline? <pre>';
		echo $request_return_check;
		echo '</pre><br>';
		echo '<hr>';
		
		
        $identifier = '0000001-1-02-1';
        $type = 'folderfile';
		$request_recall_check = Patt_Custom_Func::id_in_recall($identifier,$type);
		echo $identifier.'<br />';
		echo 'Request contains recall? <pre>';
		print_r( $request_recall_check);
		echo '</pre><br>';
		echo '<hr>';
		
		
		echo '<hr/>';
		echo "Item in Recall<br/>";
// 		$item_id = '0000002-1'; 
// 		$item_id = '0000013-3-02-23'; 
		$item_id = '0000008'; 
// 		$type = 'Box';
// 		$type = 'Folder/Doc';		
// 		$subfolder_path = 'http://086.info/wordpress3/';
		echo 'item id: '.$item_id.'<br>';
/*
		echo 'type: '.$type.'<br>';
		echo 'subfolder path: '.$subfolder_path.'<br>';
*/
		
/*
		$box_fk = Patt_Custom_Func::get_id_by_box_id( $item_id );
		
		echo 'box FK: '.$box_fk.'<br>';
*/
		
		
		$recall_info = Patt_Custom_Func::item_in_recall($item_id);
		echo 'Recall Info: <pre>';
		print_r($recall_info);
		echo '</pre><br>';
		echo 'In Recall: '.$recall_info['in_recall'];
		echo '<hr>';

		
		echo '<hr/>';
		echo "Item in Return<br/>";
		$item_id = '0000002-1'; 
// 		$item_id = '0000010-2-01-10'; 
// 		$item_id = '0000009-1-01-3'; 		
		$type = 'Box';
// 		$type = 'Folder/Doc';		
		$subfolder_path = 'http://086.info/wordpress3/';
		echo 'item id: '.$item_id.'<br>';
		echo 'type: '.$type.'<br>';
		echo 'subfolder path: '.$subfolder_path.'<br>';
		
/*
		$box_fk = Patt_Custom_Func::get_id_by_box_id( $item_id );
		
		echo 'box FK: '.$box_fk.'<br>';
*/
		
		
		$ret = Patt_Custom_Func::item_in_return($item_id, $type, $subfolder_path);
		echo 'Return: <pre>';
		print_r($ret);
		echo '</pre><br>';
		echo '<hr>';
		
		echo "EIDW LAN CHECK<br/>";
		$lanid = 'ayuen1111';
		$request_id = '0000001';
		$laninfo_json = Patt_Custom_Func::lan_id_check($lanid,$request_id);
		echo $laninfo_json;
		echo '<hr/>';
		
		
		echo "EIDW LAN ID TO JSON<br/>";
		$lanid = 'LNGUYE02';
		$laninfo_json = Patt_Custom_Func::lan_id_to_json($lanid);
		echo $laninfo_json;
		echo '<hr/>';
		
		
		echo "<hr>"; 
		echo "Get ID by Folderdoc ID<br/>";
		//$folderdoc_id = '0000001-1-01-4';
		$folderdoc_id = '0000009-1-01-3';		
		
		$the_ID = Patt_Custom_Func::get_id_by_folderdoc_id($folderdoc_id);

		echo 'Box ID: '.$folderdoc_id."<br>"; 
		echo 'The ID: '.$the_ID; 
		
		echo "<hr>"; 
		echo "Get ID by Box ID<br/>";
		$box_id = '0000001-3';
		
		$the_ID = Patt_Custom_Func::get_id_by_box_id($box_id);

		echo 'Box ID: '.$box_id."<br>"; 
		echo 'The ID: '.$the_ID; 
		
	
		
		echo "<hr>"; 
		echo "Get Program Office ID by Acronym<br/>";
		$acro = 'R01-MSD';
		$acro = 'AO';
		echo 'Acronym: '.$acro.'<br />';
		$acro_num = Patt_Custom_Func::get_program_offic_id_by_acronym($acro);
		if ($acro_num == false) {
		echo 'Invalid Program Office acronym'; 
		} else {
        echo 'Program Office ID: ' . $acro_num.'<br>';
        print_r($acro_num);
		}

		echo "<hr>";
		
		echo "<hr>"; 
		echo "Shipping tracking number check<br/>";
		$tracking_number = '1Z5X268X9093033313';
		echo $tracking_number.'<br />';
		$tracking_url = Patt_Custom_Func::get_tracking_url($tracking_number);
		if ($tracking_url == false) {
		echo 'Invalid Tracking Number'; 
		} else {
        echo 'URL: <a href="' . $tracking_url.'" target="_blank">SHIPPING URL</a>';
		}
		
		echo "<hr>"; 
		echo "Shipping tracking carrier<br/>";
		$tracking_number = '1Z5X268X9093033313';
		echo $tracking_number.'<br />';
		$tracking_carrier = Patt_Custom_Func::get_shipping_carrier($tracking_number);
		echo $tracking_carrier;
		echo '<br>';
		echo ($tracking_carrier == '' ) ? 'true' : 'false';

		echo "<hr>";
		echo "Accession Count for a Request<br/>";
		$ticket_id = 1;
		$accession_count = Patt_Custom_Func::get_accession_count($ticket_id);
        echo 'Accession count for Request # 0000001: ' . $accession_count;


		echo "<hr>";
		echo "Acceptable Box Status given item IDs<br/>";

		$item_ids = ["0000001-2", "0000003-2", "0000003-3"];
		//$item_ids = ["0000001-3", "0000003-2", "0000009-2"];		
		//$item_ids = ["0000009-1", "0000003-2"];		

		$box_statuses = Patt_Custom_Func::get_restricted_box_status_list( $item_ids );
		
		echo '<pre>';
		print_r($box_statuses);
		echo '</pre>';
		echo "<hr>";
		
		
		echo "<hr>";
		echo "Get All Statuses From Taxonomy<br/>";

		$tax = 'wpsc_box_statuses';
		$tax = 'wpsc_box_statuses';
		$tax = 'wppatt_return_statuses';
						
		$status_list = Patt_Custom_Func::get_all_status_from_tax($tax);
		
		echo '<pre>';
		print_r($status_list);
		echo '</pre>';
		echo "<hr>";
		
		echo "<hr>";
		echo "Insert return data<br/>";

// 		$num = rand ( 10000 , 99999 ); // $ticket_id
// 		$str_length = 7;
// 		$return_id = substr("000000{$num}", -$str_length);

		$data = [
// 			'return_id' => "$return_id",
			'box_id' => [2,3,6,7],
			'folderdoc_id' => [8],
			'shipping_tracking_info' => [
    			'tracking_number' => 3, //working: [2,3,17,21,31,33,34,35,39,42,43,45] //Not Working: [0,1,4-16,18-20,22,30,32,36,37,38,40,41,44,46]
    			'company_name' => 'USPS',
			 ],
			'user_id' => 2, //[2,5,67,5]
			'return_reason_id' => 5,
			'return_date' => '2020-06-02 00:00:00',
			'return_receipt_date' => '2020-06-02 00:00:00',
			'expiration_date' => '2020-06-02 00:00:00',
			'comments' => 'test insert comment',
		];

//  		$return_insert_id = Patt_Custom_Func::insert_return_data($data);
		echo '<pre>';
		print_r($return_insert_id);
		echo '</pre>';
		echo '<hr/>';
        //  die();
		
		
		echo "Get Ticket ID from Box Folder File ID<br/>";
		/**
		 * Get all status from wp_terms
		 */
		$where = ['box_folder_file_id' => '0000001-2' ];
		$ticket_id = Patt_Custom_Func::get_ticket_id_from_box_folder_file( $where );
		echo '<pre>';
		print_r($ticket_id);
		echo '</pre>';
		echo '<hr/>';



		echo "Show all the statuses<br/>";
		/**
		 * Get all status from wp_terms
		 */
		$ignore_status = ['Pending', 'Ingestion', 'Completed', 'Dispositioned'];
		$get_all_status = Patt_Custom_Func::get_all_status();
		echo '<pre>';
		print_r($get_all_status);
		echo '</pre>';
		echo '<hr/>';
		// die();

 		echo "Update user status<br/>";
		$data = [
			'box_id' => 2,
			'status' => [
					672 => [6]
				]
		]; 

		$update_status_by_id = Patt_Custom_Func::update_status_by_id($data);
		echo '<pre>';
		print_r($update_status_by_id);
		echo '</pre>';
		echo '<hr/>';
		
		echo "Get all user status data<br/>";
		/**
		 * Pass variables
		 */
		$where = [
			// 'box_id' => 2,
			// 'user_id' => 2,
			// 'status_id' => 672	
		];
		$get_user_status_data = Patt_Custom_Func::get_user_status_data($where);
		echo '<pre>';
		print_r($get_user_status_data);
		echo '</pre>';
		echo '<hr/>';
		// die();

 		echo "Insert user status<br/>";
/*
		$data = [
			'box_id' => 2,
			'status' => [
					621 => [2]
				]
		];
*/
		
		$data = [
			'box_id' => 2,
			'status' => [
					672 => [2,3,4]
				]
		];  

		//$user_status_insert = Patt_Custom_Func::user_status_insert($data);
		echo '<pre>';
		print_r($user_status_insert);
		echo '</pre>';
		echo '<hr/>';



				
		echo '<hr/>';

		echo "PATT BOX ID to PATT REQUEST ID<br/>";
		$patt_request_id = Patt_Custom_Func::convert_box_request_id('0000001-1');
		echo $patt_request_id;
		
		
		echo "Update return user data<br/>";

		$data = [
			'return_id' => 4,
			'user_id' => [4, 5, 6, 2, 7],
		];

		$date_updated = Patt_Custom_Func::update_return_user_by_id($data);
		echo '<pre>';
		print_r($date_updated);
		echo '</pre>';
		echo '<hr/>';

	    echo "Get all return data<br/>";
		/**
		 * Passing single return ID, multiple IDs, status id, programe office id or digitization center
		 */
		$where = [
			 'id' => 3,
			// 'id' => [19, 20],
			// 'return_id' => 19,
			// 'return_id' => ['19', '20'],
			// 'return_status_id' => 5,
			// 'program_office_id' => 2,
			//'program_office_id' => 19,			
			// 'digitization_center' => 'East' ,
			//'filter' => [
				// 'records_per_page' => 3,
				// 'paged' => 2,
				//'orderby' => 'return_id',
				//'order' => 'ASC',
			//],
		];
		$return_array = Patt_Custom_Func::get_return_data($where);
		echo '<pre>';
		print_r($return_array);
		echo '</pre>';
		echo '<hr/>';


/*
       echo "Insert return data<br/>";

// 		$num = rand ( 10000 , 99999 ); // $ticket_id
// 		$str_length = 7;
// 		$return_id = substr("000000{$num}", -$str_length);

		$data = [
// 			'return_id' => "$return_id",
			'box_id' => [1,2],
			'folderdoc_id' => [2],
			'shipping_tracking_id' => 3,
			'user_id' => 2, //[2,5,67,5]
			'return_reason_id' => 5,
			'return_date' => '2020-06-02 00:00:00',
			'return_receipt_date' => '2020-06-02 00:00:00',
			'expiration_date' => '2020-06-02 00:00:00',
			'comments' => 'dfbdfbd',
		];

		$return_insert_id = Patt_Custom_Func::insert_return_data($data);
		echo '<pre>';
		print_r($return_insert_id);
		echo '</pre>';
		echo '<hr/>';
*/


		
		echo "Update recall user data<br/>";

		$data = [
			'recall_id' => 19,			
			'user_id' => [4,5,6,2,7]
		];

		$date_updated = Patt_Custom_Func::update_recall_user_by_id($data);
		echo '<pre>';
		print_r($date_updated);
		echo '</pre>';
		echo '<hr/>';
		
		
		echo "Get Box Details By ID<br/>";
		//$item_id = '0000238-1';
		//$item_id = '0000001-1';
// 		$item_id = '0000001-1-02-1';
		$item_id = '0000007-1';		
		
		//$box_array = Patt_Custom_Func::get_box_file_details_by_id('0000238-1');
		//$box_array = Patt_Custom_Func::get_box_file_details_by_id('0000001-1');
		$box_array = Patt_Custom_Func::get_box_file_details_by_id($item_id);
		
		echo 'Item ID: '.$item_id;
		echo '<pre>';
		print_r($box_array);
		echo '</pre>';
		echo '<hr/>';
		
		echo "Insert recall data<br/>";

		$num = rand ( 10000 , 99999 ); // $ticket_id
		$str_length = 7;
		$recall_id = substr("000000{$num}", -$str_length);

		$data = [
			// 'recall_id' => "$recall_id",
			'box_id' => 7,
			'folderdoc_id' => 4,
			'program_office_id' => 2,
			'shipping_tracking_id' => 24,
			'record_schedule_id' => 1,
			'user_id' => 2, //[2,5,67,5]
			'recall_status_id' => 5,
			'expiration_date' => '2020-06-02 00:00:00',
			'request_date' => '2020-06-02 00:00:00',
			'request_receipt_date' => '2020-06-02 00:00:00',
			'return_date' => '2020-06-02 00:00:00',
			'updated_date' => '2020-06-02 00:00:00',
			'comments' => 'dfbdfbd',
		];

		//$recall_insert_id = Patt_Custom_Func::insert_recall_data($data);
		echo '<pre>';
		print_r($recall_insert_id);
		echo '</pre>';
		echo '<hr/>';


		echo "Get all recall data<br/>";
		/**
		 * Passing single recall ID, multiple IDs, status id, programe office id or digitization center
		 */
		
		//$where = [ 'recall_id' => '0000105' ]; //folder
		//$where = [ 'recall_id' => '0000096' ]; //box - working
		$where = [ 'recall_id' => '0000002' ]; 
/*
		$where = [
			// 'id' => 19, 
			// 'id' => [19, 20], 
			// 'recall_id' => 19, 
			'recall_id' => ['0000001', '0000002']
			// 'recall_status_id' => 5,
			// 'program_office_id' => 2,
			// 'digitization_center' => 'East'  ,
			'filter' => [
				'records_per_page' => 3,
				'paged' => 2,
				'orderby' => 'recall_id',
				'order' => 'ASC'
			]
		]; 
*/
		$recall_array = Patt_Custom_Func::get_recall_data($where);
		echo '<pre>';
		print_r($recall_array);
		echo '</pre>';
		echo '<hr/>';


		echo "Get shipping data by recall ids<br/>";
		/**
		 * Passing single recall ID, multiple IDs, status id, programe office id or digitization center
		 */
		$where = [
			'recallrequest_id' => 1, 
			// 'recallrequest_id' => ['5', '6']
		]; 
		$shipping_data = Patt_Custom_Func::get_shipping_data_by_recall_id($where);
		echo '<pre>';
		print_r($shipping_data);
		echo '</pre>';
		echo '<hr/>';


		
		echo "Insert shipping data<br/>";
		$data = [
			'ticket_id' => 7,
			'recallrequest_id' => 4,
			'company_name' => 2,
			'tracking_number' => 24,
			'status' => 1
		];

		$shipping_insert_id = Patt_Custom_Func::add_shipping_data($data);
		echo '<pre>';
		print_r($shipping_insert_id);
		echo '</pre>';
		echo '<hr/>';		


		echo "Delete shipping tracking records by recall ID<br/>";

		$where = [
			'recallrequest_id' => 4,
			// 'recallrequest_id' => ['5', '6']
		];
		$shipping_data_array = Patt_Custom_Func::delete_shipping_data_by_recall_id( $where);
		echo '<pre>';
		print_r($shipping_data_array);
		echo '</pre>';
		echo '<hr/>';

				
		echo "Get shipping data by recall ids<br/>";
		/**
		 * Passing single recall ID, multiple IDs, status id, programe office id or digitization center
		 */
		$where = [
			// 'recallrequest_id' => 1, 
			'recallrequest_id' => ['5', '6']
		]; 
		$shipping_data = Patt_Custom_Func::get_shipping_data_by_recall_id($where);
		echo '<pre>';
		print_r($shipping_data);
		echo '</pre>';
		echo '<hr/>';

		echo "Update request date - recall ID<br/>";
		$update = [
			'request_date' => '2020-05-05 00:00:00'
		];
		$where = [
			'id' => 3
		];
		$recall_array = Patt_Custom_Func::update_recall_dates($update, $where);
		echo '<pre>';
		print_r($recall_array);
		echo '</pre>';
		echo '<hr/>';

		echo "Get return primary key by returnid<br/>";
		/**
		 * Get return primary key by returnid
		 */
		$where = [
			'return_id' => 'ytvybft567747467'
		]; 
		$return_data = Patt_Custom_Func::get_primary_id_by_retunid($where);
		echo '<pre>';
		print_r($return_data);
		echo '</pre>';
		echo '<hr/>';


		echo "Get ticket id by returnid or recall_id<br/>";
		/**
		 * Get ticket id by returnid or recall_id
		 */
		$where = [
			'return_id' => 34,
			// 'recallrequest_id' => 1
		]; 
		$ticket_id_by = Patt_Custom_Func::get_ticket_id_by($where);
		echo '<pre>';
		print_r($ticket_id_by);
		echo '</pre>';
		echo '<hr/>';

		echo "Update request date - recall ID<br/>";
		$update = [
			'request_date' => '2020-05-05 00:00:00'
		];
		$where = [
			'id' => 3
		];
		$recall_array = Patt_Custom_Func::update_recall_dates($update, $where);
		echo '<pre>';
		print_r($recall_array);
		echo '</pre>';
		echo '<hr/>';

		echo "Update received date - recall ID<br/>";
		$update = [
			'request_receipt_date' => '2020-05-05 00:00:00'
		];
		$where = [
			'id' => 3
		];
		$recall_array = Patt_Custom_Func::update_recall_dates($update, $where);
		echo '<pre>';
		print_r($recall_array);
		echo '</pre>';
		echo '<hr/>';


		echo "Update return date - recall ID<br/>";
		$update = [
			'return_date' => '2020-05-05 00:00:00'
		];
		$where = [
			'id' => 3
		];
		$recall_array = Patt_Custom_Func::update_recall_dates($update, $where);
		echo '<pre>';
		print_r($recall_array);
		echo '</pre>';
		echo '<hr/>';


		echo "Auto-assign Digitization Center<br/>";
		$box_array = Patt_Custom_Func::get_default_digitization_center(238);
		print_r($box_array);
		echo '<hr/>';
		
		echo "Obtain array of Box ID's <br/>";
		$box_array = Patt_Custom_Func::fetch_box_id(1);
		print_r($box_array);
		echo '<hr/>';
		
		echo "Obtain array of Program Offices<br/>";
		$po_array = Patt_Custom_Func::fetch_program_office_array();
		print_r($po_array);
		echo '<hr/>';
		
		
		echo "Convert PATT Request ID to DB ID<br/>";
		$GLOBALS['id'] = $_GET['id'];
		echo $request_id;
		echo Patt_Custom_Func::convert_request_id($GLOBALS['id']);
		echo '<hr/>';
		
		echo "Obtain array of Box Information for frontend/backend Request Details page <br/>";
		$box_details_array = Patt_Custom_Func::fetch_box_details(1);
		print_r($box_details_array);
		echo '<hr/>';

		echo "Function to obtain location value from database <br/>";
        $box_location = Patt_Custom_Func::fetch_location(1);
		print_r($box_location);
		echo '<hr/>';

		echo "Function to obtain program office from database <br/>";
        $box_program_office = Patt_Custom_Func::fetch_program_office(1);
		print_r($box_program_office);
		echo '<hr/>';

        echo 'Function to obtain shelf from database <br/>';
        $box_shelf = Patt_Custom_Func::fetch_shelf(1);
		print_r($box_shelf);
		echo '<hr/>';

        echo 'Function to obtain bay from database <br/>';
		$box_bay = Patt_Custom_Func::fetch_bay(1);
		print_r($box_bay);
		echo '<hr/>';

        echo 'Function to obtain create month and year from database <br/>';
		$box_date = Patt_Custom_Func::fetch_create_date(1);
		print_r($box_date);
		echo '<hr/>';

        echo 'Function to obtain box count <br/>';
		$box_count = Patt_Custom_Func::fetch_box_count(1);
		print_r($box_count);
		echo '<hr/>';

        echo 'Function to obtain request key <br/>';
		$request_key = Patt_Custom_Func::fetch_request_key(1);
		print_r($request_key);
		echo '<hr/>';

        echo 'Function to obtain request ID <br/>';
		$num = Patt_Custom_Func::fetch_request_id(1);
		print_r($num);
		echo '<hr/>';

        echo 'Function to array of Box IDs <br/>';
		$box_array = Patt_Custom_Func::fetch_box_id_a('dddgd,dg4541,4544rhh');
		print_r($box_array);
		echo '<hr/>';



?>

			<?php

			/* Start the Loop */
			// while ( have_posts() ) :
			// 	the_post();

			// 	get_template_part( 'template-parts/content/content', 'page' );

			// 	// If comments are open or we have at least one comment, load up the comment template.
			// 	if ( comments_open() || get_comments_number() ) {
			// 		comments_template();
			// 	}

			// endwhile; // End of the loop.
			?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_footer();
