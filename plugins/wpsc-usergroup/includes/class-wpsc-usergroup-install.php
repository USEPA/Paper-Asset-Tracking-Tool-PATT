<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WPSC_Usergroup_Install' ) ) :
  
  final class WPSC_Usergroup_Install {
    
    public function __construct() {
      add_action( 'init', array($this,'register_post_type'), 100 );
			$this->check_version();
    }
    
    // Register texonomies
    public function register_post_type(){
      
			// Register status taxonomy
			$args = array(
          'public'             => false,
          'rewrite'            => false
      );
      register_taxonomy( 'wpsc_usergroup_data', 'wpsc_ticket', $args );
		}
		/**
		 * Check version of WPSC
		 */
		public function check_version(){
			
				$installed_version = get_option( 'wpsc_usergroup_version' );
				if( $installed_version != WPSC_USERGROUP_VERSION ){
					add_action( 'init', array($this,'upgrade'), 101 );
				}
		}
		//upgrade
		public function upgrade(){
				
				$installed_version = get_option( 'wpsc_usergroup_version' );
				$installed_version = $installed_version ? $installed_version : 0;
				
				if ($installed_version < '2.0.2') {
					
					$term = wp_insert_term( 'usergroup', 'wpsc_ticket_custom_fields' );
					if (!is_wp_error($term) && isset($term['term_id'])) {
						add_term_meta ($term['term_id'], 'wpsc_tf_label', __('Usergroup','wpsc-usergroup'));
						add_term_meta ($term['term_id'], 'agentonly', '2');
						add_term_meta ($term['term_id'], 'wpsc_tf_type', '0');
						add_term_meta ($term['term_id'], 'wpsc_allow_ticket_list', '1');
						add_term_meta ($term['term_id'], 'wpsc_customer_ticket_list_status', '0');
						add_term_meta ($term['term_id'], 'wpsc_agent_ticket_list_status', '0');
						add_term_meta ($term['term_id'], 'wpsc_allow_ticket_filter', '1');
						add_term_meta ($term['term_id'], 'wpsc_ticket_filter_type', 'string');
						add_term_meta ($term['term_id'], 'wpsc_customer_ticket_filter_status', '0');
						add_term_meta ($term['term_id'], 'wpsc_agent_ticket_filter_status', '0');
					}
				}

				if($installed_version < '2.0.4'){
                	update_option('wpsc_allow_usergroup_change_category','1');
				}

				if($installed_version < '2.0.5' ){
					global $wpdb;
					$user_group_term = get_term_by('slug','usergroup','wpsc_ticket_custom_fields');
					$load_order = $wpdb->get_var("select max(meta_value) as load_order from {$wpdb->prefix}termmeta WHERE meta_key='wpsc_tf_load_order'");
					update_term_meta ($user_group_term->term_id, 'wpsc_allow_export_ticket_list', '1');
					update_term_meta ($user_group_term->term_id,'wpsc_export_ticket_list_order',++$load_order);
				}


				update_option( 'wpsc_usergroup_version', WPSC_USERGROUP_VERSION );
			}
		}
		
endif;

new WPSC_Usergroup_Install();