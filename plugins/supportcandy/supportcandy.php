<?php 
/**
 * Plugin Name: SupportCandy
 * Plugin URI: https://wordpress.org/plugins/supportcandy/
 * Description: Easy & Powerful support ticket system for WordPress
 * Version: 2.1.7
 * Author: SupportCandy
 * Author URI: https://supportcandy.net/
 * Requires at least: 4.4
 * Tested up to: 5.5
 * Text Domain: supportcandy
 * Domain Path: /lang
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'Support_Candy' ) ) :
  
  final class Support_Candy {
    
    public $version    = '2.1.7';
		public $db_version = '2.0';
    
    public function __construct() {
        
        // define global constants
        $this->define_constants();
        
        // Include required files and classes
        $this->includes();

        add_action( 'init', array($this,'load_textdomain') );
				
				/*
         * Cron setup
         */
				$cron_job_schedule = get_option('wpsc_cron_job_schedule_setup');
				if($cron_job_schedule) {
					add_filter('cron_schedules',array( $this, 'wpsc_cron_schedule'));
	        if (!wp_next_scheduled('wpsc_cron_job_schedules')) {
	            wp_schedule_event(time(), 'wpsc5min', 'wpsc_cron_job_schedules');
	        }
					
					include( WPSC_ABSPATH.'includes/class-wp-cron.php' );
					$cron=new WPSCWPCron();
					add_action( 'wpsc_cron_job_schedules', array( $cron, 'wpsc_cron_job'));
        }   
        
        /**
         * Attachment restructure
         */
        $restuct_attach = get_option('wpsc_restructured_attach_completed', 0);
        if( !$restuct_attach ){
          if (! wp_next_scheduled ( 'wpsc_attachment_restructure')) {
            wp_schedule_event( time(), 'hourly', 'wpsc_attachment_restructure');
          }
          include( WPSC_ABSPATH.'includes/class-wpsc-attachment-restructure.php' );
        }
    }
    
    function define_constants() {
        $this->define('WPSC_STORE_URL', 'https://supportcandy.net');
        $this->define('WPSC_PLUGIN_FILE', __FILE__);
        $this->define('WPSC_ABSPATH', dirname(__FILE__) . '/');
        $this->define('WPSC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
        $this->define('WPSC_PLUGIN_BASENAME', plugin_basename(__FILE__));
        $this->define('WPSC_VERSION', $this->version);
				$this->define('WPSC_DB_VERSION', $this->db_version);
    }
    
    function load_textdomain(){
        $locale = apply_filters( 'plugin_locale', get_locale(), 'supportcandy' );
        load_textdomain( 'supportcandy', WP_LANG_DIR . '/supportcandy/supportcandy-' . $locale . '.mo' );
        load_plugin_textdomain( 'supportcandy', false, plugin_basename( dirname( __FILE__ ) ) . '/lang' );
    }
    
    public function includes() {
        include_once( WPSC_ABSPATH . 'includes/class-wpsc-install.php' );
        include_once( WPSC_ABSPATH . 'includes/class-wpsc-ajax.php' );
        include_once( WPSC_ABSPATH . 'includes/class-wpsc-functions.php' );
        include_once( WPSC_ABSPATH . 'includes/class-wpsc-actions.php' );
				include_once( WPSC_ABSPATH . 'includes/rest_api/class-rest-child.php' );
				include_once( WPSC_ABSPATH . 'includes/rest_api/v1/class-rest-v1-helper.php' );
        if ($this->is_request('admin')) {
          include_once( WPSC_ABSPATH . 'includes/class-wpsc-admin.php' );
        }
        if ($this->is_request('frontend')) {
          include_once( WPSC_ABSPATH . 'includes/class-wpsc-frontend.php' );
        }
        if( !class_exists( 'EDD_SL_Plugin_Updater' ) ) {
          include_once( WPSC_ABSPATH . 'includes/EDD_SL_Plugin_Updater.php' );
        }
				if( get_option('wpsc_rest_api') ) {
					include_once( WPSC_ABSPATH . 'includes/rest_api/class-wpsc-rest-api-v1.php' );
				}
				
    }
    
    private function define($name, $value) {
        if (!defined($name)) {
            define($name, $value);
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
		
		function wpsc_cron_schedule($schedules){
        if(!isset($schedules["wpsc5min"])){
            $schedules["wpsc5min"] = array(
                'interval' => 5*60,
                'display'  => 'Once every 5 minute',
            );
        }
        return $schedules;
    }
    
  }
  
endif;

new Support_Candy();
