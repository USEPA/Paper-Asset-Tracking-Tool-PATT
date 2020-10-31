<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WPSC_Ajax' ) ) :
    
    /**
     * Ajax class for WPSC.
     * @class WPSC_Ajax
     */
    class WPSC_Ajax {
        
        /**
         * Constructor
         */
        public function __construct(){
            
						$ajax_events = array(
							'frontend'               => true,
							'settings'               => false,
							'custom_fields'          => false,
							'ticket_list'            => false,
							'email_notifications'    => false,
							'support_agents'         => false,
							'tickets'                => true,
							'appearance_settings'    => false,
							'run_db_v2_upgrade' => false,
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
        public function tickets(){
            include WPSC_ABSPATH . 'includes/admin/tickets/tickets_ajax.php';
            die();
        }
        
        /**
         * Settings Ajax query
         */
        public function settings(){
            include WPSC_ABSPATH . 'includes/admin/settings/settings_ajax.php';
            die();
        }
				
				/**
         * Custom Fields Ajax query
         */
        public function custom_fields(){
            include WPSC_ABSPATH . 'includes/admin/custom_fields/custom_fields_ajax.php';
            die();
        }
				
				/**
         * Ticket List Ajax query
         */
        public function ticket_list(){
            include WPSC_ABSPATH . 'includes/admin/ticket_list/ticket_list_ajax.php';
            die();
        }
				
				/**
         * Email Notifications Ajax query
         */
        public function email_notifications(){
            include WPSC_ABSPATH . 'includes/admin/email_notifications/email_notifications_ajax.php';
            die();
        }
				
				/**
         * Support Agents Ajax query
         */
        public function support_agents(){
            include WPSC_ABSPATH . 'includes/admin/support_agents/support_agents_ajax.php';
            die();
        }
				
				/**
         * Appearance Ajax query
         */
				public function appearance_settings() {
					include WPSC_ABSPATH . 'includes/admin/appearance_settings/appearance_ajax.php';
					die();
				}
				
				/**
				 * Database v2 upgrade process
				 */
				public function run_db_v2_upgrade(){
					include WPSC_ABSPATH . 'includes/admin/db_upgrade/run_db_v2_upgrade.php';
					die();
				}
				
		}
    
endif;

new WPSC_Ajax();