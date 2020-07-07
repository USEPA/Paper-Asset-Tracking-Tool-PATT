<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WPSC_SLA_Install' ) ) :
  
  final class WPSC_SLA_Install {
    
    public function __construct() {
      add_action( 'init', array($this,'register_post_type'), 100 );
      $this->check_version();
    }
    
    // Register post types and texonomies
    public function register_post_type(){
      
      // Register sla texonomy
			$args = array(
          'public'             => false,
          'rewrite'            => false
      );
      register_taxonomy( 'wpsc_sla', 'wpsc_ticket', $args );
      
    }
		
		/**
     * Check version of WPSC
     */
    public function check_version(){
        $installed_version = get_option( 'wpsc_sla_current_version', 0 );
        if( $installed_version != WPSC_SLA_VERSION ){
						$this->create_db_tables();
            add_action( 'init', array($this,'upgrade'), 101 );
        }

    }
    
		/**
		 * Create mysql table
		 * @return [type] [description]
		 */
		
		 public function create_db_tables() {
 			
			global $wpdb;

 			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

 			$collate = 'CHARACTER SET utf8 COLLATE utf8_general_ci';
 			
			$sla_table = "CREATE TABLE {$wpdb->prefix}wpsc_sla_reports (
 				id int(11) NOT NULL AUTO_INCREMENT,
 				overdue_count int(11) NULL DEFAULT NULL, 
 				result_date DATETIME DEFAULT NULL,
 				PRIMARY KEY  (id)
 			) $collate;" ;
 			
 				dbDelta( $sla_table );
 		}
		
    // Upgrade
		public function upgrade(){
				
        $installed_version = get_option( 'wpsc_sla_current_version', 0 );
				$installed_version = $installed_version ? $installed_version : 0;
				
				if ( $installed_version < '1.0.0' ) {
          
          $term = wp_insert_term( 'sla', 'wpsc_ticket_custom_fields' );
          if ( !is_wp_error($term) && isset($term['term_id'])) {
						add_term_meta ($term['term_id'], 'wpsc_tf_label', __('SLA','wpsc-sla'));
						add_term_meta ($term['term_id'], 'agentonly', '2');
						add_term_meta ($term['term_id'], 'wpsc_tf_type', '0');
						add_term_meta ($term['term_id'], 'wpsc_allow_ticket_list', '1');
						add_term_meta ($term['term_id'], 'wpsc_customer_ticket_list_status', '0');
						add_term_meta ($term['term_id'], 'wpsc_agent_ticket_list_status', '0');
						add_term_meta ($term['term_id'], 'wpsc_allow_ticket_filter', '0');
						add_term_meta ($term['term_id'], 'wpsc_ticket_filter_type', 'string');
						add_term_meta ($term['term_id'], 'wpsc_allow_orderby', '1');
					}
          
          update_option('wpsc_in_sla_color', '#5cb85c');
					update_option('wpsc_out_sla_color','#d9534f');
			  }
				
				if ( $installed_version < '2.0.1' ) {
					$sla_policies = get_terms([
						'taxonomy'   => 'wpsc_sla',
						'hide_empty' => false,
						'orderby'    => 'meta_value_num',
						'order'    	 => 'ASC',
						'meta_query' => array('order_clause' => array('key' => 'load_order')),
					]);
					
					foreach ( $sla_policies as $sla ) {
							
								$conditions     = get_term_meta( $sla->term_id, 'conditions', true );
								$new_conditions = array();
								if($conditions){
									
									foreach ( $conditions as $key => $condition ) {
										
											foreach ($condition as $value) {
													
													$new_conditions[] = array(
															'field'    => $key,
															'compare'  => 'match',
															'cond_val' => $value,
													);
													
											}
										
									}
									
								}
								$new_conditions = $new_conditions ? json_encode($new_conditions) : '';
								update_term_meta( $sla->term_id ,'conditions' , $new_conditions);
							
					}	
				}
				if( $installed_version < '2.0.2'){
					
					$sla_data = get_term_by('slug' ,'sla','wpsc_ticket_custom_fields');
					if ($sla_data) {
						update_term_meta ($sla_data->term_id, 'wpsc_allow_ticket_filter', '1');
						update_term_meta ($sla_data->term_id, 'wpsc_ticket_filter_type', 'string');
						update_term_meta ($sla_data->term_id, 'wpsc_customer_ticket_filter_status', '0');
						update_term_meta ($sla_data->term_id, 'wpsc_agent_ticket_filter_status', '0');
						update_term_meta ($sla_data->term_id, 'wpsc_filter_customer_load_order', '6');
						update_term_meta ($sla_data->term_id, 'wpsc_filter_agent_load_order', '6');
					}
				}
        update_option( 'wpsc_sla_current_version', WPSC_SLA_VERSION );
        
    }
    
  }
  
endif;

new WPSC_SLA_Install();
