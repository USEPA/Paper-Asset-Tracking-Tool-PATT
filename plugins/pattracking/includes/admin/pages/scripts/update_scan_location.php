<?php

global $wpdb, $current_user, $wpscfunction;

$path = preg_replace('/wp-content.*$/','',__DIR__);
include($path.'wp-load.php');

if(isset($_POST['postvarsboxid']) && isset($_POST['postvarslocation'])){
        
        $message = "";
        $response_arr = array();
        
        $box_id = $_POST['postvarsboxid'];
        $location = $_POST['postvarslocation']; 
        
        /* the variables */
        $array_location = array($location);
        $count = count($box_id);
        $newArray_location = array();
        
        /* create a new array with AT LEAST the desired number of elements by joining the array at the end of the new array */
        while(count($newArray_location) <= $count){
            $newArray_location = array_merge($newArray_location, $array_location);
        }
        // reduce the new array to the desired length (as there might be too many elements in the new array)
        $array_location = array_slice($newArray_location, 0, $count);

        $box_insert = array_combine($box_id, $array_location);
 
        /* Identify the table */
        $table_name = 'wpqa_wpsc_epa_scan_list';

        $column_name = '';
        $error_flag = 0;
        $date = date('Y-m-d H:i:s');
        
        /* Get the ticket ID aka Request ID */
        $get_ticket_id = $wpdb->get_row("
                                        SELECT ticket_id
                                        FROM wpqa_wpsc_epa_boxinfo
                                        WHERE
                                        box_id = '" . $box_id . "'
                                        ");
                        
        $ticket_id = $get_ticket_id->ticket_id;

        
        foreach ($box_insert as $key => $value) {
            
            if(preg_match('/\b(SCN-\d\d-e|SCN-\d\d-w)\b/i', $value)) {
                $column_name = 'scanning_id';
                
                $scan_table_name = 'wpqa_wpsc_epa_scan_list';
                $wpdb->insert($table_name, array(
                                'box_id' => esc_sql($key),
                                $column_name => esc_sql(strtoupper($value)),
                                'date_modified' => $date,
                            ));
                                    $message = "Updated: Box ID " . $key . " with the following Scanning ID: " . $value;
                                    do_action('wpppatt_after_shelf_location', $ticket_id, $box_id, $message);  
                                    echo $message;  
                                
            }
            
            if(preg_match('/^\b(sa-e|sa-w)\b$/i', $value)) {
                
                $column_name = 'stagingarea_id';

                $scan_table_name = 'wpqa_wpsc_epa_scan_list';
                $wpdb->insert($scan_table_name, array(
                                'box_id' => esc_sql($key),
                                $column_name => esc_sql(strtoupper($value)),
                                'date_modified' => $date,
                            ));
                            
                                    $message = "Updated: Box ID " . $key . " with the following Staging Area ID: " . $value;
                                    do_action('wpppatt_after_shelf_location', $ticket_id, $box_id, $message);  
                                    echo $message;  
        
            }
            
            if(preg_match('/(\bcid-\d\d-e\b|\bcid-\d\d-w\b)|(\bcid-\d\d-east\scui\b|\bcid-\d\d-west\scui\b)|(\bcid-\d\d-east\b|\bcid-\d\d-west\b)|(\bcid-\d\d-eastcui\b|\bcid-\d\d-westcui\b)/gim', $value)) {
                
                $column_name = 'cart_id';
                
                $scan_table_name = 'wpqa_wpsc_epa_scan_list';
                $wpdb->insert($scan_table_name, array(
                                'box_id' => esc_sql($key),
                                $column_name => esc_sql(strtoupper($value)),
                                'date_modified' => $date,
                            ));

                    $message = "Updated: Box ID " . $key . " with the following Cart ID: " . $value;
                    do_action('wpppatt_after_shelf_location', $ticket_id, $box_id, $message);
                    echo $message; 

            } 
            
            if(preg_match('/^\d{1,3}A_\d{1,3}B_\d{1,3}S_\d{1,3}P_(E|W|ECUI|WCUI)$/i', $value)) {
               
                $column_name = 'shelf_location';
                $position_array = explode('_', $value);

                $aisle = substr($position_array[0], 0, -1);
                $bay = substr($position_array[1], 0, -1);
                $shelf = substr($position_array[2], 0, -1);
                $position = substr($position_array[3], 0, -1);
                $dc = $position_array[4];
                $center_term_id = term_exists($dc);
                $new_term_object = get_term( $center_term_id );
                $new_position_id_storage_location = $aisle.'A_'.$bay.'B_'.$shelf.'S_'.$position.'P_'.$dc; 
                $new_A_B_S_only_storage_location = $aisle.'_'.$bay.'_'.$shelf;

                /* 6/25/2020 - JM - Add logic to determine if a location is occupied. 
                    Assign location change - Generate a message stating that the location is already taken.*/
                    
                $box_id_new_scan = $key;
                
			    $storage_location_details = $wpdb->get_row(
			                                                "SELECT shelf_id 
			                                                FROM wpqa_wpsc_epa_storage_status
                                                            WHERE shelf_id = '" . esc_sql($new_A_B_S_only_storage_location) . "'"
                                			              );
                                			              
    			$facility_shelfid = $storage_location_details->shelf_id;


    			if($facility_shelfid == $new_A_B_S_only_storage_location ){
    		                  
                    $box_id_new_scan = $key;
    		
    		        /* Determine if the position is occupied */
    		        $existing_boxinfo_details = $wpdb->get_row(
			                                                "SELECT b.aisle as aisle,b.bay as bay,b.shelf as shelf,b.position as position,b.digitization_center as dc
                                                            FROM wpqa_wpsc_epa_boxinfo a                
                                                            LEFT JOIN wpqa_wpsc_epa_storage_location b ON a.storage_location_id = b.id
                                                            WHERE a.box_id = '" . esc_sql($box_id_new_scan) . "'"
                                			              );
                                			              
    		        $existing_boxinfo_aisle = $existing_boxinfo_details->aisle;
    			    $existing_boxinfo_bay = $existing_boxinfo_details->bay;
    			    $existing_boxinfo_shelf = $existing_boxinfo_details->shelf;
    			    $existing_boxinfo_position = $storage_location_details->position;
    			    $existing_boxinfo_term_object = get_term( $center_term_id );
    		        $existing_boxinfo_position_id_storage_location = $existing_aisle.'A_'.$existing_bay.'B_'.$existing_shelf.'S_'.$existing_position.'P_'.$term_object->slug;
    		   		
                   	if($existing_boxinfo_position_id_storage_location != $new_position_id_storage_location ){
                            
                            /* Update the storage status table */
                            $existing_shelf_update = $wpdb->get_row("
                                SELECT remaining
                                FROM wpqa_wpsc_epa_storage_status
                                WHERE
                                shelf_id = '" . $new_A_B_S_only_storage_location . "' AND
                                digitization_center = '" . $center_term_id . "'
                            ");
                            
                            $existing_shelf_update_remaining = $existing_shelf_update->remaining + 1;
            				$existing_ss_table_name = 'wpqa_wpsc_epa_storage_status';
            				
            				if ($existing_shelf_update_remaining == 4) {
            				    $existing_ss_data_update = array('occupied' => 0, 'remaining' => $existing_shelf_update_remaining);
            				} else {
            				    $existing_ss_data_update = array('occupied' => 1, 'remaining' => $existing_shelf_update_remaining);
            				}
            				$existing_ss_data_where = array('shelf_id' => $new_A_B_S_only_storage_location);
            				$wpdb->update($existing_ss_table_name, $existing_ss_data_update, $existing_ss_data_where);
				
                            /* Update the storage location table */
                            $table_name = 'wpqa_wpsc_epa_storage_location';
                            $data_update = array('aisle' => $aisle ,'bay'=>$bay,'shelf'=>$shelf,'position'=>$position, 'digitization_center'=>$center_term_id);
                            $data_where = array('id' => $key);
                            $wpdb->update($table_name , $data_update, $data_where);
                    
        				    /* Update the scanning table */
                            $scan_table_name = 'wpqa_wpsc_epa_scan_list';
                            $wpdb->insert($scan_table_name, array(
                                            'box_id' => esc_sql($key),
                                            $column_name => esc_sql(strtoupper($value)),
                                            'date_modified' => $date,
                                        ));

                                    $message = "Updated: Box ID " . $key . " with the following Shelf Location: " . $value;
                                    do_action('wpppatt_after_shelf_location', $ticket_id, $box_id, $message);
                                    echo $message; 

                    } else {
                            $message = "Not Updated: The location ". $existing_shelf_id ." is occupied.";
                            do_action('wpppatt_after_shelf_location', $ticket_id, $box_id, $message); 
                            echo $message;            
                            
    			    }
				}else{
				    $message = "Not Updated: The location ". $new_position_id_storage_location . " does not exist in the facility";
                    do_action('wpppatt_after_shelf_location', $ticket_id, $box_id, $message); 
                    echo $message;  
    			}		
            }
        }
        

}
    
?>