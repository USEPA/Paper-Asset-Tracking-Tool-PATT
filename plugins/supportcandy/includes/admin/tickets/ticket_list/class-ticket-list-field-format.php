<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


if ( ! class_exists( 'WPSC_Ticket_List_Field' ) ) :
    
    class WPSC_Ticket_List_Field {
        
        var $list_item;
        var $ticket;
        var $val;
        var $type;
				
        function print_field($list_item,$ticket){
          
					global $wpscfunction;
					$this->list_item = $list_item;
					$this->ticket    = $ticket;
					$get_all_meta_keys = $wpscfunction->get_all_meta_keys();
					if ($list_item->slug == 'ticket_id') {
							$list_item->slug ='id';
					}
					if(in_array($list_item->slug, $get_all_meta_keys)){
						$this->val = $wpscfunction->get_ticket_meta( $ticket['id'], $list_item->slug, true);
					}
					else {
						$this->val = $wpscfunction->get_ticket_fields( $ticket['id'], $list_item->slug);
					}
          $this->type      = get_term_meta( $list_item->term_id, 'wpsc_tf_type', true);
			
          if ( $this->type == '0' ) {
            switch ($list_item->slug) {
              
              case 'id':
                  
                 //PATT BEGIN
                $padded_request_id = Patt_Custom_Func::$ticket['id'];
                echo $padded_request_id;
                break;
                //PATT END
              case 'customer_name':
              case 'customer_email':
              case 'ticket_subject':
								$this->print_meta_value();
								break;
                
              case 'ticket_status':
                $this->print_ticket_status();
                break;
                  
              case 'ticket_category':
                $this->print_ticket_category();
                break;
								
							case 'ticket_priority':
                $this->print_ticket_priority();
                break;
                
              case 'assigned_agent':
              case 'agent_created':
                $this->print_agent_names();
                break;
                
              case 'date_created':
								$this->print_local_date();
								break;
								
              case 'date_updated':
							case 'date_closed':
                $this->print_current_diff_date();
                break;
							
							case 'user_type' :
								$this->print_user_type();
								break;
	
              default:
                do_action('wpsc_print_default_tl_field', $this);
                break;
            }
						
          } else {
						
						switch ($this->type) {
							
							case '1':
              case '2':
              case '4':
              case '7':
              case '8':
              case '9':
								$this->print_meta_value();
								break;
                
							case '3':
								$this->print_checkbox();
								break;
								
							case '6':
								$this->print_date();
								break;
								
							case '18':
                $this->print_date_time();
				break;
				
				case '21':
					$this->print_time($list_item);
					break;		
							default:
								do_action('wpsc_print_custom_tl_field', $this);
								break;
                
						}
						
					}
          
        }
        
				function print_meta_value(){
          echo stripcslashes($this->val);
        }
        
        function print_ticket_status(){
          $status           = get_term_by('id',$this->val,'wpsc_statuses');
          $color            = get_term_meta($status->term_id,'wpsc_status_color',true);
          $background_color = get_term_meta($status->term_id,'wpsc_status_background_color',true);
					$wpsc_custom_status_localize   = get_option('wpsc_custom_status_localize');
					?>
          <span class="wpsp_admin_label" style="background-color:<?php echo $background_color?>;color:<?php echo $color?>;"><?php echo $wpsc_custom_status_localize['custom_status_'.$this->val]?></span>
          <?php
        }
        
        //PATT BEGIN
        function print_ticket_category(){
          $category = get_term_by('id',$this->val,'wpsc_categories');
		  $wpsc_custom_category_localize = get_option('wpsc_custom_category_localize');
          //echo $wpsc_custom_category_localize['custom_category_'.$this->val];
          
          do_action('pattracking_print_ticket_category', $this); // PATT BEGIN - Location Filtering - PATT END

        }
        //PATT END
        
        function print_ticket_category(){
          $category = get_term_by('id',$this->val,'wpsc_categories');
					$wpsc_custom_category_localize = get_option('wpsc_custom_category_localize');
          echo $wpsc_custom_category_localize['custom_category_'.$this->val];
        }
        
        function print_ticket_priority(){
          $priority         = get_term_by('id',$this->val,'wpsc_priorities');
          $color            = get_term_meta($priority->term_id,'wpsc_priority_color',true);
          $background_color = get_term_meta($priority->term_id,'wpsc_priority_background_color',true);
					$wpsc_custom_priority_localize = get_option('wpsc_custom_priority_localize');
          ?>
          <span class="wpsp_admin_label" style="background-color:<?php echo $background_color?>;color:<?php echo $color?>;"><?php echo $wpsc_custom_priority_localize['custom_priority_'.$this->val]?></span>
          <?php
        }
        
        function print_checkbox(){
          global $wpscfunction;
					$val =  $wpscfunction->get_ticket_meta($this->ticket['id'], $this->list_item->slug);
					if($val){
						$val= implode(', ', $val);
						echo htmlentities($val);
					}
        }
				
				function print_local_date(){
					global $wpscfunction;
					$wpsc_thread_date_format = get_option('wpsc_thread_date_format');
					if($wpsc_thread_date_format == 'timestamp'){
						echo $wpscfunction->time_elapsed_timestamp($this->val);
					}else{
						echo $wpscfunction->time_elapsed_string($this->val);
					}
					//echo get_date_from_gmt($this->val);
        }
        
        function print_date(){
					global $wpscfunction;					
          echo $wpscfunction->datetimeToCalenderFormat($this->val);
        }
        
        function print_current_diff_date(){
					global $wpscfunction;
					$wpsc_thread_date_format = get_option('wpsc_thread_date_format');
					if($wpsc_thread_date_format == 'timestamp'){
						echo $wpscfunction->time_elapsed_timestamp($this->val);
					}else{
						echo $wpscfunction->time_elapsed_string($this->val);
					}
        }
        
        function print_agent_names(){
					global $wpscfunction;
					$this->val = $wpscfunction->get_ticket_meta($this->ticket['id'], $this->list_item->slug);
					$arr = array();
					foreach ($this->val as $key => $value) {
						if($value){
							$agent = get_term_by('id',$value,'wpsc_agents');
							if ($agent) {
								$arr[] = get_term_meta($agent->term_id,'label',true);
							}
						} else {
							$arr[] = __('None','supportcandy');
						}
					}
					echo implode(', ', $arr);
				}
				
				function print_date_time(){
					if($this->val!=''){
						echo $this->val;
					}
				}
				
				function print_user_type(){
					if ($this->val == "user") {
						echo __("User", 'supportcandy');
					} elseif ($this->val == "guest") {
						echo __("Guest", 'supportcandy');
					}
				}

				function print_time($list_item){
					$time_format = get_term_meta($list_item->term_id,'wpsc_time_format',true);

					if($this->val!=''){
						if($time_format == '12'){
							echo date("h:i:s a", strtotime($this->val));

						}else{
							echo $this->val;
						}
						
					}
				}

		}
    
endif;