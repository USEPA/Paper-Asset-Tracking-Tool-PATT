<?php 
/**
 * Plugin Name: SupportCandy - Export Ticket
 * Plugin URI:  https://supportcandy.net/
 * Description: Export Ticket add-on for SupportCandy
 * Version: 2.0.5
 * Author: Export Ticket
 * Author URI:  https://supportcandy.net/
 * Text Domain: wpsc-export-ticket
 * Domain Path: /lang
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

final class WPSC_Export_Ticket {
	
	public $version = '2.0.5';
	
	public function __construct() {
		
			$this->define_constants();
			add_action( 'init', array($this,'load_textdomain') );			
			$this->include_files();
			add_action('admin_enqueue_scripts',array($this,'load_scripts'));
	}
	
	function define_constants() {
		  define('WPSC_EXP_PLUGIN_FILE', __FILE__);
			define('WPSC_EXP_URL', plugin_dir_url(__FILE__));
			define('WPSC_EXP_DIR', plugin_dir_path(__FILE__));
			define('WPSC_EXP_VERSION', $this->version);
		 	define('WPSC_EXP_STORE_ID', '205');
	}
	
	function load_scripts(){
		wp_enqueue_style('export-admin-css', WPSC_EXP_URL . 'asset/css/admin.css?version='.WPSC_EXP_VERSION );
	}
	
	function load_textdomain(){
			$locale = apply_filters( 'plugin_locale', get_locale(), 'wpsc-export-ticket' );
			load_textdomain( 'wpsc-export-ticket', WP_LANG_DIR . '/wpsc/wpsc-export-ticket-' . $locale . '.mo' );
			load_plugin_textdomain( 'wpsc-export-ticket', false, plugin_basename( dirname( __FILE__ ) ) . '/lang' );
	}
				
	function include_files(){
		include( WPSC_EXP_DIR . 'includes/admin/admin.php' );
		include( WPSC_EXP_DIR . 'includes/frontend.php' );
		include_once( WPSC_EXP_DIR. 'class-wpsc-exp-install.php' );
		
		$backend  = new WPSCExportTicketBackend();
		$frontend =new WPSCExportTicketFrontend();

		if(is_admin()){
			add_action('wpsc_admin_localize_script', array($backend, 'wpsc_admin_localize_script'));
			add_action('wpsc_add_btn_after_default_filter', array($backend, 'export_btnAfterDefaultFilter'));
			add_action('wp_ajax_wpsc_get_export_ticket_field', array($backend, 'get_export_ticket_field'));
			add_action('wp_ajax_wpsc_set_export_ticket', array($backend, 'set_export_ticket'));
			
			//export settings
			add_action('wpsc_after_setting_pills', array($backend, 'setting_pillsExportTicket'));
			add_action('wp_ajax_wpsc_get_export_ticket_settings', array($backend, 'get_export_ticket_settings'));											
			add_action('wp_ajax_wpsc_set_export_ticket_settings', array($backend,'set_export_ticket_settings'));
			add_action('wp_ajax_wpsc_set_export_list_order',array($backend,'set_export_list_order'));
			
			// add new fields to load order
			add_action('wpsc_set_add_agentonly_field', array($backend,'add_agentonly_field'));
			add_action('wpsc_set_add_form_field', array($backend,'add_ticket_form_field'));

			// export active customers
			add_action('wpsc_report_after_active_customers',array($backend,'active_customers'));
			add_action('wp_ajax_set_active_customers_export',array($backend,'set_active_customers_export'));

			//Delete custom fields
			add_action('wpsc_delete_custom_field',array($backend,'after_delete_custom_field'));
			
			// License
			add_filter( 'wpsc_is_add_on_installed', array($backend,'is_add_on_installed'));
			add_action( 'wpsc_addon_license_area', array($backend,'addon_license_area'));
			add_action( 'wp_ajax_wpsc_export_ticket_activate_license', array($backend,'license_activate'));
			add_action( 'wp_ajax_wpsc_export_ticket_deactivate_license', array($backend,'license_deactivate'));
		}
		else {
			add_action('wpsc_admin_localize_script', array($frontend, 'wpsc_admin_localize_script'));
		}
		add_action('admin_init',array($this,'plugin_updator'));
	}	
	
	function plugin_updator(){
		$license_key    = get_option('wpsc_export_ticket_license_key','');
		$license_expiry = get_option('wpsc_export_ticket_license_expiry','');
		if ( class_exists('Support_Candy') && $license_key && $license_expiry ) {
			$edd_updater = new EDD_SL_Plugin_Updater( WPSC_STORE_URL, __FILE__, array(
							'version' => WPSC_EXP_VERSION,
							'license' => $license_key,
							'item_id' => WPSC_EXP_STORE_ID,
							'author'  => 'Pradeep Makone',
							'url'     => site_url()
			) );
		}	
	
	}		
}
new WPSC_Export_Ticket();
