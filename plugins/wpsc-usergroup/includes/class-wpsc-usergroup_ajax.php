<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WPSC_Usergroup_Ajax' ) ) :
    
    /**
     * Ajax class for WPSC.
     * @class WPSC_Usergroup_Ajax
     */
    class WPSC_Usergroup_Ajax {
        
        /**
         * Constructor
         */
        public function __construct(){
            
						$ajax_events = array(
							'get_company_usergroup_settings' => false,
							'set_add_new_company_settings' => false,
							'search_users_for_company'     => false,
							'set_update_company_settings'  => false,
							'delete_company'               => false,
							'filter_user_autocomplete'          => false,
							'edit_company'                 => false,
							'add_new_company_settings'     => true,
						);
            
            foreach ($ajax_events as $ajax_event => $nopriv) {
								add_action('wp_ajax_wpsc_' . $ajax_event, array($this, $ajax_event));
                if ($nopriv) {
                    add_action('wp_ajax_nopriv_wpsc_' . $ajax_event, array($this, $ajax_event));
                }
            }
        }
        
        /**
         * Tickets ajax
         */
				 public function get_company_usergroup_settings(){
             include WPSC_USERGROUP_ABSPATH . 'includes/admin/ajax/get_company_usergroup_settings.php';
             die();
         }
				 
        public function set_add_new_company_settings(){
            include WPSC_USERGROUP_ABSPATH . 'includes/admin/ajax/set_add_new_company_settings.php';
            die();
        }
        
        function search_users_for_company(){
          include WPSC_USERGROUP_ABSPATH . 'includes/admin/ajax/search_users_for_company.php';
          die();
        }
        
        function set_update_company_settings(){
          include WPSC_USERGROUP_ABSPATH . 'includes/admin/ajax/set_update_company_settings.php';
          die();
        }
        
        function delete_company(){
          include WPSC_USERGROUP_ABSPATH . 'includes/admin/ajax/delete_company.php';
          die();
        }
        
        function filter_user_autocomplete(){
          include WPSC_USERGROUP_ABSPATH . 'includes/admin/ajax/filter_autocomplete.php';
          die();
        }
        
        function edit_company(){
          include WPSC_USERGROUP_ABSPATH . 'includes/admin/ajax/edit_company.php';
          die();
        }
        
        function add_new_company_settings(){
          include WPSC_USERGROUP_ABSPATH . 'includes/admin/ajax/add_new_company_settings.php';
          die();
        }
        
    }
    
endif;

new WPSC_Usergroup_Ajax();