<?php 
/**
 * Plugin Name: SupportCandy - SLA
 * Plugin URI: https://supportcandy.net/
 * Description: Service Lavel Agreement addon for SupportCandy
 * Version: 2.0.8
 * Author: Support Candy
 * Author URI: https://supportcandy.net/
 * Requires at least: 4.4
 * Tested up to: 4.9
 * Text Domain: wpsc-sla
 * Domain Path: /lang
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'Support_Candy_SLA' ) ) :
  
  final class Support_Candy_SLA {
    
    public $version = '2.0.8';
    
    public function __construct() {
      $this->define_constants();
			$this->includes();
			add_action( 'init', array($this,'load_textdomain') );
			register_activation_hook(__FILE__,array($this,'activation'));
			register_deactivation_hook( __FILE__, array($this,'deactivate') );
		}
    
    function define_constants() {
      $this->define('WPSC_SLA_PLUGIN_FILE', __FILE__);
      $this->define('WPSC_SLA_ABSPATH', dirname(__FILE__) . '/');
      $this->define('WPSC_SLA_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
      $this->define('WPSC_SLA_PLUGIN_BASENAME', plugin_basename(__FILE__));
			$this->define('WPSC_SLA_STORE_ID', '207');
      $this->define('WPSC_SLA_VERSION', $this->version);
    }
    
    function load_textdomain(){
      $locale = apply_filters( 'plugin_locale', get_locale(), 'wpsc-sla' );
      load_textdomain( 'wpsc', WP_LANG_DIR . '/wpsc/wpsc-sla-' . $locale . '.mo' );
      load_plugin_textdomain( 'wpsc-sla', false, plugin_basename( dirname( __FILE__ ) ) . '/lang' );
    }
    
    public function includes() {
      
      include_once( WPSC_SLA_ABSPATH . 'class-wpsc-sla-install.php' );
      include_once( WPSC_SLA_ABSPATH . 'class-wpsc-sla.php' );
      $sla = new WPSC_SLA();
      
      // Setting
      add_action( 'wpsc_after_setting_pills', array($sla,'sla_setting_pill'));
      add_action( 'wp_ajax_wpsc_get_sla_settings', array($sla,'get_sla_settings'));
			add_action( 'wp_ajax_wpsc_set_sla_settings', array($sla,'set_sla_settings'));
			
			// SLA policy
			add_action( 'wp_ajax_wpsc_get_add_sla_policy', array($sla,'get_add_sla_policy'));
			add_action( 'wp_ajax_wpsc_set_add_sla_policy', array($sla,'set_add_sla_policy'));
			add_action( 'wp_ajax_wpsc_get_edit_sla_policy', array($sla,'get_edit_sla_policy'));
			add_action( 'wp_ajax_wpsc_set_edit_sla_policy', array($sla,'set_edit_sla_policy'));
			add_action( 'wp_ajax_wpsc_set_sla_order', array($sla,'set_sla_order'));
			add_action( 'wp_ajax_wpsc_delete_sla_policy', array($sla,'delete_sla_policy'));
			
			// SLA Checkpoints
			add_action( 'wpsc_ticket_created', array($sla,'sla_checkout_create_ticket'), 99);
			add_action( 'wpsc_after_submit_reply', array($sla,'sla_checkout_submit_reply'), 99, 2);
			// add_action( 'wpsc_after_submit_note', array($sla,'sla_checkout_submit_reply'), 99, 2);
			add_action( 'wpsc_set_change_status', array($sla,'sla_checkout_change_status'), 99, 2);
			add_action( 'wpsc_set_change_category', array($sla,'sla_checkout_change_status'), 99, 2);
			add_action( 'wpsc_set_change_priority', array($sla,'sla_checkout_change_status'), 99, 2);
			add_action( 'wpsc_set_change_fields', array($sla,'sla_checkout_change_field'), 99, 3);
			
			// Ticket List
			add_action( 'wpsc_print_default_tl_field', array($sla,'print_tl_sla'));
			
			// License
			add_filter( 'wpsc_is_add_on_installed', array($sla,'is_add_on_installed'));
			add_action( 'wpsc_addon_license_area', array($sla,'addon_license_area'));
			add_action( 'wp_ajax_wpsc_sla_activate_license', array($sla,'license_activate'));
			add_action( 'wp_ajax_wpsc_sla_deactivate_license', array($sla,'license_deactivate'));
			add_action( 'admin_init', array($this, 'plugin_updator'));
      
			add_action( 'wp_ajax_wpsc_get_upgrade_sla', array($sla,'upgrade_sla'));
			
			//Filter Autocomplete
			add_filter( 'wpsc_filter_autocomplete', array($sla, 'filter_autocomplete'), 10, 3 );
			add_filter('wpsc_filter_val_label', array($sla, 'filter_val_label'), 10, 2);
			
			//Get Meta query
			add_filter('wpsc_add_ticket_meta', array($sla, 'add_ticket_meta'), 10, 2);
			add_filter('wpsc_get_tickets_meta', array($sla, 'get_tickets_meta'), 10, 3);

			//Notification Mail
			add_filter('wpsc_en_types', array($sla, 'notification_types'));
			add_action('wpsc_cron', array($sla, 'out_of_sla_email'));
			
			// add meta key 
			add_filter('wpsc_get_all_meta_keys', array($sla,'wpsc_get_all_meta_keys'),10,1);
    	     add_filter('wpsc_replace_macro',array($sla,'wpsc_replace_macro'),10,2);
			
			// Reports
			add_action('wpsc_report_sub_menu' , array($sla,'sla_overdue_ticket_graph'));
			add_action('wp_ajax_get_sla_overdue_report',array($sla,'get_sla_overdue_report'));
			add_action('wp_ajax_sla_reports_by_filter',array($sla,'sla_reports_by_filter'));
			add_action('wpsc_after_dashboard_report',array($sla,'sla_dashboard_report'));
			add_action('wpsc_cron', array($sla, 'after_ticket_out_of_sla'));
			add_filter('wpsc_replace_macro',array($sla,'wpsc_replace_macro'),10,2);
		
			// Email Notification
			add_action('wpsc_after_failed_attemp_mail_sent',array($sla,'after_mail_sent'));
			add_action('wpsc_after_successfully_mail_sent',array($sla,'after_mail_sent'));
		}

		function activation() {
			$widget = get_term_by( 'slug', 'sla', 'wpsc_ticket_custom_fields' );
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
      	include( WPSC_SLA_ABSPATH.'class-wpsc-sla-uninstall.php' );
    }
		
    private function define($name, $value) {
      if (!defined($name)) {
        define($name, $value);
      }
    }
		
		function plugin_updator(){
			$license_key    = get_option('wpsc_sla_license_key','');
			$license_expiry = get_option('wpsc_sla_license_expiry','');
			if ( class_exists('Support_Candy') && $license_key && $license_expiry ) {
				$edd_updater = new EDD_SL_Plugin_Updater( WPSC_STORE_URL, __FILE__, array(
								'version' => WPSC_SLA_VERSION,
								'license' => $license_key,
								'item_id' => WPSC_SLA_STORE_ID,
								'author'  => 'Pradeep Makone',
								'url'     => site_url()
				) );
			}	
    }
    
  }
  
endif;

new Support_Candy_SLA();
