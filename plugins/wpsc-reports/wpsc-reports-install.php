<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WPSC_Report_Install' ) ) :
  
  final class WPSC_Report_Install {
    
    public function __construct() {
			$this->check_version();
    }
    
    /**
     * Check version of Timer
     */
    public function check_version(){
			$installed_version = get_option( 'wpsc_rp_current_version');
			if( $installed_version != WPSC_RP_VERSION ){
				$this->create_db_tables();
				add_action( 'init', array($this,'upgrade'), 101 );
        	}
    }
    
		/**
		 * Create mysql tables
		 */
		public function create_db_tables() {
			
			global $wpdb;

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

			$collate = 'CHARACTER SET utf8 COLLATE utf8_general_ci';
			
			$tables = "CREATE TABLE {$wpdb->prefix}wpsc_reports (
				id int(11) NOT NULL AUTO_INCREMENT,
				report_type VARCHAR(100) DEFAULT NULL,
				type VARCHAR(100) DEFAULT NULL,
				ticket_count int(11) NULL DEFAULT NULL, 
				result_date DATETIME DEFAULT NULL,
				PRIMARY KEY  (id)
			) $collate; ";
			
			dbDelta( $tables );
		}
		
    // Upgrade
		public function upgrade(){
      			$installed_version = get_option( 'wpsc_rp_current_version');
				$installed_version = $installed_version ? $installed_version : 0;

				if ( $installed_version < '2.0.1' ) {
					$fields = get_terms([
						'taxonomy'   => 'wpsc_ticket_custom_fields',
						'hide_empty' => false,
						'orderby'    => 'meta_value_num',
						'meta_key'	 => 'wpsc_tf_load_order',
						'order'    	 => 'ASC',
						'meta_query' => array(
						'relation' => 'AND',
						array(
						  'key'       => 'agentonly',
						  'value'     => array(0,1),
						  'compare'   => 'IN'
						),
					  )
					]);
					
					$dash_widgets = array();
					
					foreach ($fields as $key => $field) {
						$cust_field =  get_term_by('id', $field->term_id, 'wpsc_ticket_custom_fields');
						$wpsc_tf_type = get_term_meta($field->term_id ,'wpsc_tf_type',true);
						if($wpsc_tf_type == 2 || $wpsc_tf_type == 3 || $wpsc_tf_type == 4 ||  $cust_field->slug =='ticket_category' ) :
							$dash_widgets[] = $field->term_id;
						endif;
					}
					
					update_option('wpsc_report_dash_widgets',$dash_widgets);
					
					update_option('wpsc_dashboard_report_filters','last7days');
				}
				
      	update_option( 'wpsc_rp_current_version', WPSC_RP_VERSION );	  
    }
}
endif; 

new WPSC_Report_Install ;
