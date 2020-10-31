<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WPSC_Install' ) ) :

  final class WPSC_Install {

    public function __construct() {
      add_action( 'init', array($this,'register_post_type'), 100 );
      $this->check_version();
    }

    // Register post types and texonomies
    public function register_post_type(){

			// Register cutom post type
      $args = array(
          'public'             => false,
          'rewrite'            => false
      );
      register_post_type( 'wpsc_ticket', $args );

			// Threads post
			$args = array(
          'public'             => false,
          'rewrite'            => false
      );
      register_post_type( 'wpsc_ticket_thread', $args );

			// Register categories texonomy
			$args = array(
          'public'             => false,
          'rewrite'            => false
      );
      register_taxonomy( 'wpsc_categories', 'wpsc_ticket', $args );

			// Register status texonomy
			$args = array(
          'public'             => false,
          'rewrite'            => false
      );
      register_taxonomy( 'wpsc_statuses', 'wpsc_ticket', $args );

			// Register priorities texonomy
			$args = array(
          'public'             => false,
          'rewrite'            => false
      );
      register_taxonomy( 'wpsc_priorities', 'wpsc_ticket', $args );

			// Register form field texonomy
			$args = array(
          'public'             => false,
          'rewrite'            => false
      );
      register_taxonomy( 'wpsc_ticket_custom_fields', 'wpsc_ticket', $args );

			// Register agent texonomy
			$args = array(
          'public'             => false,
          'rewrite'            => false
      );

			register_taxonomy('wpsc_ticket_widget','wpsc_ticket',$args);
			// Register form field texonomy
			$args= array(
				'public'             => false,
				'rewrite'            => false
			);

      register_taxonomy( 'wpsc_agents', 'wpsc_ticket', $args );

			// Register attachment texonomy
			$args = array(
          'public'             => false,
          'rewrite'            => false
      );
      register_taxonomy( 'wpsc_attachment', 'wpsc_ticket', $args );

			// Register ticket notifications texonomy
			$args = array(
          'public'             => false,
          'rewrite'            => false
      );
      register_taxonomy( 'wpsc_en', 'wpsc_ticket', $args );

		// Register reply image attachment texonomy
		$args = array(
			'public'             => false,
			'rewrite'            => false
		);
		register_taxonomy( 'wpsc_image_attachment', 'wpsc_ticket', $args );

    }

		/**
     * Check version of WPSC
     */
    public function check_version(){

        $installed_version = get_option( 'wpsc_current_version' );
        if( $installed_version != WPSC_VERSION ){
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

			$tables = "
					CREATE TABLE {$wpdb->prefix}wpsc_ticket (
						id bigint(20) NOT NULL AUTO_INCREMENT,
						ticket_status integer,
						customer_name TINYTEXT NULL DEFAULT NULL,
						customer_email TINYTEXT NULL DEFAULT NULL,
						ticket_subject LONGTEXT NULL DEFAULT NULL,
						user_type varchar(30) NULL DEFAULT NULL,
						ticket_category integer,
						ticket_priority integer,
						date_created datetime,
						date_updated datetime,
						ip_address VARCHAR(30) NULL DEFAULT NULL,
						agent_created INT NULL DEFAULT '0',
						ticket_auth_code LONGTEXT NULL DEFAULT NULL,
						historyId bigint(20) DEFAULT 0,
						active int(11) DEFAULT 1,
						PRIMARY KEY  (id)
					) $collate;
					CREATE TABLE {$wpdb->prefix}wpsc_ticketmeta (
						id bigint(20) NOT NULL AUTO_INCREMENT,
						ticket_id bigint(20),
						meta_key LONGTEXT NULL DEFAULT NULL,
						meta_value LONGTEXT NULL DEFAULT NULL,
						PRIMARY KEY  (id),
						KEY ticket_id (ticket_id)
					) $collate;
					CREATE TABLE {$wpdb->prefix}wpsc_email_notification (
						id bigint(20) NOT NULL AUTO_INCREMENT,
						ticket_id bigint(20),
						from_email varchar(200) NULL DEFAULT NULL,
						reply_to varchar(200) NULL DEFAULT NULL,
						email_subject varchar(200) NULL DEFAULT NULL,
						email_body LONGTEXT NULL DEFAULT NULL,
						to_email varchar(200) NULL DEFAULT NULL,
						bcc_email LONGTEXT NULL DEFAULT NULL,
						email_type varchar(200) NULL DEFAULT NULL,
						date_created datetime,
						send_date datetime,
						mail_status integer,
						attemp integer DEFAULT 0,
						PRIMARY KEY  (id)
					) $collate;
					";
					dbDelta( $tables );
		}

		// Upgrade
		public function upgrade(){

				$installed_version = get_option( 'wpsc_current_version' );
				$installed_version = $installed_version ? $installed_version : 0;

				if(!$installed_version){
					update_option( 'wpsc_db_version', WPSC_DB_VERSION );
				}

				if ( $installed_version < '1.0.0' ) {

					// Category Items
					$term = wp_insert_term( __('General','supportcandy'), 'wpsc_categories' );
					if (!is_wp_error($term) && isset($term['term_id'])) {
						add_term_meta ($term['term_id'], 'wpsc_category_load_order', '1');
						update_option('wpsc_default_ticket_category',$term['term_id']);
					}

					// Status Items
					$term = wp_insert_term( __('Open','supportcandy'), 'wpsc_statuses' );
					if (!is_wp_error($term) && isset($term['term_id'])) {
						add_term_meta ($term['term_id'], 'wpsc_status_color', '#ffffff');
						add_term_meta ($term['term_id'], 'wpsc_status_background_color', '#d9534f');
						add_term_meta ($term['term_id'], 'wpsc_status_load_order', '1');
						update_option('wpsc_default_ticket_status',$term['term_id']);
					}
					$term = wp_insert_term( __('Awaiting customer reply','supportcandy'), 'wpsc_statuses' );
					if (!is_wp_error($term) && isset($term['term_id'])) {
						add_term_meta ($term['term_id'], 'wpsc_status_color', '#ffffff');
						add_term_meta ($term['term_id'], 'wpsc_status_background_color', '#000000');
						add_term_meta ($term['term_id'], 'wpsc_status_load_order', '2');
						update_option('wpsc_ticket_status_after_agent_reply',$term['term_id']);
					}
					$term = wp_insert_term( __('Awaiting agent reply','supportcandy'), 'wpsc_statuses' );
					if (!is_wp_error($term) && isset($term['term_id'])) {
						add_term_meta ($term['term_id'], 'wpsc_status_color', '#ffffff');
						add_term_meta ($term['term_id'], 'wpsc_status_background_color', '#f0ad4e');
						add_term_meta ($term['term_id'], 'wpsc_status_load_order', '3');
						update_option('wpsc_ticket_status_after_customer_reply',$term['term_id']);
					}
					$term = wp_insert_term( __('Closed','supportcandy'), 'wpsc_statuses' );
					if (!is_wp_error($term) && isset($term['term_id'])) {
						add_term_meta ($term['term_id'], 'wpsc_status_color', '#ffffff');
						add_term_meta ($term['term_id'], 'wpsc_status_background_color', '#5cb85c');
						add_term_meta ($term['term_id'], 'wpsc_status_load_order', '4');
						update_option('wpsc_close_ticket_status',$term['term_id']);
					}

					// Priority Items
					$term = wp_insert_term( __('Low','supportcandy'), 'wpsc_priorities' );
					if (!is_wp_error($term) && isset($term['term_id'])) {
						add_term_meta ($term['term_id'], 'wpsc_priority_color', '#ffffff');
						add_term_meta ($term['term_id'], 'wpsc_priority_background_color', '#5bc0de');
						add_term_meta ($term['term_id'], 'wpsc_priority_load_order', '1');
						update_option('wpsc_default_ticket_priority',$term['term_id']);
					}
					$term = wp_insert_term( __('Medium','supportcandy'), 'wpsc_priorities' );
					if (!is_wp_error($term) && isset($term['term_id'])) {
						add_term_meta ($term['term_id'], 'wpsc_priority_color', '#ffffff');
						add_term_meta ($term['term_id'], 'wpsc_priority_background_color', '#f0ad4e');
						add_term_meta ($term['term_id'], 'wpsc_priority_load_order', '2');
					}
					$term = wp_insert_term( __('High','supportcandy'), 'wpsc_priorities' );
					if (!is_wp_error($term) && isset($term['term_id'])) {
						add_term_meta ($term['term_id'], 'wpsc_priority_color', '#ffffff');
						add_term_meta ($term['term_id'], 'wpsc_priority_background_color', '#d9534f');
						add_term_meta ($term['term_id'], 'wpsc_priority_load_order', '3');
					}


					// Ticket form items
					$term = wp_insert_term( 'ticket_id', 'wpsc_ticket_custom_fields' );
					if (!is_wp_error($term) && isset($term['term_id'])) {
						add_term_meta ($term['term_id'], 'wpsc_tf_label', __('ID','supportcandy'));
						add_term_meta ($term['term_id'], 'agentonly', '2');
						add_term_meta ($term['term_id'], 'wpsc_tf_type', '0');
						add_term_meta ($term['term_id'], 'wpsc_allow_ticket_list', '1');
						add_term_meta ($term['term_id'], 'wpsc_customer_ticket_list_status', '1');
						add_term_meta ($term['term_id'], 'wpsc_agent_ticket_list_status', '1');
						add_term_meta ($term['term_id'], 'wpsc_allow_ticket_filter', '1');
						add_term_meta ($term['term_id'], 'wpsc_ticket_filter_type', 'number');
						add_term_meta ($term['term_id'], 'wpsc_customer_ticket_filter_status', '1');
						add_term_meta ($term['term_id'], 'wpsc_agent_ticket_filter_status', '1');
						add_term_meta ($term['term_id'], 'wpsc_tl_customer_load_order', '1');
						add_term_meta ($term['term_id'], 'wpsc_tl_agent_load_order', '1');
						add_term_meta ($term['term_id'], 'wpsc_filter_customer_load_order', '1');
						add_term_meta ($term['term_id'], 'wpsc_filter_agent_load_order', '1');
						add_term_meta ($term['term_id'], 'wpsc_allow_orderby', '1');
					}
					$term = wp_insert_term( 'ticket_status', 'wpsc_ticket_custom_fields' );
					if (!is_wp_error($term) && isset($term['term_id'])) {
						add_term_meta ($term['term_id'], 'wpsc_tf_label', __('Status','supportcandy'));
						add_term_meta ($term['term_id'], 'agentonly', '2');
						add_term_meta ($term['term_id'], 'wpsc_tf_type', '0');
						add_term_meta ($term['term_id'], 'wpsc_conditional', '1');
						add_term_meta ($term['term_id'], 'wpsc_allow_ticket_list', '1');
						add_term_meta ($term['term_id'], 'wpsc_customer_ticket_list_status', '1');
						add_term_meta ($term['term_id'], 'wpsc_agent_ticket_list_status', '1');
						add_term_meta ($term['term_id'], 'wpsc_allow_ticket_filter', '1');
						add_term_meta ($term['term_id'], 'wpsc_ticket_filter_type', 'number');
						add_term_meta ($term['term_id'], 'wpsc_customer_ticket_filter_status', '1');
						add_term_meta ($term['term_id'], 'wpsc_agent_ticket_filter_status', '1');
						add_term_meta ($term['term_id'], 'wpsc_tl_customer_load_order', '2');
						add_term_meta ($term['term_id'], 'wpsc_tl_agent_load_order', '2');
						add_term_meta ($term['term_id'], 'wpsc_filter_customer_load_order', '2');
						add_term_meta ($term['term_id'], 'wpsc_filter_agent_load_order', '2');
					}
					$term = wp_insert_term( 'customer_name', 'wpsc_ticket_custom_fields' );
					if (!is_wp_error($term) && isset($term['term_id'])) {
						add_term_meta ($term['term_id'], 'wpsc_tf_label', __('Name','supportcandy'));
						add_term_meta ($term['term_id'], 'wpsc_tf_extra_info', __('Please insert your name.','supportcandy'));
						add_term_meta ($term['term_id'], 'wpsc_tf_status', '1');
						add_term_meta ($term['term_id'], 'agentonly', '0');
						add_term_meta ($term['term_id'], 'wpsc_tf_type', '0');
						add_term_meta ($term['term_id'], 'wpsc_tf_required', '1');
						add_term_meta ($term['term_id'], 'wpsc_tf_width', '1/2');
						add_term_meta ($term['term_id'], 'wpsc_tf_load_order', '1');
						add_term_meta ($term['term_id'], 'wpsc_allow_ticket_list', '1');
						add_term_meta ($term['term_id'], 'wpsc_customer_ticket_list_status', '0');
						add_term_meta ($term['term_id'], 'wpsc_agent_ticket_list_status', '1');
						add_term_meta ($term['term_id'], 'wpsc_allow_ticket_filter', '1');
						add_term_meta ($term['term_id'], 'wpsc_ticket_filter_type', 'string');
						add_term_meta ($term['term_id'], 'wpsc_customer_ticket_filter_status', '0');
						add_term_meta ($term['term_id'], 'wpsc_agent_ticket_filter_status', '1');
						add_term_meta ($term['term_id'], 'wpsc_tl_agent_load_order', '4');
						add_term_meta ($term['term_id'], 'wpsc_filter_agent_load_order', '4');
					}
					$term = wp_insert_term( 'customer_email', 'wpsc_ticket_custom_fields' );
					if (!is_wp_error($term) && isset($term['term_id'])) {
						add_term_meta ($term['term_id'], 'wpsc_tf_label', __('Email Address','supportcandy'));
						add_term_meta ($term['term_id'], 'wpsc_tf_extra_info', __('Please insert your email.','supportcandy'));
						add_term_meta ($term['term_id'], 'wpsc_tf_status', '1');
						add_term_meta ($term['term_id'], 'agentonly', '0');
						add_term_meta ($term['term_id'], 'wpsc_tf_type', '0');
						add_term_meta ($term['term_id'], 'wpsc_tf_required', '1');
						add_term_meta ($term['term_id'], 'wpsc_tf_width', '1/2');
						add_term_meta ($term['term_id'], 'wpsc_tf_load_order', '2');
						add_term_meta ($term['term_id'], 'wpsc_allow_ticket_list', '1');
						add_term_meta ($term['term_id'], 'wpsc_customer_ticket_list_status', '0');
						add_term_meta ($term['term_id'], 'wpsc_agent_ticket_list_status', '0');
						add_term_meta ($term['term_id'], 'wpsc_allow_ticket_filter', '1');
						add_term_meta ($term['term_id'], 'wpsc_ticket_filter_type', 'string');
						add_term_meta ($term['term_id'], 'wpsc_customer_ticket_filter_status', '0');
						add_term_meta ($term['term_id'], 'wpsc_agent_ticket_filter_status', '1');
						add_term_meta ($term['term_id'], 'wpsc_filter_agent_load_order', '5');
					}
					$term = wp_insert_term( 'ticket_subject', 'wpsc_ticket_custom_fields' );
					if (!is_wp_error($term) && isset($term['term_id'])) {
						add_term_meta ($term['term_id'], 'wpsc_tf_label', __('Subject','supportcandy'));
						add_term_meta ($term['term_id'], 'wpsc_tf_extra_info', __('Short description of the ticket.','supportcandy'));
						add_term_meta ($term['term_id'], 'wpsc_tf_status', '1');
						add_term_meta ($term['term_id'], 'agentonly', '0');
						add_term_meta ($term['term_id'], 'wpsc_tf_type', '0');
						add_term_meta ($term['term_id'], 'wpsc_tf_required', '1');
						add_term_meta ($term['term_id'], 'wpsc_tf_width', '1');
						add_term_meta ($term['term_id'], 'wpsc_tf_load_order', '3');
						add_term_meta ($term['term_id'], 'wpsc_allow_ticket_list', '1');
						add_term_meta ($term['term_id'], 'wpsc_customer_ticket_list_status', '1');
						add_term_meta ($term['term_id'], 'wpsc_agent_ticket_list_status', '1');
						add_term_meta ($term['term_id'], 'wpsc_tl_customer_load_order', '3');
						add_term_meta ($term['term_id'], 'wpsc_tl_agent_load_order', '3');
						add_term_meta ($term['term_id'], 'wpsc_allow_ticket_filter', '0');
					}
					$term = wp_insert_term( 'ticket_description', 'wpsc_ticket_custom_fields' );
					if (!is_wp_error($term) && isset($term['term_id'])) {
						add_term_meta ($term['term_id'], 'wpsc_tf_label', __('Description','supportcandy'));
						add_term_meta ($term['term_id'], 'wpsc_tf_extra_info', __('Detailed description of the ticket','supportcandy'));
						add_term_meta ($term['term_id'], 'agentonly', '0');
						add_term_meta ($term['term_id'], 'wpsc_tf_status', '1');
						add_term_meta ($term['term_id'], 'wpsc_tf_type', '0');
						add_term_meta ($term['term_id'], 'wpsc_tf_required', '1');
						add_term_meta ($term['term_id'], 'wpsc_tf_width', '1');
						add_term_meta ($term['term_id'], 'wpsc_tf_load_order', '4');
						add_term_meta ($term['term_id'], 'wpsc_allow_ticket_list', '0');
						add_term_meta ($term['term_id'], 'wpsc_allow_ticket_filter', '0');
					}
					$term = wp_insert_term( 'ticket_category', 'wpsc_ticket_custom_fields' );
					if (!is_wp_error($term) && isset($term['term_id'])) {
						add_term_meta ($term['term_id'], 'wpsc_tf_label', __('Category','supportcandy'));
						add_term_meta ($term['term_id'], 'wpsc_tf_extra_info', __('Please select category.','supportcandy'));
						add_term_meta ($term['term_id'], 'agentonly', '0');
						add_term_meta ($term['term_id'], 'wpsc_tf_status', '1');
						add_term_meta ($term['term_id'], 'wpsc_tf_type', '0');
						add_term_meta ($term['term_id'], 'wpsc_tf_conditional', '1');
						add_term_meta ($term['term_id'], 'wpsc_conditional', '1');
						add_term_meta ($term['term_id'], 'wpsc_tf_required', '1');
						add_term_meta ($term['term_id'], 'wpsc_tf_width', '1/2');
						add_term_meta ($term['term_id'], 'wpsc_tf_load_order', '5');
						add_term_meta ($term['term_id'], 'wpsc_allow_ticket_list', '1');
						add_term_meta ($term['term_id'], 'wpsc_customer_ticket_list_status', '1');
						add_term_meta ($term['term_id'], 'wpsc_agent_ticket_list_status', '1');
						add_term_meta ($term['term_id'], 'wpsc_allow_ticket_filter', '1');
						add_term_meta ($term['term_id'], 'wpsc_ticket_filter_type', 'number');
						add_term_meta ($term['term_id'], 'wpsc_customer_ticket_filter_status', '1');
						add_term_meta ($term['term_id'], 'wpsc_agent_ticket_filter_status', '1');
						add_term_meta ($term['term_id'], 'wpsc_tl_customer_load_order', '6');
						add_term_meta ($term['term_id'], 'wpsc_tl_agent_load_order', '6');
						add_term_meta ($term['term_id'], 'wpsc_filter_customer_load_order', '6');
						add_term_meta ($term['term_id'], 'wpsc_filter_agent_load_order', '6');
					}
					$term = wp_insert_term( 'ticket_priority', 'wpsc_ticket_custom_fields' );
					if (!is_wp_error($term) && isset($term['term_id'])) {
						add_term_meta ($term['term_id'], 'wpsc_tf_label', __('Priority','supportcandy'));
						add_term_meta ($term['term_id'], 'wpsc_tf_extra_info', __('Please select priority.','supportcandy'));
						add_term_meta ($term['term_id'], 'agentonly', '2');
			      add_term_meta ($term['term_id'], 'wpsc_tf_type', '0');
						add_term_meta ($term['term_id'], 'wpsc_conditional', '1');
				    add_term_meta ($term['term_id'], 'wpsc_allow_ticket_list', '1');
						add_term_meta ($term['term_id'], 'wpsc_customer_ticket_list_status', '1');
						add_term_meta ($term['term_id'], 'wpsc_agent_ticket_list_status', '1');
						add_term_meta ($term['term_id'], 'wpsc_allow_ticket_filter', '1');
						add_term_meta ($term['term_id'], 'wpsc_ticket_filter_type', 'number');
						add_term_meta ($term['term_id'], 'wpsc_customer_ticket_filter_status', '1');
						add_term_meta ($term['term_id'], 'wpsc_agent_ticket_filter_status', '1');
						add_term_meta ($term['term_id'], 'wpsc_tl_customer_load_order', '7');
						add_term_meta ($term['term_id'], 'wpsc_tl_agent_load_order', '7');
						add_term_meta ($term['term_id'], 'wpsc_filter_customer_load_order', '7');
						add_term_meta ($term['term_id'], 'wpsc_filter_agent_load_order', '7');
					}
					$term = wp_insert_term( 'assigned_agent', 'wpsc_ticket_custom_fields' );
					if (!is_wp_error($term) && isset($term['term_id'])) {
						add_term_meta ($term['term_id'], 'wpsc_tf_label', __('Assigned Agent','supportcandy'));
						add_term_meta ($term['term_id'], 'agentonly', '2');
						add_term_meta ($term['term_id'], 'wpsc_tf_type', '0');
						add_term_meta ($term['term_id'], 'wpsc_allow_ticket_list', '1');
						add_term_meta ($term['term_id'], 'wpsc_customer_ticket_list_status', '0');
						add_term_meta ($term['term_id'], 'wpsc_agent_ticket_list_status', '1');
						add_term_meta ($term['term_id'], 'wpsc_allow_ticket_filter', '1');
						add_term_meta ($term['term_id'], 'wpsc_ticket_filter_type', 'number');
						add_term_meta ($term['term_id'], 'wpsc_customer_ticket_filter_status', '0');
						add_term_meta ($term['term_id'], 'wpsc_agent_ticket_filter_status', '1');
						add_term_meta ($term['term_id'], 'wpsc_tl_agent_load_order', '8');
						add_term_meta ($term['term_id'], 'wpsc_filter_agent_load_order', '8');
					}
					$term = wp_insert_term( 'date_created', 'wpsc_ticket_custom_fields' );
					if (!is_wp_error($term) && isset($term['term_id'])) {
						add_term_meta ($term['term_id'], 'wpsc_tf_label', __('Date Created','supportcandy'));
						add_term_meta ($term['term_id'], 'agentonly', '2');
						add_term_meta ($term['term_id'], 'wpsc_tf_type', '0');
						add_term_meta ($term['term_id'], 'wpsc_allow_ticket_list', '1');
						add_term_meta ($term['term_id'], 'wpsc_customer_ticket_list_status', '0');
						add_term_meta ($term['term_id'], 'wpsc_agent_ticket_list_status', '0');
						add_term_meta ($term['term_id'], 'wpsc_allow_ticket_filter', '1');
						add_term_meta ($term['term_id'], 'wpsc_ticket_filter_type', 'string');
						add_term_meta ($term['term_id'], 'wpsc_customer_ticket_filter_status', '1');
						add_term_meta ($term['term_id'], 'wpsc_agent_ticket_filter_status', '1');
						add_term_meta ($term['term_id'], 'wpsc_tl_agent_load_order', '9');
						add_term_meta ($term['term_id'], 'wpsc_filter_customer_load_order', '9');
						add_term_meta ($term['term_id'], 'wpsc_filter_agent_load_order', '9');
						add_term_meta ($term['term_id'], 'wpsc_allow_orderby', '1');
					}
					$term = wp_insert_term( 'date_updated', 'wpsc_ticket_custom_fields' );
					if (!is_wp_error($term) && isset($term['term_id'])) {
						add_term_meta ($term['term_id'], 'wpsc_tf_label', __('Date Updated','supportcandy'));
						add_term_meta ($term['term_id'], 'agentonly', '2');
						add_term_meta ($term['term_id'], 'wpsc_tf_type', '0');
						add_term_meta ($term['term_id'], 'wpsc_allow_ticket_list', '1');
						add_term_meta ($term['term_id'], 'wpsc_customer_ticket_list_status', '1');
						add_term_meta ($term['term_id'], 'wpsc_agent_ticket_list_status', '1');
						add_term_meta ($term['term_id'], 'wpsc_allow_ticket_filter', '0');
						add_term_meta ($term['term_id'], 'wpsc_ticket_filter_type', 'string');
						add_term_meta ($term['term_id'], 'wpsc_tl_customer_load_order', '10');
						add_term_meta ($term['term_id'], 'wpsc_tl_agent_load_order', '10');
						add_term_meta ($term['term_id'], 'wpsc_allow_orderby', '1');
					}
					$term = wp_insert_term( 'agent_created', 'wpsc_ticket_custom_fields' );
					if (!is_wp_error($term) && isset($term['term_id'])) {
						add_term_meta ($term['term_id'], 'wpsc_tf_label', __('Agent Created','supportcandy'));
						add_term_meta ($term['term_id'], 'agentonly', '2');
						add_term_meta ($term['term_id'], 'wpsc_tf_type', '0');
						add_term_meta ($term['term_id'], 'wpsc_allow_ticket_list', '1');
						add_term_meta ($term['term_id'], 'wpsc_customer_ticket_list_status', '0');
						add_term_meta ($term['term_id'], 'wpsc_agent_ticket_list_status', '0');
						add_term_meta ($term['term_id'], 'wpsc_allow_ticket_filter', '1');
						add_term_meta ($term['term_id'], 'wpsc_ticket_filter_type', 'number');
						add_term_meta ($term['term_id'], 'wpsc_customer_ticket_filter_status', '0');
						add_term_meta ($term['term_id'], 'wpsc_agent_ticket_filter_status', '0');
					}
					$term = wp_insert_term( 'ticket_url', 'wpsc_ticket_custom_fields' );
					if (!is_wp_error($term) && isset($term['term_id'])) {
						add_term_meta ($term['term_id'], 'wpsc_tf_label', __('Ticket URL','supportcandy'));
						add_term_meta ($term['term_id'], 'agentonly', '2');
						add_term_meta ($term['term_id'], 'wpsc_tf_type', '0');
						add_term_meta ($term['term_id'], 'wpsc_allow_ticket_list', '0');
						add_term_meta ($term['term_id'], 'wpsc_allow_ticket_filter', '0');
					}
					$term = wp_insert_term( 'last_reply', 'wpsc_ticket_custom_fields' );
					if (!is_wp_error($term) && isset($term['term_id'])) {
						add_term_meta ($term['term_id'], 'wpsc_tf_label', __('Last reply','supportcandy'));
						add_term_meta ($term['term_id'], 'agentonly', '2');
						add_term_meta ($term['term_id'], 'wpsc_tf_type', '0');
						add_term_meta ($term['term_id'], 'wpsc_allow_ticket_list', '0');
						add_term_meta ($term['term_id'], 'wpsc_allow_ticket_filter', '0');
					}
					$term = wp_insert_term( 'last_reply_user_name', 'wpsc_ticket_custom_fields' );
					if (!is_wp_error($term) && isset($term['term_id'])) {
						add_term_meta ($term['term_id'], 'wpsc_tf_label', __('Last reply user name','supportcandy'));
						add_term_meta ($term['term_id'], 'agentonly', '2');
						add_term_meta ($term['term_id'], 'wpsc_tf_type', '0');
						add_term_meta ($term['term_id'], 'wpsc_allow_ticket_list', '0');
						add_term_meta ($term['term_id'], 'wpsc_allow_ticket_filter', '0');
					}
					$term = wp_insert_term( 'last_reply_user_email', 'wpsc_ticket_custom_fields' );
					if (!is_wp_error($term) && isset($term['term_id'])) {
						add_term_meta ($term['term_id'], 'wpsc_tf_label', __('Last reply user email','supportcandy'));
						add_term_meta ($term['term_id'], 'agentonly', '2');
						add_term_meta ($term['term_id'], 'wpsc_tf_type', '0');
						add_term_meta ($term['term_id'], 'wpsc_allow_ticket_list', '0');
						add_term_meta ($term['term_id'], 'wpsc_allow_ticket_filter', '0');
					}
					$term = wp_insert_term( 'last_note', 'wpsc_ticket_custom_fields' );
					if (!is_wp_error($term) && isset($term['term_id'])) {
						add_term_meta ($term['term_id'], 'wpsc_tf_label', __('Last Note','supportcandy'));
						add_term_meta ($term['term_id'], 'agentonly', '2');
						add_term_meta ($term['term_id'], 'wpsc_tf_type', '0');
						add_term_meta ($term['term_id'], 'wpsc_allow_ticket_list', '0');
						add_term_meta ($term['term_id'], 'wpsc_allow_ticket_filter', '0');
					}
					$term = wp_insert_term( 'last_note_user_name', 'wpsc_ticket_custom_fields' );
					if (!is_wp_error($term) && isset($term['term_id'])) {
						add_term_meta ($term['term_id'], 'wpsc_tf_label', __('Last note user name','supportcandy'));
						add_term_meta ($term['term_id'], 'agentonly', '2');
						add_term_meta ($term['term_id'], 'wpsc_tf_type', '0');
						add_term_meta ($term['term_id'], 'wpsc_allow_ticket_list', '0');
						add_term_meta ($term['term_id'], 'wpsc_allow_ticket_filter', '0');
					}
					$term = wp_insert_term( 'last_note_user_email', 'wpsc_ticket_custom_fields' );
					if (!is_wp_error($term) && isset($term['term_id'])) {
						add_term_meta ($term['term_id'], 'wpsc_tf_label', __('Last note user email','supportcandy'));
						add_term_meta ($term['term_id'], 'agentonly', '2');
						add_term_meta ($term['term_id'], 'wpsc_tf_type', '0');
						add_term_meta ($term['term_id'], 'wpsc_allow_ticket_list', '0');
						add_term_meta ($term['term_id'], 'wpsc_allow_ticket_filter', '0');
					}
					$term = wp_insert_term( 'current_user_name', 'wpsc_ticket_custom_fields' );
					if (!is_wp_error($term) && isset($term['term_id'])) {
						add_term_meta ($term['term_id'], 'wpsc_tf_label', __('Current user name','supportcandy'));
						add_term_meta ($term['term_id'], 'agentonly', '2');
						add_term_meta ($term['term_id'], 'wpsc_tf_type', '0');
						add_term_meta ($term['term_id'], 'wpsc_allow_ticket_list', '0');
						add_term_meta ($term['term_id'], 'wpsc_allow_ticket_filter', '0');
					}
					$term = wp_insert_term( 'current_user_email', 'wpsc_ticket_custom_fields' );
					if (!is_wp_error($term) && isset($term['term_id'])) {
						add_term_meta ($term['term_id'], 'wpsc_tf_label', __('Current user email','supportcandy'));
						add_term_meta ($term['term_id'], 'agentonly', '2');
						add_term_meta ($term['term_id'], 'wpsc_tf_type', '0');
						add_term_meta ($term['term_id'], 'wpsc_allow_ticket_list', '0');
						add_term_meta ($term['term_id'], 'wpsc_allow_ticket_filter', '0');
					}
					$term = wp_insert_term( 'ticket_history', 'wpsc_ticket_custom_fields' );
					if (!is_wp_error($term) && isset($term['term_id'])) {
						add_term_meta ($term['term_id'], 'wpsc_tf_label', __('All threads in decending order except last reply','supportcandy'));
						add_term_meta ($term['term_id'], 'agentonly', '2');
						add_term_meta ($term['term_id'], 'wpsc_tf_type', '0');
						add_term_meta ($term['term_id'], 'wpsc_allow_ticket_list', '0');
						add_term_meta ($term['term_id'], 'wpsc_allow_ticket_filter', '0');
					}

					// Agents
					$admins = get_users( array('role' => 'administrator') );
					foreach ($admins as $admin) {
						$term = wp_insert_term( 'agent_'.$admin->ID, 'wpsc_agents' );
						if (!is_wp_error($term) && isset($term['term_id'])) {
							add_term_meta ($term['term_id'], 'user_id', $admin->ID);
							add_term_meta ($term['term_id'], 'label', $admin->display_name);
							add_term_meta ($term['term_id'], 'role', '1');
							add_term_meta ($term['term_id'], 'agentgroup', '0');
							$admin->add_cap('wpsc_agent');
							update_user_option($admin->ID,'wpsc_agent_role',1);
						}
					}

					// Email Notifications
					$term = wp_insert_term( __('New ticket customer confirmation','supportcandy'), 'wpsc_en' );
					if (!is_wp_error($term) && isset($term['term_id'])) {
						add_term_meta ($term['term_id'], 'type', 'new_ticket');
						add_term_meta ($term['term_id'], 'subject', __('Your ticket has been created successfully!','supportcandy'));
						add_term_meta ($term['term_id'], 'body', __('<p>Dear {customer_name},</p><p>Thank you for creating a ticket. We confirm that we have received your ticket and you will soon hear back from us.</p><p>Your ticket id is #{ticket_id}. You will get an email notification after we post a reply to your ticket. However, in case the email notification fails, then you can check your ticket status at the link below:</p><p>{ticket_url}</p>','supportcandy'));
						add_term_meta ($term['term_id'], 'recipients', array('customer'));
						add_term_meta ($term['term_id'], 'extra_recipients', array());
						add_term_meta ($term['term_id'], 'conditions', array());
					}
					$term = wp_insert_term( __('New ticket staff notification','supportcandy'), 'wpsc_en' );
					if (!is_wp_error($term) && isset($term['term_id'])) {
						add_term_meta ($term['term_id'], 'type','new_ticket');
						add_term_meta ($term['term_id'], 'subject', '{ticket_subject}');
						add_term_meta ($term['term_id'], 'body', __('<p>You have received new ticket from&nbsp;<strong>{customer_name}</strong>!</p><p>Below are details of the ticket:</p><p><strong>Subject:</strong>&nbsp;{ticket_subject}</p><p><strong>Description:</strong></p><p>{ticket_description}</p><p>{ticket_url}</p>','supportcandy'));
						add_term_meta ($term['term_id'], 'recipients', array('assigned_agent'));
						add_term_meta ($term['term_id'], 'extra_recipients', array());
						add_term_meta ($term['term_id'], 'conditions', array());
					}
					$term = wp_insert_term( __('Reply ticket notification','supportcandy'), 'wpsc_en' );
					if (!is_wp_error($term) && isset($term['term_id'])) {
						add_term_meta ($term['term_id'], 'type', 'ticket_reply');
						add_term_meta ($term['term_id'], 'subject', '{ticket_subject}');
						add_term_meta ($term['term_id'], 'body', __('<p><strong>{last_reply_user_name}</strong> wrote:</p><p>{last_reply}</p><p>{ticket_url}</p><p>{ticket_history}</p>','supportcandy'));
						add_term_meta ($term['term_id'], 'recipients', array('customer','assigned_agent'));
						add_term_meta ($term['term_id'], 'extra_recipients', array());
						add_term_meta ($term['term_id'], 'conditions', array());
					}
					$term = wp_insert_term( __('Close ticket customer notification','supportcandy'), 'wpsc_en' );
					if (!is_wp_error($term) && isset($term['term_id'])) {
						add_term_meta ($term['term_id'], 'type', 'change_status');
						add_term_meta ($term['term_id'], 'subject', __('Your ticket has been closed!','supportcandy'));
						add_term_meta ($term['term_id'], 'body', __('<p>Dear {customer_name},</p><p>Your ticket #{ticket_id} has been closed. We hope you are satisfied with our support.</p><p>If you have some further queries on this ticket, please feel free to reply same ticket. For queries not related to this ticket, please create new ticket.</p><p>{ticket_url}</p>','supportcandy'));
						add_term_meta ($term['term_id'], 'recipients', array('customer'));
						add_term_meta ($term['term_id'], 'extra_recipients', array());
						
						$close_status = get_option('wpsc_close_ticket_status');
						$status = get_term_by('slug', 'ticket_status', 'wpsc_ticket_custom_fields');
						$close_condition = array(
							$status->term_id => array($close_status)
						);
						add_term_meta ($term['term_id'], 'conditions', $close_condition );
					}

					update_option('wpsc_ticket_count','1');

					update_option('wpsc_allow_customer_close_ticket','1');
					update_option('wpsc_reply_form_position','1');

					update_option('wpsc_calender_date_format','yy-mm-dd');
					update_option('wpsc_attachment_max_filesize','20');
					update_option('wpsc_allow_guest_ticket','0');
					update_option('wpsc_powered_by','1');
					update_option('wpsc_ticket_alice','Ticket #');
					update_option('wpsc_captcha','1');

					$wpsc_thankyou_html = __("<p>Dear {customer_name},</p><p>We have received your ticket and confirmation has been sent to your email address&nbsp;{customer_email}.</p><p>Your ticket id is #{ticket_id}. You will get email notification after we post reply in your ticket but in case email notification failed, you can check your ticket status on below link:</p><p>{ticket_url}</p>",'supportcandy');
					update_option('wpsc_thankyou_html',$wpsc_thankyou_html);
					update_option('wpsc_thankyou_url','');

					update_option('wpsc_tl_agent_orderby', 'date_updated');
					update_option('wpsc_tl_agent_orderby_order','DESC');
					update_option('wpsc_tl_agent_no_of_tickets','20');
					update_option('wpsc_tl_agent_sort_type','meta_value_datetime');
					update_option('wpsc_tl_customer_orderby', 'date_updated');

					update_option('wpsc_tl_customer_orderby_order','DESC');
					update_option('wpsc_tl_customer_no_of_tickets','20');
					update_option('wpsc_tl_customer_sort_type','meta_value_datetime');

					update_option('wpsc_tl_agent_unresolve_statuses',array(3,4,5));
					update_option('wpsc_tl_customer_unresolve_statuses',array(3,4,5));

					// Agent Roles
					$agent_role = array(
						1 => array(
							'label' => __('Administrator','supportcandy'),
							'view_unassigned' => 1,
							'view_assigned_me' => 1,
							'view_assigned_others' => 1,
							'assign_unassigned' => 1,
							'assign_assigned_me' => 1,
							'assign_assigned_others' => 1,
							'cng_tkt_sts_unassigned' => 1,
							'cng_tkt_sts_assigned_me' => 1,
							'cng_tkt_sts_assigned_others' => 1,
							'cng_tkt_field_unassigned' => 1,
							'cng_tkt_field_assigned_me' => 1,
							'cng_tkt_field_assigned_others' => 1,
							'cng_tkt_ao_unassigned' => 1,
							'cng_tkt_ao_assigned_me' => 1,
							'cng_tkt_ao_assigned_others' => 1,
							'cng_tkt_rb_unassigned' => 1,
							'cng_tkt_rb_assigned_me' => 1,
							'cng_tkt_rb_assigned_others' => 1,
							'reply_unassigned' => 1,
							'reply_assigned_me' => 1,
							'reply_assigned_others' => 1,
							'delete_unassigned' => 1,
							'delete_assigned_me' => 1,
							'delete_assigned_others' => 1
						),
						2 => array(
							'label' => __('Agent','supportcandy'),
							'view_unassigned' => 1,
							'view_assigned_me' => 1,
							'view_assigned_others' => 0,
							'assign_unassigned' => 1,
							'assign_assigned_me' => 1,
							'assign_assigned_others' => 0,
							'cng_tkt_sts_unassigned' => 0,
							'cng_tkt_sts_assigned_me' => 1,
							'cng_tkt_sts_assigned_others' => 0,
							'cng_tkt_field_unassigned' => 0,
							'cng_tkt_field_assigned_me' => 1,
							'cng_tkt_field_assigned_others' => 0,
							'cng_tkt_ao_unassigned' => 0,
							'cng_tkt_ao_assigned_me' => 1,
							'cng_tkt_ao_assigned_others' => 0,
							'cng_tkt_rb_unassigned' => 0,
							'cng_tkt_rb_assigned_me' => 0,
							'cng_tkt_rb_assigned_others' => 0,
							'reply_unassigned' => 0,
							'reply_assigned_me' => 1,
							'reply_assigned_others' => 0,
							'delete_unassigned' => 0,
							'delete_assigned_me' => 0,
							'delete_assigned_others' => 0
						)
					);
					update_option('wpsc_agent_role',$agent_role);

				}

				if( $installed_version < '1.0.1' ) {
					$term = get_term_by('slug','date_created','wpsc_ticket_custom_fields');
					update_term_meta($term->term_id,'wpsc_ticket_filter_type','date');
					$term = get_term_by('slug','date_updated','wpsc_ticket_custom_fields');
					update_term_meta($term->term_id,'wpsc_ticket_filter_type','date');
					$date_fields = get_terms([
						'taxonomy'   => 'wpsc_ticket_custom_fields',
						'hide_empty' => false,
						'meta_query' => array(
							'relation' => 'AND',
							array(
								'key'     => 'wpsc_tf_type',
								'value'   => '6',
								'compare' => '=',
							)
						),
					]);
					foreach ($date_fields as $term) {
						update_term_meta($term->term_id,'wpsc_ticket_filter_type','date');
					}
				}

				if( $installed_version < '1.0.4' ) {
					$term = get_term_by('slug','ticket_priority','wpsc_ticket_custom_fields');
					update_term_meta($term->term_id,'agentonly','0');
					update_term_meta($term->term_id,'wpsc_tf_status','0');
					update_term_meta($term->term_id,'wpsc_tf_conditional','1');
					update_term_meta($term->term_id,'wpsc_tf_required','1');
					update_term_meta($term->term_id,'wpsc_tf_width','1/2');
					update_term_meta($term->term_id,'wpsc_tf_load_order','6');
				}

				if($installed_version < '1.0.6') {

					update_option('wpsc_reply_to_close_ticket','1');

					update_option('wpsc_terms_and_conditions','0');

					update_option( 'wpsc_default_login_setting','1');

					update_option('wpsc_set_in_gdpr','0');

					$wpsc_gdpr_html = __("I understand my personal information like Name, Email address, IP address etc will be stored in database.",'supportcandy');
					update_option('wpsc_gdpr_html',$wpsc_gdpr_html);

					// Appearance General Settings
					$wpsc_appearance_general_settings = array (

						'wpsc_bg_color'                                => '#FFFFFF',
						'wpsc_text_color'                              => '#000000',
						'wpsc_action_bar_color'                        => '#1C5D8A',
						'wpsc_crt_ticket_btn_action_bar_bg_color'      => '#FF5733',
						'wpsc_crt_ticket_btn_action_bar_text_color'    => '#FFFFFF',
						'wpsc_default_btn_action_bar_bg_color'         => '#FFFFFF',
						'wpsc_default_btn_action_bar_text_color'       => '#000000',
					);

					update_option('wpsc_appearance_general_settings',$wpsc_appearance_general_settings);

					// Appearance Ticket List Settings
					$wpsc_appearance_ticket_list = array (

						'wpsc_filter_widgets_bg_color'          => '#FFFFFF',
						'wpsc_filter_widgets_text_color'        => '#2C3E50',
						'wpsc_filter_widgets_border_color'      => '#C3C3C3',
						'wpsc_ticket_list_header_bg_color'      => '#424949',
						'wpsc_ticket_list_header_text_color'    => '#FFFFFF',
						'wpsc_ticket_list_item_mo_bg_color'     => '#FFFFFF',
						'wpsc_ticket_list_item_mo_text_color'   => '#2C3E50',
					);

					update_option('wpsc_appearance_ticket_list',$wpsc_appearance_ticket_list);

					// Appearance individual Ticket Page Settings
					$wpsc_appearance_individual_ticket_page = array (

						'wpsc_ticket_widgets_bg_color'                   => '#FFFFFF',
						'wpsc_ticket_widgets_text_color'                 => '#000000',
						'wpsc_ticket_widgets_border_color'               => '#C3C3C3',
						'wpsc_report_thread_bg_color'                    => '#FFFFFF',
						'wpsc_report_thread_text_color'                  => '#000000',
						'wpsc_report_thread_border_color'                => '#C3C3C3',
						'wpsc_reply_thread_bg_color'                     => '#FFFFFF',
						'wpsc_reply_thread_text_color'                   => '#000000',
						'wpsc_reply_thread_border_color'                 => '#C3C3C3',
						'wpsc_private_note_bg_color'                     => '#FEF9E7',
						'wpsc_private_note_text_color'                   => '#000000',
						'wpsc_private_note_border_color'                 => '#C3C3C3',
						'wpsc_ticket_logs_bg_color'                      => '#D6EAF8',
						'wpsc_ticket_logs_text_color'                    => '#000000',
						'wpsc_ticket_logs_border_color'                  => '#C3C3C3',
						'wpsc_submit_reply_btn_bg_color'                 => '#419641',
						'wpsc_submit_reply_btn_text_color'               => '#FFFFFF',
						'wpsc_submit_reply_btn_border_color'             => '#C3C3C3',
						'wpsc_other_reply_form_btn_bg_color'             => '#FFFFFF',
						'wpsc_other_reply_form_btn_text_color'           => '#000000',
						'wpsc_other_reply_form_btn_border_color'         => '#C3C3C3',
						'wpsc_edit_btn_bg_color'                         => '#FFFFFF',
						'wpsc_edit_btn_text_color'                       => '#000000',
						'wpsc_edit_btn_border_color'                     => '#C3C3C3',
					);

					update_option('wpsc_individual_ticket_page',$wpsc_appearance_individual_ticket_page);

					// Appearance  Create Ticket  Settings
					$wpsc_appearance_create_ticket = array (

						'wpsc_submit_button_bg_color'      => '#419641',
						'wpsc_submit_button_text_color'    => '#FFFFFF',
						'wpsc_submit_button_border_color'  => '#C3C3C3',
						'wpsc_reset_button_bg_color'       => '#FFFFFF',
						'wpsc_reset_button_text_color'     => '#000000',
						'wpsc_reset_button_border_color'   => '#C3C3C3',
						'wpsc_captcha_bg_color'            => '#B2BABB',
						'wpsc_captcha_text_color'          => '#000000',
						'wpsc_extra_info_text_color'       => '#000000',

					);

					update_option('wpsc_create_ticket',$wpsc_appearance_create_ticket);

					// Appearance  Madal Window  Settings
					$wpsc_appearance_modal_window = array (

						'wpsc_header_bg_color'          => '#0473AA',
						'wpsc_header_text_color'        => '#FFFFFF',
						'wpsc_footer_bg_color'          => '#F6F6F6',
						'wpsc_close_button_bg_color'    => '#AFAFAF',
						'wpsc_close_button_text_color'  => '#FFFFFF',
						'wpsc_action_button_bg_color'   => '#0473AA',
						'wpsc_action_button_text_color' => '#FFFFFF',

					);

					update_option('wpsc_modal_window',$wpsc_appearance_modal_window);


				}

				if($installed_version < '1.0.7') {

					//Appearance setting for signup form
					$wpsc_appearance_signup = array (

						'wpsc_appearance_signup_button_bg_color'      => '#419641',
						'wpsc_appearance_signup_button_text_color'    => '#FFFFFF',
						'wpsc_appearance_signup_button_border_color'  => '#C3C3C3',
						'wpsc_appearance_cancel_button_bg_color'      => '#FFFFFF',
						'wpsc_appearance_cancel_button_text_color'    => '#000000',
						'wpsc_appearance_cancel_button_border_color'  => '#C3C3C3',

					);
					update_option('wpsc_appearance_signup',$wpsc_appearance_signup);

					//Apperance Login Form color
					$wpsc_appearance_login_form = array(

						'wpsc_signin_button_bg_color'                => '#419641',
						'wpsc_signin_button_text_color'              => '#FFFFFF',
						'wpsc_signin_button_border_color'            => '#C3C3C3',
						'wpsc_register_now_button_bg_color'          => '#2aabd2',
						'wpsc_register_now_text_color'               => '#FFFFFF',
						'wpsc_register_now_button_border_color'      => '#28a4c9',
						'wpsc_continue_as_guest_button_bg_color'     => '#2aabd2',
						'wpsc_continue_as_guest_button_text_color'   => '#FFFFFF',
						'wpsc_continue_as_guest_button_border_color' => '#28a4c9',
					);
					update_option('wpsc_appearance_login_form',$wpsc_appearance_login_form);

					update_option('wpsc_allow_tinymce_in_guest_ticket','1');

					//Registration defaults
					$reg_allow = '0';
					$reg_type  = '1';
					if(get_option('users_can_register') && get_option('wpsc_current_version')){
						$reg_allow = '1';
						$reg_type  = '2';
					}
					update_option('wpsc_user_registration',$reg_allow);
					update_option('wpsc_user_registration_method',$reg_type);

				}

				if($installed_version < '1.0.8') {

					//Cron job default setting
					if(get_option('wpsc_current_version')) {
						update_option('wpsc_cron_job_schedule_setup',0);
					}else {
						update_option('wpsc_cron_job_schedule_setup',1);
					}

					// Appearance Ticket List Settings
					$ticket_list_app = get_option('wpsc_appearance_ticket_list');
					$ticket_filter_app = array (

						'wpsc_apply_filter_btn_bg_color'        => '#419641',
						'wpsc_apply_filter_btn_text_color'      => '#FFFFFF',
						'wpsc_apply_filter_btn_border_color'    => '#C3C3C3',
						'wpsc_save_filter_btn_bg_color'         => '#FFFFFF',
						'wpsc_save_filter_btn_text_color'       => '#000000',
						'wpsc_save_filter_btn_border_color'     => '#C3C3C3',
						'wpsc_close_filter_btn_bg_color'        => '#FFFFFF',
						'wpsc_close_filter_btn_text_color'      => '#000000',
						'wpsc_close_filter_btn_border_color'    => '#C3C3C3',
					);
					if($ticket_list_app){
						$ticket_filter_app = array_merge($ticket_filter_app, $ticket_list_app);
						update_option('wpsc_appearance_ticket_list',$ticket_filter_app);
					}
					
					//Bug fix of ticket widget. Remove and add wigets again.
					$wpsc_ticket_widgets = get_terms([
						'taxonomy'    => 'wpsc_ticket_widget',
						'hide_empty'  => false
					]);

					foreach($wpsc_ticket_widgets as $wpsc_ticket_widget){
						wp_delete_term($wpsc_ticket_widget->term_id,'wpsc_ticket_widget');
						delete_term_meta($wpsc_ticket_widget->term_id, 'wpsc_ticket_widget_load_order');
						delete_term_meta($wpsc_ticket_widget->term_id, 'wpsc_ticket_widget_type');
						delete_term_meta($wpsc_ticket_widget->term_id, 'wpsc_ticket_widget_role');
					}

					$agent_role_ids = array();
					$agent_role = get_option('wpsc_agent_role');
					if($agent_role){
						foreach ($agent_role as $key => $agent) {
							$agent_role_ids[] = $key;
						}
					}
					
					$customer_access= array();
					$customer_access = $agent_role_ids;
					$customer_access[] = 'customer';

					$term = wp_insert_term('Status', 'wpsc_ticket_widget' );
					if (!is_wp_error($term) && isset($term['term_id'])) {
						  add_term_meta ($term['term_id'], 'wpsc_label', __('Status','supportcandy'));
            	add_term_meta ($term['term_id'], 'wpsc_ticket_widget_load_order', '1');
							add_term_meta($term['term_id'], 'wpsc_ticket_widget_type', '1');
							add_term_meta($term['term_id'],'wpsc_ticket_widget_role', $customer_access);
					}

					$term = wp_insert_term('Raised By', 'wpsc_ticket_widget' );
					if (!is_wp_error($term) && isset($term['term_id'])) {
						  add_term_meta ($term['term_id'], 'wpsc_label', __('Raised By','supportcandy'));
							add_term_meta ($term['term_id'], 'wpsc_ticket_widget_load_order', '2');
							add_term_meta($term['term_id'], 'wpsc_ticket_widget_type', '1');
							add_term_meta($term['term_id'],'wpsc_ticket_widget_role', $agent_role_ids);
					}

					$term = wp_insert_term('Assign Agent', 'wpsc_ticket_widget' );
					if (!is_wp_error($term) && isset($term['term_id'])) {
						  add_term_meta ($term['term_id'], 'wpsc_label', __('Assign Agent','supportcandy'));
							add_term_meta ($term['term_id'], 'wpsc_ticket_widget_load_order', '3');
							add_term_meta($term['term_id'], 'wpsc_ticket_widget_type', '1');
							add_term_meta($term['term_id'],'wpsc_ticket_widget_role', $agent_role_ids);
					}

					$term = wp_insert_term('Ticket Fields', 'wpsc_ticket_widget' );
					if (!is_wp_error($term) && isset($term['term_id'])) {
					   	add_term_meta ($term['term_id'], 'wpsc_label', __('Ticket Fields','supportcandy'));
							add_term_meta ($term['term_id'], 'wpsc_ticket_widget_load_order', '4');
							add_term_meta($term['term_id'], 'wpsc_ticket_widget_type', '1');
							add_term_meta($term['term_id'],'wpsc_ticket_widget_role',$customer_access);
					}

					$term = wp_insert_term('Agent Only Fields', 'wpsc_ticket_widget' );
					if (!is_wp_error($term) && isset($term['term_id'])) {
						  add_term_meta ($term['term_id'], 'wpsc_label', __('Agent Only Fields','supportcandy'));
							add_term_meta ($term['term_id'], 'wpsc_ticket_widget_load_order', '5');
							add_term_meta($term['term_id'], 'wpsc_ticket_widget_type', '1');
							add_term_meta($term['term_id'],'wpsc_ticket_widget_role', $agent_role_ids);
					}

					update_option( 'wpsc_personal_data_retention_type','disable');
					update_option( 'wpsc_personal_data_retention_period_time','0');
					update_option( 'wpsc_personal_data_retention_period_unit','days');

					//mark all custom fields as non personal field
					$fields = get_terms([
		      	'taxonomy'   => 'wpsc_ticket_custom_fields',
		      	'hide_empty' => false,
		      	'orderby'    => 'meta_value_num',
		      	'meta_key'	 => 'wpsc_tf_load_order',
		      	'order'    	 => 'ASC',
		      	'meta_query' => array(
		      		array(
		            'key'       => 'agentonly',
		            'value'     => array(0,1),
		            'compare'   => 'IN'
		          )
		      	),
		      ]);
					foreach ($fields as $key => $field) {
						update_term_meta( $field->term_id,'wpsc_tf_personal_info',0);
					}

				}

				if($installed_version < '1.1.0') {

					update_option( 'wpsc_ticket_url_permission','1');

					update_option('wpsc_sign_out',1);

					$wpsc_appearance_general_settings = get_option('wpsc_appearance_general_settings');
					$wpsc_sign_out_general_settings = array (
						'wpsc_sign_out_bg_color'                       => '#FF5733',
						'wpsc_sign_out_text_color'                     => '#FFFFFF',
					);
					if($wpsc_appearance_general_settings ){
						$wpsc_appearance_general_settings = array_merge($wpsc_appearance_general_settings,$wpsc_sign_out_general_settings);
						update_option('wpsc_appearance_general_settings',$wpsc_appearance_general_settings);	
					}

				}

				if($installed_version  < '1.1.1'){
					update_option('wpsc_guest_can_upload_files',1);
				}

				if ($installed_version < '1.1.2') {

					update_option('wpsc_recaptcha_type', 1 );

					$term_page = get_option('wpsc_term_page_id');

					if($term_page){
						$term_url = get_permalink($term_page);
						$wpsc_tc_html = sprintf(__('I agree the %1$s','supportcandy'),'<a href="'.$term_url.'" target="_blank">Terms & Conditions.</a>');
					}else {
						$wpsc_tc_html = __("I agree the Terms & Conditions.",'supportcandy');
					}
					update_option('wpsc_terms_and_conditions_html', $wpsc_tc_html);
				}

				if ($installed_version < '1.1.3') {
					update_option('wpsc_ticket_public_mode', 0);
					update_option('wpsc_show_and_hide_filters',1);
				}

				if ($installed_version < '1.1.4') {

					update_option('wpsc_allow_reply_confirmation',1);
					update_option('wpsc_custom_ticket_count',1);
					update_option('wpsc_thread_date_format','string') ;

					$wpsc_tinymce_toolbar  = array (
						'bold'                     => array( 'name' =>__('Bold','supportcandy'),'value'=>'bold'),
		       	'italic'             			 => array( 'name' =>__('Italic','supportcandy'),'value'=>'italic'),
						'underline'								 => array( 'name' =>__('Underline','supportcandy'),'value'=>'underline'),
					  'blockquote'               => array( 'name' =>__('Blockquote','supportcandy'),'value'=>'blockquote'),
						'align'                    => array( 'name' =>__('Align','supportcandy'),'value'=>'alignleft aligncenter alignright'),
						'bulleted_list'        		 => array( 'name' =>__('Bulleted list','supportcandy'),'value'=>'bullist'),
						'numbered_list'            => array( 'name' =>__('Numbered list','supportcandy'),'value'=>'numlist'),
						'right_to_left'					   => array( 'name' =>__('Right to left','supportcandy'),'value'=>'rtl'),
						'link'                     => array( 'name' =>__('Link','supportcandy'),'value'=>'link'),
						'image'                    => array( 'name' =>__('Image','supportcandy'),'value'=>'image')

					);
					$wpsc_tinymce_toolbar_active  = array ('bold','italic','underline','blockquote','align','bulleted_list','numbered_list','right_to_left','link','image');
					update_option('wpsc_tinymce_toolbar',$wpsc_tinymce_toolbar);
					update_option('wpsc_tinymce_toolbar_active',$wpsc_tinymce_toolbar_active);

				}

				if($installed_version < '1.1.5'){
					update_option('wpsc_thread_date_time_format','Y/m/d H:i:s');
					update_option('wpsc_do_not_notify_setting',1);


					$appearance_settings = get_option('wpsc_individual_ticket_page');
					$appearance          = array (
						'wpsc_reply_thread_customer_bg_color'						 => $appearance_settings['wpsc_reply_thread_bg_color'],
						'wpsc_reply_thread_customer_text_color'		       => $appearance_settings['wpsc_reply_thread_text_color'],
						'wpsc_reply_thread_customer_border_color'	       => $appearance_settings['wpsc_reply_thread_border_color'],
					);
					if($appearance_settings){
						$wpsc_appearance_general_settings = array_merge($appearance_settings,$appearance);
						update_option('wpsc_individual_ticket_page',$wpsc_appearance_general_settings);
					}
					update_option('wpsc_reg_guest_user_after_create_ticket',0);
				}

				if( $installed_version < '2.0.0' ){

						update_option('wpsc_ticket_id_type',1);
						update_option('wpsc_hide_show_priority',1);

						$wpsc_allow_attachment  = array ('create','reply');
						update_option('wpsc_allow_attachment',$wpsc_allow_attachment);

						$term_data = get_term_by('slug' , 'ticket_status' ,'wpsc_ticket_custom_fields' );
						if ($term_data) {
							add_term_meta($term_data->term_id ,'wpsc_allow_orderby' , '1' );
						}

						$term_data =  get_term_by( 'slug' , 'ticket_priority' ,'wpsc_ticket_custom_fields' );
						if ( $term_data ) {
							add_term_meta( $term_data->term_id ,'wpsc_allow_orderby' , '1' );
						}

						$term_data = get_term_by( 'slug' , 'ticket_category', 'wpsc_ticket_custom_fields' );
						if( $term_data ){
							add_term_meta( $term_data->term_id ,'wpsc_allow_orderby' , '1');
						}

						$term_data = get_term_by( 'slug' , 'customer_name', 'wpsc_ticket_custom_fields' );
						if( $term_data ){
							add_term_meta( $term_data->term_id ,'wpsc_allow_orderby' , '1');
						}

						$term_data = get_term_by( 'slug' , 'customer_email', 'wpsc_ticket_custom_fields' );
						if( $term_data ){
							add_term_meta( $term_data->term_id ,'wpsc_allow_orderby' , '1');
						}

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
								array(
			            'key'       => 'wpsc_tf_type',
			            'value'     => 8,
			            'compare'   => '='
			          ),
			      	),
			      ]);
						foreach ($fields as $key => $field) {
							update_term_meta( $field->term_id ,'wpsc_allow_orderby' , '1');
						}

						$wpsc_allow_attachment  = array ('create','reply');
						update_option('wpsc_allow_attachment',$wpsc_allow_attachment);
						update_option('wpsc_hide_show_priority',1);
						update_option('wpsc_default_do_not_notify_option',1);
						update_option('wpsc_view_more',1);

				}

				if( $installed_version < '2.0.1' ){

						update_option( 'wpsc_thread_limit', 5 );

						$email_templates = get_terms([
							'taxonomy'   => 'wpsc_en',
							'hide_empty' => false,
							'orderby'    => 'ID',
							'order'      => 'ASC',
						]);

						if($email_templates){	

							foreach ( $email_templates as $email_template ) {

								$conditions     = get_term_meta( $email_template->term_id, 'conditions', true );
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
								update_term_meta( $email_template->term_id ,'conditions' , $new_conditions);

						}
					}

				}

				if( $installed_version < '2.0.3' ){
						$secret_key = uniqid().uniqid();
						update_option('wpsc_rest_api',0);
						update_option('wpsc_rest_api_secret_key',$secret_key);

						$reply_to_close_ticket = get_option( 'wpsc_reply_to_close_ticket');
						$wpsc_allow_reply_to_close_ticket = array();
						$wpsc_allow_reply_to_close_ticket[] = 'agents';
						if($reply_to_close_ticket){
							$wpsc_allow_reply_to_close_ticket[] = 'customer';
						}
						update_option( 'wpsc_allow_reply_to_close_ticket', $wpsc_allow_reply_to_close_ticket);
						update_option('wpsc_image_download_method',1);
						update_option('wpsc_on_and_off_auto_refresh',0);

						$term = wp_insert_term( 'date_closed', 'wpsc_ticket_custom_fields' );
						if (!is_wp_error($term) && isset($term['term_id'])) {
							add_term_meta ($term['term_id'], 'wpsc_tf_label', __('Date Closed','supportcandy'));
							add_term_meta ($term['term_id'], 'agentonly', '2');
							add_term_meta ($term['term_id'], 'wpsc_tf_type', '0');
							add_term_meta ($term['term_id'], 'wpsc_allow_ticket_list', '1');
							add_term_meta ($term['term_id'], 'wpsc_customer_ticket_list_status', '0');
							add_term_meta ($term['term_id'], 'wpsc_agent_ticket_list_status', '0');
							add_term_meta ($term['term_id'], 'wpsc_allow_ticket_filter', '1');
							add_term_meta ($term['term_id'], 'wpsc_ticket_filter_type', 'date');
							add_term_meta ($term['term_id'], 'wpsc_customer_ticket_filter_status', '0');
							add_term_meta ($term['term_id'], 'wpsc_agent_ticket_filter_status', '0');
							add_term_meta ($term['term_id'], 'wpsc_tl_customer_load_order', '10');
							add_term_meta ($term['term_id'], 'wpsc_tl_agent_load_order', '10');
							add_term_meta ($term['term_id'], 'wpsc_filter_customer_load_order', '6');
							add_term_meta ($term['term_id'], 'wpsc_filter_agent_load_order', '6');
							add_term_meta ($term['term_id'], 'wpsc_allow_orderby', '1');
						}

						global $wpdb;
						$agent_role_ids = array();
						$agent_role = get_option('wpsc_agent_role');
						if($agent_role ){
							foreach ($agent_role as $key => $agent) {
								$agent_role_ids[] = $key;
							}
						}

						$customer_access= array();
						$customer_access = $agent_role_ids;
						$customer_access[] = 'customer';

						$term = wp_insert_term('additional-recepients', 'wpsc_ticket_widget' );
						if (!is_wp_error($term) && isset($term['term_id'])) {
								add_term_meta ($term['term_id'], 'wpsc_label', __('Recipients','supportcandy'));
								add_term_meta ($term['term_id'], 'wpsc_ticket_widget_load_order', '10');
								add_term_meta($term['term_id'], 'wpsc_ticket_widget_type', '1');
								add_term_meta($term['term_id'],'wpsc_ticket_widget_role', $customer_access);
						}

						update_option('wpsc_redirect_to_ticket_list', '0');

						$agent_role = get_option('wpsc_agent_role');
						if($agent_role){
							foreach ($agent_role as $key => $capabilities){
								$capabilities['view_unassigned_private_note'] = 1;
								$capabilities['view_assigned_me_private_note'] = 1;
								$capabilities['view_assigned_others_private_note'] = 1;
								$agent_role[$key] = $capabilities;
								update_option('wpsc_agent_role',$agent_role);
							}
						}
						

						/**
						* Update translation option for custom fields label in order to support WPML
						*/

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
			      	),
			      ]);

						$custom_fields_localize = get_option('wpsc_custom_fields_localize');
						if (!$custom_fields_localize) {
							$custom_fields_localize = array();
						}

						$custom_fields_extra_info = get_option('wpsc_custom_fields_extra_info');
						if (!$custom_fields_extra_info) {
							 $custom_fields_extra_info = array();
						}

						foreach($fields as $custom_fields){
							$label = get_term_meta( $custom_fields->term_id, 'wpsc_tf_label', true);
							$custom_fields_localize['custom_fields_'.$custom_fields->term_id] = $label;
							update_option('wpsc_custom_fields_localize', $custom_fields_localize);

							$extra_info = get_term_meta( $custom_fields->term_id, 'wpsc_tf_extra_info', true);
							$custom_fields_extra_info['custom_fields_extra_info_' . $custom_fields->term_id] = $extra_info;
							update_option('wpsc_custom_fields_extra_info', $custom_fields_extra_info);
						}

						$email_templates = get_terms([
							'taxonomy'   => 'wpsc_en',
							'hide_empty' => false,
							'orderby'    => 'ID',
							'order'      => 'ASC',
						]);

						$email_subject = get_option('wpsc_email_notification_subject');
						if (!$email_subject) {
							 $email_subject = array();
						}

						$email_body = get_option('wpsc_email_notification_body');
						if (!$email_body) {
							 $email_body = array();
						}

						foreach ($email_templates as $template ) {
							$subject = get_term_meta($template->term_id,'subject',true);
							$body		 = get_term_meta($template->term_id,'body',true);

							$email_subject['email_subject_' . $template->term_id] = $subject;
							update_option('wpsc_email_notification_subject', $email_subject);

							$email_body['email_body_' . $template->term_id] = $body;
							update_option('wpsc_email_notification_body', $email_body);
						}

						$statuses = get_terms([
							'taxonomy'   => 'wpsc_statuses',
							'hide_empty' => false,
							'orderby'    => 'meta_value_num',
							'order'    	 => 'ASC',
							'meta_query' => array('order_clause' => array('key' => 'wpsc_status_load_order')),
						]);

						$wpsc_custom_status_localize =  get_option('wpsc_custom_status_localize');
						if(!$wpsc_custom_status_localize){
							$wpsc_custom_status_localize = array();
						}

						foreach ($statuses as $status) {
							$wpsc_custom_status_localize['custom_status_'.$status->term_id] = $status->name;
							update_option('wpsc_custom_status_localize', $wpsc_custom_status_localize);
						}

						$categories = get_terms([
							'taxonomy'   => 'wpsc_categories',
							'hide_empty' => false,
							'orderby'    => 'meta_value_num',
							'order'    	 => 'ASC',
							'meta_query' => array('order_clause' => array('key' => 'wpsc_category_load_order')),
						]);

						$wpsc_custom_category_localize =  get_option('wpsc_custom_category_localize');
						if(!$wpsc_custom_category_localize){
							$wpsc_custom_category_localize = array();
						}

						foreach ($categories as $category) {
							$wpsc_custom_category_localize['custom_category_'.$category->term_id] = $category->name;
							update_option('wpsc_custom_category_localize', $wpsc_custom_category_localize);
						}


						$priorities = get_terms([
							'taxonomy'   => 'wpsc_priorities',
							'hide_empty' => false,
							'orderby'    => 'meta_value_num',
							'order'    	 => 'ASC',
							'meta_query' => array('order_clause' => array('key' => 'wpsc_priority_load_order')),
						]);

						$wpsc_custom_priority_localize =  get_option('wpsc_custom_priority_localize');
						if(!$wpsc_custom_priority_localize){
							$wpsc_custom_priority_localize = array();
						}

						foreach ($priorities as $priority) {
							$wpsc_custom_priority_localize['custom_priority_'.$priority->term_id] = $priority->name;
							update_option('wpsc_custom_priority_localize', $wpsc_custom_priority_localize);
						}

						$wpsc_custom_widget_localize = get_option('wpsc_custom_widget_localize');
						if(!$wpsc_custom_widget_localize){
							$wpsc_custom_widget_localize = array();
						}

						$ticket_widgets = get_terms([
							'taxonomy'   => 'wpsc_ticket_widget',
							'hide_empty' => false,
						  'orderby'    => 'meta_value_num',
						  'order'    	 => 'ASC',
						  'meta_query' => array('order_clause' => array('key' => 'wpsc_ticket_widget_load_order')),
						]);

						foreach ($ticket_widgets as $widget) {
							$wpsc_custom_widget_localize['custom_widget_'.$widget->term_id] = get_term_meta($widget->term_id, 'wpsc_label', true);;
							update_option('wpsc_custom_widget_localize', $wpsc_custom_widget_localize);
						}
				}

				if($installed_version < '2.0.5'){
					$ticket_history_all = get_term_by('slug','ticket_history_all','wpsc_ticket_custom_fields');
					if(!$ticket_history_all){
						$term = wp_insert_term( 'ticket_history_all', 'wpsc_ticket_custom_fields' );
						if (!is_wp_error($term) && isset($term['term_id'])) {
							add_term_meta ($term['term_id'], 'wpsc_tf_label', __('All threads in decending order','supportcandy'));
							add_term_meta ($term['term_id'], 'agentonly', '2');
							add_term_meta ($term['term_id'], 'wpsc_tf_type', '0');
							add_term_meta ($term['term_id'], 'wpsc_allow_ticket_list', '0');
							add_term_meta ($term['term_id'], 'wpsc_allow_ticket_filter', '0');
						}
					}

					$ticket_history_all_with_notes = get_term_by('slug','ticket_history_all_with_notes','wpsc_ticket_custom_fields');
					if(!$ticket_history_all_with_notes){
						$term = wp_insert_term( 'ticket_history_all_with_notes', 'wpsc_ticket_custom_fields' );
						if (!is_wp_error($term) && isset($term['term_id'])) {
							add_term_meta ($term['term_id'], 'wpsc_tf_label', __('All threads in decending order with private note','supportcandy'));
							add_term_meta ($term['term_id'], 'agentonly', '2');
							add_term_meta ($term['term_id'], 'wpsc_tf_type', '0');
							add_term_meta ($term['term_id'], 'wpsc_allow_ticket_list', '0');
							add_term_meta ($term['term_id'], 'wpsc_allow_ticket_filter', '0');
						}
					}

					$ticket_notes_history = get_term_by('slug','ticket_notes_history','wpsc_ticket_custom_fields');
					if(!$ticket_notes_history){
						$term = wp_insert_term( 'ticket_notes_history', 'wpsc_ticket_custom_fields' );
						if (!is_wp_error($term) && isset($term['term_id'])) {
							add_term_meta ($term['term_id'], 'wpsc_tf_label', __('All private notes in decending order','supportcandy'));
							add_term_meta ($term['term_id'], 'agentonly', '2');
							add_term_meta ($term['term_id'], 'wpsc_tf_type', '0');
							add_term_meta ($term['term_id'], 'wpsc_allow_ticket_list', '0');
							add_term_meta ($term['term_id'], 'wpsc_allow_ticket_filter', '0');
						}
					}
				}

                if ($installed_version < '2.0.6') {

                    // Auto delete ticket
                    update_option('wpsc_auto_delete_ticket', '0');
                    update_option('wpsc_auto_delete_ticket_time', '1');
                    update_option('wpsc_auto_delete_ticket_time_period_unit', 'days');

                    $wpsc_allow_attachment_type = "jpg, jpeg, png, gif, pdf, doc, docx, ppt, pptx, pps, ppsx, odt, xls, xlsx, mp3, m4a, ogg, wav, mp4, m4v, mov, wmv, avi, mpg, ogv, 3gp, 3g2, zip, eml";
                    update_option('wpsc_allow_attachment_type', $wpsc_allow_attachment_type);

                    //input limit
                    $fields1 = get_terms([
                        'taxonomy'   => 'wpsc_ticket_custom_fields',
                        'hide_empty' => false,
                        'orderby'    => 'meta_value_num',
                        'meta_key'	 => 'wpsc_tf_load_order',
                        'order'    	 => 'ASC'
                    ]);
                    foreach ($fields1 as $key => $term) {
                        update_term_meta($term->term_id, 'wpsc_tf_limit', '0');
                    }

                    $term = wp_insert_term('user_type', 'wpsc_ticket_custom_fields');
                    if (!is_wp_error($term) && isset($term['term_id'])) {
                        add_term_meta($term['term_id'], 'wpsc_tf_label', __('User Type', 'supportcandy'));
                        add_term_meta($term['term_id'], 'agentonly', '2');
                        add_term_meta($term['term_id'], 'wpsc_tf_type', '0');
                        add_term_meta($term['term_id'], 'wpsc_allow_ticket_list', '1');
                        add_term_meta($term['term_id'], 'wpsc_customer_ticket_list_status', '0');
                        add_term_meta($term['term_id'], 'wpsc_agent_ticket_list_status', '0');
                        add_term_meta($term['term_id'], 'wpsc_allow_ticket_filter', '1');
                        add_term_meta($term['term_id'], 'wpsc_ticket_filter_type', 'string');
                        add_term_meta($term['term_id'], 'wpsc_customer_ticket_filter_status', '0');
                        add_term_meta($term['term_id'], 'wpsc_agent_ticket_filter_status', '0');
                    }

                    $agent_role_ids = array();
                    $term = wp_insert_term('biographical-info', 'wpsc_ticket_widget');
                    if (!is_wp_error($term) && isset($term['term_id'])) {
                        add_term_meta($term['term_id'], 'wpsc_label', __('Bio', 'supportcandy'));
                        add_term_meta($term['term_id'], 'wpsc_ticket_widget_load_order', '11');
                        add_term_meta($term['term_id'], 'wpsc_ticket_widget_type', '1');
                        add_term_meta($term['term_id'], 'wpsc_ticket_widget_role', $agent_role_ids);
                        $wpsc_custom_widget_localize = get_option('wpsc_custom_widget_localize');
                        $wpsc_custom_widget_localize['custom_widget_'.$term['term_id']] = get_term_meta($term['term_id'], 'wpsc_label', true);
                        ;
                        update_option('wpsc_custom_widget_localize', $wpsc_custom_widget_localize);
                    }

                    $agent_role = get_option('wpsc_agent_role');
                    if ($agent_role) {
                    	foreach ($agent_role as $key => $capabilities) {
                        	if ($key == 1) {
                            	$capabilities['edit_delete_unassigned'] = 1;
                            	$capabilities['edit_delete_assigned_me'] = 1;
                            	$capabilities['edit_delete_assigned_others'] = 1;
							} else {
                            	$capabilities['edit_delete_unassigned'] = 0;
                            	$capabilities['edit_delete_assigned_me'] = 0;
                            	$capabilities['edit_delete_assigned_others'] = 0;
                        	}

                        	$agent_role[$key] = $capabilities;
                        	update_option('wpsc_agent_role', $agent_role);
                    	}
					}
					
					$agent_role = get_option('wpsc_agent_role');
					if($agent_role){
						foreach ($agent_role as $key => $capabilities){

							$capabilities['view_unassigned_log'] = 1;
							$capabilities['view_assigned_me_log'] = 1;
							$capabilities['view_assigned_others_log'] = 1;

							$agent_role[$key] = $capabilities;
							update_option('wpsc_agent_role',$agent_role);
						}
					}
				}
				
				if($installed_version < '2.0.7'){
					
					$term = wp_insert_term( 'previously_assigned_agent', 'wpsc_ticket_custom_fields' );
					if (!is_wp_error($term) && isset($term['term_id'])) {
						add_term_meta ($term['term_id'], 'wpsc_tf_label', __('Previously Assigned Agent','supportcandy'));
						add_term_meta ($term['term_id'], 'agentonly', '2');
						add_term_meta ($term['term_id'], 'wpsc_tf_type', '0');
						add_term_meta ($term['term_id'], 'wpsc_allow_ticket_list', '0');
						add_term_meta ($term['term_id'], 'wpsc_allow_ticket_filter', '0');
					}

					update_option('wpsc_en_send_mail_count','5');
				}

				if($installed_version < '2.0.9'){
					global $wpdb;

					update_option('wpsc_login_captcha','0');

					$wpdb->query("ALTER TABLE ".$wpdb->prefix."wpsc_email_notification MODIFY COLUMN bcc_email longtext");

					$upload_dir = wp_upload_dir();
					if (!file_exists($upload_dir['basedir'] . '/wpsc/')) {
						mkdir($upload_dir['basedir'] . '/wpsc/', 0755, true);
					}
					$data = '<?php' . PHP_EOL . '//Silence is golden';

					$fh = fopen($upload_dir['basedir'] . '/wpsc/' . "/index.php", "w");
					fwrite($fh, $data);
					fclose($fh);

					$close_ticket_status = get_option('wpsc_close_ticket_status');
					update_option('wpsc_close_ticket_group',array($close_ticket_status));

					update_option('wpsc_reply_bcc_visibility','1');
				}

				if($installed_version < '2.1.0'){
					
					update_option('wpsc_email_sending_method','0');					
					
                    $wpsc_allow_attach_create_ticket = get_option( 'wpsc_allow_attach_create_ticket');
					$wpsc_guest_can_upload_files     =  get_option('wpsc_guest_can_upload_files');
					if (!$wpsc_allow_attach_create_ticket){
						$wpsc_allow_attach_create_ticket = array();
				    }
				    $wpsc_allow_attach_create_ticket[] = 'agents';
					$wpsc_allow_attach_create_ticket[] = 'customers';
				    if ($wpsc_guest_can_upload_files){
						$wpsc_allow_attach_create_ticket[] = 'guests';	
					 }
					update_option('wpsc_allow_attach_create_ticket',$wpsc_allow_attach_create_ticket);
					
                    $wpsc_allow_attach_reply_form = get_option( 'wpsc_allow_attach_reply_form');
					if (!$wpsc_allow_attach_reply_form){
					    $wpsc_allow_attach_reply_form = array();
					}
					$wpsc_allow_attach_reply_form[] = 'agents';
					$wpsc_allow_attach_reply_form[] = 'customers';
					if ($wpsc_guest_can_upload_files){
						$wpsc_allow_attach_reply_form[] = 'guests';	
					}
					update_option('wpsc_allow_attach_reply_form',$wpsc_allow_attach_reply_form);

					$wpsc_allow_rich_text_editor_for_guest = get_option('wpsc_allow_tinymce_in_guest_ticket');

					$wpsc_allow_rich_text_editor = get_option( 'wpsc_allow_rich_text_editor');
					$wpsc_allow_rich_text_editor = array();
					
					$wpsc_allow_rich_text_editor[] = 'register_user';
					if($wpsc_allow_rich_text_editor_for_guest){
						$wpsc_allow_rich_text_editor[] = 'guest_user';
					}
					update_option( 'wpsc_allow_rich_text_editor', $wpsc_allow_rich_text_editor);

					$ticket_history_all_with_logs = get_term_by('slug','ticket_history_all_with_logs','wpsc_ticket_custom_fields');
					if(!$ticket_history_all_with_logs){
						$term = wp_insert_term( 'ticket_history_all_with_logs', 'wpsc_ticket_custom_fields' );
						if (!is_wp_error($term) && isset($term['term_id'])) {
							add_term_meta ($term['term_id'], 'wpsc_tf_label', __('All threads in descending order with logs','supportcandy'));
							add_term_meta ($term['term_id'], 'agentonly', '2');
							add_term_meta ($term['term_id'], 'wpsc_tf_type', '0');
							add_term_meta ($term['term_id'], 'wpsc_allow_ticket_list', '0');
							add_term_meta ($term['term_id'], 'wpsc_allow_ticket_filter', '0');
						}
					}

					$ticket_history_all_with_notes_and_logs = get_term_by('slug','ticket_history_all_with_notes_and_logs','wpsc_ticket_custom_fields');
					if(!$ticket_history_all_with_notes_and_logs){
						$term = wp_insert_term( 'ticket_history_all_with_notes_and_logs', 'wpsc_ticket_custom_fields' );
						if (!is_wp_error($term) && isset($term['term_id'])) {
							add_term_meta ($term['term_id'], 'wpsc_tf_label', __('All threads in descending order with private note and logs','supportcandy'));
							add_term_meta ($term['term_id'], 'agentonly', '2');
							add_term_meta ($term['term_id'], 'wpsc_tf_type', '0');
							add_term_meta ($term['term_id'], 'wpsc_allow_ticket_list', '0');
							add_term_meta ($term['term_id'], 'wpsc_allow_ticket_filter', '0');
						}
					}

					$ticket_notes_history_with_logs = get_term_by('slug','ticket_notes_history_with_logs','wpsc_ticket_custom_fields');
					if(!$ticket_notes_history_with_logs){
						$term = wp_insert_term( 'ticket_notes_history_with_logs', 'wpsc_ticket_custom_fields' );
						if (!is_wp_error($term) && isset($term['term_id'])) {
							add_term_meta ($term['term_id'], 'wpsc_tf_label', __('All private notes in descending order with logs','supportcandy'));
							add_term_meta ($term['term_id'], 'agentonly', '2');
							add_term_meta ($term['term_id'], 'wpsc_tf_type', '0');
							add_term_meta ($term['term_id'], 'wpsc_allow_ticket_list', '0');
							add_term_meta ($term['term_id'], 'wpsc_allow_ticket_filter', '0');
						}
					}
					
				}
				
				if($installed_version < '2.1.1'){

					$wpsc_allow_attachment_type = get_option('wpsc_allow_attachment_type');
					$max_attachment_limit = get_option('wpsc_attachment_max_filesize');
	                $attach_notice = sprintf( __('You can upload files maximum size %1$s mb of types %2$s.','supportcandy'),$max_attachment_limit, $wpsc_allow_attachment_type);
					update_option('wpsc_attachment_notice', $attach_notice);

					update_option('wpsc_allow_datepickerjs_files','0');

				}

				if($installed_version < '2.1.2'){

					update_option('wpsc_allow_html_pasting','0');
				}

				if($installed_version < '2.1.5'){
					update_option('wpsc_allow_reply_to_public_tickets','1');
				}

				if( $installed_version < '2.1.7' ){
										
					$term = wp_insert_term( 'customer_first_name', 'wpsc_ticket_custom_fields' );
					if (!is_wp_error($term) && isset($term['term_id'])) {
						add_term_meta ($term['term_id'], 'wpsc_tf_label', __('Customer first name','supportcandy'));
						add_term_meta ($term['term_id'], 'agentonly', '2');
						add_term_meta ($term['term_id'], 'wpsc_tf_type', '0');
						add_term_meta ($term['term_id'], 'wpsc_allow_ticket_list', '0');
						add_term_meta ($term['term_id'], 'wpsc_allow_ticket_filter', '0');
					}

					$term = wp_insert_term( 'last_reply_user_first_name', 'wpsc_ticket_custom_fields' );
					if (!is_wp_error($term) && isset($term['term_id'])) {
						add_term_meta ($term['term_id'], 'wpsc_tf_label', __('Last reply user first name','supportcandy'));
						add_term_meta ($term['term_id'], 'agentonly', '2');
						add_term_meta ($term['term_id'], 'wpsc_tf_type', '0');
						add_term_meta ($term['term_id'], 'wpsc_allow_ticket_list', '0');
						add_term_meta ($term['term_id'], 'wpsc_allow_ticket_filter', '0');
					}
	
					$term = wp_insert_term( 'current_user_first_name', 'wpsc_ticket_custom_fields' );
					if (!is_wp_error($term) && isset($term['term_id'])) {
						add_term_meta ($term['term_id'], 'wpsc_tf_label', __('Current user first name','supportcandy'));
						add_term_meta ($term['term_id'], 'agentonly', '2');
						add_term_meta ($term['term_id'], 'wpsc_tf_type', '0');
						add_term_meta ($term['term_id'], 'wpsc_allow_ticket_list', '0');
						add_term_meta ($term['term_id'], 'wpsc_allow_ticket_filter', '0');
					}

					$term = wp_insert_term( 'last_note_user_first_name', 'wpsc_ticket_custom_fields' );
					if (!is_wp_error($term) && isset($term['term_id'])) {
						add_term_meta ($term['term_id'], 'wpsc_tf_label', __('Last note user first name','supportcandy'));
						add_term_meta ($term['term_id'], 'agentonly', '2');
						add_term_meta ($term['term_id'], 'wpsc_tf_type', '0');
						add_term_meta ($term['term_id'], 'wpsc_allow_ticket_list', '0');
						add_term_meta ($term['term_id'], 'wpsc_allow_ticket_filter', '0');
					}
				}
				update_option( 'wpsc_current_version', WPSC_VERSION );
			}
		}

endif;

new WPSC_Install();
