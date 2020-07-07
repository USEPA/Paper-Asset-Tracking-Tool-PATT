<?php
/**
 * Plugin Name: SupportCandy - Reports
 * Plugin URI:  https://supportcandy.net/
 * Description: Report add-on for SupportCandy
 * Version: 2.0.3
 * Author: Support Candy
 * Author URI:  https://supportcandy.net/
 * Text Domain: wpsc-rp
 * Domain Path: /lang
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

final class WPSC_Reports {

	public $version = '2.0.3';
	public $rp_db_version = '2.0';

	public function __construct() {
		
		$this->define_constants();
		$this->includes();
		add_action( 'init', array($this,'load_textdomain') );
		
	}

	function define_constants() {
			$this->define('WPSC_RP_PLUGIN_FILE', __FILE__);
			$this->define('WPSC_RP_ABSPATH', dirname(__FILE__) . '/');
			$this->define('WPSC_RP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
			$this->define('WPSC_RP_PLUGIN_BASENAME', plugin_basename(__FILE__));
			$this->define('WPSC_RP_STORE_ID', '3550');
			$this->define('WPSC_RP_VERSION', $this->version);
			$this->define('WPSC_RP_DB_VERSION', $this->rp_db_version);
	}

	function load_textdomain(){
			$locale = apply_filters( 'plugin_locale', get_locale(), 'wpsc-rp' );
			load_textdomain( 'wpsc-rp', WP_LANG_DIR . '/wpsc/wpsc-rp-' . $locale . '.mo' );
			load_plugin_textdomain( 'wpsc-rp', false, plugin_basename( dirname( __FILE__ ) ) . '/lang' );
	}

	public function includes() {

		include_once( WPSC_RP_ABSPATH . 'includes/class-wpsc-function.php' );
		include_once( WPSC_RP_ABSPATH . 'includes/class-wpsc-rp-admin.php' );
		include_once( WPSC_RP_ABSPATH . 'includes/wpsc_report_ajax.php' );
		include_once( WPSC_RP_ABSPATH . 'wpsc-reports-install.php' );
		
		$admin = new WPSC_Reports_Admin	();
		if ($this->is_request('admin')) {
			// Show seetings
			add_action('wpsc_add_submenu_page', array($admin,'rp_setting_pill'));
			add_action('wp_ajax_wpsc_get_rp_settings', array($admin,'load_settings'));
			add_action('wp_ajax_get_ticket_stats_report', array($admin,'get_ticket_stats_report'));
			add_action('wp_ajax_get_ticket_stat_report_by_filter', array($admin,'ticket_stat_report_by_filter'));
			add_action('wp_ajax_get_first_response_time_reports', array($admin,'get_first_response_time_reports'));
			add_action('wp_ajax_get_first_response_time_reports_selection', array($admin,'get_first_response_time_reports_selection'));
			add_action('wp_ajax_get_upgrade_frt', array($admin,'upgrade_frt'));
			add_action('wp_ajax_get_category_report', array($admin,'get_category_report'));
			add_action('wp_ajax_get_all_dropdown_report',array($admin,'get_all_dropdown_report'));
			add_action('wp_ajax_get_all_checkbox_report', array($admin,'get_all_checkbox_report'));
			add_action('wp_ajax_get_all_radio_button_report', array($admin,'get_all_radio_button_report'));
			add_action('wp_ajax_get_active_customers_report' ,array($admin,'get_active_customers_report'));
			add_action('wp_ajax_get_dashboard_report',array($admin,'get_dashboard_report'));
			add_action('wp_ajax_get_active_customers_filter',array($admin,'get_active_customers_filter'));
			add_action('wp_ajax_set_active_customers_default_filter',array($admin,'set_active_customers_default_filter'));
			add_action('wp_ajax_get_recalculate_first_response_time',array($admin,'get_recalculate_first_response_time'));
			add_action('wp_ajax_get_active_customers_settings',array($admin,'get_active_customers_settings'));
			add_action('wp_ajax_get_recalculate_ts', array($admin, 'get_recalculate_ts'));

			// settings 
			add_action( 'wpsc_after_setting_pills', array($admin,'reports_setting_pill'));
			add_action( 'wp_ajax_wpsc_get_reports_settings', array($admin,'get_reports_settings'));
			add_action( 'wp_ajax_wpsc_reports_save_settings', array($admin,'save_settings'));
			
			// Database upgrade 
			add_action('wp_ajax_wpsc_rp_run_db_v2_upgrade',array($admin,'run_db_v2_upgrade'));
		
		}
			// report cron
			add_action('wpsc_cron',array($admin,'wpsc_report_cron_job'));

			// set checked_frt 
			add_action('wpsc_ticket_created', array($admin,'set_checked_frt'),10,1);

			// calculate first response time
			add_action('wpsc_after_submit_reply', array($admin,'get_first_response_time'),10,2);
			
			//upgrate tickets
			add_action('wp_ajax_get_upgrade_ticket',array($admin,'get_upgrade_ticket'));
			
		// License
		 	add_filter( 'wpsc_is_add_on_installed', array($admin,'is_add_on_installed'));
			add_action( 'wpsc_addon_license_area', array($admin,'addon_license_area'));
			add_action( 'wp_ajax_wpsc_rp_activate_license', array($admin,'license_activate'));
			add_action( 'wp_ajax_wpsc_rp_deactivate_license', array($admin,'license_deactivate'));
			add_action( 'admin_init', array($this, 'plugin_updator'));
			
	}

	private function define($name, $value) {
			if (!defined($name)) {
					define($name, $value);
			}
	}

	function plugin_updator(){
		$license_key    = get_option('wpsc_rp_license_key','');
		$license_expiry = get_option('wpsc_rp_license_expiry','');
		if(class_exists('Support_Candy') && $license_key && $license_expiry ) {
			$edd_updater = new EDD_SL_Plugin_Updater( WPSC_STORE_URL, __FILE__, array(
							'version' => WPSC_RP_VERSION,
							'license' => $license_key,
							'item_id' => WPSC_RP_STORE_ID,
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

new WPSC_Reports();
