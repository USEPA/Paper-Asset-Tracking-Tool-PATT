<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$conditions = $conditions ? json_decode($conditions) : array();
$meta_keys  = $this->get_all_meta_keys();
$flag       = true;

/**
 * Conditions of same fields sort
 */
$unique_conditions = array();

foreach ($conditions as $condition){
	
		$unique_conditions[$condition->field][] = $condition;
	
}

foreach ( $unique_conditions as $field_key => $unique_condition ){
	
		$inner_flag = false;
		
		if ( is_numeric($field_key) ){
			
				foreach ( $unique_condition as $condition) {
					
						$custom_field      = get_term_by('id', $field_key, 'wpsc_ticket_custom_fields');
						$custom_field_type = get_term_meta( $custom_field->term_id, 'wpsc_tf_type', true);

						if($custom_field_type=='0'){
							
								switch ( $custom_field->slug ) {
									
										case 'ticket_category':
										case 'ticket_priority':
										case 'ticket_status':
										case 'customer_name':
										case 'customer_email':
										case 'ticket_subject':
										
												$field_val = $this->get_ticket_fields( $ticket_id, $custom_field->slug );
												if( $condition->compare == 'match' ) {
													$inner_flag = $condition->cond_val == $field_val ? true : false;
												} elseif( $condition->compare == 'not_match' ) {
													 $inner_flag = $condition->cond_val != $field_val ? true : false;
												}else {
													if(strpos($condition->cond_val , '*')){
														if(fnmatch($condition->cond_val,$field_val)){
															$inner_flag = true;	
														}	
													}else{
														$inner_flag = strpos( $field_val, $condition->cond_val ) !== false ? true : false;	
													}
												}
												break;
												
										case 'assigned_agent' :
										
												$field_val  = $this->get_ticket_meta( $ticket_id, $custom_field->slug );
												if( $condition->compare == 'match' ) {
													$inner_flag = in_array( $condition->cond_val, $field_val ) ? true : false;
												} elseif( $condition->compare == 'not_match' ) {
													$inner_flag = (!in_array( $condition->cond_val, $field_val )) ? true : false;
												}
												break;
												
										case 'ticket_description':
										
												$ticket_description = $this->get_ticket_description( $ticket_id );
												if( $condition->compare == 'match' ) {
													$inner_flag = $condition->cond_val == strip_tags($ticket_description['description']) ? true : false;
												}elseif( $condition->compare == 'not_match' ) {
													$inner_flag = $condition->cond_val != strip_tags($ticket_description['description']) ? true : false;
												}else {
													$inner_flag = strpos( $ticket_description['description'], $condition->cond_val ) !== false ? true : false;
												}
												break;
									
								}
							
						} else {
							
								switch($custom_field_type){
										
											case 1 :
											case 2 :
											case 4 :
											case 5 :
											case 7 :
											case 8 :
											case 9 :
											
													$field_val  = $this->get_ticket_meta( $ticket_id, $custom_field->slug, true );
													if( $condition->compare == 'match' ) {
														$inner_flag = $condition->cond_val == $field_val ? true : false;
													}elseif( $condition->compare == 'not_match' ) {
														$inner_flag = $condition->cond_val != $field_val ? true : false;
													}else {
														if(strpos($condition->cond_val , '*')){
															if(fnmatch($condition->cond_val,$field_val)){
																$inner_flag = true;	
															}	
														}else{
															$inner_flag = strpos( $field_val, $condition->cond_val ) !== false ? true : false;	
														}
													}
													break;
												
											case 3:
											
													$field_val  = $this->get_ticket_meta( $ticket_id, $custom_field->slug );
													if( $condition->compare == 'match' ) {
														$inner_flag = in_array( $condition->cond_val, $field_val ) ? true : false;
													} elseif( $condition->compare == 'not_match' ) {
														$inner_flag = (!in_array( $condition->cond_val, $field_val )) ? true : false;
													}

													break;
												
											default:
												$inner_flag = apply_filters( 'wpsc_check_ticket_conditions_custom_field_type', $inner_flag, $ticket_id, $condition, $custom_field_type , $custom_field );
									
								}
								
						}
						
						if( $inner_flag ) break;
					
				}
			
		} else {
			
				if( $field_key == 'agent_created' ){
					
						$agent_created = $this->get_ticket_fields( $ticket_id, 'agent_created' );
						foreach ( $unique_condition as $condition) {
							if( $condition->compare == 'match' ) {
								$inner_flag = ( $condition->cond_val == 'agent' && $agent_created > 0 ) || ( $condition->cond_val == 'user' && $agent_created == 0 ) ? true : false;
							}elseif( $condition->compare == 'not_match' ) {
								$inner_flag = ( $condition->cond_val == 'agent' && $agent_created == 0 ) || ( $condition->cond_val == 'user' && $agent_created > 0 ) ? true : false;
							}
							if( $inner_flag ) break;
						}
					
				}
				
				if( $field_key == 'user_type' ){
					
					$user_type = $this->get_ticket_fields( $ticket_id, 'user_type' );
					foreach ( $unique_condition as $condition) {
						if( $condition->compare == 'match' ) {
							$inner_flag = ( $condition->cond_val == 'user' && $user_type == 'user' ) || ( $condition->cond_val == 'guest' && $user_type == 'guest' ) ? true : false;
						}elseif( $condition->compare == 'not_match' ) {
							$inner_flag = ( $condition->cond_val == 'user' && $user_type == 'guest' ) || ( $condition->cond_val == 'guest' && $user_type == 'user' ) ? true : false;
						}
						if( $inner_flag ) break;
					}
						
				}
				
				$inner_flag = apply_filters( 'wpsc_check_custom_ticket_condition', $inner_flag, $ticket_id, $unique_condition );
			
		}
		
		if( !$inner_flag ){
			
				$flag = false;
				break;
			
		}
	
}
