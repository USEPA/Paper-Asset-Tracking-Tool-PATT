<?php

global $wpdb, $current_user, $wpscfunction;

$path = preg_replace('/wp-content.*$/','',__DIR__);
include($path.'wp-load.php');

if(isset($_POST['postvarsboxid']) && isset($_POST['postvarslocation'])){
        $record_updated = false;
        $evaluated = false;
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
        $ticket_id = "";
        $current_box_id = "";
        
        foreach ($box_insert as $key => $value) {
            
            $get_ticket_id = $wpdb->get_row("
                                        SELECT ticket_id
                                        FROM wpqa_wpsc_epa_boxinfo
                                        WHERE
                                        box_id = '" . $key . "'
                                        ");
                        
            $ticket_id = $get_ticket_id->ticket_id;         
            
            
            if($ticket_id == ""){
  
                $GLOBALS[$evaluated] = false;
                $GLOBALS[$record_updated] = false;
                $message = "Not Updated: The Box ID does not exist.\n\n";
                echo $message; 
            }else{
            
                if(preg_match('/\b(SCN-\d\d-e|SCN-\d\d-w)\b/i', $value)) {
                    $column_name = 'scanning_id';
                    
                                        /* Change the box status has been changed to "Assigned to Scanner"  */
                                        $location_statuses = $wpdb->get_row(
            			                                                "SELECT id as id,locations as locations
                                                                        FROM wpqa_wpsc_epa_location_status                
                                                                        WHERE locations = 'Assigned to Scanner'
                                        			                ");
                                        			                
                                        $location_statuses_id = $location_statuses->id;
            			                $location_statuses_locations = $location_statuses->locations;
                                        
                                        /* Set status value for box id to the returned value from above statement*/
                                        $loc_status_boxinfo_table_name = 'wpqa_wpsc_epa_boxinfo';
                                        $loc_status_boxinfo_data_update = array('location_status_id' => $location_statuses_id);
                                        $loc_status_boxinfo_data_where = array('box_id' => $key);
                                        $wpdb->update($loc_status_boxinfo_table_name , $loc_status_boxinfo_data_update, $loc_status_boxinfo_data_where);
                    
                    $scan_table_name = 'wpqa_wpsc_epa_scan_list';
                    $wpdb->insert($table_name, array(
                                    'box_id' => esc_sql($key),
                                    $column_name => esc_sql(strtoupper($value)),
                                    'date_modified' => $date,
                                ));
                                        $message = "Updated: Box ID " . $key . " has been ". $location_statuses_locations . " " . strtoupper($value). ".\n\n";
                                        do_action('wpppatt_after_shelf_location', $ticket_id, $key, $message);  
                                        echo $message;  
                                    
                                $GLOBALS[$record_updated] = true;
                                $GLOBALS[$evaluated] = true;
                }else{
                    $GLOBALS[$record_updated] = false; 
                    $GLOBALS[$evaluated] = true;     
                }
                
                if(preg_match('/^\b(sa-e|sa-w)\b$/i', $value)) {
                    
                    $column_name = 'stagingarea_id';
    
                                        /* Change the box status has been changed to "In Staging Area"  */
                                        $location_statuses = $wpdb->get_row(
            			                                                "SELECT id as id,locations as locations
                                                                        FROM wpqa_wpsc_epa_location_status                
                                                                        WHERE locations = 'In Staging Area'
                                        			                ");
                                        			                
                                        $location_statuses_id = $location_statuses->id;
            			                $location_statuses_locations = $location_statuses->locations;
                                        
                                        /* Set status value for box id to the returned value from above statement*/
                                        $loc_status_boxinfo_table_name = 'wpqa_wpsc_epa_boxinfo';
                                        $loc_status_boxinfo_data_update = array('location_status_id' => $location_statuses_id);
                                        $loc_status_boxinfo_data_where = array('box_id' => $key);
                                        $wpdb->update($loc_status_boxinfo_table_name , $loc_status_boxinfo_data_update, $loc_status_boxinfo_data_where);
    
                    $scan_table_name = 'wpqa_wpsc_epa_scan_list';
                    $wpdb->insert($scan_table_name, array(
                                    'box_id' => esc_sql($key),
                                    $column_name => esc_sql(strtoupper($value)),
                                    'date_modified' => $date,
                                ));
                                
                                        $message = "Updated: Box ID " . $key . " is " . $$location_statuses_locations . " " . strtoupper($value). ".\n\n";
                                        do_action('wpppatt_after_shelf_location', $ticket_id, $key, $message);  
                                        echo $message;  
                    $GLOBALS[$record_updated] = true;  
                    $GLOBALS[$evaluated] = true;
                }else{
                    $GLOBALS[$record_updated] = false; 
                    $GLOBALS[$evaluated] = true;     
                }
                
                /*  JM - 08032020 - Recall ID */
                if(preg_match_all('/(R-\d+)/i', $value)) {
                    
                    $column_name = 'recall_id';
    
                    /* Determine if the Recall ID is associated with the Box ID.  
                    
                    ...TODO .. 08032020 - Update SQL for Return ID Querying
                    
                    /* JM - 8/18/2020 - SQL code to check recall/return id */
                    $GLOBALS[$recall_status_bl] = $recall_return_status_check($column_name, $key, $value);
                    
                    
                    
                    
                    
                    $verify_recallID_box_link = $wpdb->get_row(
            			                                                "SELECT recall_id as recall_id
                                                                        FROM wpqa_wpsc_epa_recallrequest                
                                                                        WHERE box_id = '" . $key . "'
                                        			           ");
                                        			                
                    $location_recall_status_id = $verify_recallID_box_link->recall_status_id;
                                        
                                        /* ?? Set recall_id value for box id to the returned value from above statement ??
                                        $loc_status_boxinfo_table_name = 'wpqa_wpsc_epa_boxinfo';
                                        $loc_status_boxinfo_data_update = array('location_recall_status_id' => $location_recall_status_id);
                                        $loc_status_boxinfo_data_where = array('box_id' => $key);
                                        $wpdb->update($loc_status_boxinfo_table_name , $loc_status_boxinfo_data_update, $loc_status_boxinfo_data_where); */


                    /* Get the recall status and display it in the front end audit log entry html */
                    $get_recall_status_string = $wpdb->get_row(
            			                                                "SELECT name as name
                                                                        FROM wpqa_terms               
                                                                        WHERE term_id = '" . $recall_status_id . "'
                                        			           ");
                    $recall_status_string = $get_recall_status_string->name;
                    

                    $message = "Updated: The Box ID " . $key . " associated with Recall ID " . strtoupper($value). " is in the following status: ". $recall_status_string .".\n\n";
                    do_action('wpppatt_after_shelf_location', $ticket_id, $key, $message); 
                    echo $message;   
                    $GLOBALS[$record_updated] = true;  
                    $GLOBALS[$evaluated] = true;
                }else{
                    $GLOBALS[$record_updated] = false; 
                    $GLOBALS[$evaluated] = true;     
                }
                
                if(preg_match_all('/(RMA-\d+)/i', $value)) {
                    
                    $column_name = 'return_id';
    
                    /* Determine if the Return ID is associated with the Box ID.  
                    
                    ...TODO .. 08032020 - Update SQL for Return ID Querying */
                    /* JM - 8/18/2020 - SQL code to check recall/return id */
                   $GLOBALS[$recall_status_bl] = $recall_return_status_check($column_name, $key, $value);
                   
                    
                    
                    
                    $verify_returnID_box_link = $wpdb->get_row(
            			                                                "SELECT return_id as return_id
                                                                        FROM wpqa_wpqa_wpsc_epa_return_items               
                                                                        WHERE box_id = '" . $key . "'
                                        			           ");
                                        			                
                    $location_return_id = $verify_returnID_box_link->return_id;
                                        
                                        /* ?? Set return_id value for box id to the returned value from above statement ??
                                        $loc_status_boxinfo_table_name = 'wpqa_wpsc_epa_boxinfo';
                                        $loc_status_boxinfo_data_update = array('location_return_id' => $location_return_id);
                                        $loc_status_boxinfo_data_where = array('box_id' => $key);
                                        $wpdb->update($loc_status_boxinfo_table_name , $loc_status_boxinfo_data_update, $loc_status_boxinfo_data_where); */
    
                    /* Get the return status and display it in the front end audit log entry html */
                    
                    $get_returnID_status = $wpdb->get_row(
            			                                                "SELECT return_status_id as return_status_id
                                                                        FROM wpqa_wpsc_epa_return               
                                                                        WHERE return_id = '" . $key . "'
                                        			           ");
                    
                    $return_status_id = $get_returnID_status->return_status_id;
                    $return_status_string = $wpdb->get_row(
            			                                                "SELECT name as name
                                                                        FROM wpqa_terms               
                                                                        WHERE term_id = '" . $return_status_id . "'
                                        			           ");
                    


                    $message = "Updated: The Box ID " . $key . " associated with Return ID " . strtoupper($value). " is in the following status: ". $return_status_string .".\n\n";
                    do_action('wpppatt_after_shelf_location', $ticket_id, $key, $message); 
                    echo $message;   
                    
                    $GLOBALS[$record_updated] = true;  
                    $GLOBALS[$evaluated] = true;
                    
                }else{
                    $GLOBALS[$record_updated] = false; 
                    $GLOBALS[$evaluated] = true;     
                }
                
                if(preg_match_all('/(\bcid-\d\d-e\b|\bcid-\d\d-w\b)|(\bcid-\d\d-east\scui\b|\bcid-\d\d-west\scui\b)|(\bcid-\d\d-east\b|\bcid-\d\d-west\b)|(\bcid-\d\d-eastcui\b|\bcid-\d\d-westcui\b)/im', $value)) {
                    
                    $column_name = 'cart_id';
                    
                                        /* Change the box status has been changed to "on cart"  */
                                        $location_statuses = $wpdb->get_row(
            			                                                "SELECT id as id,locations as locations
                                                                        FROM wpqa_wpsc_epa_location_status                
                                                                        WHERE locations = 'On Cart'
                                        			                ");
                                        			                
                                        $location_statuses_id = $location_statuses->id;
            			                $location_statuses_locations = $location_statuses->locations;
                                        
                                        /* Set status value for box id to the returned value from above statement*/
                                        $loc_status_boxinfo_table_name = 'wpqa_wpsc_epa_boxinfo';
                                        $loc_status_boxinfo_data_update = array('location_status_id' => $location_statuses_id);
                                        $loc_status_boxinfo_data_where = array('box_id' => $key);
                                        $wpdb->update($loc_status_boxinfo_table_name , $loc_status_boxinfo_data_update, $loc_status_boxinfo_data_where);
                    
                    
                    $scan_table_name = 'wpqa_wpsc_epa_scan_list';
                    $wpdb->insert($scan_table_name, array(
                                    'box_id' => esc_sql($key),
                                    $column_name => esc_sql(strtoupper($value)),
                                    'date_modified' => $date,
                                ));
    
                        $message = "Updated: Box ID " . $key . " with the following Cart ID: " . strtoupper($value) . "\n\n";
                        do_action('wpppatt_after_shelf_location', $ticket_id, $key, $message);
                        echo $message; 
                    $GLOBALS[$record_updated] = true;  
                    $GLOBALS[$evaluated] = true;
                }else{
                    $GLOBALS[$record_updated] = false; 
                    $GLOBALS[$evaluated] = true;     
                }
                
                if(preg_match('/^\d{1,3}A_\d{1,3}B_\d{1,3}S_\d{1,3}P_(E|W|ECUI|WCUI)$/i', $value)) {
                    
                    /* Restrict physical location scanning assignments to one box */
                    if($count == 1){
                    
                        $column_name = 'shelf_location';
                        $position_array = explode('_', $value);
        
                        $aisle = substr($position_array[0], 0, -1);
                        $bay = substr($position_array[1], 0, -1);
                        $shelf = substr($position_array[2], 0, -1);
                        $position = substr($position_array[3], 0, -1);
                        $dc = $position_array[4];
                        $center_term_id = term_exists($dc);
                        $new_term_object = get_term( $center_term_id );
                        $new_position_id_storage_location = $aisle.'A_'.$bay.'B_'.$shelf.'S_'.$position.'P_'.strtoupper($dc); 
                        $new_A_B_S_only_storage_location = $aisle.'_'.$bay.'_'.$shelf;
        
                        /* Add logic to determine if a location is in the facility. */
        
        			    $storage_location_details = $wpdb->get_row(
        			                                                "SELECT shelf_id 
        			                                                FROM wpqa_wpsc_epa_storage_status
                                                                    WHERE shelf_id = '" . esc_sql($new_A_B_S_only_storage_location) . "'"
                                        			              );
                                        			              
            			$facility_shelfid = $storage_location_details->shelf_id;


            			if($facility_shelfid == $new_A_B_S_only_storage_location){
            		        
            		        $box_id_new_scan = $key;
            		         
            		        /* Determine if the position is occupied */
            		        $existing_boxinfo_details = $wpdb->get_row(
            			                                                "SELECT b.aisle as aisle,b.bay as bay,b.shelf as shelf,b.position as position,b.digitization_center as dc
                                                                        FROM wpqa_wpsc_epa_boxinfo a                
                                                                        LEFT JOIN wpqa_wpsc_epa_storage_location b ON a.storage_location_id = b.id
                                                                        WHERE a.box_id = '" . esc_sql($box_id_new_scan) . "'
                                        			                ");
                                  			              
            		        $existing_boxinfo_aisle = $existing_boxinfo_details->aisle;
            			    $existing_boxinfo_bay = $existing_boxinfo_details->bay;
            			    $existing_boxinfo_shelf = $existing_boxinfo_details->shelf;
            			    $existing_boxinfo_position = $existing_boxinfo_details->position;
            			    $term_object = get_term( $existing_boxinfo_details->dc);
            			    $existing_boxinfo_dc =  $term_object->slug;
            		        $existing_boxinfo_position_id_storage_location = $existing_boxinfo_aisle.'A_'.$existing_boxinfo_bay.'B_'.$existing_boxinfo_shelf.'S_'.$existing_boxinfo_position.'P_'.strtoupper($existing_boxinfo_dc);

            		   		if($existing_boxinfo_position_id_storage_location != "0A_0B_0S_0P_NOT-ASSIGNED" ){
            		   		    
            		   		    /* If the proposed storage position scanned on the shelf matches the initial auto=selected/manual location storage position (assigned) */
                                if($new_position_id_storage_location == $existing_boxinfo_position_id_storage_location){
                                        
                                        /* Change the box status has been changed from "on cart" to "on shelf" */
                                        $location_statuses = $wpdb->get_row(
            			                                                "SELECT id as id,locations as locations
                                                                        FROM wpqa_wpsc_epa_location_status                
                                                                        WHERE locations = 'On Shelf'
                                        			                ");
                                        			                
                                        $location_statuses_id = $location_statuses->id;
            			                $location_statuses_locations = $location_statuses->locations;
                                        
                                        $loc_status_boxinfo_table_name = 'wpqa_wpsc_epa_boxinfo';
                                        $loc_status_boxinfo_data_update = array('location_status_id' => $location_statuses_id);
                                        $loc_status_boxinfo_data_where = array('box_id' => $key);
                                        $wpdb->update($loc_status_boxinfo_table_name , $loc_status_boxinfo_data_update, $loc_status_boxinfo_data_where);
                                        
                    				    /* Update the scanning table */
                                        $scan_table_name = 'wpqa_wpsc_epa_scan_list';
                                        $wpdb->insert($scan_table_name, array(
                                                        'box_id' => esc_sql($key),
                                                        $column_name => esc_sql(strtoupper($value)),
                                                        'date_modified' => $date,
                                                     ));
                                        
                                        /* Notify the user that the box status has been changed from "on cart" to "on shelf" */
                                        $message = "Updated: Box ID " . $key . " has been placed " . $location_statuses_locations . " " . $new_position_id_storage_location .".\n\n";
                                        do_action('wpppatt_after_shelf_location', $ticket_id, $key, $message);
                                        echo $message; 
                                        $GLOBALS[$record_updated] = true; 
                                        $GLOBALS[$evaluated] = true;        
                                        
                                        
                                } else {
                        		    
                        			$message = "Not Updated: The scanned location ". $new_position_id_storage_location . " does not match the assigned shelf location for the box. Please select another location and try again.\n\n";
                                    echo $message;    
                                    $GLOBALS[$record_updated] = false; 
                                    $GLOBALS[$evaluated] = true;     

                                }
            		   		}else{
            		   		    
               		   		    $message = "Not Updated: The location ". $existing_boxinfo_position_id_storage_location ." is already assigned. Please select another location and try again.\n\n";
                                echo $message;               
                                $GLOBALS[$record_updated] = false; 
                                $GLOBALS[$evaluated] = true;     

            		   		}
        				}else{
        				    $message = "Not Updated: The location ". $new_position_id_storage_location . " does not exist in the facility. Please select another location and try again.\n\n";
                            echo $message;  
                            $GLOBALS[$record_updated] = false; 
                            $GLOBALS[$evaluated] = true;     

            			}	
                    }else{
                        $message = "Not Updated. The Location Scan cannot be assigned to multiple Box ID's.\n\n";
                        echo $message; 
                        $GLOBALS[$record_updated] = false; 
                        $GLOBALS[$evaluated] = true;     

                    }
                }
            }
        }
        if($GLOBALS[$record_updated] == false && $GLOBALS[$evaluated] == true){
                    $message = "Not Updated: The location value is invalid.\n\n";
                    echo $message; 
            
        }
        
        function recall_return_status_check($column_name, $boxid, $recall_return_id){
        
        $current_status = false;
        
        // TODO - Check Status    
        //  1. Pass foreign key value of the box_id that links to the return_items and the recall_list tables
        //  .....box_id_fk_ri
        
            $pk_fk_box_id = $wpdb->get_row("SELECT box_id 
                                            FROM information_schema.KEY_COLUMN_USAGE 
                                            WHERE TABLE_NAME = wpqa_wpsc_epa_recallrequest 
                                            AND CONSTRAINT_NAME = 'PRIMARY'
                                            ");
        
            echo $pk_fk_box_id;
        
        //  2. The function must return the status (explained below) of the recall/return ID associated with the box ID
            if($column_name == "return_id"){
                echo $column_name;
                
                
            }
            if($column_name == "recall_id"){
                echo $column_name;
                
                
            }
        
        //  3. Recall - epa_recall request status check
        //      --I have the full box id....
        //      --If a folder doc will always be -9999, means a whole box is in recall
        //      --If folder or document it will have a boxid and a folder doc id(is a number instead of -99999) then a file or folder is a recall request for that box


            //Function to obtain box ID from database based on ID		
            //get_box_id_by_id($id);
    	
            //Function to obtain folderdoc ID from database based on ID		
            //get_folderdoc_id_by_id($id);
            
            return $current_status;
        }
        
}
    
?>