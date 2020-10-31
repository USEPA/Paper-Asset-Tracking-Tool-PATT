<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WPSC_EXP_Install' ) ) :
	final class WPSC_EXP_Install {
		
		public function __construct() {
				$this->check_version();
		}
    
		public function check_version(){
		$installed_version = get_option( 'wpsc_export_current_version',0);
		if( $installed_version != WPSC_EXP_VERSION ){						
			add_action( 'init', array($this,'upgrade'), 101 );
		}
	}
    
	// Upgrade
	public function upgrade(){
				
		$installed_version = get_option( 'wpsc_export_current_version', 0 );
		$installed_version = $installed_version ? $installed_version : 0;
		
		if( $installed_version < '1.0.5' ){
			
			$ticket_list_items = get_terms([
				'taxonomy'   => 'wpsc_ticket_custom_fields',
				'hide_empty' => false,
				'meta_query' => array(
					'relation' => 'AND',
					array(
						'key'     => 'wpsc_allow_ticket_list',
						'value'   => '1',
						'compare' => '=',
					),
				),
			]);
			foreach ($ticket_list_items as $key=>$val) {
				$export_column[]=($key = $val->slug);
			}
			update_option('wpsc_export_ticket_list',$export_column);
		 	update_option('wpsc_select_all_option_value','1');
		}
		
		if($installed_version < '2.0.2'){
			
			$ticket_list_items = get_terms([
				'taxonomy'   => 'wpsc_ticket_custom_fields',
				'hide_empty' => false,
				'meta_query' => array(
					'relation' => 'AND',
					array(
						'key'     => 'wpsc_allow_ticket_list',
						'value'   => '1',
						'compare' => '=',
					),
				),
			]);
			
			foreach ($ticket_list_items as $key => $term) {
				delete_term_meta($term->term_id, 'wpsc_export_ticket_list_order');
			}
			
			$wpsc_export_ticket_list = get_option('wpsc_export_ticket_list');
			$load_order = 0;
			foreach ($ticket_list_items as $term) {
				add_term_meta ($term->term_id, 'wpsc_export_ticket_list_order', ++$load_order);
			}
		}
		update_option( 'wpsc_export_current_version', WPSC_EXP_VERSION );
	}
}
endif;
new WPSC_EXP_Install();