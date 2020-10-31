<?php
/**
 * Plugin Name: SupportCandy - Usergroup
 * Plugin URI:  https://supportcandy.net/
 * Description: Usergroup for SupportCandy
 * Version: 2.0.6
 * Author: Support Candy
 * Author URI:  https://supportcandy.net/
 * Text Domain: wpsc-usergroup
 * Domain Path: /lang
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

final class WPSC_USERGROUP {

		public $version = '2.0.6';

		public function __construct() {

				$this->define_constants();
				$this->includes();
				add_action( 'init', array($this,'load_textdomain') );
				register_activation_hook(__FILE__,array($this,'activation'));
				register_deactivation_hook( __FILE__, array($this,'deactivate') );
		}

		function define_constants() {
				$this->define('WPSC_USERGROUP_FILE', __FILE__);
				$this->define('WPSC_USERGROUP_ABSPATH', dirname(__FILE__) . '/');
				$this->define('WPSC_USERGROUP_URL', plugin_dir_url( __FILE__ ) );
				$this->define('WPSC_USERGROUP_BASENAME', plugin_basename(__FILE__));
				$this->define('WPSC_USERGROUP_STORE_ID', '198');
				$this->define('WPSC_USERGROUP_VERSION', $this->version);
		}

		function load_textdomain(){
				$locale = apply_filters( 'plugin_locale', get_locale(), 'wpsc-usergroup' );
				load_textdomain( 'wpsc-usergroup', WP_LANG_DIR . '/wpsc/wpsc-usergroup-' . $locale . '.mo' );
				load_plugin_textdomain( 'wpsc-usergroup', false, plugin_basename( dirname( __FILE__ ) ) . '/lang' );
		}

		public function includes() {

				include_once( WPSC_USERGROUP_ABSPATH . 'includes/class-wpsc-usergroup-install.php' );
				include_once( WPSC_USERGROUP_ABSPATH . 'includes/class-wpsc-usergroup_ajax.php' );
				include_once( WPSC_USERGROUP_ABSPATH . 'includes/class-wpsc-usergroup-admin.php' );
				include_once( WPSC_USERGROUP_ABSPATH . 'includes/class-wpsc-usergroup-frontend.php' );
				include_once( WPSC_USERGROUP_ABSPATH . 'includes/class-wpsc-usergroup-action.php' );
				include_once( WPSC_USERGROUP_ABSPATH . 'includes/class-wpsc-usergroup-functions.php' );

				$admin    = new WPSC_Usergroup_Admin();
				$frontend = new WPSC_Usergroup_Frontend();

				add_action('wpsc_after_setting_pills', array($admin,'wpsc_after_setting_pills'));
				add_action('wpsp_en_after_edit_recipients', array($admin, 'wpsp_en_after_edit_recipients'),10,1);
				add_action('wpsp_en_add_ticket_recipients', array($admin, 'wpsp_en_add_ticket_recipients'));

				add_filter( 'wpsc_tl_customer_restrict_rules', array($frontend, 'wpsc_tl_customer_restrict_rules'), 10, 1 );
				add_filter( 'wpsc_has_permission', array($frontend, 'wpsc_has_permission'), 10, 3 );
				add_filter( 'wpsc_en_create_ticket_email_addresses', array($frontend, 'wpsc_en_create_ticket_email_addresses'), 10, 3 );
				add_filter( 'wpsc_en_submit_reply_email_addresses', array($frontend, 'wpsc_en_submit_reply_email_addresses'), 10, 4 );
				add_filter( 'wpsc_en_assign_agent_email_addresses', array($frontend, 'wpsc_en_assign_agent_email_addresses'), 10, 3 );
				add_filter( 'wpsc_en_change_status_email_addresses', array($frontend, 'wpsc_en_change_status_email_addresses'), 10, 3 );
				add_filter( 'wpsc_en_delete_ticket_email_addresses', array($frontend, 'wpsc_en_delete_ticket_email_addresses'), 10, 3 );
				add_filter( 'wpsc_en_submit_note_email_addresses', array($frontend, 'wpsc_en_submit_note_email_addresses'), 10, 4 );
				add_filter( 'wpsc_create_ticket_default_category', array($frontend, 'wpsc_add_default_ticket_category'), 10, 4 );
				add_filter( 'wpsc_default_ticket_category', array($frontend, 'wpsc_selected_create_ticket_category'));
				add_filter( 'wpsc_en_change_category_email_addresses', array($frontend, 'wpsc_en_change_category_email_addresses'), 10, 3 );
				add_filter( 'wpsc_en_change_priority_email_addresses', array($frontend, 'wpsc_en_change_priority_email_addresses'), 10, 3 );
				add_filter('wpsc_export_usergroup_ticket_fields', array($frontend, 'export_usergroup_ticket_fields'), 10, 3);

				//extra usergroups
				add_action('wpsc_add_extra_ticket_users',array($frontend,'add_extra_ticket_usergroup'));
				add_action('wp_ajax_wpsc_filter_autocomplete_usergroups', array($frontend,'filter_autocomplete_usergroups'), 10 , 2);
				add_action('wpsc_set_add_ticket_users', array($frontend,'set_add_extra_ticket_usergroup') ,10 ,2);
				add_action('wpsc_after_extra_users',array($frontend, 'add_usergroups_to_individual_ticket'));

				// Category
				add_filter('wpsc_create_ticket_category', array($admin,'create_ticket_category'),10,2);

				// Ticket List
				add_action( 'wpsc_print_default_tl_field', array($admin,'print_tl_usergroup'));
				add_filter('wpsc_get_select_field',array($admin,'wpsc_get_select_field'),10,3);

				//Filter
				add_filter('wpsc_filter_autocomplete',array($admin,'wpsc_filter_autocomplete'),10,3);
				add_filter('wpsc_filter_val_label',array($admin,'wpsc_filter_val_label'),10,2);
				add_filter('wpsc_add_ticket_meta',array($admin,'wpsc_add_ticket_meta'),10,2);
				add_filter('wpsc_tl_agent_restrict_rules',array($admin,'wpsc_tl_restrict_rules'),10,1);
				add_filter('wpsc_tl_customer_restrict_rules',array($admin,'wpsc_tl_restrict_rules'),10,1);
				add_action('wp_ajax_wpsc_ug_set_other_settings',array($admin,'wpsc_set_other_settings'));

				add_filter( 'wpsc_condition_options', array( $admin, 'add_condition_option' ) );
				add_filter( 'wpsc_condition_dd_options', array($admin, 'wpsc_condition_dd_options'),10,2 );
                add_filter(  'wpsc_check_custom_ticket_condition' ,array($admin,'wpsc_check_custom_ticket_condition'),10,3);	
			   
				// disable category
                add_filter('wpsc_disable_ticket_category',array($admin,'wpsc_disable_ticket_category'),10,3);
				add_action( 'wpsc_add_hidden_category_field', array($admin,'wpsc_add_hidden_category_field'),10,2);
				add_filter('wpsc_replace_macro',array($admin,'replace_macro'),10,2);

				// License
				add_filter( 'wpsc_is_add_on_installed', array($admin,'is_add_on_installed'));
				add_action( 'wpsc_addon_license_area', array($admin,'addon_license_area'));
				add_action( 'wp_ajax_wpsc_usergroup_activate_license', array($admin,'license_activate'));
				add_action( 'wp_ajax_wpsc_usergroup_deactivate_license', array($admin,'license_deactivate'));
				add_action( 'admin_init', array($this, 'plugin_updator'));
				
		}

		function activation(){
			$widget = get_term_by( 'slug', 'usergroup', 'wpsc_ticket_custom_fields' );

			if($widget){
				update_term_meta ($widget->term_id, 'wpsc_allow_ticket_list', '1');
				update_term_meta ($widget->term_id, 'wpsc_customer_ticket_list_status', '0');
				update_term_meta ($widget->term_id, 'wpsc_agent_ticket_list_status', '0');
				update_term_meta ($widget->term_id, 'wpsc_allow_ticket_filter', '1');
				update_term_meta ($widget->term_id, 'wpsc_agent_ticket_filter_status', '0');
				update_term_meta ($widget->term_id, 'wpsc_customer_ticket_filter_status', '0');
			}
		}


		function deactivate(){
				include( WPSC_USERGROUP_ABSPATH. 'includes/class-wpsc-usergroup-uninstall.php' );
		}

		private function define($name, $value) {
				if (!defined($name)) {
						define($name, $value);
				}
		}

		function plugin_updator(){
			$license_key    = get_option('wpsc_usergroup_license_key','');
			$license_expiry = get_option('wpsc_usergroup_license_expiry','');
			if ( class_exists('Support_Candy') && $license_key && $license_expiry ) {
				$edd_updater = new EDD_SL_Plugin_Updater( WPSC_STORE_URL, __FILE__, array(
								'version' => WPSC_USERGROUP_VERSION,
								'license' => $license_key,
								'item_id' => WPSC_USERGROUP_STORE_ID,
								'author'  => 'Pradeep Makone',
								'url'     => site_url()
				) );
			}
    }

		private function is_request($type) {
				switch ($type) {
						case 'admin' :
								return is_admin();
						case 'frontend' :
								return (!is_admin() || defined('DOING_AJAX') ) && !defined('DOING_CRON');
				}
		}
}

new WPSC_USERGROUP();