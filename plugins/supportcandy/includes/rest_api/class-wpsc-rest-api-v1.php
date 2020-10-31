<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

if ( ! class_exists( 'WPSC_Rest_API_v1' ) ) :
  
    final class WPSC_Rest_API_v1 {
      
        /**
         * Register hooks and filters
         */
        public static function init(){
          
            add_action( 'rest_api_init', array( __CLASS__, 'api_register' ) );
            add_filter( 'determine_current_user',	array( __CLASS__, 'determine_current_user' ) );
          
        }
        
        /**
         * APIs registration v1
         */
        public static function api_register() {
      			
            register_rest_route( 'supportcandy/v1', '/login', array(
      				'callback' => array( 'WPSC_Rest_v1_Helper', 'user_login' ),
              'permission_callback' => array( 'WPSC_Rest_v1_Helper', 'validate_secret_key' ),
      				'methods' => 'POST'
      			));
            
            register_rest_route( 'supportcandy/v1', '/guestTicketFormFields', array(
      				'callback' => array( 'WPSC_Rest_v1_Helper', 'get_ticket_form_fields' ),
              'permission_callback' => array( 'WPSC_Rest_v1_Helper', 'validate_guest_user' ),
      				'methods' => 'POST'
      			));
            
            register_rest_route( 'supportcandy/v1', '/ticketFormFields', array(
      				'callback' => array( 'WPSC_Rest_v1_Helper', 'get_ticket_form_fields' ),
              'permission_callback' => array( 'WPSC_Rest_v1_Helper', 'validate_registered_user' ),
      				'methods' => 'POST'
      			));
            
            register_rest_route( 'supportcandy/v1', '/agentonlyFields', array(
      				'callback' => array( 'WPSC_Rest_v1_Helper', 'get_agentonly_fields' ),
              'permission_callback' => array( 'WPSC_Rest_v1_Helper', 'validate_agent' ),
      				'methods' => 'POST'
      			));
            
            register_rest_route( 'supportcandy/v1', '/statuses', array(
      				'callback' => array( 'WPSC_Rest_v1_Helper', 'get_status_list' ),
      				'methods' => 'GET'
      			));
            
            register_rest_route( 'supportcandy/v1', '/statuses/(?P<id>\d+)', array(
      				'callback' => array( 'WPSC_Rest_v1_Helper', 'get_status' ),
      				'methods' => 'GET'
      			));
            
            register_rest_route( 'supportcandy/v1', '/categories', array(
      				'callback' => array( 'WPSC_Rest_v1_Helper', 'get_category_list' ),
      				'methods' => 'GET'
      			));
            
            register_rest_route( 'supportcandy/v1', '/categories/(?P<id>\d+)', array(
      				'callback' => array( 'WPSC_Rest_v1_Helper', 'get_category' ),
      				'methods' => 'GET'
      			));
            
            register_rest_route( 'supportcandy/v1', '/priorities', array(
      				'callback' => array( 'WPSC_Rest_v1_Helper', 'get_priority_list' ),
      				'methods' => 'GET'
      			));
            
            register_rest_route( 'supportcandy/v1', '/priorities/(?P<id>\d+)', array(
      				'callback' => array( 'WPSC_Rest_v1_Helper', 'get_priority' ),
      				'methods' => 'GET'
      			));
            
            register_rest_route( 'supportcandy/v1', '/filters', array(
      				'callback' => array( 'WPSC_Rest_v1_Helper', 'get_filters' ),
              'permission_callback' => array( 'WPSC_Rest_v1_Helper', 'validate_registered_user' ),
      				'methods' => 'POST'
      			));
            
            register_rest_route( 'supportcandy/v1', '/tickets', array(
      				'callback' => array( 'WPSC_Rest_v1_Helper', 'get_tickets' ),
              'permission_callback' => array( 'WPSC_Rest_v1_Helper', 'validate_registered_user' ),
      				'methods' => 'POST'
      			));
            
            register_rest_route( 'supportcandy/v1', '/tickets/(?P<id>\d+)', array(
      				'callback' => array( 'WPSC_Rest_v1_Helper', 'get_individual_ticket' ),
              'permission_callback' => array( 'WPSC_Rest_v1_Helper', 'validate_ticket_read_permission' ),
      				'methods' => 'POST'
      			));
            
            register_rest_route( 'supportcandy/v1', '/tickets/(?P<id>\d+)/threads', array(
      				'callback' => array( 'WPSC_Rest_v1_Helper', 'get_ticket_threads' ),
              'permission_callback' => array( 'WPSC_Rest_v1_Helper', 'validate_ticket_read_permission' ),
      				'methods' => 'POST'
      			));
            
            register_rest_route( 'supportcandy/v1', 'threads/historyId', array(
      				'callback' => array( 'WPSC_Rest_v1_Helper', 'get_thread_history_id' ),
              'permission_callback' => array( 'WPSC_Rest_v1_Helper', 'validate_registered_user' ),
      				'methods' => 'POST'
      			));
            
            register_rest_route( 'supportcandy/v1', 'threads/history', array(
      				'callback' => array( 'WPSC_Rest_v1_Helper', 'get_thread_history' ),
              'permission_callback' => array( 'WPSC_Rest_v1_Helper', 'validate_registered_user' ),
      				'methods' => 'POST'
      			));
            
            register_rest_route( 'supportcandy/v1', 'tickets/addRegisteredUserTicket', array(
      				'callback' => array( 'WPSC_Rest_v1_Helper', 'create_new_ticket' ),
              'permission_callback' => array( 'WPSC_Rest_v1_Helper', 'validate_registered_user' ),
      				'methods' => 'POST'
      			));
            
            register_rest_route( 'supportcandy/v1', 'tickets/addGuestTicket', array(
      				'callback' => array( 'WPSC_Rest_v1_Helper', 'create_new_ticket' ),
              'permission_callback' => array( 'WPSC_Rest_v1_Helper', 'validate_guest_user' ),
      				'methods' => 'POST'
      			));
            
            register_rest_route( 'supportcandy/v1', 'tickets/(?P<id>\d+)/addReply', array(
      				'callback' => array( 'WPSC_Rest_v1_Helper', 'submit_reply' ),
              'permission_callback' => array( 'WPSC_Rest_v1_Helper', 'validate_ticket_reply_permission' ),
      				'methods' => 'POST'
      			));
            
            register_rest_route( 'supportcandy/v1', 'tickets/(?P<id>\d+)/addNote', array(
      				'callback' => array( 'WPSC_Rest_v1_Helper', 'submit_note' ),
              'permission_callback' => array( 'WPSC_Rest_v1_Helper', 'validate_ticket_note_permission' ),
      				'methods' => 'POST'
      			));
            
            register_rest_route( 'supportcandy/v1', 'tickets/(?P<id>\d+)/updateFields', array(
      				'callback' => array( 'WPSC_Rest_v1_Helper', 'update_ticket_fields' ),
              'permission_callback' => array( 'WPSC_Rest_v1_Helper', 'validate_agent' ),
      				'methods' => 'POST'
      			));
            
            register_rest_route( 'supportcandy/v1', 'tickets/attachRegisteredUserFile', array(
      				'callback' => array( 'WPSC_Rest_v1_Helper', 'file_attachment' ),
              'permission_callback' => array( 'WPSC_Rest_v1_Helper', 'validate_registered_user_attachment' ),
      				'methods' => 'POST'
      			));
            
            register_rest_route( 'supportcandy/v1', 'tickets/attachGuestFile', array(
      				'callback' => array( 'WPSC_Rest_v1_Helper', 'file_attachment' ),
              'permission_callback' => array( 'WPSC_Rest_v1_Helper', 'validate_guest_attachment' ),
      				'methods' => 'POST'
      			));
            
            register_rest_route( 'supportcandy/v1', 'tickets/autocompleteSuggestions', array(
      				'callback' => array( 'WPSC_Rest_v1_Helper', 'autocomplete_suggestions' ),
              'permission_callback' => array( 'WPSC_Rest_v1_Helper', 'validate_agent' ),
      				'methods' => 'POST'
      			));
            
            register_rest_route( 'supportcandy/v1', '/agents', array(
      				'callback' => array( 'WPSC_Rest_v1_Helper', 'get_agents' ),
              'permission_callback' => array( 'WPSC_Rest_v1_Helper', 'validate_agent' ),
      				'methods' => 'POST'
      			));
            
            register_rest_route( 'supportcandy/v1', '/agents/(?P<id>\d+)', array(
      				'callback' => array( 'WPSC_Rest_v1_Helper', 'get_agent' ),
              'permission_callback' => array( 'WPSC_Rest_v1_Helper', 'validate_agent' ),
      				'methods' => 'POST'
      			));
            
            register_rest_route( 'supportcandy/v1', '/deleteTickets/(?P<id>\d+)', array(
      				'callback' => array( 'WPSC_Rest_v1_Helper', 'delete_ticket_by_id' ),
              'permission_callback' => array( 'WPSC_Rest_v1_Helper', 'validate_agent' ),
      				'methods' => 'POST'
      			));
            
      	}
        
        /**
         * Set current user for API
         */
        public static function determine_current_user( $user_id = 0 ) {
          
            if( $user_id ){
              return $user_id;
            }
            
            $user_id = SELF::validate_auth_token();
            
            if( $user_id ){
              wp_set_current_user($user_id);
              return $user_id;
            }
            
            return 0;
          
        }
        
        /**
         * Validate auth token for api
         */
        public static function validate_auth_token(){
          
            $auth_user  = isset($_REQUEST) && isset($_REQUEST['auth_user']) ? intval($_REQUEST['auth_user']) : 0;
            $auth_token = isset($_REQUEST) && isset($_REQUEST['auth_token']) ? sanitize_text_field($_REQUEST['auth_token']) : '';
            $secret_key = get_option('wpsc_rest_api_secret_key');
            
            if( $auth_token == md5( $secret_key . $auth_user . $secret_key ) ) {
              return $auth_user;
            }
            
            return 0;
          
        }
        
    }
  
endif;

WPSC_Rest_API_v1::init();
