<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

if ( ! class_exists( 'WPSC_Rest_v1_Helper' ) ) :
  
    final class WPSC_Rest_v1_Helper {
      
        /**
         * Check whether auth user has read permission of ticket.
         */
        public static function validate_ticket_read_permission( $request ){
          
            global $current_user, $wpscfunction;
            
            $params       = $request->get_params();
            $ticket_id    = isset($params['id']) ? intval($params['id']) : 0;
            $public_mode  = get_option('wpsc_ticket_public_mode');
            $ticket       = $wpscfunction->get_ticket($ticket_id);
            
            if( !( $ticket && $current_user && ( $current_user->user_email == $ticket['customer_email'] || $wpscfunction->has_permission('view_ticket',$ticket_id) || $public_mode) ) ){
              return new WP_Error(
                  'unauthorized',
                  'You are not authorized to view this ticket.',
                  array(
                      'status' => 403,
                  )
              );
            }
            
            return true;
          
        }
        
        /**
         * Check whether auth user has reply permission or not
         * @param  [Array] $request Given by REST fucntionality
         * @return boolean
         */
        public static function validate_ticket_reply_permission( $request ){
          
            global $current_user, $wpscfunction;
            
            $params       = $request->get_params();
            $ticket_id    = isset($params['id']) ? intval($params['id']) : 0;
            $public_mode  = get_option('wpsc_ticket_public_mode');
            $ticket       = $wpscfunction->get_ticket($ticket_id);
            
            if( !( $ticket && $current_user && ( $current_user->user_email == $ticket['customer_email'] || $wpscfunction->has_permission('reply_ticket',$ticket_id) || $public_mode) ) ){
              return new WP_Error(
                  'unauthorized',
                  'You are not authorized to add reply to this ticket.',
                  array(
                      'status' => 403,
                  )
              );
            }
            
            return true;
          
        }
        
        /**
         * Check whether auth user has add note permission or not
         * @param  [Array] $request Given by REST fucntionality
         * @return boolean
         */
        public static function validate_ticket_note_permission( $request ){
          
            global $current_user, $wpscfunction;
            
            $params    = $request->get_params();
            $ticket_id = isset($params['id']) ? intval($params['id']) : 0;
            $ticket    = $wpscfunction->get_ticket($ticket_id);
            
            if( !( $ticket && $current_user && $current_user->has_cap('wpsc_agent') && $wpscfunction->has_permission('add_note',$ticket_id) ) ){
              return new WP_Error(
                  'unauthorized',
                  'You are not authorized to add note to this ticket.',
                  array(
                      'status' => 403,
                  )
              );
            }
            
            return true;
          
        }
        
        /**
         * Validate whether guest ticket is enabled or not.
         */
        public static function validate_guest_user($request){
          
            $secret_key_validate = SELF::validate_secret_key($request);
            if (is_wp_error($secret_key_validate)) {
              return $secret_key_validate;
            }
            
            $guest_ticket = get_option('wpsc_allow_guest_ticket');
            if (!$guest_ticket) {
              return new WP_Error(
                  'guest_ticket_disabled',
                  'Guest ticket is not allowed. In this case, accessing form fields is not permitted.',
                  array(
                      'status' => 403,
                  )
              );
            }
            
            return true;
          
        }
        
        /**
         * Validate whether user is registered or not
         */
        public static function validate_registered_user($request){
          
            global $current_user;
            
            if($current_user->ID) {
              return true;
            }
            
            return new WP_Error(
                'unauthorized',
                'Either incorrect auth token or auth user.',
                array(
                    'status' => 403,
                )
            );
          
        }
        
        /**
         * Check whether registered user attachment is valid or not
         */
        public static function validate_registered_user_attachment($request){
          
            $is_registered = SELF::validate_registered_user($request);
            if (is_wp_error($is_registered)) {
              return $is_registered;
            }
            
            $is_valid = SELF::validate_attachment();
            if (is_wp_error($is_valid)) {
              return $is_valid;
            }
            
            return true;
          
        }
        
        /**
         * Check whether registered user attachment is valid or not
         */
        public static function validate_guest_attachment($request){
          
            $is_guest = SELF::validate_guest_user($request);
            if (is_wp_error($is_guest)) {
              return $is_guest;
            }
            
            // check whether guest attachments allowed or not
            $guest_upload = get_option('wpsc_guest_can_upload_files');
            if (!$guest_upload) {
              return new WP_Error(
                  'unauthorized',
                  'Guest not allowed file attachments.',
                  array(
                      'status' => 403,
                  )
              );
            }
            
            $is_valid = SELF::validate_attachment();
            if (is_wp_error($is_valid)) {
              return $is_valid;
            }
            
            return true;
          
        }
        
        /**
         * Check file related validations
         */
        public static function validate_attachment(){
          
            // see if file available or not
            $file = $_FILES && isset($_FILES['file']) ? $_FILES['file'] : array();
            if( count($file) == 0 ){
              return new WP_Error(
                  'file_not_found',
                  'File not found or file too large error.',
                  array(
                      'status' => 403,
                  )
              );
            }
            
            // See if size is within allowed limit or not
            $max_attachment_limit = get_option('wpsc_attachment_max_filesize');
            $file_size = isset($file['size']) && $file['size'] ? $file['size'] : 0;
            $file_size = $file_size ? ($file_size/1000)/1000 : 0;
            if( $file['tmp_name']=='' || $file_size > $max_attachment_limit ) {
              return new WP_Error(
                  'file_size_error',
                  'file size exceeded allowed limit!',
                  array(
                      'status' => 403,
                  )
              );
            }
            
            return true;
          
        }
        
        /**
         * Validate whether user has agent capabilities or not
         */
        public static function validate_agent($request){
          
            global $current_user;
            
            if( $current_user->ID && $current_user->has_cap('wpsc_agent') ) {
              return true;
            }
            
            if ( $current_user->ID ) {
              return new WP_Error(
                  'unauthorized',
                  'You do not have sufficient permission to access agentonly fields.',
                  array(
                      'status' => 403,
                  )
              );
            }
            
            return new WP_Error(
                'unauthorized',
                'Either incorrect auth token or auth user.',
                array(
                    'status' => 403,
                )
            );
          
        }
        
        /**
         * Validate secret key provided
         */
        public static function validate_secret_key( $request ){
          
            $params     = $request->get_params();
            $secret_key = isset($params['secret_key']) ? sanitize_text_field($params['secret_key']) : '';
            if( $secret_key == get_option('wpsc_rest_api_secret_key') ) {
              return true;
            } else {
              return new WP_Error(
                  'invalid_secret_key',
                  'Invalid Secret Key Provided.',
                  array(
                      'status' => 403,
                  )
              );
            }
          
        }
        
        /**
         * User login and return auth token
         */
        public static function user_login($request){
          
            global $wpdb;
            
            $params = $request->get_params();
            
            if ( isset($params['username']) && $params['password'] ) {
              
                $username   = sanitize_text_field($params['username']);
                $password   = sanitize_text_field($params['password']);
                $secret_key = isset($params['secret_key']) ? sanitize_text_field($params['secret_key']) : '';
                $users      = $wpdb->get_results("select * from {$wpdb->prefix}users WHERE user_login='".$username."' OR user_email='".$username."'");
                if( count($users) && wp_check_password($password,$users[0]->user_pass,$users[0]->ID) ) {
                  
                    $auth_token = md5( $secret_key . $users[0]->ID . $secret_key );
                    return array(
                      'auth_user'  => $users[0]->ID,
                      'auth_token' => $auth_token
                    );
                  
                } else {
                  
                    return new WP_Error(
                        'invalid_login_credential',
                        'Invalid username or password',
                        array(
                            'status' => 403,
                        )
                    );
                  
                }
              
            } else {
              
                return new WP_Error(
                    'missing_required_fields',
                    'Required fields are missing for the request.',
                    array(
                        'status' => 403,
                    )
                );
              
            }
          
        }
        
        /**
         * Returns ticket form fields
         */
        public static function get_ticket_form_fields( $request ){
          
            include WPSC_ABSPATH . 'includes/rest_api/v1/get_ticket_form_fields.php';
            return $response;
          
        }
        
        /**
         * Returns agentonly fields
         */
        public static function get_agentonly_fields( $request ){
          
            include WPSC_ABSPATH . 'includes/rest_api/v1/get_agentonly_fields.php';
            return $response;
          
        }
        
        /**
         * Get all statuses. No authentication needed
         */
        public static function get_status_list( $request ){
          
            include WPSC_ABSPATH . 'includes/rest_api/v1/get_status_list.php';
            return $response;
          
        }
        
        /**
         * Get status by id
         */
        public static function get_status( $request ){
            
            include WPSC_ABSPATH . 'includes/rest_api/v1/get_status_by_id.php';
            return $response;
            
        }
        
        /**
         * Get all statuses. No authentication needed
         */
        public static function get_category_list( $request ){
          
            include WPSC_ABSPATH . 'includes/rest_api/v1/get_category_list.php';
            return $response;
          
        }
        
        /**
         * Get status by id
         */
        public static function get_category( $request ){
            
            include WPSC_ABSPATH . 'includes/rest_api/v1/get_category_by_id.php';
            return $response;
            
        }
        
        /**
         * Get all priorities. No authentication needed
         */
        public static function get_priority_list( $request ){
          
            include WPSC_ABSPATH . 'includes/rest_api/v1/get_priority_list.php';
            return $response;
          
        }
        
        /**
         * Get status by id
         */
        public static function get_priority( $request ){
            
            include WPSC_ABSPATH . 'includes/rest_api/v1/get_priority_by_id.php';
            return $response;
            
        }
        
        /**
         * Get ticket list
         */
        public static function get_tickets( $request ){
            
            include WPSC_ABSPATH . 'includes/rest_api/v1/get_tickets.php';
            return $response;
            
        }
        
        /**
         * Get individual ticket
         */
        public static function get_individual_ticket( $request ){
            
            $params    = $request->get_params();
            $ticket_id = isset($params['id']) ? intval($params['id']) : 0;
            $response  = array();
            if($ticket_id){
              $response = SELF::get_ticket_response($ticket_id);
            }
            return $response;
            
        }
        
        /**
         * Get ticket threads
         */
        public static function get_ticket_threads( $request ){
          
            include WPSC_ABSPATH . 'includes/rest_api/v1/get_ticket_threads.php';
            return $response;
          
        }
        
        /**
         * Get ticket response array for a ticket
         */
        public static function get_ticket_response( $ticket_id ) {
          
            include WPSC_ABSPATH . 'includes/rest_api/v1/get_ticket_response.php';
            return $response;
          
        }
        
        /**
         * Get thread response array
         */
        public static function get_thread_response( $thread ){
          
            include WPSC_ABSPATH . 'includes/rest_api/v1/get_thread_response.php';
            return $response;
          
        }
        
        /**
         * Get filters for logged in user
         */
        public static function get_filters(){
          
            include WPSC_ABSPATH . 'includes/rest_api/v1/get_filters.php';
            return $response;
          
        }
        
        /**
         * Get last thread history id
         */
        public static function get_thread_history_id( $request ){
          
            include WPSC_ABSPATH . 'includes/rest_api/v1/get_thread_history_id.php';
            return $response;
          
        }
        
        /**
         * Get history of threads from last history id
         */
        public static function get_thread_history( $request ){
          
            include WPSC_ABSPATH . 'includes/rest_api/v1/get_thread_history.php';
            return $response;
          
        }
        
        /**
         * Get meta query restrict rules for current user
         */
        public static function get_tl_meta_query_restrict_rules(){
          
            include WPSC_ABSPATH . 'includes/rest_api/v1/get_tl_meta_query_restrict_rules.php';
            return $restrict_rules;
          
        }
        
        /**
         * Create New Ticket
         */
        public static function create_new_ticket($request){
          
            include WPSC_ABSPATH . 'includes/rest_api/v1/create_new_ticket.php';
            return $response;
        }
        
        /**
         *  Submit ticket reply 
        */
        public static function submit_reply($request){
            
            include WPSC_ABSPATH . 'includes/rest_api/v1/submit_reply.php';
            return $response;
        }
        
        /**
         *  Submit ticket note 
        */
        public static function submit_note($request){
            
            include WPSC_ABSPATH . 'includes/rest_api/v1/submit_note.php';
            return $response;
        }
        
        /**
         * Update ticket fields 
         */
        public static function update_ticket_fields($request){
          
            include WPSC_ABSPATH . 'includes/rest_api/v1/update_ticket_fields.php';
            return $response;
        }
        
        /**
         * Attach file and return attachment id
         */
        public static function file_attachment($request){
          
            include WPSC_ABSPATH . 'includes/rest_api/v1/file_attachment.php';
            return $response;
          
        }
        
        /**
         * Attach file and return attachment id
         */
        public static function autocomplete_suggestions($request){
          
            include WPSC_ABSPATH . 'includes/rest_api/v1/autocomplete_suggestions.php';
            return $response;
          
        }
        
        /**
         * Get all agents 
         */
        public static function get_agents($request) {
            include WPSC_ABSPATH . 'includes/rest_api/v1/get_agents.php';
            return $response;
        }
        
        /**
         * Get agent by id
         */
        public static function get_agent($request) {
            include WPSC_ABSPATH . 'includes/rest_api/v1/get_agent_by_id.php';
            return $response;
        }
        
        /**
         * Return single agent response
         */
        public static function get_agent_info( $agent_id, $agent_roles ){
            
            $agent   = get_term_by('id', $agent_id, 'wpsc_agents');
            $role_id = get_term_meta( $agent->term_id, 'role', true);
            
            return array(
                'id'   => $agent->term_id,
                'name' => get_term_meta( $agent->term_id, 'label', true),
                'role' => array(
                  'id' => $role_id,
                  'name' => $agent_roles[$role_id]['label']
                ),
            );
            
        }
        
        /**
         * Delete single ticket
         */
        public static function delete_ticket_by_id($request){
            include WPSC_ABSPATH . 'includes/rest_api/v1/delete_ticket.php';
            return $response;
        }

        
    }
    
endif;
