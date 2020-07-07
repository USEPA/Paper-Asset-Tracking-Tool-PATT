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


        /**
         * Get Box details by BOX/FOLDER ID !!
         * @return Id, Title, Record Schedule, Programe Office !!
         */
        public static function get_box_file_details_by_id( $search_id ){            
            global $wpdb; 
            $box_details = [];
            $args = [
                'select' => "box_id, {$wpdb->prefix}wpsc_epa_boxinfo.id as Box_id_FK, program_office_id as box_prog_office_code, box_destroyed,
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
                    {$wpdb->prefix}wpsc_epa_boxinfo.program_office_id,  
                    index_level,
                    title, 
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
            $shipping_data = [
				'ticket_id' => -99999,
				'company_name' => '',
				'tracking_number' => '',
				'status' => '',
				'shipped' => 0,
				'recallrequest_id' => $recall_insert_id, 
				'return_id' => -99999
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
                "{$wpdb->prefix}wpsc_epa_shipping_tracking" => ['company_name as shipping_carrier', 'tracking_number', 'status'],
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
            
            $record_schedule = $wpdb->get_results("SELECT * FROM wpqa_epa_record_schedule WHERE Reserved_Flag = 0 AND id <> -99999 ORDER BY Record_Schedule_Number");
            
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

    }
    // new Patt_Custom_Func;
}