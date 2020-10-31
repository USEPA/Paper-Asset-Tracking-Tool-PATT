<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!class_exists('Patt_Custom_Func')) {

    class Patt_Custom_Func
    {

        public $table_prefix;
        /**
         * Get things started
         *
         * @access  public
         * @since   1.0
         */
        public function __construct()
        {            
            global $wpdb; 
            $this->table_prefix = $wpdb->prefix;
        }
        
        //Function to convert LAND ID to User Information JSON
        public static function lan_id_to_json( $lanid )
        {
            global $wpdb;
include_once( WPPATT_ABSPATH . 'includes/api_authorization_strings.php' );
$curl = curl_init();

$url = 'https://wamssoprd.epa.gov/iam/governance/scim/v1/Users?filter=userName%20eq%20'.$lanid;

$lan_id_details = '';

$headers = [
    'Cache-Control: no-cache',
	$eidw_authorization
];

        curl_setopt($curl,CURLOPT_URL, $url);
        curl_setopt($curl,CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl,CURLOPT_MAXREDIRS, 10);
        curl_setopt($curl,CURLOPT_TIMEOUT, 30);
        curl_setopt($curl,CURLOPT_HTTP_VERSION,CURL_HTTP_VERSION_1_1);
        curl_setopt($curl,CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($curl,CURLOPT_HTTPHEADER, $headers);
		//curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
		//curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);

if ($err) {
$lan_id_details = 'Error';
} else {

$json = json_decode($response, true);

$results = $json['totalResults'];
$active = $json['Resources']['0']['active'];
$full_name = $json['Resources']['0']['name']['givenName'].' '.$json['Resources']['0']['name']['familyName'];
$email = $json['Resources']['0']['emails']['0']['value'];
$phone = $json['Resources']['0']['phoneNumbers']['0']['value'];
$org = $json['Resources']['0']['urn:ietf:params:scim:schemas:extension:enterprise:2.0:User']['department'];

if ($results >= 1) {
// Declare array  
$lan_id_details_array = array( 
    "active"=>$active,
    "name"=>$full_name,
    "email"=>$email,
    "phone"=>$phone,
    "org"=>$org,
); 
   
// Use json_encode() function 
$lan_id_details = json_encode($lan_id_details_array); 
   
// Display the output 
//echo($json); 	
} else {
$lan_id_details = 'Error';
}

}

            return $lan_id_details;
        }

        //Function to check LAND ID
        public static function lan_id_check($lan_id, $request_id)
        {
            global $wpdb;
			$lan_id_details = Patt_Custom_Func::lan_id_to_json($lan_id);
			
			$obj = json_decode($lan_id_details);
			
			$active_check = $obj->{'active'};
			
			if ($active_check == 1) {
			$active_check = $lan_id;
			} else {
				
			$find_requester = $wpdb->get_row("SELECT customer_name FROM wpqa_wpsc_ticket
WHERE request_id = '" . $request_id . "'");

			$requester_lanid = $find_requester->customer_name;
	
			$requester_id_details = Patt_Custom_Func::lan_id_to_json($requester_lanid);
			
			$obj = json_decode($requester_id_details);
			
			$requester_id_check = $obj->{'active'};
			
			if ($requester_id_check == 1) {
			$active_check = strtolower($requester_lanid);	
			} else {
			$active_check = 'LAN ID cannot be assigned';
			}
			}
			
            return $active_check;
        }
        
        /**
         * Check Shipping Tracking Number 
         * @return URL
         */
         
        public static function get_tracking_url($tracking_number)		
{
	if (empty($tracking_number)) return false;
	if (!is_string($tracking_number)  &&  !is_int($tracking_number)) return false;

	static $tracking_urls = [
		//UPS - UNITED PARCEL SERVICE
		[
			'url'=>'http://wwwapps.ups.com/WebTracking/processInputRequest?TypeOfInquiryNumber=T&InquiryNumber1=',
			'reg'=>'/\b(1Z ?[0-9A-Z]{3} ?[0-9A-Z]{3} ?[0-9A-Z]{2} ?[0-9A-Z]{4} ?[0-9A-Z]{3} ?[0-9A-Z]|T\d{3} ?\d{4} ?\d{3})\b/i'
		],

		//USPS - UNITED STATES POSTAL SERVICE - FORMAT 1
		[
			'url'=>'https://tools.usps.com/go/TrackConfirmAction?qtc_tLabels1=',
			'reg'=>'/\b((420 ?\d{5} ?)?(91|92|93|94|01|03|04|70|23|13)\d{2} ?\d{4} ?\d{4} ?\d{4} ?\d{4}( ?\d{2,6})?)\b/i'
		],

		//USPS - UNITED STATES POSTAL SERVICE - FORMAT 2
		[
			'url'=>'https://tools.usps.com/go/TrackConfirmAction?qtc_tLabels1=',
			'reg'=>'/\b((M|P[A-Z]?|D[C-Z]|LK|E[A-C]|V[A-Z]|R[A-Z]|CP|CJ|LC|LJ) ?\d{3} ?\d{3} ?\d{3} ?[A-Z]?[A-Z]?)\b/i'
		],

		//USPS - UNITED STATES POSTAL SERVICE - FORMAT 3
		[
			'url'=>'https://tools.usps.com/go/TrackConfirmAction?qtc_tLabels1=',
			'reg'=>'/\b(82 ?\d{3} ?\d{3} ?\d{2})\b/i'
		],

        //FEDEX - FEDERAL EXPRESS
            [
                    'url'=>'http://www.fedex.com/Tracking?language=english&cntry_code=us&tracknumbers=',
                    'reg'=>'/\b(((96\d\d|6\d)\d{3} ?\d{4}|96\d{2}|\d{4}) ?\d{4} ?\d{4}( ?\d{3}| ?\d{15})?)\b/i'
            ],
	];

	//TEST EACH POSSIBLE COMBINATION
	foreach ($tracking_urls as $item) {
		$match = array();
		preg_match($item['reg'], $tracking_number, $match);
		if (count($match)) {
			return $item['url'] . preg_replace('/\s/', '', strtoupper($match[0]));
		} elseif (substr( strtoupper($tracking_number), 0, 4 ) === "DHL:") {
		$dhl_tracking_number = substr($tracking_number, 4);
        return 'http://www.dhl.com/content/g0/en/express/tracking.shtml?brand=DHL&AWB='.$dhl_tracking_number;
		}
	}


	// TRIM LEADING ZEROES AND TRY AGAIN
	if (substr($tracking_number, 0, 1) === '0') {
		return get_tracking_url(ltrim($tracking_number, '0'));
	}


	//NO MATCH FOUND, RETURN FALSE
	return false;
}


        /**
         * Determine Shipping Carrier from Tracking Number 
         * @return URL
         */
         
        public static function get_shipping_carrier($tracking_number)		
{
    
	if (empty($tracking_number)) return false;
	if (!is_string($tracking_number)  &&  !is_int($tracking_number)) return false;

    $shipping_url = Patt_Custom_Func::get_tracking_url($tracking_number);
    
    $shipping_carrier = '';
    
switch (true) {
    case strpos($shipping_url, 'ups') !== false:
        $shipping_carrier = 'ups';
    break;
    case strpos($shipping_url, 'usps') !== false:
        $shipping_carrier = 'usps';
    break;
    case strpos($shipping_url, 'fedex') !== false:
        $shipping_carrier = 'fedex';
    break;
    case strpos($shipping_url, 'dhl') !== false:
        $shipping_carrier = 'dhl';
    break; 

}

    return $shipping_carrier;
    
}


        /**
         * Determine if ID (Request,Box,Folder/File) contains a recall
         * @return Boolean
         */
         
        public static function id_in_recall($identifier,$type)		
{
            global $wpdb;
 
$get_entire_box_recall_data = '';
$recall_id_array = array();

 if ($type == 'request')  {
        $get_recall_data = $wpdb->get_results("
SELECT DISTINCT LEFT(b.box_id, 7) as id
FROM wpqa_wpsc_epa_recallrequest a
INNER JOIN wpqa_wpsc_epa_boxinfo b ON a.box_id = b.id
WHERE a.box_id <> '-99999' AND a.recall_status_id NOT IN ('733','734','878')
UNION
SELECT DISTINCT LEFT(b.folderdocinfo_id, 7) as request_id
FROM wpqa_wpsc_epa_recallrequest a
INNER JOIN wpqa_wpsc_epa_folderdocinfo b on a.folderdoc_id = b.id
WHERE a.folderdoc_id <> '-99999' AND a.recall_status_id NOT IN ('733','734','878')
        ");

        foreach ($get_recall_data as $recall_id_val) {
        $recall_id_vals = $recall_id_val->id;
        array_push($recall_id_array, $recall_id_vals);
        }
        
if (in_array($identifier, $recall_id_array))
  {
  return true;
  }
else
  {
  return false;
  }
} else if($type == 'box') {
        $get_recall_data = $wpdb->get_results("
SELECT DISTINCT b.box_id as id
FROM wpqa_wpsc_epa_recallrequest a
INNER JOIN wpqa_wpsc_epa_boxinfo b ON a.box_id = b.id
WHERE a.box_id <> '-99999' AND a.recall_status_id NOT IN ('733','734','878')
        ");
        foreach ($get_recall_data as $recall_id_val) {
        $recall_id_vals = $recall_id_val->id;
        array_push($recall_id_array, $recall_id_vals);
        }
        
if (in_array($identifier, $recall_id_array))
  {
  return true;
  }
else
  {
  return false;
  }
} else if($type == 'folderfile') {
        $get_entire_box_recall_data = $wpdb->get_results("
        SELECT DISTINCT b.box_id as id
FROM wpqa_wpsc_epa_recallrequest a
INNER JOIN wpqa_wpsc_epa_boxinfo b ON a.box_id = b.id
WHERE a.box_id <> '-99999' AND a.folderdoc_id = '-99999' AND a.recall_status_id NOT IN ('733','734','878')
        ");

// Determine if the entire box has been recalled
if (count($get_entire_box_recall_data)> 0){
        foreach ($get_entire_box_recall_data as $box_recall_id_val) {
        $recall_box_id_vals = $box_recall_id_val->id;

$recall_box_id_array = array();
        
$get_box_contents = $wpdb->get_results("
SELECT a.folderdocinfo_id as folderdocinfo
FROM wpqa_wpsc_epa_folderdocinfo a 
INNER JOIN wpqa_wpsc_epa_boxinfo b ON a.box_id = b.id WHERE b.box_id = '" . $recall_box_id_vals . "'");

foreach ($get_box_contents as $recall_folderdocinfo_id) {
$recall_box_id_content_vals = $recall_folderdocinfo_id->folderdocinfo;

array_push($recall_box_id_array, $recall_box_id_content_vals);
        }

if (in_array($identifier, $recall_box_id_array))
  {
  return true;
  }
}

}

$get_recall_data = $wpdb->get_results("
SELECT DISTINCT b.folderdocinfo_id as id
FROM wpqa_wpsc_epa_recallrequest a
INNER JOIN wpqa_wpsc_epa_folderdocinfo b ON a.folderdoc_id = b.id
WHERE a.folderdoc_id <> '-99999' AND a.recall_status_id NOT IN ('733','734','878')
        ");

        foreach ($get_recall_data as $recall_id_val) {
        $recall_id_vals = $recall_id_val->id;
        array_push($recall_id_array, $recall_id_vals);
        }
        
if (in_array($identifier, $recall_id_array))
  {
  return true;
  }
else
  {
  return false;
  }
} else {
return false;
}

}


        /**
         * Determine if ID (Request,Box,Folder/File) contains a decline
         * @return Boolean
         */
         
        public static function id_in_return($identifier,$type)		
{
            global $wpdb;
$return_id_array = array();
        
 if ($type == 'request')  {
        $get_return_data = $wpdb->get_results("
SELECT DISTINCT LEFT(b.box_id, 7) as id
FROM wpqa_wpsc_epa_return_items a
INNER JOIN wpqa_wpsc_epa_boxinfo b ON a.box_id = b.id
WHERE a.box_id <> '-99999'
        ");

        foreach ($get_return_data as $return_id_val) {
        $return_id_vals = $return_id_val->id;
        array_push($return_id_array, $return_id_vals);
        }
        
if (in_array($identifier, $return_id_array))
  {
  return true;
  }
else
  {
  return false;
  }
  
} else if($type == 'box') {
        $get_return_data = $wpdb->get_results("
SELECT DISTINCT b.box_id as id
FROM wpqa_wpsc_epa_return_items a
INNER JOIN wpqa_wpsc_epa_boxinfo b ON a.box_id = b.id
WHERE a.box_id <> '-99999'
        ");
        foreach ($get_return_data as $return_id_val) {
        $return_id_vals = $return_id_val->id;
        array_push($return_id_array, $return_id_vals);
        }
        
if (in_array($identifier, $return_id_array))
  {
  return true;
  }
else
  {
  return false;
  }    
} else if($type == 'folderfile') {
        $get_entire_box_return_data = $wpdb->get_results("
        SELECT DISTINCT b.box_id as id
FROM wpqa_wpsc_epa_return_items a
INNER JOIN wpqa_wpsc_epa_boxinfo b ON a.box_id = b.id
WHERE a.box_id <> '-99999'
        ");

// Determine if the entire box has been recalled
if (count($get_entire_box_return_data)> 0){
        foreach ($get_entire_box_return_data as $box_return_id_val) {
        $return_box_id_vals = $box_return_id_val->id;

$return_box_id_array = array();
        
$get_box_contents = $wpdb->get_results("
SELECT a.folderdocinfo_id as folderdocinfo
FROM wpqa_wpsc_epa_folderdocinfo a 
INNER JOIN wpqa_wpsc_epa_boxinfo b ON a.box_id = b.id WHERE b.box_id = '" . $return_box_id_vals . "'");

foreach ($get_box_contents as $return_folderdocinfo_id) {
$return_box_id_content_vals = $return_folderdocinfo_id->folderdocinfo;

array_push($return_box_id_array, $return_box_id_content_vals);
        }

if (in_array($identifier, $return_box_id_array))
  {
  return true;
  }
}

}

} else {
  return false;
}
    
}


        
        /**
         * Update user status data by status & box ID
         * @return Id
         */
        public static function update_status_by_id( $data ){
            global $wpdb;
            $status_id = array_keys($data['status'])[0];// array_key_first($data['status']);
            if(!isset($status_id) || !isset($data['box_id'])) {
                return false;
            }

            $args = [
                'select' => 'id',
                'where' => [
                    ['box_id', $data['box_id']],
                    ['status_id', $status_id]
                ]
            ];

            $wpsc_epa_boxinfo_userstatus = new WP_CUST_QUERY("{$wpdb->prefix}wpsc_epa_boxinfo_userstatus");
            $wpsc_epa_boxinfo_userstatus_data = $wpsc_epa_boxinfo_userstatus->get_results($args);
            
            if(count($wpsc_epa_boxinfo_userstatus_data) > 0){
                foreach($wpsc_epa_boxinfo_userstatus_data as $row_id){
                     $wpsc_epa_boxinfo_userstatus->delete($row_id->id);
                }
            }

            self::user_status_insert( $data );
            return true;
        }

        /**
         * Get all user status data
         */
        public static function get_user_status_data($where){
            global $wpdb;
            if(is_array($where) && count($where) > 0){
                foreach($where as $key => $whr) {
                    $args['where'][] = ["{$wpdb->prefix}wpsc_epa_boxinfo_userstatus.$key", "'{$whr}'"];
                }
            }
            $wpsc_epa_boxinfo_userstatus = new WP_CUST_QUERY("{$wpdb->prefix}wpsc_epa_boxinfo_userstatus");
            $wpsc_epa_boxinfo_userstatus_records = $wpsc_epa_boxinfo_userstatus->get_results($args);
            // print_r($wpsc_epa_boxinfo_userstatus_records);
            $box_id = null;
            $status_id = null;
            $sorted_data = [];
            $counter = 1;
            $get_all_status = self::get_all_status();
            
            foreach($wpsc_epa_boxinfo_userstatus_records as $record){ 
                
                if(!isset($sorted_data[$record->box_id]['all_status'])) {
                    $sorted_data[$record->box_id]['all_status'] = $get_all_status;
                }
                
                if($record->box_id == $box_id) {
                    // echo '<br/>===B====' . $record->box_id;
                    if($record->status_id <> null){
                        // echo '<br/>===C====' . $record->box_id;
                        unset($sorted_data[$record->box_id]['all_status'][$record->status_id]);
                        $sorted_data[$record->box_id]['status'][$record->status_id][] = $record->user_id;
                        $sorted_data[$record->box_id]['other_status'] = $sorted_data[$record->box_id]['all_status'];
                    }
                } else {
                    // echo '<br/>===D====' . $record->box_id;
                    if($record->status_id <> null){
                        $sorted_data[$record->box_id]['box_id'] = $record->box_id;
                        unset($sorted_data[$record->box_id]['all_status'][$record->status_id]);
                        $sorted_data[$record->box_id]['status'][$record->status_id][] = $record->user_id;
                        $sorted_data[$record->box_id]['other_status'] = $sorted_data[$record->box_id]['all_status'];
                        $box_id = $record->box_id;
                        // print_r($sorted_data[$record->box_id]['other_status']);
                        // die($record->status_id);
                    }
                }
            }


                // die(print_r($sorted_data));
            // Add the un assigned statues to the box_id
            foreach($sorted_data as $box_id_key => $fresh_data){ 
                // print_r($sorted_data);
                // echo '==!'.$box_id_key.'!==';
                // print_r($fresh_data);
                // die($box_id_key);
                if(is_array($fresh_data['other_status']) && count($fresh_data['other_status']) > 0){
                    
                    foreach($fresh_data['other_status'] as $status_id => $other_status){
                      $sorted_data[$box_id_key]['status'][$status_id] = 'N/A'; 
                    }
                    unset($sorted_data[$box_id_key]['other_status']);
                    unset($sorted_data[$box_id_key]['all_status']);
                }
            }
            
			$sorted_data = reset($sorted_data);
            return $sorted_data;
        }

        /**
         * Insert to Userstatus
         */
        public static function user_status_insert( $data ) { 

            // die(print_r($get_all_status));
/*
            echo 'This is the data: ';
            die(print_r($data)); 
*/           
            if( is_array($data['status']) && count($data['status']) > 0 ) {
                foreach ($data['status'] as $status_id => $users) {
                    if( is_array($users) && count($users) > 0 ) {                
                        foreach($users as $user){
                            $inser_data = [
                                'box_id' => $data['box_id'],
                                'user_id' => $user,
                                'status_id' => $status_id
                            ];
                            $status_table_insert_id = self::insert_status_table($inser_data);
                            // unset($get_all_status[$status_id]);
                        }
                        return $status_table_insert_id; 
                        // die();
                    } elseif( isset($user))  {
                        $inser_data = [
                            'box_id' => $data['box_id'],
                            'user_id' => $user,
                            'status_id' => $status_id
                        ];
                        $status_table_insert_id = self::insert_status_table($inser_data);
                        // unset($get_all_status[$status_id]);
                        return $status_table_insert_id;
                    } else {
	                    //do nothing
                    }
                }
            }
        }

        /**
         * Insert Userstatus table
         */
        public static function insert_status_table($data) {
            global $wpdb;
            $insert_status_table = new WP_CUST_QUERY("{$wpdb->prefix}wpsc_epa_boxinfo_userstatus");
            return $insert_status_table_id = $insert_status_table->insert($data);
        }

		// Gets all Box Statuses
        public function get_all_status( $ignore_box_status = [] ) {
/*
            $status = [
                748 => 'Pending',
                621 => 'Not Assigned',
                64  => 'Assigned',
                672 => 'Scanning Preparation',
                671 => 'Scanning/Digitization',
                65  => 'QA/QC',
                6   => 'Digitized/Not Validated',
                673 => 'Ingestion',
                674 => 'Validation',
                743 => 'Re-Scan',
                66  => 'Completed',
                68  => 'Destruction Approval',
                67  => 'Dispositioned'
            ];
            return $status;
*/
            
            
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
			// $ignore_box_status = ['Pending', 'Ingestion', 'Completed', 'Dispositioned'];
			//$ignore_box_status = [];
			
			$term_id_array = array();
			$term_and_name_array = array();
			foreach( $box_statuses as $key=>$box ) {
				if( in_array( $box->name, $ignore_box_status ) ) {
					unset($box_statuses[$key]);
				} else {
					$term_id_array[] = $box->term_id;
					$term_and_name_array[$box->term_id] = $box->name;
				}
			}
			array_values($box_statuses);
			
			return $term_and_name_array;
        }
        
        // Gets all statuses from supplied taxonomy
        public function get_all_status_from_tax( $tax ) {
			
			// Examples: 'wpsc_box_statuses', ''
			
            $tax = isset($tax) ? sanitize_text_field($tax) : '';
            $tax_prefix = $tax;
            if( $tax == 'wpsc_box_statuses') {
	            $tax_prefix = 'wpsc_box_status';
            } elseif ( $tax == 'wppatt_return_statuses') {
	            $tax_prefix = 'wppatt_return_status';
	        }
            
            // Ensure Taxonomy has been registered
			if( !taxonomy_exists($tax) ) {
				$args = array(
					'public' => false,
					'rewrite' => false
				);
				register_taxonomy( $tax, 'wpsc_ticket', $args );
			}
			
			// Get List of Statuses
			$statuses = get_terms([
				'taxonomy'   => $tax,
				'hide_empty' => false,
				'orderby'    => 'meta_value_num',
				'order'    	 => 'ASC',
				'meta_query' => array('order_clause' => array('key' => $tax_prefix.'_load_order')),
			]);
			

			$term_and_name_array = array();
			foreach( $statuses as $key=>$box ) {
					$term_and_name_array[$box->term_id] = $box->name;
				
			}
			array_values($term_and_name_array);
			
			return $term_and_name_array;
        }
        
        
         /**
         * Update return user data by return id
         * @return Id
         */
        public static function update_return_user_by_id( $data ){

            if(!isset($data['return_id']) || !isset($data['user_id'])) {
                return false;
            }

            global $wpdb;

            $args = [
                'select' => 'id',
                'where' => ['return_id', $data['return_id']]
            ];

            $wpsc_return_users = new WP_CUST_QUERY("{$wpdb->prefix}wpsc_epa_return_users");
            $wpsc_return_user_data = $wpsc_return_users->get_results($args);
            if(count($wpsc_return_user_data) > 0){
                foreach($wpsc_return_user_data as $row_id){
                     $wpsc_return_users->delete($row_id->id);
                }
            }

            if(is_array($data['user_id']) && count($data['user_id']) > 0){
                foreach($data['user_id'] as $user_id){
                    $data_req = [
                        'return_id' => $data['return_id'],
                        'user_id'   => $user_id
                    ];
                   $insert_id = $wpsc_return_users->insert($data_req); 
                }
            } else {
                $data_req = [
                    'return_id' => $data['return_id'],
                    'user_id'   => $data['user_id']
                ];
               $insert_id =  $wpsc_return_users->insert($data_req); 
            }
            return true;
        }

        /**
         * Get return data
         * @return Id
         */
        public static function get_return_data( $where ){            
            global $wpdb;   

            if(is_array($where) && count($where) > 0){
                foreach($where as $key => $whr) {
                    if($key == 'custom') {
                       $args['where']['custom'] = $whr;
                    } elseif($key == 'filter') {                        
                        $orderby = isset($whr['orderby']) ? $whr['orderby'] : 'id';
                        $order = isset($whr['order']) ? $whr['order'] : 'DESC';
                        if($orderby == 'status') {
                            $orderby = "{$wpdb->prefix}terms.name";
                        }
                        $args['order'] = [$orderby, $order];
                        if(isset($whr['records_per_page']) && $whr['records_per_page'] > 0){  
                            $number_of_records =  isset($whr['records_per_page']) ? $whr['records_per_page'] : 20;
                            $start = isset($whr['paged']) ? $whr['paged'] : 0;
                            $args['limit'] = [$start, $number_of_records];        
                        }
                    } elseif($key == 'program_office_id') {
                            $args['where'][] = [
                                "{$wpdb->prefix}wpsc_epa_program_office.office_acronym", 
                                $whr
                            ];
                        // }
                    } elseif($key == 'digitization_center') {
                        $storage_location_id = self::get_storage_location_id_by_dc($whr);
                        if(is_array($storage_location_id) && count($storage_location_id) > 0) {
                            foreach($storage_location_id as $val){
                                if($val->id) {
                                    $dc_ids[] = $val->id;
                                }
                            }
                            $dc_ids = implode(', ', $dc_ids);
                            $args['where'][] = [
                                "{$wpdb->prefix}wpsc_epa_boxinfo.storage_location_id", 
                                "($dc_ids)",
                                "AND",
                                ' IN '
                            ];
                        }
                    } elseif($key == 'id' && is_array($whr) && count($whr) > 0) {
                        $args['where'][] = [
//                             "{$wpdb->prefix}wpsc_epa_returnrequest.id",   // table does not exist. Haven't seen error 9-22-2020
                           "{$wpdb->prefix}wpsc_epa_return.id",   // correct table? id = 1, 2, 3. return_id = 0000001, 0000002, 0000003                            
                            "(".implode(',', $whr).")",
                            "AND",
                            ' IN '
                        ];
                    } elseif($key == 'return_id' && is_array($whr) && count($whr) > 0) {
                        $args['where'][] = [
                            "{$wpdb->prefix}wpsc_epa_return.return_id", 
                            '("' . implode('", "', $whr) . '")',
                            "AND",
                            ' IN '
                        ];
                    } else {
                        $args['where'][] = ["{$wpdb->prefix}wpsc_epa_return.$key", "'{$whr}'"];
                    }
                }   
            }

            $args['where'][] = [
                                "{$wpdb->prefix}wpsc_epa_return.id", 
                                "0",
                                "AND",
                                ' > '
                            ];

            $select_fields = [
                "{$wpdb->prefix}wpsc_epa_return" => ['id', 'return_id', 'return_date', 'return_receipt_date', 'comments', 'return_status_id', 'updated_date'],
                // "{$wpdb->prefix}wpsc_epa_boxinfo" => ['ticket_id', 'box_id', 'storage_location_id', 'location_status_id', 'box_destroyed', 'date_created', 'date_updated'],
//                "{$wpdb->prefix}wpsc_epa_folderdocinfo" => ['title', 'folderdocinfo_id as folderdoc_id'],
                "{$wpdb->prefix}wpsc_epa_return_items" => ['box_id', 'folderdoc_id'],
                "{$wpdb->prefix}wpsc_epa_shipping_tracking" => ['company_name as shipping_carrier', 'tracking_number', 'status', 'shipped', 'delivered'],
                "{$wpdb->prefix}terms" => ['name as reason'],
                "{$wpdb->prefix}wpsc_epa_return_users" => ['user_id'],
            ];

            foreach($select_fields as $key => $fields_array){
                foreach($fields_array as $field) {
                    if($key == "{$wpdb->prefix}wpsc_epa_return_users"){
                        $select[] = "GROUP_CONCAT($key.user_id) as $field";
                    } if($key == "{$wpdb->prefix}wpsc_epa_return_items"){
                        $select[] = "GROUP_CONCAT($key.$field) as $field";
                    } else {
                        $select[] = $key . '.' . $field;
                    }
                }
            }

            $args['groupby']  = "{$wpdb->prefix}wpsc_epa_return.return_id";
            $args['select']  = implode(', ', $select);
            $args['join']  = [

                        [
                            'type' => 'LEFT JOIN', 
                            'table' => "{$wpdb->prefix}terms", 
                            'key'  => 'term_id',
                            'compare' => '=',
                            'foreign_key' => 'return_reason_id'
                        ],
                        [
                            'type' => 'Inner JOIN', 
                            'table' => "{$wpdb->prefix}wpsc_epa_return_items", 
                            'key' => 'return_id',
                            'foreign_key'  => 'id',
                            'compare' => '=',
                        ],
                        // [
                        //     'type' => 'LEFT JOIN', 
                        //     'table' => "{$wpdb->prefix}wpsc_epa_boxinfo", 
                        //     'foreign_key'  => 'box_id',
                        //     'compare' => '=',
                        //     'key' => 'return_id',
                        //     'base_table' => "{$wpdb->prefix}wpsc_epa_return_items"
                        // ],
                        [
                            'type' => 'LEFT JOIN', 
                            'table' => "{$wpdb->prefix}wpsc_epa_return_users", 
                            'foreign_key'  => 'id',
                            'compare' => '=',
                            'key' => 'return_id'
                        ],
/*
                         [
                             'type' => 'LEFT JOIN', 
                             'table' => "{$wpdb->prefix}wpsc_epa_folderdocinfo", 
                             'key'  => 'id',
                             'compare' => '=',
                             'foreign_key' => 'folderdoc_id'
                         ],
*/
                        [
                            'type' => 'LEFT JOIN', 
                            'table' => "{$wpdb->prefix}wpsc_epa_shipping_tracking", 
                            'key'  => 'id',
                            'compare' => '=',
                            'foreign_key' => 'shipping_tracking_id'
                        ]
                        ];

            $wpsc_epa_box = new WP_CUST_QUERY("{$wpdb->prefix}wpsc_epa_return");
            $box_details = $wpsc_epa_box->get_results($args);

            if(count($box_details) > 0 ){
                foreach($box_details as $key => $record){
                    if(!empty($record->user_id)) {
                        $record->user_id = explode(',', $record->user_id );
                    }
                    if(!empty($record->box_id)) {		
                        //$record->box_id = explode(',', $record->box_id );		
                        		
                        $temp_box_id = explode(',', $record->box_id );		
                        $the_array = [];		
                        foreach( $temp_box_id as $id ) {		
	                    	$the_array[] = self::get_box_id_by_id($id)[0];		
                        }		
                        $record->box_id = $the_array;		
                        		
                    }		
                    if(!empty($record->folderdoc_id)) {		
                        //$record->folderdoc_id = explode(',', $record->folderdoc_id );		
                        		
                        $temp_folderdoc_id = explode(',', $record->folderdoc_id );		
                        $the_array = [];		
                        foreach( $temp_folderdoc_id as $id ) {		
	                    	$the_array[] = self::get_folderdoc_id_by_id($id)[0];		
                        }		
                        $record->folderdoc_id = $the_array;		
                    }
                }
            }
            
            return $box_details;
        }
        
        //Function to get first name, last name and username from the profile display name
        public static function get_full_name_by_customer_name($customer_name) {
            global $wpdb;
            
            $get_customer_id = $wpdb->get_row("SELECT a.ID as user_id FROM wpqa_users as a WHERE a.display_name = '" . $customer_name . "'");
		    $customer_id = $get_customer_id->user_id;
		    $get_first_name = get_user_meta( $customer_id, 'first_name', true );
		    $get_last_name = get_user_meta( $customer_id, 'last_name', true );
		    $get_user_login = get_user_meta( $customer_id, 'nickname', true );
		    
		    if($get_first_name != '' && $get_last_name != '') {
		        $full_display_name = $get_first_name . ' ' . $get_last_name . ' (' . $get_user_login . ')';
		    }
		    else {
		        $full_display_name  = $get_user_login;
		    }
	        return $full_display_name;
        }
        
        //Function to obtain box ID from database based on ID		
        public static function get_box_id_by_id($id)		
        {		
            global $wpdb; 		
            // die(print_r($wpdb->prefix));		
            $array = array();		
            $args = [		
                'where' => ['id', $id],		
            ];		
            $wpqa_wpsc_epa_boxinfo = new WP_CUST_QUERY("{$wpdb->prefix}wpsc_epa_boxinfo");		
            $box_result = $wpqa_wpsc_epa_boxinfo->get_results($args, false);		
            foreach ($box_result as $box) {		
	            if( $box->box_id != null ) {		
	                array_push($array, $box->box_id);		
	            }		
            }		
            return $array;		
        }
        
        //Function to obtain  ID from database based on Box ID		
        public static function get_id_by_box_id($box_id)		
        {		
            global $wpdb; 		
            // die(print_r($wpdb->prefix));		
            $array = array();
            $box_id = '"'.$box_id.'"';
            $args = [		
                'where' => ['box_id', $box_id],		
            ];		
            $wpqa_wpsc_epa_boxinfo = new WP_CUST_QUERY("{$wpdb->prefix}wpsc_epa_boxinfo");		
            $box_result = $wpqa_wpsc_epa_boxinfo->get_results($args, false);		
            
            foreach ($box_result as $box) {	
	            //die(print_r($box));	
	            if( $box->box_id != null ) {		
	                array_push($array, $box->id);		
	            }		
            }		
            return $array[0];		
        }
        		
        		
        //Function to obtain folderdoc ID from database based on ID		
        public static function get_folderdoc_id_by_id($id)		
        {		
            global $wpdb; 		
            // die(print_r($wpdb->prefix));		
            $array = array();		
            $args = [		
                'where' => ['id', $id],		
            ];		
            $wpqa_wpsc_epa_folderdocinfo = new WP_CUST_QUERY("{$wpdb->prefix}wpsc_epa_folderdocinfo"); 		
            $folderdocinfo_result = $wpqa_wpsc_epa_folderdocinfo->get_results($args, false);		
            foreach ($folderdocinfo_result as $folderdocinfo) {		
	            if( $folderdocinfo->folderdocinfo_id != null ) {		
	                array_push($array, $folderdocinfo->folderdocinfo_id);		
	            }		
            }		
            return $array;		
        }	
        
        //Function to obtain ID from database based on folderdoc ID
        public static function get_id_by_folderdoc_id($folderdoc_id)		
        {		
            global $wpdb; 		
            // die(print_r($wpdb->prefix));		
            $array = array();	
            $folderdoc_id = '"'.$folderdoc_id.'"';	
            $args = [
                'where' => ['folderdocinfo_id', $folderdoc_id],		
            ];		
            $wpqa_wpsc_epa_folderdocinfo = new WP_CUST_QUERY("{$wpdb->prefix}wpsc_epa_folderdocinfo"); 		
            $folderdocinfo_result = $wpqa_wpsc_epa_folderdocinfo->get_results($args, false);		
            foreach ($folderdocinfo_result as $folderdocinfo) {		
	            if( $folderdocinfo->folderdocinfo_id != null ) {		
	                array_push($array, $folderdocinfo->id);
	            }		
            }		
            return $array[0];		
        }	

        
        //Function to obtain the ID of the Program Office from the office acryonm		
        public static function get_program_offic_id_by_acronym($acronym)		
        {		
            global $wpdb; 		
            // die(print_r($acronym));		
            $array = array();
            $acronym = '"'.$acronym.'"';		
            $args = [		
                'where' => ['office_acronym', $acronym],		
            ];		
            $wpqa_wpsc_epa_po = new WP_CUST_QUERY("{$wpdb->prefix}wpsc_epa_program_office");		
            $po_result = $wpqa_wpsc_epa_po->get_results($args, false);		
            //die(print_r($po_result));		
            foreach ($po_result as $po) {		
	            if( $po->id != null ) {		
	                array_push($array, $po->id);		
	            }		
            }		
            return $array[0];		
        }	

        /**
         * Insert return data
         * @return Id
         */
        public static function insert_return_data( $data ){            
            global $wpdb;   
           
            $user_id = $data['user_id'];
            unset($data['user_id']);

            $folderdoc_id = isset($data['folderdoc_id']) ? $data['folderdoc_id'] : -99999;;
            unset($data['folderdoc_id']);
            
            $box_id = isset($data['box_id']) ? $data['box_id'] : -99999;
            unset($data['box_id']);
            
            // Store tracking info
             $shipping_tracking_info = $data['shipping_tracking_info'];
             unset($data['shipping_tracking_info']);
            
            // New Test
/*
            $shipping_tracking_number = isset($data['shipping_tracking_id']) ? $data['shipping_tracking_id'] : '';  
            unset($data['shipping_tracking_id']);   
                
            $shipping_carrier = isset($data['shipping_carrier']) ? $data['shipping_carrier'] : '';  
            unset($data['shipping_carrier']);   
*/
            // New Test END

            // Updated At
            $data['updated_date'] = date("Y-m-d H:i:s");
            
            // DEFAULT ID
            $data['return_id'] = '000000';
            $data['shipping_tracking_id'] = -99999;
            
            // Default Return Status
            $data['return_status_id'] = 752; // wp_terms 752 = Return Initiated.

            //print_r($data); die();
            if ( $box_id == -99999 && $folderdoc_id == -99999 ) {
                die('Cannot insert a value without a Box and Folder/File ID - ERR-001');
            } elseif ( ( is_array($box_id) && count($box_id) == 0 ) && $folderdoc_id == -99999 ) {
                die('Cannot insert a value without a Box and Folder/File ID - ERR-002');
            } elseif ( $box_id == -99999 && ( is_array($folderdoc_id) && count($folderdoc_id) == 0) ) {
                die('Cannot insert a value without a Box and Folder/File ID - ERR-003');
            } elseif ( ( is_array($box_id) && count($box_id) == 0 ) && ( is_array($folderdoc_id) && count($folderdoc_id) == 0) ) {
                die('Cannot insert a value without a Box and Folder/File ID - ERR-004');
            }
            
            $wpsc_return_method = new WP_CUST_QUERY("{$wpdb->prefix}wpsc_epa_return");  
            $return_insert_id = $wpsc_return_method->insert($data); 
                
            // Add row to Shipping Table    
            // $shipping_tracking_number = isset($data['shipping_tracking_id']) ? $data['shipping_tracking_id'] : '';   
            // unset($data['shipping_tracking_id']);    
                
            // $shipping_carrier = isset($data['shipping_carrier']) ? $data['shipping_carrier'] : '';   
            // unset($data['shipping_carrier']);
            $shipping_return_insert_id = -99999;
            $current_date = date("yy-m-d");
            if(isset($return_insert_id) && isset($shipping_tracking_info)) {
                $shipping_data = [  
                    'ticket_id' => -99999,  
                    'company_name' => $shipping_tracking_info['company_name'],  
                    'tracking_number' => $shipping_tracking_info['tracking_number'],
                    //  'tracking_number' => 4, 
                    'status' => '', 
                    'shipped' => 0, 
                    'delivered' => 0,                   
                    'recallrequest_id' => -99999,   
                    'return_id' => $return_insert_id,
                    'date_added' => $current_date
                ];  
                $wpsc_shipping_method = new WP_CUST_QUERY("{$wpdb->prefix}wpsc_epa_shipping_tracking"); 
                $shipping_return_insert_id = $wpsc_shipping_method->insert($shipping_data); 
            }
            // Update Shipping Tracking ID in Return Table  
            $update_shipping_id['shipping_tracking_id'] = $shipping_return_insert_id;   
            $shipping_recall_updated = $wpsc_return_method->update($update_shipping_id, ['id' => $return_insert_id]);   
                
            // Update the return ID with insert ID  
            $num = $return_insert_id;   
            $str_length = 7;    
            $update_data['return_id'] = substr("000000{$num}", -$str_length);   
            $return_updated = $wpsc_return_method->update($update_data, ['id' => $return_insert_id]);               

            // Add data to return_users 
            $wpsc_epa_box_user = new WP_CUST_QUERY("{$wpdb->prefix}wpsc_epa_return_users");        
            if(is_array($user_id) && count($user_id) > 0){
                foreach($user_id as $user){
                    $user_data = [
                        'user_id' => $user,
                        'return_id' => $return_insert_id
                    ];
                    $box_details = $wpsc_epa_box_user->insert($user_data);
                }
            } else {
                $user_data = [
                    'user_id' => $user_id,
                    'return_id' => $return_insert_id
                ];
                $box_details = $wpsc_epa_box_user->insert($user_data);
            }

            // Add data to return items table
            $wpsc_epa_return_items = new WP_CUST_QUERY("{$wpdb->prefix}wpsc_epa_return_items");
            // if($return_insert_id){
            //     $item_data = [
            //         'box_id' => $box_id,
            //         'folderdoc_id' => $folderdoc_id,
            //         'return_id' => $return_insert_id
            //     ];
            //     // print_r($item_data);
            //     $wpsc_epa_rec = $wpsc_epa_return_items->insert($item_data);
            // }

            if(is_array($box_id) && count($box_id) > 0){
                foreach($box_id as $box){
                    $item_data = [
                        'box_id' => $box,
                        'folderdoc_id' => -99999,
                        'return_id' => $return_insert_id
                    ];
                    $wpsc_epa_rec = $wpsc_epa_return_items->insert($item_data);
                }
            } else {
                $item_data = [
                    'box_id' => $box_id,
                    'folderdoc_id' => -99999,
                    'return_id' => $return_insert_id
                ];
                if( $box_id != -99999 ) {
                    $wpsc_epa_rec = $wpsc_epa_return_items->insert($item_data);
                }
            }


            if (is_array($folderdoc_id) && count($folderdoc_id) > 0) {
                foreach ($folderdoc_id as $folderdoc) {
                    $item_data = [
                        'box_id' => -99999,
                        'folderdoc_id' => $folderdoc,
                        'return_id' => $return_insert_id,
                    ];
                    $wpsc_epa_rec = $wpsc_epa_return_items->insert($item_data);
                }
            } else {
                $item_data = [
                    'box_id' => -99999,
                    'folderdoc_id' => $folderdoc_id,
                    'return_id' => $return_insert_id,
                ];
                if( $folderdoc_id != -99999 ) {
                    $wpsc_epa_rec = $wpsc_epa_return_items->insert($item_data);
                }
                
            }


            return $return_insert_id;
        }

        /**
         * Get Box details by BOX/FOLDER ID !!
         * @return Id, Title, Record Schedule, Programe Office !!
         */
        public static function get_box_file_details_by_id( $search_id ){            
            global $wpdb; 
            $box_details = [];
            $args = [
                'select' => "box_id, {$wpdb->prefix}wpsc_epa_boxinfo.id as Box_id_FK, program_office_id as box_prog_office_code, box_destroyed, box_status,
                {$wpdb->prefix}wpsc_epa_program_office.id as Program_Office_id_FK, 
                {$wpdb->prefix}wpsc_epa_program_office.office_acronym,
                {$wpdb->prefix}wpsc_epa_program_office.office_name,
                {$wpdb->prefix}epa_record_schedule.id as Record_Schedule_id_FK,
                {$wpdb->prefix}epa_record_schedule.Record_Schedule_Number,
                {$wpdb->prefix}epa_record_schedule.Schedule_Title",
                'join'   => [
                    [
                        'type' => 'INNER JOIN', 
                        'table' => "{$wpdb->prefix}wpsc_epa_program_office", 
                        'foreign_key'  => 'program_office_id',
                        'compare' => '=',
                        'key' => 'office_code'
                    ],
                    [
                        'type' => 'INNER JOIN', 
                        'table' => "{$wpdb->prefix}epa_record_schedule", 
                        'foreign_key'  => 'record_schedule_id',
                        'compare' => '=',
                        'key' => 'id'
                    ]
                ],
                'where' => ['box_id', "'{$search_id}'"],
            ];
            $wpsc_epa_box = new WP_CUST_QUERY("{$wpdb->prefix}wpsc_epa_boxinfo");
            $box_details = $wpsc_epa_box->get_row($args, false);            
            
            // If result set is empty, search for file/folder
            if(!is_object($box_details) && count($box_details) < 1){
                $args = [
                    'select' => "{$wpdb->prefix}wpsc_epa_folderdocinfo.box_id as Box_id_FK, 
                    {$wpdb->prefix}wpsc_epa_folderdocinfo.id as Folderdoc_Info_id_FK,
                    {$wpdb->prefix}wpsc_epa_folderdocinfo.folderdocinfo_id as Folderdoc_Info_id,
                    {$wpdb->prefix}wpsc_epa_folderdocinfo.freeze,
                    {$wpdb->prefix}wpsc_epa_folderdocinfo.unauthorized_destruction,  
                    {$wpdb->prefix}wpsc_epa_boxinfo.program_office_id,  
                    index_level,
                    title, 
                    box_destroyed,
                    box_status,
                    {$wpdb->prefix}wpsc_epa_boxinfo.box_id,
                    {$wpdb->prefix}wpsc_epa_program_office.id as Program_Office_id_FK, 
                    {$wpdb->prefix}wpsc_epa_program_office.office_acronym,
                    {$wpdb->prefix}wpsc_epa_program_office.office_name,
                    {$wpdb->prefix}epa_record_schedule.id as Record_Schedule_id_FK,
                    {$wpdb->prefix}epa_record_schedule.Record_Schedule_Number,
                    {$wpdb->prefix}epa_record_schedule.Schedule_Title,
                    {$wpdb->prefix}wpsc_epa_boxinfo.record_schedule_id",
                    'join'   => [
                        [
                            'type' => 'INNER JOIN', 
                            'table' => "{$wpdb->prefix}wpsc_epa_boxinfo", 
                            'foreign_key'  => 'box_id',
                            'compare' => '=',
                            'key' => 'id'
                        ],
                        [
                            'base_table' => "{$wpdb->prefix}wpsc_epa_boxinfo",
                            'type' => 'INNER JOIN', 
                            'table' => "{$wpdb->prefix}wpsc_epa_program_office", 
                            'foreign_key'  => 'program_office_id',
                            'compare' => '=',
                            'key' => 'office_code'
                        ],
                        [
                            'base_table' => "{$wpdb->prefix}wpsc_epa_boxinfo",
                            'type' => 'INNER JOIN', 
                            'table' => "{$wpdb->prefix}epa_record_schedule", 
                            'foreign_key'  => 'record_schedule_id',
                            'compare' => '=',
                            'key' => 'id'
                        ]
                    ],
                    'where' => ['folderdocinfo_id', "'{$search_id}'"],
                ];
                $wpsc_epa_box = new WP_CUST_QUERY("{$wpdb->prefix}wpsc_epa_folderdocinfo");
                $box_details = $wpsc_epa_box->get_row($args, false);
                if($box_details) {
                    $box_details->type = 'Folder/Doc';
                }
                // $box_details->type = ($box_details->index_level == '02') ? 'File' : 'Folder';
            } else {
                if($box_details) {
                    $box_details->type = 'Box';
                }
                $box_details->title = '';
            }
            return $box_details;
        }


        /**
         * Update recall user data by recall id
         * @return Id
         */
        public static function update_recall_user_by_id( $data ){

            if(!isset($data['recall_id']) || !isset($data['user_id'])) {
                return false;
            }

            global $wpdb;

            $args = [
                'select' => 'id',
                'where' => ['recallrequest_id', $data['recall_id']]
            ];

            $wpsc_recall_users = new WP_CUST_QUERY("{$wpdb->prefix}wpsc_epa_recallrequest_users");
            $wpsc_recall_user_data = $wpsc_recall_users->get_results($args);
            if(count($wpsc_recall_user_data) > 0){
                foreach($wpsc_recall_user_data as $row_id){
                     $wpsc_recall_users->delete($row_id->id);
                }
            }

            if(is_array($data['user_id']) && count($data['user_id']) > 0){
                foreach($data['user_id'] as $user_id){
                    $data_req = [
                        'recallrequest_id' => $data['recall_id'],
                        'user_id'   => $user_id
                    ];
                   $insert_id = $wpsc_recall_users->insert($data_req); 
                }
            } else {
                $data_req = [
                    'recallrequest_id' => $data['recall_id'],
                    'user_id'   => $data['user_id']
                ];
               $insert_id =  $wpsc_recall_users->insert($data_req); 
            }
            return true;
        }

        /**
         * Insert recall data
         * @return Id
         */
        public static function insert_recall_data( $data ){            
            global $wpdb;   
            $user_id = $data['user_id'];
            unset($data['user_id']);

            // DEFAULT ID
            $data['recall_id'] = '000000';

            $wpsc_recall_method = new WP_CUST_QUERY("{$wpdb->prefix}wpsc_epa_recallrequest");
            $recall_insert_id = $wpsc_recall_method->insert($data);

            // Update the recall ID with insert ID
            $num = $recall_insert_id;
            $str_length = 7;
            $update_data['recall_id'] = substr("000000{$num}", -$str_length);
            $recall_updated = $wpsc_recall_method->update($update_data, ['id' => $recall_insert_id]);

            // Add data to recall_users 
            $wpsc_epa_box_user = new WP_CUST_QUERY("{$wpdb->prefix}wpsc_epa_recallrequest_users");        
            if(is_array($user_id) && count($user_id) > 0){
                foreach($user_id as $user){
                    $user_data = [
                        'user_id' => $user,
                        'recallrequest_id' => $recall_insert_id
                    ];
                    $box_details = $wpsc_epa_box_user->insert($user_data);
                }
            } else {
                $user_data = [
                    'user_id' => $user_id,
                    'recallrequest_id' => $recall_insert_id
                ];
                $box_details = $wpsc_epa_box_user->insert($user_data);
            }
            
            // Add row to Shipping Table 
            $current_date = date("yy-m-d");
            $shipping_data = [
				'ticket_id' => -99999,
				'company_name' => '',
				'tracking_number' => '',
				'status' => '',
				'shipped' => 0,
				'delivered' => 0,
				'recallrequest_id' => $recall_insert_id, 
				'return_id' => -99999,
				'date_added' => $current_date
			];
            
            $wpsc_shipping_method = new WP_CUST_QUERY("{$wpdb->prefix}wpsc_epa_shipping_tracking");
            $shipping_recall_insert_id = $wpsc_shipping_method->insert($shipping_data);
            
            $update_data['shipping_tracking_id'] = $shipping_recall_insert_id;
            $shipping_recall_updated = $wpsc_recall_method->update($update_data, ['id' => $recall_insert_id]);
            
            // Return recall_id
            $num = $box_details;
            $str_length = 7;
            //$recall_id = substr("000000{$num}", -$str_length);
            $recall_id = substr("000000{$recall_insert_id}", -$str_length);

            //return $box_details;           
            return $recall_id;
        }

        /**
         * Get shipping data
         * @return Id
         */
        public static function get_shipping_data_by_recall_id( $where ){           
            global $wpdb;   
            if(is_array($where) && count($where) > 0){
                foreach($where as $key => $whr) {
                    if($key == 'recallrequest_id' && is_array($whr) && count($whr) > 0) {
                        $args['where'][] = [
                            "{$wpdb->prefix}wpsc_epa_shipping_tracking.recallrequest_id", 
                            '("' . implode('", "', $whr) . '")',
                            "AND",
                            ' IN '
                        ];
                    } else {
                        $args['where'][] = ["{$wpdb->prefix}wpsc_epa_shipping_tracking.$key", "'{$whr}'"];
                    }
                }
            }

            $args['select'] = 'id, recallrequest_id, company_name, tracking_number, status';
            $shipping_data = new WP_CUST_QUERY("{$wpdb->prefix}wpsc_epa_shipping_tracking");
            $shipping_records = $shipping_data->get_results($args);
            return $shipping_records;
        }

        
        /**
         * Insert shipping data
         * @return Id
         */
        public static function add_shipping_data( $data ){            
            global $wpdb;   

            $add_shipping_data = new WP_CUST_QUERY("{$wpdb->prefix}wpsc_epa_shipping_tracking");
            $shipping_insert_id = $add_shipping_data->insert($data);
            return $shipping_insert_id;
        }

        /**
         * Get recall data
         * @return Id
         */
        public static function get_recall_data( $where ){            
            global $wpdb;   

            if(is_array($where) && count($where) > 0){
                foreach($where as $key => $whr) {
                    if($key == 'custom') {
                       $args['where']['custom'] = $whr;
                    } elseif($key == 'filter') {
                        
                        $orderby = isset($whr['orderby']) ? $whr['orderby'] : 'id';
                        $order = isset($whr['order']) ? $whr['order'] : 'DESC';
                        if($orderby == 'status') {
                            $orderby = "{$wpdb->prefix}terms.name";
                        }
                        $args['order'] = [$orderby, $order];
                        if(isset($whr['records_per_page']) && $whr['records_per_page'] > 0){  
                            $number_of_records =  isset($whr['records_per_page']) ? $whr['records_per_page'] : 20;
                            $start = isset($whr['paged']) ? $whr['paged'] : 0;
                            $args['limit'] = [$start, $number_of_records];        
                        }
                    } elseif($key == 'program_office_id') {
                        // $storage_location_id = self::get_storage_location_id_by_dc($whr);
                        // if(is_array($storage_location_id) && count($storage_location_id) > 0) {
                        //     foreach($storage_location_id as $val){
                        //         $dc_ids[] = $val->id;
                        //     }
                            $args['where'][] = [
                                "{$wpdb->prefix}wpsc_epa_program_office.office_acronym", 
                                $whr
                            ];
                        // }
                    } elseif($key == 'digitization_center') {
                        $storage_location_id = self::get_storage_location_id_by_dc($whr);
                        if(is_array($storage_location_id) && count($storage_location_id) > 0) {
                            foreach($storage_location_id as $val){
                                if($val->id) {
                                    $dc_ids[] = $val->id;
                                }
                            }
                            $dc_ids = implode(', ', $dc_ids);
                            $args['where'][] = [
                                "{$wpdb->prefix}wpsc_epa_boxinfo.storage_location_id", 
                                "($dc_ids)",
                                "AND",
                                ' IN '
                            ];
                        }
                    } elseif($key == 'id' && is_array($whr) && count($whr) > 0) {
                        $args['where'][] = [
                            "{$wpdb->prefix}wpsc_epa_recallrequest.id", 
                            "(".implode(',', $whr).")",
                            "AND",
                            ' IN '
                        ];
                    } elseif($key == 'recall_id' && is_array($whr) && count($whr) > 0) {
                        $args['where'][] = [
                            "{$wpdb->prefix}wpsc_epa_recallrequest.recall_id", 
                            '("' . implode('", "', $whr) . '")',
                            "AND",
                            ' IN '
                        ];
                    } else {
                        $args['where'][] = ["{$wpdb->prefix}wpsc_epa_recallrequest.$key", "'{$whr}'"];
                    }
                }   
            }

               $args['where'][] = [
                                "{$wpdb->prefix}wpsc_epa_recallrequest.recall_id", 
                                "0",
                                "AND",
                                ' > '
                            ];

                            // print_r($args['where']);
            // $args['where']['custom'] =  isset($args['where']['custom']) ? $args['where']['custom'] . " AND {$wpdb->prefix}wpsc_epa_recallrequest.recall_id > 0" : " {$wpdb->prefix}wpsc_epa_recallrequest.recall_id > 0";


            // $args['where'][] = ["{$wpdb->prefix}wpsc_epa_recallrequest.recall_id", 0, 'AND', '>'];


            // print_r($where);  
            // print_r($args);  

            $select_fields = [
                "{$wpdb->prefix}wpsc_epa_recallrequest" => ['id', 'recall_id', 'expiration_date','request_date', 'request_receipt_date', 'return_date', 'updated_date', 'comments', 'recall_status_id'],
                "{$wpdb->prefix}wpsc_epa_boxinfo" => ['ticket_id', 'box_id', 'storage_location_id', 'location_status_id', 'box_destroyed', 'date_created', 'date_updated'],
                "{$wpdb->prefix}wpsc_epa_folderdocinfo" => ['title', 'folderdocinfo_id as folderdoc_id'],
                "{$wpdb->prefix}wpsc_epa_program_office" => ['office_acronym'],
                "{$wpdb->prefix}wpsc_epa_shipping_tracking" => ['company_name as shipping_carrier', 'tracking_number', 'status', 'shipped', 'delivered'],
                "{$wpdb->prefix}epa_record_schedule" => ['Record_Schedule', 'Record_Schedule_Number', 'Schedule_Title'],
                "{$wpdb->prefix}terms" => ['name as recall_status'],
                "{$wpdb->prefix}wpsc_epa_recallrequest_users" => ['user_id'],
            ];

            foreach($select_fields as $key => $fields_array){
                foreach($fields_array as $field) {
                    if($key == "{$wpdb->prefix}wpsc_epa_recallrequest_users"){
                        $select[] = "GROUP_CONCAT($key.user_id) as $field";
                    } elseif($key == "{$wpdb->prefix}epa_record_schedule"){
                        $select[] = "CONCAT($key.Record_Schedule_Number, ': ' , $key.Schedule_Title) as $field";
                    } else {
                        $select[] = $key . '.' . $field;
                    }
                }
            }

            $args['groupby']  = "{$wpdb->prefix}wpsc_epa_recallrequest.recall_id";
            $args['select']  = implode(', ', $select);
            $args['join']  = [
                        [
                            'type' => 'LEFT JOIN', 
                            'table' => "{$wpdb->prefix}wpsc_epa_boxinfo", 
                            'foreign_key'  => 'box_id',
                            'compare' => '=',
                            'key' => 'id'
                            ],
                        [
                            'type' => 'LEFT JOIN', 
                            'table' => "{$wpdb->prefix}wpsc_epa_recallrequest_users", 
                            'foreign_key'  => 'id',
                            'compare' => '=',
                            'key' => 'recallrequest_id'
                        ],
                        [
                            'type' => 'LEFT JOIN', 
                            'table' => "{$wpdb->prefix}wpsc_epa_folderdocinfo", 
                            'key'  => 'id',
                            'compare' => '=',
                            'foreign_key' => 'folderdoc_id'
                        ],
                        [
                            'type' => 'LEFT JOIN', 
                            'table' => "{$wpdb->prefix}wpsc_epa_program_office", 
                            'key'  => 'id',
                            'compare' => '=',
                            'foreign_key' => 'program_office_id'
                        ],
                        [
                            'type' => 'LEFT JOIN', 
                            'table' => "{$wpdb->prefix}wpsc_epa_shipping_tracking", 
                            'key'  => 'id',
                            'compare' => '=',
                            'foreign_key' => 'shipping_tracking_id'
                        ],
                        [
                            'type' => 'LEFT JOIN', 
                            'table' => "{$wpdb->prefix}epa_record_schedule", 
                            'key'  => 'id',
                            'compare' => '=',
                            'foreign_key' => 'record_schedule_id'
                        ],
                        [
                            'type' => 'LEFT JOIN', 
                            'table' => "{$wpdb->prefix}terms", 
                            'key'  => 'term_id',
                            'compare' => '=',
                            'foreign_key' => 'recall_status_id'
                        ]
                        ];

            $wpsc_epa_box = new WP_CUST_QUERY("{$wpdb->prefix}wpsc_epa_recallrequest");
            $box_details = $wpsc_epa_box->get_results($args);

            if(count($box_details) > 0 ){
                foreach($box_details as $key => $record){
                    if(!empty($record->user_id)) {
                        $record->user_id = explode(',', $record->user_id );
                    }
                }
            }
            
            // if(count($box_details) > 0 ){
            //     $record_id = 0;
            //     $count = 0;
            //     foreach($box_details as $key => $record){
            //         if($record->id == $record_id) {
            //             unset($box_details[$key-1]);
            //             if(is_array($user_id)) {
            //                 $user_id[] = $record->user_id;
            //                 $box_details[$key]->user_id = $user_id;
            //             } else {
            //                 $box_details[$key]->user_id = [$user_id, $record->user_id];
            //             }
            //         }
            //         $record_id = $record->id;
            //         $user_id = $record->user_id;
            //         $count++;
            //     }
            // }
            return $box_details;
        }

        /**
         * Change shipping number
         * @return recall data
         */
        public static function get_storage_location_id_by_dc( $location ){            
            global $wpdb;  
            $args['select'] = "{$wpdb->prefix}wpsc_epa_storage_location.id" ;
            $args['join']  = [
                        [
                            'type' => 'INNER JOIN', 
                            'table' => "{$wpdb->prefix}wpsc_epa_storage_location", 
                            'key'  => 'digitization_center',
                            'compare' => '=',
                            'foreign_key' => 'term_id'
                        ]
            ];
            $args['where'] = [
                ["{$wpdb->prefix}terms.name", "$location"]
            ];
            $recall_req = new WP_CUST_QUERY("{$wpdb->prefix}terms");
            $storage_location_id = $recall_req->get_results( $args );
            return $storage_location_id;
        }

        /**
         * Change shipping number
         * @return recall data
         */
        public static function update_recall_shipping( $data, $where ){            
            global $wpdb;  
            
            // Get id from recall_id
            if(is_array($where) && count($where) > 0){
                foreach($where as $key => $whr) {
                    if($key == 'recall_id' && !empty($whr)) {
                        $args['where'][] = ["{$wpdb->prefix}wpsc_epa_recallrequest.$key", "'{$whr}'"];
                        $args['select'] = 'id';
                        $recall_data = new WP_CUST_QUERY("{$wpdb->prefix}wpsc_epa_recallrequest");
                        $recall_pk_id = $recall_data->get_row($args);

						$where2 = [ 'recallrequest_id' => $recall_pk_id->id ];
                    } 
                }

            }


            $recall_req = new WP_CUST_QUERY("{$wpdb->prefix}wpsc_epa_shipping_tracking");
            $recall_res = $recall_req->update( $data, $where2 );
            $get_recall_data = self::get_recall_data( ['id' => $where2['recallrequest_id']] );
            
            return $get_recall_data;
        }
        
        /**
         * Change shipping number for Returns
         * @return Return data
         */
        public static function update_return_shipping( $data, $where ){            
            global $wpdb;  
            
            // Get id from recall_id
            if(is_array($where) && count($where) > 0){
                foreach($where as $key => $whr) {
                    if($key == 'return_id' && !empty($whr)) {
                        $args['where'][] = ["{$wpdb->prefix}wpsc_epa_return.$key", "'{$whr}'"];
                        $args['select'] = 'id';
                        $return_data = new WP_CUST_QUERY("{$wpdb->prefix}wpsc_epa_return");
                        $return_pk_id = $return_data->get_row($args);

						$where2 = [ 'return_id' => $return_pk_id->id ];
                    } 
                }

            }


            $return_req = new WP_CUST_QUERY("{$wpdb->prefix}wpsc_epa_shipping_tracking");
            $return_res = $return_req->update( $data, $where2 );
            $get_return_data = self::get_return_data( ['id' => $where2['return_id']] );
            
            return $get_return_data;
        }


        
        /**
         * Delete shipping record by recall IDs
         * @return recall data
         */
        public static function delete_shipping_data_by_recall_id( $where ){            
            global $wpdb;  
            
            $args['select'] = 'id';
            if (is_array($where) && count($where) > 0) {
                foreach ($where as $key => $whr) {
                    if ($key == 'recallrequest_id' && is_array($whr) && count($whr) > 0) {
                        $args['where'][] = [
                            "{$wpdb->prefix}wpsc_epa_shipping_tracking.recallrequest_id",
                            '("' . implode('", "', $whr) . '")',
                            "AND",
                            ' IN ',
                        ];
                    } else {
                        $args['where'][] = ["{$wpdb->prefix}wpsc_epa_shipping_tracking.$key", "'{$whr}'"];
                    }
                }

                $shipping_data = new WP_CUST_QUERY("{$wpdb->prefix}wpsc_epa_shipping_tracking");
                $shipping_records = $shipping_data->get_results($args);
                foreach($shipping_records as $record){
                    $shipping_data->delete($record->id);
                }
            }
            return $where;

        }
        
        /**
         * Change request date
         * @return recall data
         */
        public static function update_recall_dates( $data, $where ){            
            global $wpdb;  
            $recall_req = new WP_CUST_QUERY("{$wpdb->prefix}wpsc_epa_recallrequest");
            $recall_res = $recall_req->update( $data, $where );
            $get_recall_data = self::get_recall_data( $where );
            return $get_recall_data;
        }
        
        /**
         * Change request date
         * @return return data
         */
        public static function update_return_dates( $data, $where ){            
            global $wpdb;  
            $return_req = new WP_CUST_QUERY("{$wpdb->prefix}wpsc_epa_return");
            $return_res = $return_req->update( $data, $where );
            $get_return_data = self::get_return_data( $where );
            return $get_return_data;
        }
		
		/**
         * Change request table data
         * @return recall data
         */
        public static function update_recall_data( $data, $where ){            
            global $wpdb;  
            $recall_req = new WP_CUST_QUERY("{$wpdb->prefix}wpsc_epa_recallrequest");
            $recall_res = $recall_req->update( $data, $where );
            $get_recall_data = self::get_recall_data( $where );
            return $get_recall_data;
        }
        
        /**
         * Change request table data
         * @return recall data
         */
        public static function update_return_data( $data, $where ){            
            global $wpdb;  
            $return_req = new WP_CUST_QUERY("{$wpdb->prefix}wpsc_epa_return");
            $return_res = $return_req->update( $data, $where );
            $get_return_data = self::get_return_data( $where );
            return $get_return_data;
        }

        
        /**
         * Get primary id by retun id
         * @return Id
         */
        public static function get_primary_id_by_retunid( $where ){           
            global $wpdb;   
            if(is_array($where) && count($where) > 0){
                foreach($where as $key => $whr) {
                    if($key == 'return_id' && !empty($whr)) {
                       $args['where'][] = ["{$wpdb->prefix}wpsc_epa_return.$key", "'{$whr}'"];
                        $args['select'] = 'id';
                        $retun_data = new WP_CUST_QUERY("{$wpdb->prefix}wpsc_epa_return");
                        $retun_data_records = $retun_data->get_row($args);
                    } 
                }

            }
            return $retun_data_records;
        }     

        /**
         * Get ticket id by retun id or recall_id
         * @return Id
         */
        public static function get_ticket_id_by( $where ){           
            global $wpdb;   
            if(is_array($where) && count($where) > 0){
                foreach($where as $key => $whr) {
                    if(($key == 'return_id' || $key == 'recallrequest_id') && !empty($whr)) {
                        $args['where'][] = ["{$wpdb->prefix}wpsc_epa_shipping_tracking.$key", "'{$whr}'"];
                        $args['select'] = 'ticket_id';
                        $shipping_data = new WP_CUST_QUERY("{$wpdb->prefix}wpsc_epa_shipping_tracking");
                        $ticket_id = $shipping_data->get_results($args);
                    } 
                }
            }
            return $ticket_id;
        }
        
        /**
         * Get ticket id (without leading zeros) by box id or folder/file id
         * @return Id
         */
        public static function get_ticket_id_from_box_folder_file( $where ){              
            
            $id = $where['box_folder_file_id'];
            
			if( substr_count($id, '-') == 1 ) {
				$type = 'Box';
				$arr = explode("-", $id, 2);
				$ticket_id = (int)$arr[0];
			} elseif( substr_count($id, '-') == 3 ) {
				$type = 'Folder/File';
				$arr = explode("-", $id, 2);
				$ticket_id = (int)$arr[0];
			} else {
				$type = 'Error';
				$ticket_id = null;
			}
			
			$return = [
				'type' => $type,
				'ticket_id' => $ticket_id,
				'item_id' => $id
			];
			
            return $return;
        }
        
        // Get the number of accessions associated to a request for validation purposes
        public static function get_accession_count( $ticket_id ) {
	        global $wpdb;

	        $get_count = $wpdb->get_row("SELECT COUNT(DISTINCT record_schedule_id) as count FROM {$wpdb->prefix}wpsc_epa_boxinfo WHERE ticket_id = ".$ticket_id);
	        
	        return $get_count->count;
        }
        
        // Get ticket status term id from non-zero'd ticket id. 
        public static function get_ticket_status( $where ) {
	        global $wpdb;
	        $id = $where['ticket_id'];
	        $the_row = $wpdb->get_row("SELECT ticket_status FROM {$wpdb->prefix}wpsc_ticket WHERE id = ".$id);
	        
	        return $the_row->ticket_status;
        }
        
        // Get ticket owner's customer_name (wp display name) id from non-zero'd ticket id. 
        public static function get_ticket_owner_agent_id( $where ) {
	        global $wpdb;
	        $id = (int)$where['ticket_id'];
	        $the_row = $wpdb->get_row("SELECT customer_email FROM {$wpdb->prefix}wpsc_ticket WHERE id = ".$id);
	        
	        $the_user_row = $wpdb->get_row("SELECT id FROM {$wpdb->prefix}users WHERE user_email = " . $the_row->customer_email );
	        
	        $user_obj = get_user_by( 'email', $the_row->customer_email );
	        
	        //$the_user_row->id;
	        $user_id_array = array($user_obj->ID);
	        $user_agent_id = self::translate_user_id( $user_id_array, 'agent_term_id');
	        
	        return $user_agent_id;
/*
	        $data = [
		        'id' => $id,
		        'customer_email' => $the_row->customer_email,
		        'user_id' => $user_obj->ID,
		        'user_array' => $user_id_array,
		        'agent_id' => $user_agent_id
	        ];
	        return $data;
*/
        }
        

        public static function calc_max_gap_val($dc_final){

        global $wpdb; 
        $find_sequence = $wpdb->get_row("
        WITH 
        cte1 AS
        (
        SELECT id, 
            CASE WHEN     occupied  = LAG(occupied) OVER (ORDER BY id)
                        AND remaining = LAG(remaining) OVER (ORDER BY id)
                    THEN 0
                    ELSE 1 
                    END values_differs
        FROM wpqa_wpsc_epa_storage_status
        WHERE digitization_center = '" . $dc_final . "'
        ),
        cte2 AS 
        (
        SELECT id,
            SUM(values_differs) OVER (ORDER BY id) group_num
        FROM cte1
        ORDER BY id
        )
        SELECT MIN(id) as id
        FROM cte2
        GROUP BY group_num
        ORDER BY COUNT(*) DESC LIMIT 1;
        ");

        $sequence_shelfid = $find_sequence->id;

        $seq_shelfid_final = $sequence_shelfid-1;
                    
        // Determine largest Gap of consecutive shelf space
        $find_gaps = $wpdb->get_row("
        WITH 
        cte1 AS
        (
        SELECT shelf_id, remaining, SUM(remaining = 0) OVER (ORDER BY id) group_num
        FROM wpqa_wpsc_epa_storage_status
        WHERE digitization_center = '" . $dc_final . "' AND
        id BETWEEN 1 AND '" . $seq_shelfid_final . "'
        )
        SELECT GROUP_CONCAT(shelf_id) as shelf_id,
            GROUP_CONCAT(remaining) as remaining,
            SUM(remaining) as total
        FROM cte1
        WHERE remaining != 0
        GROUP BY group_num
        ORDER BY total DESC
        LIMIT 1
        ");

        $max_gap_value = $find_gaps->total;
        return $max_gap_value;
    }


    public static function get_unassigned_boxes($tkid){

        global $wpdb; 

        $obtain_box_ids_details = $wpdb->get_results("
        SELECT wpqa_wpsc_epa_boxinfo.storage_location_id
        FROM wpqa_wpsc_epa_boxinfo 
        INNER JOIN wpqa_wpsc_epa_storage_location ON wpqa_wpsc_epa_boxinfo.storage_location_id = wpqa_wpsc_epa_storage_location.id 
        WHERE
        wpqa_wpsc_epa_storage_location.aisle = 0 AND 
        wpqa_wpsc_epa_storage_location.bay = 0 AND 
        wpqa_wpsc_epa_storage_location.shelf = 0 AND 
        wpqa_wpsc_epa_storage_location.position = 0 AND
        wpqa_wpsc_epa_boxinfo.ticket_id = '" . $tkid . "'
        ");

        $box_id_array = array();
        foreach ($obtain_box_ids_details as $box_id_val) {
        $box_id_array_val = $box_id_val->storage_location_id;
        array_push($box_id_array, $box_id_array_val);
        }
        return $box_id_array;
    }

        public static function get_default_digitization_center($id)
        {
            global $wpdb;

            // Get Distinct program office ID
            $get_program_office_id = $wpdb->get_results("
            SELECT wpqa_wpsc_epa_program_office.organization_acronym as acronym
            FROM wpqa_wpsc_epa_boxinfo 
            LEFT JOIN wpqa_wpsc_epa_program_office ON wpqa_wpsc_epa_boxinfo.program_office_id = wpqa_wpsc_epa_program_office.office_code 
            WHERE wpqa_wpsc_epa_boxinfo.ticket_id = '" . $id . "'
            ");

            $program_office_east_array = array();
            $program_office_west_array = array();

            foreach ($get_program_office_id as $program_office_id_val) {
            $program_office_val = $program_office_id_val->acronym;

            $east_region = array("R01", "R02", "R03", "AO", "OITA", "OCFO", "OCSPP", "ORD", "OAR", "OW", "OIG", "OGC", "OMS", "OLEM", "OECA");
            $west_region = array("R04", "R05", "R06", "R07", "R08", "R09", "R10");

            if (in_array($program_office_val, $east_region))
            {
            array_push($program_office_east_array, $program_office_val);
            }

            if (in_array($program_office_val, $west_region))
            {
            array_push($program_office_west_array, $program_office_val);
            }
            }

            $east_count = count($program_office_east_array);
            $west_count = count($program_office_west_array);

            $set_center = '';

            if ($east_count > $west_count)
            {
            $set_center = 62;
            }

            if ($west_count > $east_count)
            {
            $set_center = 2;
            }

            if ($west_count == $east_count)
            {
            $set_center = 666;
            }

            return $set_center;
        }

        public static function fetch_request_id($id)
        {
            global $wpdb; 
            $args = [
                'where' => ['id', $id],
            ];
            $wpqa_wpsc_ticket = new WP_CUST_QUERY("{$wpdb->prefix}wpsc_ticket");
            $box_details = $wpqa_wpsc_ticket->get_row($args, false);
            $asset_id = $box_details->request_id;
            return $asset_id;
        }

        //Function to obtain serial number (box ID) from database based on Request ID
        public static function fetch_box_id($id)
        {
            global $wpdb; 
            // die(print_r($wpdb->prefix));
            $array = array();
            $args = [
                'where' => ['ticket_id', $id],
            ];
            $wpqa_wpsc_epa_boxinfo = new WP_CUST_QUERY("{$wpdb->prefix}wpsc_epa_boxinfo");
            $box_result = $wpqa_wpsc_epa_boxinfo->get_results($args, false);

            foreach ($box_result as $box) {
                array_push($array, $box->box_id);
            }
            return $array;
        }
        
        //Function to obtain full list of Program Offices
        public static function fetch_program_office_array()
        {
            global $wpdb;
            
        $po_result = $wpdb->get_results("
        SELECT DISTINCT office_acronym FROM wpqa_wpsc_epa_program_office
        WHERE id <> -99999
        ");

            $array = array();

            foreach ($po_result as $po) {
                array_push($array, $po->office_acronym);
            }
            return $array;
        }
        
        //gets list of record schedules for the box-details page
        public static function fetch_record_schedule_array()
        {
            global $wpdb;
            $array = array();
            
            $record_schedule = $wpdb->get_results("SELECT * FROM wpqa_epa_record_schedule WHERE Reserved_Flag = 0 AND id <> -99999 AND Ten_Year = 1 ORDER BY Record_Schedule_Number");
            
            foreach($record_schedule as $rs)
            {
                array_push($array, $rs->Record_Schedule_Number);
            }
            return $array;
        }
        
        //Convert box patt id to id
        public static function convert_box_id( $id )
        {
            global $wpdb;
            $id = '"'.$id.'"';
            $args = [
                'select' => 'id',
                'where' => ['box_id',  $id],
            ];
            $wpqa_wpsc_box = new WP_CUST_QUERY("{$wpdb->prefix}wpsc_epa_boxinfo");
            $request_key = $wpqa_wpsc_box->get_row($args, false);

            $key = $request_key->id;
            return $key;
        }
        
        //Convert box patt id to patt request id
        public static function convert_box_request_id( $id )
        {
            global $wpdb;

            $request_id_get = $wpdb->get_row("SELECT wpqa_wpsc_ticket.request_id as request_id FROM wpqa_wpsc_epa_boxinfo, wpqa_wpsc_ticket WHERE wpqa_wpsc_epa_boxinfo.box_id = '" . $id . "' AND wpqa_wpsc_epa_boxinfo.ticket_id = wpqa_wpsc_ticket.id");
            
            $request_id_val = $request_id_get->request_id;
            
            return $request_id_val;
        }
        
        //Convert patt request id to id
        public static function convert_request_id( $id )
        {
            global $wpdb;
            $id = '"'.$id.'"';
            $args = [
                'select' => 'id',
                'where' => ['request_id',  $id],
            ];
            $wpqa_wpsc_request = new WP_CUST_QUERY("{$wpdb->prefix}wpsc_ticket");
            $id_key = $wpqa_wpsc_request->get_row($args, false);

            $key = $id_key->id;
            return $key;
        }
        
        //Convert id to patt request id
        public static function convert_request_db_id( $id )
        {
            global $wpdb;
            $id = '"'.$id.'"';
            $args = [
                'select' => 'request_id',
                'where' => ['id',  $id],
            ];
            $wpqa_wpsc_request = new WP_CUST_QUERY("{$wpdb->prefix}wpsc_ticket");
            $id_key = $wpqa_wpsc_request->get_row($args, false);

            $key = $id_key->request_id;
            return $key;
        }
        
        //Function to obtain box ID, title, date and contact 
        
        public static function fetch_box_content($id)
        {
            global $wpdb; 
            // die(print_r($wpdb->prefix));
            $array = array();
            $args = [
                'where' => ['box_id', $id],
            ];
            $wpqa_wpsc_epa_folderdocinfo = new WP_CUST_QUERY("{$wpdb->prefix}wpsc_epa_folderdocinfo");
            $box_content = $wpqa_wpsc_epa_folderdocinfo->get_results($args, false);

            foreach ($box_content as $box) {
                $parent = new stdClass;
                $parent->id = $box->folderdocinfo_id;
                $parent->title = $box->title;
                $parent->date = $box->date;
                $parent->contact = $box->epa_contact_email;
                $parent->source_format = $box->source_format;
                $parent->validation = $box->validation;
                $parent->validation_user = $box->validation_user_id;
                $parent->destruction = $box->unauthorized_destruction;
                $array[] = $parent;

            }
            return $array;
        }
        
        //Function to obtain box ID, location, shelf, bay and index from ticket 
        
        public static function fetch_box_details($id)
        {
            global $wpdb; 
            // die(print_r($wpdb->prefix));
            $array = array();
            $args = [
                'where' => [
                    ['ticket_id',  $id],
                    ['wpqa_wpsc_epa_boxinfo.storage_location_id', 'wpqa_wpsc_epa_storage_location.id', 'AND'],
                ]
            ];
            $wpqa_wpsc_epa_boxinfo = new WP_CUST_QUERY("{$wpdb->prefix}wpsc_epa_boxinfo, {$wpdb->prefix}wpqa_wpsc_epa_storage_location");
            $box_result = $wpqa_wpsc_epa_boxinfo->get_results($args, false);
        
            foreach ($box_result as $box) {
                $box_shelf_location = $box->aisle . 'a_' .$box->bay .'b_' . $box->shelf . 's_' . $box->position .'p';
                $parent = new stdClass;
                $parent->id = $box->box_id;
                $parent->shelf_location = $box_shelf_location;
                $array[] = $parent;

            }
            return $array;
        }

        //Function to obtain box details from box ID
        public static function fetch_box_id_a( $id )
        {
            $boxidArray = explode(',', $id);
            return $boxidArray;
        }

        //Function to obtain location value from database
        public static function fetch_location( $id )
        {
            global $wpdb;
            $array = array();
            // $box_digitization_center = $wpdb->get_results( "SELECT * FROM wpqa_wpsc_epa_boxinfo WHERE ticket_id = " . $GLOBALS['id']);
            $args = [
                'where' => ['ticket_id', $id],
            ];
            $wpqa_wpsc_epa_boxinfo = new WP_CUST_QUERY("{$wpdb->prefix}wpsc_epa_boxinfo");
            $box_digitization_center = $wpqa_wpsc_epa_boxinfo->get_results($args, false);

            foreach ($box_digitization_center as $location) {
                array_push($array, strtoupper($location->location));
            }
            return $array;
        }

        //Function to obtain program office from database
        public static function fetch_program_office( $id )
        {
            global $wpdb;
            $array = array();
            // $request_program_office = $wpdb->get_results("SELECT acronym FROM wpqa_wpsc_epa_boxinfo, wpqa_wpsc_epa_program_office WHERE wpqa_wpsc_epa_boxinfo.program_office_id = wpqa_wpsc_epa_program_office.id AND ticket_id = " . $GLOBALS['id']);
            $args = [
                'select' => 'acronym',
                'where' => [
                    ['ticket_id',  $id],
                    ['wpqa_wpsc_epa_boxinfo.program_office_id', 'wpqa_wpsc_epa_program_office.id', 'AND'],
                ]
            ];

            $wpqa_wpsc_epa_boxinfo_wpqa_wpsc_epa_program_office = new WP_CUST_QUERY("{$wpdb->prefix}wpsc_epa_boxinfo, {$wpdb->prefix}wpsc_epa_program_office");
            $request_program_office = $wpqa_wpsc_epa_boxinfo_wpqa_wpsc_epa_program_office->get_results($args, false);
            // dd($request_program_office);
            foreach ($request_program_office as $program_office) {
                array_push($array, strtoupper($program_office->acronym));
            }
            // dd($array);
            return $array;
        }

        //Function to obtain shelf from database
        public static function fetch_shelf( $id )
        {
            global $wpdb;
            $array = array();
            // $request_shelf = $wpdb->get_results("SELECT shelf FROM wpqa_wpsc_epa_boxinfo, wpqa_wpsc_ticket WHERE wpqa_wpsc_epa_boxinfo.ticket_id = wpqa_wpsc_ticket.id AND ticket_id = " . $GLOBALS['id']);
            $args = [
                'select' => 'shelf',
                'where' => [
                    ['ticket_id',  $id],
                    ['wpqa_wpsc_epa_boxinfo.ticket_id', 'wpqa_wpsc_ticket.id', 'AND'],
                ],
            ];
            $wpqa_wpsc_epa_boxinfo_wpqa_wpsc_ticket = new WP_CUST_QUERY("{$wpdb->prefix}wpsc_epa_boxinfo, {$wpdb->prefix}wpsc_ticket");
            $request_shelf = $wpqa_wpsc_epa_boxinfo_wpqa_wpsc_ticket->get_results($args, false);

            foreach ($request_shelf as $shelf) {
                array_push($array, strtoupper($shelf->shelf));
            }
            return $array;
        }

        //Function to obtain bay from database
        public static function fetch_bay( $id )
        {
            global $wpdb;
            $array = array();
            // $request_bay = $wpdb->get_results("SELECT bay FROM wpqa_wpsc_epa_boxinfo, wpqa_wpsc_ticket WHERE wpqa_wpsc_epa_boxinfo.ticket_id = wpqa_wpsc_ticket.id AND ticket_id = " . $GLOBALS['id']);

            $args = [
                'select' => 'bay',
                'where' => [
                    ['ticket_id',  $id],
                    ['wpqa_wpsc_epa_boxinfo.ticket_id', 'wpqa_wpsc_ticket.id', 'AND'],
                ],
            ];
            $wpqa_wpsc_epa_boxinfo_wpqa_wpsc_ticket = new WP_CUST_QUERY("{$wpdb->prefix}wpsc_epa_boxinfo, {$wpdb->prefix}wpsc_ticket");
            $request_bay = $wpqa_wpsc_epa_boxinfo_wpqa_wpsc_ticket->get_results($args, false);

            foreach ($request_bay as $bay) {
                array_push($array, strtoupper($bay->bay));
            }
            return $array;
        }

        //Function to obtain create month and year from database
        public static function fetch_create_date( $id )
        {
            global $wpdb;
            // $request_create_date = $wpdb->get_row( "SELECT date_created FROM wpqa_wpsc_ticket WHERE id = " . $GLOBALS['id']);

            $args = [
                'select' => 'date_created',
                'where' => ['id',  $id],
            ];
            $wpqa_wpsc_ticket = new WP_CUST_QUERY("{$wpdb->prefix}wpsc_ticket");
            $request_create_date = $wpqa_wpsc_ticket->get_row($args, false);

            $create_date = $request_create_date->date_created;
            $date = strtotime($create_date);

            return strtoupper(date('M y', $date));
        }

        //Function to obtain request key
        public static function fetch_request_key( $id )
        {
            global $wpdb;
            // $request_key = $wpdb->get_row( "SELECT ticket_auth_code FROM wpqa_wpsc_ticket WHERE id = " . $GLOBALS['id']);

            $args = [
                'select' => 'ticket_auth_code',
                'where' => ['id',  $id],
            ];
            $wpqa_wpsc_ticket = new WP_CUST_QUERY("{$wpdb->prefix}wpsc_ticket");
            $request_key = $wpqa_wpsc_ticket->get_row($args, false);

            $key = $request_key->ticket_auth_code;
            return $key;
        }

        //Function to obtain box count
        public static function fetch_box_count( $id )
        {
            global $wpdb;
            // $box_count = $wpdb->get_row( "SELECT COUNT(ticket_id) as count FROM wpqa_wpsc_epa_boxinfo WHERE ticket_id = " . $GLOBALS['id']);

            $args = [
                'select' => 'COUNT(ticket_id) as count',
                'where' => ['ticket_id', $id],
            ];
            $wpqa_wpsc_epa_boxinfo = new WP_CUST_QUERY("{$wpdb->prefix}wpsc_epa_boxinfo");
            $box_count = $wpqa_wpsc_epa_boxinfo->get_row($args, false);

            $count_val = $box_count->count;
            return $count_val;
        }
        
        function convert_epc_pattboxid($epc) 
        {
            $remove_E = strtok($epc, 'E');
            
            $newstr = substr_replace($remove_E, '-', 8, 0);
            
            return $newstr;
        }
            
        function convert_pattboxid_epc($pattid) 
        {
            $add_E = str_replace('-', '', $pattid).'E';
            
            $str_length = 24;
            
            $newstr = str_pad($add_E, $str_length, 0);
            
            
            return $newstr;
        }
        
       	// Translates an array of wp user ids to wpsc agent ids and visaversa
        public static function translate_user_id( $array_of_users, $change_to_type ) {
	
			$agent_ids = self::get_user_lut();
			
			if( $change_to_type == 'wp_user_id' ) {
				if( is_array($array_of_users) ) {
					foreach ( $array_of_users as $wp_id ) {
						$key = array_search( $wp_id, array_column($agent_ids, 'agent_term_id'));
						$wp_user_id = $agent_ids[$key]['wp_user_id']; //current user agent term id
						$assigned_agents[] = $wp_user_id;
					}
				} 
				
			} elseif ( $change_to_type == 'agent_term_id' ) {
				if( is_array($array_of_users) ) {
					foreach ( $array_of_users as $agent_term ) {
						$key = array_search( $agent_term, array_column($agent_ids, 'wp_user_id'));
						$agent_term_id = $agent_ids[$key]['agent_term_id']; //current user agent term id
						$assigned_agents[] = $agent_term_id;
					}
				} 
			}
			
			return $assigned_agents;
		}
		
		// Creates an array that acts as a lookup table for wp user id and wpsc agent id
        public static function get_user_lut() {
			// Get current user id & convert to wpsc agent id.
			$agent_ids = array();
			$agents = get_terms([
				'taxonomy'   => 'wpsc_agents',
				'hide_empty' => false,
				'orderby'    => 'meta_value_num',
				'order'    	 => 'ASC',
			]);
			foreach ($agents as $agent) {
				$agent_ids[] = [
					'agent_term_id' => $agent->term_id,
					'wp_user_id' => get_term_meta( $agent->term_id, 'user_id', true),
				];
			}
			
			return $agent_ids;
		}
		
		
		// Filters the input $agent_array (agent_ids) based on the roles given by the $role_array
		public static function return_agent_ids_in_role( $agent_array, $role_array ) {
			
			if( count($role_array) == 0 || count($agent_array) == 0 ) {
				return false;
			}
			
			$role_num_array = [];
			$agent_role_lut = self::get_agent_user_role_lut();
			
			foreach( $role_array as $role_name ) {
				if( $role_name == 'Administrator' ) { // name from PATT Digitization Staff (https://086.info/wordpress3/wp-admin/admin.php?page=wpsc-support-agents)
					$role_num_array[] = 1; // role id in termmeta with meta_key = 'role'
				} elseif( $role_name == 'Manager' ) {
					$role_num_array[] = 4;
				} elseif( $role_name == 'Agent' ) {
					$role_num_array[] = 2;
				} elseif( $role_name == 'Requester' ) {
					$role_num_array[] = 3;
				}
			}
			
			$agent_ids_in_roles = [];
			
			foreach( $agent_array as $agent ) {
				
				$index = array_search( $agent, array_column($agent_role_lut, 'agent_id') );

				
				if( in_array( $agent_role_lut[$index]['role'], $role_num_array ) ) {
					$agent_ids_in_roles[] = $agent;
				}
				
				//$agent_ids_in_roles[] = [ $agent, $index ];
			}
			
			return $agent_ids_in_roles;
			
		}
		
		// Creates an array that acts as a lookup table for wp user id and wpsc agent id
        public static function get_agent_user_role_lut() {
			// Get current user id & convert to wpsc agent id.
			$agent_ids = array();
			$agents = get_terms([
				'taxonomy'   => 'wpsc_agents',
				'hide_empty' => false,
				'orderby'    => 'meta_value_num',
				'order'    	 => 'ASC',
			]);
			foreach ($agents as $agent) {
				$agent_ids[] = [
					'agent_id' => $agent->term_id,
					'role' => get_term_meta( $agent->term_id, 'role', true)
				];
			}
			
			return $agent_ids;
		}
		
		//Function to return agent ids for a particular group
        public static function agent_from_group($agent_group_name)
        {
            global $wpdb;

$agents = get_terms([
	'taxonomy'   => 'wpsc_agents',
	'hide_empty' => false,
	'meta_query' => array(
    array(
      'key'       => 'agentgroup',
      'value'     => '0',
      'compare'   => '='
    )
  )
]);

$agent_role = get_option('wpsc_agent_role');

$agent_group_array = array();
foreach ( $agents as $agent ) {
    //GET USER ID $agent_user_id = get_term_meta( $agent->term_id, 'user_id', true);
    $agent_id = $agent->term_id;
    $role_id = get_term_meta( $agent->term_id, 'role', true);
if ($agent_role[$role_id]['label'] == $agent_group_name) {
array_push($agent_group_array, $agent_id);
}

}

           return $agent_group_array;

        }

		//TESTING ONLY REMOVE CONVERT DB EMAIL to USER ID
        public static function convert_db_contact_email($agent_email)
        {
            global $wpdb;

	        $get_user_id = $wpdb->get_row("SELECT ID FROM wpqa_users WHERE user_email = '" . $agent_email . "'");
	        
	        if (count($get_user_id)> 0){
	       	$user_id_array = array($get_user_id->ID);
	        $user_id = self::translate_user_id( $user_id_array, 'agent_term_id');
            
            } else {
            $user_id = 'error';
            }
            
	        return $user_id;

        }	
		// Returns an array of acceptable box status that can be set given the array of Box Folder File IDs, based on the following rules:
		//
		// 1) IF Nobody assigned to the box THEN all but Pending must be disabled. (672,671,65,6,673,674,743,66,68,67)
		// 2) Box is not validated (66,68,67) - Validation is a status in same list, right? Must be in status of Validation?
		//                                   - count validated flag is in folder doc 
		// 3) Destruction Approval - Check to see if request contains a destruction_approval of 1 in wpqa_wpsc_ticket - IF = 0 then disable
		//                         - Disable the ability to select Destruction approval if Not approved. 
		// 4) if request status = 3,670,69 THEN 672,671,65,6,673,674,743,66,68,67 Need to be disabled (Only allow Pending) 
		// 5) restrict the available status to only the next status in the recall process
		
        public static function get_restricted_box_status_list( $item_ids, $role = 'Agent' ) {
	        
	        global $wpdb;
	        
	        $restricted_status_list = array();
			$restriction_reason = '';
			$all_unassigned_x = true;
			$condition_c1 = false; 
			$condition_c4 = false; 
			
			foreach( $item_ids as $item ) {
				$box_obj = self::get_box_file_details_by_id($item);
				$status_agent_array = self::get_user_status_data( ['box_id' => $box_obj->Box_id_FK ] );
				$ignore_box_status = ['Pending', 'Ingestion', 'Completed', 'Dispositioned'];
				$status_list_assignable = self::get_all_status($ignore_box_status);
			 	$where = ['box_folder_file_id' => $box_obj->box_id ];
			 	$ticket_id_obj = self::get_ticket_id_from_box_folder_file( $where );
			
				// Condition 1: IF Nobody assigned to the box THEN all but Pending must be disabled.
				$all_assigned = false;
				$all_unassigned = true;
				foreach( $status_agent_array['status'] as $term_id=>$user_array ) {
					
					if( array_key_exists($term_id, $status_list_assignable) ) {
						if( count($user_array) > 0 && $user_array != 'N/A' ) {
							//users exist
							$all_unassigned = false;
							break;
						}
					}
				}
				
				// Condition 1 SET.
				if( $all_unassigned ) {
					$restriction_reason .= '<p>Box '.$box_obj->box_id.' has no one assigned to Any Status. (C1)<p>';
					$condition_c1 = true;
					
					if( !in_array('Scanning Preparation', $restricted_status_list) ) {
						$restricted_status_list[] = 'Scanning Preparation';
					} 
					if( !in_array('Scanning/Digitization', $restricted_status_list) ) {
						$restricted_status_list[] = 'Scanning/Digitization';
					} 
					if( !in_array('QA/QC', $restricted_status_list) ) {
						$restricted_status_list[] = 'QA/QC';
					} 
					if( !in_array('Digitized - Not Validated', $restricted_status_list) ) {
						$restricted_status_list[] = 'Digitized - Not Validated';
					} 
					if( !in_array('Ingestion', $restricted_status_list) ) {
						$restricted_status_list[] = 'Ingestion';
					} 
					if( !in_array('Validation', $restricted_status_list) ) {
						$restricted_status_list[] = 'Validation';
					} 
					if( !in_array('Re-scan', $restricted_status_list) ) {
						$restricted_status_list[] = 'Re-scan';
					} 
					if( !in_array('Completed', $restricted_status_list) ) {
						$restricted_status_list[] = 'Completed';
					} 
					if( !in_array('Destruction Approval', $restricted_status_list) ) {
						$restricted_status_list[] = 'Destruction Approval';
					} 
					if( !in_array('Dispositioned', $restricted_status_list) ) {
						$restricted_status_list[] = 'Dispositioned';
					} 
				}
				
				
				// Condition 2: Box is not validated (66,68,67)
				$get_sum_total = $wpdb->get_row("select sum(a.total_count) as sum_total_count
													from (
														SELECT (
															SELECT count(id) 
																FROM wpqa_wpsc_epa_folderdocinfo as c
															WHERE box_id = a.id 
														) as total_count 
														FROM wpqa_wpsc_epa_boxinfo as a  
														WHERE a.id = '" . $box_obj->Box_id_FK . "'
													) 
												a");
				
				$sum_total_val = $get_sum_total->sum_total_count;
				
				$get_sum_validation = $wpdb->get_row("select sum(a.validation) as sum_validation
														from (
															SELECT (
																SELECT sum(validation = 1) FROM wpqa_wpsc_epa_folderdocinfo WHERE box_id = a.id
															) as validation 
															FROM wpqa_wpsc_epa_boxinfo as a 
															
															WHERE a.id = '" . $box_obj->Box_id_FK . "'	
														) 
													a");									
									
				$sum_validation = $get_sum_validation->sum_validation;
			
				$validated = '';
				
				if($sum_total_val == $sum_validation) {
					$validated = 1;
				} else {
					$validated = 0;
				}
				
				// Condition 2 SET
				if( !$validated ) {
					$restriction_reason .= '<p>Contents of Box '.$box_obj->box_id.' have not been Validated. (C2)</p>';
					
					if( !in_array('Completed', $restricted_status_list) ) {
						$restricted_status_list[] = 'Completed';
					} 
					if( !in_array('Dispositioned', $restricted_status_list) ) {
						$restricted_status_list[] = 'Dispositioned';
					}
					if( !in_array('Destruction Approval', $restricted_status_list) ) {
						$restricted_status_list[] = 'Destruction Approval';
					}
				}
				
				
				// Condition 3 - Destruction Approval
				
			 	$box_destruction_approval = $wpdb->get_row("SELECT destruction_approval FROM wpqa_wpsc_ticket WHERE id='".$ticket_id_obj['ticket_id']."'");				
				
				// Condition 3 SET - Show destruction approval setting?
				if( $box_destruction_approval->destruction_approval ) {
					
					// if Destruction Approval has already been restricted AND C1 OR C4 has never come up... 
					if( in_array('Destruction Approval', $restricted_status_list) && !$condition_c1 && !$condition_c4 ) {
						$the_key = array_search('Destruction Approval', $restricted_status_list);
						unset($restricted_status_list[$the_key]);
						array_values($restricted_status_list);
					}
				} else {
					$restriction_reason .= '<p>Contents of Box '.$box_obj->box_id.' have not been approved for Destruction. (C3)</p>';
					if( !in_array('Destruction Approval', $restricted_status_list) ) {
						$restricted_status_list[] = 'Destruction Approval';
					}
					if( !in_array('Dispositioned', $restricted_status_list) ) {
						$restricted_status_list[] = 'Dispositioned';
					}
				}
				
				// Condition 4 - if request status = 3,670,69 - only allow 'Pending'
				$data = [ 'ticket_id'=>$ticket_id_obj['ticket_id'] ];
				$ticket_status = self::get_ticket_status( $data );
				
				
				// Condition 4 SET
				// if request status = 3,670,69 THEN 672,671,65,6,673,674,743,66,68,67 Need to be disabled (Only allow Pending) 
				if( $ticket_status == 3 || $ticket_status == 670 || $ticket_status == 69 ) {
					$save_enabled = false;
					$restriction_reason .= '<p>Containing Request of Box '.$box_obj->box_id.' has a status of New, Cancelled, or Initial Review Rejected. (C4)</p>';
					$condition_c4 = true;
					
					if( !in_array('Scanning Preparation', $restricted_status_list) ) {
						$restricted_status_list[] = 'Scanning Preparation';
					} 
					if( !in_array('Scanning/Digitization', $restricted_status_list) ) {
						$restricted_status_list[] = 'Scanning/Digitization';
					} 
					if( !in_array('QA/QC', $restricted_status_list) ) {
						$restricted_status_list[] = 'QA/QC';
					} 
					if( !in_array('Digitized - Not Validated', $restricted_status_list) ) {
						$restricted_status_list[] = 'Digitized - Not Validated';
					} 
					if( !in_array('Ingestion', $restricted_status_list) ) {
						$restricted_status_list[] = 'Ingestion';
					} 
					if( !in_array('Validation', $restricted_status_list) ) {
						$restricted_status_list[] = 'Validation';
					} 
					if( !in_array('Re-scan', $restricted_status_list) ) {
						$restricted_status_list[] = 'Re-scan';
					} 
					if( !in_array('Completed', $restricted_status_list) ) {
						$restricted_status_list[] = 'Completed';
					} 
					if( !in_array('Destruction Approval', $restricted_status_list) ) {
						$restricted_status_list[] = 'Destruction Approval';
					} 
					if( !in_array('Dispositioned', $restricted_status_list) ) {
						$restricted_status_list[] = 'Dispositioned';
					} 
					
				}
				
				
				// Condition 5: restrict the available status to only the next status in the recall process
				// if multi, they all have same status at this point. 
				// if single, find status, get next status. 
				
				// get current status 
				// list all statuses - self::get_all_status();
				// get/set NEXT status
				// remove NEXT status from array
				// get NEW $restricted_status_list
				// compare restricted_status_lists 
				// box_statuses: [723] => Name
				
				if ($role == 'Agent') {
					$current_status_term = self::get_box_file_details_by_id($item)->box_status;
					$next_status = '';
					$all_statuses = self::get_all_status();
					$current_status = $all_statuses[$current_status_term];
					
					// Set $next_status
					switch($current_status) {
						case 'Pending':
							$next_status = 'Scanning Preparation';
							break;
						case 'Scanning Preparation':
							$next_status = 'Scanning/Digitization';
							break;
						case 'Scanning/Digitization':
							$next_status = 'QA/QC';
							break;
						case 'QA/QC':
							$next_status = 'Digitized - Not Validated';
							break;
						case 'Digitized - Not Validated':
							$next_status = 'Ingestion';
							break;
						case 'Ingestion':
							$next_status = 'Validation';
							break;
						case 'Validation':
							$next_status = 'Completed';
							break;
						case 'Completed':
							$next_status = '';
							break;
						case 'Destruction Approval':
							$next_status = '';
							break;
						case 'Dispositioned':
							$next_status = '';
							break;						
								
							
					}
					
					// remove NEXT status from $all_statuses array
					if( $next_status != '') {
						$the_key = array_search($next_status, $all_statuses);
						unset($all_statuses[$the_key]);
					
					
					
						//array_values($all_statuses);
						
						// get NEW $restricted_status_list
						// list_2: [748] => Pending [672] => Scanning | $restricted_status_list: [0]=> Pending [1]=> Scanning Preparation
						$restricted_status_list_2 = $all_statuses; 
						foreach( $restricted_status_list_2 as $key=>$value ) {
							$restricted_status_list[] = $value;
						}
						$restricted_status_list = array_unique($restricted_status_list);
					
					} 
						
					
					// When Destruction Approval is selected then Validation is disabled (9)
					if( $current_status == 'Destruction Approval' ) {
						if( !in_array('Validation', $restricted_status_list) ) {
							$restricted_status_list[] = 'Validation';
						} 
					}
					// When Dispositioned is selected then disable Destruction Approval (10)
					if( $current_status == 'Dispositioned' ) {
						if( !in_array('Destruction Approval', $restricted_status_list) ) {
							$restricted_status_list[] = 'Destruction Approval';
						} 
					}	
					
					// When Completed is selected then disable all other statuses (11)
					if( $current_status == 'Completed' ) {
						$restricted_status_list_2 = $all_statuses; 
						foreach( $restricted_status_list_2 as $key=>$value ) {
							$restricted_status_list[] = $value;
						}
						$restricted_status_list = array_unique($restricted_status_list);
					}	
				} // End IF $role == 'Agent'		
				
				// Waiting/Shelved and Re-scan should always be enabled except when status is Completed, Destruction Approval, or Dispositioned (1)
				if( $current_status != 'Completed' || $current_status != 'Destruction Approval' || $current_status != 'Dispositioned' ) {
					
					$ws_index = array_search('Waiting/Shelved', $restricted_status_list);
					$rs_index = array_search('Re-scan', $restricted_status_list);
					if ( $ws_index ) {
						unset($restricted_status_list[$ws_index]);
					}
					if ( $rs_index ) {
						unset($restricted_status_list[$rs_index]);
					}
				}
				
				// allow current status to be placed in the list
				$current_status_index = array_search($current_status, $restricted_status_list);
				if ( $current_status_index ) {
					unset($restricted_status_list[$current_status_index]);
				}

				
				
			}
			
			$box_statuses = self::get_all_status($restricted_status_list);
			$return_array = array();
			$return_array['box_statuses'] = $box_statuses;
			$return_array['restriction_reason'] = $restriction_reason;
			
			// DEBUG - START
			$return_array['debug_restricted_status_list'] = $restricted_status_list;
			$return_array['debug_restricted_status_list_2'] = $restricted_status_list_2;
			$return_array['debug_next_status'] = $next_status;
			$return_array['debug_current_status'] = $current_status;						
			// DEBUG - END

			return $return_array;
	    }
	    
	    
	    public static function item_in_return( $item_id, $type, $subfolder_path) {
		   	
		   	global $wpdb;
 		   	
		   	$return_info = [];
		   	
		   	
		   	
			if( $type == 'Box' ) {
				$box_fk = self::get_id_by_box_id( $item_id );
/*
				$return_check = $wpdb->get_row(
												"SELECT return_id
												FROM wpqa_wpsc_epa_return_items
												WHERE box_id = '" .  $box_fk . "'");
*/

				$return_check = $wpdb->get_row(
												"SELECT
												    Item.return_id as return_id,
												    Ret.return_status_id as return_status
												FROM
												    wpqa_wpsc_epa_return_items Item
												JOIN wpqa_wpsc_epa_return Ret ON
												    Ret.id = Item.return_id
												WHERE
												    Ret.return_status_id <> 754 AND Ret.return_status_id <> 791 AND Item.box_id = '" .  $box_fk . "'");												
												

				
				//$return_info = $return_check;
				
				if( $return_check->return_id != null ) {
					
					$num = $return_check->return_id;	
		            $str_length = 7;	
		            $return_id = substr("000000{$num}", -$str_length);	
		            
		            $box_link = '<a href="'.$subfolder_path.'/wp-admin/admin.php?page=boxdetails&pid=boxsearch&id='.$item_id.'" >'.$item_id.'</a>';
		            
					$return_info['item_error'] = 'Box '.$box_link.' already in Return ';
					$return_info['return_id'] = $return_id;
					
					return $return_info;
				}
				
				
				
			} elseif ($type == 'Folder/Doc') {
				
				$folderdoc_fk = self::get_id_by_folderdoc_id($item_id);
				
/*
				$return_check = $wpdb->get_row(
												"SELECT return_id
												FROM wpqa_wpsc_epa_return_items
												WHERE folderdoc_id = '" .  $folderdoc_fk . "'");
*/
												
				$return_check = $wpdb->get_row(
												"SELECT
												    Item.return_id as return_id,
												    Ret.return_status_id as return_status
												FROM
												    wpqa_wpsc_epa_return_items Item
												JOIN wpqa_wpsc_epa_return Ret ON
												    Ret.id = Item.return_id
												WHERE
												     Ret.return_status_id <> 754 AND Ret.return_status_id <> 791 AND Item.folderdoc_id = '" .  $folderdoc_fk . "'");											
				if( $return_check->return_id != null ) {
					
					$num = $return_check->return_id;	
		            $str_length = 7;	
		            $return_id = substr("000000{$num}", -$str_length);
		            
		            $folder_link = '<a href="'.$subfolder_path.'/wp-admin/admin.php?pid=docsearch&page=filedetails&id='.$item_id.'" >'.$item_id.'</a>';
		            //$box_link = '<a href="'.$subfolder_path.'/wp-admin/admin.php?page=boxdetails&pid=boxsearch&id='.$details_array['box_id'].'" >'.$details_array['box_id'].'</a>';
		            
					$return_info['item_error'] = 'Containing Box for Folder/File '.$folder_link.' already in Return ';
					$return_info['return_id'] = $return_id;
					
					return $return_info;
				}	
			}
		    
		    return $return_info;
	    }


        /**
         * Convert Ticket ID to Request ID
         * @return id Request ID
         */
         
        public static function ticket_to_request_id($ticket_id)
        {

$str_length = 7;
$padded_request_id = substr("000000{$ticket_id}", -$str_length);

return $padded_request_id;

        }

        /**
         * Help text tooltip function
         * @return help text
         */
         
        public static function helptext_tooltip($post_name)
        {
		   	global $wpdb;

			$get_post_details = $wpdb->get_row("SELECT a.post_content 
			FROM wpqa_posts a 
			INNER JOIN wpqa_term_relationships b ON a.id = b.object_id 
			INNER JOIN wpqa_term_taxonomy c ON b.term_taxonomy_id = c.term_taxonomy_id 
			INNER JOIN wpqa_terms d ON c.term_id = d.term_id WHERE a.post_status = 'publish' AND
			d.slug = 'help-messages' AND a.post_name = '" . $post_name . "'");
			$post_details_content = $get_post_details->post_content; 

return $post_details_content;

        }
        
        /**
         * Identify the type of PATT ID
         * @return id type
         */
         
        public static function patt_id_type($patt_id)
        {
$patt_type = '';
if (strpos($patt_id, 'R-') !== false) {
$patt_type = 'recall';
} elseif ((strpos($patt_id, 'D-') !== false) || (strpos($patt_id, 'RTN-') !== false)) {
$patt_type = 'decline';
} elseif (strlen($patt_id) == 7 && !preg_match('/[A-Za-z]/', $patt_id)) {
$patt_type = 'request';
} elseif (substr_count($patt_id, '-') == 1 && !preg_match('/[A-Za-z]/', $patt_id)) {
$patt_type = 'box';
} elseif (substr_count($patt_id, '-') == 3 && !preg_match('/[A-Za-z]/', $patt_id)) {
$patt_type = 'folder_file';
}

return $patt_type;
        }
            
        /**
         * Insert new notification to one or many users
         * @return pm id
         */
         
        public static function insert_new_notification($post_name, $array_of_users, $patt_id, $data = [], $email = 0) {

			global $wpdb;
			
			// Get post details
			$get_post_details = $wpdb->get_row("SELECT a.post_title, a.post_content FROM wpqa_posts a
			INNER JOIN wpqa_term_relationships b ON a.id = b.object_id 
			INNER JOIN wpqa_term_taxonomy c ON b.term_taxonomy_id = c.term_taxonomy_id 
			INNER JOIN wpqa_terms d ON c.term_id = d.term_id
			WHERE a.post_status = 'publish' AND d.slug = 'email-messages' AND a.post_name = '" . $post_name . "'");
			$post_details_subject = $get_post_details->post_title;
			$post_details_content = $get_post_details->post_content;
			
			if($post_details_subject == '' || $post_details_content == '')  {
				return 'Invalid Message Type'; 
			} else {
			    
				if ($patt_id != '') {
				// Determine PATT ID Type: Request, Box, Folder/File, Recall, Decline
				
					$patt_type = Patt_Custom_Func::patt_id_type($patt_id);
					
					switch ($patt_type) {
					    case "recall":
					        $patt_url = admin_url( 'admin.php?page=recalldetails&id='.$patt_id );
					        break;
					    case "decline":
//					        $patt_url = admin_url( 'admin.php?page=returndetails&id='.$patt_id );
					        $patt_url = admin_url( 'admin.php?page=declinedetails&id='.$patt_id );					        
					        break;
					    case "request":
					        $patt_url = admin_url( 'admin.php?page=wpsc-tickets&id='.$patt_id );
					        break;
					    case "box":
					        $patt_url = admin_url( 'admin.php?page=boxdetails&id='.$patt_id );
					        break;
					    case "folder_file":
					        $patt_url = admin_url( 'admin.php?page=filedetails&id='.$patt_id );
					        break;
					}
					
					// Declare Replacement variables
					$item_type = '';
					$action_initiated_by = '';
					$item_id = '';
					
					if(!empty($data)) {
					// Data Switch
					foreach( $data as $key => $val ) {
						switch ($key) {
						    case "item_type":
						        $item_type = $val;
						        break;
						    case "action_initiated_by":
						        $action_initiated_by = $val;
						        break;
						    case "item_id":
						        if( is_array($val) ) {
							        $item_id = implode( ', ', $val );
						        } else {
							        $item_id = $val;
						        }
						        break;    
						        
						}
					}
					}
					
					$tags = array( '%ID%', '%URL%', '%ITEM_TYPE%', '%ITEM_ID%', '%INITIATED_BY%' );
					$replacement = array( $patt_id, $patt_url, $item_type, $item_id, $action_initiated_by );
					
					$post_details_subject = str_replace( $tags, $replacement, $get_post_details->post_title );
					$post_details_content = str_replace( $tags, $replacement, $get_post_details->post_content );
				 
				}
							
				$wpdb->insert('wpqa_pm', array(
				    'subject' => $post_details_subject,
				    'content' => $post_details_content,
				    'date' => current_time('mysql', 1)
				));
				
				$insert_id = $wpdb->insert_id;
				
				$wp_user_id_array = Patt_Custom_Func::translate_user_id($array_of_users,'wp_user_id');
				
				foreach ($wp_user_id_array as $wp_user_id) {

					//For each username
					$wpdb->insert('wpqa_pm_users', array(
					    'pm_id' => $insert_id,
					    'recipient' => $wp_user_id,
					    'viewed' => 0,
					    'deleted' => 1
					));
					
					// Send email notification
					$option = get_option( 'rwpm_option' );
					
					// send email to user
					if ( $option['email_enable'] && $email == 1) {
						$sender = $wpdb->get_var( "SELECT display_name FROM $wpdb->users WHERE user_login = 'admin' LIMIT 1" );
		
						// replace tags with values
						$tags = array( '%TITLE%','%BODY%','%BLOG_NAME%', '%BLOG_ADDRESS%', '%SENDER%', '%INBOX_URL%' );
						$replacement = array( $post_details_subject, $post_details_content, get_bloginfo( 'name' ), get_bloginfo( 'admin_email' ), $sender, admin_url( 'admin.php?page=rwpm_inbox' ) );
		
						$email_name = str_replace( $tags, $replacement, $option['email_name'] );
						$email_address = str_replace( $tags, $replacement, $option['email_address'] );
						$email_subject = str_replace( $tags, $replacement, $option['email_subject'] );
						$email_body = str_replace( $tags, $replacement, $option['email_body'] );
		
						// set default email from name and address if missed
						if ( empty( $email_name ) )
							$email_name = get_bloginfo( 'name' );
		
						if ( empty( $email_address ) )
							$email_address = get_bloginfo( 'admin_email' );
		
						$email_subject = strip_tags( $email_subject );
						if ( get_magic_quotes_gpc() )
						{
							$email_subject = stripslashes( $email_subject );
							$email_body = stripslashes( $email_body );
						}
						$email_body = nl2br( $email_body );
		
						$recipient_email = $wpdb->get_var( "SELECT user_email from $wpdb->users WHERE ID = $wp_user_id" );
						$mailtext = "<html><head><title>$email_subject</title></head><body>$email_body</body></html>";
		
						// set headers to send html email
						$headers = "To: $recipient_email\r\n";
						$headers .= "From: $email_name <$email_address>\r\n";
						$headers .= "MIME-Version: 1.0\r\n";
						$headers .= 'Content-Type: ' . get_bloginfo( 'html_type' ) . '; charset=' . get_bloginfo( 'charset' ) . "\r\n";
		
						wp_mail( $recipient_email, $email_subject, $mailtext, $headers );
					}
				
				}
			
				return $insert_id;
			}
        }	    


// Notifications for Comments - Determine agents assigned.
	    public static function agents_assigned_request( $ticket_id ) {
	       global $wpdb;
	      
	       //OBTAIN BOX IDs

$get_box_ids = $wpdb->get_results("SELECT id from wpqa_wpsc_epa_boxinfo WHERE ticket_id = " . $ticket_id);

$user_id_array = array();

 foreach ($get_box_ids as $item) {
$box_id = $item->id;
$get_user_ids = $wpdb->get_results("SELECT user_id from wpqa_wpsc_epa_boxinfo_userstatus where box_id = " . $box_id);

 foreach ($get_user_ids as $item) {
     $user_id = $item->user_id;
     array_push($user_id_array, $user_id);
 }
        }
$user_id_final = array_values(array_unique($user_id_array));

        return $user_id_final;
}

// Notifications for Comments - Send emails out
	    public static function insert_new_comment_notification( $request_id, $comment, $user_ids, $bcc ) {
		    global $wpdb;
		                
if(count($bcc) == 0) {
    $email_array = array();
} else {
    $email_array = $bcc;
}
                        foreach($user_ids as $item) {
                           $additional_email = $wpdb->get_row( "SELECT user_email from wpqa_users WHERE ID = " . $item ); 

                           array_push($email_array, $additional_email->user_email);
                        }
		   				
                        //foreach email 
                        
                        foreach($email_array as $recipient_email) {
            
						if ( empty( $email_name ) )
							$email_name = get_bloginfo( 'name' );
		
						if ( empty( $email_address ) )
							$email_address = get_bloginfo( 'admin_email' );
							
		                $email_subject = 'PATT New Comment: Request #'.Patt_Custom_Func::ticket_to_request_id($request_id);
						$mailtext = "<html><head><title>$email_subject</title></head><body>$comment</body></html>";
		
						// set headers to send html email
						$headers = "To: $recipient_email\r\n";
						$headers .= "From: $email_name <$email_address>\r\n";
						$headers .= "MIME-Version: 1.0\r\n";
						$headers .= 'Content-Type: ' . get_bloginfo( 'html_type' ) . '; charset=' . get_bloginfo( 'charset' ) . "\r\n";
		
						wp_mail( $recipient_email, $email_subject, $mailtext, $headers );
                        }
		    
	    }
	    
	    public static function item_in_recall( $item_id ) {
		    global $wpdb;
		    //, $type, $subfolder_path
 		   	
		   	
		   	
		   	$box_file_details = self::get_box_file_details_by_id($item_id);
			
			$details_array = json_decode(json_encode($box_file_details), true);
			
			
			if ( $details_array == false ) {
				$details_array['search_error'] = true;
			} else {
				$details_array['search_error'] = false;
			}
			
			
			// Set variables for search
			$is_folder_search = array_key_exists('Folderdoc_Info_id',$details_array);
			$details_array['in_recall'] = false;
			$details_array['is_folder_search'] = $is_folder_search;
			$details_array['is_folder_search_TEST'] =  ($is_folder_search) ? 'true' : 'false';
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
			
			// Not needed as this is checked in the indiviual sections so that more details errors are returned. 
			//			WHERE wpqa_wpsc_epa_recallrequest.recall_status_id <> 733 
			//	AND wpqa_wpsc_epa_recallrequest.recall_status_id <> 734
			//	AND wpqa_wpsc_epa_recallrequest.recall_status_id <> 1
			
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
			
				
			} // if else

			return $details_array;
		   	   	
		} // END item_in_recall
        
        
        // Utility function for searching a multidimentional array in php
        public static function searchMultiArray($val, $array) {
			foreach ($array as $element) {
				if ($element['name'] == $val) {
					return $element['slug'];
				}
			}
			return null;
		}
		
		// Utility function for searching a multidimentional array 
		public static function searchMultiArrayByFieldValue( $input_array, $field, $value ) {
		   foreach($input_array as $key => $val)
		   {
		      if ( $val[$field] === $value )
		         return $key;
		   }
		   return false;
		}
        

    }
    // new Patt_Custom_Func;
}