<?php
/**
 * Plugin Name: SupportCandy - Automatic Close Ticket
 * Plugin URI: https://supportcandy.net/
 * Description: Close ticket automatically after x days of inactivity for chosen statuses.
 * Version: 2.0.5
 * Author: Support Candy
 * Author URI: https://supportcandy.net/
 * Requires at least: 4.4
 * Tested up to: 4.9
 * Text Domain: wpsc-atc
 * Domain Path: /lang
 */

 if ( ! defined( 'ABSPATH' ) ) {
 	exit; // Exit if accessed directly
 }

if ( ! class_exists( 'Support_Candy_ATC' ) ) :

  final class Support_Candy_ATC {

    public $version = '2.0.5';

    public function __construct() {
    
      $this->define_constants();
      $this->includes();
      add_action( 'init', array($this,'load_textdomain') );
      
    }

    function define_constants() {
      define('WPSC_ATC_PLUGIN_FILE', __FILE__);
      define('WPSC_ATC_ABSPATH', dirname(__FILE__) . '/');
      define('WPSC_ATC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
      define('WPSC_ATC_PLUGIN_BASENAME', plugin_basename(__FILE__));
      define('WPSC_ATC_STORE_ID', '1656');
      define('WPSC_ATC_VERSION', $this->version);
    }

    function load_textdomain() {
      $locale = apply_filters( 'plugin_locale', get_locale(), 'wpsc-atc' );
      load_textdomain( 'wpsc', WP_LANG_DIR . '/wpsc/wpsc-atc-' . $locale . '.mo' );
      load_plugin_textdomain( 'wpsc-atc', false, plugin_basename( dirname( __FILE__ ) ) . '/lang' );
    }

    public function includes() {

      include_once( WPSC_ATC_ABSPATH . 'class-wpsc-atc-install.php' );
      include_once( WPSC_ATC_ABSPATH . 'class-wpsc-atc.php' );
      $atc = new WPSC_ATC();

      // Setting
      add_action( 'wpsc_after_setting_pills', array($atc,'atc_setting_pill'));
      add_action( 'wp_ajax_wpsc_get_atc_settings', array($atc,'get_atc_settings'));
      add_action( 'wp_ajax_wpsc_atc_save_settings', array($atc,'save_settings'));
      

      // Cron
			add_action('wpsc_cron', array($atc, 'atc_cron'));
      add_action('wpsc_after_submit_reply', array($atc, 'wpsc_after_submit_reply'),10,2);
      
      // Email Notification
		  add_action('wpsc_after_failed_attemp_mail_sent',array($atc,'after_mail_sent'));
      add_action('wpsc_after_successfully_mail_sent',array($atc,'after_mail_sent'));
      add_action('wpsc_after_en_setting_pills', array($atc,'wpsc_after_en_setting_pills'));
      add_action('wp_ajax_wpsc_get_atc_ticket_notifications',array($atc,'get_atc_ticket_notifications'));
      add_action('wp_ajax_wpsc_set_atc_ticket_notifications',array($atc,'set_atc_ticket_notifications'));
      add_action('wpsc_add_external_en_setting_scripts',array($atc,'add_external_en_atc_setting_scripts'));

      // License
			add_filter( 'wpsc_is_add_on_installed', array($atc,'is_add_on_installed'));
			add_action( 'wpsc_addon_license_area', array($atc,'addon_license_area'));
			add_action( 'wp_ajax_wpsc_atc_activate_license', array($atc,'license_activate'));
			add_action( 'wp_ajax_wpsc_atc_deactivate_license', array($atc,'license_deactivate'));
			add_action( 'admin_init', array($this, 'plugin_updator'));


    }
    
    private function define($name, $value) {
      if (!defined($name)) {
        define($name, $value);
      }
    }
		
		function plugin_updator(){
			$license_key    = get_option('wpsc_atc_license_key','');
			$license_expiry = get_option('wpsc_atc_license_expiry','');
			if(class_exists('Support_Candy') && $license_key && $license_expiry ) {
				$edd_updater = new EDD_SL_Plugin_Updater( WPSC_STORE_URL, __FILE__, array(
								'version' => WPSC_ATC_VERSION,
								'license' => $license_key,
								'item_id' => WPSC_ATC_STORE_ID,
								'author'  => 'Pradeep Makone',
								'url'     => site_url()
				) );
			}	
    }
    
  }

endif;

new Support_Candy_ATC();
