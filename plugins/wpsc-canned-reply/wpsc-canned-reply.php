<?php 
/**
 * Plugin Name: SupportCandy - Canned Reply
 * Plugin URI: https://supportcandy.net/
 * Description: Canned Reply add-on for SupportCandy
 * Version: 2.0.3
 * Author: Support Candy
 * Author URI: https://supportcandy.net/
 * Text Domain: wpsc-canned-reply
 * Domain Path: /lang
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

final class WPSC_Canned_Reply {
	
	public $version = '2.0.3';
	
	public function __construct() {
		
			$this->define_constants();			
			add_action( 'init', array($this,'load_textdomain') );
			add_action( 'init', array($this, 'register_post_type'));
			add_action( 'add_meta_boxes', array($this,'wpsc_custom_metaboxes_canned_reply') );
			add_action( 'save_post', array($this,'save_canned_reply'));
			$this->include_files();
	}
	
	function define_constants() {
		  define('WPSC_CR_PLUGIN_FILE', __FILE__);
			define('WPSC_CANNED_URL', plugin_dir_url(__FILE__));
			define('WPSC_CANNED_DIR', plugin_dir_path(__FILE__));
			define('WPSC_CANNED_STORE_ID', '271');
			define('WPSC_CANNED_VERSION', $this->version);
	}
	
	function load_textdomain(){
			$locale = apply_filters( 'plugin_locale', get_locale(), 'wpsc-canned-reply' );
			load_textdomain( 'wpsc-canned-reply', WP_LANG_DIR . '/wpsc/wpsc-canned-reply-' . $locale . '.mo' );
			load_plugin_textdomain( 'wpsc-canned-reply', false, plugin_basename( dirname( __FILE__ ) ) . '/lang' );
	}
	
	function register_post_type() {
		
			include( WPSC_CANNED_DIR . 'includes/admin/register_post_type.php' );
	}
	
	function wpsc_custom_metaboxes_canned_reply(){			
    add_meta_box(
      'canned_reply',
      __( 'Support Candy', 'wpsp-canned-reply' ),
      array($this,'wpsc_canned_reply'),
      'wpsc_canned_reply',
      'side',
      'low'
    );
	}
	
	function save_canned_reply($post_id){
    if ( isset($_POST['post_type']) && $_POST['post_type']=='wpsc_canned_reply' ) {
      include WPSC_CANNED_DIR.'includes/admin/save_post_canned_reply.php';									
    }
  }
	
	function wpsc_canned_reply($post){          
    include WPSC_CANNED_DIR.'includes/admin/wpsc_canned_reply.php';									
	}
		
	function include_files(){
		include( WPSC_CANNED_DIR . 'includes/admin/admin.php' );
		
		$backend  = new WPSCannedreplyBackend();
				
			add_action('wpsc_add_addon_tab_after_macro', array($backend, 'wpsc_add_addon_tab_after_macro'));			
			add_action('wpsc_add_addon_reply_tab', array($backend, 'wpsc_canned_reply_post'));			
			add_action('wp_ajax_get_canned_reply', array($backend, 'get_canned_reply'));
			add_action('wp_ajax_delete_canned_reply', array($backend, 'delete_canned_reply'));
			add_action('wp_ajax_submit_canned_reply_post', array($backend, 'submit_canned_reply_post'));
			add_action('wp_ajax_wpsc_set_canned_reply', array($backend, 'set_canned_reply'));
			add_action('wp_ajax_wpsc_insert_canned_reply', array($backend, 'insert_canned_reply'),10,1);
			
			// License
			add_filter( 'wpsc_is_add_on_installed', array($backend,'is_add_on_installed'));
			add_action( 'wpsc_addon_license_area', array($backend,'addon_license_area'));
			add_action( 'wp_ajax_wpsc_canned_reply_activate_license', array($backend,'license_activate'));
			add_action( 'wp_ajax_wpsc_canned_reply_deactivate_license', array($backend,'license_deactivate'));
			add_action( 'admin_init', array($this, 'plugin_updator'));		
	}
	
	function plugin_updator(){
		$license_key    = get_option('wpsc_canned_reply_license_key','');
		$license_expiry = get_option('wpsc_canned_reply_license_expiry','');
		if ( class_exists('Support_Candy') && $license_key && $license_expiry ) {
			$edd_updater = new EDD_SL_Plugin_Updater( WPSC_STORE_URL, __FILE__, array(
							'version' => WPSC_CANNED_VERSION,
							'license' => $license_key,
							'item_id' => WPSC_CANNED_STORE_ID,
							'author'  => 'Pradeep Makone',
							'url'     => site_url()
			) );
		}	
	}
				
}

new WPSC_Canned_Reply();
